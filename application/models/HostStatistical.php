<?php

namespace App\Models;

class HostStatistical extends Model
{
    public function getSensorSystem()
    {
        $field = "b.value as name,count(a.id) as value";
        $join1 = "host_properties b on a.id = b.host_id";
        $data = $this->table('host')->alias('a')->field($field)->where('b.value != \'\'')->join($join1, 'left')->group('b.value')->order('value desc')->limit(0, 11)->select();
        if (!empty($data)) {
            return $data;
        } else {
            return [['name' => '暂无数据', 'value' => 0]];
        }

    }

    public function getJuanBao()
    {
        $host_id = $this->getSensorId(11, 1101);//采集器
        $shenji_id = $this->getSensorId(11, 1201);//监测审计
        if (!empty($host_id['host_id'])) {
            if (!empty($this->getinfo($host_id))) {
                $data = $this->getinfo($host_id);
            } else {
                $data = [];
            }
            unset($host_id['host_id']);
            unset($host_id['created']);
            array_push($data, $host_id);
        }

        if (!empty($shenji_id['host_id'])) {

            if (!empty($this->getinfo($shenji_id))) {
                $data = $this->getinfo($shenji_id);

            } else {
                $data = [];
            }

            unset($shenji_id['host_id']);
            unset($shenji_id['created']);
            array_push($data, $shenji_id);
        }

        //去空
        foreach ($data as $k => $v) {
            if ($v['value'] == 0) {
                unset($data[$k]);
            }
        }

        if (!empty(array_values($data))) {

            return array_values($data);
        } else {
            return [['name' => '暂无数据', 'value' => 0]];
        }

    }

    public function getZhiSi()
    {
        $host_id = $this->getSensorId(11, 1102);//采集器
        $shenji_id = $this->getSensorId(11, 1202);//监测审计
        if (!empty($host_id['host_id'])) {
            if (!empty($this->getinfo($host_id))) {
                $data = $this->getinfo($host_id);
            } else {
                $data = [];
            }
            unset($host_id['host_id']);
            unset($host_id['created']);
            array_push($data, $host_id);
        }

        if (!empty($shenji_id['host_id'])) {
            if (!empty($this->getinfo($shenji_id))) {
                $data = $this->getinfo($shenji_id);

            } else {
                $data = [];
            }

            unset($shenji_id['host_id']);
            unset($shenji_id['created']);
            array_push($data, $shenji_id);
        }

        //去空
        foreach ($data as $k => $v) {
            if ($v['value'] == 0) {
                unset($data[$k]);
            }
        }
        if (!empty(array_values($data))) {
            return array_values($data);
        } else {
            return [['name' => '暂无数据', 'value' => 0]];
        }
    }

    public function getNengGuan()
    {
        $host_id = $this->getSensorId(11, 1103);  //采集器
        $shenji_id = $this->getSensorId(11, 1203);//监测审计
       // var_dump($host_id);die;
        if (!empty($host_id['host_id'])) {
            //  var_dump(123);die;
            if (!empty($this->getinfo($host_id))) {
                $data = $this->getinfo($host_id);

            } else {
                $data = [];
            }
            unset($host_id['host_id']);
            unset($host_id['created']);
            array_push($data, $host_id);
        }

        if (!empty($shenji_id['host_id'])) {
            if (!empty($this->getinfo($shenji_id))) {
                $data = $this->getinfo($shenji_id);

            } else {
                $data = [];
            }

            unset($shenji_id['host_id']);
            unset($shenji_id['created']);
            array_push($data, $shenji_id);
        }

        //去空
        foreach ($data as $k => $v) {
            if ($v['value'] == 0) {
                unset($data[$k]);
            }
        }
        if (!empty(array_values($data))) {
            return array_values($data);
        } else {
            return [['name' => '暂无数据', 'value' => 0]];
        }
    }

    public function getSensorId($id, $sid)
    {
        $data = $this->table('host_types')->alias('a')->join('device_types b on a.type=b.class and a.subtype=b.id', 'left')->join('host c on a.host_id = c.id', 'left')->field('b.name,count(hex(a.host_id)) value,hex(a.host_id) host_id,c.created')->where([['a.type' => ['exp', "={$id}"]], ['a.subtype' => ['exp', "= {$sid}"]]])->find();
        return $data;
    }


    public function getinfo($host_id)
    {

        $field = "d.name,count(a.id) as value";
        $join1 = "host_sensor_reference b on a.id=b.sensor_id";
        $join2 = "host_types c on b.host_id = c.host_id";
        $join3 = "device_types d on c.type = d.class and c.subtype = d.id";
        $data = $this->table('host')->alias('a')->field($field)->where([['a.id' => ['exp', "=unhex('" . $host_id['host_id'] . "')"]], ['c.type' => ['exp', "!=0"]], ['c.subtype' => ['exp', "!=0"]]])->join($join1, 'left')->join($join2, 'left')->join($join3, 'left')->group('d.name')->select();
        //echo $this->getlastsql();die;
        if ($data[0]['name'] == null) {
            $database = [];
        } else {
            $database = $data;
        }

        //判断有没有扫描同步来的资产 没有匹配到的 是未知类型。
        // 0    0 未知
        $num = $this->table('host_types')->alias('a')->field('count(hex(a.host_id)) value')->join('host_sensor_reference b on a.host_id=b.host_id', 'left')->where([['a.type' => 0], ['a.subtype' => 0], ['b.sensor_id' => ["exp", "=unhex('{$host_id['host_id']}')"]]])->select();
        // 0    1 或者2 或者3  ，，， 反正不是未知的
        $num1 = $this->table('host_types')->alias('a')->field('c.name,count(c.name) value')->join('host_sensor_reference b on a.host_id=b.host_id', 'left')->join('device_types c on a.type=c.class and a.subtype=c.id', 'left')->where([['a.type' => 0], ['a.subtype' => ['exp', '!=0']], ['b.sensor_id' => ["exp", "=unhex('{$host_id['host_id']}')"]]])->select();

        if (!empty($num[0]['value'])) {
            array_push($database, ['name' => '未知', 'value' => $num[0]['value']]);
        }
        if (!empty($num1[0]['value'])) {
            array_push($database, ['name' => $num1[0]['name'], 'value' => $num1[0]['value']]);
        }

        return $database;
    }

    //改了 大屏改成显示高危漏洞资产数了
    public function getHost()
    {
//        $field = "a.created,inet6_ntoa(b.ip) ip,a.hostname";
//        $join1 = "host_ip b on a.id=b.host_id";
//        $data = $this->table('host')->alias('a')->field($field)->join($join1, 'left')->order('a.created desc')->limit(0, 100)->select();
//        return $data;

        $res = self::getRequestList();
        $data = [];
        foreach ($res as $k => $v) {
            $arr = [];
            $arr['name'] = $v;
            $arr['value'] = $this->table('host_services')->where(['service' => ['like', "%\"port\":{$v}%"]])->count('host_id');
            $data[] = $arr;
        }
        return $data;
    }

    private function getRequestList()
    {
        $where = ['asset_open_ports'];
        $data = $this->table('config')->where(['conf' => ['in', $where]])->find();
        if (!empty($data['value'])) {
            $data = explode(',', $data['value']);
            return $data;
        }

    }


    public function getHostService()
    {
        $field = "a.hostname,inet6_ntoa(b.ip) ip,c.service";
        $join1 = "host_ip b on a.id=b.host_id";
        $join2 = "host_services c on a.id=c.host_id";
        $data = $this->table('host')->alias('a')->field($field)->join($join1, 'left')->join($join2, 'left')->order('a.created desc')->limit(0, 100)->select();
        return $data;
    }

    public function hostNum()
    {
        $chejian = ['区域一' => [11, 1101, 1201], '区域二' => [11, 1102, 1202], '区域三' => [11, 1103, 1203]];

        $time = [
            date("Y-m-d ", time()),
            date("Y-m-d ", time() - 86400),
            date("Y-m-d ", time() - 86400 * 2),
            date("Y-m-d ", time() - 86400 * 3),
            date("Y-m-d ", time() - 86400 * 4),
        ];
        $datas["time"] = array_reverse($time);

        foreach ($datas["time"] as $k => $v) {
            $datas["time"][$k] = substr($v, 5, 5);
        }

        foreach ($chejian as $kk => $vv) {
            $data = [];
            $data["name"] = $kk;

            $host_id = $this->getSensorId($vv[0], $vv[1]);
            $shenji_id = $this->getSensorId($vv[0], $vv[2]);

            if (!empty($host_id)) {
                foreach (array_reverse($time) as $k => $v) {
                    $sensor_id = $this->table('host')->field('hex(a.id) id')->alias('a')->where(['a.id' => ['exp', "=unhex('" . $host_id['host_id'] . "')"]])->find();
                    $field = "count(a.id) value";
                    $join1 = "host_sensor_reference b on a.id=b.host_id";
                    $res = $this->table('host')->alias('a')->field($field)->join($join1, 'left')->where([['b.sensor_id' => ['exp', "=unhex('" . $sensor_id['id'] . "')"]], ['a.created' => ['exp', "<= '" . $v . " 23:59:59'"]]])->find();
                    $num = 0;
                    //判断创建的时间跟在不在当前内
                    if (strtotime($v . ' 23:59:59') >= strtotime($host_id['created'])) {
                        $num += $host_id['value'];
                    }

                    if (!empty($shenji_id) && (strtotime($v . ' 23:59:59') >= strtotime($shenji_id['created']))) {
                        $num += $shenji_id['value'];
                    }
                    $num += $res['value'];
                    $data["data"][] = $num;
                }
            }


            $datas["arr"][] = $data;
        }
        return $datas;
    }
}

?>