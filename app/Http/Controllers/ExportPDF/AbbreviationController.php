<?php

namespace App\Http\Controllers\ExportPDF;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class AbbreviationController extends Controller
{
    public function abbreviation($request,$pdf,$orie='landscape'){

    	$abb_billing = @$request['abb_billing'];
    	$abb_rendering = @$request['abb_rendering'];
    	$abb_insurance = @$request['abb_insurance'];
    	$abb_facility = @$request['abb_facility'];
    	$abb_user = @$request['abb_user'];
    	$abb_pos = @$request['abb_pos'];

    	if ($orie == 'portrait') {
    		$x = 150;
    	}
    	else{
    		$x = 230;
    	}

    	if (!empty($abb_billing)) {
			$x_axis = $pdf->getx();
		    $pdf->SetFont('Calibri-Bold','',7);
			$pdf->Vcell(30,10,$x_axis,"Billing Provider",20,"","L");
			$pdf->Ln();
			foreach ($abb_billing as $key => $value) {
				$x_axis = $pdf->getx();
				$text = $value;
				$lengthToSplit = strlen($text);
				$pdf->SetFont('Calibri','',7);
				$pdf->Vcell(40,5,$x_axis,$text,35,"","L");
				if ($x_axis >= $x) {
					$pdf->Ln();
				}
			}
			$pdf->Ln();
		}

		if (!empty($abb_rendering)) {
			$x_axis = $pdf->getx();
		    $pdf->SetFont('Calibri-Bold','',7);
			$pdf->Vcell(30,10,$x_axis,"Rendering Provider",20,"","L");
			$pdf->Ln();
			foreach ($abb_rendering as $key => $value) {
				$x_axis = $pdf->getx();
				$text = $value;
				$lengthToSplit = strlen($text);
				$pdf->SetFont('Calibri','',7);
				$pdf->Vcell(40,5,$x_axis,$text,35,"","L");
				if ($x_axis >= $x) {
					$pdf->Ln();
				}
			}
			$pdf->Ln();
		}

		if (!empty($abb_facility)) {
			$x_axis = $pdf->getx();
		    $pdf->SetFont('Calibri-Bold','',7);
			$pdf->Vcell(30,10,$x_axis,"Facility Provider",20,"","L");
			$pdf->Ln();
			foreach ($abb_facility as $key => $value) {
				$x_axis = $pdf->getx();
				$text = $value;
				$lengthToSplit = strlen($text);
				$pdf->SetFont('Calibri','',7);
				$pdf->Vcell(40,5,$x_axis,$text,35,"","L");
				if ($x_axis >= $x) {
					$pdf->Ln();
				}
			}
			$pdf->Ln();
		}

		if (!empty($abb_insurance)) {
			$x_axis = $pdf->getx();
		    $pdf->SetFont('Calibri-Bold','',7);
			$pdf->Vcell(30,10,$x_axis,"Insurance",20,"","L");
			$pdf->Ln();
			foreach ($abb_insurance as $key => $value) {
				$x_axis = $pdf->getx();
				$text = $value;
				$lengthToSplit = strlen($text);
				$pdf->SetFont('Calibri','',7);
				$pdf->Vcell(50,5,$x_axis,$text,45,"","L");
				if ($x_axis >= $x) {
					$pdf->Ln();
				}
			}
			$pdf->Ln();
		}

		if (!empty($abb_user)) {
			$x_axis = $pdf->getx();
		    $pdf->SetFont('Calibri-Bold','',7);
			$pdf->Vcell(30,10,$x_axis,"User",20,"","L");
			$pdf->Ln();
			foreach ($abb_user as $key => $value) {
				$x_axis = $pdf->getx();
				$text = $value;
				$lengthToSplit = strlen($text);
				$pdf->SetFont('Calibri','',7);
				$pdf->Vcell(50,5,$x_axis,$text,45,"","L");
				if ($x_axis >= $x) {
					$pdf->Ln();
				}
			}
			$pdf->Ln();
		}

		if (!empty($abb_pos)) {
			$x_axis = $pdf->getx();
		    $pdf->SetFont('Calibri-Bold','',7);
			$pdf->Vcell(30,10,$x_axis,"POS",20,"","L");
			$pdf->Ln();
			foreach ($abb_pos as $key => $value) {
				$x_axis = $pdf->getx();
				$text = $value;
				$lengthToSplit = strlen($text);
				$pdf->SetFont('Calibri','',7);
				$pdf->Vcell(50,5,$x_axis,$text,45,"","L");
				if ($x_axis >= $x) {
					$pdf->Ln();
				}
			}
			$pdf->Ln();
		}
    }
}
