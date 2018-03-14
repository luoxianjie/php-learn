<?php

require __DIR__."/vendor/autoload.php";


$config = [
    'clientId'      =>'AYl9tFRD8f3Di-j-8Ysea7__fNXL_h3HWpcSjZqL415DX_FdD0HU1wzUoVZ9lRKIbj3EypLbOkzXWKAq',
    'clientSecret'  =>'EAcL6WkKLMvyw91wJ2UdyoWZhijqQIv8IrprOyHmZjJOloAIbOWp1R-9bMol65nxC7VPVrDfis4yuzLf'
];

$act = isset($_GET['act'])?trim($_GET['act']):'payment';

(new \PayPal($config))->$act();