<?php
// +----------------------------------------------------------------------
// | Author: jiexianluo@hotmail.com
// | Date  : 2018/3/1
// | Time  : 14:28
// +----------------------------------------------------------------------

/**
 * 详见(http://open.weibo.com/wiki/Connect/login)
 * Class WeiboLogin
 */
class WeiboLogin
{

    private $appKey = '';
    private $appSecret = '';

    private $redirect_url = '';

    /**
     * 获取跳转至微博OAuth2.0 服务器的连接地址
     * @return string
     */
    public function getCode()
    {
        return "https://api.weibo.com/oauth2/authorize?client_id={$this->appKey}&response_type=code&redirect_uri={$this->redirect_url}";
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
        $uid = $this->_getUid($accessToken);
        $user_info = $this->_getUserInfo($accessToken,$uid);

        // 执行相关操作 如将用户信息存入数据库......


        // 跳转到首页
        $url = '/index.php';
        header('Location:',$url);

    }

    /**
     * 获取accessToken
     * @param $code
     * @return mixed
     */
    private function _getAccessToken($code)
    {
        $url = "https://api.weibo.com/oauth2/access_token?client_id={$this->appKey}&client_secret={$this->appSecret}&grant_type=authorization_code&redirect_uri={$this->redirect_url}&code={$code}";

        $data = file_get_contents($url);
        $arr = json_decode($data,true);
        return $arr['access_token'];
    }

    /**
     * 获取用户id
     * @param $accessToken
     * @return mixed
     */
    private function _getUid($accessToken)
    {
        $url = "https://api.weibo.com/2/account/get_uid.json?access_token={$accessToken}";

        $data = file_get_contents($url);
        $arr = json_decode($data,true);
        return $arr['uid'];
    }

    /**
     * 获取用户信息
     * @param $accessToken
     * @param $uid
     * @return mixed
     */
    private function _getUserInfo($accessToken,$uid)
    {
        $url = "https://api.weibo.com/2/users/show.json?access_token={$accessToken}&uid={$uid}";

        $data = file_get_contents($url);
        $arr = json_decode($data,true);
        return $arr;
    }
}