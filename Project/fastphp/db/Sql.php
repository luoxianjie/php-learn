<?php
// +----------------------------------------------------------------------
// | Author: jiexianluo@hotmail.com
// | Date  : 2018/1/12
// | Time  : 10:50
// +----------------------------------------------------------------------
namespace fastphp\db;

class Sql
{
    protected $table;

    protected $primaryKey = 'id';

    private $filter = '';

    private $param = [];

    public function where($where = [],$param = [])
    {
        if ($where){
            $this->filter .= ' WHERE ';
            $this->filter .= implode(' ',$where);

            $this->param = $param;
        }

        return $this;
    }

    public function order($order = [])
    {
        if ($order){
           $this->filter .= " ORDERR BY ";
           $this->filter .= implode(',',$order);
        }

        return $this;
    }

    public function fetchAll()
    {
        $sql = sprintf("select * from `%s` %s",$this->table,$this->filter);
        $sth = Db::PDO()->prepare($sql);

        $sth = $this->formatParam($sth, $this->param);
        $sth->execute();

        return $sth->fetchAll();
    }

    public function fetch()
    {
        $sql = sprintf("select * from `%s` %s",$this->table,$this->filter);
        $sth = Db::PDO()->prepare($sql);

        $sth = $this->formatParam($sth, $this->param);
        $sth->execute();

        return $sth->fetch();
    }

    public function delete($id)
    {
        $sql = sprintf("delete from `%s` where `%s` = :%s", $this->table, $this->primary, $this->primary);
        $sth = Db::pdo()->prepare($sql);
        $sth = $this->formatParam($sth, [$this->primary => $id]);
        $sth->execute();

        return $sth->rowCount();
    }

    public function formatParam(PDOStatement $sth, $params = [])
    {
        foreach ($params as $param => &$value) {
            $param = is_int($param) ? $param + 1 : ':' . ltrim($param, ':');
            $sth->bindParam($param, $value);
        }

        return $sth;
    }

    // 新增数据
    public function add($data)
    {
        $sql = sprintf("insert into `%s` %s", $this->table, $this->formatInsert($data));
        $sth = Db::pdo()->prepare($sql);
        $sth = $this->formatParam($sth, $data);
        $sth = $this->formatParam($sth, $this->param);
        $sth->execute();

        return $sth->rowCount();
    }

    // 修改数据
    public function update($data)
    {
        $sql = sprintf("update `%s` set %s %s", $this->table, $this->formatUpdate($data), $this->filter);
        $sth = Db::pdo()->prepare($sql);
        $sth = $this->formatParam($sth, $data);
        $sth = $this->formatParam($sth, $this->param);
        $sth->execute();

        return $sth->rowCount();
    }

    private function formatInsert($data)
    {
        $fields = array();
        $names = array();
        foreach ($data as $key => $value) {
            $fields[] = sprintf("`%s`", $key);
            $names[] = sprintf(":%s", $key);
        }

        $field = implode(',', $fields);
        $name = implode(',', $names);

        return sprintf("(%s) values (%s)", $field, $name);
    }

    // 将数组转换成更新格式的sql语句
    private function formatUpdate($data)
    {
        $fields = array();
        foreach ($data as $key => $value) {
            $fields[] = sprintf("`%s` = :%s", $key, $key);
        }

        return implode(',', $fields);
    }
}