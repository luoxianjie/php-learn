<?php

require __DIR__."/vendor/autoload.php";

$mail = new Mail('2860899420@qq.com','pgygvackxgzideih');

$res = $mail->send('Jack','2860899420@qq.com','jiexianluo@hotmail.com','test','test...');


var_dump($res);