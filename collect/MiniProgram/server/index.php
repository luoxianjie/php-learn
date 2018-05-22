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
    public function getWeaather()
    {
        $latitude  = $_POST['latitude'];
        $longitude = $_POST['longitude'];

        $city = $this->getCity($latitude,$longitude);

        $url = "https://www.sojson.com/open/api/weather/json.shtml?city=".urlencode($city);
        $data = file_get_contents($url);

        return $data;
    }


}

$data = (new Weather)->getWeaather();

echo $data;
