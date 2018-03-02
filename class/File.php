<?php
// +----------------------------------------------------------------------
// | Author: jiexianluo@hotmail.com
// | Date  : 2018/2/28
// | Time  : 15:00
// +----------------------------------------------------------------------
/**
 * 文件处理类
 * Class File
 */
class File
{
    private $save_path;        // 默认上传的路径
    private $allow_type = [];  // 允许上传的文件类型

    private $file;             // 文件句柄

    public $error;            // 错误信息

    public function __construct($save_path = './upload',$allow_type = ['jpeg','jpg','png','gif','doc','txt'])
    {
        $this->save_path = $save_path;
        $this->allow_type = $allow_type;
    }


    /**
     * 单个文件上传
     * @param $file
     * @return bool|string
     */
    public function upload($file)
    {
        // 文件信息
        $this->file = $_FILES[$file];

        $fileExt = $this->_getExt($this->file['name']);

        if(!in_array($fileExt,$this->allow_type)){
            $this->error = '文件类型有误!';
            return false;
        }

        switch ($this->file['error']){
            case 0: $this->error = ""; break;
            case 1: $this->error = "文件大小超出ini文件限制!"; break;
            case 2: $this->error = "文件大小超出MAX_FILE_SIZE限制!"; break;
            case 3: $this->error = "文件被部分上传!"; break;
            case 4: $this->error = "没有文件上传!"; break;
            case 5: $this->error = "文件大小为0!"; break;
            default: $this->error = "文件上传失败!"; break;
        }

        if($this->error){
            return false;
        }

        if(!is_uploaded_file($this->file['tmp_name'])){
            $this->error = '文件非法!';
            return false;
        }

        if(!is_dir($this->save_path)){
            mkdir($this->save_path,'0777');
        }

        $save_name = $this->_getFileName($this->file['name']);

        if(move_uploaded_file($this->file['tmp_name'],$save_name)){
            return $save_name;
        }else{
            $this->error = "上传失败!";
            return false;
        }

    }

    /**
     * 多个文件上传
     * @param $file
     * @return array|bool|string
     */
    public function uploads($file)
    {
        // 文件信息
        $this->file = $_FILES[$file];

        if(!is_array($this->file['name'])){
            return $this->upload($file);
        }
        $save_names = [];
        foreach ($this->file['name'] as $key=> $name) {
            $fileExt = $this->_getExt($this->file['name'][$key]);

            if (!in_array($fileExt, $this->allow_type)) {
                $this->error = $name.'文件类型有误!';
                return false;
            }

            switch ($this->file['error'][$key]) {
                case 0:
                    $this->error = "";
                    break;
                case 1:
                    $this->error = $name."文件大小超出ini文件限制!";
                    break;
                case 2:
                    $this->error = $name."文件大小超出MAX_FILE_SIZE限制!";
                    break;
                case 3:
                    $this->error = $name."文件被部分上传!";
                    break;
                case 4:
                    $this->error = $name."没有文件上传!";
                    break;
                case 5:
                    $this->error = $name."文件大小为0!";
                    break;
                default:
                    $this->error = $name."文件上传失败!";
                    break;
            }

            if ($this->error) {
                return false;
            }

            if (!is_uploaded_file($this->file['tmp_name'][$key])) {
                $this->error = $name.'文件非法!';
                return false;
            }

            if (!is_dir($this->save_path)) {
                mkdir($this->save_path, '0777');
            }

            $save_name = $this->_getFileName($this->file['name'][$key]);

            if (move_uploaded_file($this->file['tmp_name'][$key], $save_name)) {
                $save_names[] = $save_name;
            } else {
                $this->error = $name."上传失败!";
                return false;
            }
        }
        return $save_names;
    }

    /**
     * 下载文件
     * @param $filename
     */
    public function download($filename)
    {
        if(!file_exists($filename)){
            die('文件不存在!');
        }

        $filesize = filesize($filename);
        $basename = basename($filename);

        Header("Content-type: application/octet-stream");
        Header("Accept-Ranges: bytes");
        Header("Accept-Length: " . $filesize);
        Header("Content-Disposition: attachment; filename=" . $basename);

        $file = fopen($filename,'r');
        echo fread($file, $filesize);
        fclose($file);
    }

    /**
     * 获取文件类型
     * @param $filename
     * @return mixed
     */
    private function _getExt($filename)
    {
        $arr = explode('.',$filename);
        $ext = end($arr);
        return $ext;
    }


    /**
     * 获取文件存储路径
     * @param $filename
     * @return string
     */
    private function _getFileName($filename)
    {
        $dirname = rtrim($this->save_path,'/').'/'.date('ymd');

        if(!is_dir($dirname)){
            mkdir($dirname,'0777');
        }

        $ext = $this->_getExt($filename);

        $saveName = $dirname.'/'.md5(uniqid().$filename).'.'.$ext;

        return $saveName;
    }

}

/*<html>
<head>
    <title>upload file</title>
</head>
<body>
<div style="width:300px;height:200px;margin:100px auto;">
<form action="/" method="post" enctype="multipart/form-data">
    <input name="MAX_FILE_SIZE" value="300000" type="hidden" />
    <label>选择文件1：</label><input name="file" type="file" /><br/>
    <input name="submit" type="submit" value="上传"/>
</form>
</div>
</body>
</html>*/

/*// 文件上传

$file = new File(__DIR__.'/uploads');

$res = $file->uploads('file');

var_dump($res,$file->error);

// 文件下载

$file = new File();

$res = $file->download(__DIR__.'/uploads/180228/9f2da6d02298c01fd2444c369104ff99.jpg');

var_dump($res,$file->error);*/