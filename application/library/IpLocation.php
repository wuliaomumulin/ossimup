<?php 
/**
读取gbk编码的qqwry.dat转换成utf-8类库
可以传入ip地址源 和输入编码类型 输出编码类型 
@761071654@qq.com
*/
 class IpLocation {
   //数据文件指针
   private $fp;
   private $firstip;
   private $lastip;
   private $totalip;
   private $incode;//默认编码,如果设置别的编码，请传此参数
   private $outcode;//默认编码,如果设置别的编码，请传此参数
  
  
   private function getlong() {
    //unpack从二进制字符串对数据进行解包
    //将读取的little-endian编码的4个字节转化为长整型数,fread安全读取二进制文件
    $result = unpack('Vlong', fread($this->fp, 4));
    return $result['long'];
   }
  
  
   private function getlong3() {
    //将读取的little-endian编码的3个字节转化为长整型数
    $result = unpack('Vlong', fread($this->fp, 3).chr(0));
    return $result['long'];
   }
  
  
   private function packip($ip) {
    //pack把数据装入一个二进制字符串
    //ip2long将IP地址转成无符号的长整型，也可以用来验证IP地址
    return pack('N', intval(ip2long($ip)));
   }
  
  
   private function getstring($data = "") {
    $char = fread($this->fp, 1);
    while (ord($char) > 0) {    //ord返回字符的ASCII值，字符串按照C格式保存，以\0结束
      $data .= $char;
      $char = fread($this->fp, 1);
    }
    return iconv($this->incode,$this->outcode,$data);
   }
  
  
   protected function getarea() {
    $byte = fread($this->fp, 1);  // 标志字节
    switch (ord($byte)) {
      case 0:           // 没有区域信息
        $area = "";
        break;
      case 1:
      case 2:           // 标志字节为1或2，表示区域信息被重定向
        fseek($this->fp, $this->getlong3());
        $area = $this->getstring();
        break;
      default:          // 否则，表示区域信息没有被重定向
        $area = $this->getstring($byte);
        break;
    }
    return $area;
   }
  
  
   public function getlocation($ip) {
    if (!$this->fp) return null;      // 如果数据文件没有被正确打开，则直接返回空
    $location['ip'] = gethostbyname($ip);  // 域名转化为IP地址
    $ip = $this->packip($location['ip']);  // 将输入的IP地址转化为可比较的IP地址
    // 不合法的IP地址会被转化为255
    // 对分搜索
    $l = 0;             // 搜索的下边界
    $u = $this->totalip;      // 搜索的上边界
    $findip = $this->lastip;    // 如果没有找到就返回最后一条IP记录（QQWry.Dat的版本信息）
    while ($l <= $u) {       // 当上边界小于下边界时，查找失败
      $i = floor(($l + $u) / 2); // 计算近似中间记录
      fseek($this->fp, $this->firstip + $i * 7);
      $beginip = strrev(fread($this->fp, 4));   // 获取中间记录的开始IP地址,strrev反转字符串
      // strrev函数在这里的作用是将little-endian的压缩IP地址转化为big-endian的格式，便于比较
      //关于little-endian与big-endian 参考：http://baike.baidu.com/view/2368412.htm
      if ($ip < $beginip) {    // 用户的IP小于中间记录的开始IP地址时
        $u = $i - 1;      // 将搜索的上边界修改为中间记录减一
      }
      else {
        fseek($this->fp, $this->getlong3());
        $endip = strrev(fread($this->fp, 4));  // 获取中间记录的结束IP地址
        if ($ip > $endip) {   // 用户的IP大于中间记录的结束IP地址时
          $l = $i + 1;    // 将搜索的下边界修改为中间记录加一
        }
        else {         // 用户的IP在中间记录的IP范围内时
          $findip = $this->firstip + $i * 7;
          break;       // 则表示找到结果，退出循环
        }
      }
    }
  
  
    fseek($this->fp, $findip);
    $location['beginip'] = long2ip($this->getlong());  // 用户IP所在范围的开始地址
    $offset = $this->getlong3();
    fseek($this->fp, $offset);
    $location['endip'] = long2ip($this->getlong());   // 用户IP所在范围的结束地址
    $byte = fread($this->fp, 1);  // 标志字节
    switch (ord($byte)) {
      case 1:           // 标志字节为1，表示国家和区域信息都被同时重定向
        $countryOffset = $this->getlong3();     // 重定向地址
        fseek($this->fp, $countryOffset);
        $byte = fread($this->fp, 1);  // 标志字节
        switch (ord($byte)) {
          case 2:       // 标志字节为2，表示国家信息又被重定向
           fseek($this->fp, $this->getlong3());
           $location['country'] = $this->getstring();
           fseek($this->fp, $countryOffset + 4);
           $location['area'] = $this->getarea();
           break;
          default:      // 否则，表示国家信息没有被重定向
           $location['country'] = $this->getstring($byte);
           $location['area'] = $this->getarea();
           break;
        }
        break;
      case 2:           // 标志字节为2，表示国家信息被重定向
        fseek($this->fp, $this->getlong3());
        $location['country'] = $this->getstring();
        fseek($this->fp, $offset + 8);
        $location['area'] = $this->getarea();
        break;
      default:          // 否则，表示国家信息没有被重定向
        $location['country'] = $this->getstring($byte);
        $location['area'] = $this->getarea();
        break;
    }
    if ($location['country'] == " CZNET") { // CZNET表示没有有效信息
      $location['country'] = "未知";
    }
    if ($location['area'] == " CZNET") {
      $location['area'] = "";
    }
    return $location;
   }
   /**
   * 构造函数，打开 QQWry.Dat 文件并初始化类中的信息
   可以传入ip地址源 和编码类型
   */
   public function __construct($filename = "qqwry.dat",$incode='gbk',$outcode='utf-8') {
    $this->fp = 0;
    if(!is_file($filename)) {
            $filename = dirname(__FILE__) . '/' . $filename;
      }
    if (($this->fp = fopen($filename, 'rb')) !== false) {
      $this->firstip = $this->getlong();
      $this->lastip = $this->getlong();
      $this->incode = $incode;
      $this->outcode = $outcode;
      $this->totalip = ($this->lastip - $this->firstip) / 7;
    }
   }
   /**
   * 析构函数，用于在页面执行结束后自动关闭打开的文件。
   */
   public function __destruct() {
    if ($this->fp) {
      fclose($this->fp);
    }
    $this->fp = 0;
   }

   //获得ip
   public function getIP()
   {
     if(getenv("HTTP_CLIENT_IP") && strcasecmp(getenv("HTTP_CLIENT_IP"), "unknown"))
     $ip = getenv("HTTP_CLIENT_IP");
     elseif(getenv("HTTP_X_FORWARDED_FOR") && strcasecmp(getenv("HTTP_X_FORWARDED_FOR"), "unknown"))
     $ip = getenv("HTTP_X_FORWARDED_FOR");
     elseif (getenv("REMOTE_ADDR") && strcasecmp(getenv("REMOTE_ADDR"), "unknown"))
     $ip = getenv("REMOTE_ADDR");
     elseif (isset($_SERVER['REMOTE_ADDR']) && $_SERVER['REMOTE_ADDR'] && strcasecmp($_SERVER['REMOTE_ADDR'], "unknown"))
     $ip = $_SERVER['REMOTE_ADDR'];
     else
     $ip = "0.0.0.0";
     return $ip;
   }

    //随机生成国内ip
    public function rand_ip(){
     $ip_long = array(
      array('607649792', '608174079'), //36.56.0.0-36.63.255.255
      array('975044608', '977272831'), //58.30.0.0-58.63.255.255
      array('999751680', '999784447'), //59.151.0.0-59.151.127.255
      array('1019346944', '1019478015'), //60.194.0.0-60.195.255.255
      array('1038614528', '1039007743'), //61.232.0.0-61.237.255.255
      array('1783627776', '1784676351'), //106.80.0.0-106.95.255.255
      array('1947009024', '1947074559'), //116.13.0.0-116.13.255.255
      array('1987051520', '1988034559'), //118.112.0.0-118.126.255.255
      array('2035023872', '2035154943'), //121.76.0.0-121.77.255.255
      array('2078801920', '2079064063'), //123.232.0.0-123.235.255.255
      array('-1950089216', '-1948778497'), //139.196.0.0-139.215.255.255
      array('-1425539072', '-1425014785'), //171.8.0.0-171.15.255.255
      array('-1236271104', '-1235419137'), //182.80.0.0-182.92.255.255
      array('-770113536', '-768606209'), //210.25.0.0-210.47.255.255
      array('-569376768', '-564133889'), //222.16.0.0-222.95.255.255
     );
     $rand_key = mt_rand(0, 14);
     $huoduan_ip= long2ip(mt_rand($ip_long[$rand_key][0], $ip_long[$rand_key][1]));
     return $huoduan_ip;
    }
  
    //检测主机是否畅通
    public function ping_ip($ip,$w=1){
      $command ='ping -c 1 -w '.$w.' '.$ip.' &>/dev/null && echo "ok" || echo "no"';
      exec($command,$signal,$status);
      return $signal[0];
    }

    /**
    * 检测端口是否畅通
    * @params $ip
    * @params $port
    * @params $w time out
    */
    public static function nc_port($ip,$port,$w=1){
      /*shell
      $string = `nc -w {$w} {$ip} {$port} && echo ok||echo no `;
      return trim($string);
      */
      $fp = fsockopen($ip,$port,$err,$errstr,$w);
      fclose($fp);
      return $fp ? 'ok' : 'no';
    }
 }