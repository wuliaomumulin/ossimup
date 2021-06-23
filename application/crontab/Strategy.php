<?php
include_once '/work/web/application/crontab/library/PDO.class.php';

error_reporting(E_ALL & ~E_NOTICE & ~E_WARNING);

/**
 *推荐策略定时任务
 */
class Crontab_Strategy
{

    /* 代表采集器组 */
    private $pdo;
    private $_REDIS;
    //前缀
    public $redis_prefix = 'sensor-ip-';

    public function __construct()
    {
        @$this->pdo = DB::getInstance("127.0.0.1","root",'123456','alienvault','utf8');
        $this->redis();
    }

    /**
     * 连接redis句柄
     */
    public function redis()
    {
        if (extension_loaded('redis')) {
            $this->_REDIS = new \Redis();
            $this->_REDIS->connect('127.0.0.1', 6379);
            return $this->_REDIS;
        } else {
            exit('redis is not found.');
        }
    }

    public function set($key, $value, $type = 0, $repeat = 0, $time = 0, $old = 0)
    {
        $return = null;

        if ($type) {
            $return = $this->_REDIS->append($key, $value);
        } else {
            if ($old) {
                $return = $this->_REDIS->getSet($key, $value);
            } else {
                if ($repeat) {
                    $return = $this->_REDIS->setnx($key, $value);
                } else {
                    if ($time && is_numeric($time)) $return = $this->_REDIS->setex($key, $time, $value);
                    else $return = $this->_REDIS->set($key, $value);
                }
            }
        }

        return $return;
    }

    //+++-------------------------哈希操作-------------------------+++//  

    /**
     * 将key->value写入hash表中
     * @param $hash string 哈希表名
     * @param $data array 要写入的数据 array('key'=>'value')
     */
    public function hashSet($hash, $data)
    {
        $return = null;

        if (is_array($data) && !empty($data)) {
            $return = $this->_REDIS->hMset($hash, $data);
        }

        return $return;
    }

    /**
     * 获取hash表的数据
     * @param $hash string 哈希表名
     * @param $key mixed 表中要存储的key名 默认为null 返回所有key>value
     * @param $type int 要获取的数据类型 0:返回所有key 1:返回所有value 2:返回所有key->value
     */
    public function hashGet($hash, $key = array(), $type = 0)
    {
        $return = null;

        if ($key) {
            if (is_array($key) && !empty($key))
                $return = $this->_REDIS->hMGet($hash, $key);
            else
                $return = $this->_REDIS->hGet($hash, $key);
        } else {
            switch ($type) {
                case 0:
                    $return = $this->_REDIS->hKeys($hash);
                    break;
                case 1:
                    $return = $this->_REDIS->hVals($hash);
                    break;
                case 2:
                    $return = $this->_REDIS->hGetAll($hash);
                    break;
                default:
                    $return = false;
                    break;
            }
        }

        return $return;
    }

    /**
     * 获取hash表中元素个数
     * @param $hash string 哈希表名
     */
    public function hashLen($hash)
    {
        $return = null;

        $return = $this->_REDIS->hLen($hash);

        return $return;
    }

    /**
     * 删除hash表中的key
     * @param $hash string 哈希表名
     * @param $key mixed 表中存储的key名
     */
    public function hashDel($hash, $key)
    {
        $return = null;

        $return = $this->_REDIS->hDel($hash, $key);

        return $return;
    }

    /**
     * 查询hash表中某个key是否存在
     * @param $hash string 哈希表名
     * @param $key mixed 表中存储的key名
     */
    public function hashExists($hash, $key)
    {
        $return = null;

        $return = $this->_REDIS->hExists($hash, $key);

        return $return;
    }

    /**
     * 自增hash表中某个key的值
     * @param $key string 哈希表名
     * @param $time mixed 表中存储的key名
     */
    public function setKeyExpire($key, $time)
    {
        $return = null;

        $return = $this->_REDIS->setTimeout($key, $time);

        return $return;
    }

    /**
     * 查询字符串
     */
    public function query($sql)
    {
        $statement = $this->pdo->prepare($sql);
        $statement->execute();
        return $statement->fetchAll();
    }

    /**
     * 查询字符串
     */
    public function find($sql)
    {
        $statement = $this->pdo->prepare($sql);
        $statement->execute();
        return $statement->fetch();
    }

    public function execute($sql)
    {
        $statement = $this->pdo->prepare($sql);
        $statement->execute();
        return $statement->fetchAll();
    }

    /**
     * 连接数据库句柄
     */
    public function strategy()
    {
        $data  = $this->pdo->query("SELECT `value` FROM `config` WHERE `conf` IN ('strategy_num','strategy_time')");

        $num = (int)$data[0]['value'];
        $time = explode('~', $data[1]['value']);
        $begin_time = date("Y-m-d", time()) . ' ' . $time[0];
        $end_time = date("Y-m-d", time()) . ' ' . $time[1];

        $da1 = $this->pdo->query("select INET6_NTOA(ip_src) ip_src,count(ip_src) count from alienvault_siem.acid_event where `timestamp` BETWEEN '{$begin_time}' and '{$end_time}' group by ip_src having count(ip_src) >{$num}");
        //  $da2 = $this->query("select INET6_NTOA(ip_dst) ip_src,count(ip_dst) count from alienvault_siem.acid_event where `timestamp` BETWEEN '{$begin_time}' and '{$end_time}' group by ip_dst");
        $ips = '';

        if(!empty($da1)){
            foreach ($da1 as $k => $v){
                $ips = $ips.$v['ip_src'].'-';
            }
            $ips = rtrim($ips,'-');
        }

        $old_ips = $this->pdo->query("SELECT `value` FROM `config` WHERE `conf` IN ('strategy_ips')");
        if(!empty($old_ips[0]['value'])){
            if($ips != ''){
                $new_ips = $old_ips[0]['value'].'-'.$ips;
            }else{
                $new_ips = $old_ips[0]['value'];
            }
        }else{
            $new_ips = $ips;
        }

        $this->pdo->update('config',array('value'=>$new_ips),'conf = \'strategy_ips\'');

    }

    /**
     * 生产redis数据
     */
    public function production($data, $role = '')
    {

        //10分钟过期
        if (empty($role)) {
            foreach ($data as $ip => $arr) {
                $this->set($this->redis_prefix . $ip, json_encode($arr), 0, 0, 120);
            }
        } else {
            $this->set($this->redis_prefix . '1', json_encode($data), 0, 0, 120);
        }

        return true;

    }

    /**
     *    生产table,生产map
     * @param $hash String hash表名
     * @param $sql  String 要同步的sql语句
     * @param $key  String hash键名
     * @param $val  String hash键值
     * @param $expire Int 过期时间，默认10分钟
     */
    private function production_table(string $hash, string $sql, string $key, string $val = '', int $expire = 600)
    {

        if ($this->hashLen($hash) == 0) {
            $data = $this->query($sql);
            if (!is_null($data)) {
                //当val为空的时候，证明值是一个数组
                if (empty($val)) {
                    $keys = array();
                    $vals = array();
                    foreach ($data as $arr) {
                        $keys[] = $arr[$key];
                        $vals[] = json_encode($arr, 256);
                    }

                    $data = array_combine($keys, $vals);

                } else {
                    $data = array_combine(array_column($data, $key), array_column($data, $val));

                }
            } else {
                return false;
            }
            //表缓存数据设置为10分钟，即600s
            $this->hashSet($hash, $data);
            $this->setKeyExpire($hash, $expire);
        }

        return true;

    }


}

/**
 * 生产数据,
 * 创建两个进程，父进程用于用于管理员数据，子进程用于处理子账户安全事件的数据
 */
$Crontab_Strategy = new Crontab_Strategy();
$Crontab_Strategy->strategy();

?>
