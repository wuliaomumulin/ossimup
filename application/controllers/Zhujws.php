<?php

use App\Models\Zhujws;
use App\Models\HostTypes;

/*
 * 主机卫士
 */

class ZhujwsController extends Base
{
    protected $model = null;
    protected $conf = './list_conf/Zhujws.json';
    protected $num = 43;

    public function init()
    {
        parent::init();
        $this->model = new Zhujws();
        $this->checkAuth($this->num);
    }

    public function getsysinfoAction()
    {
        $method = substr(__FUNCTION__, 0, -6);
        $ret = $this->model->$method($post);
        if ($ret) {
            echo $ret;
        } else {
            jsonResult();
        }
    }

    public function loginAction()
    {
        $method = substr(__FUNCTION__, 0, -6);
        echo $this->model->$method();
    }

    public function getUserInfoAction()
    {
        $method = substr(__FUNCTION__, 0, -6);
        $ret = $this->model->$method($post);
        if ($ret) {
            echo $ret;
        } else {
            jsonResult();
        }
    }

    public function getGroupListAction()
    {
        $method = substr(__FUNCTION__, 0, -6);
        $ret = $this->model->$method($post);
        if ($ret) {
            echo $ret;
        } else {
            jsonResult();
        }
    }

    /**
     * 批量图形接口
     */
    public function getSafetyProfileAction()
    {
        $method = substr(__FUNCTION__, 0, -6);
        $ret = $this->model->$method($post);
        if ($ret) {
            echo $ret;
        } else {
            jsonResult();
        }
    }

    public function getSafetyStatisticsAction()
    {
        $method = substr(__FUNCTION__, 0, -6);
        $ret = $this->model->$method($post);
        if ($ret) {
            echo $ret;
        } else {
            jsonResult();
        }
    }

    public function getSafetyAnalysisAction()
    {
        $method = substr(__FUNCTION__, 0, -6);
        $ret = $this->model->$method($post);
        if ($ret) {
            echo $ret;
        } else {
            jsonResult();
        }
    }

    public function getSafetyTrendAction()
    {
        $method = substr(__FUNCTION__, 0, -6);
        $ret = $this->model->$method($post);
        if ($ret) {
            echo $ret;
        } else {
            jsonResult();
        }
    }

    public function getHostModuleAction()
    {

        $post['hid'] = input('post.hid/s', '');

        $method = substr(__FUNCTION__, 0, -6);
        $ret = $this->model->$method($post);
        if ($ret) {
            echo $ret;
        } else {
            jsonError();
        }
    }

    public function setHostModuleAction()
    {

        $post['hid'] = input('post.hid/s', '');
        $post['state'] = input('post.state/d', 0);
        $post['mid'] = input('post.mid/d', 1);

        $method = substr(__FUNCTION__, 0, -6);

        $ret = $this->model->$method($post);
        if ($ret) {
            echo $ret;
        } else {
            jsonError();
        }
    }

    /**
     * 节点模块
     */
    public function getHostListAction()
    {

        $post['gid'] = input('post.gid/d', 0);
        $post['tid'] = input('post.tid/s', null);
        $post['status'] = input('post.status/d', '1');
        $post['online_state'] = input('post.online_state/d', '');
        $post['search'] = input('post.search/s', '');
        $post['host_id'] = input('post.host_id/s', '');
        $post['page'] = input('post.page/d', 1);
        $post['limit'] = input('post.pagesize/d', 10);
        $post['sort'] = input('post.sort/a', (object)[]);



        $method = $this->getRequest()->getActionName();
        $ret = $this->model->$method($post);

        jsonResult($ret);

    }

    public function getHostInfoAction()
    {

        $post['hid'] = input('post.hid/s', '');
        $method = substr(__FUNCTION__, 0, -6);
        $ret = $this->model->$method($post);
        if ($ret) {
            echo $ret;
        } else {
            jsonResult();
        }
    }

    public function gerAssetStatAction()
    {

        $post['hid'] = input('post.hid/s', '');

        $method = substr(__FUNCTION__, 0, -6);
        $ret = $this->model->$method($post);
        if ($ret) {
            echo $ret;
        } else {
            jsonResult();
        }
    }

    public function ratifyHostInfoAction()
    {

        $post['hid'] = input('post.hid/s', '');

        $method = substr(__FUNCTION__, 0, -6);
        $ret = $this->model->$method($post);
        if ($ret) {
            echo $ret;
        } else {
            jsonResult();
        }
    }

    public function editHostInfoAction()
    {

        $post['hid'] = input('post.hid/s', '');
        $post['hostname'] = input('post.hostname/s', '');
        $post['risk_rank'] = input('post.risk_rank/s', '-1');
        $post['gid'] = input('post.gid/s', '1');
        $post['tags'] = input('post.tags/s', '');

        $method = substr(__FUNCTION__, 0, -6);
        $ret = $this->model->$method($post);
        if ($ret) {
            echo $ret;
        } else {
            jsonResult();
        }
    }

    public function delUnauthHostAction()
    {

        $post['hid'] = input('post.hid/s', '');

        $method = substr(__FUNCTION__, 0, -6);
        $ret = $this->model->$method($post);
        if ($ret) {
            echo $ret;
        } else {
            jsonResult();
        }
    }

    public function delHostInfoAction()
    {

        $post['hid'] = input('post.hid/s', '');

        $method = substr(__FUNCTION__, 0, -6);
        $ret = $this->model->$method($post);
        if ($ret) {
            echo $ret;
        } else {
            jsonResult();
        }
    }

    public function getHostSafetyStatisticsAction()
    {

        $post['hid'] = input('post.hid/s', '');

        $method = substr(__FUNCTION__, 0, -6);
        $ret = $this->model->$method($post);
        if ($ret) {
            echo $ret;
        } else {
            jsonResult();
        }
    }

    public function getHostSafetyAnalysisAction()
    {

        $post['hid'] = input('post.hid/s', '');

        $method = substr(__FUNCTION__, 0, -6);
        $ret = $this->model->$method($post);
        if ($ret) {
            echo $ret;
        } else {
            jsonResult();
        }
    }

    public function getHostScoreAction()
    {

        $post['hid'] = input('post.hid/s', '');

        $method = substr(__FUNCTION__, 0, -6);
        $ret = $this->model->$method($post);
        if ($ret) {
            echo $ret;
        } else {
            jsonResult();
        }
    }


    public function getAuditLogListAction()
    {

        $post['type'] = input('post.type/d', 0);
        $post['page'] = input('post.page/d', 1);
        $post['limit'] = input('post.limit/d', 10);
        $post['host_id'] = input('post.host_id/s', '');
        $post['keyword'] = input('post.keyword/s', '');
        $post['start_time'] = input('post.start_time/d', 0);
        $post['end_time'] = input('post.end_time/d', 0);

        $method = substr(__FUNCTION__, 0, -6);
        $ret = $this->model->$method($post);
        if ($ret) {
            echo $ret;
        } else {
            jsonResult();
        }
    }

    public function getSysAuditAction()
    {

        $post['page'] = input('post.page/d', 1);
        $post['limit'] = input('post.limit/d', 10);
        $post['keyword'] = input('post.keyword/s', '');
        $post['start_time'] = input('post.start_time/d', 0);
        $post['end_time'] = input('post.end_time/d', 0);

        $method = substr(__FUNCTION__, 0, -6);
        $ret = $this->model->$method($post);
        if ($ret) {
            echo $ret;
        } else {
            jsonResult();
        }
    }

    public function getSystemPolicyAction()
    {

        $post['hid'] = input('post.hid/s', '');

        $method = substr(__FUNCTION__, 0, -6);
        $ret = $this->model->$method($post);
        if ($ret) {
            $ret = json_decode($ret, true);
            $ret['rulelist'] = array_reverse($ret['rulelist']);

            echo json_encode($ret);
        } else {
            jsonResult();
        }
    }

    public function editSystemPolicyAction()
    {

        $post['hid'] = input('post.hid/s', '');
        $post['policy_type'] = input('post.policy_type/s', '');
        $post['policy_value'] = input('post.policy_value/d', 0);

        $method = substr(__FUNCTION__, 0, -6);
        $ret = $this->model->$method($post);
        if ($ret) {
            echo $ret;
        } else {
            jsonResult();
        }
    }

    public function getDeviceTypeAction()
    {

        $method = $this->getRequest()->getActionName();

        $ret = $this->model->$method($post);
        if ($ret) {
            jsonResult($ret);
        } else {
            jsonResult();
        }
    }

    public function demoAction()
    {
        echo $_SESSION["user_monitor_power"];
    }
}