<?php

use App\Models\Host;
use App\Models\Log;
use App\Models\HostTypes;
use App\Models\DeviceTypes;
use App\Models\AssetOsTpye;
use App\Models\Userreference;
use App\Models\User;
use App\Models\Import;
 // ini_set('display_errors', 1);
 // ini_set('display_startup_errors', 1);

class HostController extends Base
{
    protected $model = null;
    protected $config = null;
    protected $uid = null;
    protected $rid = null;
    protected $role = null;
 
    public function init()
    {  
        parent::init();
        
        $this->config = \Yaf\Registry::get("config");
        $this->model = new Host();
        $this->uid = $_SESSION['uid'];
        $this->rid = $_SESSION['rid'];
        $this->role = [1, 2, 3];//系统内置角色
         
    }
    //资产列表
    public function querylistAction()
    {
        $is_export          = input('get.is_export',1);  // 1 不处理    2 输出Excel
        if ($is_export == 2) {
            set_time_limit(300);//导出时五分钟超时
            $datalist = $this->model->getAll();
//            echo "<pre>";
//           var_dump($datalist);die;
             self::exportexcelquerylist($datalist['list']);
            //jsonResult($datalist);
        }else{
            $page                = input('page',1);
            $pagesize            = input('page_size',10);
        }

        $where = self::prev();
        $datalist = $this->model->getList($where,$page,$pagesize);
        jsonResult($datalist);
    }


    /**
     * 上传文件
     */
    public function uploadAction(){
        set_time_limit(300);
        $upload = new spUploadFile();
        if(!Tools::isEmpty($_FILES['file'])){
            $upload->upload_file($_FILES['file'],"xls|xlsx|csv",'excel/');
            if($upload->errmsg==''){


                $Import = new Import();

                $Import->index($upload->uploaded,'check');
                $this->logger(6);
                jsonResult(
                    [
                        'data'=>$Import->error,
                        'file' => $upload->uploaded,
                        'total_num' => $Import->allRows,
                    ]
                    ,"上传文件成功");

            }else{
                $this->logger(7);
                jsonError($upload->errmsg);
            }
        }else{
            jsonError('无上传文件');
        }
    }




    /**
     * 解析文件
     */
    public function importAction(){
        set_time_limit(300);
        $file = input('post.file','');
        if($file<>'' && file_exists($file)){
            $Import = new Import();
            $Import->index($file,'save');
            jsonResult(
                [
                    $Import->error
                ]
                ,"导入文件成功");
        }else{
            jsonError('无文件路径或文件不存在');
        }
    }




    /**
     * @abstract 资产数据导出   [这里因为服务器用的是PHP7.2版本 需要修改兼容性的问题(已经解决)]
     * @author   王晓辉
     * @param    和查询列表一样 查询什么出什么
     * @return   .xlsx
     */

    private function exportexcelquerylist($list){

        //设置超时时间  5 Minute
        set_time_limit(300);

        //初始化Excel
        $obj = new PHPExcel();
        //引入初始Sheet
        $obj->getActiveSheetIndex(0);

        //初始化原始数据
        $fileName = '用户资产数据表'.date('Y-m-d%20H:i:s');

        // 初始化表头
        $arr = ['1'=>['资产名称','IP','MAC','资产价值','告警数量','资产类型','资产系统','经度','纬度' ]];

        //数据整合
        foreach ($list as $k => $v) {
            switch ($v['alert']) {
                case '0':
                    $v['alert'] = '无';
                    break;
                default:
                    $v['alert'] = '有';
                    break;
            }
            switch ($v['asset']) {
                case '1':
                    $v['asset'] = '低';
                    break;
                case '2':
                    $v['asset'] = '中';
                    break;
                case '3':
                    $v['asset'] = '高';
                    break;
                default:
                    break;
            }
            if($v['os'] == ''){
                 $v['os'] = '未知';
            }
            if($v['type'] == ':'){
                $v['type'] = '未知';
            }

            $v['mac'] = substr($v['mac'], 0, 2) . '-'.substr($v['mac'], 2, 2) .'-'. substr($v['mac'], 4, 2) . '-'.substr($v['mac'], 6, 2) . '-'.substr($v['mac'], 8, 2). '-'.substr($v['mac'], 10, 2);


            $arr[$k+2] = [
                $v['hostname'],$v['ip'],$v['mac'],$v['asset'],$v['alert'],$v['type'], $v['os'],$v['lon'],$v['lat']
            ];
        }

        //初始化行
        $list = ['A','B','C','D','E','F','G','H','I','J','K','L','M','N','O','P','Q','R','S','T','U','V','W','X','Y','Z'];
        //获取长度
        $j_length = count($arr[1]);
        $i_length = count($arr);
        //数据循环导入
        for ($i=1; $i <= $i_length; $i++) {

            for ($j=0; $j < $j_length; $j++) {

                $obj->getActiveSheet()->setCellValue($list[$j].$i,$arr[$i][$j]);
            }
        }

        // 设置当前sheet的名称
        $obj->getActiveSheet()->setTitle('资产列表');

        //设置长度
        $obj->getActiveSheet()->getColumnDimension('A')->setWidth(120);
        $obj->getActiveSheet()->getColumnDimension('B')->setWidth(80);
        $obj->getActiveSheet()->getColumnDimension('C')->setWidth(30);
        $obj->getActiveSheet()->getColumnDimension('D')->setWidth(20);
        $obj->getActiveSheet()->getColumnDimension('E')->setWidth(15);
        $obj->getActiveSheet()->getColumnDimension('F')->setWidth(30);
        $obj->getActiveSheet()->getColumnDimension('G')->setWidth(30);
        $obj->getActiveSheet()->getColumnDimension('H')->setWidth(40);
        $obj->getActiveSheet()->getColumnDimension('L')->setWidth(90);
        //清楚ob 缓存 防止乱码
        ob_clean();
        //建立输出区
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . $fileName . '.xlsx');
        header('Cache-Control: max-age=1');
        $objWriter = \PHPExcel_IOFactory::createWriter($obj, 'Excel2007');
        ob_end_clean();
        //输出结果
        $objWriter->save('php://output');
        $this->logger(5);
        exit;
    }



    //资产详情
    public function detailAction()
    {  
        $where['id'] = input('id/s','');//32位
       
        if(Tools::isEmpty($where['id'])){
            jsonError('无效的参数:id');
        }
        $datalist = $this->model->getOne($where);
        jsonResult($datalist);
    }

    /***
    * 前置方法，取出查询的唯一字段
    */
    public function prev(){
        $request = input('post.');
        unset($request['page'],$request['page_size']);

        //时间范围
        if(!\Tools::isEmpty($request['begindate']) and !\Tools::isEmpty($request['enddate'])){
            $where['a.created'] = array(array('egt',$request['begindate']),array('elt',$request['enddate']));
            unset($request['begindate'],$request['enddate']);
        }
        //判断IP
        if(isset($request['ip'])){
            if(filter_var($request['ip'],FILTER_VALIDATE_IP)){
                $where['b.ip'] = ['exp',"=inet6_aton('{$request["ip"]}')"];
                unset($request['ip']);
            }
        }
        //资产种类判断
        if(isset($request['type'])){
            if(stripos($request['type'],':')){
                $type = explode(':',$request['type'])[0];
            }else{
                $type = $request['type'];
            }
            $DeviceTypes = new DeviceTypes();
            $tid = $DeviceTypes->getId(['name' => ['like',"%{$type}%"]]);
            if(!Tools::isEmpty($tid)){
                $host_ids = (new HostTypes())->getHostId(
                    ['type|subtype' => $tid]
                );
                if(!Tools::isEmpty($host_ids)){

                    $host_id_str = '';
                    array_walk($host_ids,function(&$v) use(&$host_id_str){
                        $host_id_str .= 'unhex("'.$v.'"),';
                    });

                    $where['a.id'] = ['exp','in('.rtrim($host_id_str,',').')'];
                }
            }

            unset($request['type']);
        }


        //根据ID搜索
        if(isset($request['id'])){
            $where['a.id'] = ['exp',"=unhex('{$request["id"]}')"];
            unset($request['id']);
        }
        //资产名称
        if(isset($request['hostname'])){
            $where['a.hostname'] = ['like',"%{$request["hostname"]}%"];
            unset($request['hostname']);
        }
        //mac地址
        if(isset($request['mac'])){
            $where['b.mac'] = ['exp',"=unhex('{$request["mac"]}')"];
            unset($request['mac']);
        }

        //默认等于匹配
        if(!Tools::isEmpty($request)){
            if(\Tools::is_single_array($request)){
                $where['a.'.key($request)] = current($request);
            }else{
                foreach ($request as $k => $v) {
                    $where['a.'.$k] = $v;
                }
            }        
        }


        return $where;
    }


    /**
     * @abstract 获取资产类型
     */
    public function typeAction()
    {
        $DeviceTypes = new DeviceTypes(); 
        $datalist = $DeviceTypes->tree();
        jsonResult($datalist);
    }

    //获取操作系统
    public function osAction()
    {
        $keyword = input('keyword');
        $where = [];
        if(!empty($keyword)){
            $where['a.asset_os_name'] = array('like', "%{$keyword}%");
        }
        $asset_os_type = new AssetOsTpye();
        $datalist = $asset_os_type->where($where)->select();
        jsonResult($datalist);
    }

    /**
     * @abstract
     */
    public function saveAction(){
        //接值
        $data['id'] = input('post.id/s','');//32位
        $data['ctx'] = input('post.ctx/s',$this->config->application->ctx);
        $data['hostname'] = input('post.hostname/s','');
        $data['asset'] = input('post.asset/d',2);
        $data['fqdns'] = input('post.fqdns/s','');
        $data['alert'] = input('post.alert/d',0);
        $data['persistence'] = input('post.persistence/d',0);
        $data['nat'] = input('post.nat/s',0);
        $data['rrd_profile'] = input('post.rrd_profile/s',0);
        $data['descr'] = input('post.descr/s','');
        $data['lat'] = input('post.lat/f',0);
        $data['lon'] = input('post.lon/f',0);
        $data['icon'] = input('post.icon','');
        $data['country'] = input('post.country/s','');
        $data['external_host'] = input('post.external_host/d',0);
        $data['ip'] = input('post.ip/s','');
        $data['mac'] = input('post.mac/s','');
        $data['interface'] = input('post.interface/s','');
        $data['type'] = input('post.type/a',0);
        $data['subtype'] = input('post.subtype/d',0);
        $data['os'] = input('post.os/s','');


        //字符串小写转大写
        $data['mac'] = strtoupper(str_replace('-','',$data['mac']));

        if(Tools::isEmpty($data['id'])){
            $data['id'] = uuid();
        }
        if(Tools::isEmpty($data['lat'])){
            $data['lat'] = 0;
        }
        if(Tools::isEmpty($data['lon'])){
            $data['lon'] = 0;
        }
        if(Tools::isEmpty($data['external_host'])){
            $data['external_host'] = 0;
        }
        if(Tools::isEmpty($data['hostname'])){
            jsonError('资产名称不允许为空');
        }
        if(Tools::isEmpty($data['ip']) OR !filter_var($data['ip'],FILTER_VALIDATE_IP)){
            jsonError('无效的参数:资产ip');
        }
        $result = $this->model->saveHost($data);

        if(intval($result) > 0){
            
            $this->logger(3);

            jsonResult([],'保存资产成功');
        }else{
            $this->logger(4);
            jsonError('保存资产失败');
        }
     
    }

    /**
    * 删除资产
    */
    public function destroyAction(){
        $ids = input('post.id/s',0);//32位
        if(empty($ids)) jsonError('ID参数缺失');
        $result = $this->model->delHost($ids);

        if(intval($result) > 0){
            $this->logger(1);
            jsonResult([],'删除资产成功');
        }else{
            $this->logger(2);
            jsonError('删除资产失败');
        }
    }

//    public function demo1Action()
//    {
//      $arr = [
//          [1,2,3],
//          [1,2,3],
//          [1,2,3,4],
//          [2,6],
//          [5],
//          [5],
//          [1,6,9]
//      ];
//
//      foreach ($arr as $key => $val){
//          $arr[$key] = implode(',',$val);
//      }
//        foreach ($arr as $ky => $value){
//            $data[$value]['num'] = 0;
//            foreach ($arr as $kk => $vv){
//                if($value == $vv){
//                    $data[$value]['arr'] = $value;
//                    $data[$value]['num'] ++ ;
//                }
//            }
//        }
//
//        $data = array_values($data);
//
//        foreach ($data as $k => $v){
//            for ($i = 0 ; $i < $v['num'];$i++){
//                   $datas[] = explode(',',$v['arr']);
//            }
//        }
//
//
//
//        jsonResult($datas);
//    }

}