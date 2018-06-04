<?php
$client = new \Yar_Client("http://192.168.234.131/server.php");
/* the following setopt is optinal */
$client->SetOpt(YAR_OPT_CONNECT_TIMEOUT, 1000);

$client->SetOpt(YAR_OPT_HEADER, array("hd1: val", "hd2: val"));  //Custom headers, Since 2.0.4

/* call remote service */
$result = $client->some_method("parameter");

var_dump($result);
?>