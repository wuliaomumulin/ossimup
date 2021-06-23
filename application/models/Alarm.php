<?php

namespace App\Models;

use App\Models\Es;
use App\Models\Vulnerability;
use App\Models\Host;

class Alarm extends Model
{
    protected $tableName = 'alarm';
    protected $tablePrefix = '';
    protected $pk = 'backlog_id';

//    public  function __construct($where)
//    {
//        $this->where = self::getWhere($where);
//    }

    private function getWhere($sql,Array $where = [],$table='')
    {




        $inside = '';

        if (!empty($where['begindate']) && !empty($where['enddate'])) {
            //默认时是查全部以b为准 搜索时查客户看到的以主表a为准
            $inside .= " AND ( a.TIMESTAMP >= '{$where["begindate"]}' AND a.TIMESTAMP <= '{$where["enddate"]}' ) ";
            //$new_where[] = ['a.TIMESTAMP' => ["exp", "between '{$where['begindate']}' and '{$where['enddate']}'"]];
        } 

        if (strlen($where['risk']) > 0) {
            //$new_where['a.risk'] = $where['risk'];
            if(substr($inside,-4)!='and ') $inside .= 'and ';
            $inside .= " a.risk = {$where['risk']} ";
            //$new_where[] = ['a.risk' => ["exp", "={$where['risk']}"]];
        }

        if (!empty($where['alarm_type'])) {

            //拼接and
            if(substr($inside,-4)!='and ') $inside .= 'and ';
            // $new_where['ta.category'] = $where['alarm_type'];
            $inside .= " ta.category = '{$where["alarm_type"]}' ";
            //$new_where[] = ['ta.category' => ["exp", "={$where['alarm_type']}"]];
        }

        if (!empty($where['src_ip']) && !empty($where['src_hostname'])) {
            
            if(substr($inside,-4)!='and ') $inside .= 'and ';
            $inside .= " a.src_ip = INET6_ATON('{$where["src_ip"]}') ";

            //$new_where[] = ['a.src_ip' => ["exp", "= INET6_ATON('{$where['src_ip']}')"]];
        }

        if (!empty($where['dst_ip']) && !empty($where['dst_hostname'])) {

            if(substr($inside,-4)!='and ') $inside .= 'and ';
            $inside .= " a.dst_ip = INET6_ATON('{$where["dst_ip"]}') ";
            //$new_where[] = ['a.dst_ip' => ["exp", "= INET6_ATON('{$where['dst_ip']}')"]];
        }

        if (!empty($where['src_ip']) && empty($where['src_hostname'])) {

            if(substr($inside,-4)!='and ') $inside .= 'and ';
            $inside .= " a.src_ip = INET6_ATON('{$where["src_ip"]}') ";

            //$new_where[] = ['a.src_ip' => ["exp", "= INET6_ATON('{$where['src_ip']}')"]];
        }

        if (!empty($where['dst_ip']) && empty($where['dst_hostname'])) {
            if(substr($inside,-4)!='and ') $inside .= 'and ';
            $inside .= " a.dst_ip = INET6_ATON('{$where["dst_ip"]}') ";
            //$new_where[] = ['a.dst_ip' => ["exp", "= INET6_ATON('{$where['dst_ip']}')"]];
        }

        if (!empty($where['src_hostname']) && empty($where['src_ip'])) {
            $ip = self::getHostIp($where['src_hostname']);

            if(substr($inside,-4)!='and ') $inside .= 'and ';
            $inside .= " a.src_ip = INET6_ATON('{$ip}') ";

            //$new_where[] = ['a.src_ip' => ["exp", "= INET6_ATON('{$ip}')"]];
        }

        if (!empty($where['dst_hostname']) && empty($where['dst_ip'])) {
            $ip = self::getHostIp($where['dst_hostname']);

            if(substr($inside,-4)!='and ') $inside .= 'and ';
            $inside .= " a.dst_ip = INET6_ATON('{$ip}') ";
            //$new_where[] = ['a.dst_ip' => ["exp", "= INET6_ATON('{$ip}')"]];
        }

        if (!empty($where['src_port'])) {
            // $new_where = ['a.src_port' => ["exp", "={$where['src_port']}"]];
            if(substr($inside,-4)!='and ') $inside .= 'and ';
            $inside .= " a.src_port = {$where['src_port']} ";
            //$new_where[] = ['a.src_port' => ["exp", "={$where['src_port']}"]];

        }

        if (!empty($where['dst_port'])) {
            if(substr($inside,-4)!='and ') $inside .= 'and ';
            $inside .= " a.dst_port = {$where['dst_port']} ";
            //$new_where = ['a.dst_port' => ["exp", "={$where['dst_port']}"]];
           // $new_where[] = ['a.dst_port' => ["exp", "={$where['dst_port']}"]];
        }

        if (!empty($where['protocol'])) {
            if(substr($inside,-4)!='and ') $inside .= 'and ';
            $inside .= " a.protocol = {$where['protocol']} ";
            //$new_where = ['a.dst_port' => ["exp", "={$where['dst_port']}"]];
            //$new_where[] = ['a.protocol' => ["exp", "={$where['protocol']}"]];
        }

        if (!empty($where['plugin_id'])) {
            if(substr($inside,-4)!='and ') $inside .= 'and ';
            $inside .= " a.plugin_id = {$where['plugin_id']} ";
            //$new_where = ['a.dst_port' => ["exp", "={$where['dst_port']}"]];
            //$new_where[] = ['a.plugin_id' => ["exp", "={$where['plugin_id']}"]];
        }

        if (!empty($where['plugin_sid'])) {
            if(substr($inside,-4)!='and ') $inside .= 'and ';
            $inside .= " a.plugin_sid = {$where['plugin_sid']} ";
            //$new_where = ['a.dst_port' => ["exp", "={$where['dst_port']}"]];
            //$new_where[] = ['a.plugin_sid' => ["exp", "={$where['plugin_sid']}"]];
        }

        if (!empty($where['status'])) {
            if(substr($inside,-4)!='and ') $inside .= 'and ';
            $inside .= " a.STATUS = '{$where["status"]}' ";
            //$new_where = ['a.dst_port' => ["exp", "={$where['dst_port']}"]];
           /* unset($new_where['a.STATUS']);
            $new_where[] = ['a.STATUS' => ["exp", "='{$where['status']}'"]];*/
        }

        //触发类型
        if (!empty($where['kingdom'])) {

            if($inside != 'where' && substr($inside,-4)!='and ') $inside .= 'and ';
            // $new_where['ta.category'] = $where['alarm_type'];
            if(empty($table)){
                $inside .= " ki.id = '{$where["kingdom"]}' ";
            }else{
                $inside .= " ta.kingdom = '{$where["kingdom"]}' ";
            }
            //$new_where[] = ['ki.id' => ["exp", "={$where['kingdom']}"]];
        }

        //产生类型
        if (!empty($where['category'])) {
            if($inside != 'where' && substr($inside,-4)!='and ') $inside .= 'and ';
            //如果为空，我们需要查出内容
            if(empty($table)){
                $inside .= " ca.id = '{$where["category"]}' ";
            }else{
            //如果不为空，我们需要数据条数
                $inside .= " ta.category = '{$where["category"]}' ";
            }
            //$new_where[] = ['ca.id' => ["exp", "={$where['category']}"]];
        }
        //事件ID
        if (!empty($where['event_id'])) {
            if(substr($inside,-4)!='and ') $inside .= 'and ';
            $inside .= " a.event_id = unhex('{$where["event_id"]}') ";
            //$new_where[] = ['a.event_id' => ["exp", "=unhex('{$where['event_id']}')"]];
        }
        //是否确认
        if (!is_null($where['is_read'])) {
            if(substr($inside,-4)!='and ') $inside .= 'and ';
            $inside .= " a.is_read = {$where["is_read"]} ";
            //$new_where[] = ['a.is_read' => $where['is_read']];
        }
        //产生子类型
       if(!empty($where['subcategory'])){
            if(substr($inside,-4)!='and ') $inside .= 'and ';
            $inside .= " a.sid = {$where["subcategory"]} ";
           //$new_where[] = ['ta.sid' => ["exp", "={$where['subcategory']}"]];
       }


        $sql = sprintf($sql,$inside);
//         foreach ($where as $k=>$v){
//
//            if (in_array($k,$gte) && isset($v)){
//                $new_where['b.TIMESTAMP'] = ['between',[$v]];
//            }elseif (in_array($k,$lte) && isset($v)){
//
//                $new_where['b.TIMESTAMP'] = ['between',[]];
//            }/*elseif (in_array($k,$eq) && isset($v)){
//                $new_where['b.TIMESTAMP'] = ['neq',$v];
//            }*//*elseif (in_array($k,$like)){
//
//                $this->where[$k] = ['like','%'.$v.'%'];
//            }*/
//         }
        //var_dump($new_where);die;
        return $sql;
    }

    public function getHostIp($name)
    {
        $hostip = $this->table('host')->field("INET6_NTOA(ip) ip")->join('left join host_ip on host.id = host_ip.host_id')->where(['hostname' => $name])->find();
        return $hostip['ip'];
    }

    public function getCount($join, $where)
    {
        //xss过滤事件
        foreach($where as $k => $v) $where[$k] = self::xss_filter($v);

                $sql = "SELECT
    COUNT( 1 ) AS tp_count 
FROM
    alarm a
    LEFT JOIN (
    alarm_taxonomy ta


    ) ON a.plugin_sid = ta.sid 
    AND a.corr_engine_ctx = ta.engine_id
WHERE
    a.`status` = 'open' %s";

        //file_put_contents('cached/slow_qery.log', self::getWhere($sql,$where,'ta'). PHP_EOL, FILE_APPEND);

        return $this->query(self::getWhere($sql,$where,'ta'))[0]['tp_count'];

        /*$where = self::getWhere($where);
        $count = $this->alias('a')->join($join)->where($where)->count();
        return $count;*/
    }

    public function getOneStats($field, $where)
    {
        $data = $this->alias('a')->field($field)->where($where)->find();
        return $data;
    }

    public function getDataList($field, $join, $where, $group, $order, $page, $page_size)
    {
        $limit = $page-1;
        $offset = $page_size*$page;

        //xss过滤事件
        foreach($where as $k => $v) $where[$k] = self::xss_filter($v);

        $sql = "SELECT
    hex(a.backlog_id) backlog_id,
    hex(a.event_id) event_id,
    hex(a.corr_engine_ctx) corr_engine_ctx,
    a. `timestamp`,
    a. `status`,
    a.protocol,
    INET6_NTOA (a.src_ip) src_ip,
    INET6_NTOA (a.dst_ip) dst_ip,
    a.src_port,
    a.dst_port,
    a.risk,
    a.plugin_id,
    a.plugin_sid,
    ki. NAME AS kingdom,
    ca. NAME AS category,
    ta.subcategory
FROM
    alarm a
LEFT JOIN (
    alarm_taxonomy ta
    LEFT JOIN alarm_kingdoms ki ON ta.kingdom = ki.id
    LEFT JOIN alarm_categories ca ON ta.category = ca.id
) ON a.plugin_sid = ta.sid
AND a.corr_engine_ctx = ta.engine_id,
 backlog b
WHERE
    a. STATUS = 'open'
AND a.backlog_id = b.id
AND (
    b. `timestamp` <> '1970-01-01 00:00:00'
) %s
ORDER BY
    a. `timestamp` DESC
LIMIT ".$limit.",
    ".$offset;

 

        $sql = self::getWhere($sql,$where);
        //file_put_contents('cached/slow_qery.log', $sql. PHP_EOL, FILE_APPEND);

        return $this->query($sql);



    }

//    public function getEvent($field, $where)
//    {
//        $data = $this->table('event')->field($field)->where($where)->order('timestamp desc')->limit(5)->select();
//       // echo  $this->getlastsql();die;
//        return $data;
//    }

    public function gatAlarmCategory()
    {
        $data = $this->table('alarm_categories')->field('id,name')->select();
        return $data;
    }

    /**
     * 根据条件检索事件
     */
    public function getAll($field, $where = [], $order = 'a.timestamp desc'): ?array
    {

        $page = 0;
        $pagesize = 20;
        $result = $this->field($field)->alias('a')->order($order)->page($page, $page_size)->select();

        //exit($this->_sql());
        /*
         获得附加属性
        */
        if (!\Tools::isEmpty($result)) {
            array_walk($result, function (&$arr) {

                $arr['category'] = self::attrCategory($arr);


                unset($arr['corr_engine_ctx']);
            });
        }

        return $result;
    }

    public function getListConfig()
    {
        $where = ['alarm_list_config'];
        $data = $this->table('config')->where(['conf' => ['in', $where]])->select();

        return $this->dataDispost($data);
    }

    public function dataDispost($data)
    {
        $i = 0;
        foreach ($data as $k => $v) {
            $i++;
            if ($i > 1) {
                $res[$v['conf']] = $v['value'];
            } else {
                $res = [$v['conf'] => $v['value']];
            }


        }
        return $res;
    }

    //plugin_sid 获取事件名称
    public function getEventName($param)
    {
        $data = $this->table('plugin_sid')->field('name')->where($param)->find();
        return $data['name'];
    }

    public function getProtocol($protocol = '')
    {
        $data = $this->table('protocol')->field('number,name')->where(['name' => ['like', "%{$protocol}%"]])->select();
        return $data;
    }

    public function getPro($number)
    {
        $name = $this->table('protocol')->field('name')->where(['number' => $number])->find();
        return $name['name'];
    }

//    public function getKingdoms($name = '')
//    {
//        $data = $this->table('alarm_kingdoms')->field('id,name as kingdoms')->where(['name' => ['like',"%{$name}%"]])->select();
//        return $data;
//    }

    public function getCategory()
    {
        $id = $this->table('alarm_categories')->field('id,name as category')->select();
        $data = [];
        foreach ($id as $k => $v) {
            $data['ids'][] = $v['id'];
            $data['names'][] = $v['category'];
        }
        $res['ids'] = implode(',', $data['ids']);
        $res['names'] = implode(',', $data['names']);
        return $res;

    }

//    public function getSubCategory($subcategory = '')
//    {
//        $data = $this->table('alarm_taxonomy')->field('sid,subcategory')->where(['subcategory' => ['like',"%{$subcategory}%"]])->group('sid')->select();
//        return $data;
//    }

    public function pro()
    {
        $ids = $this->field('protocol')->group('protocol')->select();

        $where['number'] = ['in', array_column($ids, 'protocol')];
        $protocol = $this->table('protocol')->field('number,name as protocol')->where($where)->select();
        $data = [];
        foreach ($protocol as $k => $v) {
            $data['number'][] = $v['number'];
            $data['protocol'][] = $v['protocol'];
        }
        $res['number'] = implode(',', $data['number']);
        $res['protocol'] = implode(',', $data['protocol']);
        return $res;
    }

    /**
     * 获得告警事件组合的分类,就是大类:小类
     * 数据项
     */
    private function attrCategory($arr)
    {
        $where = [
            'ta.sid' => $arr['plugin_sid'],
            'ta.engine_id' => $arr['corr_engine_ctx'],
        ];

        $join = 'LEFT JOIN alarm_kingdoms ki ON ta.kingdom = ki.id LEFT JOIN alarm_categories ca ON ta.category = ca.id';
        $result = $this->table('alarm_taxonomy')->alias('ta')->join($join)->where($where)->getField('CONCAT(ki.name,":",ca.name) category');

        return key($result);
    }

    public function getAlarmEvent($event_id)
    {
        $event_id = strtolower($event_id);
        $event_id = substr($event_id, 0, 8) . '-' . substr($event_id, 8, 4) . '-' . substr($event_id, 12, 4) . '-' . substr($event_id, 16, 4) . '-' . substr($event_id, 16);
        $params["query"]["term"]['event_id.keyword'] = $event_id;
        $params['_source'] = ["plugin_sid_desc", "src_ip", "dst_ip", "fdate"];
        $this->es = new Es();
        $data = $this->Format($this->es->query('zn-event-', $params));
        return $data;
    }

    //提取es数据
    private function Format($data)
    {
        $res = [];
        foreach ($data['hits']['hits'] as $k => $v) {
            foreach ($v['_source'] as $key => $value) {
                $res[$key] = $value;
            }
        }
        return $res;
    }


    public function getHostInfo($ip)
    {

        $host_id = $this->table('host_ip')->alias('a')->field("hex(a.host_id) host_id,hex(a.mac) mac,interface")->where(['a.ip' => ["exp", "=INET6_ATON('{$ip}')"]])->find();

        if(!empty($host_id['host_id'])){
            $res = [];
            //var_dump($host_id);
            if (empty($host_id['mac'])) {
                $res['mac'] = '未设置';
            } else { //122334455667
                $res['mac'] = substr($host_id['mac'], 0, 2) . '-' . substr($host_id['mac'], 2, 2) . '-' . substr($host_id['mac'], 4, 2) . '-' . substr($host_id['mac'], 6, 2) . '-' . substr($host_id['mac'], 8, 2) . '-' . substr($host_id['mac'], 10, 2);

            }

            if (empty($host_id['interface'])) {
                $res['interface'] = '未设置';
            } else {
                $res['interface'] = $host_id['interface'];
            }


            $field = "a.fqdns,a.asset,b.service,c.type,c.subtype,d.value";
            $join = 'host_services b on a.id = b.host_id';
            $join1 = "host_types c on c.host_id = a.id";
            $join2 = "host_properties d on d.host_id = a.id";
            $data = $this->table('host')->alias('a')->field($field)->join($join, 'left')->join($join1, 'left')->join($join2,'left')->where(['a.id' => ["exp", "=unhex('{$host_id['host_id']}')"]])->find();

            if (empty($data['fqdns'])) {
                $res['fqdns'] = '未设置';
            }else{
                $res['fqdns'] = $data['fqdns'];
            }

            if (empty($data['value'])) {
                $res['value'] = '未设置';
            }else{
                $res['value'] = $data['value'];
            }

            if (empty($data['asset'])) {
                $res['asset'] = '未设置';
            } elseif ($data['asset'] == 1) {
                $res['asset'] = '低';
            } elseif ($data['asset'] == 2) {
                $res['asset'] = '中';
            } elseif ($data['asset'] == 3) {
                $res['asset'] = '高';
            }

            if (empty($data['service'])) {
                $res['service'] = '暂无';
            } else {
                $arr = json_decode($data['service'], 1);
                $ports = '';
                foreach ($arr as $k => $v) {
                    $ports .= $v['port'] . ',';
                }
                $res['service'] = rtrim($ports, ',');

            }

            //查所属分区
            $model1 = new Vulnerability();
            $sensor = $model1->getSensor($host_id['host_id']);
            if (empty($sensor)) {
                $res['sensor'] = '未设置';
            }else{
                $res['sensor'] = $sensor;
            }

            $model2 = new Host();
            $type = $model2->setType($host_id['host_id']);
            $res['type'] = $type;
        }else{
            $res['mac'] = '未设置';
            $res['interface'] = '未设置';
            $res['fqdns'] = '未设置';
            $res['asset'] = '未设置';
            $res['service'] = '暂无';
            $res['sensor'] = '未设置';
            $res['type'] = '未设置';
        }

        return $res;

    }

}

?>
