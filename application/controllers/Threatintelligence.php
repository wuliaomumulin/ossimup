<?php
use App\Models\ThreatIntelligence;
use App\Models\Log;
use App\Models\UdpSensor;
use App\Models\Es;

class ThreatintelligenceController extends Base
{
    protected $model = null;
    protected $config = null;
    protected $uid = null;
    protected $rid = null;
    protected $role = null;
    protected $es;
    protected $conf = './list_conf/threatIntelligence.json';
    protected $num = 53;
 
    public function init()
    {  
/*                ini_set('display_errors', 1);
        ini_set('display_startup_errors', 1);*/
        parent::init();
        
        $this->config = \Yaf\Registry::get("config");
        $this->model = new ThreatIntelligence();
        $this->uid = $_SESSION['uid'];
        $this->rid = $_SESSION['rid'];
        $this->role = [1, 2, 3];//系统内置角色
        $this->es = new Es();
        $this->checkAuth($this->num);
         
    }
    //列表
    public function querylistAction()
    {  

        $input['page']                = input('page',1);
        $input['pagesize']            = input('page_size',10);
        $input['type']                = input('type','url');
        $input['family']                = input('family','');
        $input['severity']                = input('severity','');
        $input['begindate']                = input('begindate',date('Y-m-d H:i:s',strtotime("-9 day")));
        $input['enddate']                = input('enddate',date('Y-m-d H:i:s'));

        $zhongy_Threatintelligence = new zhongy_Threatintelligence();

        $method = $this->getRequest()->getActionName();

        $post = $zhongy_Threatintelligence->$method($input);

        $ret = array();
        $table = 'threatintelligence';
        $redis = new \phpredis();

        //配置config
        $json = file_get_contents($this->conf);
        $config = json_decode($json, 256);

        $ret['config'] =  $config;
        $ret['list'] =  [];

        $r = $this->es->query($table,$post);

        foreach($r['hits']['hits'] as &$arr) {
           $name = $redis->hashGet('table-ip-hostname',$arr['_source']['device']);
           $arr['_source']['name'] = \Tools::isEmpty($name) ? '未知' : $name;
           $name = $redis->hashGet('table-ip-hostname',$arr['_source']['src_ip']);
           $arr['_source']['src_name'] = \Tools::isEmpty($name) ? '未知' : $name;
           $name = $redis->hashGet('table-ip-hostname',$arr['_source']['dst_ip']);
           $arr['_source']['dst_name'] = \Tools::isEmpty($name) ? '未知' : $name;
           $arr['_source']['created_at'] = date('Y-m-d H:i:s',$arr['_source']['created_at']);
           $arr['_source']['find_at'] = date('Y-m-d H:i:s',$arr['_source']['find_at']);
           $arr['_source']['update_at'] = date('Y-m-d H:i:s',$arr['_source']['update_at']);
           $ret['list'][] = $arr['_source'];
        }

        $ret['total_num'] = $r['hits']['total']['value'];

        jsonResult($ret);

    }



    /**
    * 图表
    */
    public function chartAction(){
        $zhongy_Threatintelligence = new zhongy_Threatintelligence();
        $r = array();
    

        for($i=1;$i<4;$i++){
            $post = $zhongy_Threatintelligence->top($i);
            $table = 'threatintelligence';
            $r[] = $this->es->count($table,$post);
        }

        $ret = [
                'top' => [
                    'ip' => $r[0],
                    'domain' => $r[1],
                    'url' => $r[2],
                    'cc' => $r[0],
                    'ssl' => $r[0],
                ],
                'center' => [
                    ['name' => date('Y.m.d',strtotime("-9 day")),'value'=>0],
                    ['name' => date('Y.m.d',strtotime("-8 day")),'value'=>0],
                    ['name' => date('Y.m.d',strtotime("-7 day")),'value'=>0],
                    ['name' => date('Y.m.d',strtotime("-6 day")),'value'=>0],
                    ['name' => date('Y.m.d',strtotime("-5 day")),'value'=>0],
                    ['name' => date('Y.m.d',strtotime("-4 day")),'value'=>0],
                    ['name' => date('Y.m.d',strtotime("-3 day")),'value'=>0],
                    ['name' => date('Y.m.d',strtotime("-2 day")),'value'=>0],
                    ['name' => date('Y.m.d',strtotime("-1 day")),'value'=>0],
                     ['name' => date('Y.m.d'),'value'=>0],
                ]

            ];

        $post = $zhongy_Threatintelligence->common();
        $date = array_column($ret['center'],'name');
        foreach ($date as $i => $t) $ret['center'][$i]['value'] = $this->es->count($table.'-'.str_replace('.','-',$t),$post);

        jsonResult($ret);
    }

    public function bottomAction(){
        $zhongy_Threatintelligence = new zhongy_Threatintelligence();
        $method = substr(__FUNCTION__,0,-6);
        $post = $zhongy_Threatintelligence->$method();
        //$PluginSid = new PluginSid();

        $table = 'threatintelligence';

        $r = $this->es->query($table,$post);
        
        $ret = [];
        if(!Tools::isEmpty($r['hits']['hits'])){
            foreach ($r['hits']['hits'] as &$arr) {

                $ret[] = $arr['_source'];
            }
        }
        jsonResult($ret);
    }
    
}