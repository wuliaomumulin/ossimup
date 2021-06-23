<?php
use App\Models\SystemNews;

class SystemnewsController extends Base
{
	protected $model = null;
    protected $uid = null;
    public function init()
    {
        parent::init();
        $this->model = new SystemNews();
        $this->uid = $_SESSION['uid'];
    }

    /**
     * @abstract 获取当前用户 全部未读的消息条数
     * @return [type] [description]
     */
    public function getUnreadDataCountAction(){

        $rs = $this->model->where("is_read = 0 AND uid = ".$this->uid)->count(); 
        jsonResult($rs);
    }

    /**
     * @abstract 获取当前用户消息
     * @return [type] [description]
     */
    public function getDataListAction(){

        $page = input('get.page/d',1);
        $page_size = input('get.page_size/d',10);

        $read  = input("get.is_read",'');

        //xss过滤事件
        $read = $this->model->xss_filter($read);

        if (in_array($read, ['0','1'])) { 
            $where['is_read'] = ['eq',$read];
        }

        $where['uid'] = ['eq',$this->uid];

        $data['total_num'] = strval($this->model->where($where)->count()); 
        $data['total_page'] = strval(ceil($data['total_num']/$page_size));
        if ($data['total_page'] < 1 || $page > $data['total_page']) {

           $data['list'] = [];
        }else{

            $data['list'] = $this->model->field("id,title,value,is_read,ctime")->where($where)->order("is_read asc ,id desc")->page($page,$page_size)->select();
        }
        jsonResult($data);
    }

    /**
     * @abstract 标记已读消息
     * @return [type] [description]
     */
    public function signReadAction(){

        $id = input("get.id",'');

        !$id && jsonError('无效的参数');

        $where['uid'] = ['eq',$this->uid];
        $where['id']  = ['in',explode(',', $id)];

        $save['is_read'] = 1;
        $save['utime']   = getmicrotime();
        $rs = $this->model->where($where)->save($save);

        jsonResult($rs);
    } 

    /**
     * @abstract 全部标记已读消息
     * @return [type] [description]
     */
    public function signReadAllAction(){

        $where['uid'] = ['eq',$this->uid];
        $where['is_read']  = ['eq',0];

        $save['is_read'] = 1;
        $save['utime']   = getmicrotime();
        $rs = $this->model->where($where)->save($save);

        jsonResult($rs);
    }   
}

