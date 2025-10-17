<?php

function fontFormat($str) {
	if ( isset($_GET['pdf']) && isset($_GET['rtl']) ) {
		return iconv('UTF-8', 'windows-1255', $str);
	} else {
		return $str;
	}
}

if ( isset($_GET['pdf']) && isset($_GET['ad_id']) && isset($_GET['stats']) &&
	 $_GET['stats'] >= 7 && $_GET['stats'] <= 90 && $_GET['pdf'] == substr(md5($_GET['ad_id'].'1'), 1, 11) ) {
	$ad_id = $_GET['ad_id'];
	$ma = (isset($_GET['ma']) && $_GET['ma'] == '1' ? true : false);
	if ( file_exists(dirname(__FILE__).'/PDF/reports/ad-'.$ad_id.'.txt') ) {
		require_once( dirname( __FILE__ ) . '/PDF/fpdf.php' );
		$pdf = new FPDF();
		$pdf->AddPage();
		$pdf->SetFont('Arial','B',12);
		$days = $_GET['stats'];
		$data = file_get_contents(dirname(__FILE__).'/PDF/reports/ad-'.$ad_id.'.txt', true);
		$pdf->SetFontSize(10);
		$pdf->Cell(190, 8, date('d/m/Y H:i:s'), 0, 1, "R");
		$pdf->SetFontSize(14);
		$pdf->Cell(190, 14, 'ID: '.$ad_id, 0, 0, "C");
		$pdf->SetFontSize(10);
		$pdf->Ln(15);
		$i = 0;
		$d = 0;
		$n = 0;
		$arrays = json_decode($data, true);
		$values = explode('|', $arrays['stats']);

//		echo '<pre>';
//		var_dump($values);
//		echo '</pre>';

		$pdf->SetFontSize(11);
		$viewableSize = 20;
		if ( is_array($arrays['total']) ) {
			$viewable = ($arrays['total']['viewable'] != '' ? $arrays['total']['viewable'] : '');
			if ( $viewable != '' && $ma == false ) {
				$viewableSize = 0;
			} else {
				$viewableSize = 20;
			}
			$pdf->SetDrawColor(211,211,211);
			$pdf->SetFillColor(211,211,211);
			$pdf->Cell(190, 2, '', 1, 1, "C", true);
			$pdf->Cell(40 + $viewableSize, 10, fontFormat($arrays['total']['clicks']), 1, 0, "C", true);
			$pdf->Cell(50 + $viewableSize, 10, fontFormat($arrays['total']['views']), 1, 0, "C", true);
			$pdf->Cell(40 + $viewableSize, 10, fontFormat($arrays['total']['ctr']), 1, ($viewableSize == 0 && $ma == false ? 0 : 1), "C", true);
			if ( $viewableSize == 0 && $ma == false ) {
				$pdf->Cell(60, 10, fontFormat($arrays['total']['viewable']), 1, 1, "C", true);
			}
			$pdf->Cell(40 + $viewableSize, 10, $arrays['total']['clicks_val'], 1, 0, "C", true);
			$pdf->Cell(50 + $viewableSize, 10, $arrays['total']['views_val'], 1, 0, "C", true);
			$pdf->Cell(40 + $viewableSize, 10, $arrays['total']['ctr_val'].'%', 1, ($viewableSize == 0 && $ma == false ? 0 : 1), "C", true);
			if ( $viewableSize == 0 && $ma == false ) {
				$min = $arrays['total']['viewable_val'] > 60 ? $arrays['total']['viewable_val'] / 60 : 0;
				$sec = ($min - intval($min)) * 60;
				$pdf->Cell(60, 10, intval($min) . ' ' . $arrays['total']['viewable_min'] . ' ' . intval($sec) . ' ' . $arrays['total']['viewable_sec'], 1, 1, "C", true);
			}

			$pdf->Cell(190, 2, '', 1, 1, "C", true);
			$pdf->SetDrawColor(249,192,50);
			$pdf->SetFillColor(249,192,50);
			$pdf->Cell(40 + $viewableSize, 1, '', 1, 0, "C", true);
			$pdf->SetDrawColor(103,58,183);
			$pdf->SetFillColor(103,58,183);
			$pdf->Cell(50 + $viewableSize, 1, '', 1, 0, "C", true);
			$pdf->SetDrawColor(42,150,243);
			$pdf->SetFillColor(42,150,243);
			$pdf->Cell(40 + $viewableSize, 1, '', 1, ($viewableSize == 0 && $ma == false ? 0 : 1), "C", true);
			if ( $viewableSize == 0 && $ma == false ) {
				$pdf->SetDrawColor(76, 175, 80);
				$pdf->SetFillColor(76, 175, 80);
				$pdf->Cell(60, 1, '', 1, 1, "C", true);
			}
		}

		$pdf->SetFontSize(10);
		$pdf->SetTextColor(0,0,0);
		$pdf->SetDrawColor(211,211,211);
		$pdf->SetFillColor(211,211,211);

		$day = 1;
		foreach($values as $columnValue) {

			// break foreach loop
			if ( $day > $_GET['stats'] ) {
				break;
			}

			$entry = explode(';', $columnValue);
			if ( isset($entry[1]) ) {
				if ( $d - date('z', $entry[1]) > $days ) {
					break;
				}
			}
			if ( $entry[0] != '' ) {
				if ( date('W', $entry[1]) != $i ) {
					$pdf->Ln(5);
					$pdf->Cell(190, 8, '- - - - - - - - - - - - - -', 0, 1, "C");
					$i = date('W', $entry[1]);
				}
				$k = 0;
				if ( date('z', $entry[1]) != $n ) {
					$pdf->Ln(5);
					$n = date('z', $entry[1]);
					$k = 1;
				}

				$clicks = (isset($clicks) && $clicks > 0 ? $clicks : 0);
				if ( $k == 1 ) {
					$pdf->SetAutoPageBreak(true, 20);
					$pdf->SetFontSize(11);
					$pdf->Cell(190, 11, date('d/m/Y', $entry[1]), 1, 1, "C", true);
					$pdf->SetFontSize(10);
					$clicks = 0;
					$pdf->SetAutoPageBreak(false);
				}

				if ( $entry[0] == 'click' ) {
					$clicks++;
					$pdf->Cell(10, 8, $clicks, 1, 0, "C");
					$pdf->Cell(30, 8, date('H:i:s', $entry[1]), 1, 0, "C");
					$pdf->Cell(55, 8, substr($entry[2], 0, 20), 1, 0, "C");
					$pdf->Cell(55, 8, ($entry[3] != '' ? substr($entry[3], 0, 20).(strlen($entry[3]) > 20 ? '...' : '') : '-' ), 1, 0, "C", '', $entry[3]);
					$pdf->Cell(40, 8, $entry[4], 1, 1, "C");
				}

				if ( $entry[0] == 'view' ) {
					$pdf->SetDrawColor(249,192,50);
					$pdf->SetFillColor(249,192,50);
					$pdf->Cell(40 + $viewableSize, 1, '', 0, 0, "C", true);
					$pdf->SetDrawColor(103,58,183);
					$pdf->SetFillColor(103,58,183);
					$pdf->Cell(50 + $viewableSize, 1, '', 0, 0, "C", true);
					$pdf->SetDrawColor(42,150,243);
					$pdf->SetFillColor(42,150,243);
					$pdf->Cell(40 + $viewableSize, 1, '', 0, ($viewableSize == 0 && $ma == false ? 0 : 1), "C", true);
					if ( $viewableSize == 0 && $ma == false ) {
						$pdf->SetDrawColor(76, 175, 80);
						$pdf->SetFillColor(76, 175, 80);
						$pdf->Cell(60, 1, '', 0, 1, "C", true);
					}

					$pdf->SetDrawColor(211,211,211);
					$pdf->SetFillColor(211,211,211);

					$pdf->Cell(190, 2, '', 1, 1, "C", true);
					$pdf->Cell(40 + $viewableSize, 8, fontFormat($entry[3]), 1, 0, "C", true);
					$pdf->Cell(50 + $viewableSize, 8, fontFormat($entry[4]), 1, 0, "C", true);
					$pdf->Cell(40 + $viewableSize, 8, fontFormat($entry[5]), 1, ($viewableSize == 0 && $ma == false ? 0 : 1), "C", true);
					if ( $viewableSize == 0 && $ma == false ) {
						$pdf->Cell(60, 8, fontFormat($arrays['total']['viewable']), 1, 1, "C", true);
					}
					$pdf->Cell(40 + $viewableSize, 4, $clicks, 1, 0, "C", true);
					$pdf->Cell(50 + $viewableSize, 4, $entry[2], 1, 0, "C", true);
					$pdf->Cell(40 + $viewableSize, 4, ($clicks > 0 ? number_format(($clicks / $entry[2]) * 100, '2').'%' : '0.00%'), 1, ($viewableSize == 0 && $ma == false ? 0 : 1), "C", true);
					if ( $viewableSize == 0 && $ma == false ) {
						$minutes = $entry[7] > 0 ? $entry[7] / 60 : 0;
						$seconds = ($minutes - intval($minutes)) * 60;
						$pdf->Cell(60, 4, fontFormat(intval($minutes) . ' ' . $entry[9] . ' ' . intval($seconds) . ' ' . $entry[8]), 1, 1, "C", true);
					}
					$pdf->Cell(190, 4, '', 1, 1, "C", true);
					$pdf->SetAutoPageBreak(false);

					// increase day
					$day++;
				}

				if ( $d == 0 ) {
					$d = date('z', $entry[1]);
				}
			}
		}
		$pdf->Ln(30);
		$pdf->SetFont('Arial','',12);
		$pdf->Ln();
		$pdf->Output('D', 'statistics-'.$ad_id.'.pdf'); // I - load in browser, D - download as a file
	}
}

