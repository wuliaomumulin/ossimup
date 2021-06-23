<?php

namespace App\Models;

/** 拓扑节点 */
class TopologyEdge extends Model
{
    protected $tableName = 'edge';
    protected $tablePrefix = 'topology_';
    protected $pk = 'id';

    protected $status = 'if(update_time>date_sub(now(),interval 2 day),1,0) `status`';


    public function getAll($where=[]){
        return $this->where($where)->select();
    }
    /**
	* 获取符合条件线条和连接状态,为了做连线匹配
    */
    public function links($where=[]){
        $join = 'topology_node b on a.device=b.ip ';
    	$result = $this->field('a.src,a.dst,'.$this->status)->alias('a')->join($join,'INNER')->where($where)->select();
        
/*        $TopologyNode = new TopologyNode();
        if (!\Tools::isEmpty($result)) {
            array_walk($result, function (&$arr) use($TopologyNode) {
                $middle = $TopologyNode->where(['ip'=>$arr['src']])->getField('id');
                $arr['src'] = is_null($middle) ? '' : $middle;
                $middle = $TopologyNode->where(['ip'=>$arr['dst']])->getField('id');
                $arr['dst'] = is_null($middle) ? '' : $middle;
            });
        }*/

        return $result;        

    }
}