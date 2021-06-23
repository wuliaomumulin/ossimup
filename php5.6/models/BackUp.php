<?php
namespace App\Models;


class BackUp extends Model
{

    protected $dbName = 'alienvault';
    protected $tableName = 'config';
    protected $tablePrefix = '';
    protected $pk = '';

    public function getNowHaveLog()
    {
        $query = "SELECT DISTINCT DATE(timestamp) as day FROM alienvault_siem.acid_event ORDER BY day DESC";
        $rs = $this->query($query);

//        $result = $this->getBackDir();
//        $backup_dir = $result['backup_dir'];
//
//        foreach ($rs as $k => $v) {
//
//            $v["day"] = str_replace('-', '', $v["day"]);
//
//            if (file_exists($backup_dir . "/delete-" . $v["day"] . ".sql.gz")) {
//
//                $size = filesize($backup_dir . "/delete-" . $v["day"] . ".sql.gz");
//                $sz = self::fileSize($size);
//
//                $backup[$k]['day'] = $rs[$k]["day"];
//                $backup[$k]['size'] = $sz;
//
//            }
//
//        }

        return $rs;

    }

    public function getNowHaveAudit()
    {
        $query = "SELECT DISTINCT DATE(insert_time) as day FROM alienvault.sys_log ORDER BY day DESC";
        $rs = $this->query($query);
        return $rs;
    }

    public function fileSize($size)
    {
        $unit = array('B', 'KB', 'MB', 'GB', 'TB', 'PB');
        return @round($size / pow(1024, ($i = floor(log($size, 1024)))), 2) . '' . $unit[$i];
    }

    public function getRestore()
    {
        $backup = self::getNowHaveLog();

        $result = $this->getBackDir();
        $dir = dir($result['backup_dir']);

        //var_dump($dir);die;
        $executing = [];
        while ($file = $dir->read()) {

            if (preg_match("/^insert\-(.+)\.sql\.gz/", $file, $found)) {
                if (!in_array($found[1], $backup) && !$executing[$found[1]]) {
                    $insert[] = $found[1];
                }
            }

        }
        rsort($insert);
        $dir->close();


        foreach ($insert as $k => $v) {

            $rs[$k]['day'] = substr($v, 0, 4) . '-' . substr($v, 4, 2) . '-' . substr($v, -2);

            $size = filesize($result['backup_dir'] . "/insert-" . $v . ".sql.gz");
            $sz = self::fileSize($size);
            $rs[$k]['size'] = $sz;

        }
        return $rs;
    }


    public function getBackListCount()
    {
        $where = ['log_event' => ['like',"%安全事件%"],
        ];
        $data = $this->table('sys_log')->where($where)->count("id");
        return $data;
    }

    public function getBackList($page,$page_size)
    {

        $where = ['log_event' => ['like',"%安全事件%"],
        ];
        $data = $this->table('sys_log')->where($where)->order('insert_time desc')->page($page,$page_size)->select();

        foreach ($data as $k => &$v){
            if($v['remark'] == '成功'){
                $v['status'] = 2;
                $v['percent'] = 100;
            }else{
                $v['status'] = 1;
                $v['percent'] = 0;
            }
        }
        //echo $this->getlastsql();die;
        return $data;
    }

    public function getAuditListCount()
    {
        $where = ['log_event' => ['like',"%审计日志%"],
        ];
        $data = $this->table('sys_log')->where($where)->count("id");
        return $data;
    }

    public function getAuditList($page,$page_size)
    {
        $where = ['log_event' => ['like',"%审计日志%"],
        ];
        $data = $this->table('sys_log')->where($where)->order('insert_time desc')->page($page,$page_size)->select();
        foreach ($data as $k => &$v){
            if($v['remark'] == '成功'){
                $v['status'] = 2;
                $v['percent'] = 100;
            }else{
                $v['status'] = 1;
                $v['percent'] = 0;
            }
        }
        // echo $this->getlastsql();die;
        return $data;

    }


    public function restore($dates_list)
    {
        //  $dates_list = ($dates_list != '') ? explode(',', $dates_list) : array();

//        if ($filter_by = 'undefined') {
//            $filter_by = '';
//        }

        if (count($dates_list) > 0) {  // echo $filter_by;die;
            $launch_status = self::Insert($dates_list);

            if ($launch_status['status'] > 0) {
                $response['status'] = 'success';
                $response['message'] = _('正在插入事件…');
                $response['info'] = $launch_status['info'];
            } else {
                $response['status'] = 'error';
                $response['message'] = _('对不起，由于还原事件时出错，操作未完成');
                $response['info'] = $launch_status['info'];
            }
        } else {
            $response['status'] = 'error';
            $response['message'] = _('请选择要还原的日期');
        }

        return $response;
    }

    public function delete($dates_list = '')
    {
        //$dates_list = ($dates_list != '') ? explode(',', $dates_list[0]) : array();

        $query1 = "truncate table alienvault_siem.ac_acid_event";
        $query2 = "truncate table alienvault_siem.acid_event";
        $rs1 = $this->query($query1);
        $rs2 = $this->query($query2);
        if($rs1 !== false && $rs2 !== false){
            //self::insertRestoreDb("删除安全事件",2);
             $data['status'] = 1;
             $data['info'] = '删除安全事件';
        }else{
           // self::insertRestoreDb("删除安全事件",1);
             $data['status'] = 0;
             $data['info'] = '删除安全事件';
        }
        return $data;

//        if (count($dates_list) > 0) {
//
//            $launch_status = self::deleteInfo($dates_list);
//
//            if ($launch_status > 0) {
//                $response['status'] = 'success';
//                $response['message'] = _('正在清除事件…');
//            } else {
//                $response['status'] = 'error';
//                $response['message'] = _('对不起，由于清除事件时出错，操作没有完成');
//            }
//        } else {
//            $response['status'] = 'error';
//            $response['message'] = _('请选择要清除的日期');
//        }
//
//        return $response;
    }

    public function deleteAudit()
    {
        $query = "truncate table alienvault.sys_log";
        $rs = $this->query($query);
        if($rs !== false){
           // self::insertRestoreDb("删除审计日志",2);
            $data['status'] = 1;
            $data['info'] = '删除审计日志';
        }else{
           // self::insertRestoreDb("删除审计日志",1);
            $data['status'] = 0;
            $data['info'] = '删除审计日志';
        }
        return $data;
    }

    public function deleteInfo($delete)
    {
        if (!is_array($delete) || count($delete) < 1) {
            return;
        }

        $info = \Yaf\Registry::get("config");
        $dbhost = $info['database']['config']['host'];
        $dbuser = $info['database']['config']['usr'];
        $dbpass = $info['database']['config']['pwd'];

        $files = preg_replace("/(\d\d\d\d)(\d\d)(\d\d)/", "\\1-\\2-\\3", self::array2str($delete));

        $cmd = 'backup action="purge_events"  dates="' . $files . '" bbddhost="' . $dbhost . '" bbdduser="' . $dbuser . '" bbddpasswd="' . $dbpass . '"';
        $status = self::sendCommand($cmd);

        if ($status > 0) {
            //更改默认 >0 默认成功   其他全是失败
            self::insertRestoreDb("Delete events from $files",2);
        }else{
            self::insertRestoreDb("Delete events from $files",1);
        }

        return $status;
    }

    public function array2str($arr)
    {
        $str = '';
        if (is_array($arr)) {
            while (list($key, $value) = each($arr)) {
                if ($str == '') {
                    $str = $value;
                } else {
                    $str .= "," . $value;
                }
            }
        }

        return $str;
    }


    public function Insert($insert, $filter_by = '', $nomerge = "merge")
    {

        if (!is_array($insert) || count($insert) < 1) {
            return;
        }

        $info = \Yaf\Registry::get("config");
        $dbhost = $info['database']['config']['host'];
        $dbuser = $info['database']['config']['usr'];
        $dbpass = $info['database']['config']['pwd'];

        $first = preg_replace("/(\d\d\d\d)(\d\d)(\d\d)/", "\\1-\\2-\\3", $insert[count($insert) - 1]);

        //$last = (preg_match("/\d\d\d\d\d\d\d\d/", $insert[0])) ? preg_replace("/(\d\d\d\d)(\d\d)(\d\d)/", "\\1-\\2-\\3", $insert[0]) : $first;
        // $files = "$first-$last";
        $last =   preg_replace("/(\d\d\d\d)(\d\d)(\d\d)/", "\\1-\\2-\\3", $insert[0]) ? preg_replace("/(\d\d\d\d)(\d\d)(\d\d)/", "\\1-\\2-\\3", $insert[0]) : $first;
        if($first == $last){
            $files = "$first";
        }else{
            $files = "$first-$last";
        }

        $newdb = ($nomerge == "merge") ? "0" : "1";

        $cmd = 'backup action="backup_restore"  begin="' . $first . '" end="' . $last . '" entity="' . $filter_by . '" newbbdd="' . $newdb . '" bbddhost="' . $dbhost . '" bbdduser="' . $dbuser . '" bbddpasswd="' . $dbpass . '"';
        //echo $cmd;die;
        $status = self::sendCommand($cmd);

        $res['status'] = $status;
        $res['info'] = "还原安全事件： {$files} 的备份文件";
//        if ($status > 0) {
//            //更改默认 >0 默认成功   其他全是失败
//            self::insertRestoreDb("还原安全事件： {$files} 的备份文件",2);
//        }else{
//            self::insertRestoreDb("还原安全事件： {$files} 的备份文件",1);
//        }

        return $res;

    }

    public function insertRestoreDb($list,$status = 2)
    {
        $user = $_SESSION['username'];    //不考虑状态 直接根据结果入库 状态  1错误 或者2完成
        $ip = self::getIp();
        if($status == 2){
            $sql = "insert into sys_log(`user_name`,`log_event`,`log_ip`,`remark`) values ('" . $user . "','" . $list . "','" . $ip."','成功')";

        }elseif ($status == 1){
            $sql = "insert into sys_log(`user_name`,`log_event`,`log_ip`,`remark`) values ('" . $user . "','" . $list . "','" . $ip."','失败')";

        }

        $this->execute($sql);

    }


    public function sendCommand($action = '')
    {
        if ($action == '') {
            $action = 'backup action="backup_status"';
        }

        $timeout = array('sec' => 5, 'usec' => 0);
        $address = '127.0.0.1';
        $result = self::getPort();
        $port = $result['frameworkd_port'];

        file_put_contents("./restore_actions.log", gmdate("Y-m-d H:i:s") . ", Launch: $action\n", FILE_APPEND);

        $socket = socket_create(AF_INET, SOCK_STREAM, 0);
        if ($socket < 0) {
            $msg = _("Can't connect with frameworkd") . ": " . socket_strerror(socket_last_error($socket));
            file_put_contents("./restore_actions.log", gmdate("Y-m-d H:i:s") . ", $msg\n\n", FILE_APPEND);

            return $msg;
        }

        socket_set_block($socket);
        socket_set_option($socket, SOL_SOCKET, SO_RCVTIMEO, $timeout);
        socket_set_option($socket, SOL_SOCKET, SO_SNDTIMEO, $timeout);
        $result = @socket_connect($socket, $address, $port);
        if (!$result) {
            $msg = _("Can't connect with frameworkd") . " ($address:$port)";
            file_put_contents("./restore_actions.log", gmdate("Y-m-d H:i:s") . ", $msg\n\n", FILE_APPEND);
            return $msg;
        }

        $in = "$action\n";
        $out = '';

        socket_write($socket, $in, strlen($in));
        $out = @socket_read($socket, 5120, PHP_BINARY_READ);

        // Error found
        if (preg_match("/.* errno\=\"(\d+)\" error\=\"(.*)\" ack(end)?$/", $out, $found)) {
            @socket_close($socket);
            file_put_contents("./restore_actions.log", gmdate("Y-m-d H:i:s") . ", return:" . $found[2] . "\n\n", FILE_APPEND);
            return $found[2];
        }

        // Status found
        if (preg_match("/.* status\=\"(\d+)\" ack(end)?$/", $out, $found)) {
            @socket_close($socket);
            file_put_contents("./restore_actions.log", gmdate("Y-m-d H:i:s") . ", return:" . $found[1] . "\n\n", FILE_APPEND);
            return $found[1];
        }

        // Bad response
        @socket_close($socket);
        $msg = _("Bad response from frameworkd");
        file_put_contents("./restore_actions.log", gmdate("Y-m-d H:i:s") . ", $msg\n\n", FILE_APPEND);
        return -1;

    }

    public function getPort()
    {
        $where = ['frameworkd_port'];
        $data = $this->where(['conf' => ['in', $where]])->select();

        return $this->dataDispost($data);
    }

    public function status()
    {

        list($is_running, $mode, $progress) = self::isRunning();

        if ($is_running > 0) {
            $response['status'] = 'success';
            $response['message'] = ($mode == 'insert') ? _('正在插入事件…') : _('正在清除事件…');
            $response['progress'] = Util::number_format_locale($progress);
        } elseif ($is_running < 0) {
            $response['message'] = _('请查看日志了解更多信息');
            $response['status'] = 'error';
        }

        return $response;
    }

    public function isRunning()
    {
        $running_data = '';
        $status = 0;
        $mode = '';
        $dates = array();
        $progress = 0;

        // Look for running backup actions (database)
        $query = "SELECT * FROM restoredb_log WHERE status = 1";
        $rs = $this->query($query);

        $running_data = $rs[count($rs) - 1]['data'];


        if ($running_data != '') {
            // Look for running backup actions (frameworkd)
            $status = self::sendCommand('backup action="backup_status"');

            // Error from frameworkd
            if ($status < 0) {

                self::setFailed();
            } // Something is running
            elseif ($status > 0) {
                if (preg_match('/Insert events from (\d{4}\-\d{2}\-\d{2}) to (\d{4}\-\d{2}\-\d{2})/', $running_data, $found)) {
                    $mode = 'insert';
                    $date_from = $found[1];
                    $date_to = $found[2];
                    $date_cur = strtotime($date_from);
                    while ($date_cur <= strtotime($date_to)) {
                        $dates[] = date("Y-m-d", $date_cur);
                        $date_cur += 86400;
                    }
                } elseif (preg_match('/Delete events from (\d{4}\-\d{2}\-\d{2},?)+/', $running_data, $found)) {
                    $mode = 'delete';
                    $dates = explode(',', $found[1]);
                }

                // Calculate pending events to insert/purge
                if (count($dates) > 0) {
                    $progress = self::getProgress($dates);
                }
            } // Nothing is running (update restoredb_log table)
            else {
                self::setFinished();
            }
        }

        return array($status, $mode, $progress);
    }

    public function getProgress($dates)
    {
        $total = 0;
        $dates_str = '"' . implode('", "', $dates) . '"';
        $sql = 'SELECT sum(cnt) as total FROM alienvault_siem.ac_acid_event WHERE DATE(timestamp) IN (' . $dates_str . ')';
        $rs = $this->execute($sql);

        $total = $rs[count($rs) - 1]['total'];

        return $total;
    }

    public function setFinished()
    {

        $sql = 'UPDATE restoredb_log SET status = 2,percent = 100 WHERE status=1';
        $this->execute($sql);

    }


    public function setFailed()
    {
        $sql = "UPDATE restoredb_log SET status = -1,percent = 100 WHERE status=1";
        $this->execute($sql);
    }

    //获取backdir
    public function getBackDir()
    {
        $where = ['backup_dir'];
        $data = $this->where(['conf' => ['in', $where]])->select();

        return $this->dataDispost($data);
    }

    public function dataDispost($data)
    {
        $i = 0;
        foreach ($data as $k => $v) {
            $i++;
            if ($i > 1) {
                $res[$v['conf']] = $v['value'];
            } else {
                $res = [$v['conf'] => $v['value']];
            }


        }
        return $res;
    }

    public function getNowAuditLog()
    {
        $src = '/var/log/audit';
        $data = scandir($src);
        unset($data[0], $data[1]);
        $data = array_values($data);
        foreach ($data as $k => $v) {
            $res[$k]['name'] = $v;
            $res[$k]['file'] = substr($v,0,4).'-'.substr($v,4,2).'-'.substr($v,6,2);
            $size = filesize($src . '/' . $v);
            $res[$k]['size'] = self::fileSize($size); //文件大小
        }
        array_multisort(array_column($res,'file'),SORT_DESC,$res);
        return $res;
    }


    public function restoreAuditLog($param)
    {
        ini_set('max_execution_time',600);
        $arr = [];
        foreach ($param['name'] as $k =>$v)
        {   //文件名
            array_push($arr,substr($v,0,4).'-'.substr($v,4,2).'-'.substr($v,6,2));
        }
        $str = json_encode(['file_list'=>$arr,'username'=>$_SESSION['username']]);
        $files = implode(',',$arr);
        //请求python审计恢复接口
        $data = file_post("http://localhost:40111/reduct/reduct",$str);
        $data = json_decode($data,1);
        $data['info'] = "还原审计日志： {$files} 的备份文件";
//        if( $data['status']!= 'failed'){
//            self::insertRestoreDb("还原审计日志： {$files} 的备份文件",2);
//        }else{
//            self::insertRestoreDb("还原审计日志： {$files} 的备份文件",1);
//        }
        return $data;
    }


    //获取访问者ip
    public function getIP(){
        if (isset($_SERVER)){
            if (isset($_SERVER["HTTP_X_FORWARDED_FOR"])){
                $realip = $_SERVER["HTTP_X_FORWARDED_FOR"];
            } else if (isset($_SERVER["HTTP_CLIENT_IP"])) {
                $realip = $_SERVER["HTTP_CLIENT_IP"];
            } else {
                $realip = $_SERVER["REMOTE_ADDR"];
            }
        } else {
            if (getenv("HTTP_X_FORWARDED_FOR")){
                $realip = getenv("HTTP_X_FORWARDED_FOR");
            } else if (getenv("HTTP_CLIENT_IP")) {
                $realip = getenv("HTTP_CLIENT_IP");
            } else {
                $realip = getenv("REMOTE_ADDR");
            }
        }
        return $realip;
    }


}

?>
