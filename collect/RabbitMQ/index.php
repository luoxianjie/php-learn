<?php

$conn_args = [
    'host'  => '192.168.234.130',
    'port'  => '5672',
    'login' => 'lxj',
    'password'  => '1994319',
    'vhost' => '/'
];

$e_name = 'test';
$k_route = 'key_1';

//创建连接和channel
$conn = new AMQPConnection($conn_args);
if(!$conn->connect()){
    die('cannot connect to the broker!');
}
$channel = new AMQPChannel($conn);


//创建交换机
$exchange = new AMQPExchange($channel);
$exchange->setName($e_name);
$exchange->setType(AMQP_EX_TYPE_DIRECT);
$exchange->setFlags(AMQP_DURABLE); //持久化
$exchange->declare();


//发送消息
for($i=0; $i<5; ++$i){
    sleep(1);
    echo "Send Message:".$exchange->publish("TEST MESSAGE" . date('H:i:s', time()), $k_route)."\n";
}

$conn->disconnect();