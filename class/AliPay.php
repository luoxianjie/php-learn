<?php
// +----------------------------------------------------------------------
// | Author: jiexianluo@hotmail.com
// | Date  : 2018/3/15
// | Time  : 9:37
// +----------------------------------------------------------------------


class AliPay
{

    private $config = [];

    public function __construct()
    {
        $this->config = [
            'app_id'                =>  '2016091100490007',
            'merchant_private_key'  =>  'MIIEowIBAAKCAQEAvppKn+FOTNkZ7uqYfqwUPBAd9rLF6Wrc6JvJRF2MJMEvoV3MfSAS0daWJ8Bu0FMA7sWscClSosQMgHxgQQKjmGEcgLSF5FRc6VZQYD744y0ZgUx65iMJSe5djkKGtqo2MPE+wbw4dToDz2KfGO+deU2uM10Sw+snr0Jr/5Rvf95Q8tkMRD+q4+gy33hCmBHTBhm00Kz1BelZQCgY7HxlVFfYcUnx8na1ykMUWdqCp7lphLsd1n1v5gAB1GW1Qtc7b+MVSEkhjkrigozgs7z5BODrUc+nuPGmlaYJi5janHSD8nYae4RCsCXG603kxGVQZcZBXQePh8vMwQSgyT19nQIDAQABAoIBACRgljWFbshD9yseIxSqCwKboNxgql0uRBMxCGy/3et1431MxaZr5Kuf5PCZTYz9CoSHva16dYcTG7+5/TTrKAYBIst9qMA3SbRPAPAdlKrnBKMk0Z/yt+cgU0K/d66NNeKJaIzZO31EIU4HaI39zXRFWyX6cYZq7xwH9UzGK7GfjCzsq8QpWcjDSrRv6PJb8MRmNcM66cs2Cdqij3zIzDUALkXRr6wrXO7WffIn7YmN7Nyw5MjEHmnb/BV1t3zsodkETRIC7p4UM48nYFHaE6RhQ8CjmxP8ftmLNpR1/bCkCZVxZ3jQ//F5/LtvrLO/TQFsbgrz2nLMLzsyJHtGCgECgYEA6KsmIVuEYhDtb+gnPSdRD1w9EKP0+jlPMphSS8f+3OJkWqH51T7Uya7NCFpseu7+83UBjGaleTevKQba+fWdkADhuDMmcRilO65ohq6fBpJ2AkhPTV53/0okof5nOV34ruhvlXJ3sJEEUFBzQaZ4ZIZtmlSNsqDYTBGXtpSDHH0CgYEA0bdEYdh/uhXs2uCA96Pm7rgLwLeNYzgEEN1zMHYbRYXBUkwUe2JwiJfUbJaZ1epu2RFTh+gBu50oCXwL+y+rKc2uonV+Xj5pHjRwqbvg198ehm33T/bu1HGyCATKbsKANR8WDkgozLApHRgvXghP9zy6VNnFzq9PJyKA0LdWT6ECgYEAwvnp0jpnDecxkn3xpAW2oDCj27YKLUapX7TWAjtvEJuHjYv/WPx2RSz/FOjxz3NIo6yBx66dLJ/FYZQlHSL/DxYuEbaLmGsWzJSzc2oSSeeijPcbvbJUzNLNMBOJXUGqjKisGYj7VDOycbt5WqKucU9Vuebxd6fimpyDjrWVyeECgYBjwW9Ps7IeDCvceYofQeGpj/ZXN3iTx6N3ej8+wA985IDu6q2be1nYP5CWDpUigh80Nd+r+BpK6hiPWmhAlY4vll0JfiYcIkfSTFODCZGMXd+hRlGrweQdPe/XjJ/WS8K8ggr1xDFUHD+STVzKt92B12syzVwgUpeuH+VyYqM1wQKBgFOy0+oZfgrMWFygxiaoClDIte5lCTj5qNLhfP0/BLviXIEYe0LZb2c2gumVORtz6V2XqqStizjhfcfLLYZ9Vz5n82jEEESpG3uluogCOwdmhE+ubZRSTQlIUKt0XaiU/crc0C/Qlg5IoBPJq+win/7mJGEAnGrEx+snL+jP3bCt',
            'alipay_public_key'     =>  'MIIBIjANBgkqhkiG9w0BAQEFAAOCAQ8AMIIBCgKCAQEAwSCMA78pBGXND/j1NgNsLAdBX2zGcmSvzYiUG8DCaOmdU0OJOCyG5lseeHYPXthJT0ZRK1DnUueEJ9p9uTS8ivpqZfdWghPag/6k3oiM7iEYZphUcZ11nJHmKQyb9lshyEM3wufCA6hJ4aGlcabkcmfYxmmjNVX1f+s1JGT7YYVvy7uytf1qPy7YKv5ihSTfKzk5jJVVgtxw/Lp0Da5ZbbkdT2iteGc1ChSEjwcsBsglTPDYkdb5JgwjnmmSBDYiobfHwb52437gFlBr2V8i/de3CeitDAYxAKuMkQear21c+RwPbMXNGdLYYqI7Xm1Taf4WWknoI362Xb0HfU2k0QIDAQAB',
            'notify_url'            =>  '',
            'return_url'            =>  '',
            'charset'               =>  'UTF-8',
            'sign_type'             =>  'RSA2',
            'gatewayUrl'            =>  'https://openapi.alipaydev.com/gateway.do'
        ];
    }

    public function pagepay()
    {
        //商户订单号，商户网站订单系统中唯一订单号，必填
        $out_trade_no = trim($_POST['WIDout_trade_no']);

        //订单名称，必填
        $subject = trim($_POST['WIDsubject']);

        //付款金额，必填
        $total_amount = trim($_POST['WIDtotal_amount']);

        //商品描述，可空
        $body = trim($_POST['WIDbody']);

        //构造参数
        $payRequestBuilder = new \AliPay\Pagepay\Buildermodel\AlipayTradePagePayContentBuilder();
        $payRequestBuilder->setBody($body);
        $payRequestBuilder->setSubject($subject);
        $payRequestBuilder->setTotalAmount($total_amount);
        $payRequestBuilder->setOutTradeNo($out_trade_no);

        $aop = new \AliPay\Pagepay\Service\AlipayTradeService($this->config);

        /**
         * pagePay 电脑网站支付请求
         * @param $builder 业务参数，使用buildmodel中的对象生成。
         * @param $return_url 同步跳转地址，公网可以访问
         * @param $notify_url 异步通知地址，公网可以访问
         * @return $response 支付宝返回的信息
         */
        $response = $aop->pagePay($payRequestBuilder, $this->config['return_url'], $this->config['notify_url']);

        //输出表单
        var_dump($response);
    }

    public function query()
    {
        //商户订单号，商户网站订单系统中唯一订单号
        $out_trade_no = trim($_POST['WIDTQout_trade_no']);

        //支付宝交易号
        $trade_no = trim($_POST['WIDTQtrade_no']);
        //请二选一设置
        //构造参数
        $RequestBuilder = new \AliPay\Pagepay\Buildermodel\AlipayTradeQueryContentBuilder();
        $RequestBuilder->setOutTradeNo($out_trade_no);
        $RequestBuilder->setTradeNo($trade_no);

        $aop = new \AliPay\Pagepay\Service\AlipayTradeService($this->config);

        /**
         * alipay.trade.query (统一收单线下交易查询)
         * @param $builder 业务参数，使用buildmodel中的对象生成。
         * @return $response 支付宝返回的信息
         */
        $response = $aop->Query($RequestBuilder);
        var_dump($response);
    }

    public function refund()
    {
        //商户订单号，商户网站订单系统中唯一订单号
        $out_trade_no = trim($_POST['WIDTRout_trade_no']);

        //支付宝交易号
        $trade_no = trim($_POST['WIDTRtrade_no']);
        //请二选一设置

        //需要退款的金额，该金额不能大于订单金额，必填
        $refund_amount = trim($_POST['WIDTRrefund_amount']);

        //退款的原因说明
        $refund_reason = trim($_POST['WIDTRrefund_reason']);

        //标识一次退款请求，同一笔交易多次退款需要保证唯一，如需部分退款，则此参数必传
        $out_request_no = trim($_POST['WIDTRout_request_no']);

        //构造参数
        $RequestBuilder=new \AliPay\Pagepay\Buildermodel\AlipayTradeRefundContentBuilder();
        $RequestBuilder->setOutTradeNo($out_trade_no);
        $RequestBuilder->setTradeNo($trade_no);
        $RequestBuilder->setRefundAmount($refund_amount);
        $RequestBuilder->setOutRequestNo($out_request_no);
        $RequestBuilder->setRefundReason($refund_reason);

        $aop = new \AliPay\Pagepay\Service\AlipayTradeService($this->config);

        /**
         * alipay.trade.refund (统一收单交易退款接口)
         * @param $builder 业务参数，使用buildmodel中的对象生成。
         * @return $response 支付宝返回的信息
         */
        $response = $aop->Refund($RequestBuilder);
        var_dump($response);;
    }

    public function refundQuery()
    {
        //商户订单号，商户网站订单系统中唯一订单号
        $out_trade_no = trim($_POST['WIDRQout_trade_no']);

        //支付宝交易号
        $trade_no = trim($_POST['WIDRQtrade_no']);
        //请二选一设置

        //请求退款接口时，传入的退款请求号，如果在退款请求时未传入，则该值为创建交易时的外部交易号，必填
        $out_request_no = trim($_POST['WIDRQout_request_no']);

        //构造参数
        $RequestBuilder=new \AliPay\Pagepay\Buildermodel\AlipayTradeFastpayRefundQueryContentBuilder();
        $RequestBuilder->setOutTradeNo($out_trade_no);
        $RequestBuilder->setTradeNo($trade_no);
        $RequestBuilder->setOutRequestNo($out_request_no);

        $aop = new \AliPay\Pagepay\Service\AlipayTradeService($this->config);

        /**
         * 退款查询   alipay.trade.fastpay.refund.query (统一收单交易退款查询)
         * @param $builder 业务参数，使用buildmodel中的对象生成。
         * @return $response 支付宝返回的信息
         */
        $response = $aop->refundQuery($RequestBuilder);
        var_dump($response);
    }

    public function close()
    {
        //商户订单号，商户网站订单系统中唯一订单号
        $out_trade_no = trim($_POST['WIDTCout_trade_no']);

        //支付宝交易号
        $trade_no = trim($_POST['WIDTCtrade_no']);
        //请二选一设置

        //构造参数
        $RequestBuilder=new \AliPay\Pagepay\Buildermodel\AlipayTradeCloseContentBuilder();
        $RequestBuilder->setOutTradeNo($out_trade_no);
        $RequestBuilder->setTradeNo($trade_no);

        $aop = new \AliPay\Pagepay\Service\AlipayTradeService($this->config);

        /**
         * alipay.trade.close (统一收单交易关闭接口)
         * @param $builder 业务参数，使用buildmodel中的对象生成。
         * @return $response 支付宝返回的信息
         */
        $response = $aop->Close($RequestBuilder);
        var_dump($response);
    }

    public function notify_url()
    {
        $arr = $_POST;
        $alipaySevice = new \AliPay\Pagepay\Service\AlipayTradeService($this->config);
        $alipaySevice->writeLog(var_export($_POST,true));
        $result = $alipaySevice->check($arr);

        /* 实际验证过程建议商户添加以下校验。
        1、商户需要验证该通知数据中的out_trade_no是否为商户系统中创建的订单号，
        2、判断total_amount是否确实为该订单的实际金额（即商户订单创建时的金额），
        3、校验通知中的seller_id（或者seller_email) 是否为out_trade_no这笔单据的对应的操作方（有的时候，一个商户可能有多个seller_id/seller_email）
        4、验证app_id是否为该商户本身。
        */
        if($result) {//验证成功
            /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
            //请在这里加上商户的业务逻辑程序代


            //——请根据您的业务逻辑来编写程序（以下代码仅作参考）——

            //获取支付宝的通知返回参数，可参考技术文档中服务器异步通知参数列表

            //商户订单号

            $out_trade_no = $_POST['out_trade_no'];

            //支付宝交易号

            $trade_no = $_POST['trade_no'];

            //交易状态
            $trade_status = $_POST['trade_status'];


            if($_POST['trade_status'] == 'TRADE_FINISHED') {

                //判断该笔订单是否在商户网站中已经做过处理
                //如果没有做过处理，根据订单号（out_trade_no）在商户网站的订单系统中查到该笔订单的详细，并执行商户的业务程序
                //请务必判断请求时的total_amount与通知时获取的total_fee为一致的
                //如果有做过处理，不执行商户的业务程序

                //注意：
                //退款日期超过可退款期限后（如三个月可退款），支付宝系统发送该交易状态通知
            }else if ($_POST['trade_status'] == 'TRADE_SUCCESS') {
                //判断该笔订单是否在商户网站中已经做过处理
                //如果没有做过处理，根据订单号（out_trade_no）在商户网站的订单系统中查到该笔订单的详细，并执行商户的业务程序
                //请务必判断请求时的total_amount与通知时获取的total_fee为一致的
                //如果有做过处理，不执行商户的业务程序
                //注意：
                //付款完成后，支付宝系统发送该交易状态通知
            }
            //——请根据您的业务逻辑来编写程序（以上代码仅作参考）——
            echo "success";	//请不要修改或删除
        }else {
            //验证失败
            echo "fail";

        }
    }

    public function return_url()
    {
        $arr = $_GET;
        $alipaySevice = new \AliPay\Pagepay\Service\AlipayTradeService($this->config);
        $result = $alipaySevice->check($arr);

        /* 实际验证过程建议商户添加以下校验。
        1、商户需要验证该通知数据中的out_trade_no是否为商户系统中创建的订单号，
        2、判断total_amount是否确实为该订单的实际金额（即商户订单创建时的金额），
        3、校验通知中的seller_id（或者seller_email) 是否为out_trade_no这笔单据的对应的操作方（有的时候，一个商户可能有多个seller_id/seller_email）
        4、验证app_id是否为该商户本身。
        */
        if($result) {//验证成功
            /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
            //请在这里加上商户的业务逻辑程序代码

            //——请根据您的业务逻辑来编写程序（以下代码仅作参考）——
            //获取支付宝的通知返回参数，可参考技术文档中页面跳转同步通知参数列表

            //商户订单号
            $out_trade_no = htmlspecialchars($_GET['out_trade_no']);

            //支付宝交易号
            $trade_no = htmlspecialchars($_GET['trade_no']);

            echo "验证成功<br />支付宝交易号：".$trade_no;

            //——请根据您的业务逻辑来编写程序（以上代码仅作参考）——

            /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
        }else{
            //验证失败
            echo "验证失败";
        }
    }


}