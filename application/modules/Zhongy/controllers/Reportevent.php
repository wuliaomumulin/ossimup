<?php
use App\Models\Es;
use App\Models\PluginSid;
use App\Models\Event;
/* 
 * 安全事件报表--一天事件
 */
class ReporteventController extends Base{

	protected $table=null;
    protected $es;
    //依赖
    protected $zhongy_Reportevent;
    protected $num = 55;  //这是为了防止普通用户访问 而故意写了一个超级管理员的权限数字 传入checkAuth（）内，这样肯定不能访问

	public function init(){
        parent::init();
        $this->es = new Es();
        $this->table = 'zn-event-'.date('Y-m-d',strtotime('-1 day'));
        $this->checkAuth($this->num);
	}

	/**
	* 日志数量变化趋势
	*/
	public function index1Action(){

		$zhongy_Reportevent = new zhongy_Reportevent();
        $method = substr(__FUNCTION__,0,-6);
        $post = $zhongy_Reportevent->$method();

        $r = $this->es->query($this->table,$post);
		
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
	/*
	* 采集器日志类型占比
	*/
	public function index2Action(){
		$zhongy_Reportevent = new zhongy_Reportevent();
        $method = substr(__FUNCTION__,0,-6);
        $post = $zhongy_Reportevent->$method();

        $method = 'format'.$method;
        $r = $this->es->query($this->table,$post);
		$ret = $zhongy_Reportevent->$method($r);
		jsonResult($ret);
	}
	/*
	* 分区安全日志数
	*/
	public function index3Action(){
		$zhongy_Reportevent = new zhongy_Reportevent();
		$method = substr(__FUNCTION__,0,-6);
        $post = $zhongy_Reportevent->$method();

        $r = $this->es->query($this->table,$post);

        $ret = [];
		$keys = ['key','value'];

		if(!Tools::isEmpty($r['aggregations']['device']['buckets'])){
			foreach($r['aggregations']['device']['buckets'] as $item) {

					if(!empty($item['key'])){

						array_push($ret,[
							'key' => $item['key'],
							'value' => $item['doc_count'],
						]);

					}
              }

		}
		
		jsonResult($ret);
	}
	/*
	* 设备日志数排名TOP10
	*/
	public function index4Action(){
		$zhongy_Reportevent = new zhongy_Reportevent();
		$method = substr(__FUNCTION__,0,-6);
        $post = $zhongy_Reportevent->$method();

        $method = 'format'.$method;
        $r = $this->es->query($this->table,$post);
		$ret = $zhongy_Reportevent->$method($r);
		jsonResult($ret);
	}
	/*
	* 被访问IPTOP10
	*/
	public function index5Action(){
		$zhongy_Reportevent = new zhongy_Reportevent();
		$method = substr(__FUNCTION__,0,-6);
        $post = $zhongy_Reportevent->$method();

        $method = 'format'.$method;
        $r = $this->es->query($this->table,$post);
		$ret = $zhongy_Reportevent->$method($r);
		jsonResult($ret);		
	}

}