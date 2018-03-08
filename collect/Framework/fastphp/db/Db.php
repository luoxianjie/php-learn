<?php
// +----------------------------------------------------------------------
// | Author: jiexianluo@hotmail.com
// | Date  : 2018/1/12
// | Time  : 10:50
// +----------------------------------------------------------------------
namespace fastphp\db;

class Db
{
    private static $pdo;

    public static function PDO()
    {
        if(!self::$pdo){
            return self::$pdo;
        }

        try {
            $dsn    = sprintf('mysql:host=%s;dbname=%s;charset=utf8', DB_HOST, DB_NAME);
            $option = array(\PDO::ATTR_DEFAULT_FETCH_MODE => \PDO::FETCH_ASSOC);

            return self::$pdo = new PDO($dsn, DB_USER, DB_PASS, $option);
        } catch (\PDOException $e) {
            exit($e->getMessage());
        }
    }

}