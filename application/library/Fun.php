<?php
/**
 * 单例公共函数
 * @author Myxf
 *
 */
class Fun{
    static $_instance;
    
    public function instace() {
        if( ! (self::$_instance instanceof self) ) {
            self::$_instance = new self();
        }
        return self::$_instance;
    }
    
    private function __clone() {}
    
    /**生成链接
     * @param string $a
     * @return string
     */
    public static function site_url($a){
        $config = Yaf_Application::app()->getConfig();
        $ext = $config->pro->ext;
        $site = $config->pro->site;
        $site = !empty($site) ? $site : 'http://'.$_SERVER['HTTP_HOST'].'/';
        return $site.$a.$ext;
    }
/**
     * get
     *
     */
    public function getg($name, $default = ''){
        $value = $this->getRequest()->get($name, $default);
        $value = Tools::filter($value);
        return $value;
    }

    /**
     * post
     *
     */
    public function getp($name, $default = ''){
        $value = $this->getRequest()->getPost($name, $default);
        $value = Tools::filter($value);
        return $value;
    }

    /**
     * request
     *
     */
    public function getParam($name, $default = '') {
        $value = $this->getRequest()->getQuery($name, $default);
        $value = Tools::filter($value);
        return $value;
    }

    /**
     * getallpost
     *
     */
    public function getAllp() {
        $value = $_POST;
        $value = Tools::filter($value);
        return $value;
    }
    
    public function getAllg(){
        $value = $this->getRequest()->get($name, $default);
        $value = Tools::filter($value);
        return $value;
    }

    /**
     * 违规关键词屏蔽
     * @param $content
     */
    public function shielding($content){
        if (is_array($content)){
            $content = serialize($content);
        }
        $config = new ConfigModel();
        $key = $config->getOne("shielding");
        $maxz_28=explode(",", $key['shielding']);
        for ($i=0; $i < count($maxz_28); $i++) { 
            if (strpos($content,$maxz_28[$i])!==false){
            //Fun::msg("您提交的信息含有违规字符[".$maxz_28[$i]."]，请返回修改！");
            Fun::msg("您提交的信息含有违规字符，请返回修改！");
            exit();
            }
        }
    }

    /**
     * 提示信息
     * @param $msg
     * @param bool $url
     * @internal param string $pMsg
     * @internal param bool $pUrl
     */
    public static function msg($msg, $url = FALSE) {
        header('Content-Type:text/html; charset=utf-8');
        is_array($msg) && $msg = join('\n', $msg);
        echo '<script type="text/javascript">';
        if($msg) echo "alert('$msg');";
        if($url) echo "self.location='{$url}'";
        elseif(empty($_SERVER['HTTP_REFERER'])) echo 'window.history.back(-1);';
        else echo "self.location='{$_SERVER['HTTP_REFERER']}';";
        exit('</script>');
    }
    
    /**
     * 分页函数
     * @param 所有总数 int $count
     * @param 每页条数 int $every
     * @param 当前页数 int $page
     * @param 显示最大页数 int $max
     * @param 链接 string $page_url
     * @return string
     */
    public static function page($count, $every, $page, $max, $page_url){
        $sum_page = ceil($count/$every);
        if ($count <= $every) {
            return '';
        }
        $str = '<ul class="pagination">';
        if ($page >= $max) {
            $pre_page = ($page - $max) > 0 ? ($page - $max) : 1;
            $str .= '<li><a href="'. self::site_url($page_url.'/page/'.$pre_page) .'">«</a></li>';
        }else{
            $str .= '<li class="disabled"><a href="javascript:;">«</a></li>';
        }
        
        $for_start = ($page-(int)($max/2) > 0) ? ($page-(int)($max/2)) : 1;
        $for_max    = ($for_start + $max -1 < $sum_page) ? ($for_start + $max - 1) : $sum_page;
        
        while($for_start <= $for_max){
            $str .= '<li';
            if ($for_start == $page) {
                $str .= ' class="active">';
            }else{
                $str .= '>';
            }           
            $str .= '<a href="'. self::site_url($page_url.'/page/'.$for_start) .'">'. $for_start .'</a></li>';
            $for_start++;
        }
        if ($page <= $sum_page - $max ) {
            $next_page = ($page + $max) < $sum_page ? ($page + $max) : $sum_page;
            $str .= '<li><a href="'. self::site_url($page_url.'/page/'.$next_page) .'">»</a></li>';
        }else{
            $str .= '<li class="disabled"><a href="javascript:;">»</a></li>';
        }
        
        $str .= '</ul>';
        return $str;
    }
    
    /**
     * 判断id是否属于ids中
     * @param int $id
     * @param array $ids
     * @return bool
     */
    public static function ifInTags($id = 0,$ids = array()){
        if (in_array($id, $ids)) return TRUE;
        return FALSE;
    }

    /**
     * double类型数据转换为string
     * @param $num
     * @return string
     */
    public static function number_format($num){
        return number_format($num,0,'','');
    }

    /**
     * 从数组中取得某字段并返回数组，一般为主键
     * @param $data
     * @param $column
     *
     * @return array
     */
    public static function get_column($data,$column){
        if (!is_array($data) || count($data) < 1) return FALSE;

        $result = array();
        foreach($data as $v){
            if (array_key_exists($column,$v)) $result[] = $v[$column];
        }
        return $result;
    }

    /**
     * 获取avatar图标
     * @param string $email
     * @param int $size
     * @return string
     */
    public static function avatar($email, $size = 32){
        $str = 'http://www.gravatar.com/avatar/'.md5($email).'.png';
        $str .= '?s='.$size;
        return $str;
    }
}