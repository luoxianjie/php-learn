<?php
// +----------------------------------------------------------------------
// | Author: jiexianluo@hotmail.com
// | Date  : 2018/2/24
// | Time  : 17:53
// +----------------------------------------------------------------------
/**
 * 数据库操作类
 */
class Db
{

    private static $_instance;
    private $pdo;
    private $sql;

    private $table;
    private $where = [];
    private $whereStr = '1 = 1';

    private function __construct()
    {
        $type = 'mysql';
        $host = '127.0.0.1';
        $dbname = 'test';
        $user = 'root';
        $passwd = '';
        $this->pdo = new PDO("{$type}:host={$host};dbname={$dbname};charset=utf8",$user,$passwd);
    }

    public static function getInstance()
    {
        if(!self::$_instance){
            self::$_instance = new self();
        }

        return self::$_instance;
    }

    public function __clone()
    {
        die('cannot clone!');
    }

    public function where($where = [])
    {
        $this->where = $where;

        if(!empty($where)){
            foreach($where as $field => $value){
                $this->whereStr .= " AND  `{$field}` = :{$field}";
            }
        }

        return $this;
    }

    public function table($table)
    {
        $this->table = $table;
        return $this;
    }

    /**
     * $db->table('test')->where(['id'=>10])->find();
     *
     * @return mixed
     */
    public function find()
    {
        $this->sql = "SELECT * FROM `{$this->table}` WHERE {$this->whereStr}";

        $sth = $this->pdo->prepare($this->sql);
        $bindData = [];
        foreach ($this->where as $field => $value) {
            $bindData[':'.$field] = $value;
        }

        $sth->execute($bindData);

        $result = $sth->fetch(PDO::FETCH_ASSOC);

        return $result;
    }


    public function getLastSql()
    {
        return $this->sql;
    }

    /**
     * $db->table('test')->where(['sex'=>'male'])->select();
     *
     * @return array
     */
    public function select()
    {
        $this->sql = "SELECT * FROM `{$this->table}` WHERE {$this->whereStr}";

        $sth = $this->pdo->prepare($this->sql);
        $bindData = [];
        foreach ($this->where as $field => $value){
            $bindData[':'.$field] = $value;
        }

        $sth->execute($bindData);

        $result = $sth->fetchAll(PDO::FETCH_ASSOC);

        return $result;
    }

    /**
     * $db->exec('select * from test where id = :id',[':id'=>10]);
     *
     * @param $sql sql 语句
     * @param $data sql语句中对应绑定的数据
     * @return array
     */
    public function exec($sql,$data)
    {
        $this->sql = $sql;

        $sth = $this->pdo->prepare($sql);
        $sth->execute($data);
        $result = $sth->fetchAll(PDO::FETCH_ASSOC);

        return $result;
    }

    /**
     * $db->query('select * from test');
     *
     * @param $sql
     * @return array
     */
    public function query($sql)
    {
        $this->sql = $sql;

        $sth = $this->pdo->query($this->sql);
        $result = $sth->fetchAll(PDO::FETCH_ASSOC);

        return $result;
    }

    /**
     * $db->table('test')->where(['id'=>10])->update(['sex'=>'female']);
     *
     * @param array $data
     * @return int
     */
    public function update($data = [])
    {
        $updateStr = '';
        $fields = array_keys($data);

        foreach ($fields as $field){
            $updateStr .= "{$field} = :{$field},";
        }
        $updateStr = rtrim($updateStr,',');

        $this->sql = "UPDATE `{$this->table}` SET {$updateStr} WHERE {$this->whereStr}";

        $sth = $this->pdo->prepare($this->sql);

        $data = array_merge($this->where,$data);
        $bindData = [];
        foreach ($data as $field =>$value){
            $bindData[':'.$field] = $value;
        }
        $sth->execute($bindData);

        return $sth->rowCount();

    }

    /**
     * $db->table('test')->insert(['sex'=>'male','name'=>'jack']);
     *
     * @param array $data
     * @return int
     */
    public function insert($data = [])
    {
        $fields = array_keys($data);
        $value = array_values($data);

        $insertStr = '(';
        $insertStr .= implode($fields,',');
        $insertStr .= ') VALUES (';
        $insertStr .= '\''.implode($value,'\',\'').'\'';
        $insertStr .= ')';

        $this->sql = "INSERT INTO `{$this->table}` {$insertStr} ";

        $sth = $this->pdo->prepare($this->sql);

        $bindData = [];
        foreach ($data as $field =>$value) {
            $bindData[':'.$field] = $value;
        }

        $sth->execute($bindData);
        return $sth->rowCount();
    }


    /**
     * $db->table('test')->delete(['id'=>10]);
     * $db->table('test')->where(['id'=>10])->delete();
     *
     * @return int
     */
    public function delete($data = [])
    {
        if(empty($this->where) && empty($data)){
            die('condition cannot be null!');
        }

        $data = array_merge($this->where,$data);
        $whereStr = '';
        foreach ($data as $field =>$value){
            $whereStr .= "{$field} = :{$field},";
        }
        $whereStr = rtrim($whereStr,',');

        $this->sql = "DELETE FROM `{$this->table}` WHERE {$whereStr}";
        $sth = $this->pdo->prepare($this->sql);

        $bindData = [];
        foreach ($data as $field => $value){
            $bindData[':'.$field] = $value;
        }

        $sth->execute($bindData);

        return $sth->rowCount();

    }

}


//$db = Db::getInstance();

// 增
//$db->table('test')->insert(['sex'=>'male','name'=>'tom']);

// 删
//$res = $db->table('test')->delete(['id'=>1]);
//$db->table('test')->where(['id'=>2])->delete();

// 改
//$db->table('test')->where(['id'=>3])->update(['sex'=>'female']);

// 查
//$res = $db->table('test')->where(['id'=>4])->find();
//$res = $db->table('test')->where(['name'=>'tom'])->select();

// 执行sql
//$res = $db->exec('select * from test where id = :id',[':id'=>4]);
//$res = $db->query('select * from test');

//$sql = $db->getLastSql();
//var_dump($sql,$res);