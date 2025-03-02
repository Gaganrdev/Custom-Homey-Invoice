<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once('/srv/htdocs/wp-content/plugins/tcpdf/tcpdf.php'); // tcpdf path

$pdf = new TCPDF();
$pdf->AddPage();
$pdf->SetFont('helvetica', 'B', 16);
$pdf->Cell(0, 10, '✅ TCPDF is working!', 0, 1, 'C');
$pdf->Output('test.pdf', 'D');
?>