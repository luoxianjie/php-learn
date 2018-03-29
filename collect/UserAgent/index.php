<?php
// +----------------------------------------------------------------------
// | Author: jiexianluo@hotmail.com
// | Date  : 2018/3/26
// | Time  : 11:59
// +----------------------------------------------------------------------
session_start();

class Form
{
    /**
     * 显示表单界面
     * @param  title 标题
     * @param  from  来源
     */
    public function show()
    {
        // 来源
        $from = isset($_GET['from'])?$_GET['from']:'Sample';
        if(!in_array($from,['Sample','Document','Contact'])){
            die('来源不明');
        }
        require "index.html";
    }

    /**
     * 收集表单数据转发到相关站点
     * @param firstName 名
     * @param LastName  姓
     * @param busPhone  手机号
     * @param emailAddress     邮件地址
     * @param company   公司名
     * @param jobRole1  职业
     * @param industry1 行业
     * @param country   国家
     * @param stateProv       省份
     * @param city      城市
     * @param address1        地址
     * @param description     描述
     * @param form      来源
     */
    public function deal()
    {
        $firstName = isset($_POST['firstName'])?addslashes(trim($_POST['firstName'])):null;
        $lastName = isset($_POST['lastName'])?addslashes(trim($_POST['lastName'])):null;
        $busPhone = isset($_POST['busPhone'])?addslashes(trim($_POST['busPhone'])):null;
        $emailAddress = isset($_POST['emailAddress'])?addslashes(trim($_POST['emailAddress'])):null;
        $company = isset($_POST['company'])?addslashes(trim($_POST['company'])):null;
        $jobRole1 = isset($_POST['jobRole1'])?addslashes(trim($_POST['jobRole1'])):null;
        $industry1 = isset($_POST['industry1'])?addslashes(trim($_POST['industry1'])):null;
        $country = isset($_POST['country'])?addslashes(trim($_POST['country'])):null;
        $stateProv = isset($_POST['stateProv'])?addslashes(trim($_POST['stateProv'])):null;
        $city = isset($_POST['city'])?addslashes(trim($_POST['city'])):null;
        $address1 = isset($_POST['address1'])?addslashes(trim($_POST['address1'])):null;
        $description = isset($_POST['description'])?addslashes(trim($_POST['description'])):null;
        $from = isset($_POST['from'])?addslashes(trim($_POST['from'])):null;

        $dUNSConfidence1 = '';
        $dUNSNumber1 = '';
        $eloquaCampaignId = '';
        $elqCampaignId = '';
        $paragraphText = '';
        $reqType = $from;
        $tECewt7 = '';

        $elqFormName = 'sg-cn-elecfans';
        $elqSiteId = '2070786569';
        $fid = 111;
        $field4 = '运动系统';
        $field8 = 'te-test';
        $uid = trim($_POST['uid']);

        if(empty($uid)){
            die('请先登录');
        }

        /*if(!$from || !in_array($from,['Sample','Contact','Document'])){
            die('来源不明');
        }*/

        if(!$firstName || !$lastName || !$busPhone || !$emailAddress || !$company || !$jobRole1 || !$industry1 || !$country || !$stateProv || !$city || !$address1){
            die('缺少必要信息');
        }

        $data = [
            'firstName'     => $firstName,
            'lastName'      => $lastName,
            'busPhone'      => $busPhone,
            'emailAddress'  => $emailAddress,
            'company'       => $company,
            'jobRole1'      => $jobRole1,
            'industry1'     => $industry1,
            'country'       => $country,
            'stateProv'     => $stateProv,
            'city'          => $city,
            'address1'      => $address1,
            'description'   => $description,

            'dUNSConfidence1'  =>$dUNSConfidence1,
            'dUNSNumber1'      => $dUNSNumber1,
            'eloquaCampaignId' => $eloquaCampaignId,
            'elqCampaignId'    => $elqCampaignId,
            'paragraphText'    => $paragraphText,
            'reqType'          => $reqType,
            'tECewt7'          => $tECewt7,
            'elqFormName'      => $elqFormName,
            'elqSiteId'        => $elqSiteId,
            'fid'              => $fid,
            'field4'           => $field4,
            'field8'           => $field8,
            'uid'              => $uid
        ];

        $_SESSION['data'] = $data;
        header('Content-type:text/html;charset=utf-8');
        try {

            // 获取代理
            $proxy = $this->_getProxy();

            // 模拟浏览器请求发送到外部站点
            $referer = 'http://www.elecfans.com/company/te_connectivity2018/applications.html';
            // $referer = $_SERVER['HTTP_REFERER'];
            $url1 = 'https://s2070786569.t.eloqua.com/e/f2';
            $res1 = $this->_curlPost($url1, $data, $referer, $proxy,true);

            if($res1['status']  != 200 || !empty(trim($res1['body']))){
                /*var_dump($res1);*/
                $this->_jump('处理失败，请稍后重试!');
            }

            // 模拟浏览器请求发送到内部站点
            $url2 = 'http://bbs.elecfans.com/topicform/index.php?s=/Home/Ajaxpost/handle';
            $res2 = $this->_curlPost($url2, $data, $referer, $proxy);
            $res2 = json_decode(substr($res2, strpos($res2,'{'), strrpos($res2,'}')-3),true);

        } catch (\Exception $e){
            /*var_dump($res1,$res2);*/
            $this->_jump('处理失败，请稍后重试!');
        }

        if($res1['status']  == 200 && empty(trim($res1['body'])) && $res2['status'] == 'successed'){
            unset($_SESSION['data']);
            die('处理成功');
        }else{
            /*var_dump($res1,$res2);*/
            $this->_jump('处理失败，请稍后重试!');
        }

    }

    /**
     * curl 模拟浏览器请求
     * @param $url        url 地址
     * @param $post_data  post 数据
     * @param $referer    来路
     * @param $proxy      代理
     * @param bool $https      是否https请求
     * @return mixed      响应结果
     */
    private function _curlPost($url,$post_data,$referer,$proxy,$https = false)
    {
        $ch = curl_init($url);
        // 设置请求头(可有可无)
        //curl_setopt($ch,CURLOPT_HTTPHEADER,$header);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        // 设置是否不直接输出
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        // 设置超时时间
        curl_setopt($ch, CURLOPT_TIMEOUT,30);
        // 设置请求方式
        curl_setopt($ch, CURLOPT_POST, 1);
        // 设置来路
        curl_setopt($ch, CURLOPT_REFERER, $referer);
        // 设置传输字段
        curl_setopt($ch, CURLOPT_POSTFIELDS, $post_data);

        // 设置代理ip及端口号
        curl_setopt($ch, CURLOPT_PROXYAUTH, CURLAUTH_BASIC);
        curl_setopt($ch, CURLOPT_PROXY, $proxy);
        /*curl_setopt($ch, CURLOPT_PROXYTYPE, CURLPROXY_HTTP);
        curl_setopt($ch, CURLOPT_HTTPPROXYTUNNEL, 1);*/

        if($https === true){
            // https请求 不验证证书和hosts
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
            // 模拟浏览器代理
            curl_setopt($ch,CURLOPT_USERAGENT,"Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.1; SV1; .NET CLR 1.1.4322; .NET CLR 2.0.50727)");
        }

        //执行
        $out_put = curl_exec($ch);

        // 是否只获取状态码
        if($https === true){
            $httpCode = curl_getinfo($ch,CURLINFO_HTTP_CODE);
            //释放curl句柄
            curl_close($ch);
            return ['status'=>$httpCode,'body'=>$out_put];
        }

        //释放curl句柄
        curl_close($ch);
        return $out_put;
    }

    /**
     * 获取代理ip
     * @return string
     */
    private function _getProxy()
    {
        $url = 'http://proxy.elecfans.net/proxys.php?key=nTAZhs5QxjCNwiZ6';
        $proxyData = file_get_contents($url);
        $data = json_decode($proxyData,true);

        return $data['data'][0]['ip'];
    }

    private function _jump($msg)
    {
        ob_clean();
        echo "<a href='/'>{$msg}</a><span id='time' >3</span>秒后跳转。";
        echo "<script type='text/javascript'> var time = document.getElementById('time'); setInterval(function(){ time.innerHTML = parseInt(time.innerHTML) -1; if(time.innerHTML<1){ location.href='/'}; },1000);</script>";
        die;
    }
}

/**
 * 保留历史记录
 * @param $varname
 * @return null
 */
function old($varname)
{
    if(isset($_SESSION['data'][$varname])){
        return $_SESSION['data'][$varname];
    }else{
        return null;
    }
}


$action = isset($_GET['action'])?$_GET['action']:"show";

(new Form())->$action();