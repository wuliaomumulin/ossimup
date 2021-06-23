<?php
use App\Models\EventChart;

class EventchartController extends Base
{
    protected $eventChart;

    public function init()
    {
        parent::init();
        $this->eventChart = new Eventchart();
    }


    public function getEventChartAction()
    {
        $data = $this->eventChart->getEventChart();
        $data['value'] = $_SERVER['REQUEST_SCHEME'].'://'.$_SERVER['SERVER_ADDR'].$data['value'];
        jsonResult($data);
    }
}
?>