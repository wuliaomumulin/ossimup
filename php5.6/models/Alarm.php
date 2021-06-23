<?php

namespace App\Models;

class Alarm extends Model
{
    protected $tableName = 'alarm';
    protected $tablePrefix = '';
    protected $pk = 'backlog_id';

//    public  function __construct($where)
//    {
//        $this->where = self::getWhere($where);
//    }

    private static function getWhere($where)
    {

        $new_where['a.STATUS'] = 'open';
        $new_where['a.backlog_id'] = ['exp', "= b.id"];

        //      $gte = ["stime"];//大于等于
        //     $lte = ["etime"];//小于等于
        //     $eq   = ['b.TIMESTAMP'];//等于
        // $like   = ['a.name','a.nickname'];// like
        //find_set_in  diff
        if (!empty($where['begindate']) && !empty($where['enddate'])) {
            //默认时是查全部以b为准 搜索时查客户看到的以主表a为准
            $new_where[] = ['a.TIMESTAMP' => ["exp", "between '{$where['begindate']}' and '{$where['enddate']}'"]];
        } else {
            //    $new_where['b.TIMESTAMP'] = ['neq', $where['TIMESTAMP']];
            $new_where[] = ['b.TIMESTAMP' => ["exp", "<> '{$where['TIMESTAMP']}'"]];
        }

            if (strlen($where['risk']) > 0 ) {
            //$new_where['a.risk'] = $where['risk'];
            $new_where[] = ['a.risk' => ["exp", "={$where['risk']}"]];
        }

        if (!empty($where['alarm_type'])) {
            // $new_where['ta.category'] = $where['alarm_type'];
            $new_where[] = ['ta.category' => ["exp", "={$where['alarm_type']}"]];
        }

        if (!empty($where['src_ip'])) {
            //$new_where = ['a.src_ip' => ["exp", "= INET6_ATON('{$where['src_ip']}')"]];
            $new_where[] = ['a.src_ip' => ["exp", "= INET6_ATON('{$where['src_ip']}')"]];
        }

        if (!empty($where['dst_ip'])) {
            //$new_where = ['a.dst_ip' => ["exp", "= INET6_ATON('{$where['dst_ip']}')"]];
            $new_where[] = ['a.dst_ip' => ["exp", "= INET6_ATON('{$where['dst_ip']}')"]];
        }

        if (!empty($where['src_port'])) {
            // $new_where = ['a.src_port' => ["exp", "={$where['src_port']}"]];
            $new_where[] = ['a.src_port' => ["exp", "={$where['src_port']}"]];
        }

        if (!empty($where['dst_port'])) {
            //$new_where = ['a.dst_port' => ["exp", "={$where['dst_port']}"]];
            $new_where[] = ['a.dst_port' => ["exp", "={$where['dst_port']}"]];
        }

        if (!empty($where['protocol'])) {
            //$new_where = ['a.dst_port' => ["exp", "={$where['dst_port']}"]];
            $new_where[] = ['a.protocol' => ["exp", "={$where['protocol']}"]];
        }

        if (!empty($where['plugin_id'])) {
            //$new_where = ['a.dst_port' => ["exp", "={$where['dst_port']}"]];
            $new_where[] = ['a.plugin_id' => ["exp", "={$where['plugin_id']}"]];
        }

        if (!empty($where['plugin_sid'])) {
            //$new_where = ['a.dst_port' => ["exp", "={$where['dst_port']}"]];
            $new_where[] = ['a.plugin_sid' => ["exp", "={$where['plugin_sid']}"]];
        }

        if (!empty($where['status'])) {
            //$new_where = ['a.dst_port' => ["exp", "={$where['dst_port']}"]];
            unset($new_where['a.STATUS']);
            $new_where[] = ['a.STATUS' => ["exp", "='{$where['status']}'"]];
        }

        //触发类型
        if(!empty($where['kingdom'])){
            $new_where[] = ['ki.id' => ["exp", "={$where['kingdom']}"]];
        }

        //产生类型
        if(!empty($where['category'])){
            $new_where[] = ['ca.id' => ["exp", "={$where['category']}"]];
        }

//        //产生子类型
//        if(!empty($where['subcategory'])){
//            $new_where[] = ['ta.sid' => ["exp", "={$where['subcategory']}"]];
//        }



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
        return $new_where;
    }

    public function getCount($join, $where)
    {
        $where = self::getWhere($where);
        $count = $this->alias('a')->join($join)->where($where)->count();
        return $count;
    }

    public function getOneStats($field, $where)
    {
        $data = $this->alias('a')->field($field)->where($where)->find();
        return $data;
    }

    public function getDataList($field, $join, $where, $group, $order, $page, $page_size)
    {
        $where = self::getWhere($where);
        $data = $this->field($field)->alias("a")
            ->join($join)
            ->where($where)->group($group)->order($order)->page($page, $page_size)->select();
        //echo $this->getlastsql();die;
        return $data;

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
    public function getAll($field, $where = [], $order = 'a.timestamp desc')
    {

    	$page = 0;
    	$pagesize = 20;
    	$result = $this->field($field)->alias('a')->order($order)->page($page, $page_size)->select();

        /*
         获得附加属性
        */
         if(!\Tools::isEmpty($result)){
            array_walk($result,function(&$arr){

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
        $data = $this->table('protocol')->field('number,name')->where(['name' => ['like',"%{$protocol}%"]])->select();
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

            $where['number'] = ['in',array_column($ids,'protocol')];
            $protocol = $this->table('protocol')->field('number,name as protocol')->where($where)->select();
            $data = [];
            foreach ($protocol as $k=> $v){
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
        private function attrCategory($arr){
            $where = [
                'ta.sid' => $arr['plugin_sid'],
                'ta.engine_id' => $arr['corr_engine_ctx'],
            ];

            $join = 'LEFT JOIN alarm_kingdoms ki ON ta.kingdom = ki.id LEFT JOIN alarm_categories ca ON ta.category = ca.id';
            $result =  $this->table('alarm_taxonomy')->alias('ta')->join($join)->where($where)->getField('CONCAT(ki.name,":",ca.name) category');
  
            return key($result);
        } 
}

?>
