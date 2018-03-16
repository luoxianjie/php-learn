<?php
// +----------------------------------------------------------------------
// | Author: jiexianluo@hotmail.com
// | Date  : 2018/3/1
// | Time  : 13:57
// +----------------------------------------------------------------------
/**
 * QQ登录流程 (详见 http://wiki.open.qq.com/wiki/%E3%80%90QQ%E7%99%BB%E5%BD%95%E3%80%91%E5%BC%80%E5%8F%91%E6%94%BB%E7%95%A5_Server-side)
 * 1，QQ用户请求登录第三方应用，第三方应用请求QQOAuth2.0授权登录(https://graph.qq.com/oauth2.0/authorize?response_type=code&client_id=[YOUR_APPID]&redirect_uri=[YOUR_REDIRECT_URI]&scope=[THE_SCOPE])
 * 2，用户确认，QQ OAuth2.0服务器重定向到第三方应用，并带上临时授权票据（code）比如 http://lxj.com/callback?code=fsdfdsfgdghfghdhdfhfdhfdhdfhd
 * 3, 第三方应用服务器通过code appkey redirect_url 获取 access_token 授权码 (https://graph.qq.com/oauth2.0/token?grant_type=authorization_code&client_id=[YOUR_APP_ID]&client_secret=[YOUR_APP_Key]&code=[The_AUTHORIZATION_CODE]&state=[The_CLIENT_STATE]&redirect_uri=[YOUR_REDIRECT_URI])
 * 4, 第三方应用服务器通过使用Access Token来获取用户的OpenID (https://graph.qq.com/oauth2.0/me?access_token=YOUR_ACCESS_TOKEN)
 * 5，第三方应用服务器通过access_token 和openId 获取用户基本信息 具体接口参见相关文档
 */
class QQLogin
{

    private $appid = '';
    private $appkey = '';

    private $redirect_url = '';

    /**
     * 获取跳转至QQ OAuth2.0 服务器的连接地址
     * @return string
     */
    public function getCode()
    {
        return "https://graph.qq.com/oauth2.0/authorize?response_type=code&client_id={$this->appid}&redirect_uri={$this->redirect_url}&scope=get_user_info";
    }

    /**
     * 回调方法，当用户确权后会跳转到该方法
     * 我们可以再此方法中获取用户信息在进行数据库操作
     * 最后引导到相应页面
     */
    public function callback()
    {
        $code = $_GET['code'];

        $accessToken = $this->_getAccessToken($code);

        $openId = $this->_getOpenId($accessToken);

        $userInfo = $this->_getUserInfo($accessToken, $openId);

        // 执行数据库操作......

        // 跳转地址
        $url = '/index.php';
        header('Location:'.$url);
    }

    /**
     * 获取acccessToken
     * @param $code
     * @return mixed
     */
    private function _getAccessToken($code)
    {
        $url = "https://graph.qq.com/oauth2.0/token?grant_type=authorization_code&client_id={$this->appid}&client_secret={$this->appkey}&code={$code}&state=123&redirect_uri={$this->redirect_url}";

        $data = $this->_curlGet($url);
        parse_str($data, $arr);

        $accessToken = $arr['access_token'];
        return $accessToken;
    }

    /**
     * 获取openid
     * @param $accessToken
     * @return mixed
     */
    private function _getOpenId($accessToken)
    {
        $url = "https://graph.qq.com/oauth2.0/me?access_token={$accessToken}";

        $data = $this->_curlGet($url);
        $openId = false;
        if(stripos($data,'callback')!==false){
            $lpos = stripos($data,'(');
            $rpos = stripos($data,')');

            $openId = json_decode(substr($data, $lpos, $rpos),true)['openid'];
        }
        return $openId;
    }


    /**
     * 获取用户信息
     * @param $accessToken
     * @param $openId
     * @return mixed
     */
    private function _getUserInfo($accessToken,$openId)
    {
        $url = "https://graph.qq.com/user/get_user_info?access_token={$accessToken}&oauth_consumer_key={$this->appid}&openid={$openId}";

        $data = $this->_curlGet($url);
        return json_decode($data,true);
    }

    /**
     * curl get 获取资源
     * @param $url
     * @return mixed
     */
    private function _curlGet($url)
    {
        //初始化
        $ch = curl_init();
        //设置选项，包括URL
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        //执行并获取HTML文档内容
        $output = curl_exec($ch);
        //释放curl句柄
        curl_close($ch);

        return $output;
    }




}