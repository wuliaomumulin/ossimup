<?php
class HeaderPlugin extends Yaf\Plugin_Abstract {


	public function preDispatch(\Yaf\Request_Abstract $request, \Yaf\Response_Abstract $response) {



/*        $headers = getallheaders();
        $str = trim(`ifconfig eth5|grep 'inet '|awk '{print $2}'`);//指定网口Ip



        if(is_null($headers['Referer']) || is_null($headers['Host']) || is_null($headers['User-Agent'])){
        	jsonError('无权操作');
        }

        $str ='https://'.$str;

        if(trim($headers['Referer'],'/') != $str){
                jsonError('无权操作');
        }*/


        
        //白名单
        $blackList = [
        	'Index/Login/index/',
        	'Index/Login/checklogin/',
        	'Index/Login/captchaimg/',
        	'Index/Login/logout/',
        	'Zhongy/Operator/disablerequestip/',
        	'/Index/Menu/index/',
        ];

        $url = $request->module.'/'.$request->controller.'/'.$request->action.'/';

        /*if(!in_array($url, $blackList)){
        	jsonError('非法请求');
        }*/

	}

	public function postDispatch(\Yaf\Request_Abstract $request, \Yaf\Response_Abstract $response) {

                //echo json_encode($request);
	}

	public function dispatchLoopShutdown(\Yaf\Request_Abstract $request, \Yaf\Response_Abstract $response) { 
		
	}
}

?>