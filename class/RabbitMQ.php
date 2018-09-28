<?php
// +----------------------------------------------------------------------
// | Author: jiexianluo@hotmail.com
// | Date  : 2018/9/27
// | Time  : 14:46
// +----------------------------------------------------------------------


/**
 *  RabbitMQ 慨念说明
 *  Exchange：消息交换机，它指定消息按什么规则，路由到哪个队列。
 *　Queue：消息队列载体，每个消息都会被投入到一个或多个队列。
 *　Binding：绑定，它的作用就是把exchange和queue按照路由规则绑定起来。
 *　Routing Key：路由关键字，exchange根据这个关键字进行消息投递。
 *　vhost：虚拟主机，一个broker里可以开设多个vhost，用作不同用户的权限分离。
 *　producer：消息生产者，就是投递消息的程序。
 *　consumer：消息消费者，就是接受消息的程序。
 *　channel：消息通道，在客户端的每个连接里，可建立多个channel，每个channel代表一个会话任务。
 *
 *
 *
 *  对于生产者 只需要创建交换机 如果其申明类型是直达型，则还需在发布时指定路由key，如果是扩散型则无需
 *  对于消费者 需要创建并声明交换机、队列，同时还需将两者绑定，同时指定路由key 消费者一般通过定时脚本后台执行
 */
class RabbitMQ
{

    public $host;           // 主机
    public $port;           // 端口号
    public $login;          // 登录名
    public $password;       // 密码
    public $vhost = '/';    // 虚拟机名

    public $conn;

    public $exchange_name = 'test_exchange';
    public $route_key = 'test_route';

    /**
     * 构造函数
     * RabbitMQ constructor.
     */
    public function __construct($config = [])
    {
        $conf = [
            'host'  => $this->host,
            'port'  => $this->port,
            'login' => $this->login,
            'password'  => $this->password,
            'vhost' => $this->vhost
        ];
        $config = array_merge($conf,$config);
        try {
            $this->conn = new AMQPConnection($config);
            // 建立连接
            if (!$this->conn->connect()) {
                die('Cannot connect to the borker!');
            }
        }catch (Exception $e){
            die($e->getMessage());
        }
    }


    /**
     * 生产者
     */
    public function publish($message,$queue_name = ''){
        $exchange_name = $this->exchange_name;
        $route_key = $this->route_key;

        try {
            // 创建通道
            $channel = new AMQPChannel($this->conn);
            // 创建交换机
            $exchange = new AMQPExchange($channel);
            // 设置交换机名称
            $exchange->setName($exchange_name);

            // 发布消息
            $exchange->publish($message,$route_key);
        }catch (Exception $e){
            die($e->getMessage());
        }
    }

    /**
     * 消费者
     */
    public function consume($queue_name){
        $exchange_name = $this->exchange_name;
        $route_key = $this->route_key;
        // 创建通道
        $channel = new AMQPChannel($this->conn);
        // 创建交换机
        $exchange = new AMQPExchange($channel);
        // 设置交换机名称
        $exchange->setName($exchange_name);
        // 设置交换机类型
        //$exchange->setType(AMQP_EX_TYPE_FANOUT);     // 扩散型 对应该交换机下所有队列
        $exchange->setType(AMQP_EX_TYPE_DIRECT);   // 直达型 对应一个队列 发布时需要申明路由key
        // 声明交换机
        $exchange->declareExchange();
        // 创建队列
        $queue = new AMQPQueue($channel);
        // 设置对列名
        $queue->setName($queue_name);
        // 设置队列持久化
        $queue->setFlags(AMQP_DURABLE);
        // 声明队列
        $queue->declareQueue();
        // 绑定交换机与路由
        $queue->bind($exchange_name,$route_key);
        while (true){
            // 获取队列消息，并处理
            $queue->consume(function ($envelope, $queue){
                // 获取消息
                $msg = $envelope->getBody();
                var_dump(" [x] Received:" . $msg);
                // 处理完成，手动发送ack应答，清除此消息
                $queue->ack($envelope->getDeliveryTag());
            });
            // 当有任务在处理时，不再接受消息
            $channel->qos(0,1);
        }
    }
}