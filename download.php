<?php
# Infosuck Generator
# Sheran A. Gunasekera <sheran@zensay.com>
#
# Code is free to use, modify, and profit as you see fit
# No warranties made.


function getDraw($font_size){
	$draw = new ImagickDraw();
	$draw->setFont("Courier");
	$draw->setStrokeAntialias(true);
	$draw->setFontWeight(400);
	$i_fontsize = 16;
	if(is_numeric($font_size)){
		if($font_size < 30 && $font_size > 0) {
			$i_fontsize = $font_size;
		}
	}
	$i_fontsize += 2;
	$draw->setFontSize($i_fontsize);
	return $draw;
}

function wrapper($msg, $max_width,$fontsize){
	$draw = new ImagickDraw();
	$draw->setFont("Courier");
	$draw->setFontSize($fontsize);
	
	$image = new Imagick();
	$image->newImage(0,0,"none");
	
	
	$words = explode(" ", $msg);
	$lines = array();
	$current_line = "";
	foreach ($words as $word) {
	    if ($current_line == "") {
	        $test = $word;
	    } else {
	        $test = "$current_line $word";
	    }
	    $metrics = $image->queryFontMetrics($draw, $test);
	    if ($metrics['textWidth'] > $max_width) {
	        if ($current_line == "") {
	            $lines[] = $test;
	            $current_line = "";
	        } else {
	            $lines[] = $current_line;
	            $current_line = $word;
	        }
	    } else {
	        $current_line = $test;
	    }
	}
	if ($current_line != "") {
	    $lines[] = $current_line;
	}
	$msg = implode("\n", $lines);
	return $msg;
}
import_request_variables("gp","req_");

$img_file_name = "infosuck-".time().".png";

$xpos = 13;
$ypos = 25;
$composite_width = 220;
$composite_height = 145;
$max_width = 200;
$max_stringlen = 300;

$templates = array();
for($a=0; $a < 11; $a++){
	$templates[$a] = str_replace("#",$a+1,"template#.png");
}

$index = array_search($req_template,$templates);

if($index && file_exists($templates[$index])){
	$image = new Imagick($templates[$index]);
} else {
	$image = new Imagick('template1.png');
}


$text1 = substr($req_text1,0,$max_stringlen);
$text2 = substr($req_text2,0,$max_stringlen);
$text3 = substr($req_text3,0,$max_stringlen);


$caption1 = new Imagick();
$caption1->newImage($composite_width,$composite_height,"none");
$caption1->annotateImage(getDraw($req_font1),$xpos,$ypos,0,wrapper($text1,$max_width,getDraw($req_font1)->getFontSize()));
$image->compositeImage($caption1,imagick::COMPOSITE_DEFAULT,0,0);

$caption2 = new Imagick();
$caption2->newImage($composite_width,$composite_height,"none");
$caption2->annotateImage(getDraw($req_font2),$xpos,$ypos,0,wrapper($text2,$max_width,getDraw($req_font1)->getFontSize()));
$image->compositeImage($caption2,imagick::COMPOSITE_DEFAULT,225,0);

$caption3 = new Imagick();
$caption3->newImage($composite_width,$composite_height,"none");
$caption3->annotateImage(getDraw($req_font3),$xpos,$ypos,0,wrapper($text3,$max_width,getDraw($req_font1)->getFontSize()));
$image->compositeImage($caption3,imagick::COMPOSITE_DEFAULT,450,0);


$image->setImageFormat("png");

header('Content-type: image/png');
header('Content-size: '.$image->getImageSize());
header('Content-Disposition: attachment;filename="'.$img_file_name.'"');
echo $image;
?>
