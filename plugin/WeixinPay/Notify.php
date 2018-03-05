<?php

namespace WeixinPay;

// +----------------------------------------------------------------------
// | Author: jiexianluo@hotmail.com
// | Date  : 2018/3/5
// | Time  : 11:49
// +----------------------------------------------------------------------

use WeixinPay\lib\WxPayNotify;

/**
 * 微信支付之扫码支付回调示例
 *
 * Class WeixinPay
 */
class Notify extends WxPayNotify
{
    /**
     * 异步通知处理
     */
    public function notify()
    {
        $this->handle();
    }

    public function NotifyProcess()
    {
        // 改变预支付订单状态

        // 改变订单状态
    }

}