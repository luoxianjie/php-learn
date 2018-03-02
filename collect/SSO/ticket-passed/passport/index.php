<?php
// +----------------------------------------------------------------------
// | Author: jiexianluo@hotmail.com
// | Date  : 2018/1/9
// | Time  : 18:35
// +----------------------------------------------------------------------

session_start();
class Passport
{

    public function login()
    {
        //发放ticket
        $url = urldecode($_GET['server']."?ticket=fsfsfsfewrrwejysfutuyu");
        header('Location:' . $url);
    }

    public function verify()
    {
        $ticket = $_GET['ticket'];

        //验证ticket有效性
        if($ticket){
            echo 'success';
            die;
        }
        echo 'fail';
        die;
    }


}

$route = @$_GET['route'];
if(!empty($route)){
    (new Passport())->$route();
}else{
    (new Passport())->login();
}
