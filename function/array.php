<?php
// +----------------------------------------------------------------------
// | Author: jiexianluo@hotmail.com
// | Date  : 2018/1/24
// | Time  : 11:14
// +----------------------------------------------------------------------
$data = [
    ['id'=>1,'pid'=>0,'name'=>'a'],
    ['id'=>2,'pid'=>1,'name'=>'b'],
    ['id'=>7,'pid'=>4,'name'=>'g'],
    ['id'=>9,'pid'=>7,'name'=>'i'],
    ['id'=>3,'pid'=>1,'name'=>'c'],
    ['id'=>4,'pid'=>2,'name'=>'d'],
    ['id'=>8,'pid'=>3,'name'=>'h'],
    ['id'=>5,'pid'=>2,'name'=>'e'],
    ['id'=>6,'pid'=>3,'name'=>'f'],
    ['id'=>10,'pid'=>6,'name'=>'j'],
];
/**
 * 数组转树状结构
 * @param array $data   要转化的数组
 * @param int $pid      父级id
 * @param int $level    层级
 * @return array|void   转化后的树状结构
 */
function array2tree($data = [],$pid = 0,$level = 1)
{
    if(empty($data)) {
        return ;
    }
    $data = array_column($data,null,'id');
    ksort($data);

    $tree = [];
    foreach($data as $value){
        if($value['pid'] == $pid){
            $value['level'] = $level;
            $value['child'] = array2tree($data,$value['id'],++$level);
            $tree[] = $value;
        }
    }
    return $tree;
}

/**
 * 树状结构转数组
 * @param array $tree     树状结构
 * @param string $prefix  层级前缀
 * @return array|void     输出数组
 */
function tree2array($tree = [],$prefix='|-')
{
    if(empty($tree)){
        return ;
    }

    $data = [];
    foreach($tree as $value) {
        $value['html'] = str_pad($prefix, $value['level'], ' ', STR_PAD_LEFT);
        $child = $value['child'];
        unset($value['child']);
        $data[] = $value;

        if (!empty($child)) {
            $data = array_merge($data,tree2array($child));
        }
    }
    return $data;
}

var_dump(tree2array(array2tree($data)));die;