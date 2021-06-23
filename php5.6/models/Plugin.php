<?php

use App\Models\Properties;
use App\Models\PluginSid;

namespace App\Models;

class Plugin extends Model
{
    protected $tableName = 'plugin';
    protected $tablePrefix = '';
    protected $pk = 'id';

    //自动完成
    protected $_auto = array();

    /**
     * 获取所有
     */

    public function getAll($where = [], $order = 'a.id desc')
    {
        //field
        $field = 'a.id,a.type,a.name,a.description,a.product_type,a.vendor,c.name gname';
        $join1 = 'plugin_group_descr b on a.id=b.plugin_id';
        $join2 = 'plugin_group c on b.group_id=c.group_id';
        $result['list'] = $this->field($field)->alias('a')->join($join1)->join($join2)->where($where)->group($group)->order($order)->select();
        $result['total_num'] = $this->field($field)->alias('a')->join($join)->join($join2)->where($where)->count();
        return $result;
    }

    /**
     * 获取列表
     */

    public function getList($where = [], $page, $pagesize, $order = 'a.id desc')
    {
        //配置config
        // $result['config'] = [
        //     ["key" => "id", "description" => "插件ID"],
        //     ["key" => "name", "description" => "名称"],
        //     ["key" => "description", "description" => "描述"],
        //     ["key" => "product_type_sname", "description" => "产品类别"],
        //     ["key" => "vendor", "description" => "供应商"],
        //     // ["key" =>"type","description"=>"类型"],
        //     // ["key" =>"sname","description"=>"分类名称"],
        // ];

        //field
        $field = 'a.id,a.type,a.name,a.description,a.product_type,a.vendor,d.name product_type_sname';
        //$join1 = 'plugin_group_descr b on a.id=b.plugin_id';
        //$join2 = 'plugin_group c on b.group_id=c.group_id';
        $join3 = 'category d on a.product_type=d.id';

        $result['list'] = $this->field($field)->alias('a')->join($join1, 'LEFT')->join($join3, 'LEFT')->where($where)->order($order)->page($page, $pagesize)->select();
        //echo $this->getlastsql();exit();
        $result['total_num'] = $this->alias('a')->join($join1, 'LEFT')->join($join2, 'LEFT')->join($join3, 'LEFT')->where($where)->count();
        $type = $this->getType();

        if (!empty($result['list'])) {
            foreach ($result['list'] as $k => $v) {
                $result['list'][$k]['type'] = $type[$v['type']];
            }
        }

        return $result;
    }

    /**
     * 获取设备类型
     */
    public function setType($host_id)
    {
        //$sql = "select hex(a.host_id),b.type from host_types a,(select a.id,b.id pid,concat(b.name,':',a.name) type from device_types a inner join device_types b on a.class = b.id) b where a.subtype = b.id or a.type = b.id and ?";
        $where['host_id'] = ['exp', "=unhex('{$host_id}')"];
        $subQuery = $this->table('device_types a')->join('join device_types b on a.class = b.id')->field("a.id,b.id pid,concat(b.name,':',a.name) types")->buildSql();
        $res = $this->table('host_types a,' . $subQuery . ' b')->where($where)->field('hex(a.host_id) host_id,b.type')->getField('types');
        return $res;
    }

    /**
     * 获取一个操作系统
     * return 大类:小类
     */
    public function setOs($host_id)
    {
        //$sql = select ifnull(( SELECT concat(b.description,':',a.value) os FROM host_properties a INNER JOIN host_property_reference b on a.property_ref = b.id WHERE a.host_id =unhex('20B3D323E69273FCDFB435A13BB7B39B') ),'') os;
        $where['a.host_id'] = ['exp', "=unhex('{$host_id}')"];
        $subQuery = $this->table('host_properties a')->join('host_property_reference b on a.property_ref = b.id')->where($where)->field("concat(b.description,':',a.value) os")->buildSql();
        $result = $this->query("select ifnull({$subQuery},'') os");
        /*if($result === false){
            return $this->getlastsql();
        }*/
        return is_array($result) ? $result[0]['os'] : $result;
    }

    /**
     * 获取agent
     */
    public function setAgent($host_id)
    {
        $where['a.host_id'] = ['exp', "=unhex('{$host_id}')"];
        $result = $this->table('hids_agents a')->field('agent_name,agent_ip')->where($where)->find();
        //return $this->table('hids_agents a')->getlastsql();
        return $result;
    }

    /**
     * 用于解析ID
     */
    public function getId()
    {
        $id = input('id');

    }

    public function getType()
    {
        $arr = ['1' => 'Detector (1)', '2' => 'Monitor (2)', '3' => 'Other (3)'];
        return $arr;
    }

    public function delPlugin($id)
    {

        if (!is_numeric($id) || $id < 0) jsonError('无效的参数');

        $this->startTrans();

        try {
            //删除主表的数据
            $rs = $this->where(['id' => ['eq', $id]])->delete();
            //删除分表
            (new PluginSid())->where(['plugin_id' => ['eq', $id]])->delete();

            $this->commit();

            return '删除成功';

        } catch (Exception $e) {
            $this->rollback();

            return '删除失败';
        }

    }

    /**
     * @return array
     */
    public function getPluginType()
    {
        return $this->table('alienvault.category')->field('id,name')->select();

    }

    public function saveData($param)
    {
        if ($param['edit'] == 0) {
            unset($param['edit']);

            $res = $this->add($param);

        } elseif ($param['edit'] == 1) {
            unset($param['edit']);
            $id = $param['ID'];
            unset($param['ID']);
            unset($param['type']);
            unset($param['product_type_sname']);
            $res = $this->where("id = {$id}")->save($param);

        }


        if (empty($res)) {
            return 0;
        }
        return 1;
    }

    public function delPlugins($param)
    {
        $res = $this->where("id = {$param}")->delete();
        if (empty($res)) {
            return 0;
        }
        return 1;
    }

}