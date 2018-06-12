<?php

/**
 * 接口文档自动生成类
 *
 * Class InterfaceDoc
 */
class InterfaceDoc
{


    /**
     * 生成目录下接口文档
     *
     * @param string $dir  接口所在目录
     * @param string $doc  接口生成的markdown文件路径
     */
    protected static function generate($dir = __INTERFACE_DIR__,$doc = __MARKDOWN__)
    {
        self::requireFile($dir);
        $ingoreClass = self::ingoreClass();
        is_file($doc) && unlink($doc);
        touch($doc);
        /*ob_start();*/
        foreach (get_declared_classes() as $class) {
            $reflectionClass = new ReflectionClass($class);
            if($reflectionClass->isUserDefined() && !in_array($class,$ingoreClass)){
                self::generateDoc($class,$doc);
            }
        }
        echo json_encode(['status'=>200,'msg'=>'OK']);
        /*ob_end_clean();*/
    }

    /**
     * 生成单个类接口文档
     *
     * @param $class
     * @param $doc
     */
    protected static function generateDoc($class,$doc)
    {
        $document = '';
        $reflectionClass = new ReflectionClass($class);

        // 类描述信息
        $classDoc = self::getClassDoc($class);

        // 各接口信息
        $methods = $reflectionClass->getMethods();

        $methodDoc = '';
        foreach ($methods as $method){
            if(in_array($method->class,self::ingoreClass())){
                continue;
            }
            if(strpos($method->name,'post_')!==false ||strpos($method->name,'get_')!==false){
                $methodDoc .= self::getMethodDoc($class,$method->name);
            }
        }
        if(!empty(trim($methodDoc))){
            $document .= $classDoc.$methodDoc;
        }

        file_put_contents($doc,$document,FILE_APPEND);
        /*$message = $class.' api doc generate success!'.PHP_EOL;
        echo $message;
        ob_flush();
        flush();*/
        sleep(0.5);
    }

    /**
     * 获取类文档
     *
     * @param $class
     * @return string
     */
    protected static function getClassDoc($class)
    {
        $doc = '';
        $reflectionClass = new ReflectionClass($class);
        $classDocment = $reflectionClass->getDocComment();
        $classDesc = self::parseComment($classDocment,true);
        $doc .= '### ';
        $doc .= $classDesc .PHP_EOL.PHP_EOL;

        return $doc;
    }

    /**
     * 获取方法文档
     *
     * @param $class
     * @param $method
     * @return string
     */
    protected static function getMethodDoc($class,$method)
    {
        $doc = '';
        $reflectionClass = new ReflectionClass($class);

        if($reflectionClass->hasMethod($method)){
            $reflectionMethod = $reflectionClass->getMethod($method);
            $methodComment = $reflectionMethod->getDocComment();

            $parseMethodComment = self::parseComment($methodComment);

            if(!empty($_GET['keyword'])){
                if(strpos($parseMethodComment['title'],trim($_GET['keyword'])) === false && strpos($parseMethodComment['url'],trim($_GET['keyword'])) === false){
                    return ;
                }
            }

            $doc .= '#### ';
            $doc .= $parseMethodComment['title'].PHP_EOL.PHP_EOL;
            $doc .= '接口地址: ';
            $doc .= $parseMethodComment['url'].PHP_EOL.PHP_EOL;
            $doc .= '请求方式: ';
            $doc .= $parseMethodComment['method'].PHP_EOL.PHP_EOL;
            $doc .= '接口参数: ';

            if(is_array($parseMethodComment['param'][0])&&($parseMethodComment['param'][0])){
                $doc .= self::getHasParamMethodParamDoc($parseMethodComment);
            }else{
                $doc .= '无'.PHP_EOL.PHP_EOL;
            }

            $doc .= PHP_EOL."<div class='btn response-btn' data-method='".$parseMethodComment['method']."' data-url='".$parseMethodComment['url']."'>输入参数生成响应</div>".PHP_EOL;
            $doc .= PHP_EOL."<div class='response-area'></div>".PHP_EOL.PHP_EOL;
        }

        return $doc.PHP_EOL;
    }

    /**
     * 获取有参数的接口的参数部分接口文档
     *
     * @param $parseMethodComment
     * @return string
     */
    protected static function getHasParamMethodParamDoc($parseMethodComment)
    {
        $paramDoc = '';
        $paramDoc .= PHP_EOL.PHP_EOL;
        $paramDoc .= '名称 | 说明'.PHP_EOL;
        $paramDoc .= ':---:|:---:'.PHP_EOL;
        foreach ($parseMethodComment['param'][0] as $key => $desc){
            $paramDoc .= $desc."|".$parseMethodComment['param'][1][$key].PHP_EOL;;
        }
        return $paramDoc;
    }

    /**
     * 加载目录下所有类文件
     *
     * @param $dir 作用目录
     */
    protected static function requireFile($dir)
    {
        if(!is_dir($dir)){
            die('目录有误!');
        }
        $handle = opendir($dir);
        while(($file = readdir($handle)) !== false){
            if($file !== '.' && $file !=='..'){
                $filePath = $dir.'/'.$file;
                if(is_dir($filePath)){
                    self::requireFile($filePath);
                }else{
                    require $filePath;
                }
            }
        }
        closedir($handle);
    }

    /**
     * 不处理的类
     *
     * @return array
     */
    protected static function ingoreClass()
    {
        return [
            self::class,
            'Weapp\BaseAction',
            'Weapp\AjaxReturnHackException'
        ];
    }


    /**
     * 解析注释
     *
     * @param $comment
     * @param bool $onlyTitle
     * @return array
     */
    protected static function parseComment($comment,$onlyTitle = false)
    {
        $title_pattern  = "/\*\s+([\x{4e00}-\x{9fa5}a-zA-Z0-9\(\)]+)\s+/u";
        $method_pattern = "/\*\s+api[:：]\s?(\w+)\s+/s";
        $url_pattern    = "/\*\s+api[:：]\s?\w+\s+([0-9a-z\/\_\-]+)\s+/s";
        $param_pattern  = "/\*\s+@param\s+(\w+)\s+(.*?\s{6,6})\s+/s";

        preg_match($title_pattern,$comment,$title);
        preg_match($method_pattern,$comment,$method);
        preg_match($url_pattern,$comment,$url);
        preg_match_all($param_pattern,$comment,$param);

        is_array($title) && !empty($title) && $title = $title[1];
        is_array($method) && !empty($method) && $method = $method[1];
        is_array($url) && !empty($url) && $url = $url[1];
        is_array($param) && !empty($param) && $param = [$param[1],array_map('trim',$param[2])];

        if($onlyTitle){
            return $title;
        }

        return ['title'=>$title,'method'=>$method,'url'=>$url,'param'=>$param];
    }

    /**
     * 路由转发
     */
    public static function handle()
    {
        $act = isset($_GET['act'])?trim($_GET['act']):'generate';
        self::$act();
    }

    /**
     * 获取接口响应结果
     *
     * @return mixed
     */
    protected static function getResponse()
    {
        $data = $_POST;
        $method = $data['method'];
        $url = __INTERFACE_URI__.$data['url'];

        unset($data['method'],$data['url']);
        $params = $data;

        if($method == 'GET') {
            echo self::curlGet($url, $params);
        }elseif($method == 'POST'){
            echo self::curlPost($url, $params);
        }
    }

    /**
     * curl get
     *
     * @param $url
     * @param $params
     * @return mixed
     */
    protected static function curlGet($url,$params)
    {
        $paramstr = http_build_query($params);
        $url .= '?'.$paramstr;
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_HTTPHEADER,['auth:1c2ffa5f-aa97-5a50-9cfc-39e52ba462d9']);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        $output = curl_exec($ch);
        curl_close($ch);
        return $output;
    }


    /**
     * curl post
     *
     * @param $url
     * @param $params
     * @return mixed
     */
    protected static function curlPost($url,$params)
    {
        //curl 初始化
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_HTTPHEADER,['auth:1c2ffa5f-aa97-5a50-9cfc-39e52ba462d9']);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $params);
        $out_put = curl_exec($ch);
        curl_close($ch);
        return $out_put;
    }

}





