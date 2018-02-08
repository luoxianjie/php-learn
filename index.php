<?php
// +----------------------------------------------------------------------
// | Author: jiexianluo@hotmail.com
// | Date  : 2018/2/8
// | Time  : 16:49
// +----------------------------------------------------------------------

/**
 * file_put_content 写文件
 */
/*function handle()
{   $start_time = time();
    for($i=0;$i<10000;$i++){
        echo "waiting\r\n";
        file_put_contents('test.txt',"test ...\r\n",FILE_APPEND);
    }
    $end_time = time();
    $time = $end_time - $start_time;
    print('time:'.$time);
}*/

/*function handle()
{
    $start_time = time();
    $handle = fopen('test.txt','wr');
    flock($handle,LOCK_EX | LOCK_NB);
    for($i=0;$i<10000;$i++){
        echo "waiting{$i}\r\n";
        fwrite($handle,"test ...\r\n");
    }
    $end_time = time();
    $time = $end_time - $start_time;
    print('time:'.$time);
}*/

//handle();