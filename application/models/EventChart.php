<?php
namespace App\Models;

class EventChart extends Model
{
    protected $tableName = 'config';
    protected $tablePrefix = '';
    protected $pk = '';

    public function getEventChart($chart='')
    {
        $data = $this->field('value')->where(['conf' => $chart])->find();

        return $data;
    }
}
?>