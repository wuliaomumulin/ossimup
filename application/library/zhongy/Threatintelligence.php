<?php
/**
* 中烟项目-威胁情报
*/

class zhongy_Threatintelligence extends \zhongy_Base{
	/**
	* 查询数量
	* @param $input 分类ID，ti_class:1=恶意域名，ti_class：2=恶意IP,ti_class:3=恶意URL
	*/
	public function top(int $ti_class){
		/** 骨架 begin **/
        $post = Array(
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
                                                        "is_queryd" => Array
                                                            (
                                                                "value" => 1
                                                            )

                                                    )

                                            ),
                                        1 => Array
                                            (
                                                "term" => Array
                                                    (
                                                        "ti_class" => Array
                                                            (
                                                                "value" => $ti_class
                                                            )

                                                    )

                                            )
                                    )

                            )

                    )             
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
    * 列表
    */
    public function bottom(){
		/** 骨架 begin **/
        $post = Array(
                "_source" => Array(
                    "excludes" => [
                        "userdata*"
                    ],
                    /*"includes" => [
                        "src_*","dst_*","device","interface","fdate",'protocol','priority','plugin*'
                    ]*/
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
                                                        "is_queryd" => Array
                                                            (
                                                                "value" => 1
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

        if($this->user_device_power == 'all' && $this->user_monitor_power == 'all'){

        }else{

            $device = $this->role("elasticsearch");
            if(empty($device)) jsonResult([],'无设备');
                
            $post["query"]["bool"]["must"][] = $device;

        }
	    return $post;

    }
    /**
    * 查询数量
    */
    public function common(){
        /** 骨架 begin **/
        $post = Array(
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
                                                        "is_queryd" => Array
                                                            (
                                                                "value" => 1
                                                            )

                                                    )

                                            )
                                    )

                            )

                    )             
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
    * 查询列表
    */
    public function querylist(Array $input){

        //类型
        switch ($input['type']) {
            case 'ip':
                $type = 1;
                break;
            case 'domain':
                $type = 2;
                break;
            case 'url':
                $type = 3;
                break;
            case 'cc':
                $type = 4;
                break;
            case 'ssl':
                $type = 5;
                break;            
            default:
                $type = 0;
                break;
        }

        /** 骨架 begin **/
        
        
        $post = Array(
                "_source" => Array(
                    "includes" => [
                        "id","ioc_raw","severity","created_at","find_at",'update_at','family','family_desc','port','related_sample','related_ip','related_gangs','related_gangs_desc','related_events_and_desc','solution','src*','dst*','device','sensor_id','sensor','date','fdate','plugin_sid_desc','is_queryd','history'
                    ],
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
                                                        "ti_class" => Array
                                                            (
                                                                "value" => $type
                                                            )

                                                    )

                                            ),
                                        1 => Array
                                        (
                                            "range" => [
                                                '@timestamp' => [
                                                    'gte' => $input['begindate'],
                                                    'lte' => $input['enddate'],
                                                    'format' =>  "yyyy-MM-dd HH:mm:ss",
                                                ]
                                            ]
                                        )        
                                    )

                            )

                    ),
                "from" => ($input['page']-1)*$input['pagesize'],
                "size" => $input['pagesize'],
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

        //类型判断
         if(!empty($input['family'])){
            $post['query']['bool']['must'][] = [
                'term' => [
                    'family' => [
                        'value' => $input['family']
                    ]
                ]
            ];
        }
         if(!empty($input['severity'])){
            $post['query']['bool']['must'][] = [
                'term' => [
                    'severity' => [
                        'value' => $input['severity']
                    ]
                ]
            ];
        }

 /*       ['range']['ts'] = [
                    "gte" => $timestamp[0],//'2019-09-01 12:00:00',
                    "lte" => $timestamp[1],//'2019-09-09 12:00:00',
                    "format" => "yyyy-MM-dd HH:mm:ss"
                    //,"time_zone"=>"+08:00"
                ];*/
        //事件附加

        if($this->user_device_power == 'all' && $this->user_monitor_power == 'all'){

        }else{

            $device = $this->role("elasticsearch");
            if(empty($device)) jsonResult([],'无设备');
                
            $post["query"]["bool"]["must"][] = $device;

        }
        return $post;

    }

}
