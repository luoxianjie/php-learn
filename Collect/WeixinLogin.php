<?php
// +----------------------------------------------------------------------
// | Author: jiexianluo@hotmail.com
// | Date  : 2018/3/1
// | Time  : 11:01
// +----------------------------------------------------------------------

/**
 * 流程说明（详见https://open.weixin.qq.com/cgi-bin/showdocument?action=dir_list&t=resource/res_list&verify=1&id=open1419316505&token=62c54cb5ecf5f9ee597d04932caf7f9d7a294822&lang=zh_CN）
 * 1，微信用户请求登录第三方应用，第三方应用请求微信OAuth2.0授权登录(https://open.weixin.qq.com/connect/qrconnect?appid=APPID&redirect_uri=REDIRECT_URI&response_type=code&scope=SCOPE&state=STATE#wechat_redirect)
 * 2，用户确认，微信OAuth2.0服务器重定向到第三方应用，并带上临时授权票据（code）比如 http://lxj.com/callback?code=fsdfdsfgdghfghdhdfhfdhfdhdfhd
 * 3, 第三方应用服务器通过code appid appsecret 获取 access_token 授权码 (https://api.weixin.qq.com/sns/oauth2/access_token?appid=APPID&secret=SECRET&code=CODE&grant_type=authorization_code)
 * 4，第三方应用服务器通过access_token 获取用户基本信息 具体接口参见相关文档
 * Class WeixinLogin
 */
class WeixinLogin
{
    private $AppID = '';
    private $AppSecret = '';

    private $RedirectUrl = '';      // 回调地址

    /**
     * 获取跳转至微信OAuth2.0 服务器的连接地址
     * @return string
     */
    public function getUrl()
    {
        return "https://open.weixin.qq.com/connect/qrconnect?appid={$this->AppID}&redirect_uri={$this->RedirectUrl}&response_type=code&scope=snsapi_login&state=123#wechat_redirect";
    }

    /**
     * 回调方法，当用户确权后会跳转到该方法
     * 我们可以再此方法中获取用户信息在进行数据库操作
     * 最后引导到相应页面
     */
    public function callback()
    {
        $code = $_GET['code'];

        $data = $this->_getAccessToken($code);

        $user_info = $this->_getUserInfo($data['access_token'],$data['openid']);

        // 执行相关操作 如将用户信息存入数据库......


        // 跳转到首页
        $url = '/index.php';
        header('Location:',$url);
    }

    /**
     * 获取access_token
     * @param $code
     */
    private function _getAccessToken($code)
    {
        $url = "https://api.weixin.qq.com/sns/oauth2/access_token?appid={$this->AppID}&secret={$this->AppSecret}&code={$code}&grant_type=authorization_code";

        $data = $this->_curlGet($url);
    }

    /**
     * 获取用户个人信息
     * @param $access_token
     * @param $open_id
     * @return mixed
     */
    private function _getUserInfo($access_token,$open_id)
    {
        $url = "https://api.weixin.qq.com/sns/userinfo?access_token={$access_token}&openid={$open_id}";

        $data = $this->_curlGet($url);
        return $data;
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