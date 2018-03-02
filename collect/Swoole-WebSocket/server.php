<?php
// +----------------------------------------------------------------------
// | Author: jiexianluo@hotmail.com
// | Date  : 2018/1/4
// | Time  : 17:24
// +----------------------------------------------------------------------

$ws = new swoole_websocket_server("192.168.234.3", 9502);

//监听WebSocket连接打开事件
$ws->on('open', function ($ws, $request) {
    $ws->push($request->fd, "hello, welcome\n");
});

//监听WebSocket消息事件
$ws->on('message', function ($ws, $frame) {
    foreach($ws->connections as $fd) {
        if($fd != $frame->fd)
            $ws->push($fd, "{$fd}: {$frame->data}");
    }
});

$ws->start();