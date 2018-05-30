<?php

require './vendor/autoload.php';


class Weather{

    private $map_sk  = 'rVny8i60JnVygNev3pPnJfN2Ok3gzrKw';      // qqmap sk
    private $map_key = 'IXRBZ-D7FW3-N2P3I-YVDZ4-GHMR2-S7FVL';   // qqmap key

    /**
     * 获取当前地址城市信息
     * @param $latitude
     * @param $longitude
     * @return mixed
     */
    private function getCity($latitude,$longitude)
    {

        $sk  = $this->map_sk;
        $uri = '/ws/geocoder/v1/';
        $key = $this->map_key;
        $method = 'GET';

        $location = $latitude.','.$longitude;
        $params   = array(
            'key'     => $key,
            "location"=> $location
        );

        // 获取签名
        $sn =  $this->getSn($sk, $uri, $params, $method);

        // 获取地址信息
        $url = 'http://apis.map.qq.com/ws/geocoder/v1/?key='.$key.'&location='.$location.'&sn='.$sn;
        $mapData = json_decode(file_get_contents($url),true);

        return $mapData['result']['address_component']['city'];
    }


    /**
     * 获取签名
     * @param $sk
     * @param $url
     * @param $querystring_arrays
     * @param string $method
     * @return string
     */
    private function getSn($sk, $url, $querystring_arrays, $method = 'GET')
    {
        if ($method === 'POST'){
            ksort($querystring_arrays);
        }
        //这个 querystring 汉字和部分字符会被 url 编码，所以在后面使用前应先反编码
        $querystring = http_build_query($querystring_arrays);
        return md5(urlencode($url.'?'. urldecode($querystring) . $sk));
    }


    /**
     * 获取天气信息
     */
    public function getWeather()
    {
        $latitude  = $_POST['latitude'];
        $longitude = $_POST['longitude'];

        $city = $this->getCity($latitude,$longitude);

        $url = "https://www.sojson.com/open/api/weather/json.shtml?city=".urlencode($city);
        $data = file_get_contents($url);

        return $data;
    }

    /**
     * 获取笑话列表
     */
    public function getHappyList()
    {
        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => "https://www.toutiao.com/api/pc/feed/?category=news_hot&utm_source=toutiao&widen=1&max_behot_time=0&max_behot_time_tmp=0&tadrequire=true&as=A1254BD067EF0D6&cp=5B07EFE05DB6EE1&_signature=HZMAaAAARpmUcjny4JDPxB2TAH",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "GET",
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_HTTPHEADER => array(
                "Host: www.toutiao.com",
                "User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:60.0) Gecko/20100101 Firefox/60.0",
                "Accept: text/javascript, text/html, application/xml, text/xml, */*",
                "Accept-Language: zh-CN,zh;q=0.8,zh-TW;q=0.7,zh-HK;q=0.5,en-US;q=0.3,en;q=0.2",
                "Accept-Encoding: gzip, deflate, br",
                "Referer: https://www.toutiao.com/ch/news_hot/",
                "X-Requested-With: XMLHttpRequest",
                "Content-Type: application/x-www-form-urlencoded",
                "Cookie: tt_webid=6542707462377145859; WEATHER_CITY=%E5%8C%97%E4%BA%AC; UM_distinctid=162ae4e4b1e1395-0d519005af88278-4c322073-1fa400-162ae4e4b2016b7; CNZZDATA1259612802=1261507073-1523339592-https%253A%252F%252Fwww.baidu.com%252F%7C1527245018; tt_webid=6542707462377145859; uuid=\"w:9bb4771f9cd24a65aca95f156b22f41c\"; _ga=GA1.2.1296133280.1527155071; __tasessionId=v2rpfbgsn1527243354901",
                "Connection: keep-alive",
                "Pragma: no-cache",
                "Cache-Control: no-cache"
            ),
        ));

        $response = curl_exec($curl);
        $err = curl_error($curl);

        curl_close($curl);

        if ($err) {
            echo json_encode(['message'=>'error']);
        } else {
            echo $response;
        }
    }


    public function getHappy()
    {

    }


}

$action = isset($_GET['action'])?$_GET['action']:'getWeather';

$data = (new Weather)->$action();

echo $data;
