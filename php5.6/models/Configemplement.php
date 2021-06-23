<?php

namespace App\Models;

class Configemplement extends Model
{
    protected $tableName = 'custom_verify';
    protected $tablePrefix = '';
    protected $pk = '';
    protected $server = 'localhost';

    protected $_validate = array(
        array('company', 'checkHave', '集团名称不能为空！',1,'function',3),
        array('company', 'checkLength', '集团名称不得超出于20字符', 1, 'function',3),
        array('factory', 'checkHave', '厂站名称不能为空！',1,'function',3),
        array('factory', 'checkLength', '厂站名称不得超出于20字符', 1, 'function',3),
        array('nick_name', 'checkHave', '厂站简称不能为空！',1,'function',3),
        array('nick_name', 'checkLength', '厂站简称不得超出于10字符', 1, 'function',3),
        array('address', 'checkHave', '地址不能为空！',1,'function',3),
        array('address', 'checkLength', '地址不得超出于20字符', 1, 'function',3),
        array('factory_person', 'checkHave', '电厂负责人不能为空！',1,'function',3),
        array('factory_person', 'checkLength', '电厂负责人不得超出于20字符', 1, 'function',3),
        array('lng', 'checkHave', '经度不能为空！',1,'function',3),
        array('lng', 'checkLon', '经度范围-180~180', 1, 'function',3),
        array('lat', 'checkHave', '纬度不能为空！',1,'function',3),
        array('lat', 'checkLat', '纬度范围-90~90', 1, 'function',3),
        array('factory_phone', 'checkHave', '负责人电话不能为空！',1,'function',3),
        array('factory_phone', 'checkTel', '请输入正确的手机号', 1, 'function',3),
    );

    protected $_validate1 = array(
        array('contact', 'checkHave', '实施组长不能为空！',1,'function',3),
        array('contact', 'checkLength', '实施组长不得超出于20字符', 1, 'function',3),
        array('telphone', 'checkHave', '组长电话不能为空！',1,'function',3),
        array('telphone', 'checkTel', '请输入正确的手机号', 1, 'function',3),
    );

    protected $_validate2 = array(
        array('channel_num', 'checkHave', 'vpdn通道号不能为空！',1,'function',3),
        array('equ_num', 'checkHave', 'vpdn设备号不能为空！',1,'function',3),
        array('sim_num', 'checkHave', 'vpdn设备卡号不能为空！',1,'function',3),
    );

    public function getPlatform($param = '')
    {
        if (!empty($param)) {
            $this->_validate = $this->_validate;
            if (!$this->create()) {
                $errtips = $this->getError();
                if (!empty($errtips)) {
                    return $errtips;
                }
                return $this->operateService($param);
            }

        }

        $where = ['company', 'factory', 'nick_name', 'factory_type', 'isp',
            'province', 'provinceCode', 'city', 'cityCode', 'county', 'countyCode', 'address', 'lng', 'lat', 'factory_person', 'factory_phone'];
        $data = $this->where(['attribute' => ['in', $where]])->select();

        return $this->dataDispost($data);
    }

    public function getEmplement($param = '')
    {
        if (!empty($param)) {
            $this->_validate = $this->_validate1;
            if (!$this->create()) {
                $errtips = $this->getError();
                if (!empty($errtips)) {
                    return $errtips;
                }
                return $this->operateService($param);
            }

        }

        $where = ['contact', 'telphone', 'accept_time', 'memo'];
        $data = $this->where(['attribute' => ['in', $where]])->select();

        return $this->dataDispost($data);
    }

    public function getVpn($param = '')
    {
        if (!empty($param)) {

            $this->_validate = $this->_validate2;
            if (!$this->create()) {
                $errtips = $this->getError();
                if (!empty($errtips)) {
                    return $errtips;
                }
                return $this->operateService($param);
            }

        }

        $where = ['channel_num', 'equ_num', 'sim_num'];
        $data = $this->where(['attribute' => ['in', $where]])->select();

        return $this->dataDispost($data);
    }

    public function dataDispost($data)
    {
        $i = 0;
        foreach ($data as $k => $v) {
            $i++;
            if ($i > 1) {
                $res[$v['attribute']] = $v['value'];
            } else {
                $res = [$v['attribute'] => $v['value']];
            }


        }
        return $res;
    }

    //添加或者更新
    public function operateService($param)
    {

        $tag = true;
        foreach ($param as $k => $v) {
            if ($this->where(['attribute' => $k])->count()) {
                //这里是更新
                $data = ['value' => $v];
                $where = ['attribute' => $k];
                $result = $this->where($where)->save($data);
                //  echo $this->getlastsql();die;
                if ($result === false) {
                    $tag = false;
                    break;
                }
            } else {
                //添加
                $data = ['attribute' => $k, 'value' => $v];
                $result = $this->add($data);
                if ($result === false) {

                    $tag = false;
                    break;
                }
            }

        }
        return $tag;
    }

}

?>