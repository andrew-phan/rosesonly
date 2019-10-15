<?php
	$out = '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html><head>
<meta http-equiv="Content-Language" content="en-us">
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<META content="Root" name=Author>
<style>@page Section1{
size:21.0cm 842.0pt;
margin:2.0cm 2.0cm 2.0cm 2.0cm;
mso-header-margin:36.0pt;
mso-footer-margin:36.0pt;
mso-paper-source:0;
}
div.Section1{page:Section1;}
.noidung{
	-webkit-transform: rotate(-90deg); 
	-moz-transform: rotate(-90deg);	
}

</style>
</head>
	<body>
		<div class=Section1>
			<div class="noidung">
				<div>My Dearest Parul</div>
				<div>It doesn\'t seem fair that we only get to celebrate one day for all the other 364 days you are stuck with me. Thank you for putting up with all my faults and celebrating all my good qualities. I love you!
				</div>
				<div>From: Sid</div>
				<div>S/O:00000031</div>
			</div>
		</div>
	</body>
</html>';
	$size=strlen($out);
	$filename = "Message Card.doc";
	ob_end_clean();
	header("Cache-Control: private");
	header("Content-Type: application/force-download;");
	header("Content-Disposition:attachment; filename=\"$filename\"");
	header("Content-length:$size");
	print $out;
			
	ob_flush();
?>