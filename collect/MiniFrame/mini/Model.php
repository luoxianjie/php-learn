<?php
namespace mini;
// +----------------------------------------------------------------------
// | Author: jiexianluo@hotmail.com
// | Date  : 2018/7/10
// | Time  : 11:49
// +----------------------------------------------------------------------

class Model
{
    public $conn;
    public $table = '';
    public $db_prefix = '';

    public function __construct()
    {
        $table = strtolower(substr(get_class($this),strpos(get_class($this),'\\')+1));
        if(empty($this->table))
        {
            $this->table = $this->db_prefix.$table;
        }else{
            $this->table = $this->db_prefix.$this->table;
        }
        $this->table($this->table);
    }

    public function table($table)
    {
        $this->conn = DB::getInstance();
        $this->conn = $this->conn->table($this->table);
        return  $this->conn;
    }

    public function where($where = [])
    {
        return $this->conn->where($where);
    }

    public function find()
    {
        return $this->conn->find();
    }

    public function select()
    {
        return $this->conn->select();
    }
}