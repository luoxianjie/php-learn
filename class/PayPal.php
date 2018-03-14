<?php
// +----------------------------------------------------------------------
// | Author: jiexianluo@hotmail.com
// | Date  : 2018/3/14
// | Time  : 10:32
// +----------------------------------------------------------------------

/**
 * paypal支付类
 * 1，创建一个支付，发送到paypal服务端
 * 2, paypal服务端返回一个用户授权地址
 * 3，转链到用户授权地址，用户授权
 * 4, 用户授权完毕，paypal返回到客户端设置的execute地址，付款实现
 * Class PayPal
 */
class PayPal
{
    public $clientId = '';
    public $clientSecret = '';
    public $apiContext = '';

    public function __construct($config)
    {
        $this->clientId = $config['clientId'];
        $this->clientSecret = $config['clientSecret'];

        $this->apiContext = new \PayPal\Rest\ApiContext(
            new \PayPal\Auth\OAuthTokenCredential(
                $this->clientId,
                $this->clientSecret
            )
        );
        $this->apiContext->setConfig(
            array(
                'mode' => 'sandbox',
                'log.LogEnabled' => true,
                'log.FileName' => './PayPal.log',
                'log.LogLevel' => 'DEBUG', // PLEASE USE `INFO` LEVEL FOR LOGGING IN LIVE ENVIRONMENTS
                'cache.enabled' => false,
                // 'http.CURLOPT_CONNECTTIMEOUT' => 30
                // 'http.headers.PayPal-Partner-Attribution-Id' => '123123123'
                // 'log.AdapterFactory' => '\PayPal\Log\DefaultLogFactory' // Factory class implementing \PayPal\Log\PayPalLogFactory
            )
        );

    }

    /**
     * 创建订单，跳转至paypal付款界面
     */
    public function payment()
    {
        $payer = new \PayPal\Api\Payer();
        $payer->setPaymentMethod("paypal");

        // 订单商品
        $item1 = new \PayPal\Api\Item();
        $item1->setName('test pro 1')
            ->setCurrency('USD')
            ->setQuantity(1)
            ->setSku("testpro1_01")
            ->setPrice(20);
        $item2 = new \PayPal\Api\Item();
        $item2->setName('test pro 2')
            ->setCurrency('USD')
            ->setQuantity(5)
            ->setSku("testpro2_01")
            ->setPrice(10);

        $itemList = new \PayPal\Api\ItemList();
        $itemList->setItems(array($item1, $item2));


        // 设置收货地址，可防止用户后期修改
        $address = new \PayPal\Api\ShippingAddress();
        $address->setRecipientName('什么名字')
            ->setLine1('什么街什么路什么小区')
            ->setLine2('什么单元什么号')
            ->setCity('城市名')
            ->setState('浙江省')
            ->setPhone('12345678911')
            ->setPostalCode('12345')
            ->setCountryCode('CN');

        $itemList->setShippingAddress($address);

        // 设置商品细节信息
        $details = new \PayPal\Api\Details();
        $details->setShipping(5)    // 邮费
        ->setTax(10)            // 税费
        ->setSubtotal(70);      // 小计

        // 设置商品总价
        $amount = new \PayPal\Api\Amount();
        $amount->setCurrency("USD")
            ->setTotal(85)
            ->setDetails($details);

        // 设置事务信息
        $transaction = new \PayPal\Api\Transaction();
        $transaction->setAmount($amount)
            ->setItemList($itemList)
            ->setDescription("Payment description")
            ->setInvoiceNumber(uniqid());

        // 设置回调地址
        $baseUrl = getBaseUrl();
        $redirectUrls = new \PayPal\Api\RedirectUrls();
        $redirectUrls->setReturnUrl("$baseUrl/index.php?act=exec&success=true")
            ->setCancelUrl("$baseUrl/index.php?act=cancel&success=false");

        $payment = new \PayPal\Api\Payment();
        $payment->setIntent("sale")
            ->setPayer($payer)
            ->setRedirectUrls($redirectUrls)
            ->setTransactions(array($transaction));

        // 创建一个支付到paypal服务器
        $payment->create($this->apiContext);

        // 获取回调地址
        $approvalUrl = $payment->getApprovalLink();

        // 跳转到授权地址
        header('location:'.$approvalUrl);

    }

    /**
     * 同意付款后回调地址
     */
    public function exec()
    {
        if (isset($_GET['success']) && $_GET['success'] == 'true') {

            $paymentId = $_GET['paymentId'];
            $payment = \PayPal\Api\Payment::get($paymentId, $this->apiContext);

            $execution = new \PayPal\Api\PaymentExecution();
            $execution->setPayerId($_GET['PayerID']);

            $transaction = new \PayPal\Api\Transaction();
            $amount = new \PayPal\Api\Amount();
            $details = new \PayPal\Api\Details();

            $details->setShipping(5)
                ->setTax(10)
                ->setSubtotal(70);

            $amount->setCurrency('USD');
            $amount->setTotal(85);
            $amount->setDetails($details);
            $transaction->setAmount($amount);

            $execution->addTransaction($transaction);

            try {
                // Execute the payment
                $result = $payment->execute($execution, $this->apiContext);
                echo "支付成功";
            } catch (Exception $ex) {
                echo "支付失败";
            }

            return $payment;
        } else {
            echo "PayPal返回回调地址参数错误";
        }
    }

    /**
     * 取消付款后回调地址
     */
    public function cancel()
    {
        echo "用户取消付款";
    }

}
