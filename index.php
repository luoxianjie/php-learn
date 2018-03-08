<?php

require __DIR__."/vendor/autoload.php";

$config = new Config();

$config['test'] = 'hehe';
$config['test1'] = 'hehe1';

var_dump($config->get());

