<?php

$conn_args = [
    'host'  => '192.168.234.130',
    'port'  => '5672',
    'login' => 'lxj',
    'password'  => '1994319',
    'vhost' => '/'
];

$exchange_name = 'test';      //交换机名
$queue_name = 'test';      //队列名
$route_key = 'key_1';   //路由key

//创建连接和channel
$conn = new AMQPConnection($conn_args);
if(!$conn->connect()){
    die('cannot connect to the broker!');
}
$channel = new AMQPChannel($conn);

//创建交换机
$exchange = new AMQPExchange($channel);
$exchange->setName($exchange_name);
$exchange->setType(AMQP_EX_TYPE_DIRECT);
$exchange->setFlags(AMQP_DURABLE); //持久化
echo "Exchange Status:".$exchange->declare()."\n";

//创建队列
$queue = new AMQPQueue($channel);
$queue->setName($queue_name);
$queue->setFlags(AMQP_DURABLE); //持久化
echo "Message Total:".$queue->declare()."\n";

//绑定交换机与队列，并指定路由键
echo 'Queue Bind: '.$queue->bind($exchange_name, $route_key)."\n";


//阻塞模式接收消息
echo "Message:\n";
while (true) {
    $queue->consume('processMessage', AMQP_AUTOACK); //自动ACK应答
}
$conn->disconnect();

/**
 * 消费回调函数
 * 处理消息
 */
function processMessage($envelope, $queue) {
    $msg = $envelope->getBody();
    echo $msg."\n"; //处理消息
}