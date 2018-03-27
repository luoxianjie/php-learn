<?php
// +----------------------------------------------------------------------
// | Author: jiexianluo@hotmail.com
// | Date  : 2018/3/26
// | Time  : 11:59
// +----------------------------------------------------------------------

class Form
{
    /**
     * 显示表单界面
     * @param  title 标题
     * @param  from  来源
     */
    public function show()
    {
        // $title = isset($_GET['title'])?$_GET['title']:'表单';
        // 来源
        $from = isset($_GET['from'])?$_GET['from']:'sample';
        if(!in_array($from,['sample','download','ask'])){
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
     * @param $stateProv      省份
     * @param city      城市
     * @param address1        地址
     * @param description     描述
     * @param form      来源
     */
    public function deal()
    {
        $firstName = isset($_POST['firstName'])?addslashes(trim($_POST['firstName'])):null;
        $lastName = isset($_POST['lastName'])?addslashes(trim($_POST['lastName'])):null;
        $phone = isset($_POST['busPhone'])?addslashes(trim($_POST['busPhone'])):null;
        $email = isset($_POST['emailAddress'])?addslashes(trim($_POST['emailAddress'])):null;
        $company = isset($_POST['company'])?addslashes(trim($_POST['company'])):null;
        $jobRole1 = isset($_POST['jobRole1'])?addslashes(trim($_POST['jobRole1'])):null;
        $industry = isset($_POST['industry1'])?addslashes(trim($_POST['industry1'])):null;
        $country = isset($_POST['country'])?addslashes(trim($_POST['country'])):null;
        $stateProv = isset($_POST['stateProv'])?addslashes(trim($_POST['stateProv'])):null;
        $city = isset($_POST['city'])?addslashes(trim($_POST['city'])):null;
        $address = isset($_POST['address1'])?addslashes(trim($_POST['address1'])):null;
        $description = isset($_POST['description'])?addslashes(trim($_POST['description'])):null;

        /*$from = isset($_POST['from'])?addslashes(trim($_POST['from'])):null;

        if(!$from || !in_array($from,['sample','ask','download'])){
            die('来源不明');
        }*/

        if(!$firstName || !$lastName || !$phone || !$email || !$company || !$jobRole1 || !$industry || !$country || !$stateProv || !$city || !$address){
            die('缺少必要信息');
        }

        $data = [
            'firstName' => $firstName,
            'lastName'  => $lastName,
            'phone'     => $phone,
            'email'     => $email,
            'company'   => $company,
            'jobRole1'  => $jobRole1,
            'industry'  => $industry,
            'country'   => $country,
            'stateProv' => $stateProv,
            'city'      => $city,
            'address'   => $address,
            'description'   => $description
        ];

        $referer = 'http://www.baidu.com';

        // 模拟浏览器请求发送到站点一

        $url1 = 'https://s2070786569.t.eloqua.com/e/f2';
        $res1 = $this->_curlPost('a.com',$data,$referer,true);

        // 模拟浏览器请求发送到站点二
        $url2 = 'https://s2070786569.t.eloqua.com/e/f2';
        $res2 = $this->_curlPost('b.com',$data,$referer);
        $res2 = json_decode($res2,true);

        if($res1 == $res2['status'] && $res1['status']  == 200){
            die('处理成功');
        }else{
            die('处理失败，请稍后重试!');
        }

    }

    /**
     * curl 模拟浏览器请求
     * @param $url        url 地址
     * @param $post_data  post 数据
     * @param $referer    来路
     * @param bool $https      是否https请求
     * @return mixed      响应结果
     */
    private function _curlPost($url,$post_data,$referer,$https = false)
    {
        $ch = curl_init($url);
        // 设置请求头(可有可无)
        //curl_setopt($ch,CURLOPT_HTTPHEADER,$header);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        // 设置是否不直接输出
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        // 设置请求方式
        curl_setopt($ch, CURLOPT_POST, 1);
        // 设置来路
        curl_setopt($ch, CURLOPT_REFERER, $referer);
        // 设置传输字段
        curl_setopt($ch, CURLOPT_POSTFIELDS, $post_data);

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
            return $httpCode;
        }

        //释放curl句柄
        curl_close($ch);

        return $out_put;
    }
}

$action = isset($_GET['action'])?$_GET['action']:"show";

(new Form())->$action();