<?php

namespace App\Models;

class NetHost extends Model
{
    protected $tableName = 'net';
    protected $tablePrefix = '';
    protected $pk = 'id';
    protected $conf = './list_conf/netAssetManager.json';

    public function addNetHost($params)
    {
        $ctx = '1DCE035C308311EBA5B4000C29EE55FD';
        $name = decryptcode($params['name']);
        $ips = decryptcode($params['ips']);
        $asset = decryptcode($params['asset']);
        $threshold_c = 0;
        $threshold_a = 0;
        $alert = 0;
        $persistence = 0;
        $rrd_profile = 0;
        $descr = decryptcode($params['descr']);
        $icon = '';
        $external_net = 0;
        $permissions = "''";
        $owner = decryptcode($params['owner']);
        $sensor_id = decryptcode($params['sensor_id']);

        $sqls = [];
        if (empty(decryptcode($params['id']))) {
            $res = self::isHave($ips);
            if(!empty($res)){
                return 0;
            }
            $id = uuid();
            $sql = "insert into net values(unhex('{$id}'),unhex('{$ctx}'),'{$name}','{$ips}',{$asset},{$threshold_c},{$threshold_a},{$alert},{$persistence},{$rrd_profile},'{$descr}','{$icon}',{$external_net},{$permissions},'{$owner}')";
            array_push($sqls, $sql);

            $arr = explode('.', $ips);
            $begin = $arr[0] . '.' . $arr[1] . '.' . $arr[2] . '.0';
            $end = $arr[0] . '.' . $arr[1] . '.' . $arr[2] . '.255';
            $sql = "insert into net_cidrs values(unhex('{$id}'),'{$ips}',INET6_ATON('{$begin}'),INET6_ATON('{$end}'))";
            array_push($sqls, $sql);

            $sql = "insert into net_sensor_reference values(unhex('{$id}'),unhex('{$sensor_id}'))";
            array_push($sqls, $sql);
        } else {
            $id = decryptcode($params['id']);

            $res = self::isHave1($ips,$id);
            if(!empty($res)){
                return 0;
            }
            $sql = "update net set `name` = '{$name}',ips = '{$ips}',asset = {$asset},descr = '{$descr}',owner = '{$owner}' where id =  unhex('{$id}')";
            array_push($sqls, $sql);

            $arr = explode('.', $ips);
            $begin = $arr[0] . '.' . $arr[1] . '.' . $arr[2] . '.0';
            $end = $arr[0] . '.' . $arr[1] . '.' . $arr[2] . '.255';
            $sql = "update net_cidrs set cidr = '{$ips}',`begin`=INET6_ATON('{$begin}'),`end`=INET6_ATON('{$end}') where net_id =  unhex('{$id}')";
            array_push($sqls, $sql);

            $sql = "update net_sensor_reference set sensor_id = unhex('{$sensor_id}') where net_id =  unhex('{$id}')";
            array_push($sqls, $sql);


        }

        foreach ($sqls as $sql) {
            $pool[] = $this->execute($sql);
        }

        //判断事务状态,事务使用貌似不管用
        if (in_array(false, $pool, TRUE)) {
            $this->rollback();


            jsonError('数据格式有误');
        } else {
            $this->commit();
            return true;
        }

    }

    public function delNetHost($id)
    {
        $sqls = [
            "delete from net where id = unhex('{$id}')",
            "delete from net_cidrs where net_id =  unhex('{$id}')",
            "delete from net_sensor_reference where net_id =  unhex('{$id}')"
        ];

        foreach ($sqls as $sql) {
            $pool[] = $this->execute($sql);
        }

        //判断事务状态,事务使用貌似不管用
        if (in_array(false, $pool, TRUE)) {
            $this->rollback();


            jsonError('数据格式有误');
        } else {
            $this->commit();
            return true;
        }
    }

    public function getOneNetHost($id)
    {

        $res1 = $this->query("select name,ips,asset,descr,owner from net where id = unhex('{$id}')");
        $res2 = $this->query("select hex(sensor_id) as sensor_id from net_sensor_reference where net_id = unhex('{$id}')");
        $res1[0]['sensor_id'] = $res2[0]['sensor_id'];
        return $res1[0];
    }

    private function getWhere($params)
    {
        //这里的where条件是为了过滤掉ossim表的原始数据 我们用不到这个 又不能动原始数据
    //    $where['a.name'] = ['exp',"!='Local_192_168_1_0_24'"];

        if(!empty($params['sensor_name'])){
            $where['c.name'] = ['like', "%{$params["sensor_name"]}%"];
        }

        if(!empty($params['ips'])){
            $where['a.ips'] = ['like', "%{$params["ips"]}%"];
        }

        if(!empty($params['asset'])){
            $where['a.asset'] = ['exp', "={$params["asset"]}"];
        }

        if(!empty($params['sensor'])){
            $where['c.name'] = ['like', "%{$params["sensor"]}%"];
        }

        if(!empty($params['descr'])){
            $where['a.descr'] = ['like', "%{$params["descr"]}%"];
        }

        if(!empty($params['owner'])){
            $where['a.owner'] = ['like', "%{$params["owner"]}%"];
        }

        return $where;
    }

    public function getNetHostList($where, $page, $pagesize)
    {
        $where = self::getWhere($where);

        $field = "hex(a.id) id,a.name,a.ips,a.asset,c.name as sensor_name,c.host_id as sensor_id,a.descr,a.owner";
        $join1 = "net_sensor_reference b on b.net_id = a.id";
        $join2 = "udp_sensor c on c.host_id = hex(b.sensor_id)";

        if ($_SESSION['user_device_power'] == 'all' && $_SESSION['user_monitor_power'] == 'all') {
            $data = $this->alias('a')->field($field)->join($join1,'left')->join($join2,'left')->where($where)->page($page, $pagesize)->select();

        }else{
            if (!empty($_SESSION['user_device_power'])) {
                $user_device_power = array_shift(json_decode($_SESSION['user_device_power'], 1));
            }
            if (!empty($_SESSION['user_monitor_power'])) {
                $user_monitor_power = array_shift(json_decode($_SESSION['user_monitor_power'], 1));
            }

            $where['b.sensor_id'] = ['exp',"=unhex('{$user_device_power['host_id']}')"];
        }

        $res['config'] =  self::getConfig();

        $res['list'] = $data;
        $res['total_num'] = count($data);
        $res['total_page'] = ceil($res['total_num']/$pagesize);
        return $res;
    }

    private function getConfig()
    {
        //配置config
        $json = file_get_contents($this->conf);
        return json_decode($json, 1);
    }

    public function isHave($ips)
    {
        $res = $this->field('ips')->where([['ips' => $ips]])->find();
        return $res;
    }

    public function isHave1($ips,$id)
    {
        $res = $this->field('ips')->where([['ips' => $ips],['id' => ['exp'," != unhex('{$id}')"]]])->find();
        return $res;
    }

}

?>