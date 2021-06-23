<?php

namespace App\Models;
class Host extends Model
{
    protected $tableName = 'host';
    protected $tablePrefix = '';
    protected $pk = 'id';

    protected $conf = './list_conf/assetsManager.json';
    //自动完成
    protected $_auto = array(
        array('id', 'getid', 3, 'callback'),
        array('ctx', 'getctx', 3, 'callback'),
        array('icon', 'geticon', 3, 'callback'),
    );

    /**
     * 获取所有
     */

    public function getAll($where = [], $order = 'a.updated desc')
    {
        //field
        $field = 'hex(a.id) id,a.hostname,inet6_ntoa(b.ip) ip,hex(b.mac) mac,a.fqdns,a.persistence,a.asset,a.threshold_a,a.threshold_c,a.alert,a.nat,a.created,a.lat,a.lon';
        $join = 'host_ip b on a.id=b.host_id ';
        $result['list'] = $this->field($field)->alias('a')->join($join)->where($where)->group($group)->order($order)->select();
        /*
         获得资产附加属性
        */
        if (!\Tools::isEmpty($result['list'])) {
            array_walk($result['list'], function (&$arr) {
                $arr['type'] = $this->setType($arr['id']);
                $arr['os'] = $this->setOs($arr['id']);
            });
        }
        return $result;
    }

    /**
     * 获取列表
     */

    public function getList($where = [], $page, $pagesize, $order = 'a.updated desc')
    {

        //配置config

        $json = file_get_contents($this->conf);
        $config = json_decode($json, 256);

        $result['config'] = $config;

        //field
        $field = 'hex(a.id) id,a.hostname,inet6_ntoa(b.ip) ip,hex(b.mac) mac,a.asset,a.alert';
        $join = 'host_ip b on a.id=b.host_id';
        $result['list'] = $this->field($field)->alias('a')->join($join, 'LEFT')->where($where)->order($order)->page($page, $pagesize)->select();
        /*
         获得资产附加属性
        */
        if (!\Tools::isEmpty($result['list'])) {
            array_walk($result['list'], function (&$arr) {
                $arr['type'] = ($this->setType($arr['id'])) == ':' ? '未知' : $this->setType($arr['id']);
                $arr['os'] = ($this->setOs($arr['id'])) == '' ? '未知' : $this->setOs($arr['id']);
                $arr['mac'] = substr($arr['mac'], 0, 2) . '-' . substr($arr['mac'], 2, 2) . '-' . substr($arr['mac'], 4, 2) . '-' . substr($arr['mac'], 6, 2) . '-' . substr($arr['mac'], 8, 2) . '-' . substr($arr['mac'], 10, 2);
            });
        }

        $result['total_num'] = $this->alias('a')->join($join, 'LEFT')->where($where)->count();

        return $result;
    }

    /**
     * 获取单个
     */

    public function getOne($data = [])
    {


        //field
        $field = 'hex(a.id) id,a.hostname,inet6_ntoa(b.ip) ip,hex(b.mac) mac,a.external_host,a.persistence,a.lat,a.lon,a.fqdns,a.asset,a.alert,a.nat,a.descr,a.country,b.interface,a.created,a.updated';
        $join = 'host_ip b on a.id=b.host_id';
        $where = [
            'id' => ['exp', "=unhex('{$data["id"]}')"]
        ];
        $result = $this->field($field)->alias('a')->join($join, 'LEFT')->where($where)->find();
        /*
         获得资产附加属性
        */
        if (!\Tools::isEmpty($result)) {
            $result['type'] = $this->setTypeId($result['id']);
            $result['os'] = $this->setOs($result['id']) == '' ? '未知' : $this->setOs($result['id']);

            $agent = $this->setAgent($result['id']);
            //$arr['agent'] = $agent;
            $result['agent_name'] = $agent['agent_name'];
            $result['agent_ip'] = $agent['agent_ip'];
            $result['lon'] = $result['lon'] == 0 ? '' : $result['lon'];
            $result['lat'] = $result['lon'] == 0 ? '' : $result['lat'];
            //$arr['agent_status'] = $agent['agent_status'];
        };
        return $result;
    }

    /**
     * 获取设备类型
     */
    public function setType( $host_id)
    {
        $type = '';

        $where['host_id'] = ['exp', "=unhex('{$host_id}')"];

        $middle = $this->table('host_types a')->where($where)->field('type,subtype')->find();

        $type = $this->table('device_types')->where(['id' => $middle['type']])->getField('name');
        $subtype = $this->table('device_types')->where(['id' => $middle['subtype']])->getField('name');

        if (!\Tools::isEmpty($type) and !\Tools::isEmpty($subtype)) {
            return $type . ':' . $subtype;
        }
        if (!\Tools::isEmpty($type)) {
            return $type;
        }
        if (!\Tools::isEmpty($subtype)) {
            return $subtype;
        }
        return '未知';
    }

    /**
     * 获取设备类型ID
     */
    public function setTypeId( $host_id)
    {

        $where['host_id'] = ['exp', "=unhex('{$host_id}')"];
        $res = $this->table('host_types a')->where($where)->field('hex(a.host_id) host_id,a.type,a.subtype')->find();
        unset($res['host_id']);
        return array_values($res);
    }

    /**
     * 获取一个操作系统
     * return 大类:小类
     */
    public function setOs( $host_id)
    {
        $where['a.host_id'] = ['exp', "=unhex('{$host_id}')"];
        //$subQuery = $this->table('host_properties a')->join('host_property_reference b on a.property_ref = b.id')->where($where)->field("concat(b.description,':',a.value) os")->buildSql();
        $subQuery = $this->table('host_properties a')->join('host_property_reference b on a.property_ref = b.id')->where($where)->field("a.value os")->buildSql();
        $result = $this->query("select ifnull({$subQuery},'') os");
        //exit($this->_sql());
        /*if($result === false){
            return $this->getlastsql();
        }*/
        return is_array($result) ? $result[0]['os'] : $result;
    }

    /**
     * 获取agent
     */
    public function setAgent( $host_id)
    {
        $where['a.host_id'] = ['exp', "=unhex('{$host_id}')"];
        $result = $this->table('hids_agents a')->field('agent_name,agent_ip')->where($where)->find();
        //return $this->table('hids_agents a')->getlastsql();
        return $result;
    }

    /**
     * 保存Host
     */
    public function saveHost($data)
    {

        //判断是添加还是更新
        $sqls = [
            "replace into host(`id`,`ctx`,`hostname`,`external_host`,`fqdns`,`asset`,`rrd_profile`,`alert`,`persistence`,`nat`,`descr`,`lat`,`lon`,`country`,`icon`) values(unhex('{$data['id']}'),unhex('{$data['ctx']}'),'{$data['hostname']}',{$data['external_host']},'{$data['fqdns']}',{$data['asset']},'{$data['rrd_profile']}',{$data['alert']},{$data['persistence']},'{$data['nat']}','{$data['descr']}',{$data['lat']},{$data['lon']},'{$data['country']}','{$data['icon']}')",
        ];


        //判断是添加还是更新
        $subsql1 = "select hex(host_id) host_id from host_types where host_id = unhex('{$data['id']}')";

        $status = $this->query($subsql1);
        if (!\Tools::isEmpty($status)) {
            //更新
            $sql = "update host_types set `type` = {$data['type'][0]},`subtype` = {$data['type'][1]} where `host_id` = unhex('{$data['id']}')";
            array_push($sqls, $sql);
        } else {
            //添加
            $sql = "replace into host_types(`host_id`,`type`,`subtype`) values(unhex('{$data['id']}'),{$data['type'][0]},{$data['type'][1]})";
            array_push($sqls, $sql);
        }

        //判断是添加还是更新
        $subsql1 = "select inet6_ntoa(ip) ip from host_ip where host_id = unhex('{$data['id']}')";
        $status = $this->query($subsql1);
        if (!\Tools::isEmpty($status)) {
            //更新
            $sql = "update host_ip set `ip` = INET6_ATON('{$data['ip']}'),`mac` = unhex('{$data['mac']}'),`interface` = '{$data['interface']}' where `host_id` = unhex('{$data['id']}')";
            array_push($sqls, $sql);
        } else {
            //添加
            $sql = "replace into host_ip(`host_id`,`ip`,`mac`,`interface`) values(unhex('{$data['id']}'),INET6_ATON('{$data['ip']}'),unhex('{$data['mac']}'),'{$data['interface']}')";
            array_push($sqls, $sql);
        }


        //判断是添加还是更新
        if (isset($data['os'])) {
            $subsql1 = "select hex(host_id) from host_properties where host_id = unhex('{$data['id']}')";
            $status = $this->query($subsql1);
            if (!\Tools::isEmpty($status)) {
                $sql = "update host_properties set `value` = '{$data['os']}' where `host_id` = unhex('{$data['id']}')";
                array_push($sqls, $sql);
            } else {
                $sql = "replace into host_properties(`host_id`, `property_ref`, `last_modified`, `source_id`, `value`) values(unhex('{$data['id']}'),3,now(),1,'{$data['os']}')";
                array_push($sqls, $sql);
            }
        }


        //添加设备如果是采集器和主备的时候,往udp_sensor中添加一份
        $subsql1 = "select id from device_types where name like '%平台' or name like '%采集器'";
        $ids = array_column($this->query($subsql1), 'id');
        if (in_array($data['type'][1], $ids)) {
            $subsql1 = "select count(1) c from udp_sensor where host_id='{$data["id"]}'";
            $mode = $this->query($subsql1)[0]['c'];
            if($mode > 0){
                $sql = "update udp_sensor set ip = '{$data['ip']}', name = '{$data["hostname"]}', `type` = {$data['type'][0]},`subtype` = {$data['type'][1]} where host_id='{$data["id"]}'";
            }else{
                //说明是厂级平台的基础设备
                $sql = "replace into udp_sensor(`host_id`,`ip`,`name`,`sensor_id`,`type`,`subtype`) values('{$data['id']}','{$data['ip']}','{$data["hostname"]}','NOTFOUND',{$data['type'][0]},{$data['type'][1]})";
            }
            array_push($sqls, $sql);
        } else {
            //如果保存的不是基础设备,就删掉关联任务;
            $sql = "delete from udp_sensor where host_id = '{$data['id']}'";
            array_push($sqls, $sql);
        }


        $this->startTrans();

        foreach ($sqls as $sql) {
            $pool[] = $this->execute($sql);
        }

        //判断事务状态,事务使用貌似不管用
        if (in_array(false, $pool, TRUE)) {
            $this->rollback();
            //var_dump($sqls);

            jsonError('数据格式有误');
        } else {
            $this->commit();
        }

        //返回所有影响sql条数
        return array_sum($pool);

    }

    /**
     * 删除资产
     */
    public function delHost($ids)
    {
        $Hexwhere = $this->delHexWhere($ids);
        $where = $this->delWhere($ids);

        //删除采集器辖下资产

        $sqls = [
            "delete from host where id {$Hexwhere}",
            "delete from host_ip where host_id {$Hexwhere}",
            "delete from host_types where host_id {$Hexwhere}",
            "delete from host_properties where host_id {$Hexwhere}",
            "delete from udp_sensor where host_id {$where}",
        ];

        foreach ($sqls as $sql) {
            $pool[] = $this->execute($sql);
        }

        //判断事务状态，事务使用貌似不管用
        if (in_array(false, $pool, TRUE)) {
            $this->rollback();
            jsonError($this->getLastSql());
        } else {
            $this->commit();
        }

        //返回所有影响sql条数
        return array_sum($pool);
    }

    /**
     * 拼装删除条件
     * ids = 1,2,3,4,5,68
     */
    private function delHexWhere($ids)
    {
        //如果是多个资产，资产ID，重新生成条件
        $where = 'in (';
        if (stripos($ids, ',')) {
            $ids = explode(',', $ids);

            while ($item = each($ids)) {
                $where .= "unhex('{$item['value']}'),";
            }
            $where = rtrim($where, ',');
            $where .= ')';
        } else {
            $where .= "unhex('{$ids}'))";
        }

        return $where;
    }

    /**
     * 拼装删除条件
     * ids = 1,2,3,4,5,68
     */
    private function delWhere($ids)
    {
        //如果是多个资产，资产ID，重新生成条件
        $where = 'in (';
        if (stripos($ids, ',')) {
            $ids = explode(',', $ids);

            while ($item = each($ids)) {
                $where .= "'{$item['value']}',";
            }
            $where = rtrim($where, ',');
            $where .= ')';
        } else {
            $where .= "'{$ids}')";
        }

        return $where;
    }

    /**
     * 得到当前的数据对象名称
     * @access public
     * @return string
     */
    public function getModelName()
    {
        return '资产管理';
    }
}
