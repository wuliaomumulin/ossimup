<?php

use App\Models\Configemplement;

class ConfigemplementController extends Base
{
    protected $Configemplement = '';
    protected $num = 24;

    public function init()
    {
        parent::init();
        $this->Configemplement = new Configemplement();
        $this->checkAuth($this->num);
    }

    //获取厂站信息
    public function getPlatformAction()
    {

        if (!\Tools::isEmpty(input('post.'))) {
            $param['address'] = decryptcode(input('post.address'));
            $param['city'] = decryptcode(input('post.city'));
            $param['cityCode'] = decryptcode(input('post.cityCode'));
            $param['company'] = decryptcode(input('post.company'));
            $param['county'] = decryptcode(input('post.county'));
            $param['countyCode'] = decryptcode(input('post.countyCode'));
            $param['factory'] = decryptcode(input('post.factory'));
            $param['factory_person'] = decryptcode(input('post.factory_person'));
            $param['factory_phone'] = decryptcode(input('post.factory_phone'));
            $param['factory_type'] = decryptcode(input('post.factory_type'));
            $param['isp'] = decryptcode(input('post.isp'));
            $param['lat'] = decryptcode(input('post.lat'));
            $param['lng'] = decryptcode(input('post.lng'));
            $param['nick_name'] = decryptcode(input('post.nick_name'));
            $param['province'] = decryptcode(input('post.province'));
            $param['provinceCode'] = decryptcode(input('post.provinceCode'));
            $param['addressDetail'] = decryptcode(input('post.addressDetail'));

        }
        $data = $this->Configemplement->getPlatform($param);
        if(is_bool($data)){
            $this->logger(31);
            jsonResult($data);
        }elseif (is_array($data)){
            $res = encryptcode(json_encode($data,1));
            jsonResult($res);
        }
        jsonError($data);
    }

    //实施信息
    public function getEmplementAction()
    {
        if (!\Tools::isEmpty(input('post.'))) {
            $param['contact'] = decryptcode(input('post.contact'));
            $param['telphone'] = decryptcode(input('post.telphone'));
            $param['accept_time'] = decryptcode(input('post.accept_time'));
            $param['memo'] = decryptcode(input('post.memo'));
        }
        $data = $this->Configemplement->getEmplement($param);
        if(is_bool($data)){
            $this->logger(32);
            jsonResult($data);
        }elseif (is_array($data)){
            $res = encryptcode(json_encode($data,1));
            jsonResult($res);
        }
        jsonError($data);
    }

    //vpn信息
    public function getVpnAction()
    {
        if (!\Tools::isEmpty(input('post.'))) {
            $param['channel_num'] = decryptcode(input('post.channel_num'));
            $param['equ_num'] = decryptcode(input('post.equ_num'));
            $param['sim_num'] = decryptcode(input('post.sim_num'));
        }
        $data = $this->Configemplement->getVpn($param);
        if(is_bool($data)){
            $this->logger(33);
            jsonResult($data);
        }elseif (is_array($data)){
            $res = encryptcode(json_encode($data,1));
            jsonResult($res);
        }
        jsonError($data);
    }
}

?>