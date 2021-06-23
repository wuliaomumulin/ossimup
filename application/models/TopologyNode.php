<?php

namespace App\Models;

/** 拓扑节点 */
class TopologyNode extends Model
{
    protected $tableName = 'node';
    protected $tablePrefix = 'topology_';
    protected $pk = 'id';

    //代表一级和二级设备
    protected $second_device = '1101,1102,1103,1105,1201,1202,1203';
    //分类
    protected $categories = [];
    //模式：聚合:agg|离散:discrete
    protected $mode = '';
    //判断是否只查资产
    protected $is_asset = '';
    //连线状态判断标准
    protected $status = 'if(update_time>date_sub(now(),interval 2 day),1,0) `status`';

    //模型类
    protected $Theme,$TopologyEdge,$Host;

    public function getAll($where=[]){
        return $this->where($where)->select();
    }


    // 回调方法 初始化模型
    protected function _initialize() {

        $this->TopologyEdge = new TopologyEdge();
        $this->Theme = new \Theme();
        $this->Host = new Host();
    }

    /** 
		获得拓扑结构
    	获得根据IP分组之后的ip和小分类
        

        大于24h代表离线
     */
    public function index(Array $input){

        //模式：聚合:agg|离散:discrete
        $this->mode = $input['mode'];
        //判断是否只查资产
        $this->is_asset = $input['is_asset'];

        if(\Tools::isEmpty($input['id'])){
            //模式：聚合:agg|离散:discrete
            $where = $input['mode'] == 'agg' ? ['type2'=>['in',$this->second_device]] : [];

            $ret =  $this->field('name,id,ip,alert,type1,type2,is_asset,'.$this->status)->where($where)->group('ip')->select();

            return [
                "categories" => $this->categories($ret),
                "nodes" => $this->node($ret),
                "links" => $this->links(),
                ];
        }else{

            //断言是否是外部资产,是的话就是三级资产，不是的话就是一、二级资产
            $asset = $this->assertSecondDevice($input['id']);
            if(!$asset){
                return [
                "nodes" => [],
                "categories" => [],
                "links" => [],
                ];
            }

            //代表二级查询

            $where = [
                    'src' => $asset['ip'],
                    'dst' => $asset['ip'],
                    '_logic' => 'OR'
            ];
            return [
                "nodes" => $this->node(Null,$where),
                "categories" => $this->categories,
                "links" => $this->links($where),
            ];
        }

    }
    

    /**
    	获得分类
		@params $input Array
    */
    private function categories($input){

    	$categories = [];
		if(!is_null($input)){
			$type = array_unique(array_column($input,'type2'));
			foreach ($type as $v) {
				$categories[] = ["name" => $v];
			}
		}
        $this->categories = $categories;
        
		return $categories;
    }
    /**
    	获得节点 
		@params $input Array
    */
    private function node($input,$where=[]){

    	$node = [];
        //聚合模式的线条操作
        if($this->mode == 'agg'){
            if(!empty($where)){
                $whe = [
                    [
                        'ip' => $where['src'],
                        'device' => $where['src'],
                        '_logic' => 'OR'
                    ],
                    'type2'=>['NOT IN',$this->second_device]
                ];

                if($this->is_asset == 'asset'){
                    $whe['is_asset'] = 1;
                }

                $input = $this->field('id,name,ip,alert,type1,type2,is_asset,'.$this->status)->group('ip')->where($whe)->select();

            }
        }else{
            //离散模式的线条操作
            if(!empty($where)){
                //单节点获取
                $in = $this->TopologyEdge->getAll($where);
                $where = [
                    'ip' => ['in',array_unique(array_merge(array_column($in,'src'),array_column($in,'dst')))],
                    'type2' => ['NOT IN',$this->second_device]
                ];

                if($this->is_asset == 'asset'){
                    $where['is_asset'] = 1;
                }

                $input = $this->field('id,name,ip,alert,type1,type2,is_asset,'.$this->status)->where($where)->select();
                
            }
        }
        //分类赋值
        $this->categories($input);

		if(!is_null($input)){
			foreach ($input as $a) {
				$node[] = $this->formatNode($a);
			}
		}

		return $node;
    }
    /**
    	获得线条 
		@params $input Array
    */
    private function links($where = []){
        $links = [];
        //聚合模式的线条操作
        if($this->mode == 'agg'){
            
            $whe = [
                'device' => ['NEQ','']
            ];
            

            $input = $this->field('id,ip src,name,device dst,'.$this->status)->where($whe)->group('ip')->select();
            //exit($this->_sql());
            //为了适应唯一标识新增的逻辑
/*            if (!\Tools::isEmpty($input)) {
                array_walk($input, function (&$arr) {
                    $middle = $this->where(['ip'=>$arr['src']])->getField('id');
                    $arr['src'] = is_null($middle) ? '' : $middle;
                    $middle = $this->where(['ip'=>$arr['dst']])->getField('id');
                    $arr['dst'] = is_null($middle) ? '' : $middle;
                });
            }*/

        }else{
            //离散模式的线条操作
            $input = $this->TopologyEdge->links($where);
        }

		if(!is_null($input)){
			foreach ($input as $a) {
				$links[] = [
					"source" => $a['src'],
                    "target" =>  $a['dst'],
                    "lineStyle" => [
                    	"color" => $this->Theme->status($a['status'])
                    ]
                ];
			}
		}
		return $links;
    }
	/** 
		获得资产属性

     */
    public function attribute($ip){
       	$where = [
       		'b.ip' => ['exp', "=inet6_aton('{$ip}')"]
       	];
    	return $this->Host->getOne($where);

    }

    /** 
        格式化每个node节点
        @params $node Array
    */
    private function formatNode($input){

        //判断设备图标
        $segment = '';
        switch ($input['type2']) {
            //设备
            case "1101":
                $segment .= $input['status']>0 ? 'caijiqi-zaixian' : 'caijiqi-lixian';
                break;
            case "1102":
                $segment .= $input['status']>0 ? 'caijiqi-zaixian' : 'caijiqi-lixian';
                break;
            case "1103":
                $segment .= $input['status']>0 ? 'caijiqi-zaixian' : 'caijiqi-lixian';
                break;
            case '1105':
                $segment .= $input['status']>0 ? 'pingtai-zaixian' : 'pingtai-lixian';
                break;            
            case "1201":
                $segment .= $input['status']>0 ? 'caijiqi-zaixian' : 'caijiqi-lixian';
                break;
            case "1202":
                $segment .= $input['status']>0 ? 'caijiqi-zaixian' : 'caijiqi-lixian';
                break;
            case "1203":
                $segment .= $input['status']>0 ? 'caijiqi-zaixian' : 'caijiqi-lixian';
                break;
            //在拓扑展示上面，其他的都为资产
            default:
                
                $segment .= $input['is_asset']>0 ? 'zichan-' : 'fei-';
                $segment .= $input['status']>0 ? 'zaixian' : 'lixian';
                $segment .= $input['alert']>0 ? '-gj' : '';


                break;
        }
        $symbol = "image:///static/img/topo/{$segment}.png";


        return  [
                    "ip" => html_entity_decode($input['name']),
                    "name" => $input['ip'],
                    "category" =>  $input['type2'],
                    'grade' =>   $input['type1'] == 11 ? 'sensor' : 'three',
                    "flag" => $this->mode=='agg' ? true : false,
                    "symbol" =>  $symbol,
                    'is_asset' => $input['is_asset'],
                    'status' => $input['status'],
                ];
    }

    /**
    * 判断是否为管理设备
    * 是的话就是三级资产，不是的话就是一、二级资产
    * return BOOL
    */
    private function assertSecondDevice($id){

        $where = [
            'ip'=>$id,
            'type2' => ['IN',$this->second_device],
        ];
        $second_d = $this->where($where)->find();
        return is_null($second_d) ? false : $second_d;

    }


    /**
    * 拓扑反转--点的追溯
    */
    public function grade($ip){
        

        $where = [
            'src' => $ip,
            'dst' => $ip,
            'device' => $ip,
            '_logic' => 'or',
        ];
        $arr = $this->TopologyEdge->field('src source,dst target,device')->getAll($where);
        if(is_null($arr)){

            $node = self::formatNode($this->field('name,id,ip,alert,type1,type2,is_asset,'.$this->status)->where(['ip' => $ip])->find());

            return [
                'categories' => [],
                'nodes' => [$node],
                'links' => [],
            ];
        } 

        $ips = array();
        $nodes = array();

        foreach ($arr as $k => $item) {
            
            //赋予线的颜色
            $arr[$k]['lineStyle'] = ['color' => '#ffffff'];

            array_push($ips,$item['source'],$item['target'],$item['device']);
        }
        $where = [
            'ip' => ['in',array_unique($ips)]
        ];
        
        $items =  $this->field('name,id,ip,alert,type1,type2,is_asset,'.$this->status)->where($where)->group('ip')->select();

        foreach ($items as $item) $nodes[] = self::formatNode($item);

        return  [
                'categories' => self::categories($items),
                'nodes' => $nodes,
                'links' => $arr,
            ];

        
    }

    /**
    * 获取外部资产的IP
    */
    public function externalIP(){
        $where = [
            'external' => 1,
            'is_asset' => 0,
            'update_time' => ['exp','>date_sub(now(),interval 1 day)']
        ];
        $ret =  $this->field('name,id,ip src_ip,alert,is_src,device,type1,type2')->where($where)->group('ip')->select();
        return $ret;
    }
}