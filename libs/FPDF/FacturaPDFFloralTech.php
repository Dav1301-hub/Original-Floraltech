<?php
/**
 * Clase FacturaPDF para FloralTech. Se carga solo cuando se genera una factura en PDF.
 * Ruta correcta para servidores case-sensitive (Linux): FPDF con mayúsculas.
 */
if (!class_exists('FPDF', false)) {
    require_once __DIR__ . '/fpdf.php';
}

class FacturaPDF extends FPDF {
    function Cell($w, $h = 0, $txt = '', $border = 0, $ln = 0, $align = '', $fill = false, $link = '') {
        parent::Cell($w, $h, $this->fixEncoding($txt), $border, $ln, $align, $fill, $link);
    }

    function MultiCell($w, $h, $txt, $border = 0, $align = 'J', $fill = false) {
        parent::MultiCell($w, $h, $this->fixEncoding($txt), $border, $align, $fill);
    }

    private function fixEncoding($text) {
        if (mb_detect_encoding($text, 'UTF-8', true)) {
            return iconv('UTF-8', 'windows-1252', $text);
        }
        return $text;
    }

    function Header() {
        $this->SetTextColor(79, 129, 189);
        $this->SetFont('Arial','B',16);
        $this->Cell(0,10,'FACTURA ELECTRÓNICA',0,1,'R');
        $this->SetFont('Arial','',10);
        $this->Cell(0,5,'FloralTech - Sistema de venta de flores online',0,1,'R');
        $this->SetDrawColor(79, 129, 189);
        $this->SetLineWidth(0.5);
        $this->Line(10, 25, 200, 25);
        $this->SetY(30);
        $this->Image('assets/images/logoepymes.png', 10, 30, 40);
        $this->Ln(50);
    }

    function Footer() {
        $this->SetY(-15);
        $this->SetFont('Arial','I',8);
        $this->Cell(0,10,'Página '.$this->PageNo().'/{nb}',0,0,'C');
    }
}
