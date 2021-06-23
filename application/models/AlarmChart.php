<?php
namespace App\Models;

class AlarmChart extends Model
{
    protected $tableName = 'config';
    protected $tablePrefix = '';
    protected $pk = '';

    public function getAlarmChart()
    {
        $data = $this->field('value')->where(['conf' => 'alarm_chart'])->find();

        return $data;
    }
}
?>