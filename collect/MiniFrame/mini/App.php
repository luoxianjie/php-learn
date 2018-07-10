<?php
error_reporting(E_ERROR | E_WARNING | E_PARSE);
define("__ROOT__",dirname(__DIR__));
class App
{
    /**
     * 启动app
     */
    public static function start()
    {
        // 设置错误处理方法
        set_error_handler([self::class,'displayErrorHandler']);

        // 加载帮助函数
        require __ROOT__."/function/function.php";

        // composer自动加载
        require __ROOT__."/vendor/autoload.php";

        // 注册自动加载
        spl_autoload_register([self::class,'autoload']);

        // 路由解析
        self::parseUrl();

        // 路由
        self::dispatcher();
    }

    /**
     * 重写路由解析
     */
    private static function parseUrl()
    {
        if(isset($_SERVER['PATH_INFO'])){
            $path = $_SERVER['PATH_INFO'];
            if(false !== strpos($path,'.html')){
                $path = str_replace('.html','',$path);
            }
            $path = explode('/',trim($path,'/'));
            $_GET['c'] = array_shift($path);
            $_GET['a'] = array_shift($path);
        }
    }

    /**
     * 路由转发
     */
    private static function dispatcher()
    {
        $c = isset($_GET['c'])?trim(ucfirst($_GET['c'])):"Index";
        $a = isset($_GET['a'])?trim($_GET['a']):"index";
        define('__CONTROLLER__',$c);
        define('__ACTION__',$a);

        $c = "controller\\".$c;

        call_user_func_array([new $c(), $a], []);
    }

    /**
     * 自动加载
     * @param $classname
     */
    private static function autoload($classname)
    {
        $file = __ROOT__."/".str_replace('\\','/',$classname).".php";
        if(is_file($file)){
            require $file;
            return ;
        }
        die("Error:".$classname." not found!");
    }

    /**
     * 捕获错误
     * @param $error
     * @param $error_string
     * @param $filename
     * @param $line
     * @param $symbols
     */
    public static function displayErrorHandler($error, $error_string, $filename, $line, $symbols)
    {
        $error_no_arr = array(1=>'ERROR', 2=>'WARNING', 4=>'PARSE', 8=>'NOTICE', 16=>'CORE_ERROR', 32=>'CORE_WARNING', 64=>'COMPILE_ERROR', 128=>'COMPILE_WARNING', 256=>'USER_ERROR', 512=>'USER_WARNING', 1024=>'USER_NOTICE', 2047=>'ALL', 2048=>'STRICT');

        if(in_array($error,array(1,2,4)))
        {
            $str =  "<b>Filename</b>:".$filename."<br>";
            $str .= "<b>line</b>:".$line."<br>";
            $str .= "<b>Message</b>:".$error_string."<br>";
            echo $str;
            die;
        }
    }

}