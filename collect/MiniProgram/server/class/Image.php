<?php
// +----------------------------------------------------------------------
// | Author: jiexianluo@hotmail.com
// | Date  : 2018/2/8
// | Time  : 16:49
// +----------------------------------------------------------------------

/**
 * 图片处理类
 * Class Image
 */
class Image
{

    private $path;      // 图片路径
    public  $picSrc;    // 图片地址
    private $prefix;    // 缩略图前缀
    private $filename;  // 图片名称

    public function __construct($path = '.' )
    {
        $this->path = $path;
    }

    /**
     * 打开资源图片
     * @param $name
     */
    public function open($name)
    {
        $this->filename = $name;
        $this->picSrc = rtrim($this->path,'/').'/'.$name;
    }

    /**
     * 缩放图片
     * @param $width 宽度
     * @param $height 高度
     * @param string $prefix 缩放前缀
     */
    public function thumb($width, $height, $prefix='th_')
    {
        $this->prefix = $prefix;

        // 获取图片信息
        $imageInfo = $this->getImageInfo();
        // 创建图片资源
        $src_im = $this->createBackgroundIm($imageInfo);

        // 缩放比例
        $scale_x = $imageInfo['width'] / $width;
        $scale_y = $imageInfo['height'] / $height;
        $scale =  $scale_x > $scale_y ? $scale_x:$scale_y;

        $dst_x = ($scale == $scale_x) ? 0 : ($width - floor($imageInfo['width'] / $scale)) /2;
        $dst_y = ($scale == $scale_x) ? ($height - floor($imageInfo['height'] / $scale)) / 2 : 0;

        $dst_w = ($scale == $scale_x) ? $width : floor($imageInfo['width'] / $scale);
        $dst_h = ($scale == $scale_x) ? floor($imageInfo['height'] / $scale) : $height;

        // 创建目标图片资源
        $dst_im = imagecreatetruecolor($width, $height);

        // 复制图片
        imagecopyresampled($dst_im, $src_im, $dst_x, $dst_y,0,0, $dst_w, $dst_h, $imageInfo['width'], $imageInfo['height']);

        // 输出图片
        $dst_name = $this->outputImg($dst_im, $imageInfo);

        imagedestroy($src_im);
        imagedestroy($dst_im);

        return $dst_name;
    }

    /**
     * 图片加水印
     * @param $waterImg 水印路径
     * @param $pos 方向 默认右下角
     * @param string $prefix 前缀
     * @return string 加水印处理后图片路径
     */
    public function watermark($waterImg, $pos = 5, $prefix = 'wt_')
    {
        // 原图片信息
        $srcInfo = $this->getImageInfo();

        // 创建目标图片背景资源
        $dstIm = $this->createBackgroundIm($srcInfo);

        // 水印图片信息
        $waterInfo = $this->getImageInfo($waterImg);

        // 创建水印图片目标资源
        $waterIm = $this->createBackgroundIm($waterInfo, $waterImg);

        // 水印位置
        $x=$y=0;

        switch ($pos){
            case 1:         // 左上角
                break;
            case 2:         // 右上角
                $x = $srcInfo['width'] - $waterInfo['width'];
                $y = 0;
                break;
            case 3:         //居中
                $x = ($srcInfo['width'] - $waterInfo['width']) / 2;
                $y = ($srcInfo['height'] - $waterInfo['height']) / 2;
                break;
            case 4:         //左下角
                $x = 0;
                $y = $srcInfo['height'] - $waterInfo['width'];
                break;
            case 5:
                $x = $srcInfo['width'] - $waterInfo['width'];
                $y = $srcInfo['height'] - $waterInfo['height'];
                break;
        }

        imagecopymerge($dstIm, $waterIm, $x, $y,0,0, $waterInfo['width'], $waterInfo['height'],80);

        // 保存图片
        $dst_name = $this->outputImg($dstIm, $srcInfo, $prefix);

        // 销毁资源
        imagedestroy($dstIm);
        imagedestroy($waterIm);

        return $dst_name;
    }

    /**
     * 剪切图片
     * @param $width  长度
     * @param $height  宽度
     * @param $pos    方向 默认居中
     * @param string $prefix 前缀
     * @return string 路径
     */
    public function cut($width, $height, $pos = 3, $prefix = 'ct_')
    {
        // 获取原图片信息
        $srcInfo = $this->getImageInfo();

        if($srcInfo['width'] < $width || $srcInfo['height'] < $height) die('图片长度或宽度小于裁剪长度或宽度');

        // 获取原图片资源
        $srcIm = $this->createBackgroundIm($srcInfo);

        // 创建目标图片背景资源
        $dstIm = imagecreatetruecolor($width, $height);

        switch ($pos){
            case 1:     // 从左上角开始裁剪
                $x = 0;
                $y = 0;
                break;
            case 2:    // 从右上角开始裁剪
                $x = ($srcInfo['width'] - $width);
                $y = 0;
                break;
            case 3:
                $x = ($srcInfo['width'] - $width)/2;
                $y = ($srcInfo['height'] - $height)/2;
                break;
            case 4:
                $x = 0;
                $y = $srcInfo['height'] - $height;
                break;
            case 5:
                $x = $srcInfo['width'] - $width;
                $y = $srcInfo['height'] - $height;
                break;
        }

        imagecopyresampled($dstIm, $srcIm,0,0, $x, $y, $width, $height, $width, $height);

        // 输出图片
        $dst_name = $this->outputImg($dstIm, $srcInfo, $prefix);

        // 销毁资源
        imagedestroy($dstIm);
        imagedestroy($srcIm);

        return $dst_name;

    }

    /**
     * 获取图片详细信息
     * @return mixed
     */
    private function getImageInfo($filename = '')
    {
        $filename = empty($filename) ? $this->picSrc:$filename;
        $data = getimagesize($filename);
        $imageInfo['width'] = $data[0];
        $imageInfo['height'] = $data[1];
        $imageInfo['type']  = $data[2];

        return $imageInfo;
    }

    /**
     * 创建背景图片资源
     * @param $imageInfo
     * @return resource
     */
    private function createBackgroundIm($imageInfo, $filename = '')
    {
        $filename = empty($filename) ? $this->picSrc : $filename;
        switch  ($imageInfo['type']) {
            case 1:
                $img = imagecreatefromgif($filename);
                break;
            case 2:
                $img = imagecreatefromjpeg($filename);
                break;
            case 3:
                $img = imagecreatefrompng($filename);
                break;
            default :
                die('不支持的图片类型');
                break;
        }
        return $img;
    }

    /**
     * 保存图片
     * @param $im
     * @param $imageInfo
     * @return string
     */
    private function outputImg($im, $imageInfo, $prefix = '')
    {
        $prefix = empty($prefix) ? $this->prefix : $prefix;
        // 目标地址
        $dst = rtrim($this->path, '/') .'/'.$prefix.$this->filename;

        switch ($imageInfo['type']){
            case 1:
                imagegif($im, $dst);
                break;
            case 2:
                imagejpeg($im, $dst);
                break;
            case 3:
                imagepng($im, $dst);
                break;
            default:
                die('图片类型有误');
                break;
        }

        return $dst;
    }


}

$image = new Image(__DIR__.'/pic');

$image->open('1.png');
//$res = $image->thumb(1000,1000);
//$res = $image->watermark(__DIR__.'/pic/3.png');
//$res = $image->cut(1000,300,1);

//var_dump($res);