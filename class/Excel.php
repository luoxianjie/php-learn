<?php
// +----------------------------------------------------------------------
// | Author: jiexianluo@hotmail.com
// | Date  : 2018/3/5
// | Time  : 16:32
// +----------------------------------------------------------------------

class Excel
{
    /**
     * 数据库记录导出到excel文件
     */
    public function output()
    {
        $db = Db::getInstance();

        $data = $db->table('test')->select();

        //实例化
        $objPHPExcel = new PHPExcel();

        /*右键属性所显示的信息*/
        $objPHPExcel->getProperties()
        ->setCreator("lxj")             //作者
        ->setLastModifiedBy("lxj")      //最后一次保存者
        ->setTitle('数据EXCEL导出')     //标题
        ->setSubject('数据EXCEL导出')   //主题
        ->setDescription('导出数据')    //描述
        ->setKeywords("excel")          //标记
        ->setCategory("result file");   //类别


        //设置当前的表格
        $objPHPExcel->setActiveSheetIndex(0);
        // 设置表格第一行显示内容
        $objPHPExcel->getActiveSheet()
            ->setCellValue('A1', 'id')
            ->setCellValue('B1', 'name')
            ->setCellValue('C1', 'sex')
            //设置第一行为红色字体
            ->getStyle('A1:C1')->getFont()->getColor()->setARGB(PHPExcel_Style_Color::COLOR_RED);

        $key = 1;
        /*以下就是对处理Excel里的数据，横着取数据*/
        foreach($data as $v){

            //设置循环从第二行开始
            $key++;
            $objPHPExcel->getActiveSheet()
                //Excel的第A列，name是你查出数组的键值字段，下面以此类推
                ->setCellValue('A'.$key, $v['id'])
                ->setCellValue('B'.$key, $v['name'])
                ->setCellValue('C'.$key, $v['sex']);

        }
        //设置当前的表格
        $objPHPExcel->setActiveSheetIndex(0);


        header('Content-Type: application/vnd.ms-excel');           //文件类型
        header('Content-Disposition: attachment;filename="1.xls"'); //文件名
        header('Cache-Control: max-age=0');
        header('Content-Type: text/html; charset=utf-8');           //编码
        $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');  //excel 2003
        $objWriter->save('php://output');
    }


    /**
     * excel文件导入数据库
     */
    public function import($filename)
    {
        //error_reporting(E_ERROR);
        //实例化
        $objPHPExcel = new PHPExcel();
        $objReader = PHPExcel_IOFactory::createReader('Excel5');    //use excel2007 for 2007 format

        //接收存在缓存中的excel表格
        $objPHPExcel = $objReader->load($filename);                 //$filename可以是上传的表格，或者是指定的表格
        $sheet = $objPHPExcel->getSheet(0);
        $highestRow = $sheet->getHighestRow();                      // 取得总行数


        $insertNum = 0;
        $db = Db::getInstance();

        for($j=2;$j<=$highestRow;$j++)
        {
            $a = $objPHPExcel->getActiveSheet()->getCell("A".$j)->getValue();//获取A列的值
            $b = $objPHPExcel->getActiveSheet()->getCell("B".$j)->getValue();//获取B列的值
            $c = $objPHPExcel->getActiveSheet()->getCell("C".$j)->getValue();//获取C列的值

            $res = $db->table('test')->insert(['id'=>$a, 'name'=>$b, 'sex'=>$c]);

            $res && $insertNum++;
        }

        return $insertNum;
    }


}