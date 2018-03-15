<?php
// +----------------------------------------------------------------------
// | Author: jiexianluo@hotmail.com
// | Date  : 2018/3/14
// | Time  : 16:55
// +----------------------------------------------------------------------


class GithubLogin
{
    private $appKey = '';
    private $appSecret = '';

    private $redirect_url = '';

    public function getCode()
    {
        return "https://github.com/login/oauth/authorize?client_id=xxxxx&state=xxx&redirect_uri=xxxx";
    }

    public function callback()
    {

    }

    private function _getAccessToken()
    {

    }

    private function _getUserInfo()
    {

    }
}
