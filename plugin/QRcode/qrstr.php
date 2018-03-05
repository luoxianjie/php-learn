<?php
namespace QRcode;

// +----------------------------------------------------------------------
// | Author: jiexianluo@hotmail.com
// | Date  : 2018/3/2
// | Time  : 18:23
// +----------------------------------------------------------------------

class qrstr {
    public static function set(&$srctab, $x, $y, $repl, $replLen = false) {
        $srctab[$y] = substr_replace($srctab[$y], ($replLen !== false)?substr($repl,0,$replLen):$repl, $x, ($replLen !== false)?$replLen:strlen($repl));
    }
}