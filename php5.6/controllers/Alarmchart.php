<?php
use App\Models\AlarmChart;

class AlarmchartController extends Base
{
    protected $alarmChart;

    public function init()
    {
        parent::init();
        $this->alarmChart = new Alarmchart();
    }


    public function getAlarmChartAction()
    {

        $data = $this->alarmChart->getAlarmChart();
        $data['value'] = $_SERVER['REQUEST_SCHEME'].'://'.$_SERVER['SERVER_ADDR'].$data['value'];
        jsonResult($data);
    }
}
?>