<?php
class AptPDF
{
	
	public function __construct()
	{
		if($_GET['action'] == "pdf")
		{
			$result = $this->OutputPDF();
			
			if(!$result)
			{
				//header("Location: " . site_url("/404.php"));
			}
			
			exit;
		}
	}
	
	function getUnit()
	{
		$unit = false;
		$adata = AptData::Instance();
		if(isset($_GET['unit']))
			$unit = $adata->getUnit($_GET['unit']);
		elseif(isset($_GET['id']))
			$unit = $adata->getUnitByPID($_GET['id']);
		elseif(($model = $adata->_('models.^model')) !== false)
			$unit = array('model'=>$model['id']);
		elseif(($model = $adata->_search('models', 'name', $_GET['model'])) !== false)
			$unit = array('model'=>$model[0]['id']);

		if($unit === false || $unit['disabled']) return false;
		return $unit;
	}
	
	public function OutputPDF()
	{
		//global $blog_id;
		$unit = $this->getUnit();
		if($unit === false) return false;
		$settings = get_option('razz_apt_opt');
		$adata = AptData::Instance();
		
		$model = $adata->getModel($unit['model']);

		$aptdata = array();
		$aptdata['apt'] = $unit['id'];
		$aptdata['modelid'] = $model['name'];
		$aptdata['floor'] = $unit['floor'];
		$aptdata['bed'] = $model['rooms']['bedroom'];
		if($aptdata['bed'] == 0) $aptdata['bed'] = 'Studio';
		$aptdata['bath'] = $model['rooms']['bathroom'];
		$aptdata['adate'] = $unit['date'];
		$aptdata['price'] = getFormattedRent($unit['id'] ? $unit : $model);
		$aptdata['leaseterm'] = $settings['uv_leasingterm'];
		$aptdata['bldg'] = $adata->_('ident');
		
		if($settings['data_sqft_source'] == 'unit' && $unit['sqft'])
			$source = $unit;
		else
			$source = $model;
		
		$sqftmin = $source['sqft']['min'];
		$sqftmax = $source['sqft']['max'];
		
		if($sqftmin != $sqftmax)
			$aptdata['sqft'] = "{$sqftmin} - {$sqftmax}";
		else
			$aptdata['sqft'] = $sqftmin;
		
		$logo = $settings['pdf_logo']['src'];
		$footer = $settings['pdf_footer'];
		$footer_1 = $settings['pdf_footer1'];
		$barbg = $this->hex2rgb($settings['pdf_bar_bg']);
		$barfg = $this->hex2rgb($settings['pdf_bar_fg']);
		$grayscale = $settings['pdf_grayscale'];
		$img = $model['image'];
		$pdfurl = $model['pdf'];
		
		$missing = false;
		foreach($aptdata as $key=>$value)
		{
			if(empty($value) && $value !== "0")
			{
				$aptdata[$key] = "N/A *";
				$missing = true;
			}
		}
		
		require_once(plugin_dir_path(__FILE__) . 'pdflib/fpdf.php');
		
		if(!isset($pdfurl) || empty($pdfurl))
		{
			$pdf = new FPDF();
			$pdf->addPage();
		}
		else
		{
			require_once(plugin_dir_path(__FILE__) . 'pdflib/fpdi.php');
			$pdf = new FPDI('P', 'mm', 'Letter');
			$pdf->AddPage();
			$upload_dir = wp_upload_dir();
			$path = $upload_dir['basedir'] . "/" . $pdfurl;
			if(!file_exists($path)) return false;
			$pdf->setSourceFile($path);
			$tpl = $pdf->importPage(1);
			$output = $pdf->useTemplate($tpl, 0, 0, 0, 0, true);
		}
		
		$info = array(
			'x' => 571,
			'w' => 210,
			'modelbar' => array(
				'y' => 142,
				'w' => 50
			)
		);
		$pdf->AddFont('halisrextralight', '');
		$pdf->SetFont('halisrextralight', '', '9');
		$pdf->SetTextColor(0, 0, 0);
		
		if(empty($pdfurl))
		{
			if(!empty($img))
			{
				$fimg = fopen($img, 'rb');
				$imgick = new Imagick();
				$imgick->readImageFile($fimg);
				$format = $imgick->getImageFormat();
				if($format == 'PNG' && $imgick->getImageDepth() == 16)
					$imgick->setImageDepth(8);
				
				if($grayscale)
					$imgick->modulateImage(100, 0, 100); //Desaturates image (brightness%, saturation%, hue%)
				
				$pdf->Image("data:image/{$format};base64,".base64_encode($imgick->getImageBlob()), px2mm(10), px2mm($info['modelbar']['y']), px2mm(550), 0, $format); //path, x, y, w, h
				//fclose?
			}
			else
			{
				//TODO: Floorplan Not Available
			}

			$footerimg = plugin_dir_url(__FILE__) . 'pdf/footer-logos.png';
			$footer_info = getimagesize($footerimg);
			$footer_w = $footer_info[0];
			$footer_h = $footer_info[1];

			$logoinfo = getimagesize($logo);
			$logow = $logoinfo[0];
			$logoh = $logoinfo[1];
			if($logoinfo[0] > $info['w'] - 20)
			{
				$logow = $info['w'] - 20;
				$logoh = $logow / ($logoinfo[0] / $logoinfo[1]);
			}
			else
			{
				$logow = $logoinfo[0];
				$logoh = $logoinfo[1];
			}
			$pdf->Image($logo, px2mm($info['x'] + ($info['w']/2 - $logow/2)), px2mm($info['modelbar']['y'] - 11 - $logoh), px2mm($logow), px2mm($logoh)); //path, x, y, w, h
			//$pdf->Image($logo, 150+$logow, 0); //path, x, y, w, h
			
			$pdf->SetFillColor($barbg[0], $barbg[1], $barbg[2]);
			$pdf->Rect(px2mm($info['x']), px2mm($info['modelbar']['y']), px2mm($info['w']), px2mm($info['modelbar']['w']), "F");
			$pdf->SetTextColor($barfg[0], $barfg[1], $barfg[2]);
			$pdf->SetFont('halisrextralight', '', '12');
			$pdf->Text(px2mm(598), px2mm($info['modelbar']['y'] + 30), $aptdata['modelid']);
			

			$pdf->SetFont('halisrextralight', '', '9');
			$pdf->SetTextColor(0, 0, 0);
			
			$pdf->Text(px2mm(595), px2mm(236), "BEDROOMS:");
			$pdf->Text(px2mm(595), px2mm(277), "BATHROOMS:");
			$pdf->Text(px2mm(595), px2mm(319), "SQ. FEET:");
			$pdf->Text(px2mm(595), px2mm(366), "UNIT NO.:");
			$pdf->Text(px2mm(595), px2mm(408), "PRICE:");
			$pdf->Text(px2mm(595), px2mm(450), "LEASE TERM:");
			$pdf->Text(px2mm(595), px2mm(491), "MOVE-IN:");

			$pdf->Image($footerimg, $pdf->w - px2mm($footer_w/2), $pdf->h - px2mm($footer_h/2 + 5), px2mm($footer_w/2), px2mm($footer_h/2)); //path, x, y, w, h
		}
		
		
		$pdf->Text(px2mm(694), px2mm(236), $aptdata['bed']);
		$pdf->Text(px2mm(694), px2mm(277), $aptdata['bath']);
		$pdf->Text(px2mm(694), px2mm(319), $aptdata['sqft']);
		$pdf->Text(px2mm(694), px2mm(366), $aptdata['apt']);
		$pdf->Text(px2mm(694), px2mm(408), $aptdata['price']);
		$pdf->Text(px2mm(694), px2mm(450), $aptdata['leaseterm']);
		$pdf->Text(px2mm(694), px2mm(491), $aptdata['adate']);
		
		$timestamp = "";
		
		if(empty($settings['data_price_override']))
			$timestamp .= "Price subject to change. ";

		$timestamp .= $footer_1;

		if($missing)
			//$timestamp .= " *Contact the leasing office for more information.";

        $pdf->SetFont('halisrextralight', '', '7');
		$this->CenteredText($pdf, $timestamp, $pdf->h - px2mm(46));
		$this->CenteredText($pdf, $footer, $pdf->h - px2mm(30));

		$pdf->Output("{$aptdata['bldg']['MarketingName']} - {$aptdata['modelid']} - {$aptdata['apt']}.pdf", 'I');
		return true;

	}
	
	function CenteredText($pdf, $text, $y)
	{
		$pdf->Text(($pdf->w/2) - ($pdf->GetStringWidth($text) / 2), $y, $text);
	}
	
	function hex2rgb($hex)
	{
		$hex = str_replace("#", "", $hex);

		if(strlen($hex) == 3)
		{
			$r = hexdec(substr($hex,0,1).substr($hex,0,1));
			$g = hexdec(substr($hex,1,1).substr($hex,1,1));
			$b = hexdec(substr($hex,2,1).substr($hex,2,1));
		}
		else
		{
			$r = hexdec(substr($hex,0,2));
			$g = hexdec(substr($hex,2,2));
			$b = hexdec(substr($hex,4,2));
		}
		$rgb = array($r, $g, $b);
		return $rgb;
	}
	
}

	
function px2mm($px)
{
	return $px*0.2625;
}

function mm2px($mm)
{
	return $mm/0.2625;
}
?>