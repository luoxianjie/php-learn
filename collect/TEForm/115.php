<?php

set_time_limit(0);
require_once './vendor/autoload.php';

$objPHPExcel = new PHPExcel();
$objReader = PHPExcel_IOFactory::createReader('Excel2007');    //use excel2007 for 2007 format

//接收存在缓存中的excel表格
$objPHPExcel = $objReader->load('115.xlsx');                   //$filename可以是上传的表格，或者是指定的表格
$sheet = $objPHPExcel->getSheet(0);
$highestRow = $sheet->getHighestRow();                         //取得总行数

$records = [];
$data = [];

$db = Db::getInstance();


$step = isset($_GET['step'])?intval($_GET['step']):1;

$num = 0;
if($step === 1) {
    for ($row = 1; $row <= $highestRow; $row++) {

        $a = $objPHPExcel->getActiveSheet()->getCell("A" . $row)->getValue();
        $b = $objPHPExcel->getActiveSheet()->getCell("B" . $row)->getValue();
        $c = $objPHPExcel->getActiveSheet()->getCell("C" . $row)->getValue();
        $d = $objPHPExcel->getActiveSheet()->getCell("D" . $row)->getValue();
        $e = $objPHPExcel->getActiveSheet()->getCell("E" . $row)->getValue();
        $f = $objPHPExcel->getActiveSheet()->getCell("F" . $row)->getValue();
        $g = $objPHPExcel->getActiveSheet()->getCell("G" . $row)->getValue();
        $h = $objPHPExcel->getActiveSheet()->getCell("H" . $row)->getValue();
        $i = $objPHPExcel->getActiveSheet()->getCell("I" . $row)->getValue();
        $j = $objPHPExcel->getActiveSheet()->getCell("J" . $row)->getValue();
        $k = $objPHPExcel->getActiveSheet()->getCell("K" . $row)->getValue();

        $n = $objPHPExcel->getActiveSheet()->getCell("N" . $row)->getValue();


        $data['elqFormName'] = 'SelectGrowthChinaFY18Advertorial';
        $data['elqSiteId'] = '2070786569';
        $data['elqCampaignId'] = '';
        $data['dUNSNumber1'] = '';
        $data['dUNSConfidence1'] = '';
        $data['eloquaCampaignId'] = '25325';
        $data['tECewt7'] = '';
        $data['reqType'] = '';

        $data['lastName'] = htmlspecialchars($a);
        $data['firstName'] = htmlspecialchars($b);
        $data['busPhone'] = htmlspecialchars($c);
        $data['emailAddress'] = htmlspecialchars($d);
        $data['company'] = htmlspecialchars($e);
        $data['jobRole1'] = htmlspecialchars($f);
        $data['industry1'] = htmlspecialchars($g);
        $data['country'] = htmlspecialchars($h);
        $data['stateProv'] = htmlspecialchars($i);
        $data['city'] = htmlspecialchars($j);
        $data['address1'] = (string)$k;
        $data['paragraphText'] = trim($n);

        $data['status'] = 0;

        //$res = $db->table('teform')->insert($data);
        $res && $num++;
    }

    var_dump($num);
    die;

}else{

    function getProxy()
    {
        $url = 'http://proxy.elecfans.net/proxys.php?key=nTAZhs5QxjCNwiZ6';
        $proxyData = file_get_contents($url);
        $data = json_decode($proxyData,true);

        return $data['data'][0]['ip'];
    }

    function curlPost($url,$post_data,$referer,$proxy,$https = false)
    {
        $ch = curl_init($url);
        // 设置请求头(可有可无)
        //curl_setopt($ch,CURLOPT_HTTPHEADER,$header);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        // 设置是否不直接输出
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        // 设置超时时间
        curl_setopt($ch, CURLOPT_TIMEOUT,30);
        // 设置请求方式
        curl_setopt($ch, CURLOPT_POST, 1);
        // 设置来路
        curl_setopt($ch, CURLOPT_REFERER, $referer);
        // 设置传输字段
        curl_setopt($ch, CURLOPT_POSTFIELDS, $post_data);

        // 设置代理ip及端口号
        /*curl_setopt($ch, CURLOPT_PROXYAUTH, CURLAUTH_BASIC);
        curl_setopt($ch, CURLOPT_PROXY, $proxy);
        curl_setopt($ch, CURLOPT_PROXYTYPE, CURLPROXY_HTTP);
        curl_setopt($ch, CURLOPT_HTTPPROXYTUNNEL, 1);*/

        if($https === true){
            // https请求 不验证证书和hosts
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
            // 模拟浏览器代理
            curl_setopt($ch,CURLOPT_USERAGENT,"Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.1; SV1; .NET CLR 1.1.4322; .NET CLR 2.0.50727)");
        }

        //执行
        $out_put = curl_exec($ch);

        // 是否只获取状态码
        if($https === true){
            $httpCode = curl_getinfo($ch,CURLINFO_HTTP_CODE);
            //释放curl句柄
            curl_close($ch);
            return ['status'=>$httpCode,'body'=>$out_put];
        }
        //释放curl句柄
        curl_close($ch);

        return $out_put;
    }

    $records = $db->table('teform')->where(['status'=>0])->select();
    // 获取代理
    $proxy = getProxy();

    foreach ($records as $data){

        try {

            // 模拟浏览器请求发送到外部站点
            $referer = 'http://www.elecfans.com/company/te_connectivity2018/applications.html';
            $url = 'https://s2070786569.t.eloqua.com/e/f2';
            $res = curlPost($url, $data, $referer, $proxy,true);

            if($res['status']  != 200 || !empty(trim($res['body']))){
                var_dump($data);
                die('处理失败，请稍后重试!本次共处理'.$num.'条记录');
            }

            $res = $db->table('teform')->where(['id'=>$data['id']])->update(['status'=>1]);
            $res && $num++;

        } catch (\Exception $e){
            var_dump($data);
            die('处理失败，请稍后重试!本次共处理'.$num.'条记录');
        }
        die('记录'.$data['id'].'处理成功!');
    }

    die('本次共处理'.$num.'条记录,全部处理完成');
}
