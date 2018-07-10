<?php
namespace mini;
// +----------------------------------------------------------------------
// | Author: jiexianluo@hotmail.com
// | Date  : 2018/7/10
// | Time  : 11:50
// +----------------------------------------------------------------------

class Controller
{
    protected $view;

    public function __construct()
    {
        $this->view = new View();
    }

    protected function success($msg,$url = '')
    {
        $this->_jump($msg,$_SERVER['HTTP_REFERER']);
    }

    protected function error($msg,$url = '')
    {
        $this->_jump($msg,$_SERVER['HTTP_REFERER']);
    }

    private function _jump($msg,$url)
    {
        ob_clean();
        header('Content-type:text/html;charset=utf-8');
        echo "<a href='{$url}'>{$msg}</a><span id='time' >3</span>秒后跳转。";
        echo "<script type='text/javascript'> var time = document.getElementById('time'); setInterval(function(){ time.innerHTML = parseInt(time.innerHTML) -1; if(time.innerHTML<1){ location.href='{$url}'}; },1000);</script>";
        die;
    }

    protected function assign($var,$value)
    {
        $this->view->assign($var,$value);
    }

    protected function display($template = '')
    {
        $this->view->display($template);
    }
}