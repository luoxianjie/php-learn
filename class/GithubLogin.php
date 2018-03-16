<?php
// +----------------------------------------------------------------------
// | Author: jiexianluo@hotmail.com
// | Date  : 2018/3/14
// | Time  : 16:55
// +----------------------------------------------------------------------


class GithubLogin
{
    private $clientId = '';
    private $clientSecret = '';

    private $redirect_url = '';

    /**
     * 跳转到github授权界面的链接
     * 用户同意授权后携带code参数跳转到回调方法
     * @return string
     */
    public function getCode()
    {
        return "https://github.com/login/oauth/authorize?client_id={$this->clientId}&state=123&redirect_uri={$this->clientSecret}";
    }

    public function callback()
    {
        $code = $_GET['code'];
        $get_access_token_url = "https://github.com/login/oauth/access_token?client_id={$this->clientId}&client_secret={$this->clientSecret}&code={$code}&redirect_uri={$this->redirect_url}";

        $data = file_get_contents($get_access_token_url);

        $accessToken = $this->_getAccessToken($data);

        $get_user_info_url = "https://api.github.com/user?access_token={$accessToken}";

        $data = file_get_contents($get_user_info_url);

        $userInfo = $this->_getUserInfo($data);

        // 获取用户详情后保存自本地数据库的操作

    }

    /**
     * 解析accessToken
     */
    private function _getAccessToken()
    {

    }

    /**
     * 解析userInfo
     */
    private function _getUserInfo()
    {

    }
}
