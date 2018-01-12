<?php
// +----------------------------------------------------------------------
// | Author: jiexianluo@hotmail.com
// | Date  : 2018/1/12
// | Time  : 10:50
// +----------------------------------------------------------------------
namespace fastphp\base;

use fastphp\db\Sql;

class Model extends Sql
{
    protected $model;

    public function __construct()
    {
        if(!$this->table){
            $modelClass = get_class($this);

            $this->model = substr($this->model,0,-5);

            $this->table = strtolower($this->model);
        }
    }
}