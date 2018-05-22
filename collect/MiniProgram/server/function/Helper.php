<?php
// +----------------------------------------------------------------------
// | Author: jiexianluo@hotmail.com
// | Date  : 2018/4/3
// | Time  : 10:20
// +----------------------------------------------------------------------

class Helper
{
    public static function dd(...$args)
    {
        ob_clean();
        echo "<pre>";
        foreach ($args as $arg){
            var_dump($arg);
        }
        die;
    }


    public static function jump($url, $msg)
    {
        ob_clean();
        echo "<a href='{$url}'>{$msg}</a><span id='time' >3</span>秒后跳转。";
        echo "<script type='text/javascript'> var time = document.getElementById('time'); setInterval(function(){ time.innerHTML = parseInt(time.innerHTML) -1; if(time.innerHTML<1){ location.href='{$url}'}; },1000);</script>";
        die;
    }


    public static function old($varname)
    {
        if(isset($_SESSION['old'][$varname])){
            return $_SESSION['old'][$varname];
        }else{
            return null;
        }
    }
}