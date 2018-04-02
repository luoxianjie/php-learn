<?php
// +----------------------------------------------------------------------
// | Author: jiexianluo@hotmail.com
// | Date  : 2018/1/9
// | Time  : 18:04
// +----------------------------------------------------------------------

session_start();
class A
{
    /**
     *
     */
    public function login()
    {
        $ticket = @$_GET['ticket'];
        if(!empty($ticket)){
            // 验证ticket有效性
            $verify_url = 'http://passport.com/index.php?action=verify&ticket='.$ticket;
            if(file_get_contents($verify_url)=='success'){
                // 获取用户信息
                $get_user_info_url = 'http://passport.com/index.php?action=user&ticket='.$ticket;
                $user = file_get_contents($get_user_info_url);
                $_SESSION['user'] = json_decode($user,true);
                $this->index();
            }else{
                $msg = "您还未登录";
                $url = "http://passport.com/index.php?action=login&server=http://a.com/index.php";
                $this->_jump($msg,$url);
            }
        }else{
            $msg = "您还未登录";
            $url = "http://passport.com/index.php?action=login&server=http://a.com/index.php";
            $this->_jump($msg,$url);
        }

    }

    /**
     * 若用户未登陆，则跳转到单点登陆
     */
    public function index()
    {
        $ticket = @$_GET['ticket'];
        if($ticket && !isset($_SESSION['user'])){
            $verify_url = 'http://passport.com/index.php?action=verify&ticket='.$ticket;
            if(file_get_contents($verify_url)=='success') {
                // 获取用户信息
                $get_user_info_url = 'http://passport.com/index.php?action=user&ticket=' . $ticket;
                $user = file_get_contents($get_user_info_url);
                $_SESSION['user'] = json_decode($user, true);
            }else{
                $msg = "您还未登录";
                $url = "http://passport.com/index.php?action=login&server=http://a.com/index.php";
                $this->_jump($msg,$url);
            }
        }
        if($_SESSION['user']) {
            $ticket = $_SESSION['user']['ticket'];
            echo "<script src='http://b.com/index.php?action=login&ticket={$ticket}'></script>";
            echo "A已登陆成功<a href='http://passport.com/index.php?action=logout&server=http://a.com/index.php'>退出</a><br>";
            echo "<a href='http://b.com/index.php?action=index&ticket={$ticket}'>跳转到B</a>";
        }else{
            $msg = "您还未登录";
            $url = "http://passport.com/index.php?action=login&server=http://a.com/index.php";
            $this->_jump($msg,$url);
        }
    }

    public function logout()
    {
        session_destroy();
        $server = $_GET['server'];
        $url1 = 'http://b.com/index.php?action=logout&server='.$server;
        header('Location:'.$url1);
    }

    /**
     * 跳转方法
     * @param $msg
     * @param $url
     */
    private function _jump($msg, $url)
    {
        ob_clean();
        echo "<a href='$url'>{$msg}</a><span id='time' >3</span>秒后跳转。";
        echo "<script type='text/javascript'> var time = document.getElementById('time'); setInterval(function(){ time.innerHTML = parseInt(time.innerHTML) -1; if(time.innerHTML<1){ location.href='$url'}; },1000);</script>";
        die;
    }


}

$action = isset($_GET['action'])?trim($_GET['action']):'index';

(new A())->$action();
