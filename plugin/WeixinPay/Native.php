<?php

// +----------------------------------------------------------------------
// | Author: jiexianluo@hotmail.com
// | Date  : 2018/3/2
// | Time  : 10:42
// +----------------------------------------------------------------------
namespace WeixinPay;

use QRcode\QRcode;
use WeixinPay\data\WxPayUnifiedOrder;
use WeixinPay\lib\NativePay;
use WeixinPay\lib\WxPayConfig;

/**
 * 微信支付之扫码支付示例
 *
 * Class WeixinPay
 */
class Native
{

    /**
     * 生成支付二维码
     */
    public function QRCode()
    {
        ob_clean();
        $data = $this->getCodeUrl();
        QRcode::png($data);
    }

    /**
     * 异步通知
     */
    public function notify()
    {
        // 改变预支付订单状态

        // 改变订单状态
    }

    /**
     * ajax 检测订单状态
     */
    public function checkOrderStatus()
    {
        // ajax 检测订单状态
    }

    /**
     * 获取code_url
     */
    private function getCodeUrl()
    {
        ini_set('date.timezone','Asia/Shanghai');
        // 统一下单
        $input = new WxPayUnifiedOrder();
        $input->SetBody("test");
        $input->SetAttach("test");
        $input->SetOut_trade_no(WxPayConfig::MCHID.date("YmdHis"));
        $input->SetTotal_fee("1");
        $input->SetTime_start(date("YmdHis"));
        $input->SetTime_expire(date("YmdHis", time() + 600));
        $input->SetGoods_tag("test");
        $input->SetNotify_url("http://paysdk.weixin.qq.com/example/notify.php");
        $input->SetTrade_type("NATIVE");
        $input->SetProduct_id("123456789");

        // 本地生成预支付订单
        $this->prepaidOrder();

        // 获取code_url
        $notify = new NativePay();
        $result = $notify->GetPayUrl($input);
        $data = $result["code_url"];

        return $data;
    }

    /**
     * 本地生成预支付订单
     */
    private function prepaidOrder()
    {
        return true;
    }



}