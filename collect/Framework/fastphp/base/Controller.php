<?php
// +----------------------------------------------------------------------
// | Author: jiexianluo@hotmail.com
// | Date  : 2018/1/12
// | Time  : 10:49
// +----------------------------------------------------------------------
namespace fastphp\base;

class Controller
{
    protected $_controller;
    protected $_action;
    protected $_view;

    public function __construct($controller,$action)
    {
        $this->_controller = $controller;
        $this->_action = $action;
        $this->_view = new View($controller,$action);
    }

    public function assign($name,$value)
    {
        $this->_view->assign($name,$value);
    }

    public function render()
    {
        $this->_view->render();
    }
}