<?php
// +----------------------------------------------------------------------
// | Author: jiexianluo@hotmail.com
// | Date  : 2018/2/5
// | Time  : 9:59
// +----------------------------------------------------------------------

$data = [3,2,4,5,9,7,6,8,1];


/**
 * 冒泡排序
 * @param $arr
 * @return mixed
 */
function bubbleSort($arr)
{
    $len = count($arr);
    for($i=0;$i<$len;$i++){
        for($j=1;$j<$len-$i;$j++ ){
            if($arr[$j]>$arr[$j-1]){
                $tmp = $arr[$j];
                $arr[$j] = $arr[$j-1];
                $arr[$j-1] = $tmp;
            }
        }
    }
    return $arr;
}


function selectSort($arr)
{

}

var_dump(bubbleSort($data));die;