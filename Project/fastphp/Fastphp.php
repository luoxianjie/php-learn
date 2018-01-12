<?php
// +----------------------------------------------------------------------
// | Author: jiexianluo@hotmail.com
// | Date  : 2018/1/12
// | Time  : 10:07
// +----------------------------------------------------------------------

namespace fastphp;

use function Composer\Autoload\includeFile;

defined('CORE_PATH') or define('CORE_PATH',__DIR__);

class Fastphp
{
    protected $config = [];

    public function __construct($config)
    {
        $this->config = $config;
    }

    public function run()
    {
        spl_autoload_register([$this,'autoload']);

        $this->setErrorReport();
        $this->removeMagicQuotes();
        $this->unregisterGlobals();
        $this->setDbConfig();
        $this->route();


    }

    public function autoload($className)
    {
        $classMap = $this->classMap();

        if(isset($classMap[$className]))
        {
            $file = $classMap[$className];
        }elseif(strpos($className,'\\') !== false){
            $file = APP_PATH . str_replace('\\','/',$className).'.php';

            if(!is_file($file)){
                return ;
            }
        }else{
            return ;
        }

        include $file;
    }

    public function classMap()
    {
        return [
            'fastphp\base\Controller' => CORE_PATH . '/base/Controller.php',
            'fastphp\base\Model' => CORE_PATH . '/base/Model.php',
            'fastphp\base\View' => CORE_PATH . '/base/View.php',
            'fastphp\db\Db' => CORE_PATH . '/db/Db.php',
            'fastphp\db\Sql' => CORE_PATH . '/db/Sql.php',
        ];
    }

    public function setErrorReport()
    {
        if(APP_DEBUG == true){
            error_reporting(E_ALL);
            ini_set('display_errors','On');
        }else{
            error_reporting(E_ALL);
            ini_set('display_errors','Off');
            ini_set('log_errors','On');
        }
    }

    public function removeMagicQuotes()
    {
        if(get_magic_quotes_gpc()){
            $_GET      = isset($_GET)?$this->stripSlashesDeep($_GET):'';
            $_POST     = isset($_POST)?$this->stripSlashesDeep($_POST):'';
            $_SESSION  = isset($_SESSION)?$this->stripSlashesDeep($_SESSION):'';
            $_COOKIE   = isset($_COOKIE)?$this->stripSlashesDeep($_COOKIE):'';
        }
    }

    public function stripSlashesDeep($data)
    {
        $data = is_array($data)?array_map([$this,'stripSlashesDeep'],$data):stripslashes($data);
        return $data;
    }

    public function unregisterGlobals()
    {
        if(ini_get('register_globals')){
            $array = ['_SESSION', '_POST', '_GET', '_COOKIE', '_REQUEST', '_SERVER', '_ENV', '_FILES'];

            foreach ($array as $item){
                foreach ($GLOBALS[$item] as $key => $var) {
                    if($var === $GLOBALS[$key]){
                        unset($GLOBALS[$key]);
                    }
                }
            }
        }
    }

    public function setDbConfig()
    {
        if($this->config){
            define('DB_HOST', $this->config['DB_HOST']);
            define('DB_NAME', $this->config['DB_NAME']);
            define('DB_USER', $this->config['DB_USER']);
            define('DB_PASS', $this->config['DB_PASS']);
        }
    }

    public function route()
    {
        $controllerName = $this->config['DEFAULT_CONTROLLER'];
        $actionName = $this->config['DEFAULT_ACTION'];
        $params = [];

        $uri = $_SERVER['REQUEST_URI'];

        $position = strpos($uri,'?');
        $url = ($position===false)?$uri : substr($uri,0,$position);

        $url = trim($url,'/');

        if($url){
            $urlArr = array_filter(explode('/',$url));

            $controllerName = ucfirst($urlArr[0]);

            array_shift($urlArr);
            $actionName = $urlArr?$urlArr[0]:$actionName;

            array_shift($urlArr);
            $params = $urlArr?$urlArr:$params;
        }

        $controller = 'app\\controllers\\'.$controllerName."Controller";

        if(!class_exists($controller)){
            exit('控制器不存在!');
        }

        if(!method_exists($controller,$actionName)){
            exit('方法不存在!');
        }

        $dispatch = new $controller();

        call_user_func_array([$dispatch,$actionName],$params);

    }


}