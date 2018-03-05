<?php
namespace QRcode;

// +----------------------------------------------------------------------
// | Author: jiexianluo@hotmail.com
// | Date  : 2018/3/2
// | Time  : 18:10
// +----------------------------------------------------------------------
class QRencode
{

    public $casesensitive = true;
    public $eightbit = false;

    public $version = 0;
    public $size = 3;
    public $margin = 4;

    public $structured = 0; // not supported yet

    public $level = 0;
    public $hint = 2;

    //----------------------------------------------------------------------
    public static function factory($level = 0, $size = 3, $margin = 4)
    {
        $enc = new QRencode();
        $enc->size = $size;
        $enc->margin = $margin;

        switch ($level.'') {
            case '0':
            case '1':
            case '2':
            case '3':
                $enc->level = $level;
                break;
            case 'l':
            case 'L':
                $enc->level = 0;
                break;
            case 'm':
            case 'M':
                $enc->level = 1;
                break;
            case 'q':
            case 'Q':
                $enc->level = 2;
                break;
            case 'h':
            case 'H':
                $enc->level = 3;
                break;
        }

        return $enc;
    }

    //----------------------------------------------------------------------
    public function encodeRAW($intext, $outfile = false)
    {
        $code = new QRcode();

        if($this->eightbit) {
            $code->encodeString8bit($intext, $this->version, $this->level);
        } else {
            $code->encodeString($intext, $this->version, $this->level, $this->hint, $this->casesensitive);
        }

        return $code->data;
    }

    //----------------------------------------------------------------------
    public function encode($intext, $outfile = false)
    {
        $code = new QRcode();

        if($this->eightbit) {
            $code->encodeString8bit($intext, $this->version, $this->level);
        } else {
            $code->encodeString($intext, $this->version, $this->level, $this->hint, $this->casesensitive);
        }

        QRtools::markTime('after_encode');

        if ($outfile!== false) {
            file_put_contents($outfile, join("\n", QRtools::binarize($code->data)));
        } else {
            return QRtools::binarize($code->data);
        }
    }

    //----------------------------------------------------------------------
    public function encodePNG($intext, $outfile = false,$saveandprint=false)
    {
        try {

            ob_start();
            $tab = $this->encode($intext);
            $err = ob_get_contents();
            ob_end_clean();

            if ($err != '')
                QRtools::log($outfile, $err);

            $maxSize = (int)(1024 / (count($tab)+2*$this->margin));

            QRimage::png($tab, $outfile, min(max(1, $this->size), $maxSize), $this->margin,$saveandprint);

        } catch (\Exception $e) {

            QRtools::log($outfile, $e->getMessage());

        }
    }
}