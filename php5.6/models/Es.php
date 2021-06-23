<?php

namespace App\Models;

use App\Models\UserReference;

class Es extends Model{

	private $debug = false;
    private $debug_file = './cache/es.tmp';

	/**
	 * @abstract POST获取Elasticsearch 数据
	 * @author 王晓辉
	 * @datetime 2019-09-16    
	 * @param  string $tables [需要查询的索引(可以理解为表名))]
	 * @param  array  $params [以数组的形式 传递 需要查询的json]
	 * @return [type]         [返回一个数组]
	 */
	public function getList($tables = 'zhny-smart-collect',$params = []){

		if (!is_array($params) || empty($params)) return '无效的参数';
		
		$spHttp = new \spHttp();
		//$config = \Yaf\Registry::get("config");
		$url = $_SESSION['ES_HOSTNAME']. '/'.$tables.'*/_search';
		$head="Content-Type: application/json";

		$params = $this->getMatchSearch($tables,$params);

		$list = json_decode($spHttp->vpost($url, json_encode($params), $head), 256);
		$r = array();
		if($list['hits']['total']<>0){
			foreach($list['hits']['hits'] as $v) $r[] = $v['_source'];
		}
		return $r;
	}
	/**
	* 独立查询
	*/
	public function query($tables = 'zhny-smart-collect',$params = []){

		if (empty($params)) return '无效的参数';
		
		$params = is_array($params) ? json_encode($params) : $params;

		$spHttp = new \spHttp();

		$url = $_SESSION['ES_HOSTNAME']. '/'.$tables.'/_search';
		$head="Content-Type: application/json";
		$ret = json_decode($spHttp->vpost($url, $params, $head),256);
		return $ret;
	}

	/**
	* 增加一条数据
	* 如果数组的key不存在是会自动创建的
	*/
	public function add($tables = 'zhny-smart-collect',$params = []){

		if (!is_array($params) || empty($params)) return '无效的参数';
		
		$spHttp = new \spHttp();
		//$config = \Yaf\Registry::get("config");
		$url = $_SESSION['ES_HOSTNAME']. '/'.$tables.'/_doc';
		$head="Content-Type: application/json";

		$ret = json_decode($spHttp->vpost($url, json_encode($params), $head), 256);

		return (isset($ret['result']) && $ret['result'] == "created") ? true : false;
	}

	/**
	* 修改一条数据
	* 当传递的id存在为替换，不存在则创建
	*/
	public function replace($tables = 'zhny-smart-collect',$id='',$params = []){

		if (!is_array($params) || empty($params)) return '无效的参数';
		if(empty($id)) return '无效的主键';

		$spHttp = new \spHttp();
		//$config = \Yaf\Registry::get("config");
		$url = $_SESSION['ES_HOSTNAME']. '/'.$tables.'/_doc/'.$id;
		$head="Content-Type: application/json";

		$ret = json_decode($spHttp->vpost($url, json_encode($params), $head), 256);

		return $ret['result'];
	}


	/**
	* 删除一条数据
	* @input 根据条件删除数据
	*  {
      "_id":"jF3IKHMBiEm-uab_u3xz"
       }
	*/
	public function delete($tables = 'zhny-smart-collect',$input = []){

		if (!is_array($input) || empty($input)) return '无效的参数';

		$spHttp = new \spHttp();
		//$config = \Yaf\Registry::get("config");
		$url = $_SESSION['ES_HOSTNAME']. '/'.$tables.'/_doc/_delete_by_query';
		$head="Content-Type: application/json";

		$params = [
			'query' => [
				'match' => $input
			]
		];

		$ret = json_decode($spHttp->vpost($url, json_encode($params), $head), 256);

		return $ret['deleted'];
	}

	/**
	 * @abstract POST获取Elasticsearch 数据
	 * @author 王晓辉
	 * @datetime 2019-09-23   
	 * @param  string $tables [需要查询的索引(可以理解为表名))]
	 * @param  array  $params [以数组的形式 传递 需要查询的json]
	 * @return [type]         [返回一个数组]
	 */
	public function getAggregationsList($tables = 'zhny-smart-collect',$params = [],$aggregations = ''){

		if (!is_array($params) || empty($params)) return '无效的参数';
		
		$spHttp = new \spHttp();
		//$config = \Yaf\Registry::get("config");
		$url = $_SESSION['ES_HOSTNAME'] . '/'.$tables.'*/_search';
		$head="Content-Type: application/json"; 
		$list = json_decode($spHttp->vpost($url, json_encode($params), $head), 256);

		$r = array();
		if(!empty($list['aggregations'][$aggregations]['buckets'])){
			foreach($list['aggregations'][$aggregations]['buckets'] as $k=>$v){
				$r[$k]['key'] = $v['key'];
				$r[$k]['doc_count'] = $v['doc_count'];
			} 
		}
		return $r;
	}

	/**
	 * @abstract POST获取Elasticsearch 数据
	 * @author 王晓辉   
	 * @param  string $tables [需要查询的索引(可以理解为表名))]
	 * @param  array  $params [以数组的形式 传递 需要查询的json]
	 * @return [type]         [返回一个数组]
	 */
	public function getOriginalList($tables = 'zhny-smart-collect',$params = []){

		if (!is_array($params) || empty($params)) return '无效的参数';
		
		$spHttp = new \spHttp();
		$config = \Yaf\Registry::get("config");
		//$url = $_SESSION['ES_HOSTNAME'] . '/'.$tables.'*/_search';  
		$url = $_SESSION['ES_HOSTNAME'] . '/'.$tables.'*/_search';  
		$head="Content-Type: application/json"; 
		if($this->debug){
            file_put_contents($this->debug_file, json_encode($params) . PHP_EOL, FILE_APPEND);
        }
		$list = json_decode($spHttp->vpost($url, json_encode($params), $head), 256);

		return $list;
	}

	/**
	 * @abstract POST获取Elasticsearch 数据 2.0 版本
	 * @abstract ----->新增根据用户权限获取指定电厂数据<-----
	 * @author 王晓辉   
	 * @param  string $tables [需要查询的索引(可以理解为表名))]
	 * @param  array  $params [以数组的形式 传递 需要查询的json]
	 * @return [type]         [返回一个数组]
	 */
	public function getOriginalListV2($tables = 'data_event',$params = []){

		if (!is_array($params) || empty($params)) return '无效的参数';
		// 安全事件和告警事件 判断权限
		if ($tables == 'data_event' || $tables == 'data_flow') {
			//获取用户的电厂权限 从SESSION里面
			$facid = $_SESSION['user_reference']['facid'];
			//没有权限的不放行  
			!$facid && jsonError('用户暂未获取电厂授权/登陆过期,请重新登陆'); 
			// 全体权限 不需要增加单独的限定
			if($facid != 'all'){

			  $redis = new \phpredis();

			      $key = 'Models-Userreference-getuserassetpower-'.$_SESSION['uid'];

			      $asset = $redis->get($key);

			      if (!$rs) {

			            $asset = $this->table('asset')->field('es_id,asset_type,factory_id,company_id,asset_id')->where(['factory_id'=>['in', $_SESSION['user_reference']['facid']],'es_id'=>['neq'," "]])->select();
			         
			            $redis->set($key,json_encode($asset),0,0,600);
			        
			      }else{

			        $asset = json_decode($asset,1);

			      }

				// 如果查询到指定的电厂的话
				if (!empty($asset)) {
					$es_id = array_column($asset, 'es_id');
				
					$params['query']['bool']['should'] =[
												['terms'=>['src_id.keyword'=>$es_id]],
												['terms'=>['dst_id.keyword'=>$es_id]],
												['terms'=>['device_id.keyword'=>$es_id]]
											];													
				}
			}
		}
		
		$spHttp = new \spHttp();
		$config = \Yaf\Registry::get("config");
		$url = $_SESSION['ES_HOSTNAME'] . '/'.$tables.'*/_search';
		$head="Content-Type: application/json"; 
	
		$list = json_decode($spHttp->vpost($url, json_encode($params), $head), 256);

		return $list;
	}
	/**
	 * @abstract 根据条件 获取 es 的必要模糊搜索 json
	 * @param  string $tables [es索引]
	 * @param  string $search [模糊搜索的内容]
	 * @param  array  $index  [模糊搜索的范围 此处因为每个接口需要搜索的范围均有区别 故需要自己传递]
	 * @return [type]         [array]
	 */
	public function getMustSearch($search = '',$fields = []){

		if (empty($search) || empty($fields)) return false;
		
		$params = [];
		//此处ip 与 date  需要根据索引的改动而改动
		$ip = ['device','plugin_sid','dst_ip','src_ip','IP','DEVICE'];

		$date = ['CREATETIME','@timestamp'];
		$match_ip = preg_match("/^(?:25[0-5]|2[0-4]\d|1\d\d|[1-9]\d|\d)(?:[.](?:25[0-5]|2[0-4]\d|1\d\d|[1-9]\d|\d)){3}$/", $search);

		//如果没有匹配到ip格式   则不搜索Ip相关
		if ($match_ip != 1){ 
            foreach ($fields as $k => $v) {

            	if (in_array($v, $ip)) {
            		unset($fields[$k]);
            	}
            }
        // 如果匹配到IP格式  就只正对IP格式进行检索    
		}else{
			foreach ($fields as $k => $v) {

            	if (!in_array($v, $ip)) {
            		unset($fields[$k]);
            	}
            }
		}
		// ES 特性  时间格式中间以T隔开
		$search = str_replace(" ", 'T', $search);
		// 日期匹配
		$match_date = preg_match("/^[0-9]{4}-(0[1-9]|1[0-2])-(0[1-9]|[1-2][0-9]|3[0-1])$/",$search);
		//小时匹配
        $match_date_hous = preg_match("/^[0-9]{4}-(0[1-9]|1[0-2])-(0[1-9]|[1-2][0-9]|3[0-1])T[0-9]{2}$/",$search);  
        //分钟匹配
        $match_date_min = preg_match("/^[0-9]{4}-(0[1-9]|1[0-2])-(0[1-9]|[1-2][0-9]|3[0-1])T[0-9]{2}:[0-9]{2}$/",$search); 
        //秒匹配
         $match_date_second = preg_match("/^[0-9]{4}-(0[1-9]|1[0-2])-(0[1-9]|[1-2][0-9]|3[0-1])T[0-9]{2}:[0-9]{2}:[0-9]{2}$/",$search);
		//如果没有匹配到date格式   则不搜索date相关
		if ($match_date != 1 && $match_date_hous != 1 && $match_date_min != 1 && $match_date_second != 1) { 
        	foreach ($fields as $k => $v) {
            	if (in_array($v, $date)) {
            		unset($fields[$k]);
            	}
            }
		}else{
			foreach ($fields as $k => $v) {
            	if (!in_array($v, $date)) {
            		unset($fields[$k]);
            	}
            }
		}

		$rs['multi_match']['query'] = $search;
		$rs['multi_match']['fields'] = array_values($fields);
	
		return $rs;
	}

	/**
	 * @abstract 指定字段模糊搜索条件拼接
	 * @author wangxiaohui
	 * @param  string $index  索引/表名
	 * @param  array  $search [Array/Json 双格式传入]
	 * @return [type]         [description]
	 */
	public function getMatchSearch($index,$search){

		if (!empty($search)){
			//如果需要更改格式的话
			if (!is_array($search)) $search = json_decode($search,256);
			//公共抽取时间格式  前端设定 有开始必有结尾  
			if (isset($search['ts']) && !empty($search['ts'])) {
		        $timestamp = explode('|', $search['ts']);
				//拼接ES
				$params['query']['bool']['must'][]['range']['ts'] = [
	                "gte" => $timestamp[0],//'2019-09-01 12:00:00',
	                "lte" => $timestamp[1],//'2019-09-09 12:00:00',
	                "format" => "yyyy-MM-dd HH:mm:ss"
	                //,"time_zone"=>"+08:00"
            	];

            	//删除多余字段
            	unset($search['begindate']);
            	unset($search['enddate']);
			}

			//如果还有其他的搜索条件
			if (!empty($search)) {

				foreach ($search as $k => $v) {
					if(strlen($v) > 0){
						//进行数据过滤
						$rs = self::getIndexKey($index,$k,$v);
						if($rs !== FALSE) $params['query']['bool']['must'][][$rs['type']] = [$rs['key'] => $rs['search']];
					}
					
				}
			}
			
		}
		return $params;
	}

	/**
	 * @abstract 获取接口用的 ES 返回数据
	 * @author wangxiaohui
	 * @param  string $index  [索引/表名]
	 * @param  [type] $params [Array/Json 双格式传入]
	 * @return [type]         [Array]
	 */
	public function getDataList($index = 'data_event',$params){
		//输入限定
		if (empty($params)) return '无效的参数';
		//定义返回
		$rs = [];
		//兼容数组   并获取page_size
		if (is_array($params)) {
			$rs['page_size'] = $params['size'];
			$params  = json_encode($params,256);
		}else{
			//如果是json格式下 获取 size
			$rs['page_size'] = json_decode($params)['size'];
		}
		//构建POST 提交数据
		$spHttp = new \spHttp();
		$config = \Yaf\Registry::get("config");
		$url = $_SESSION['ES_HOSTNAME'] . '/'.$index.'*/_search';
		$head="Content-Type: application/json"; 
		$list = json_decode($spHttp->vpost($url, $params, $head), 256);
		//返回格式限定   总条数
		$rs['total_num'] = $list['hits']['total']?strval($list['hits']['total']):"0";
		//总页数
		$rs['total_page'] =strval(ceil($rs['total_num']/$rs['page_size'])) ;
		//List列表信息
		if ($rs['total_num'] > 0 ) {
			foreach ($list['hits']['hits'] as $k => $v) {
				$rs['list'][] = $v['_source'];
			}
		}else{
			$rs['list'] = [];
		}
		return $rs;
	}
	/**
	 * @abstract 匹配时间格式
	 */
	public function matchTime($search = ''){
		$search = str_replace(" ", 'T', $search);
        // 日期匹配
        $match_date = preg_match("/^[0-9]{4}-(0[1-9]|1[0-2])-(0[1-9]|[1-2][0-9]|3[0-1])$/",$search);
        //小时匹配
        $match_date_hous = preg_match("/^[0-9]{4}-(0[1-9]|1[0-2])-(0[1-9]|[1-2][0-9]|3[0-1])T[0-9]{2}$/",$search);  
        //分钟匹配
        $match_date_min = preg_match("/^[0-9]{4}-(0[1-9]|1[0-2])-(0[1-9]|[1-2][0-9]|3[0-1])T[0-9]{2}:[0-9]{2}$/",$search); 
        //秒匹配
        $match_date_second = preg_match("/^[0-9]{4}-(0[1-9]|1[0-2])-(0[1-9]|[1-2][0-9]|3[0-1])T[0-9]{2}:[0-9]{2}:[0-9]{2}$/",$search);
        //如果没有匹配到date格式   则不搜索date相关
        if ($match_date != 1 && $match_date_hous != 1 && $match_date_min != 1 && $match_date_second != 1) { 
        	return false;
		}else{
			return true;
		}
	}
	//获取UTC时间
	public function getUTCTime($time){

		// 首先进行截取  时间格式

		$time = str_replace("T", " ", $time);

		//去掉毫秒部分

		$time = substr($time,0,19);

		//转成时间戳

		$time = strtotime($time)+8*60*60;

		//返回UTC时间
		$time = date('Y-m-d H:i:s',$time);
		return $time;
	}

	/**
	 * @abstract 保存ES数据
	 * @param  [type] $table  [description]
	 * @param  [type] $id     [description]
	 * @param  [type] $params [description]
	 * @return [type]         [description]
	 */
	public function saveEsData($table,$id,$params){

	    $url = $_SESSION['ES_HOSTNAME']. '/'.$table.'/_doc/'.$id.'/_update';
        $head="Content-Type: application/json"; 
        $params = ['doc'=>$params];
 
        $rs = json_decode((new \spHttp())->vpost($url, json_encode($params), $head), 256);

        return $rs;
	}

	/**
	 * @abstract  根据不同的索引/表  对不同的字段的类型 进行分类匹配
	 * @author  wangxiaohui
	 * @param  [type] $index [索引/表]
	 * @param  [type] $key   [字段键名]
	 * @param  [type] $value [字段值]
	 * @return [type]        [Array]
	 * @ps     可以根据Es的变化进行修改  这个静态类
	 */
	private static function getIndexKey($index,$key,$value){

		if ($index == 'zhny-smart-collect') {
			switch ($key) {
				case 'vendor':
				    // 不分词的模糊搜索
					$rs['type'] = 'term';
					$rs['search'] = $value;
					$rs['key'] = $key;
					break;
				case 'dev':
				    // 不分词的模糊搜索
					$rs['type'] = 'term';
					$rs['search'] = $value;
					$rs['key'] = $key;
					break;
				case 'tag':
					//简称 从前向后匹配   
					$rs['type'] = 'term';
					$rs['search'] = $value;
					$rs['key'] = $key;
					break;
				case 'value':
					//简称 从前向后匹配   
					$rs['type'] = 'term';
					$rs['search'] = $value;
					$rs['key'] = $key;
					break;
				case 'src_ip':				
					//源IP 全匹配
					$rs['type'] = 'term';
					$rs['search'] = $value;
					$rs['key'] = $key;
					break;
				case 'src_port':
					//源端口 全匹配
					$rs['type'] = 'term';
					$rs['search'] = $value;
					$rs['key'] = $key;
					break;
				case 'dst_ip':
					//目的IP 全匹配
					$rs['type'] = 'term';
					$rs['search'] = $value;
					$rs['key'] = $key;
					break;
				case 'dst_port':
				    //目的端口 全匹配
					$rs['type'] = 'term';
					$rs['search'] = $value;
					$rs['key'] = $key;
					break;
				case 'uploadLogLevel':
				    // 不分词的模糊搜索
					$rs['type'] = 'wildcard';
					$rs['search'] = '*'.$value.'*';
					$rs['key'] = $key;
					break;
				case 'uploadLogLevelName':
					$rs['type'] = 'wildcard';
					$rs['search'] = '*'.$value.'*';
					$rs['key'] = $key;
					break;
				case 'level_1_company':
					$rs['type'] = 'wildcard';
					$rs['search'] = '*'.$value.'*';
					$rs['key'] = $key;
					break;
				case 'level_2_company': 
					$rs['type'] = 'wildcard';
					$rs['search'] = '*'.$value.'*';
					$rs['key'] = $key;
					break;
				case 'level_3_company':
					$rs['type'] = 'wildcard';
					$rs['search'] = '*'.$value.'*';
					$rs['key'] = $key;
					break;
				case 'uploadloglevelType':
					$rs['type'] = 'wildcard';
					$rs['search'] = '*'.$value.'*';
					$rs['key'] = $key;
					break;
				case 'platname':   
					$rs['type'] = 'wildcard';
					$rs['search'] = '*'.$value.'*';
					$rs['key'] = $key;
					break;
				case 'senname': 
					$rs['type'] = 'wildcard';
					$rs['search'] = '*'.$value.'*';
					$rs['key'] = $key;
					break;
				case 'level':  
					$rs['type'] = 'wildcard';
					$rs['search'] = '*'.$value.'*';
					$rs['key'] = $key;
					break;
				case 'alarm_type':   
					$rs['type'] = 'wildcard';
					$rs['search'] = '*'.$value.'*';
					$rs['key'] = $key;
					break;
				case 'alarm_name':   
					$rs['type'] = 'match';
					$rs['search'] = $value;
					$rs['key'] = $key;
					break;
				case 'device':				
					$rs['type'] = 'match';
					$rs['search'] = $value;
					$rs['key'] = $key;
					break;
				case 'plugin_id':
					$rs['type'] = 'match';
					$rs['search'] = $value;
					$rs['key'] = $key;
					break;
				case 'plugin_sid':
					$rs['type'] = 'match';
					$rs['search'] = $value;
					$rs['key'] = $key;
					break;
				case 'protocol':
					$rs['type'] = 'match';
					$rs['search'] = $value;
					$rs['key'] = $key;
					break;
				case 'priority':
					$rs['type'] = 'match';
					$rs['search'] = $value;
					$rs['key'] = $key;
					break;
				case 'pulses':
					$rs['type'] = 'match';
					$rs['search'] = $value;
					$rs['key'] = $key;
					break;
				case 'ctx':
					$rs['type'] = 'match';
					$rs['search'] = $value;
					$rs['key'] = $key;
					break;
				case 'log':
					$rs['type'] = 'wildcard';
					$rs['search'] = '*'.strtolower($value).'*';
					$rs['key'] = $key;
					break;
				case 'binary_data':
					$rs['type'] = 'wildcard';
					$rs['search'] = '*'.strtolower($value).'*';
					$rs['key'] = $key;
					break;
				case 'username':
					$rs['type'] = 'match';
					$rs['search'] = $value;
					$rs['key'] = $key;
					break;
				case 'password':
					$rs['type'] = 'match';
					$rs['search'] = $value;
					$rs['key'] = $key;
					break;
				case 'sensor_id':
					$rs['type'] = 'match';
					$rs['search'] = $value;
					$rs['key'] = $key;
					break;
				case 'interface':
					$rs['type'] = 'match';
					$rs['search'] = $value;
					$rs['key'] = $key;
					break;
				case 'sensor':
					$rs['type'] = 'match';
					$rs['search'] = $value;
					$rs['key'] = $key;
					break;
				case 'occurrences':
					$rs['type'] = 'match';
					$rs['search'] = $value;
					$rs['key'] = $key;
					break;	
				case 'userdata1':
					$rs['type'] = 'match';
					$rs['search'] = $value;
					$rs['key'] = $key;
					break;
				case 'userdata2':
					$rs['type'] = 'match';
					$rs['search'] = $value;
					$rs['key'] = $key;
					break;
				case 'userdata3':
					$rs['type'] = 'match';
					$rs['search'] = $value;
					$rs['key'] = $key;
					break;
				case 'userdata4':
					$rs['type'] = 'match';
					$rs['search'] = $value;
					$rs['key'] = $key;
					break;
				case 'userdata5':
					$rs['type'] = 'match';
					$rs['search'] = $value;
					$rs['key'] = $key;
					break;
				case 'userdata6':
					$rs['type'] = 'match';
					$rs['search'] = $value;
					$rs['key'] = $key;
					break;
				case 'userdata7':
					$rs['type'] = 'match';
					$rs['search'] = $value;
					$rs['key'] = $key;
					break;
				case 'userdata8':
					$rs['type'] = 'match';
					$rs['search'] = $value;
					$rs['key'] = $key;
					break;
				case 'userdata9':
					$rs['type'] = 'match';
					$rs['search'] = $value;
					$rs['key'] = $key;
					break;		
				default:
					return false;
					break;
			}

		}
		//恶意URL
		if ($index == 'threaturl') {
			switch ($key) {
				case 'HOSTNAME':
					$rs['type'] = 'term';
					$rs['search'] = $value;
					$rs['key'] = $key.'.keyword';
					break;
				case 'URL':
					//  前缀匹配  从前向后匹配   
					$rs['type'] = 'wildcard';
					$rs['search'] = '*'.$value.'*';
					$rs['key'] = $key.'.keyword';
					break;
				case 'LOG':
					$rs['type'] = 'wildcard';
					$rs['search'] = '*'.$value.'*';
					$rs['key'] = $key.'.keyword';
					break;
				case 'PLUGINID':
					$rs['type'] = 'term';
					$rs['search'] = $value;
					$rs['key'] = $key.'.keyword';
					break;
				case 'SEVERITY':
					$rs['type'] = 'wildcard';
					$rs['search'] = '*'.$value.'*';
					$rs['key'] = $key.'.keyword';
					break;
				case 'THREATTYPE':
					$rs['type'] = 'wildcard';
					$rs['search'] = '*'.$value.'*';
					$rs['key'] = $key.'.keyword';
					break;
				case 'STAMP':
					$rs['type'] = 'term';
					$rs['search'] = $value;
					$rs['key'] = $key;
					break;
				default:
					return false;
					break;
			}
		}
		//恶意IP
		if ($index == 'threatip') {
			switch ($key) {
				case 'IP':
					$rs['type'] = 'term';
					$rs['search'] = $value;
					$rs['key'] = $key.'.keyword';
					break;
				case 'OTXRESULT':
					//  前缀匹配  从前向后匹配   
					$rs['type'] = 'wildcard';
					$rs['search'] = '*'.$value.'*';
					$rs['key'] = $key.'.keyword';
					break;
				case 'THREATTYPE':
					$rs['type'] = 'wildcard';
					$rs['search'] = '*'.$value.'*';
					$rs['key'] = $key.'.keyword';
					break;
				case 'STAMP':
					$rs['type'] = 'term';
					$rs['search'] = $value;
					$rs['key'] = $key;
					break;
				case 'OTXSTATUS':
					$rs['type'] = 'term';
					$rs['search'] = $value;
					$rs['key'] = $key.'.keyword';
					break;
				case 'SEVERITY':
					$rs['type'] = 'term';
					$rs['search'] = $value;
					$rs['key'] = $key.'.keyword';
					break;
				default:
					return false;
					break;
			}
		}

		//恶意HOST
		if ($index == 'threathost') {
			switch ($key) {
				case 'HOST':
					//  前缀匹配  从前向后匹配   
					$rs['type'] = 'wildcard';
					$rs['search'] = '*'.$value.'*';
					$rs['key'] = $key.'.keyword';
					break;
				case 'OTXSTATUS':
					$rs['type'] = 'term';
					$rs['search'] = $value;
					$rs['key'] = $key.'.keyword';
					break;
				case 'THREATTYPE':
					$rs['type'] = 'wildcard';
					$rs['search'] = '*'.$value.'*';
					$rs['key'] = $key.'.keyword';
					break;
				case 'MLRESULT':
					$rs['type'] = 'term';
					$rs['search'] = $value;
					$rs['key'] = $key.'.keyword';
					break;
				case 'MLJSONSTRING':
					$rs['type'] = 'wildcard';
					$rs['search'] = '*'.$value.'*';
					$rs['key'] = $key.'.keyword';
					break;
				case 'STAMP':
					$rs['type'] = 'term';
					$rs['search'] = $value;
					$rs['key'] = $key;
					break;
				
				case 'SEVERITY':
					$rs['type'] = 'term';
					$rs['search'] = $value;
					$rs['key'] = $key.'.keyword';
					break;
				default:
					return false;
					break;
			}
		}
		//告警事件
		if ($index == 'zhny_enterprise_report') {
			switch ($key) {
				case 'vendor':
				    // 不分词的模糊搜索
					$rs['type'] = 'term';
					$rs['search'] = $value;
					$rs['key'] = $key;
					break;
				case 'dev':
				    // 不分词的模糊搜索
					$rs['type'] = 'term';
					$rs['search'] = $value;
					$rs['key'] = $key;
					break;
				case 'tag':
					//简称 从前向后匹配   
					$rs['type'] = 'term';
					$rs['search'] = $value;
					$rs['key'] = $key;
					break;
				case 'value':
					//简称 从前向后匹配   
					$rs['type'] = 'term';
					$rs['search'] = $value;
					$rs['key'] = $key;
					break;
				case 'level_1_company':
					$rs['type'] = 'wildcard';
					$rs['search'] = '*'.$value.'*';
					$rs['key'] = $key;
					break;
				case 'level_2_company': 
					$rs['type'] = 'wildcard';
					$rs['search'] = '*'.$value.'*';
					$rs['key'] = $key;
					break;
				case 'level_3_company':
					$rs['type'] = 'wildcard';
					$rs['search'] = '*'.$value.'*';
					$rs['key'] = $key;
					break;
				case 'uploadloglevelType':
					$rs['type'] = 'wildcard';
					$rs['search'] = '*'.$value.'*';
					$rs['key'] = $key;
					break;
				case 'platname':   
					$rs['type'] = 'wildcard';
					$rs['search'] = '*'.$value.'*';
					$rs['key'] = $key;
					break;
				case 'senname': 
					$rs['type'] = 'wildcard';
					$rs['search'] = '*'.$value.'*';
					$rs['key'] = $key;
					break;
				case 'level':  
					$rs['type'] = 'wildcard';
					$rs['search'] = '*'.$value.'*';
					$rs['key'] = $key;
					break;
				case 'alarm_type':   
					$rs['type'] = 'wildcard';
					$rs['search'] = '*'.$value.'*';
					$rs['key'] = $key;
					break;
				case 'alarm_name':   
					$rs['type'] = 'wildcard';
					$rs['search'] = '*'.$value.'*';
					$rs['key'] = $key.'.keyword';
					break;
				case 'sentype':  
					$rs['type'] = 'wildcard';
					$rs['search'] = '*'.$value.'*';
					$rs['key'] = $key;
					break;
				case 'device':				
					$rs['type'] = 'match';
					$rs['search'] = $value;
					$rs['key'] = $key;
					break;
				case 'src_ip':				
					$rs['type'] = 'match';
					$rs['search'] = $value;
					$rs['key'] = $key;
					break;
				case 'src_port':
					$rs['type'] = 'match';
					$rs['search'] = $value;
					$rs['key'] = $key;
					break;
				case 'dst_ip':
					$rs['type'] = 'match';
					$rs['search'] = $value;
					$rs['key'] = $key;
					break;
				case 'dst_port':
					$rs['type'] = 'match';
					$rs['search'] = $value;
					$rs['key'] = $key;
					break;
				case 'plugin_id':
					$rs['type'] = 'match';
					$rs['search'] = $value;
					$rs['key'] = $key;
					break;
				case 'plugin_sid':
					$rs['type'] = 'match';
					$rs['search'] = $value;
					$rs['key'] = $key;
					break;
				case 'protocol':
					$rs['type'] = 'match';
					$rs['search'] = $value;
					$rs['key'] = $key;
					break;
				case 'username':
					$rs['type'] = 'match';
					$rs['search'] = $value;
					$rs['key'] = $key;
					break;
				case 'password':
					$rs['type'] = 'match';
					$rs['search'] = $value;
					$rs['key'] = $key;
					break;
				case 'sensor_id':
					$rs['type'] = 'match';
					$rs['search'] = $value;
					$rs['key'] = $key;
					break;
				case 'interface':
					$rs['type'] = 'match';
					$rs['search'] = $value;
					$rs['key'] = $key;
					break;
				case 'sensor':
					$rs['type'] = 'match';
					$rs['search'] = $value;
					$rs['key'] = $key;
					break;
				case 'occurrences':
					$rs['type'] = 'match';
					$rs['search'] = $value;
					$rs['key'] = $key;
					break;
				case 'priority':
					$rs['type'] = 'match';
					$rs['search'] = $value;
					$rs['key'] = $key;
					break;
				case 'log':
					$rs['type'] = 'wildcard';
					$rs['search'] = '*'.strtolower($value).'*';
					$rs['key'] = $key;
					break;
				case 'binary_data':
					$rs['type'] = 'wildcard';
					$rs['search'] = '*'.strtolower($value).'*';
					$rs['key'] = $key;
					break;
				case 'userdata1':
					$rs['type'] = 'match';
					$rs['search'] = $value;
					$rs['key'] = $key;
					break;
				case 'userdata2':
					$rs['type'] = 'match';
					$rs['search'] = $value;
					$rs['key'] = $key;
					break;
				case 'userdata3':
					$rs['type'] = 'match';
					$rs['search'] = $value;
					$rs['key'] = $key;
					break;
				case 'userdata4':
					$rs['type'] = 'match';
					$rs['search'] = $value;
					$rs['key'] = $key;
					break;
				case 'userdata5':
					$rs['type'] = 'match';
					$rs['search'] = $value;
					$rs['key'] = $key;
					break;
				case 'userdata6':
					$rs['type'] = 'match';
					$rs['search'] = $value;
					$rs['key'] = $key;
					break;
				case 'userdata7':
					$rs['type'] = 'match';
					$rs['search'] = $value;
					$rs['key'] = $key;
					break;
				case 'userdata8':
					$rs['type'] = 'match';
					$rs['search'] = $value;
					$rs['key'] = $key;
					break;
				case 'userdata9':
					$rs['type'] = 'match';
					$rs['search'] = $value;
					$rs['key'] = $key;
					break;
				case 'hd_status':
					$rs['type'] = 'term';
					$rs['search'] = $value;
					$rs['key'] = $key;
					break;
				default:
					return false;
					break;
			}
		}
		//证书检测
		if ($index == 'threatcer') {
			switch ($key) {
				case 'serial':
					//全匹配   
					$rs['type'] = 'match';
					$rs['search'] = $value;
					$rs['key'] = $key.'.keyword';
					break;
					//单字母匹配
				case 'detectResult':
					$rs['type'] = 'match';
					$rs['search'] = $value;  
					$rs['key'] = $key;
					break;
					//
				case 'reason':
					$rs['type'] = 'wildcard';
					$rs['search'] = '*'.$value.'*';
					$rs['key'] = $key.'.keyword';
					break;
				case 'tlsversion':
					$rs['type'] = 'match';
					$rs['search'] = $value;
					$rs['key'] = $key.'.keyword';
					break;
				case 'subject':
					$rs['type'] = 'wildcard';
					$rs['search'] = '*'.strtolower($value).'*';
					$rs['key'] = $key;
				//使用者域名
				case 'userHostName':
					$rs['type'] = 'wildcard';
					$rs['search'] = '*'.strtolower($value).'*';
					$rs['key'] = $key;
					break;
				//颁发者
				case 'issuerdn':
					$rs['type'] = 'wildcard';
					$rs['search'] = '*'.strtolower($value).'*';
					$rs['key'] = $key;
					break;
				//颁发者域名
				case 'issuerHostName':  
					$rs['type'] = 'wildcard';
					$rs['search'] = '*'.strtolower($value).'*';
					$rs['key'] = $key;
					break;
				default:
					return false;
					break;
			}		
		}
		return $rs;
	}

}