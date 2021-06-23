<?php
use App\Models\Userreference;
use App\Models\Event;
use App\Models\Alarm;
use App\Models\HostTypes;
use App\Models\CustomVerify;
use App\Models\UdpSensor;
use App\Models\Config;
        // ini_set('display_errors', 1);
        // ini_set('display_startup_errors', 1);
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
        $datalist['title'] = '采集器状态';
        $datalist['data'] = $UdpSensor->platAndSenosrCount();
        jsonResult($datalist);
    }
    /*  
    * 数据发送状态
    */
    public function leftcenterAction(){
        $Config = new Config();
        $eth = $Config->getEth();
        //table
        $table = $eth.'_packets';
        $phpredis = new phpredis();
        $datalist['title'] = '数据发送状态';
        $datalist['data'] = array_reverse($phpredis->listGet($table,0,60));
        jsonResult($datalist);
        
    }
    /** 
    * 告警事件 
    */
    public function righttopAction(){
        $model = new Alarm();
        $datalist['title'] = '告警事件'; 
        $datalist['data'] = $model->getAll('hex(a.backlog_id) id,hex(a.event_id) event_id,a.`status`,a.plugin_id,a.plugin_sid,a.protocol,inet6_ntoa(a.src_ip) src_ip,inet6_ntoa(a.dst_ip) dst_ip,a.src_port,a.dst_port,a.risk,a.timestamp,a.corr_engine_ctx');
        if(is_null($datalist['data'])) $datalist['data'] = [];
         jsonResult($datalist);
    }
    /*  
    * 安全事件
    */
    public function rightcenterAction(){
        $model = new Event();
        $datalist['title'] = '安全事件'; 
        $datalist['data'] = $model->getAll('hex(a.id) id,inet6_ntoa(a.ip_src) src_ip,inet6_ntoa(a.ip_dst) dst_ip,a.layer4_sport,a.layer4_dport,a.timestamp,a.plugin_id,a.plugin_sid');
        if(is_null($datalist['data'])) $datalist['data'] = [];
        jsonResult($datalist);
    }
    /*  
    * 中间地图
    * 取得当天安全事件和告警事件
    */
    public function centerAction(){


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

        $model = new Event();
        $field = ' src_hostname,dst_hostname,hex(src_host) src_host,hex(dst_host) dst_host,inet6_ntoa(a.src_ip) src_ip,inet6_ntoa(a.dst_ip) dst_ip,risk_a,risk_c,alarm';
        $where = [
            'date(a.timestamp)' => ['exp','=curdate()'] //一天 
        ];

        // select hex(id) id,src_hostname,dst_hostname,hex(src_host) src_host,hex(dst_host) dst_host,inet6_ntoa(a.src_ip) src_ip,inet6_ntoa(a.dst_ip) dst_ip,inet6_ntoa(b.src_ip) bsrc_ip,inet6_ntoa(b.dst_ip) bdst_ip,risk_a,risk_c,alarm from event a left join alarm b on a.id=b.event_id where alarm = 1
        $ips = $model->getAll($field,$where);

        array_walk($ips,function($arr,$key) use(&$datalist){
            $src = $this->location->getlongLat($arr['src_ip']);
            $src[0] = strval($src[0]);
            $src[1] = strval($src[1]);
            $dst = $this->location->getlongLat($arr['dst_ip']);
            $dst[0] = strval($dst[0]);
            $dst[1] = strval($dst[1]);
            //判断源地址和目标地址是否相同，如果相同就过滤
            if(!\Tools::isEmpty(array_diff($src,$dst))){
                
                //来源点
                $userDataItem = [
                    'name' => $arr['src_ip'],
                    'value' => $src,
                    'detail' => [
                        'alarm' => $arr['alarm'],
                        'src_ip' => $arr['src_ip'],
                        'dst_ip' => $arr['dst_ip'],
                        'src_host' => $arr['src_host'],
                        'dst_host' => $arr['dst_host'],
                        'src_hostname' => $arr['src_hostname'],
                        'dst_hostname' => $arr['dst_hostname'],
                        'risk_a' => $arr['risk_a'],
                        'risk_c' => $arr['risk_c'],
                    ],
                ];
                
                //攻击线
                $lineDataItem = [
                    'coords' => [
                        $src,
                        $datalist['data']['platformData'][0]['value'],
                        
                    ],
                    'ruleRiskLevel' => $arr['alarm'],
                ];
                
                $datalist['data']['userData'][] = $userDataItem; 
                $datalist['data']['lineData'][] = $lineDataItem; 

            }

        });

        jsonResult($datalist);    

    }
    /*  
    * 威胁来源
    */
    public function bottom1Action(){
        $model = new Alarm();
        $field = 'inet6_ntoa(a.src_ip) src_ip';
        $datalist = [
            'title' => '威胁来源',
            'data' => []
        ];
        $ips = $model->getAll($field);
        if(!Tools::isEmpty($ips)){
            $ips = array_column($ips,'src_ip');
            $temp = [];
            
            while(!Tools::isEmpty(current($ips))){
                $iso = $this->location->getisoCode(current($ips));
                if(array_key_exists($iso,$temp)){
                    $temp[$iso]++;
                }else{
                    $temp[$iso] = 1;
                }

                next($ips);
            }

            while(!Tools::isEmpty(key($temp))){
                $datalist['data'][] = [
                    'name' => key($temp),
                    'value' => strval(current($temp)),
                ];
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
          $data = [
            'title'=>'资产统计',
            'data'=> $host->typeCount(),
        ];
       jsonResult($data);

    }
}
