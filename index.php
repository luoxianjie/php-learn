<?php

require './vendor/autoload.php';

$config = [
    'rw_sep'    => true,
    'slave'     =>[
        ['type'=>'mysql','host'=>'192.168.234.130','dbname'=>'test','user'=>'root','passwd'=>'123456','port'=>'3308'],
        ['type'=>'mysql','host'=>'192.168.234.130','dbname'=>'test','user'=>'root','passwd'=>'123456','port'=>'3309'],
    ],
    'master'    =>[
        ['type'=>'mysql','host'=>'192.168.234.130','dbname'=>'test','user'=>'root','passwd'=>'123456','port'=>'3306'],
        ['type'=>'mysql','host'=>'192.168.234.130','dbname'=>'test','user'=>'root','passwd'=>'123456','port'=>'3307'],
    ]
];

$config1 = ['type'=>'mysql','host'=>'192.168.234.130','dbname'=>'test','user'=>'root','passwd'=>'123456','port'=>'3306'];

$db = Db::getInstance($config1);

$res = $db->table('user')->where(['id'=>2])->find();

dd($res);