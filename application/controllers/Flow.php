<?php
use App\Models\Flow;
//ini_set('display_errors', 1);
//ini_set('display_startup_errors', 1);
class FlowController extends Base
{
    protected $flow;

    public function init()
    {
        parent::init();
        $this->flow = new Flow();
    }

    public function flow_statusAction()
    {
        $data = $this->flow->getFlowStatusChart();
        $data['value'] = $_SERVER['REQUEST_SCHEME'].'://'.$_SERVER['SERVER_ADDR'].$data['value'];
        jsonResult($data);

    }

    public function flow_eventAction()
    {
        $data = $this->flow->getFlowEventChart();
        $data['value'] = $_SERVER['REQUEST_SCHEME'].'://'.$_SERVER['SERVER_ADDR'].$data['value'];
        jsonResult($data);
    }

    public function flow_activeAction()
    {
        $data = $this->flow->getFlowActiveChart();
        $data['value'] = $_SERVER['REQUEST_SCHEME'].'://'.$_SERVER['SERVER_ADDR'].$data['value'];
        jsonResult($data);
    }
}