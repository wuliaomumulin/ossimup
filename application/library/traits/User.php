<?php

use App\Models\Userreference;
/*
	用户特性
*/
trait traits_User{

    //用户标识
    protected $authorization = '';

    private function _initialize(){
    	//签名验证
       $this->authorization = parseToken();

       if(\Tools::isEmpty($this->authorization)) jsonError('没有签名或签名不正确');
    }


    public function store(){

    }

    /**
    * 获取menusid
    */
    protected function menusids(){

        $Userreference = new Userreference();

        return $Userreference->where(['uid' => $this->authorization['id']])->getField('menuids');

    }
}
?>