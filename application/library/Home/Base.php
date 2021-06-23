<?php
namespace Home;
/**
 * @name Base
 * @author root
 * @desc 基础类 
 */
class Base extends \Yaf\Controller_Abstract {
    
    public function init()
    {
        $m_id = isset($_SESSION['m_id'])?$_SESSION['m_id']:0;
        if ($m_id==0){
        //    \Yaf\Controller_Abstract::redirect('/Home/Login/index');
        }  
    }
}