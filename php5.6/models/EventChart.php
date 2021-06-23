<?php
namespace App\Models;

class EventChart extends Model
{
    protected $tableName = 'config';
    protected $tablePrefix = '';
    protected $pk = '';

    public function getEventChart()
    {
        $data = $this->field('value')->where(['conf' => 'event_chart'])->find();

        return $data;
    }
}
?>