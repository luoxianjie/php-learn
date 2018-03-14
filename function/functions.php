<?php
// +----------------------------------------------------------------------
// | Author: jiexianluo@hotmail.com
// | Date  : 2018/3/14
// | Time  : 15:38
// +----------------------------------------------------------------------
/**
 * ### getBaseUrl function
 * // utility function that returns base url for
 * // determining return/cancel urls
 *
 * @return string
 */
function getBaseUrl()
{
    if (PHP_SAPI == 'cli') {
        $trace=debug_backtrace();
        $relativePath = substr(dirname($trace[0]['file']), strlen(dirname(dirname(__FILE__))));
        echo "Warning: This sample may require a server to handle return URL. Cannot execute in command line. Defaulting URL to http://localhost$relativePath \n";
        return "http://localhost" . $relativePath;
    }
    $protocol = 'http';
    if ($_SERVER['SERVER_PORT'] == 443 || (!empty($_SERVER['HTTPS']) && strtolower($_SERVER['HTTPS']) == 'on')) {
        $protocol .= 's';
    }
    $host = $_SERVER['HTTP_HOST'];
    $request = $_SERVER['PHP_SELF'];
    return dirname($protocol . '://' . $host . $request);
}


/**
 * 数组转树状结构
 * @param array $data   要转化的数组
 * @param int $pid      父级id
 * @param int $level    层级
 * @return array|void   转化后的树状结构
 */
function array2tree($data = [],$pid = 0,$level = 1)
{
    if(empty($data)) {
        return ;
    }
    $data = array_column($data,null,'id');
    ksort($data);

    $tree = [];
    foreach($data as $value){
        if($value['pid'] == $pid){
            $value['level'] = $level;
            $value['child'] = array2tree($data,$value['id'],++$level);
            $tree[] = $value;
        }
    }
    return $tree;
}

/**
 * 树状结构转数组
 * @param array $tree     树状结构
 * @param string $prefix  层级前缀
 * @return array|void     输出数组
 */
function tree2array($tree = [],$prefix='|-')
{
    if(empty($tree)){
        return ;
    }

    $data = [];
    foreach($tree as $value) {
        $value['html'] = str_pad($prefix, $value['level'], ' ', STR_PAD_LEFT);
        $child = $value['child'];
        unset($value['child']);
        $data[] = $value;

        if (!empty($child)) {
            $data = array_merge($data,tree2array($child));
        }
    }
    return $data;
}

// 浏览器友好的变量输出
function dump($var, $echo=true, $label=null, $strict=true) {
    $label = ($label === null) ? '' : rtrim($label) . ' ';
    if (!$strict) {
        if (ini_get('html_errors')) {
            $output = print_r($var, true);
            $output = "<pre>" . $label . htmlspecialchars($output, ENT_QUOTES) . "</pre>";
        } else {
            $output = $label . print_r($var, true);
        }
    } else {
        ob_start();
        var_dump($var);
        $output = ob_get_clean();
        if (!extension_loaded('xdebug')) {
            $output = preg_replace("/\]\=\>\n(\s+)/m", "] => ", $output);
            $output = '<pre>' . $label . htmlspecialchars($output, ENT_QUOTES) . '</pre>';
        }
    }
    if ($echo) {
        echo($output);
        return null;
    }else
        return $output;
}