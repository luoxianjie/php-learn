<?php

require './vendor/autoload.php';


$curl = curl_init();

curl_setopt_array($curl, array(
    CURLOPT_URL => "https://www.toutiao.com/api/pc/feed/?category=funny&utm_source=toutiao&widen=1&max_behot_time=1527136246&max_behot_time_tmp=1527136246&tadrequire=true&as=A165EBC0A69659C&cp=5B066645799CEE1&_signature=7UCmFwAAtlFkoZ-Nlf5gFO1Apg",
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_ENCODING => "",
    CURLOPT_MAXREDIRS => 10,
    CURLOPT_TIMEOUT => 30,
    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
    CURLOPT_CUSTOMREQUEST => "GET",
    CURLOPT_SSL_VERIFYPEER => false,
    CURLOPT_POSTFIELDS => "Host: www.toutiao.com\r\nUser-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:60.0) Gecko/20100101 Firefox/60.0\r\nAccept: text/javascript, text/html, application/xml, text/xml, */*\r\nAccept-Language: zh-CN,zh;q=0.8,zh-TW;q=0.7,zh-HK;q=0.5,en-US;q=0.3,en;q=0.2\r\nAccept-Encoding: gzip, deflate, br\r\nReferer: https://www.toutiao.com/ch/funny/\r\nX-Requested-With: XMLHttpRequest\r\nContent-Type: application/x-www-form-urlencoded\r\nCookie: tt_webid=6542707462377145859; WEATHER_CITY=%E5%8C%97%E4%BA%AC; UM_distinctid=162ae4e4b1e1395-0d519005af88278-4c322073-1fa400-162ae4e4b2016b7; CNZZDATA1259612802=1261507073-1523339592-https%253A%252F%252Fwww.baidu.com%252F%7C1527142390; tt_webid=6542707462377145859; __tasessionId=vkozn2dm41527143442061; uuid=\"w:9bb4771f9cd24a65aca95f156b22f41c\"\r\nConnection: keep-alive\r\nPragma: no-cache\r\nCache-Control: no-cache",
    CURLOPT_HTTPHEADER => array(
        "User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:60.0) Gecko/20100101 Firefox/60.0",
        "Accept: text/javascript, text/html, application/xml, text/xml, */*",
        "Accept-Language: zh-CN,zh;q=0.8,zh-TW;q=0.7,zh-HK;q=0.5,en-US;q=0.3,en;q=0.2",
        "Accept-Encoding: gzip, deflate, br",
        "Referer: https://www.toutiao.com/ch/funny/",
        "X-Requested-With: XMLHttpRequest",
        "Content-Type: application/x-www-form-urlencoded",
        "Cookie: tt_webid=6542707462377145859; WEATHER_CITY=%E5%8C%97%E4%BA%AC; UM_distinctid=162ae4e4b1e1395-0d519005af88278-4c322073-1fa400-162ae4e4b2016b7; CNZZDATA1259612802=1261507073-1523339592-https%253A%252F%252Fwww.baidu.com%252F%7C1527142390; tt_webid=6542707462377145859; __tasessionId=vkozn2dm41527143442061; uuid=\"w:9bb4771f9cd24a65aca95f156b22f41c\"",
        "Connection: keep-alive",
        "Pragma: no-cache",
        "Cache-Control: no-cache"
    ),
));

$response = curl_exec($curl);
$err = curl_error($curl);

curl_close($curl);

if ($err) {
    echo "cURL Error #:" . $err;
} else {
    echo $response;
}
