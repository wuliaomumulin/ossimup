<?php
use App\Models\Userreference;
use App\Models\Event;
use App\Models\Device;
use App\Models\Alarm;
use App\Models\TopologyNode;
use App\Models\HostTypes;
use App\Models\CustomVerify;
use App\Models\UdpSensor;
use App\Models\Config;

class IndexController extends Base
{
    protected $config = null;
    protected $uid=null;
    protected $rid=null;
    protected $role=null;
    protected $uname=null;
    protected $location=null;
    protected $user_reference = null;
    protected $redis = null;
    protected $type = null;
    protected $num = 23;  //这是为了防止普通用户访问 而故意写了一个超级管理员的权限数字 传入checkAuth（）内，这样肯定不能访问
     public function init()
    {
        parent::init();
        $this->redis = new phpredis();
        $this->Userreference = new Userreference;
        $this->config = \Yaf\Registry::get("config");
        $this->uid =$_SESSION['uid'];
        $this->uname = $_SESSION['username'];
        $this->rid =$_SESSION['rid'];
        $this->user_reference = $_SESSION['user_reference'];
        $this->role = [1,2,3];//系统内置角色
        $this->location = new GeoLite2(); 
        $this->type = input('type');
        $this->checkAuth($this->num);
    }
    public function indexAction()
    {

        echo date('Y-m-d H:i:s');
        //echo date(DATE_ISO8601,strtotime("now"));//ISO8601时间戳
        
    }
    /** 
    * 采集器状态 
    */
    public function lefttopAction(){
        $UdpSensor = new UdpSensor();
        $datalist['title'] = '采集状态';
        $datalist['data'] = $UdpSensor->platAndSenosrCount();
        jsonResult($datalist);
    }
    /*  
    * 数据接受状态
    */
    public function leftcenterAction(){
        $Config = new Config();
        $eth = $Config->getEth();
        //table
        $table = $eth.'_packets';
        $phpredis = new phpredis();
        $datalist['title'] = '数据接受状态';
        $datalist['data'] = array_reverse($phpredis->listGet($table,0,60));
        jsonResult($datalist);
        
    }
    /** 
    * 告警事件 
    */
    public function righttopAction(){


        $model = new Alarm();
        $field = "hex(a.backlog_id) backlog_id,hex(a.event_id) event_id,hex(a.corr_engine_ctx) corr_engine_ctx,a.timestamp,
                a.status,a.protocol,INET6_NTOA(a.src_ip) src_ip,INET6_NTOA(a.dst_ip) dst_ip,a.src_port,a.dst_port,a.risk,
                a.plugin_id,a.plugin_sid,
                ki.NAME AS kingdom,
                ca.NAME AS category,
                ta.subcategory ";


        $join = " LEFT JOIN (alarm_taxonomy ta
                 LEFT JOIN alarm_kingdoms ki ON ta.kingdom = ki.id
                LEFT JOIN alarm_categories ca ON ta.category = ca.id 
                ) ON a.plugin_sid = ta.sid 
                AND a.corr_engine_ctx = ta.engine_id,
                backlog b ";

        $where = ['TIMESTAMP' => '1970-01-01 00:00:00','is_read' => 0,'risk'=>4];

        $group = "a.similar ";
        $order = "a.TIMESTAMP DESC";

        $page_size = input('post.page_size', 5);
        $page = input('post.page', 1);

        $data_list = $model->getDataList($field, $join, $where, $group, $order, $page, $page_size);

        $GeoLite2 = new GeoLite2();
        $event = new Event();
        $Device = new Device();
        foreach ($data_list as $k => &$v) {

            if (filter_var($v['src_ip'], FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE) == false) {
                $v['src_flag'] = '';
                $v['src_flag_name'] = '';
                $v['dst_flag'] = '';
                $v['dst_flag_name'] = '';
            } else {
                $v['src_flag'] = strtolower($GeoLite2->getisoCode($v['src_ip']));
                $v['src_flag_name'] = $GeoLite2->getIpCountryName($v['src_ip']);
                $v['dst_flag'] = strtolower($GeoLite2->getisoCode($v['dst_ip']));
                $v['dst_flag_name'] = $GeoLite2->getIpCountryName($v['dst_ip']);
            }

            //查询关联的安全事件
            $name = $model->getEventName(['plugin_id' => $v['plugin_id'], 'sid' => $v['plugin_sid']]);
            if (!empty($name)) {
                if (strpos($name, ':') !== false) {
                    $v['alarm_name'] = substr(strrchr($name, ':'), 1);
                } else {
                    $v['alarm_name'] = $name;
                }

            } else {
                $v['alarm_name'] = '未知';
            }
            //根据告警事件追溯报警设备
            $v['device_hostname'] = $Device->eventIdToHostname($v["event_id"]);

            $v['src_hostname'] = $event->getHostName($v['src_ip']) ? $event->getHostName($v['src_ip']) : $v['src_ip'];
            $v['dst_hostname'] = $event->getHostName($v['dst_ip']) ? $event->getHostName($v['dst_ip']) : $v['dst_ip'];

            $v['kingdom'] = $v['kingdom'] ? $v['kingdom'] : '未知';
            $v['category'] = $v['category'] ? $v['category'] : '未知';
            $v['subcategory'] = $v['subcategory'] ? $v['subcategory'] : '未知';
            $v['corr_engine_ctx'] = $v['corr_engine_ctx'] ? $v['corr_engine_ctx'] : '未知';
        }
        $data['title'] = '告警事件';
        $data['data'] = is_null($data_list) ? [] : $data_list;


        jsonResult($data);

    }
    /*  
    * 安全事件
    */
    public function rightcenterAction(){
        $redis = new \phpredis();

        $key = $_SESSION['uid'] . '-'. __METHOD__;
        if ($result = $redis->get($key)) {

            jsonResult(json_decode($result,true));

        } else {

            $model = new Event();
            $datalist['title'] = '安全事件'; 
            $datalist['data'] = $model->getAll('hex(a.id) id,inet6_ntoa(a.ip_src) src_ip,inet6_ntoa(a.ip_dst) dst_ip,a.layer4_sport,a.layer4_dport,date_format(a.`timestamp`,"%H:%i:%s") `timestamp`,a.plugin_id,a.plugin_sid');
            if(is_null($datalist['data'])) $datalist['data'] = [];
            if(!empty($datalist['data'])){
                $model->setCache($key,$datalist,300);
            }
            jsonResult($datalist);
        }
    }
    /*  
    * 中间地图
    * 取得当天安全事件和告警事件
    */
    public function centerAction(){

        $redis = new \phpredis();

        $key = $_SESSION['uid'] . '-'. __METHOD__;
        if ($result = $redis->get($key)) {

            jsonResult(json_decode($result,true));

        } else {        


            //个人信息
            $CustomVerify = new CustomVerify();
            $info = $CustomVerify->getInfo();

            //目标IP
            $ip = $CustomVerify->getServiceIp()['sensor_info'];


            //构建数据模板
            $datalist = [
                'title' => '访问事件',
                'data' => [
                    'platformData' => [
                        [
                            'name' => $info['factory'],
                            'value' => [
                                $info['lng'],$info['lat'],
                            ],
                            'detail' => $info
                        ]
                    ],
                    //来源点
                    'userData' => [
                        /*[
                            'name' => '',
                            'value' => '',
                            'detail' => [

                            ]
                        ]*/
                    ],
                    //攻击线
                    'lineData' => [

                    ]
                ]
            ];

            /*$model = new Event();
            $field = '`src_hostname`,`dst_hostname`,hex(src_host) src_host,hex(dst_host) dst_host,inet6_ntoa(a.ip_src) src_ip,inet6_ntoa(a.ip_dst) dst_ip,`ossim_risk_a`,`ossim_risk_c`';
            $where = [
                'date(a.timestamp)' => ['exp','=curdate()'], //一天 
                'ip_src' => ['exp','<> ip_dst and ip_src <> "\0\0\0\0" and ip_dst <> "\0\0\0\0" and inet6_ntoa(ip_src) not like "10.157.%" and inet6_ntoa(ip_dst) not like "10.157.%"'], //去掉本地，之后再过滤掉黑名单IP组

            ];*/

            // select hex(id) id,src_hostname,dst_hostname,hex(src_host) src_host,hex(dst_host) dst_host,inet6_ntoa(a.src_ip) src_ip,inet6_ntoa(a.dst_ip) dst_ip,inet6_ntoa(b.src_ip) bsrc_ip,inet6_ntoa(b.dst_ip) bdst_ip,risk_a,risk_c,alarm from event a left join alarm b on a.id=b.event_id where alarm = 1
            //$ips = $model->getAll($field,$where);
            //$ips = json_decode($redis->get('sensor-ip-1'),true);

            $model = new TopologyNode();
            $ips = $model->externalIP();

            array_walk($ips,function($arr,$key) use(&$datalist){
                $src = $this->location->getlongLat($arr['src_ip']);
                $src[0] = strval($src[0]);
                $src[1] = strval($src[1]);
                $dst = $this->location->getlongLat($arr['device']);
                $dst[0] = strval($dst[0]);
                $dst[1] = strval($dst[1]);

                //判断源地址和目标地址是否相同，如果相同就过滤
                if(!\Tools::isEmpty(array_diff($src,$dst))){
                    
                    //来源点
                    $userDataItem = [
                        'name' => $arr['ip'],
                        'value' => $src,
                        'detail' => [
                            'src_ip' => $arr['src_ip'],
                            'dst_ip' => $arr['device'],
                            'src_host' => $arr['name'],
                            'dst_host' => $arr['device_name'],
                            'src_hostname' => $arr['name'],
                            'dst_hostname' => $arr['device_name'],
                            'timestamp' => date('Y-m-d H:i:s',strtotime('-1 day')).'|'.date('Y-m-d H:i:s'),
                            'is_src' => $arr['is_src']>0 ? 'src_ip' : 'dst_ip',
                            'alarm' => 0,
                            //'risk_a' => $arr['ossim_risk_a'],
                            //'risk_c' => $arr['ossim_risk_c'],
                        ],
                    ];
                    
                    //攻击线
                    $lineDataItem = [
                        'coords' => [
                            $src,
                            $datalist['data']['platformData'][0]['value'],
                        ],
                        'ruleRiskLevel' => '0',
                        //'ruleRiskLevel' => $arr['alarm'],
                    ];
                    
                    $datalist['data']['userData'][] = $userDataItem; 
                    $datalist['data']['lineData'][] = $lineDataItem; 

                }

            });

            $CustomVerify->setCache($key,$datalist,600);

            jsonResult($datalist);
        }    

    }
    /*  
    * 威胁来源
    */
    public function bottom1Action(){
        $model = new TopologyNode();
        $datalist = [
            'title' => '威胁来源',
            'data' => []
        ];
        $ips = $model->externalIP();
        //var_dump($ips);die;
        if(!Tools::isEmpty($ips)){
            $ips = array_column($ips,'src_ip');

            $temp = [];
            
            while(!Tools::isEmpty(current($ips))){
                $iso = $this->location->getIpCountryName(current($ips));
                $iso = empty($iso) ? '未知' : $iso;
                if(array_key_exists($iso,$temp)){
                    $temp[$iso]++;
                }else{
                    $temp[$iso] = 1;
                }

                next($ips);
            }

            while(!Tools::isEmpty(key($temp))){

                if(key($temp) != '未知'){
                    $datalist['data'][] = [
                        'name' => key($temp),
                        'value' => strval(current($temp)),
                    ];
                }
  
                next($temp);
            }

        }
        jsonResult($datalist);    
    }
    /*  
    * 资产性能--监控本机性能
    */
    public function bottom2Action(){
    
        $phpredis = new phpredis();
        $keys = [
            'memUsage',
            'diskUsage',
            'cpuUsage',
        ];
        $value = $phpredis->mget($keys);


        $data['data'] = [
            ['name' => '内存','value' => $value[0]], 
            ['name' => '磁盘','value' => $value[1]], 
            ['name' => 'CPU','value' => $value[2]],
        ];

        jsonResult($data);
    }
    /*  
    * 资产统计
    */
    public function bottom3Action(){

        $host = new HostTypes();
        $r = $host->typeCount();
        if(\Tools::isEmpty($r)){

            $data = [
            'title'=>'资产统计',
            'data'=> [
                    ['name'=>'暂无数据','value'=>"0"]
                ],
            ];

        }else{
            $data = [
            'title'=>'资产统计',
            'data'=> $host->typeCount(),
            ];
        }

       jsonResult($data);

    }
}
