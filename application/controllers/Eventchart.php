<?php
use App\Models\EventChart;

class EventchartController extends Base
{
    protected $eventChart;
    protected $num = 13;

    public function init()
    {
        parent::init();
        $this->eventChart = new Eventchart();
        $this->checkAuth($this->num);
    }


    public function getEventChartAction()
    {
        $chart = input('post.chart','');
        if(empty($chart)) jsonError('缺少参数');
        $data = $this->eventChart->getEventChart($chart);
        $data['value'] = $_SERVER['REQUEST_SCHEME'].'://'.$_SERVER['SERVER_ADDR'].$data['value'];
        jsonResult($data);
    }
}
?>