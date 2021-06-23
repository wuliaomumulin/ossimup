<?php
use App\Models\Es;
use App\Models\TopologyNode;
use App\Models\TopologyEdge;

/* 
 * 网络拓扑
 */
class TopologyController extends Base{
	protected $model=null;
    protected $es;
    protected $num = 47;

	public function init(){
        parent::init();
        $this->es = new Es();
        $this->checkAuth($this->num);
	}

	public function indexAction(){


		$where['id'] = input('post.ip/s','');
        //模式：聚合:agg|离散:discrete
		$where['mode'] = input('post.mode/s','agg');
		//判断是否只查资产
		$where['is_asset'] = input('post.asset/s','all');


		$TopologyNode = new TopologyNode();
		$ret = $TopologyNode->index($where);

		jsonResult($ret);


	}
	
	/**
	 	属性
	*/
	public function attributeAction(){

		$ip = input('post.ip/s','');
	 	if(empty($ip)){
            jsonError('无效的参数:ip');
        }

		$TopologyNode = new TopologyNode();
		$ret = $TopologyNode->attribute($ip);

		if(!Tools::isEmpty($ret)){
			jsonResult($ret);
		}else{
			jsonError('未找到关联关系');
		}

	}
	/*
	* 网络安全上面态势的三级资产点击拓扑变化
	*/
	public function gradeAction(){
		$ip = input('post.ip/s','');
	 	if(empty($ip)){
            jsonError('无效的参数:ip');
        }

		$TopologyNode = new TopologyNode();
		$ret = $TopologyNode->grade($ip);

		if(!Tools::isEmpty($ret)){
			jsonResult($ret);
		}else{
			jsonError('未找到关联关系');
		}
	}




}