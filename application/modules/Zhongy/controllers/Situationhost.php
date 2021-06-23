<?php
use App\Models\Es;
use App\Models\PluginSid;
use App\Models\Zhujws;
use App\Models\Event;
/* 
 * 主机防护--一周事件
 */
class SituationhostController extends Base{
	protected $model=null;
    protected $es;
    //依赖
    protected $DateRange;
    protected $num = 23;  //这是为了防止普通用户访问 而故意写了一个超级管理员的权限数字 传入checkAuth（）内，这样肯定不能访问
    public function init(){
        parent::init();
        $this->es = new Es();
        $this->DateRange = new \zhongy_DateRange();
        $this->checkAuth($this->num);
	}

	public function indexAction(){
		echo __METHOD__;
	}

	public function topAction(){

		$Zhujws = new Zhujws();
		list($offline,$online) = $Zhujws->assetStatus();
	
		$timestamp = date("Y-m-d H:i:s",strtotime("-6 day")).'|'.date("Y-m-d H:i:s");

		$ret = array(
			array(
				'key' => '总在线数量(个)',
				'value' => $online,
				'type' => 'online',
				'icon' => 'icon-zaixianshuliang',
				'searchItem'   => [
					'online_state' => 1,
				],
			),
			array(
				'key' => '总离线数量(个)',
				'value' => $offline,
				'type' => 'offline',
				'icon' => 'icon-lixian',				
				'searchItem'   => [
					'online_state' => 0,
				],

			),
			array(
				'key' => '上报事件数',
				'value' => 0,
				'type' => 'upNum',
				'icon' => 'icon-shangbaopng'				
			),
			array(
				'key' => '移动存储控制',
				'value' => 0,
				'type' => 'mobile',
				'icon' => 'icon-yidong',
				'searchItem'   => [
					'timestamp' => $timestamp,
					'plugin_id' => 100026,
					'plugin_sid' => 15,
				],
			),
			array(
				'key' => '核心进程防护',
				'value' => 0,
				'type' => 'internet',
				'icon' => 'icon-wenjianxiugai',
				'searchItem'   => [
					'timestamp' => $timestamp,
					'plugin_id' => 100026,
					'plugin_sid' => 7,
				]
			),
		);

		//取得最近7天的数据
		$table = $this->DateRange->weekIndex();
		$post = '{"query":{"bool":{"must":[{"range":{"@timestamp":{"gte":"'.date("Y-m-d",strtotime("-6 day")).'","lte":"now","format":"yyyy-MM-dd"}}},{"term":{"plugin_id.keyword":{"value":100026}}}]}}}';
		
		$ret[2]['value'] = $this->es->count($table,$post);
		//移动存储控制（系统事件）
		$post = '{"query":{"bool":{"must":[{"range":{"@timestamp":{"gte":"'.date("Y-m-d",strtotime("-6 day")).'","lte":"now","format":"yyyy-MM-dd"}}},{"term":{"plugin_id":{"value":100026}}},{"term":{"plugin_sid":{"value":15}}}]}}}';
		$ret[3]['value'] = $this->es->count($table,$post);
		//核心进程防护
		$post = '{"query":{"bool":{"must":[{"range":{"@timestamp":{"gte":"'.date("Y-m-d",strtotime("-6 day")).'","lte":"now","format":"yyyy-MM-dd"}}},{"term":{"plugin_id":{"value":100026}}},{"term":{"plugin_sid":{"value":7}}}]}}}';
		$ret[4]['value'] = $this->es->count($table,$post);


		jsonResult($ret);
	}

	/**
	* 前七天事件
	*/
	public function leftAction(){
		$post = '{"query":{"bool":{"must":[{"term":{"plugin_id.keyword":{"value":100026}}},{"range":{"@timestamp":{"gte":"'.date("Y-m-d H:i:s",strtotime("-6 day")).'","lte":"now","format":"yyyy-MM-dd HH:mm:ss"}}}]}},"aggs":{"1d":{"date_histogram":{"field":"@timestamp","format":"yyyy-MM-dd","interval":"1d","min_doc_count":0,"extended_bounds":{"min":"'.date("Y-m-d",strtotime("-6 day")).'","max":"now/d"}}}},"size":0}';
		//取得最近7天的数据
		$table = $this->DateRange->weekIndex();

		$r = $this->es->query($table,$post);
		
		$ret = [];
		$keys = ['key','value'];

		if(!Tools::isEmpty($r['aggregations']['1d']['buckets'])){
			foreach($r['aggregations']['1d']['buckets'] as $item) {

                    if(array_key_exists('key',$item)&&sizeof($item)>2) unset($item['key']);
                    $ret[] = array_combine($keys,$item);
              }


		}

		jsonResult($ret);
	}

	/**
	* 十大主机事件类型
	*/
	public function rightAction(){
		$redis = new \phpredis();
		
		$plugin_id = input('plugin_id','');

        /*$key = $_SESSION['uid'] .'-'.$plugin_id.'-'. __METHOD__;
        if ($result = $redis->get($key)) {

            jsonResult(json_decode($result,true));

        } else {*/

        	if(empty($plugin_id)){
        		$post = '{"aggs":{"type":{"terms":{"field":"plugin_sid.keyword","size":8}}},"size":0}';
        	}else{
        		$post = '{"query":{"bool":{"must":[{"term":{"plugin_id":{"value":'.$plugin_id.'}}}]}},"aggs":{"type":{"terms":{"field":"plugin_sid.keyword","size":10}}},"size":0}';
        	}


			//取得最近7天的数据
			$table = $this->DateRange->weekIndex();

			$r = $this->es->query($table,$post);

			
			$ret = [];
			$keys = ['name','value'];
	        $PluginSid = new PluginSid();


			if(!Tools::isEmpty($r['aggregations']['type']['buckets'])){
				foreach($r['aggregations']['type']['buckets'] as &$item) {

	                    if(array_key_exists('key',$item)&&sizeof($item)>2) unset($item['key']);

	                    //查询数据库所对应的插件名称
	                    $name = $PluginSid->getName(['sid'=>$item['key']]);
	                    $item['key'] = Tools::isEmpty($name) ? $item['key'] : $name;
	                    $ret[] = array_combine($keys,$item);
	              }


			}


		//缓存
		//$PluginSid->setCache($key, $ret,300);	

		jsonResult($ret);


       // }

	}


	/**
	* 实时防护事件
	*/
	public function bottom1Action(){
		//$post = '{"_source":{"excludes":["userdata*"],"includes":["src_*","dst_*","device","interface","fdate","plugin*","event_id","log"]},"from":0,"size":100,"sort":[{"@timestamp":{"order":"desc"}}]}';
		$post = '{"query":{"bool":{"must":[{"term":{"plugin_id.keyword":{"value":100026}}}]}},"from":0,"size":100,"sort":[{"@timestamp":{"order":"desc"}}]}';
		//取得最近7天的数据
		$table = $this->DateRange->weekIndex();

		$r = $this->es->query($table,$post);
        $Event = new Event();
		$ret = [];
		if(!Tools::isEmpty($r['hits']['hits'])){
			foreach ($r['hits']['hits'] as $arr) {

                $arr['_source']['src_hostname'] = $Event->ipToHostname($arr['_source']['src_ip']);	
                $arr['_source']['dst_hostname'] = $Event->ipToHostname($arr['_source']['dst_ip']);	
                $arr['_source']['device_hostname'] = $Event->ipToHostname($arr['_source']['device']);	
				$arr['_source']['fdate'] = date('H:i:s',strtotime($arr['_source']['fdate']));
				$arr['_source']['plugin_sid_desc'] = Tools::isEmpty($arr['_source']['plugin_sid_desc']) ? '未知' : $arr['_source']['plugin_sid_desc'];
				$arr['_source']['eventname'] = $arr['_source']['plugin_sid_desc'];
				$ret[] = $arr['_source'];
			}
		}
		jsonResult($ret);
	}

	/**
	* 主机状态列表
	*/
	public function bottom2Action(){
		echo __METHOD__;
	}
}