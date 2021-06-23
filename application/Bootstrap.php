<?php
/**
 * @name Bootstrap
 * @author jsyzchenchen@gmail.com
 * @desc 所有在Bootstrap类中, 以_init开头的方法, 都会被Yaf调用,
 * @see http://www.php.net/manual/en/class.yaf-bootstrap-abstract.php
 * 这些方法, 都接受一个参数:Yaf_Dispatcher $dispatcher
 * 调用的次序, 和申明的次序相同
 */
class Bootstrap extends \Yaf\Bootstrap_Abstract
{
    /** @var object config */
	private $config;

    /**
     * 初始化错误,要放在最前面
     */
    public function _initErrors()
    {
        //如果为开发环境,打开所有错误提示
        if (Yaf\ENVIRON === 'develop') {
            error_reporting(E_ERROR | E_PARSE);//使用error_reporting来定义哪些级别错误可以触发//E_ERROR | E_WARNING | E_PARSE
            ini_set('display_errors', 1);
            ini_set('display_startup_errors', 1);
        }else{
            //错误 E_NOTICE和E_WARNING之外的所有错误
            error_reporting(E_ALL &~E_NOTICE &~E_WARNING); 
        }
    }

    /**
     * 加载vendor下的文件
     */
    public function _initLoader()
    {
        \Yaf\Loader::import(APP_PATH . '/vendor/autoload.php');
    }

    /**
     * 配置
     */
    public function _initConfig()
    {
		//把配置保存起来
		$this->config = \Yaf\Application::app()->getConfig();
        \Yaf\Registry::set('config', $this->config);
        \Yaf\Dispatcher::getInstance()->autoRender(FALSE);  // 关闭自动加载模板

        //默认时区设置
        date_default_timezone_set('Asia/Shanghai');
        ini_set('date_timezone','Asia/Shanghai');

        //配置文件最大上传MB
        ini_set('upload_max_filesize','1024MB');
        ini_set('post_max_size','1024MB');
	}

    /**
     * 日志
     * @param \Yaf\Dispatcher $dispatcher
     */
	public function _initLogger(\Yaf\Dispatcher $dispatcher)
    {
        //SocketLog
        if (Yaf\ENVIRON === '1-develop') {
            if ($this->config->socketlog->enable) {
                //载入
                \Yaf\Loader::import('Common/Logger/slog.function.php');
                //配置SocketLog
                slog($this->config->socketlog->toArray(),'config');
            }
        }
    }

    /**
     * 公用函数载入
     */
    public function _initFunction()
    {
        \Yaf\Loader::import('Common/functions.php');
    }

    /**
     * 初始化插件
     */
    public function _initPlugins(Yaf\Dispatcher $dispatcher)
    {
        $dispatcher->registerPlugin(new \HeaderPlugin());
    }
}
