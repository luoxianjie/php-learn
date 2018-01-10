<?php
// +----------------------------------------------------------------------
// | Author: jiexianluo@hotmail.com
// | Date  : 2018/1/9
// | Time  : 18:04
// +----------------------------------------------------------------------

session_start();
class B
{
    public function login()
    {
        $ticket = @$_GET['ticket'];
        if(!empty($ticket)){

            //去验证ticket有效性
            $verify_url = 'http://passport.lxj.com/?route=verify&ticket='.$ticket;

            if($this->curl_get($verify_url)=='success'){

                $_SESSION['ticket'] = $ticket;
                $this->index();
            }else{
                echo "您还没有登录跳转到验证服务器。。。";
                $server = 'http://passport.lxj.com/?server=http://b.lxj.com/';
                header("Location: $server" );
            }

        }else{
            echo "您还没有登录跳转到验证服务器。。。";
            $server = 'http://passport.lxj.com/?server=http://b.lxj.com/';
            header("Location: $server" );
        }

    }

    private function curl_get($verify_url)
    {
        //初始化
        $ch = curl_init($verify_url);
        //设置选项，包括URL
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch,CURLOPT_TIMEOUT,13);
        $output = curl_exec($ch);
        curl_close($ch);
        return $output;
    }


    public function index()
    {
        echo "B登陆成功<br>";

        echo "<a href='http://a.lxj.com/?ticket=".$_SESSION['ticket']."'>跳转到A</a>";
    }


}

(new B())->login();
