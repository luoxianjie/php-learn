<?php

require __DIR__."/vendor/autoload.php";

$file = new File();

$data = $file->uploads('file');

if($data !==false ){
    echo json_encode(['status'=>200,'save_name'=>$data]);
}else{
    echo json_encode(['status'=>404,'message'=>$file->error]);
}

