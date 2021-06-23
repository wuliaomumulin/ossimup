<?php

namespace App\Models;
class DetectionAuditDevice extends Model
{
    protected $tableName = 'device';
    protected $tablePrefix = 'detection_audit_';
    protected $pk = 'code';
    protected $config;

    /***
     * 同步当前采集器的数据
     * @params host_id 采集器id
     * @params arr 多维数组
     */
    public function sync($host_id, $arr)
    {
        $this->config = \Yaf\Registry::get("config");
        $ctx = $this->config->application->ctx;

        foreach ($arr as $k => $v) {
            $this->duplicate_removal($host_id, $v, $ctx);

        }


    }


    //资产判重
    public function duplicate_removal($host_id, $data, $ctx)
    {
        //先处理MAC
        if(!empty($data['maca'])){
            $data['maca'] = substr($data['maca'],0,2).substr($data['maca'],3,2).substr($data['maca'],6,2).substr($data['maca'],9,2).substr($data['maca'],12,2).substr($data['maca'],15,2);
        }else{
            $data['maca'] = '';
        }

        //$res = $this->table('host')->field('id,fqdns')->where(['fqdns' => $data['ipa']])->find();
        $is_have_host = "select hex(host_id) as id,inet6_ntoa(ip) as ip from host_ip where inet6_ntoa(ip) = '{$data['ipa']}'";
        $res = $this->query($is_have_host);
        if (!empty($res)) {
            //再去查是不是同一个采集器下的
            $sql = "select * from host_sensor_reference where sensor_id = unhex('{$host_id}') and host_id = unhex('{$res[0]['id']}')";
            $result = $this->execute($sql);
            if (!empty($result)) {
                //更新
                $this->sensor_upd($res[0]['id'], $data);
            } else {

                //新增
                $this->sensor_add($data, $ctx, $host_id);
            }
        } else {

            //新增
            $this->sensor_add($data, $ctx, $host_id);
        }
    }

    //资产新增
    public function sensor_add($data, $ctx, $host_id)
    {

        $uuid = uuid();

        $sqls = [];

        //host
        $sqls[] = "insert into host(`id`,`ctx`,`hostname`) values(unhex('{$uuid}'),unhex('{$ctx}'),'{$data['assetname']}')";

        //host_ip
        $sqls[] = "insert into host_ip(`host_id`,`ip`,`mac`) values(unhex('{$uuid}'),INET6_ATON('{$data['ipa']}'),unhex('{$data['maca']}'))";

        //host_types
        //查新增资产的类型
        if (!empty($data['devtype'])) {
            $type = "select id,class from device_types where enname = '{$data['devtype']}'";
            $type = $this->query($type);

            if(!empty($type)){
                $sqls[] = "insert into host_types(`host_id`,`type`,`subtype`) values(unhex('{$uuid}'),{$type[0]['class']},{$type[0]['id']})";
            }else{
                $sqls[] = "insert into host_types(`host_id`,`type`,`subtype`) values(unhex('{$uuid}'),'','')";
            }

        } else {
            $sqls[] = "insert into host_types(`host_id`,`type`,`subtype`) values(unhex('{$uuid}'),'','')";
        }

        //host_properties
        if ($data['ostype'] == 'None') {
            $sqls[] = "insert into host_properties(`host_id`,`value`) values(unhex('{$uuid}'),'')";
        } else {
            $sqls[] = "insert into host_properties(`host_id`,`value`) values(unhex('{$uuid}'),'{$data['ostype']}')";
        }


        //host_sensor_reference
        $sqls[] = "insert into host_sensor_reference(`host_id`,`sensor_id`) values(unhex('{$uuid}'),unhex('{$host_id}'))";

        //host_services
        if ($data['open_service'] == 'None') {
            $sqls[] = "insert into host_services(`host_id`,`service`) values(unhex('{$uuid}'),'')";
        } else {
            $sqls[] = "insert into host_services(`host_id`,`service`) values(unhex('{$uuid}'),'{$data['open_service']}')";
        }


        foreach ($sqls as $sql) {
            $pool = $this->execute($sql);

        }
        //判断事务状态,事务使用貌似不管用
        if (in_array(false, $pool, TRUE)) {
            $this->rollback();


            jsonError('更新失败!');
        } else {
            $this->commit();
        }


    }

    //资产更新
    public function sensor_upd($host_id, $data)
    {
        //查资产的类型
        $sqls = [];

        //host
        $sqls[] = "update host set `hostname` = '{$data['assetname']}' where `id` = unhex('{$host_id}')";

        //host_ip
        $sqls[] = "update host_ip set `ip` = INET6_ATON('{$data['ipa']}'),`mac` = unhex('{$data['maca']}') where `host_id` = unhex('{$host_id}')";

        //host_types
        if (!empty($data['devtype'])) {
            $type = "select id,class from device_types where enname = '{$data['devtype']}'";
            $type = $this->query($type);
            if (!empty($type)) {
                $sqls[] = "update host_types set `type` = {$type[0]['class']},`subtype` ={$type[0]['id']} where `host_id` = unhex('{$host_id}')";
            } else {
                $sqls[] = "update host_types set `type` = '',`subtype` = '' where `host_id` = unhex('{$host_id}')";
            }

        } else {
            $sqls[] = "update host_types set `type` = '',`subtype` = '' where `host_id` = unhex('{$host_id}')";
        }


        //host_properties
        if ($data['ostype'] == 'None') {
            $sqls[] = "update host_properties set `value` = '' where `host_id` = unhex('{$host_id}')";
        } else {
            $sqls[] = "update host_properties set `value` = '{$data['ostype']}' where `host_id` = unhex('{$host_id}')";
        }


        //host_sensor_reference
        //在当前采集器下 不需要更改

        //host_services
        if ($data['open_service'] == 'None') {
            $sqls[] = "update host_services set `service` = '' where `host_id` = unhex('{$host_id}')";
        } else {
            $sqls[] = "update host_services set `service` = '{$data['open_service']}' where `host_id` = unhex('{$host_id}')";
        }


        foreach ($sqls as $sql) {
            $pool = $this->execute($sql);
        }
        if (in_array(false, $pool, TRUE)) {
            $this->rollback();


            jsonError('更新失败!');
        } else {
            $this->commit();
        }
    }
}