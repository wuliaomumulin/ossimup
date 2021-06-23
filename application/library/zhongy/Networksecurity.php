<?php
/**
* 中烟项目-网络安全大屏
*/

class zhongy_Networksecurity extends \zhongy_Base{
 /**
 * 前15网络行为访问
 */
	public function lefttop(){
	 	        $post = Array(
	            "query" => Array
	                (
	                    "bool" => Array
	                        (
	                            "must" => Array
	                                (
	                                    "0" => Array
	                                        (
	                                            "term" => Array
	                                                (
	                                                    "plugin_id" => Array
	                                                        (
	                                                            "value" => 1001
	                                                        )

	                                                )

	                                        )

	                                ),
                                //过滤未知事件
                                "must_not" => Array
                                    (
                                        "0" => Array
                                            (
                                                "term" => Array
                                                    (
                                                        "plugin_sid" => Array
                                                            (
                                                                "value" => 2000000
                                                            )

                                                    )

                                            )

                                    )
	                        )

	                ),

	            "aggs" => Array
	                (
	                    "plugin_sid" => Array
	                        (
	                            "terms" => Array
	                                (
	                                    "field" => "plugin_sid.keyword",
	                                    "size" => 5
	                                )

	                        )

	                ),

	            "size" => 0
	        );

	        if($this->user_device_power == 'all' && $this->user_monitor_power == 'all'){

	        }else{

	            $device = $this->role("elasticsearch");
	            if(empty($device)) jsonResult([],'无设备');
	                
	            $post["query"]["bool"]["must"][] = $device;

	        }

	        return $post;
	}
    /**
    * 网络通讯控制事件
    */
    public function leftcenter(){
		/** 骨架 begin **/
        $post = Array(
                "_source" => Array(
                    "excludes" => [
                        "userdata*"
                    ],
                    "includes" => [
                        "src_*","dst_*","device","interface","fdate",'protocol','priority','plugin*'
                    ]
                ),
                "query" => Array
                    (
                        "bool" => Array
                            (
                                "must" => Array
                                    (
                                        0 => Array
                                            (
                                                "term" => Array
                                                    (
                                                        "plugin_id" => Array
                                                            (
                                                                "value" => 100026
                                                            )

                                                    )

                                            ),

                                        1 => Array
                                            (
                                                "term" => Array
                                                    (
                                                        "plugin_sid" => Array
                                                            (
                                                                "value" => 3
                                                            )

                                                    )

                                            )

                                    )

                            )

                    ),

                "size" => 50
            );

        if($this->user_device_power == 'all' && $this->user_monitor_power == 'all'){

        }else{

            $device = $this->role("elasticsearch");
            if(empty($device)) jsonResult([],'无设备');
                
            $post["query"]["bool"]["must"][] = $device;

        }
	    return $post;

    }

	/**
    * 工控网络安全协议事件 S7、modbus
    */
    public function leftbottom(){

        /** 骨架 begin **/
        $post = Array(
                /*"_source" => Array(
                    "excludes" => [
                        "userdata*"
                        ],
                "includes" => [
                    "src_*",
                    "dst_*",
                    "device",
                    "interface",
                    "fdate",
                    "protocol",
                    "priority",
                    "sensor",
                    "date",
                    'plugin*'
                    ]
                ),*/
                "query" => Array
                    (
                        'bool' => Array
                            (
                                "must" => Array
                                    (
                                        0 => Array
                                            (
                                                "term" => Array
                                                    (
                                                        "plugin_id" => Array
                                                            (
                                                                "value" => 1001
                                                            )

                                                    )

                                            ),

                                        1 => Array
                                            (
                                                "terms" => Array
                                                    (
                                                        "plugin_sid" => Array
                                                            (
                                                                0 => 202800017,
                                                                1 => 202800018,
                                                                2 => 202800019,
                                                                3 => 202800020,
                                                                4 => 202800021,
                                                                5 => 202800022,
                                                                6 => 202800023,
                                                                7 => 202800024,
                                                                8 => 202800025,
                                                                9 => 202800026,
                                                                10 => 202800027,
                                                                11 => 202800028,
                                                                12 => 202800029,
                                                                13 => 202800030,
                                                                14 => 202800031,
                                                                15 => 202800032,
                                                                16 => 200000001,
                                                                17 => 200000002,
                                                                18 => 200000003
                                                            )

                                                    )

                                            )

                                    )

                            )

                    ),

                "size" => 50,
                "sort" => Array
                    (
                        0 => Array
                            (
                                "@timestamp" => Array
                                    (
                                        "order" => "desc"
                                    )

                            )

                    )

        );
        /** 骨架 end **/



        if($this->user_device_power == 'all' && $this->user_monitor_power == 'all'){

            //$post = '{"query":{"bool":{"must":[{"term":{"plugin_id":{"value":1001}}},{"terms":{"plugin_sid":[202800017,202800018,202800019,202800020,202800021,202800022,202800023,202800024,202800025,202800026,202800027,202800028,202800029,202800030,202800031,202800032,200000001,200000002,200000003]}}]}},"size":50,"sort":[{"@timestamp":{"order":"desc"}}]}';
        }else{

            $device = $this->role("elasticsearch");
            if(empty($device)) jsonResult([],'无设备');
                
            $post["query"]["bool"]["must"][] = $device;

        }

	    return $post;

    }

    /**
    * 网络安全事件
    */
    public function righttop(){

        /** 骨架 begin **/
        $post = Array(
                /*"_source" => Array(
                    "excludes" => [
                        "userdata*"
                        ],
                "includes" => [
                    "src_*",
                    "dst_*",
                    "device",
                    "interface",
                    "fdate",
                    "protocol",
                    "priority",
                    "sensor",
                    "date",
                    'plugin*'
                    ]
                ),*/
                "query" => Array
                    (
                        'bool' => Array
                            (
                                "must" => Array
                                    (
                                        0 => Array
                                            (
                                                "term" => Array
                                                    (
                                                        "plugin_id" => Array
                                                            (
                                                                "value" => 1001
                                                            )

                                                    )

                                            )

                                    )

                            )

                    ),

                "size" => 10,
                "sort" => Array
                    (
                        0 => Array
                            (
                                "@timestamp" => Array
                                    (
                                        "order" => "desc"
                                    )

                            )

                    )

        );
        /** 骨架 end **/



        if($this->user_device_power == 'all' && $this->user_monitor_power == 'all'){

        }else{

            $device = $this->role("elasticsearch");
            if(empty($device)) jsonResult([],'无设备');
                
            $post["query"]["bool"]["must"][] = $device;

        }

        return $post;

    }
}
?>