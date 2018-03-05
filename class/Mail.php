<?php
// +----------------------------------------------------------------------
// | Author: jiexianluo@hotmail.com
// | Date  : 2018/3/5
// | Time  : 18:39
// +----------------------------------------------------------------------

class Mail
{
    public $mail;

    /**
     * 邮件初始化
     * Mail constructor.
     * @param $username
     * @param $password
     */
    public function __construct($username,$password)
    {
        $this->mail = new PHPMailer\PHPMailer();
        $this->mail->isSMTP();              // 使用SMTP服务
        $this->mail->CharSet = "utf8";      // 编码格式为utf8，不设置编码的话，中文会出现乱码
        $this->mail->Host = "smtp.qq.com";  // 发送方的SMTP服务器地址
        $this->mail->SMTPAuth = true;       // 是否使用身份验证
        $this->mail->Username = $username;  // 发送方的邮箱用户名
        $this->mail->Password = $password;  // 发送方的邮箱密码，注意用163邮箱这里填写的是“客户端授权密码”而不是邮箱的登录密码！
        $this->mail->SMTPSecure = "ssl";    // 使用ssl协议方式
        $this->mail->Port = 465;            // 163邮箱的ssl协议方式端口号是465/994
        $this->mail->IsHTML(true);
    }

    /**
     * 发送邮件
     * @param $name     发送人昵称
     * @param $from     发送人地址
     * @param $to       接收人地址
     * @param $sub      邮件主题
     * @param $body     邮件内容
     * @return bool     是否发送成功
     */
    public function send($name,$from,$to,$sub,$body)
    {
        $this->mail->setFrom($from,$name);      // 设置发件人信息，如邮件格式说明中的发件人，这里会显示为xxxx@qq.com
        $this->mail->addAddress($to);           // 设置收件人信息，如邮件格式说明中的收件人，这里会显示为yyyy@163.com
        $this->mail->Subject = $sub;            // 邮件标题
        $this->mail->Body = $body;              // 邮件正文

        if($this->mail->send()){
            return true;
        }else{
            return false;
        }
    }
}