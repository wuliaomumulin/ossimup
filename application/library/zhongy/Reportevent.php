<?php
/**
 * 安全事件报表--一天事件
*/

class zhongy_Reportevent extends \zhongy_Base{
	/**
	* 日志数量变化趋势
	*/
	public function index1(){

		$post = [
			'aggs' => [
				'1d' => [
					'date_histogram' => [
						'field' => "@timestamp",
				        "interval"=> "hour",
				        "format"=>"yyyy-MM-dd HH:mm:ss",
				        "min_doc_count"=>0,
				        'extended_bounds'=>[
				        	'min'=>"now/d",
         					"max"=>"now/d"
				        ]
					]
				]
			],
			'size' => 0
		];

		return $post;
	}
	/*
	* 采集器日志类型占比
	* 分组之后会带出相关联字段
	*/
	public function index2(){
		/*$post = [
			'aggs' => [
				'2' => [
					"terms" => [
						"field" => "plugin_id_desc.keyword",
        				"size"=> 3
					],
					"aggs" => [
						"top_score_hits" => [
							"top_hits" => [
								"_source"=> ["device"], 
	            				"size"=> 1
							]
						]
					]
				]
			],
			'size' => 0
		];*/

		$post = [
			'aggs' => [
				"2" => [
					'terms' => [

						'field' => 'device.keyword',
						'order' => [
							"_count" => "desc"
						],
						'size' => 5
					],
					"aggs" => [
						"3" => [
							'terms' => [

								'field' => 'plugin_id_desc.keyword',
								'order' => [
									"_count" => "desc"
								],
								'size' => 5
							]
						]
					]
				]
			],
			'size' => 0
		];

		return $post;		
	}
	/**
	* 格式化index3
	*/
	public function index3(){
		$post = [
			'aggs' => [
				'device' => [
					"terms" => [
						"field" => "device.keyword",
        				"size"=> 3
					]
				]
			],
			'size' => 0
		];

		return $post;
	}

	/**
	*/
	public function index4(){
		$post = '{
				  "aggs": {
				    "2": {
				      "terms": {
				        "field": "device.keyword",
				        "order": {
				          "_count": "desc"
				        },
				        "size": 10
				      },
				      "aggs": {
				        "3": {
				          "terms": {
				            "field": "src_ip.keyword",
				            "order": {
				              "_count": "desc"
				            },
				            "size": 10
				          },
				          "aggs": {
				            "4": {
				              "terms": {
				                "field": "dst_ip.keyword",
				                "order": {
				                  "_count": "desc"
				                },
				                "size": 10
				              },
				              "aggs": {
				                "5": {
				                  "terms": {
				                    "field": "plugin_id_desc.keyword",
				                    "order": {
				                      "_count": "desc"
				                    },
				                    "size": 5
				                  },
				                  "aggs": {
				                    "6": {
				                      "terms": {
				                        "field": "plugin_sid_desc.keyword",
				                        "order": {
				                          "_count": "desc"
				                        },
				                        "size": 5
				                      }
				                    }
				                  }
				                }
				              }
				            }
				          }
				        }
				      }
				    }
				  },
				  "size": 0
				}';

		return $post;
	}

	public function formatindex4(Array $r){
		$ret = [];


		if(!Tools::isEmpty($r['aggregations'][2]['buckets'])){
			foreach($r['aggregations'][2]['buckets'] as $item2) {
				foreach ($item2[3]['buckets'] as $item3) {
					foreach($item3[4]['buckets'] as $item4) {
						foreach($item4[5]['buckets'] as $item5) {
							foreach($item5[6]['buckets'] as $item6) {
								array_push($ret,[
									'device' => $item2['key'],
									'src_ip' => $item3['key'],
									'dst_ip' => $item4['key'],
									'plugin_id_desc' => $item5['key'],
									'plugin_sid_desc' => $item6['key'],
									'value' => $item6['doc_count'],
								]);
							}
						}
					}
				}
            }

		}
		return $ret;
	}

	/**
	*/
	public function index5(){
		$post = '{
		  "aggs": {
		    "2": {
		      "terms": {
		        "field": "dst_ip.keyword",
		        "order": {
		          "_count": "desc"
		        },
		        "size": 10
		      }
		    }
		  },
		  "size": 0
		}';

		return $post;
	}

	public function formatindex5(Array $r){
		$ret = [];


		if(!Tools::isEmpty($r['aggregations'][2]['buckets'])){
			foreach($r['aggregations'][2]['buckets'] as $item2) {
				
				array_push($ret,[
					'dst_ip' => $item2['key'],
					'value' => $item2['doc_count'],
				]);
	
            }

		}
		return $ret;
	}
	public function formatindex2(Array $r){
		$ret = [];

		$r = json_decode(str_replace('""','"未知"',
			str_replace('doc_count','value',
			str_replace('key','name',json_encode($r))))
		,1);

		if(!Tools::isEmpty($r['aggregations'][2]['buckets'])){
			foreach($r['aggregations'][2]['buckets'] as $item2) {
				
				array_push($ret,[
					'name' => $item2['name'],
					'value' => $item2['value'],
					'items' => $item2[3]['buckets'],
				]);
	
            }

		}
		return $ret;
	}
}