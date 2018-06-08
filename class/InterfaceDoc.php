<?php

class InterfaceDoc
{


    /**
     * 生成目录下接口文档
     *
     * @param $dir
     * @param string $doc
     */
    public static function generate($dir,$doc = 'doc.md')
    {
        self::requireFile($dir);
        $ingoreClass = self::ingoreClass();
        unlink($doc);
        touch($doc);
        ob_start();
        foreach (get_declared_classes() as $class) {
            $reflectionClass = new ReflectionClass($class);
            if($reflectionClass->isUserDefined() && !in_array($class,$ingoreClass)){
                self::generateDoc($class,$doc);
            }
        }
        ob_end_clean();
    }

    /**
     * 生成单个类接口文档
     *
     * @param $class
     * @param $doc
     */
    public static function generateDoc($class,$doc)
    {
        $document = '';
        $reflectionClass = new ReflectionClass($class);

        // 类描述信息
        $document .= self::getClassDoc($class);

        // 各接口信息
        $methods = $reflectionClass->getMethods();

        foreach ($methods as $method){
            if(in_array($method->class,self::ingoreClass())){
                continue;
            }
            if(strpos($method->name,'post_')!==false ||strpos($method->name,'get_')!==false){
                $document .= self::getMethodDoc($class,$method->name);
            }
        }
        file_put_contents($doc,$document,FILE_APPEND);
        $message = $class.' api doc generate success!'.PHP_EOL;
        echo $message;
        ob_flush();
        flush();
        sleep(1);
    }

    /**
     * 类描述信息
     *
     * @param $class
     * @return string
     */
    public static function getClassDoc($class)
    {
        $doc = '';
        $reflectionClass = new ReflectionClass($class);
        $classDocment = $reflectionClass->getDocComment();
        $classDesc = self::parseComment($classDocment,true);
        $doc .= '### ';
        $doc .= $classDesc .PHP_EOL.PHP_EOL;

        return $doc;
    }

    public static function getMethodDoc($class,$method)
    {
        $doc = '';
        $reflectionClass = new ReflectionClass($class);

        if($reflectionClass->hasMethod($method)){
            $reflectionMethod = $reflectionClass->getMethod($method);
            $methodComment = $reflectionMethod->getDocComment();

            $parseMethodComment = self::parseComment($methodComment);
            $doc .= '#### ';
            $doc .= $parseMethodComment['title'].PHP_EOL.PHP_EOL;
            $doc .= '接口地址: ';
            $doc .= $parseMethodComment['url'].PHP_EOL.PHP_EOL;
            $doc .= '请求方式: ';
            $doc .= $parseMethodComment['method'].PHP_EOL.PHP_EOL;
            $doc .= '接口参数: ';

            if(is_array($parseMethodComment['param'][0])&&($parseMethodComment['param'][0])){
                $doc .= PHP_EOL.PHP_EOL;
                $doc .= '名称 | 说明'.PHP_EOL;
                $doc .= ':---:|:---:'.PHP_EOL;
                foreach ($parseMethodComment['param'][0] as $key => $desc){
                    $doc .= $desc."|".$parseMethodComment['param'][1][$key].PHP_EOL;;
                }
            }else{
                $doc .= '无'.PHP_EOL.PHP_EOL;
            }
        }

        return $doc.PHP_EOL;
    }

    /**
     * 加载目录下所有类文件
     *
     * @param $dir 作用目录
     */
    public static function requireFile($dir)
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
    public static function ingoreClass()
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
    public static function parseComment($comment,$onlyTitle = false)
    {

        $comment = <<<EOF
    /**
     * 取消订单
     *
     * api: POST /order/cancel
     *
     * @param id	  integer 订单ID
     * @param reason  string 选择的原因
     * @param content string 其他原因
     */
EOF;


        $title_pattern  = "/\*\s+([\x{4e00}-\x{9fa5}a-zA-Z0-9\(\)]+)\s+/u";
        $method_pattern = "/\*\s+api[:：]\s?(\w+)\s+/s";
        $url_pattern    = "/\*\s+api[:：]\s?\w+\s+([0-9a-z\/\_\-]+)\s+/s";
        $param_pattern  = "/\*\s+@param\s+(\w+)\s+(.*?)\s+/s";

        preg_match($title_pattern,$comment,$title);
        preg_match($method_pattern,$comment,$method);
        preg_match($url_pattern,$comment,$url);
        preg_match_all($param_pattern,$comment,$param);

        is_array($title) && !empty($title) && $title = $title[1];
        is_array($method) && !empty($method) && $method = $method[1];
        is_array($url) && !empty($url) && $url = $url[1];
        is_array($param) && !empty($param) && $param = [$param[1],$param[2]];

        if($onlyTitle){
            return $title;
        }

        var_dump($param);die;

        return ['title'=>$title,'method'=>$method,'url'=>$url,'param'=>$param];
    }

}





