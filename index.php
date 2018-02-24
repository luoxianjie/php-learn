<?php
// +----------------------------------------------------------------------
// | Author: jiexianluo@hotmail.com
// | Date  : 2018/2/8
// | Time  : 16:49
// +----------------------------------------------------------------------


/**
 * 单文件上传
 */
function upload_file($filename,$path)
{
    $fileInfo = $_FILES[$filename];

    // 获取文件信息
    $tmp_name=$fileInfo["tmp_name"];
    $size=$fileInfo["size"];
    $error=$fileInfo["error"];
    $type=$fileInfo["type"];

    //获取文件后缀
    $ext=pathinfo($filename,PATHINFO_EXTENSION);

    //文件目的地址信息
    if (!file_exists($path)) {
        mkdir($path,0777,true);
        chmod($path, 0777);
    }
    $uniName=md5(uniqid(microtime(true),true)).'.'.$ext;
    $destination=$path."/".$uniName;


    if ($error==0) {
        // 最大允许上传四兆文件
        if ($size > 4*1024*1024) {
            exit("上传文件过大！");
        }
        if (!in_array($ext, ['.jpg','.png','.jpeg','.bmp','.txt','.doc'])) {
            exit("非法文件类型");
        }
        if (!is_uploaded_file($tmp_name)) {
            exit("上传方式有误，请使用post方式");
        }
        //判断是否为真实图片（防止伪装成图片的病毒一类的
        if (!getimagesize($tmp_name)) {//getimagesize真实返回数组，否则返回false
            exit("不是真正的图片类型");
        }
        if (@move_uploaded_file($tmp_name, $destination)) {//@错误抑制符，不让用户看到警告
            echo "文件".$filename."上传成功!";
        }else{
            echo "文件".$filename."上传失败!";
        }


    }else{
        switch ($error){
            case 1:
                echo "超过了上传文件的最大值，请上传2M以下文件";
                break;
            case 2:
                echo "上传文件过多，请一次上传20个及以下文件！";
                break;
            case 3:
                echo "文件并未完全上传，请再次尝试！";
                break;
            case 4:
                echo "未选择上传文件！";
                break;
            case 7:
                echo "没有临时文件夹";
                break;
        }
    }
    return $destination;
}

function upload_files($filename)
{

}


/**
 * 文件下载
 */

function download_file($path)
{

}

/**
 * 图片裁剪处理
 */
function image_clip($path)
{

}


/**
 * 图片加水印处理
 */
function image_watermark($image_path,$watermaker_path)
{

}

