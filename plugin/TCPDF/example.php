<?php


// +----------------------------------------------------------------------
// | Author: jiexianluo@hotmail.com
// | Date  : 2018/3/5
// | Time  : 14:57
// +----------------------------------------------------------------------


class PDF
{
    private $pdf;

    public function __construct()
    {
        $this->pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

        // set document information
        $this->pdf->SetCreator(PDF_CREATOR);
        $this->pdf->SetAuthor('TCPDF');
        $this->pdf->SetTitle('PDF');
        $this->pdf->SetSubject('PDF');
        $this->pdf->SetKeywords('PDF');

        // set default header data
        //$this->pdf->SetHeaderData(PDF_HEADER_LOGO, PDF_HEADER_LOGO_WIDTH, PDF_HEADER_TITLE . ' 061', PDF_HEADER_STRING);

        // set header and footer fonts
        $this->pdf->setHeaderFont(Array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));
        $this->pdf->setFooterFont(Array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));

        // set default monospaced font
        $this->pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);

        // set margins
        $this->pdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);
        $this->pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
        $this->pdf->SetFooterMargin(PDF_MARGIN_FOOTER);

        // set auto page breaks
        $this->pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);

        // set image scale factor
        $this->pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);

        // ---------------------------------------------------------

        // set font
        $this->pdf->SetFont('droidsansfallback', '', 10);

        // add a page
        $this->pdf->AddPage();
    }


    public function output()
    {
        $htmlFile = dirname(__FILE__) . "/../../tpl/pi.html";

        $html = file_get_contents($htmlFile);

        //填充订单数据
        $finder = [
            '{invoiceDate}','{invoiceDueDate}','{invoicedName}',
            '{invoicedAddr}','{invoicedCountry}'
        ];

        $replace = [
            '','','','',''
        ];

        $html = str_replace($finder,$replace,$html);

        // output the HTML content
        $this->pdf->writeHTML($html, true, false, true, false, '');

        $this->pdf->lastPage();

        $this->pdf->Output('pi.pdf', 'I');
    }

}