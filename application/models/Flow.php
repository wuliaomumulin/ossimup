<?php
namespace App\Models;

class Flow extends Model
{
    protected $tableName = 'config';
    protected $tablePrefix = '';
    protected $pk = '';

    public function getFlowActiveChart()
    {
        $data = $this->field('value')->where(['conf' => 'flow_active_chart'])->find();

        return $data;
    }

    public function getFlowEventChart()
    {
        $data = $this->field('value')->where(['conf' => 'flow_event_chart'])->find();

        return $data;
    }

    public function getFlowStatusChart()
    {
        $data = $this->field('value')->where(['conf' => 'flow_status_chart'])->find();

        return $data;
    }
}
?>