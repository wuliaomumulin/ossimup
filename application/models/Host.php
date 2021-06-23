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

    public function getAll($where = [], $order = 'a.updated desc'): ?array
    {
        //field
        $field = 'hex(a.id) id,a.hostname,inet6_ntoa(b.ip) ip,hex(b.mac) mac,a.fqdns,a.persistence,a.asset,a.threshold_a,a.threshold_c,a.alert,a.nat,a.created,a.lat,a.lon,d.name';
        $join = 'host_ip b on a.id=b.host_id ';
        $join2 = 'host_sensor_reference c on a.id=c.host_id';
        $join3 = 'udp_sensor d on unhex(d.host_id) = c.sensor_id';
        $result['list'] = $this->field($field)->alias('a')->join($join, 'left')->join($join2, 'left')->join($join3, 'left')->where($where)->order($order)->select();

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

    public function getList($where = [], int $page, int $pagesize, $order = 'a.updated desc'): ?array
    {

        $result['config'] = self::getConfig();

        //field
        $field = 'hex(a.id) id,a.hostname,inet6_ntoa(b.ip) ip,hex(b.mac) mac,a.asset,a.fqdns,a.alert,a.created,a.updated,d.name,f.service ports';
        $join = 'host_ip b on a.id=b.host_id ';
        $join2 = 'host_sensor_reference c on a.id=c.host_id';
        $join3 = 'udp_sensor d on unhex(d.host_id) = c.sensor_id';
        $join5 = 'host_services f on f.host_id = a.id';
        if ($where['e.type'] == 0 && $where['e.subtype'] == 0) {
            if ($_SESSION['user_device_power'] == 'all' && $_SESSION['user_monitor_power'] == 'all') {

                $join4 = 'host_types e on a.id = e.host_id';
                $result['list'] = $this->field($field)->alias('a')->join($join, 'LEFT')->join($join2, 'LEFT')->join($join3, 'LEFT')->join($join4, 'LEFT')->join($join5, 'LEFT')->where($where)->order($order)->page($page, $pagesize)->select();
                //exit($this->_sql());
                $result['total_num'] = $this->alias('a')->join($join, 'LEFT')->join($join2, 'LEFT')->join($join3, 'LEFT')->join($join4, 'LEFT')->join($join5, 'LEFT')->where($where)->count();
            } else {
                if (!empty($_SESSION['user_device_power'])) {
                    $user_device_power = array_shift(json_decode($_SESSION['user_device_power'], 1));
                }
                if (!empty($_SESSION['user_monitor_power'])) {
                    $user_monitor_power = array_shift(json_decode($_SESSION['user_monitor_power'], 1));
                }

                $where['d.host_id'] = $user_device_power['host_id'];
//                $where['_logic'] = 'or';
//                $where['a.id'] = ['exp',"in(unhex('{$user_device_power["host_id"]}'),unhex('{$user_monitor_power["host_id"]}'))"];

                $join4 = 'host_types e on a.id = e.host_id';
                $result['list'] = $this->field($field)->alias('a')->join($join, 'LEFT')->join($join2, 'LEFT')->join($join3, 'LEFT')->join($join4, 'LEFT')->where($where)->order($order)->page($page, $pagesize)->select();
                // echo $this->getlastsql();die;
                $result['total_num'] = $this->alias('a')->join($join, 'LEFT')->join($join2, 'LEFT')->join($join3, 'LEFT')->join($join4, 'LEFT')->where($where)->count();

            }

        } else {
            if ($_SESSION['user_device_power'] == 'all' && $_SESSION['user_monitor_power'] == 'all') {
                $result['list'] = $this->field($field)->alias('a')->join($join, 'LEFT')->join($join2, 'LEFT')->join($join3, 'LEFT')->where($where)->order($order)->page($page, $pagesize)->select();
                $result['total_num'] = $this->alias('a')->join($join, 'LEFT')->join($join2, 'LEFT')->join($join3, 'LEFT')->where($where)->count();
            } else {

                if (!empty($_SESSION['user_device_power'])) {
                    $user_device_power = array_shift(json_decode($_SESSION['user_device_power'], 1));
                }
                if (!empty($_SESSION['user_monitor_power'])) {
                    $user_monitor_power = array_shift(json_decode($_SESSION['user_monitor_power'], 1));
                }
                $where['d.host_id'] = $user_device_power['host_id'];
//                $where['_logic'] = 'or';
//                $where['a.id'] = ['exp',"in(unhex('{$user_device_power["host_id"]}'),unhex('{$user_monitor_power["host_id"]}'))"];

                $result['list'] = $this->field($field)->alias('a')->join($join, 'LEFT')->join($join2, 'LEFT')->join($join3, 'LEFT')->where($where)->order($order)->page($page, $pagesize)->select();
                $result['total_num'] = $this->alias('a')->join($join, 'LEFT')->join($join2, 'LEFT')->join($join3, 'LEFT')->where($where)->count();
            }


        }
        //var_dump($result['list']);die;
        /*
         获得资产附加属性
        */
        if (!\Tools::isEmpty($result['list'])) {
            array_walk($result['list'], function (&$arr) {
                $arr['hostname'] = html_entity_decode($arr['hostname']);
                $arr['name'] = html_entity_decode($arr['name']);
                $arr['type'] = ($this->setType($arr['id'])) == ':' ? '未设置' : $this->setType($arr['id']);
                $arr['os'] = (($this->setOs($arr['id'])) == '' || ($this->setOs($arr['id'])) == '未知') ? '未设置' : $this->setOs($arr['id']);
                $arr['mac'] = substr($arr['mac'], 0, 2) . '-' . substr($arr['mac'], 2, 2) . '-' . substr($arr['mac'], 4, 2) . '-' . substr($arr['mac'], 6, 2) . '-' . substr($arr['mac'], 8, 2) . '-' . substr($arr['mac'], 10, 2);
                $arr['zjws'] = ($this->setAgent($arr['id'])) == '' ? '否' : '是';
                $arr['ports'] = implode(',', array_column(json_decode($arr['ports'], true), 'port'));
            });
        }


        return $result;
    }

    /**
     * 获取单个
     */

    public function getOne($where = []): ?array
    {


        //field
        $field = 'hex(a.id) id,a.hostname,inet6_ntoa(b.ip) ip,hex(b.mac) mac,a.external_host,a.persistence,a.lat,a.lon,a.fqdns,a.asset,a.alert,a.nat,a.descr,a.country,b.interface,a.created,a.updated,hex(c.sensor_id) as host_id';
        $join = 'host_ip b on a.id=b.host_id';
        $join1 = 'host_sensor_reference c on a.id=c.host_id';
        $result = $this->field($field)->alias('a')->join($join, 'LEFT')->join($join1, 'LEFT')->where($where)->find();
        if ($result['host_id'] == '00000000000000000000000000000000') {
            unset($result['host_id']);
        }
        //echo $this->getlastsql();die;
        /*
         获得资产附加属性
        */

        if (!\Tools::isEmpty($result)) {
            $result['hostname'] = html_entity_decode($result['hostname']);
            $result['descr'] = html_entity_decode($result['descr']);
            $result['type'] = $this->setTypeId($result['id']);
            $result['os'] = ($this->setOs($result['id']) == '') || ($this->setOs($result['id']) == '未知') ? '' : $this->setOs($result['id']);

            $agent = $this->setAgent($result['id']);
            //$arr['agent'] = $agent;
            $result['agent_name'] = $agent['agent_name'];
            $result['agent_ip'] = $agent['agent_ip'];
            $result['lon'] = $result['lon'] == 0 ? '' : $result['lon'];
            $result['lat'] = $result['lon'] == 0 ? '' : $result['lat'];
            $result['mac'] = substr($result['mac'], 0, 12);
            //$arr['agent_status'] = $agent['agent_status'];
        };
        return $result;
    }

    /**
     * 获取设备类型
     */
    public function setType(?string $host_id): ?string
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
        return '未设置';
    }

    /**
     * 获取设备类型ID
     */
    public function setTypeId(?string $host_id): ?array
    {

        $where['host_id'] = ['exp', "=unhex('{$host_id}')"];
        $res = $this->table('host_types a')->where($where)->field('hex(a.host_id) host_id,a.type,a.subtype')->find();
        if ($res['type'] == 0) {
            $res['type'] = $res['subtype'];
            $re = $this->table('device_types')->where(['class' => $res['type'], 'enname' => 'unknow'])->field('id')->find();
            $res['subtype'] = $re['id'];
        }
        unset($res['host_id']);
        return array_values($res);
    }

    /**
     * 获取一个操作系统
     * return 大类:小类
     */
    public function setOs(?string $host_id)
    {
        $where['host_id'] = ['exp', "=unhex('{$host_id}')"];
        //$subQuery = $this->table('host_properties a')->join('host_property_reference b on a.property_ref = b.id')->where($where)->field("concat(b.description,':',a.value) os")->buildSql();
        //$subQuery = $this->table('host_properties a')->join('host_property_reference b on a.property_ref = b.id')->where($where)->field("a.value os")->buildSql();
        $subQuery = $this->table('host_properties')->where($where)->field("value os")->buildSql();

        $result = $this->query("select ifnull({$subQuery},'') os");
        //var_dump($result);
        //exit($this->_sql());
        /*if($result === false){
            return $this->getlastsql();
        }*/
        return is_array($result) ? $result[0]['os'] : $result;
    }

    /**
     * 获取agent
     */
    public function setAgent(?string $host_id): ?array
    {
        $where['a.host_id'] = ['exp', "=unhex('{$host_id}')"];
        $result = $this->table('hids_agents a')->field('agent_name,agent_ip')->where($where)->find();
        return $result;
    }

    /**
     * 保存Host
     */
    public function saveHost($data)
    {

        //判断是添加还是更新
//        $sqls = [
//            "replace into host(`id`,`ctx`,`hostname`,`external_host`,`fqdns`,`asset`,`rrd_profile`,`alert`,`persistence`,`nat`,`descr`,`lat`,`lon`,`country`,`icon`) values(unhex('{$data['id']}'),unhex('{$data['ctx']}'),'{$data['hostname']}',{$data['external_host']},'{$data['fqdns']}',{$data['asset']},'{$data['rrd_profile']}',{$data['alert']},{$data['persistence']},'{$data['nat']}','{$data['descr']}',{$data['lat']},{$data['lon']},'{$data['country']}','{$data['icon']}')",
//        ];

// (unhex('{$data['id']}'),unhex('{$data['ctx']}'),'{$data['hostname']}',{$data['external_host']},'{$data['fqdns']}',{$data['asset']},'{$data['rrd_profile']}',{$data['alert']},{$data['persistence']},'{$data['nat']}','{$data['descr']}',{$data['lat']},{$data['lon']},'{$data['country']}','{$data['icon']}','".date("Y-m-d H:i:s",time())."')",
        if (!isset($data['type'][1])) {
            $data['type'][1] = 0;
        }

        //判断是添加还是更新
        $subsql1 = "select hex(host_id) host_id from host_types where host_id = unhex('{$data['id']}')";
        $sqls = [];
        $status = $this->query($subsql1);
        if (!\Tools::isEmpty($status)) {
            //更新
            $sql1 = "update host set `id` = unhex('{$data['id']}'),`ctx` = unhex('{$data['ctx']}'),`hostname` = '{$data['hostname']}',`external_host` = {$data['external_host']},`fqdns` = '{$data['fqdns']}',`asset` = {$data['asset']},`rrd_profile` ='{$data['rrd_profile']}' ,`alert` = {$data['alert']},`persistence` ={$data['persistence']} ,`nat` = '{$data['nat']}',`descr` = '{$data['descr']}',`lat` = {$data['lat']},`lon` = {$data['lon']},`country` = '{$data['country']}',`icon` = '{$data['icon']}',`updated` = '" . date("Y-m-d H:i:s", time()) . "' where `id` = unhex('{$data['id']}')";

            array_push($sqls, $sql1);

            $sql = "update host_types set `type` = {$data['type'][0]},`subtype` = {$data['type'][1]} where `host_id` = unhex('{$data['id']}')";
            array_push($sqls, $sql);
        } else {
            $sql1 = "replace into host(`id`,`ctx`,`hostname`,`external_host`,`fqdns`,`asset`,`rrd_profile`,`alert`,`persistence`,`nat`,`descr`,`lat`,`lon`,`country`,`icon`) values(unhex('{$data['id']}'),unhex('{$data['ctx']}'),'{$data['hostname']}',{$data['external_host']},'{$data['fqdns']}',{$data['asset']},'{$data['rrd_profile']}',{$data['alert']},{$data['persistence']},'{$data['nat']}','{$data['descr']}',{$data['lat']},{$data['lon']},'{$data['country']}','{$data['icon']}')";
            array_push($sqls, $sql1);
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


        /*
            添加设备如果是采集器和主备的时候,往udp_sensor中添加一份
            一组和数据库对应用于描述资产类型的序号
        */
        $ids = [1101, 1102, 1103, 1104, 1105, 1201, 1202, 1203];

        if (in_array($data['type'][1], $ids)) {
            $subsql1 = "select count(1) c from udp_sensor where host_id='{$data["id"]}'";
            $mode = $this->query($subsql1)[0]['c'];
            if ($mode > 0) {
                $sql = "update udp_sensor set ip = '{$data['ip']}', name = '{$data["hostname"]}', `type` = {$data['type'][0]},`subtype` = {$data['type'][1]},`descr` = '{$data['descr']}' where host_id='{$data["id"]}'";
            } else {
                //说明是厂级平台的基础设备
                $sql = "replace into udp_sensor(`host_id`,`ip`,`name`,`sensor_id`,`type`,`subtype`,`descr`) values('{$data['id']}','{$data['ip']}','{$data["hostname"]}','NOTFOUND',{$data['type'][0]},{$data['type'][1]},'{$data['descr']}')";
            }
            array_push($sqls, $sql);
        } else {
            //如果保存的不是基础设备,就删掉关联任务;
            $sql = "delete from udp_sensor where host_id = '{$data['id']}'";
            array_push($sqls, $sql);
        }

        //判断是添加还是更新

        $subsql1 = "select hex(host_id) from host_sensor_reference where host_id = unhex('{$data['id']}')";
        $status = $this->query($subsql1);
        if (!\Tools::isEmpty($status)) {
            $sql = "update host_sensor_reference set `sensor_id` = unhex('{$data['host_id']}') where `host_id` = unhex('{$data['id']}')";
            array_push($sqls, $sql);
        } else {

            $sql = "replace into host_sensor_reference(`host_id`, `sensor_id`) values(unhex('{$data['id']}'),unhex('{$data['host_id']}'))";
            //  echo $sql;die;
            array_push($sqls, $sql);
        }

        //判断是添加还是更新
        $subsql1 = "select hex(host_id) from host_services where host_id = unhex('{$data['id']}')";
        $status = $this->query($subsql1);
        if (\Tools::isEmpty($status)) {
            $sql = "replace into host_services(`host_id`, `host_ip`) values(unhex('{$data['id']}'),INET6_ATON('{$data['ip']}'))";

            array_push($sqls, $sql);
        }


        $this->startTrans();

        foreach ($sqls as $sql) {
            $pool[] = $this->execute($sql);
        }

        //判断事务状态,事务使用貌似不管用
        if (in_array(false, $pool, TRUE)) {
            $this->rollback();


            jsonError('数据格式有误');
        } else {
            $this->commit();
        }

        //返回所有影响sql条数
        return array_sum($pool);

    }

    /*
    *是不是采集器
    */
    public function isSensor($ids)
    {
        $host_id['sensor'] = [];
        $host_id['host'] = [];
        foreach ($ids as $v) {
            $res = $this->query("select * from udp_sensor where host_id = '{$v}'");
            if (!empty($res[0])) {
                array_push($host_id['sensor'], $v);
            } else {
                array_push($host_id['host'], $v);
            }
        }
        return $host_id;
    }


    /**
     * 删除资产
     */
    public function delHost($ids)
    {
        //var_dump($ids);die;
        //获取采集器下面的资产id 再和所选资产去重
        if (!empty($ids['id']['sensor'])) {
            if (empty($ids['id']['host'])) {
                $ids['id']['host'] = [];
            }
            foreach ($ids['id']['sensor'] as $v) {
                $res = $this->query("select hex(host_id) as host_id from host_sensor_reference where sensor_id = unhex('{$v}')");

                if (!empty($res)) {
                    foreach ($res as $value) {
                        array_push($ids['id']['host'], $value['host_id']);
                    }
                }
                array_push($ids['id']['host'], $v);
            }

            $ids['host'] = array_values(array_unique($ids['id']['host']));

        }

        $res = [];
        foreach ($ids['host'] as $v) {
            //先看删除的是不是采集器  是的话 删除关联关系
//           $res = $this->query("select * from udp_sensor where host_id = '{$v}'");
//           if(!empty($res)){
//               $this->execute("delete from host_sensor_reference where sensor_id = unhex('{$v}')");
//           }
            $Hexwhere = $this->delHexWhere($v);

            $where = $this->delWhere($v);

            //删除采集器辖下资产

            $sqls = [
                "delete from host where id {$Hexwhere}",
                "delete from host_ip where host_id {$Hexwhere}",
                "delete from host_types where host_id {$Hexwhere}",
                "delete from host_properties where host_id {$Hexwhere}",
                "delete from udp_sensor where host_id {$where}",
                "delete from host_sensor_reference where host_id {$Hexwhere}",
                "delete from host_services where host_id {$Hexwhere}",
                "delete from hids_agents where host_id {$Hexwhere}"
            ];

            $pool = [];
            foreach ($sqls as $sql) {
                $pool[] = $this->execute($sql);
            }

            //  var_dump($pool);
            //判断事务状态，事务使用貌似不管用
            if (in_array(false, $pool, TRUE)) {
                $this->rollback();
                jsonError($this->getLastSql());
            } else {
                $res[] = array_sum($pool);
                $this->commit();
            }
        }

        //返回所有影响sql条数
        return array_sum($res);
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

    public function getTopologyAsset($page, $page_size)
    {
        $data = $this->table('topology_node')->field('id,ip,name,mac,alert,asset,type1,type2,device,device_name,model,description')->where(['is_asset' => 0])->page($page, $page_size)->select();
        foreach ($data as $k => $v) {
            $data[$k]['type'] = [];
            array_push($data[$k]['type'], $v['type1'], $v['type2']);
            unset($data[$k]['type1']);
            unset($data[$k]['type2']);
        }
        $res['list'] = $data;
        $total_num = $this->table('topology_node')->field('count(id) sum')->where(['is_asset' => 0])->find();
        $res['total_num'] = (int)$total_num['sum'];
        return $res;
    }

    public function quchong($data)
    {
        $host_id = $this->table('host a')->field('hex(a.id) host_id')->join('left join host_ip b on a.id = b.host_id left join host_sensor_reference c on a.id = c.host_id')->where([['b.ip' => ['exp', "=INET6_ATON('{$data['ip']}')"]], ['c.sensor_id' => ['exp', "=unhex('{$data['sensor']}')"]]])->find();

        if (!empty($host_id)) {
            return true;
        } else {
            return false;
        }
    }

    public function synchronousHostLiu($data)
    {
        $sqls = [];
        //host
        $sqls[] = "insert into host(`id`,`ctx`,`hostname`,`fqdns`,`asset`,`threshold_c`,`threshold_a`,`alert`,`persistence`,`nat`,`descr`,`lat`,`lon`,`country`,`external_host`) values(unhex('{$data['id']}'),unhex('{$data['ctx']}'),'{$data['hostname']}','{$data['fqdns']}','{$data['asset']}',1,1,'{$data['alert']}','{$data['persistence']}','{$data['nat']}','{$data['descr']}','{$data['lat']}','{$data['lon']}','{$data['country']}','{$data['external_host']}')";
        //host_ip
        $sqls[] = "insert into host_ip(`host_id`,`ip`,`mac`) values(unhex('{$data['id']}'),INET6_ATON('{$data['ip']}'),unhex('{$data['mac']}'))";
        //host_types
        $sqls[] = "insert into host_types(`host_id`,`type`,`subtype`) values(unhex('{$data['id']}'),{$data['type']},{$data['subtype']})";
        //host_properties
        $sqls[] = "insert into host_properties(`host_id`,`value`,`extra`) values(unhex('{$data['id']}'),'{$data['os']}','{$data['model']}')";
        //host_sensor_reference
        $sqls[] = "insert into host_sensor_reference(`host_id`,`sensor_id`) values(unhex('{$data['id']}'),unhex('{$data['sensor']}'))";
        //host_services
        $sqls[] = "insert into host_services(`host_id`,`service`) values(unhex('{$data['id']}'),'')";

//        //判定是不是安装了主机卫士
//        $sqls[] = "insert into hids_agents (`sensor_id`,`agent_id`,`host_id`) value (unhex('{$data['sensor']}'),'{$data['hids_id']}',unhex('{$data['id']}'))";

        foreach ($sqls as $sql) {
            $res = $this->execute($sql);
            if (intval($res) < 0) {
                return $data['ip'];
            }
        }
    }


    public function synchronousHostZj($data)
    {
        $sqls = [];
        //host
        $sqls[] = "insert into host(`id`,`ctx`,`hostname`,`fqdns`,`asset`,`threshold_c`,`threshold_a`,`alert`,`persistence`,`nat`,`descr`,`lat`,`lon`,`country`,`external_host`) values(unhex('{$data['id']}'),unhex('{$data['ctx']}'),'{$data['hostname']}','{$data['fqdns']}','{$data['asset']}',1,1,'{$data['alert']}','{$data['persistence']}','{$data['nat']}','{$data['descr']}','{$data['lat']}','{$data['lon']}','{$data['country']}','{$data['external_host']}')";
        //host_ip
        $sqls[] = "insert into host_ip(`host_id`,`ip`,`mac`) values(unhex('{$data['id']}'),INET6_ATON('{$data['ip']}'),unhex('{$data['mac']}'))";
        //host_types
        $sqls[] = "insert into host_types(`host_id`,`type`,`subtype`) values(unhex('{$data['id']}'),{$data['type']},{$data['subtype']})";
        //host_properties
        $sqls[] = "insert into host_properties(`host_id`,`value`,`extra`) values(unhex('{$data['id']}'),'{$data['os']}','{$data['model']}')";
        //host_sensor_reference
        $sqls[] = "insert into host_sensor_reference(`host_id`,`sensor_id`) values(unhex('{$data['id']}'),unhex('{$data['sensor']}'))";
        //host_services
        $sqls[] = "insert into host_services(`host_id`,`service`) values(unhex('{$data['id']}'),'')";

        //判定是不是安装了主机卫士
        $sqls[] = "insert into hids_agents (`sensor_id`,`agent_id`,`host_id`) value (unhex('{$data['sensor']}'),'{$data['hids_id']}',unhex('{$data['id']}'))";

        foreach ($sqls as $sql) {
            $res = $this->execute($sql);
            if (intval($res) < 0) {
                return $data['ip'];
            }
        }
    }

    public function getsensorid($ip)
    {
        $sensor_id = $this->table('udp_sensor')->field('host_id')->where(['ip' => $ip])->find();
        return $sensor_id['host_id'];
    }

    public function getConfig()
    {
        //配置config
        $json = file_get_contents($this->conf);
        return json_decode($json, 1);
    }

    public function macFlush()
    {
        $field = 'hex(a.id) id,a.hostname,inet6_ntoa(b.ip) ip,hex(b.mac) mac,a.asset,a.fqdns,a.alert,a.created,a.updated,d.name,d.ip as sensor_ip,f.service ports';
        $join = 'host_ip b on a.id=b.host_id ';
        $join2 = 'host_sensor_reference c on a.id=c.host_id';
        $join3 = 'udp_sensor d on unhex(d.host_id) = c.sensor_id';
        $join5 = 'host_services f on f.host_id = a.id';
        $result = $this->field($field)->alias('a')->join($join, 'LEFT')->join($join2, 'LEFT')->join($join3, 'LEFT')->join($join5, 'LEFT')->select();

        foreach ($result as $k => $aaa ) {
            if($aaa['mac'] == '0000000000000000000000000000000000000000000000000000000000000000'){
                unset($result[$k]);
            }
        }

        $topology_node = $this->table('topology_node')->field('ip,mac,device')->where(['is_asset' => 1])->select();
        foreach ($result as $k => $v) {


            if (substr($v['mac'], 0, 12) == '000000000000') {

                foreach ($topology_node as $kk => $vv) {

                    if ($v['sensor_ip'] == $vv['device'] && $v['ip'] == $vv['ip']) {

                        $mac = strtoupper(str_replace('-', '', $vv['mac']));

                        $this->execute("update host_ip set mac = unhex('{$mac}') where host_id = unhex('{$v['id']}')");


                    }
                }
            }
        }
       
        return true;
    }
}
