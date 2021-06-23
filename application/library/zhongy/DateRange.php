<?php
/**
* 中烟项目-主机卫士
*/

class zhongy_DateRange extends \zhongy_Base{

	public $weekMapKey = 'elasticsearch-index-event-week';
	/**
	* 获取一周index
	*/
	public function weekIndex(){

		$week = $this->weekmapping();
		return $week;
	}

	/**
	* 匹配一周mapping
	*/
	private function weekmapping(){

		$intersect = $this->redis->get($this->weekMapKey);
		if(!$intersect){

			$intersect = implode(',',array_intersect($this->eventIndex(),self::eventMap()));
			if(!is_null($intersect)){

				$this->redis->set($this->weekMapKey,$intersect,0,0,43200);//半天
			}
		}

		return $intersect;		
	}
	/**
	* 一周地图
	*/
	private function eventMap(){
		return [
			'zn-event-'.date("Y-m-d",strtotime("-6 day")),
			'zn-event-'.date("Y-m-d",strtotime("-5 day")),
			'zn-event-'.date("Y-m-d",strtotime("-4 day")),
			'zn-event-'.date("Y-m-d",strtotime("-3 day")),
			'zn-event-'.date("Y-m-d",strtotime("-2 day")),
			'zn-event-'.date("Y-m-d",strtotime("-1 day")),
			'zn-event-'.date("Y-m-d"),
		];
	}
	

}