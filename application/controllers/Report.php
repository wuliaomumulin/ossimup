<?php
use App\Models\Asset;
use App\Models\Es;
use App\Models\Factory;
use App\Models\Report;
use App\Models\Company;
use App\Models\Log;
use App\Models\Userreference;
//异常报表
class ReportController extends Base{
	protected $model = null;

    private $debug = false;
    private $debug_file = './cache/sql.tmp';


    public function init()
    {
       parent::init();
       $this->model = new Report();
    }
    /**
    *  模板列表
    */
    public function querylistAction(){
        try{
            $name = input('name');
            $is_screen = input('is_screen');//是否为大屏
            $page       = input('page/d', 0);
            $pagesize   = input('pagesize/d', 10);
            $where = [];
            if($name<>''){
                $where['name'] = array('like',"%{$name}%");
                $fields = 'id,name,content,thumb,is_mine,is_screen,ctime,utime';
            }
            if($is_screen<>''){
                $where['is_screen'] = array('eq',$is_screen);
                $fields = 'id,name,thumb';
            }
            
            $res = $this->model->getallreport(@$fields,$where,$page,$pagesize);
            jsonResult($res); 
        }catch(Exception $e){
            jsonError($e->getMessage());
        }
    }

    /**
    * 各种类资产数量统计
    * @facid String 厂站ID
    * @time String 时间

    */
    public function object1Action(){
        $title = '各种类资产数量统计';
        $where = self::renametime(self::prev());//partial
        $asset = new Asset();
        $data = $asset->allasset('b.asset_type_name name,count(1) value',$where,'a.asset_type','left join asset_type b on a.asset_type = b.asset_type_id');

        jsonResult(self::format($title,$data));
    }

    /**
    * 安全状况统计 异常电厂数量/总电厂数量
    */
    public function object2Action(){
        //输入数据
        $where = self::prev();

        
        $Es = new Es();
        $index = 'data_event';

        //异常电厂统计
        $params = self::getparams('factory',$where);
        $params['aggs']['facname']['terms']["field"] = "facname";
        $params['aggs']['facname']['terms']["size"] = 1000;//估约异常电厂个数
        $params['size'] = 0;
        $r = $Es->getOriginalList($index,$params);

        //异常电厂数量
        $tmp_factory['title'] = '异常电厂数量/总电厂数量';
        $tmp_factory['data'] = [
            ['name'=>'异常电厂','value'=>sizeof($r['aggregations']['facname']['buckets'])],
            ['name'=>'电厂总量','value'=>sizeof($params['query']['bool']['must'][1]['terms']['facname'])],
        ];


        jsonResult($tmp_factory);
    }

    /**
    * 安全状况统计 异常资产数量/总资产数量
    */
    public function object3Action(){
        $Es = new Es();
        $index = 'data_event';

        //异常资产统计
        //输入数据
        $where = self::prev();
        $params = self::getparams('asset',$where);
        $params['aggs']['device']['terms']["field"] = "device";
        $params['aggs']['device']['terms']["size"] = 10000;//估约异常资产个数
        $params['size'] = 0;
        $r = $Es->getOriginalList($index,$params);

        //异常资产数量
        $tmp_asset['title'] = '异常资产数量/总资产数量';
        $tmp_asset['data'] = [
            ['name'=>'异常资产','value'=>sizeof($r['aggregations']['device']['buckets'])],
            ['name'=>'资产总量','value'=>sizeof($params['query']['bool']['filter']['bool']['should'][0]['terms']['src_id.keyword'])],
        ];

        jsonResult($tmp_asset);
    }
    /**
    * 监管资产数量统计
    * 告警数量统计
    * 探针数量统计
    * 最新告警统计
    */
    public function object4Action(){
        $title = '数量统计';
        $result = [];
        $where = self::renametime(self::prev());

        $asset = new Asset();
        $val = $asset->allcount('1',$where);
        $result[] = ['name'=>'监管资产','value'=>(int)$val];

        $where['b.asset_type_soruce'] = 2;//用户探针资产
        $val = $asset->allcount('1',$where,'left join asset_type b on a.asset_type = b.asset_type_id');
        $result[] = ['name'=>'探针资产','value'=>(int)$val];
        unset($where['b.asset_type_soruce']);

        $params = self::getparams('asset',$where);
        $params['size'] = 0;

        $Es = new Es;
        $index = 'data_event';
        $r = $Es->getOriginalList($index,$params);
        $result[] = ['name'=>'告警数量','value'=>$r['hits']['total']];

        //最新告警
        jsonResult(self::format($title,$result)); 
    }
    /**
    * 聚合接口
	* @params $title 标题
	* @params $agg 一层聚合
	* @params $agg2 二层聚合
    */
    public function aggsAction(){
        $title = input('title','');
        $agg = input('agg','');
        $agg2 = input('agg2','');
        $step = input('step','');
        if(Tools::isEmpty($title)) jsonError('无标题');
        if(Tools::isEmpty($agg)) jsonError('聚合方式不能为空');
        if(!Tools::isEmpty($agg2)) $param['agg2'] = $agg2;
        if($agg===$agg2) jsonError('两次聚合不能相同');
        if(!Tools::isEmpty($step)) $param['step'] = $step;
        $param['agg'] = $agg;
        $param['title'] = $title;
        self::_chartstack($param);
    }

    /**
    * 厂站威胁资产排名
    */
    public function object5Action(){
       self::_chartstack(['agg'=>'device','title'=>'厂站威胁资产排名']);          
    }
    /**
    * 受威胁厂站排名
    */
    public function object6Action(){ 
       self::_chartstack(['agg'=>'facname','title'=>'受威胁厂站排名']);         
    }

    /**
    * 厂站威胁来源排名
    */
    public function object7Action(){
       self::_chartstack(['agg'=>'src_ip','title'=>'厂站威胁来源排名']);       
    }

    /**
    * 厂站威胁目标排名
    */
    public function object8Action(){
       self::_chartstack(['agg'=>'dst_ip','title'=>'厂站威胁目标排名']);
    }

    /** 实施厂站总数量 */
    public function object9Action(){
        //输入数据
        $where = self::prev();
        $tmp_factory['title'] = '实施厂站总数量';

        $tmp_factory['data'] = [
            ['name'=>'实施厂站总数量','value'=>sizeof(explode(',', $where['factory_id'][1]))]
        ];

        jsonResult($tmp_factory);
    }
    /** 正常厂站总数量 */
    public function object10Action(){
        //输入数据
        $where = self::prev();

        $Es = new Es();
        $index = 'data_event';
        $total = sizeof(explode(',', $where['factory_id'][1]));
        //正常厂站总数量
        $params = self::getparams('factory',$where);
        $params['aggs']['facname']['terms']["field"] = "facname";
        $params['aggs']['facname']['terms']["size"] = 1000;//估约异常电厂个数
        $params['size'] = 0;
        $r = $Es->getOriginalList($index,$params);
        $fail = $total-sizeof($r['aggregations']['facname']['buckets']);


        $tmp_factory['title'] = '正常厂站总数量';
        $tmp_factory['data'] = [
            ['name'=>'正常厂站总数量','value'=>$fail]
        ];


        jsonResult($tmp_factory);
    }


    /** 异常厂站总数量 */
    public function object11Action(){
        //输入数据
        $where = self::prev();

        $Es = new Es();
        $index = 'data_event';
        
        //异常电厂统计
        $params = self::getparams('factory',$where);
        $params['aggs']['facname']['terms']["field"] = "facname";
        $params['aggs']['facname']['terms']["size"] = 1000;//估约异常电厂个数
        $params['size'] = 0;
        $r = $Es->getOriginalList($index,$params);

        //异常电厂数量
        $tmp_factory['title'] = '异常厂站总数量';
        $tmp_factory['data'] = [
            ['name'=>'异常厂站总数量','value'=>sizeof($r['aggregations']['facname']['buckets'])],
        ];


        jsonResult($tmp_factory);
    }
    /**
    * 一周告警事件统计
    */
    public function object12Action(){
       self::_chartstack(['agg'=>'timestamp','step'=>'1d','title'=>'一周告警事件统计']);
    }
    /**
    * 一天告警事件统计
    */
    public function object13Action(){
       self::_chartstack(['agg'=>'timestamp','step'=>'1h','title'=>'一天告警事件统计']);
    }

    /**
    * 公司视角--各公司实施厂站数量
    */
    public function object14Action(){
        //输入数据
        $where = self::prev();

        $Factory = new Factory();
        $field = 'b.comname name,count(a.comid) `value`';
        $join = 'left join company b on a.comid=b.comid';
        $whe['a.facid'] = $where['factory_id'];
        $group = 'b.comname';
        $result = self::maxItems($Factory->alias('a')->field($field)->where($whe)->join($join)->group($group)->order('`value` desc')->select());
        
        //echo $Factory->getlastsql();

        $tmp_factory['data'] = $result;
        $tmp_factory['title'] = '各公司实施厂站数量';

        jsonResult($tmp_factory);
    

    }


    /**
    * 公司视角--各公司电厂离线数量
    */
    public function object15Action(){
       //输入数据
        $where = self::prev();

        $factory = new Factory();
        $whe['a.facid'] = $where['factory_id'];
        $whe['a.status'] = 0;//离线
        //$whe['a.comid'] = $where['comid'];
        $field = 'b.comname `name`,count(a.facid) `value`';
        $join = 'left join company b on a.comid=b.comid';
        $group = 'a.comid';
        $tmp_factory = [];
        $data = self::maxItems($factory->allfactory($field,$whe,$join,$group));
        
        //异常电厂数量
        $tmp_factory['title'] = '各公司电厂离线数量';
        $tmp_factory['data'] = $data;

        jsonResult($tmp_factory);
    }
    /**
    * 公司视角--运营商统计
    */
    public function object16Action(){
        //输入数据
        $where = self::prev();
        $factory = new Factory();
        $whe['a.facid'] = $where['factory_id'];
        $data = self::maxItems($factory->allfactory("ifnull(isp,'未知') `name`,count(facid) `value`",$whe,'','name'));

        $tmp_factory['title'] = '公司视角--运营商统计';
        $tmp_factory['data'] = $data;
        jsonResult($tmp_factory);
    }

    /**
    * 公司视角--VPDN统计
    */
    public function object17Action(){
        //输入数据
        $where = self::prev();
        
        $model = new Factory();
        $whe['a.facid'] = $where['factory_id'];
        $data = $model->allfactory("ifnull(a.isp,'未知') `name`,count(a.facid) `value`",$whe,'left join localdb b on a.simnum=b.simNum','a.isp');
        $tmp_factory['title'] = '公司视角--VPDN统计';
        $tmp_factory['data'] = $data;
        jsonResult($tmp_factory);
    }

    /**
    * 告警级别统计 
    */
    public function object18Action(){
       self::_chartstack(['agg'=>'level','title'=>'告警级别统计']);
    }
    /**
    * 公司视角--各公司资产数量
    */
    public function object19Action(){
        //输入数据
        $where = self::prev();
        $model = new Asset();
        $whe['a.factory_id'] = $where['factory_id'];

        $data = $model->allasset('c.comname `name`,count(a.asset_id) `value`',$whe,'c.comid','left join factory b on a.factory_id = b.facid left join company c on b.comid = c.comid');
        $tmp_factory['title'] = '公司视角--各公司资产数量';
        $tmp_factory['data'] = $data;
        jsonResult($tmp_factory);
    }
    /**
    * 事件数量
    */
    public function object20Action(){
        $tmp_factory['title'] = '事件数量';
        $tmp_factory['data'] = [
            ['name' => '事件数量','value' => self::esTotal('data_event'),]
        ];
        jsonResult($tmp_factory);
    }
    /**
    * 日志数量
    */
    public function object21Action(){
        $tmp_factory['title'] = '日志数量';
        $tmp_factory['data'] = [
            ['name' => '日志数量','value' => self::esTotal('data_flow'),]
        ];
        jsonResult($tmp_factory);
    }
    /**
    * 公司数量
    */
    public function object22Action(){
        //输入数据
        $where = self::prev();

        $tmp_factory['title'] = '公司数量';
        $tmp_factory['data'] = [
            ['name' => '公司数量','value' => sizeof(explode(',',$where['comid'][1]))]
        ];
        jsonResult($tmp_factory);
    }
    /**
    * 部署数量
    */
    public function object23Action(){
        //输入数据
        $where = self::prev();

        $tmp_factory['title'] = '部署数量';
        $tmp_factory['data'] = [
            ['name' => '部署数量','value' => sizeof(explode(',',$where['factory_id'][1]))]
        ];
        jsonResult($tmp_factory);
    }
    /**
    * 平台数量
    */
    public function object24Action(){
        //输入数据
        $where = self::prev();
        $whe['a.factory_id'] = $where['factory_id'];
        $whe['a.asset_type'] = ['in','88,89'];

        $asset = new Asset();
        $count = $asset->allcount('a.asset_id',$whe);


        $tmp_factory['title'] = '平台数量';
        $tmp_factory['data'] = [
            ['name' => '平台数量','value' =>  $count]
        ];
        jsonResult($tmp_factory);
    }
    /**
    * 探针数量
    */
    public function object25Action(){
        //输入数据
        $where = self::prev();
        $whe['a.factory_id'] = $where['factory_id'];
        $whe['a.asset_type'] = ['in','90,91,92'];

        $asset = new Asset();
        $count = $asset->allcount('a.asset_id',$whe);      
        $tmp_factory['title'] = '探针数量';
        $tmp_factory['data'] = [
            ['name' => '探针数量','value' => $count]
        ];
        jsonResult($tmp_factory);
    }
    /**
    * 资产数量
    */
    public function object26Action(){
        //输入数据
        $where = self::prev();
        $whe['a.factory_id'] = $where['factory_id'];

        $asset = new Asset();
        $count = $asset->allcount('a.asset_id',$whe);
        $tmp_factory['title'] = '资产数量';
        $tmp_factory['data'] = [
            ['name' => '资产数量','value' => $count]
        ];
        jsonResult($tmp_factory);
    }


    /**
    * 公司视角--电厂类型统计
    */
    public function getfactorynumAction(){
        //输入数据
        $where = self::prev();
       
        $whe['facid'] = $where['factory_id'];
        $result = [
            'title' => '公司视角--电厂类型统计',
            'data' => $this->model->factypesum($whe),

        ];
        
        jsonResult($result);
    }

    /**
    * 厂站各级别威胁排名
    */
    public function stackobject1Action(){
       self::_chartstack(['agg'=>'level','agg2'=>'facname','title'=>'厂站各级别威胁排名']);
    }

    /**
    * 厂站各告警类型威胁排名
    */
    public function stackobject2Action(){
        self::_chartstack(['agg'=>'alarm_type','agg2'=>'facname','title'=>'厂站各告警类型威胁排名']);
    }

    /**
    * 资产各告警类型威胁统计
    */
    public function stackobject3Action(){
        self::_chartstack(['agg'=>'alarm_type','agg2'=>'device','title'=>'资产各告警类型威胁统计']);
    }
    /**
    * 资产各告警级别统计
    */
    public function stackobject4Action(){
        self::_chartstack(['agg'=>'level','agg2'=>'device','title'=>'资产各告警级别统计']);
    }
    /**
    * 一周各种类告警事件统计
    */
    public function stackobject5Action(){
       self::_chartstack(['agg'=>'alarm_type','agg2'=>'timestamp','step'=>'1d','title'=>'一周各种类告警事件统计']);
    }

    /**
    * 一周各种类告警事件趋势
    */
    public function stackobject6Action(){

        /**
        * 7天数据
        */

        //输入数据
        $where = self::prev();
        $Es = new Es();
        $index = 'data_event';

        $params = self::getparams('factory',$where);

        //前置如果是按照时间聚合，需要指定字段
        $aggtime = ['fdate','date','timestamp'];
        //最大时间
        $max = $params['query']['bool']['must'][0]['range']['@timestamp']['lte'];

        //存取7天数据
        $sevenday = [
                        "min" => Tools::getSimpleDate(strtotime($max)-518400),//前七天
                        "max" => Tools::getSimpleDate(strtotime($max)),
                    ];
        $params['aggs']['alarm_type']['terms']["field"] = 'alarm_type';
        $params['aggs']['alarm_type']['terms']["size"] = 10;//估约异常电厂个数
        $params['aggs']['alarm_type']['aggs']['1d']['date_histogram']['field']='@timestamp';
        $params['aggs']['alarm_type']['aggs']['1d']['date_histogram']['interval']='1d';
        $params['aggs']['alarm_type']['aggs']['1d']['date_histogram']['format'] =  'yyyy-MM-dd';
        $params['aggs']['alarm_type']['aggs']['1d']['date_histogram']['min_doc_count'] =  0;
        $params['aggs']['alarm_type']['aggs']['1d']['date_histogram']['extended_bounds'] =  $sevenday;          
        $params['size'] = 0;

        $r = $Es->getOriginalList($index,$params);

        //配置参数
        $title = '一周各种类告警事件趋势';
        $data = [];
        $par = [
            'aggsAs' => 'data',
        ];
        if(!Tools::isEmpty($r['aggregations']['alarm_type']['buckets'])){                                  
                array_walk($r['aggregations']['alarm_type']['buckets'],function($a) use(&$data,$par){
                    $name = array_key_exists('key_as_string',$a) ? $a['key_as_string'] : $a['key'];
                    $temp = [
                        'name' => $name,
                        $par['aggsAs'] => [],
                        'tmp' => [],
                    ];
                    if(!Tools::isEmpty($a['1d']['buckets'])){
                        foreach($a['1d']['buckets'] as $fac) {

                            //如果存在时间戳，就将他去掉
                            if(array_key_exists('key',$fac)&&sizeof($fac)>2) unset($fac['key']);
                            $temp[$par['aggsAs']][] = array_combine(['time','value'],$fac);
                            $temp['tmp'][] = $fac['key'];
                        }
                    }
                    array_push($data,$temp);
                });

                //拼装最长数量
                $maxTmp = [];
                array_walk_recursive(array_column($data,'tmp'),function($value) use(&$maxTmp){
                    array_push($maxTmp,$value);
                }); 
                $maxTmp = array_unique($maxTmp);

                //填充空数据

               foreach ($maxTmp as $name) {
                    foreach ($data as $key => $val) {
                        if(Tools::isEmpty($data[$key]['tmp'])){
                            $data[$key][$par['aggsAs']][] = ['time'=>$name,'value'=>0];
                        }else{
                            if(!in_array($name,$data[$key]['tmp'])){
                                $data[$key][$par['aggsAs']][] = ['time'=>$name,'value'=>0];  
                            }
                        }
                    }
               }

               foreach ($data as $key => $val) {
                   unset($data[$key]['tmp']);
               }
               unset($maxTmp);


            }else{
                //如果没有威胁
                $data = [
                    [
                        'name' => '暂无类型1',
                         $par['aggsAs'] => [
                            [
                                'time' => '暂无类型2',
                                'value' => 0
                            ]
                        ]
                    ],
                ];
                
                jsonResult(self::format($title,$data),'厂站暂无威胁');
            }


            jsonResult(self::format($title,$data));        


        //$min = 5;$max =50;
       // $title = '一周各种类告警事件趋势';
        // $data = [
        //     ['name'=>"分类".Tools::getRandom($min, $max),'data'=>[ 
        //         ['value'=>Tools::getRandom($min, $max),'time'=>date("Y-m-d",strtotime("-6 day"))], 
        //         ['value'=>Tools::getRandom($min, $max),'time'=>date("Y-m-d",strtotime("-5 day"))], 
        //         ['value'=>Tools::getRandom($min, $max),'time'=>date("Y-m-d",strtotime("-4 day"))], 
        //         ['value'=>Tools::getRandom($min, $max),'time'=>date("Y-m-d",strtotime("-3 day"))], 
        //         ['value'=>Tools::getRandom($min, $max),'time'=>date("Y-m-d",strtotime("-2 day"))], 
        //         ['value'=>Tools::getRandom($min, $max),'time'=>date("Y-m-d",strtotime("-1 day"))], 
        //         ['value'=>Tools::getRandom($min, $max),'time'=>date("Y-m-d")], 

        //     ]],
        //     ['name'=>"分类".Tools::getRandom($min, $max),'data'=>[
        //         ['value'=>Tools::getRandom($min, $max),'time'=>date("Y-m-d",strtotime("-6 day"))], 
        //         ['value'=>Tools::getRandom($min, $max),'time'=>date("Y-m-d",strtotime("-5 day"))], 
        //         ['value'=>Tools::getRandom($min, $max),'time'=>date("Y-m-d",strtotime("-4 day"))], 
        //         ['value'=>Tools::getRandom($min, $max),'time'=>date("Y-m-d",strtotime("-3 day"))], 
        //         ['value'=>Tools::getRandom($min, $max),'time'=>date("Y-m-d",strtotime("-2 day"))], 
        //         ['value'=>Tools::getRandom($min, $max),'time'=>date("Y-m-d",strtotime("-1 day"))], 
        //         ['value'=>Tools::getRandom($min, $max),'time'=>date("Y-m-d")],
        //      ]],
        //     ['name'=>"分类".Tools::getRandom($min, $max),'data'=>[
        //         ['value'=>Tools::getRandom($min, $max),'time'=>date("Y-m-d",strtotime("-6 day"))], 
        //         ['value'=>Tools::getRandom($min, $max),'time'=>date("Y-m-d",strtotime("-5 day"))], 
        //         ['value'=>Tools::getRandom($min, $max),'time'=>date("Y-m-d",strtotime("-4 day"))], 
        //         ['value'=>Tools::getRandom($min, $max),'time'=>date("Y-m-d",strtotime("-3 day"))], 
        //         ['value'=>Tools::getRandom($min, $max),'time'=>date("Y-m-d",strtotime("-2 day"))], 
        //         ['value'=>Tools::getRandom($min, $max),'time'=>date("Y-m-d",strtotime("-1 day"))], 
        //         ['value'=>Tools::getRandom($min, $max),'time'=>date("Y-m-d")],
        //     ]],
        //     ['name'=>"分类".Tools::getRandom($min, $max),'data'=>[
        //         ['value'=>Tools::getRandom($min, $max),'time'=>date("Y-m-d",strtotime("-6 day"))], 
        //         ['value'=>Tools::getRandom($min, $max),'time'=>date("Y-m-d",strtotime("-5 day"))], 
        //         ['value'=>Tools::getRandom($min, $max),'time'=>date("Y-m-d",strtotime("-4 day"))], 
        //         ['value'=>Tools::getRandom($min, $max),'time'=>date("Y-m-d",strtotime("-3 day"))], 
        //         ['value'=>Tools::getRandom($min, $max),'time'=>date("Y-m-d",strtotime("-2 day"))], 
        //         ['value'=>Tools::getRandom($min, $max),'time'=>date("Y-m-d",strtotime("-1 day"))], 
        //         ['value'=>Tools::getRandom($min, $max),'time'=>date("Y-m-d")],
        //     ]],
        //     ['name'=>"分类".Tools::getRandom($min, $max),'data'=>[
        //         ['value'=>Tools::getRandom($min, $max),'time'=>date("Y-m-d",strtotime("-6 day"))], 
        //         ['value'=>Tools::getRandom($min, $max),'time'=>date("Y-m-d",strtotime("-5 day"))], 
        //         ['value'=>Tools::getRandom($min, $max),'time'=>date("Y-m-d",strtotime("-4 day"))], 
        //         ['value'=>Tools::getRandom($min, $max),'time'=>date("Y-m-d",strtotime("-3 day"))], 
        //         ['value'=>Tools::getRandom($min, $max),'time'=>date("Y-m-d",strtotime("-2 day"))], 
        //         ['value'=>Tools::getRandom($min, $max),'time'=>date("Y-m-d",strtotime("-1 day"))], 
        //         ['value'=>Tools::getRandom($min, $max),'time'=>date("Y-m-d")],
        //     ]],
        // ];
        jsonResult(self::format($title,$data)); 
    }

    /*
    *  地图
    */
    public function mapAction(){
        $temp = self::prev();
        $where['facid'] = $temp['factory_id'];
        $model =new Factory();
        $title = '电厂统计';
        $datalist = array('title'=>$title);
        $tem = $model->allfactory('a.check_time,a.status,b.comname,a.facname,a.factype,a.address,a.contact,a.telphone,a.longitude,a.latitude,a.memo,a.ip,a.nickname',$where,'LEFT JOIN  company b ON a.comid = b.comid'); 

        sort($tem);   
        $datalist['data'] = $tem;
        jsonResult($datalist);
    }
    /*
    *  电厂异常统计
    */
    public function errorfactoryAction(){
        $temp = self::prev();
        $where['facid'] = $temp['factory_id'];
        $model =new Factory();
        $title = '电厂异常统计';

        $datalist = array('title'=>$title);
        $where['a.`status`'] = 0;//1正常|0异常
        $tem = $model->allfactory("b.comname,a.facid,a.facname,a.factype,a.check_time checktime,a.contact,a.telphone,a.nickname,a.facstatus",$where,'left join company b on a.comid=b.comid');

        foreach ($tem as &$a) {
            if(!is_null($a['facid'])){
                $temp1 = $model->query("select asset_nickname name,asset_type,b.asset_type_name type,a.asset_attrs->>'$.abnormalcauses' abnormalcauses,a.asset_attrs->>'$.checktime' checktime from asset a left join asset_type b on a.asset_type=b.asset_type_id where a.factory_id in ({$a['facid']}) and a.asset_status=0 and a.asset_attrs->>'$.status'=1");
                //sql printr plugin
                if($this->debug){
                    file_put_contents($this->debug_file, $model->getlastsql() . PHP_EOL, FILE_APPEND);
                }
                if(!Tools::isEmpty($temp1)){
                    foreach($temp1 as $arr){
                        //format采集器
                       if(in_array($arr['asset_type'],[90,91,92])){
                            $a['sensors'][] = ['name'=>Tools::substr($arr['type'],0,2),'des'=>$arr['abnormalcauses']];
                       }
                       //format格式化平台
                        if(in_array($arr['asset_type'],[88,89])){
                            $a['platforms'][] = ['name'=>$arr['type'],'des'=>$arr['abnormalcauses']];
                       }        
                    }      
                }else{
                    //默认置空
                    $a['sensors'] = [];
                    $a['platforms'] = [];
                } 
            }    
        }
        //补零方法
        $datalist['data'] = $tem;
        jsonResult($datalist);
    }

    //电厂实施趋势
    public function carryouttrendAction(){
        $temp = self::prev();
        $data = $this->model->isp7day();

        //通过数据库查询数据
        $model = new Company;
        $datalist = array();


        $where = 'date_sub(curdate(), INTERVAL 7 DAY) <= date(c.asset_creation_time) and b.facid in('.$temp['factory_id'][1].')';

        $tem = $model->allcompany("count(1) doc_count,ifnull(c.asset_attrs->>'$.isp','未知') isp,date_format(c.asset_creation_time, '%Y-%m-%d') ctime,c.asset_status",$where,'c.asset_attrs->>"$.isp"','LEFT JOIN factory b ON a.comid = b.comid LEFT JOIN asset c ON b.facid = c.factory_id','a.ctime desc');

        //插槽(将数据库返回的值插入初始化的数据集)
        if(!is_null($tem)){
            array_walk($tem,function($arr) use(&$data){

                switch ($arr['ctime']) {
                    case date("Y-m-d",strtotime("-6 day")):
                        $data['isp']['data'][0]['value'] += (int)$arr['doc_count'];
                        //运行商判断
                        switch ($arr['isp']) {
                            case '电信':
                                $data['isp']['data'][0]['dx'] = (int)$arr['doc_count'];
                                break;
                            case '联通':
                                $data['isp']['data'][0]['lt'] = (int)$arr['doc_count'];
                                break;
                            case '移动':
                                $data['isp']['data'][0]['yd'] = (int)$arr['doc_count'];
                                break;
                            default:
                                # code...
                                break;
                        }


                        break;
                    case date("Y-m-d",strtotime("-5 day")):
                        $data['isp']['data'][1]['value'] += (int)$arr['doc_count'];
                        switch ($arr['isp']) {
                            case '电信':
                                $data['isp']['data'][1]['dx'] = (int)$arr['doc_count'];
                                break;
                            case '联通':
                                $data['isp']['data'][1]['lt'] = (int)$arr['doc_count'];
                                break;
                            case '移动':
                                $data['isp']['data'][1]['yd'] = (int)$arr['doc_count'];
                                break;
                            default:
                                # code...
                                break;
                        }


                        break;
                    case date("Y-m-d",strtotime("-4 day")):
                        $data['isp']['data'][2]['value'] += (int)$arr['doc_count'];
                        switch ($arr['isp']) {
                            case '电信':
                                $data['isp']['data'][2]['dx'] = (int)$arr['doc_count'];
                                break;
                            case '联通':
                                $data['isp']['data'][2]['lt'] = (int)$arr['doc_count'];
                                break;
                            case '移动':
                                $data['isp']['data'][2]['yd'] = (int)$arr['doc_count'];
                                break;
                            default:
                                # code...
                                break;
                        }

                        break;
                    case date("Y-m-d",strtotime("-3 day")):
                        $data['isp']['data'][3]['value'] += (int)$arr['doc_count'];
                        switch ($arr['isp']) {
                            case '电信':
                                $data['isp']['data'][3]['dx'] = (int)$arr['doc_count'];
                                break;
                            case '联通':
                                $data['isp']['data'][3]['lt'] = (int)$arr['doc_count'];
                                break;
                            case '移动':
                                $data['isp']['data'][3]['yd'] = (int)$arr['doc_count'];
                                break;
                            default:
                                # code...
                                break;
                        }

                        break;
                    case date("Y-m-d",strtotime("-2 day")):
                        $data['isp']['data'][4]['value'] += (int)$arr['doc_count'];
                        switch ($arr['isp']) {
                            case '电信':
                                $data['isp']['data'][4]['dx'] = (int)$arr['doc_count'];
                                break;
                            case '联通':
                                $data['isp']['data'][4]['lt'] = (int)$arr['doc_count'];
                                break;
                            case '移动':
                                $data['isp']['data'][4]['yd'] = (int)$arr['doc_count'];
                                break;
                            default:
                                # code...
                                break;
                        }

                        break;
                    case date("Y-m-d",strtotime("-1 day")):
                        $data['isp']['data'][5]['value'] += (int)$arr['doc_count'];
                        switch ($arr['isp']) {
                            case '电信':
                                $data['isp']['data'][5]['dx'] = (int)$arr['doc_count'];
                                break;
                            case '联通':
                                $data['isp']['data'][5]['lt'] = (int)$arr['doc_count'];
                                break;
                            case '移动':
                                $data['isp']['data'][5]['yd'] = (int)$arr['doc_count'];
                                break;
                            default:
                                # code...
                                break;
                        }


                        break;
                    case date("Y-m-d"):
                        $data['isp']['data'][6]['value'] += (int)$arr['doc_count'];
                        switch ($arr['isp']) {
                            case '电信':
                                $data['isp']['data'][6]['dx'] = (int)$arr['doc_count'];
                                break;
                            case '联通':
                                $data['isp']['data'][6]['lt'] = (int)$arr['doc_count'];
                                break;
                            case '移动':
                                $data['isp']['data'][6]['yd'] = (int)$arr['doc_count'];
                                break;
                            default:
                                # code...
                                break;
                        }


                        break;
                    default:
                        # code...
                        break;
                }
            
            });
        }
 

        //返回该数据集
        jsonResult($data);
    }

    /**
    * 告警列表
    */
    public function list1Action(){
        $where = self::prev();
        $params = self::getparams('factory',$where);
        $Es = new Es;
        $r = $Es->getOriginalList('data_event',$params);
        $data  = [];
        array_walk($r['hits']['hits'],function(&$v) use(&$data){$v['_source']['timestamp'] = substr(str_replace('T'," ",$v['_source']['@timestamp']),0,19).'.'.substr($v['_source']['@timestamp'],-4,-1);unset($v['_source']['@timestamp'],$v['_source']['message'],$v['_source']['log']);$data[]=$v['_source'];return true;});
        $tmp_factory['title'] = '告警列表';
        $tmp_factory['data'] = $data;
        jsonResult($tmp_factory);
    }

    /*
    * 根据角色组织params基础参数
    * $scence factory|asset 
    */
    protected function getparams($scence,$where = []){
        //公共抽取时间格式  前端设定 有开始必有结尾  
        if (isset($where['begindate']) && !empty($where['begindate'])) {
            //拼接ES
            $params['query']['bool']['must'][]['range']['@timestamp'] = [
                "gte" => $where['begindate'],//'2019-09-01 12:00:00',
                "lte" => $where['enddate'],//'2019-09-09 12:00:00',
                "format" => "yyyy-MM-dd HH:mm:ss"
                //,"time_zone"=>"+08:00"
            ];

            //删除多余字段
            unset($where['begindate']);
            unset($where['enddate']);
        }

        //应用场景        
        switch($scence) {
            case 'factory':   
                //电厂统计
                $factory = new Factory;
                $wh =[];
                if(isset($where['factory_id'])){
                    $wh['a.facid'] = $where['factory_id'];
                }

                //确认厂站
                $temp = $factory->allfactory('facname',$wh);

                if($this->debug){
                    file_put_contents($this->debug_file, $factory->getlastsql() . PHP_EOL, FILE_APPEND);
                }
                if(is_null($temp)) jsonError('暂无厂站');
                $factoryname = array_values(array_column($temp,'facname'));
                $params['query']['bool']['must'][]['terms']['facname'] = $factoryname;     
                return $params;
                break;
            case 'company':   
                //公司统计
                $Company = new Company;
                $wh =[];
                if(isset($where['company_id'])){
                    $wh['a.company_id'] = $where['company_id'];
                }
                
                //确认厂站
                $temp = $Company->allcompany('comname',$wh);
                if($this->debug){
                    file_put_contents($this->debug_file, $Company->getlastsql() . PHP_EOL, FILE_APPEND);
                }
                if(is_null($temp)) jsonError('暂无公司');

                $comname = array_values(array_column($temp,'comname'));
                $filter['bool']['should']=[
                    ['terms'=>['level_1_company'=>$comname]],
                    ['terms'=>['level_2_company'=>$comname]],
                    ['terms'=>['level_3_company'=>$comname]],
                ];
                $params['query']['bool']['filter'] = $filter;  
                return $params;
                break;
            case 'asset':
                //资产统计
                $asset = new Asset();
                $temp = $asset->allasset('es_id',$where);
                if(is_null($temp)) jsonError('暂无资产');
                if($this->debug){
                    file_put_contents($this->debug_file, $asset->getlastsql() . PHP_EOL, FILE_APPEND);
                }
                $es_id = array_values(array_unique(array_column($temp,'es_id')));
                $filter['bool']['should']=[
                    ['terms'=>['src_id.keyword'=>$es_id]],
                    ['terms'=>['dst_id.keyword'=>$es_id]],
                    ['terms'=>['device_id.keyword'=>$es_id]]
                ];
                $params['query']['bool']['filter'] = $filter;

                return $params;
                break;
            default:
                jsonError('scence无效');
                break;
        }
    }
    //格式化数据
    protected function format($title,$data){
        $result = [];
        $result['title'] = $title;

        if(is_array($data)){
            //判断是否有该key值
            if(array_key_exists('name',$data[0])){
            $result['data'] = $data;
            }else{
                $result['data'][] = [
                    'name'=>mb_substr($title,0,4),
                    'value'=>0//设置成字符串
                ];
            }
        }else{
            //如果是一个字符串，那么将title截取一部分，将$data赋值给title即可
            if(is_null($data)){
                $result['data'][] = [
                    'name'=>mb_substr($title,0,4),
                    'value'=>0//设置成字符串
                ];
            }else{
                $result['data'][] = [
                    'name'=>mb_substr($title,0,4),
                    'value'=>$data
                ];
            }     
        }
        
        return $result;
    }
    /**
    *  前置方法
    */
    private function prev(){
        $where = [];
        $where['begindate'] = input('begindate',date('Y-m-d H:i:s',(time()-$_SESSION['es_show_time'])));
        $where['enddate'] = input('enddate',date('Y-m-d H:i:s'));
        $ref = $this->renamefacid(input('facid',''));

        if(!empty($ref)){
            if(!empty($ref['facid'])){
               $where['factory_id'] = ['in',$ref['facid']];
            }
            if(!empty($ref['comid'])){
               $where['company_id'] = ['in',$ref['comid']];
            }
        }
        if(empty($where['factory_id'])){
            jsonError('您没有选择厂站?');
        }

        return $where;
    }
    /**
    *   后置ES过滤方法
    */
    private function next($r){
        array_walk($r['hits']['hits'],function(&$v){$v['_source']['timestamp'] = substr(str_replace('T'," ",$v['_source']['@timestamp']),0,19).'.'.substr($v['_source']['@timestamp'],-4,-1);unset($v['_source']['@timestamp']);return true;});
        return $r;
    }
    /**
    * 当去查数据库的时候，修改字段key
    */
    private function renametime($where){
        $where['a.asset_creation_time'] = array(['egt',$where['begindate']],['elt',$where['enddate']]);
        unset($where['begindate'],$where['enddate']);
        return $where;
    }
    /**
    * 从ES反射到数据库里
    */
    private function reflection($es){
        /*
        * 第一步
        */
        $where = ['es_id'=>[
            'in'
        ]];
        if(sizeof($es['hits']['hits'])>0){
            array_walk($es['hits']['hits'],function($a) use(&$where){
                //var_dump($a['_source']);
                $where['es_id'][1][] = $a['_source']['src_id'];
                $where['es_id'][1][] = $a['_source']['dst_id'];
                $where['es_id'][1][] = $a['_source']['device_id'];
            });
        }
        //去重
        $where['es_id'][1] = array_unique($where['es_id'][1]);
        //第二步
        $field = '1';
        $asset = new Asset();
        return $asset->allcount($field, $where);
    }
    /**
    * 由于给的有的是厂站，有的是公司，区分web端属性，返回厂站
    */
    private function renamefacid($temp){
		if(empty($temp)) return '';
        $tem = explode(',',$temp);
        $data = [];
        array_walk($tem,function($val) use(&$data){
            if($val[0]=='f'){
                $data['facid'][]=substr($val,1);
            }
            if($val[0]=='c'){
                $data['comid'][]=substr($val,1);
            }
        });
        $data['facid'] = implode(',',$data['facid']);
        $data['comid'] = implode(',',$data['comid']);
        return $data;
    }
    /*
    * stack 聚合堆叠图
    * @params [
    *      'agg'=>'alarm_type',
           'agg2'=>'facname',
           'title'='厂站各告警类型威胁排名',
           'step' => 1y(年),1q(季度),1d(月份),1w(星期),1d(每天),1h(一小时),1m(分钟),1s(second)//判断时间间隔
           ]
    */
    private function _chartstack($par = ['agg'=>'alarm_type','title'=>'厂站各告警类型威胁排名','step'=>'1d']){

         //输入数据
        $where = self::prev();
        $Es = new Es();
        $index = 'data_event';

        $params = self::getparams('factory',$where);

        //前置如果是按照时间聚合，需要指定字段
        $aggtime = ['fdate','date','timestamp'];
        //最大时间
        $max = $params['query']['bool']['must'][0]['range']['@timestamp']['lte'];

        //存取7天数据
        $sevenday = [
                        "min" => Tools::getSimpleDate(strtotime($max)-518400),//前七天
                        "max" => Tools::getSimpleDate(strtotime($max)),
                    ];
        //存取1天数据
        $oneday = [
                        "min" => Tools::getFullDate(strtotime($max)-86400),//一天
                        "max" => Tools::getFullDate(strtotime($max)),
                    ];            


        //是否是双重聚合

        if(isset($par['agg2'])){

            $params['aggs'][$par['agg']]['terms']["field"] = $par['agg'];
            $params['aggs'][$par['agg']]['terms']["size"] = 10;//估约异常电厂个数
            
            if(in_array($par['agg'],$aggtime)){
                if($par['agg']=='timestamp'){
                    
                        //判断时间间隔
                        $par['agg'] = $par['step'];
                        //间隔值
                        $interval  = Tools::substr($par['step'],-1);
                        switch($interval){
                            //按天
                            case 'd':                                
                                $params['aggs'][$par['agg']]['date_histogram']['field']='@timestamp';
                                $params['aggs'][$par['agg']]['date_histogram']['interval']=$par['agg'];
                                $params['aggs'][$par['agg']]['date_histogram']['format'] =  'yyyy-MM-dd';
                                $params['aggs'][$par['agg']]['date_histogram']['min_doc_count'] =  0;
                                $params['aggs'][$par['agg']]['date_histogram']['extended_bounds'] = $sevenday;
                                break; 
                            //按时
                            case 'h':                                
                                $params['aggs'][$par['agg']]['date_histogram']['field']='@timestamp';
                                $params['aggs'][$par['agg']]['date_histogram']['interval']=$par['agg'];
                                $params['aggs'][$par['agg']]['date_histogram']['format'] =  'yyyy-MM-dd HH:mm:ss';
                                $params['aggs'][$par['agg']]['date_histogram']['min_doc_count'] =  0;
                                $params['aggs'][$par['agg']]['date_histogram']['extended_bounds'] = $oneday;
                                break;                            
                            default:
                                jsonError('step参数不正确');
                                break;
                        }                    
                        

                }
            }

            if(in_array($par['agg2'],$aggtime)){
                if($par['agg2']=='timestamp'){
                    
                    //判断时间间隔
                    $par['agg2'] = $par['step'];
                    //间隔值
                    $interval  = Tools::substr($par['step'],-1);
                    
                    switch($interval){
                            //按天
                            case 'd':                                
                                $params['aggs'][$par['agg']]['aggs'][$par['agg2']]['date_histogram']['field']='@timestamp';
                                $params['aggs'][$par['agg']]['aggs'][$par['agg2']]['date_histogram']['interval']=$par['agg2'];
                                $params['aggs'][$par['agg']]['aggs'][$par['agg2']]['date_histogram']['format'] =  'yyyy-MM-dd';
                                $params['aggs'][$par['agg']]['aggs'][$par['agg2']]['date_histogram']['min_doc_count'] =  0;
                                $params['aggs'][$par['agg']]['aggs'][$par['agg2']]['date_histogram']['extended_bounds'] =  $sevenday;
                                break; 
                            //按时
                            case 'h':                                
                                $params['aggs'][$par['agg']]['aggs'][$par['agg2']]['date_histogram']['field']='@timestamp';
                                $params['aggs'][$par['agg']]['aggs'][$par['agg2']]['date_histogram']['interval']=$par['agg2'];
                                $params['aggs'][$par['agg']]['aggs'][$par['agg2']]['date_histogram']['format'] =  'yyyy-MM-dd HH:mm:ss';
                                $params['aggs'][$par['agg']]['aggs'][$par['agg2']]['date_histogram']['min_doc_count'] =  0;
                                $params['aggs'][$par['agg']]['aggs'][$par['agg2']]['date_histogram']['extended_bounds'] =  $oneday;
                                break;                            
                            default:
                                jsonError('step参数不正确');
                                break;
                    }


                }
            }else{
                    $params['aggs'][$par['agg']]['aggs'][$par['agg2']]['terms']['field'] = $par['agg2'];
                    $params['aggs'][$par['agg']]['aggs'][$par['agg2']]['terms']['size'] = 10;
                    $params['aggs'][$par['agg']]['aggs'][$par['agg2']]['terms']['min_doc_count'] = 0;  
            }
            $params['size'] = 0;
            $r = $Es->getOriginalList($index,$params);

            //获取默认图表
            $data = array();

            //电厂
            $title = $par['title'];

            if(!Tools::isEmpty($r['aggregations'][$par['agg']]['buckets'])){                                  
                array_walk($r['aggregations'][$par['agg']]['buckets'],function($a) use(&$data,$par){
                    $name = array_key_exists('key_as_string',$a) ? $a['key_as_string'] : $a['key'];
                    $temp = [
                        'name' => $name,
                        'factory' => [],
                        'tmp' => [],
                    ];
                    if(!Tools::isEmpty($a[$par['agg2']]['buckets'])){
                        foreach($a[$par['agg2']]['buckets'] as $fac) {

                            //如果存在时间戳，就将他去掉
                            if(array_key_exists('key',$fac)&&sizeof($fac)>2) unset($fac['key']);
                            $temp['factory'][] = array_combine(['name','value'],$fac);
                            $temp['tmp'][] = $fac['key'];
                        }
                    }
                    array_push($data,$temp);
                });

                //拼装最长数量
                $maxTmp = [];
                array_walk_recursive(array_column($data,'tmp'),function($value) use(&$maxTmp){
                    array_push($maxTmp,$value);
                }); 
                $maxTmp = array_unique($maxTmp);

                //填充空数据

               foreach ($maxTmp as $name) {
                    foreach ($data as $key => $val) {
                        if(Tools::isEmpty($data[$key]['tmp'])){
                            $data[$key]['factory'][] = ['name'=>$name,'value'=>0];
                        }else{
                            if(!in_array($name,$data[$key]['tmp'])){
                                $data[$key]['factory'][] = ['name'=>$name,'value'=>0];  
                            }
                        }
                    }
               }

               foreach ($data as $key => $val) {
                   unset($data[$key]['tmp']);
               }
               unset($maxTmp);


            }else{
                //如果没有威胁
                $data = [
                    [
                        'name' => '暂无类型1',
                        'factory' => [
                            [
                                'name' => '暂无类型2',
                                'value' => 0
                            ]
                        ]
                    ],
                ];
                
                jsonResult(self::format($title,$data),'厂站暂无威胁');
            }
            jsonResult(self::format($title,$data));


        }else{
        
            if(in_array($par['agg'],$aggtime)){
                if($par['agg']=='timestamp'){

                    //判断时间间隔
                    $par['agg'] = $par['step'];
                    //间隔值
                    $interval  = Tools::substr($par['step'],-1);

                    switch($interval){
                        //按天
                        case 'd':                                
                            $params['aggs'][$par['agg']]['date_histogram']['field']='@timestamp';
                            $params['aggs'][$par['agg']]['date_histogram']['interval']=$par['agg'];
                            $params['aggs'][$par['agg']]['date_histogram']['format'] =  'yyyy-MM-dd';
                            $params['aggs'][$par['agg']]['date_histogram']['min_doc_count'] =  0;
                            $params['aggs'][$par['agg']]['date_histogram']['extended_bounds'] = $sevenday;
                            break; 
                        //按时
                        case 'h':                                
                            $params['aggs'][$par['agg']]['date_histogram']['field']='@timestamp';
                            $params['aggs'][$par['agg']]['date_histogram']['interval']=$par['agg'];
                            $params['aggs'][$par['agg']]['date_histogram']['format'] =  'yyyy-MM-dd HH:mm:ss';
                            $params['aggs'][$par['agg']]['date_histogram']['min_doc_count'] =  0;
                            $params['aggs'][$par['agg']]['date_histogram']['extended_bounds'] = $oneday;
                            break;                            
                        default:
                            jsonError('step参数不正确');
                            break;
                    }

                }
                //其他处理


            }else{
                $params['aggs'][$par['agg']]['terms']["field"] = $par['agg'];
                $params['aggs'][$par['agg']]['terms']["size"] = 10;//估约异常电厂个数  
            }
            
            $params['size'] = 0;
            $r = $Es->getOriginalList($index,$params);
            /*
            * 处理返回数据
            * 电厂             
            */
            $title = $par['title'];
            $data = [];
            if(!Tools::isEmpty($r['aggregations'][$par['agg']]['buckets'])){
                foreach ($r['aggregations'][$par['agg']]['buckets'] as $a) {
                    //如果存在时间戳，就将他去掉
                   if(array_key_exists('key',$a)&&sizeof($a)>2) unset($a['key']);
                   //每项数据，加到返回集当中

                   $data[]=array_combine(['name','value'],$a);
                }

                //填充空数据
                //$data = self::_filllist($data);

            }
            jsonResult(self::format($title,$data));

        }
        
    }
    /**
    * 最大10项,填充空数据
    */
    private function _filllist($data){
        if(sizeof($data)<10){
            $data[] = [
                'name'=> '暂无数据',
                'value'=>0
            ];
            return self::_filllist($data);
        }
        return $data;
    }
    /**
    * 聚合参数列表
    */
    public function aggsparamsAction(){
        $data = $this->model->aggparams();
        $title = '聚合参数列表';
        jsonResult(self::format($title,$data));
    }
    /**
    * 最大输出项，默认为5项
    */
    private static function maxItems($data,$max=5){
        if(sizeof($data)>$max){
            $result = array_slice($data,0,5);
            $result[] = array('name'=>'其他','value'=>array_sum(array_column(array_slice($data,5),'value')));
            $data = $result;
        }
        return $data;
    }

    /**
    * Es统计数量
    */
    private function esTotal($index = 'data_event'){
        //输入数据
        $where = self::prev();
        $Es = new Es();
        $params = self::getparams('factory',$where);
        $params['size'] = 0;
        $r = $Es->getOriginalList($index,$params);
        return $r['hits']['total'];
    }

    //信息保存
    public function saveAction()
    {
        $log = new Log();
        try {
            $model = $this->model;
            if (!$model->create()){   
                $errtips = $model->getError();
                echo resultstatus('002',$errtips);                
            }else{
                $id = input('id');
                if ($id>0){
                    
                    $result = $model->where($model->getPk()." ='{$id}' ")->save();
                    
                    //修改用户关联
                    self::setUserReferenceHook();

                } else {
                        $result = $model->add();
                        //重新赋值id
                        $id = $result;
                }


                if ($result===false){
                    $da = array('user_name'=>$_SESSION['username'],'log_event'=>$model->getModelName(),'log_ip'=>get_client_ip(),'remark'=>'保存信息失败');
                    echo resultstatus(1,'保存信息失败');
                } else {
                     $da = array('user_name'=>$_SESSION['username'],'log_event'=>$model->getModelName(),'log_ip'=>get_client_ip(),'remark'=>'保存信息成功');
                    echo resultstatus(0,'保存信息成功',['id'=>$id]);
                }                 
            }
        } catch(Exception $e){
            $da = array('user_name'=>$_SESSION['username'],'log_event'=>$model->getModelName(),'log_ip'=>get_client_ip(),'remark'=>$e->getMessage());
            echo resultstatus($e->getCode(),$e->getMessage());            
        }
        $log->syslogadd($da);
    }

    //文件上传
    public function uploadAction()
    {
        set_time_limit(300);
        $id = input('post.id','');
        if(!Tools::isEmpty($_FILES['thumb']) and !Tools::isEmpty($id)){

            $upload = new spUploadFile();
            $data['thumb'] = $upload->upload_file($_FILES['thumb'],"jpg|png|gif|jpeg",'thumb/');        
            $data['bgImgUrl'] = $upload->upload_file($_FILES['bgImgUrl'],"jpg|png|gif|jpeg",'thumb/');

            /**修改文件属性*/
            self::savethumb($data,'上传文件成功');

            jsonResult($data);
        }else{
            jsonError('没有上传文件');
        }
        
    }

    private function savethumb($data){
        $id = input('id',0);
        $model = $this->model;
        $content = $model->where("id={$id}")->getField("content");
        $arr = json_decode($content,true);
        $arr['addForm']['bgImgUrl'] = $data['bgImgUrl'];
        unset($data['bgImgUrl']);
        $data['content'] = json_encode($arr);
        $result = $model->where("id={$id}")->save($data);
        if(intval($result) > 0){
            //echo resultstatus(200,'succuss');
        }else{

        }
    }
    /**
    * 当修改默认大屏的时候，会同时修改menu的router,以适应默认首页
    */
    private function setUserReferenceHook():void
    {
        $post = input('post.');
        if(sizeof($post) == 2){
            if(!Tools::isEmpty($post['id']) && !Tools::isEmpty($post['is_screen'])){
            
                $Userreference = new Userreference();
                $uid = $_SESSION['uid'];
                $Userreference->where("uid={$uid}")->setField('menu_id',$post['id']);
            }
        }
    }

}