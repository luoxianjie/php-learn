<?php

require __DIR__."/vendor/autoload.php";

$pdf = new Pdf();

$res = $pdf->output();

var_dump($res);