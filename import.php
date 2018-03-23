<?php

require './vendor/autoload.php';

$filename = "./a.xlsx";

//error_reporting(E_ERROR);
//实例化
$objPHPExcel = new PHPExcel();
$objReader = PHPExcel_IOFactory::createReader('Excel2007');    //use excel2007 for 2007 format

//接收存在缓存中的excel表格
$objPHPExcel = $objReader->load($filename);                 //$filename可以是上传的表格，或者是指定的表格
$objPHPExcel->setActiveSheetIndex(0);
$sheet = $objPHPExcel->getSheet(0);

$insertNum = 0;
$db = Db::getInstance();

for($j=70;$j<=70;$j++)
{
    $a = $objPHPExcel->getActiveSheet()->getCell("A".$j)->getValue();
    $b = $objPHPExcel->getActiveSheet()->getCell("B".$j)->getValue();

    $c = $objPHPExcel->getActiveSheet()->getCell("C".$j)->getValue();
    $d = $objPHPExcel->getActiveSheet()->getCell("D".$j)->getValue();
    $e = $objPHPExcel->getActiveSheet()->getCell("E".$j)->getValue();
    $f = $objPHPExcel->getActiveSheet()->getCell("F".$j)->getValue();
    $g = $objPHPExcel->getActiveSheet()->getCell("G".$j)->getValue();
    $h = $objPHPExcel->getActiveSheet()->getCell("H".$j)->getValue();
    $i = $objPHPExcel->getActiveSheet()->getCell("I".$j)->getValue();
    $o = $objPHPExcel->getActiveSheet()->getCell("J".$j)->getValue();
    $k = $objPHPExcel->getActiveSheet()->getCell("K".$j)->getValue();

    /*$res1 = $db->table('test_dhl_price')->insert(['type'=>3, 'min_weight'=>$a,'max_weight'=>str_replace(',','',$b),'zone'=>1,'total'=>str_replace(',','',$c)]);
    $res2 = $db->table('test_dhl_price')->insert(['type'=>3, 'min_weight'=>$a,'max_weight'=>str_replace(',','',$b),'zone'=>2,'total'=>str_replace(',','',$d)]);
    $res3 = $db->table('test_dhl_price')->insert(['type'=>3, 'min_weight'=>$a,'max_weight'=>str_replace(',','',$b),'zone'=>3,'total'=>str_replace(',','',$e)]);
    $res4 = $db->table('test_dhl_price')->insert(['type'=>3, 'min_weight'=>$a,'max_weight'=>str_replace(',','',$b),'zone'=>4,'total'=>str_replace(',','',$f)]);
    $res5 = $db->table('test_dhl_price')->insert(['type'=>3, 'min_weight'=>$a,'max_weight'=>str_replace(',','',$b),'zone'=>5,'total'=>str_replace(',','',$g)]);
    $res6 = $db->table('test_dhl_price')->insert(['type'=>3, 'min_weight'=>$a,'max_weight'=>str_replace(',','',$b),'zone'=>6,'total'=>str_replace(',','',$h)]);
    $res7 = $db->table('test_dhl_price')->insert(['type'=>3, 'min_weight'=>$a,'max_weight'=>str_replace(',','',$b),'zone'=>7,'total'=>str_replace(',','',$i)]);
    $res8 = $db->table('test_dhl_price')->insert(['type'=>3, 'min_weight'=>$a,'max_weight'=>str_replace(',','',$b),'zone'=>8,'total'=>str_replace(',','',$o)]);
    $res9 = $db->table('test_dhl_price')->insert(['type'=>3, 'min_weight'=>$a,'max_weight'=>str_replace(',','',$b),'zone'=>9,'total'=>str_replace(',','',$k)]);


    $res1 && $res2 && $res3 && $res4 && $res5 && $res6 && $res7 && $res8 && $res9 && ($insertNum = $insertNum + 9);*/
}

var_dump($insertNum);
