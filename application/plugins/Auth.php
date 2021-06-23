<?php
class AuthPlugin extends Yaf\Plugin_Abstract {


	public function preDispatch(Yaf_Request_Abstract $request, Yaf_Response_Abstract $response) {
        echo __METHOD__,'<br/>';


	}

	public function postDispatch(Yaf\Request_Abstract $request, Yaf\Response_Abstract $response) {
        echo __METHOD__,'<br/>';
		
          if (intval($_SESSION['uid'])===0){
            echo resultstatus(0,'未登录');
            exit;
        }  

	}

	public function dispatchLoopShutdown(Yaf\Request_Abstract $request, Yaf\Response_Abstract $response) {
        echo __METHOD__,'<br/>';   
		
	}
}

?>