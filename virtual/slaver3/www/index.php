<?php
session_start();
try {
    $con = new PDO('mysql:host=mysql;dbname=test', 'root', '123456');
    $con->query('SET NAMES UTF8');
    $res =  $con->query('select * from user');
    while ($row = $res->fetch(PDO::FETCH_ASSOC)) {
        echo "id:{$row['id']} name:{$row['name']}<br/>";
    }
} catch (PDOException $e) {
     echo 'error'  . $e->getMessage();
}


?>


