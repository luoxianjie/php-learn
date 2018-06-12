<?php
header('Access-Control-Allow-Origin:*');

//require '../vendor/autoload.php';
require 'InterfaceDoc.php';

// 接口所在目录
define('__INTERFACE_DIR__',__DIR__.'/Interface');
// 接口生成的markdown文件路径
define('__MARKDOWN__','./doc.md');
// 接口地址前缀
define('__INTERFACE_URI__','http://hqpcb.qaulau.net/weapp');

InterfaceDoc::handle();

