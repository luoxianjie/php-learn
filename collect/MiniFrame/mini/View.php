<?php
namespace mini;
// +----------------------------------------------------------------------
// | Author: jiexianluo@hotmail.com
// | Date  : 2018/7/10
// | Time  : 11:49
// +----------------------------------------------------------------------

class View
{
    public function assign($var,$value)
    {
        $this->var[$var] = $value;
    }

    public function display($template = '')
    {
        $template = empty($template)?__ACTION__:$template;
        $template = __ROOT__."/view/".__CONTROLLER__."/".$template.'.html';
        if(!is_file($template)){
            trigger_error($template.'模板文件不存在!');
        }
        ob_start();
        ob_implicit_flush(1);
        extract($this->var);
        require $template;
        $content = ob_get_clean();
        header('Content-Type:text/html; charset=utf8');
        header('X-Powered-By:ThinkPHP');
        echo $content;
    }

}