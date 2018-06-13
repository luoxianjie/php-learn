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
    private $master_pdo;
    private $slave_pdo;
    private $pdo;
    private $sql;

    private $table;
    private $where = [];
    private $whereStr = '1 = 1';

    /**
     * 构造方法
     * 初始化pdo
     *
     * Db constructor.
     * @param array $config
     */
    private function __construct($config = [])
    {
        $this->_pdo($config);
    }

    /**
     * 初始化pdo
     * @param $config
     */
    public function _pdo($config)
    {
        if(isset($config['rw_sep']) && $config['rw_sep'] === true){
            $this->_initMaster($config);
            $this->_initSlave($config);
        }else{
            $this->_initPDO($config);
        }
    }

    /**
     * 解析配置信息
     * @param array $config
     * @return array
     */
    private function _parseConfig($config = [])
    {
        $conf['type']     = 'mysql';
        $conf['host']     = '127.0.0.1';
        $conf['dbname']   = 'test';
        $conf['user']     = 'root';
        $conf['passwd']   = '';
        $conf['port']     = '3306';

        $config = array_merge($conf,$config);
        return  $config;
    }

    /**
     * 无读写分离情况下初始化pdo
     *
     * @param array $config
     * @param string $pdo
     */
    private function _initPDO($config = [],$pdo = 'pdo')
    {
        $config = $this->_parseConfig($config);
        try {
            $this->$pdo = new PDO("{$config['type']}:host={$config['host']};port={$config['port']};dbname={$config['dbname']};charset=utf8", $config['user'], $config['passwd'],[PDO::MYSQL_ATTR_INIT_COMMAND=>'SET NAMES utf8']);
            $this->$pdo->exec('set names utf8');
        }catch (Exception $e){
            header('Content-type:text/html;charset=utf8');
            die($e->getMessage());
        }
    }

    /**
     * 初始化 奴隶数据库 pdo
     *
     * @param array $config
     */
    private function _initSlave($config = [])
    {
        if(isset($config['slave'])){
            if(isset($config['slave']['host'])){
                // 单主模式
                $this->_initPDO($config['slave'],'slave_pdo');
            }else{
                // 多主模式

                // 获取随机主机Id
                $host_id = $this->_getRandHostId($config['slave']);
                $this->_initPDO($config['slave'][$host_id], 'slave_pdo');
            }
        }
    }

    /**
     * 初始化 主数据库 pdo
     *
     * @param array $config
     */
    private function _initMaster($config = [])
    {
        if(isset($config['master'])){
            if(isset($config['master']['host'])){
                // 单主模式
                $this->_initPDO($config['master'],'master_pdo');
            }else{
                // 多主模式

                // 获取随机主机Id
                $host_id = $this->_getRandHostId($config['master']);
                $this->_initPDO($config['master'][$host_id], 'master_pdo');
            }
        }
    }

    /**
     * 多主机情况随机获取单个主机
     *
     * @param array $host
     * @return int
     */
    private function _getRandHostId($host = [])
    {
        $count = count($host);
        return mt_rand(0,$count-1);
    }

    /**
     * 单例模式获取类实例
     *
     * @return Db|null
     */
    public static function getInstance($config = [])
    {
        if(!self::$_instance instanceof self){
            self::$_instance = new self($config);
        }

        return self::$_instance;
    }

    public function __clone()
    {
        die('cannot clone!');
    }

    /**
     * where 条件
     *
     * @param array $where
     * @return $this
     */
    public function where($where = [])
    {
        $this->where = $where;
        $this->whereStr = '1 = 1';

        if(!empty($where)){
            foreach($where as $field => $value){
                $this->whereStr .= " AND  `{$field}` = :{$field}";
            }
        }
        return $this;
    }

    /**
     * 指定数据表
     *
     * @param $table
     * @return $this
     */
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
        $sql = "SELECT * FROM `{$this->table}` WHERE {$this->whereStr}";

        $bindData = [];
        foreach ($this->where as $field => $value) {
            $bindData[':'.$field] = $value;
        }

        $result = $this->query($sql,$bindData);

        if(is_array($result)){
            return $result[0];
        }

        return null;
    }


    /**
     * 获取执行的sql
     *
     * @return mixed
     */
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
        $sql = "SELECT * FROM `{$this->table}` WHERE {$this->whereStr}";

        $bindData = [];
        foreach ($this->where as $field => $value){
            $bindData[':'.$field] = $value;
        }

        $result = $this->query($sql,$bindData);
        return $result;
    }

    /**
     * $db->exec('select * from test where id = :id',[':id'=>10]);
     *
     * @param $sql sql 语句
     * @param $data sql语句中对应绑定的数据
     * @return array
     */
    public function query($sql, $data = [])
    {
        if(!empty($this->slave_pdo)){
            $this->pdo = $this->slave_pdo;
        }

        $this->sql = $sql;
        $sth = $this->pdo->prepare($sql);
        $sth->execute($data);
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

        $data = array_merge($this->where, $data);
        $bindData = [];
        foreach ($data as $field => $value){
            $bindData[':'.$field] = $value;
        }

        return $this->execute($bindData);
    }

    /**
     * $db->table('test')->insert(['sex'=>'male','name'=>'jack']);
     *
     * @param array $data
     * @return int
     */
    public function insert($data = [])
    {
        if (!is_array($data)){
            die('param should be an array');
        }
        $fields = array_keys($data);
        $value = array_values($data);

        $insertStr = '(';
        $insertStr .= implode($fields,',');
        $insertStr .= ') VALUES (';
        $insertStr .= '\''.implode($value,'\',\'').'\'';
        $insertStr .= ')';

        $this->sql = "INSERT INTO `{$this->table}` {$insertStr} ";

        $bindData = [];
        foreach ($data as $field => $value) {
            $bindData[':'.$field] = $value;
        }

        return $this->execute($bindData);
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

        $data = array_merge($this->where, $data);
        $whereStr = '';
        foreach ($data as $field => $value){
            $whereStr .= "{$field} = :{$field},";
        }
        $whereStr = rtrim($whereStr,',');

        $this->sql = "DELETE FROM `{$this->table}` WHERE {$whereStr}";

        $bindData = [];
        foreach ($data as $field => $value){
            $bindData[':'.$field] = $value;
        }

        return $this->execute($bindData);
    }


    /**
     * 执行原生pdo
     *
     * @param $bindData
     * @return int
     */
    public function execute($bindData)
    {
        if(!empty($this->master_pdo)){
            $this->pdo = $this->master_pdo;
        }

        try{
            $sth = $this->pdo->prepare($this->sql);
            $sth->execute($bindData);
            return $sth->rowCount();
        }catch (Exception $e){
            die($e->getMessage());
        }
    }

}


// 多数据库配置
/*$config = [
    'rw_sep'    => true,
    'slave'     =>[
        ['type'=>'mysql','host'=>'192.168.234.130','dbname'=>'test','user'=>'root','passwd'=>'123456','port'=>'3308'],
        ['type'=>'mysql','host'=>'192.168.234.130','dbname'=>'test','user'=>'root','passwd'=>'123456','port'=>'3309'],
    ],
    'master'    =>[
        ['type'=>'mysql','host'=>'192.168.234.130','dbname'=>'test','user'=>'root','passwd'=>'123456','port'=>'3306'],
        ['type'=>'mysql','host'=>'192.168.234.130','dbname'=>'test','user'=>'root','passwd'=>'123456','port'=>'3307'],
    ]
];*/

// 单数据库配置
//$config = ['type'=>'mysql','host'=>'192.168.234.130','dbname'=>'test','user'=>'root','passwd'=>'123456','port'=>'3306'];

//$db = Db::getInstance($config);

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
//$res = $db->query('select * from test where id = :id',[':id'=>4]);
//$res = $db->query('select * from test');

//$sql = $db->getLastSql();
//var_dump($sql,$res);