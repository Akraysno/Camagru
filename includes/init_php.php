<?php
//session_start();
$_SESSION['img'] = array();
$_SESSION['img']["TrollFace"] = array ("name" => "TrollFace", "src" => $dircalques.'/trollface.png', "width" => 116, "height" => 94);
$_SESSION['img']["Licorne"] = array ("name" => "Licorne", "src" => $dircalques.'/licorne.png', "width" => 129, "height" => 108);
$_SESSION['img']["Flame"] = array ("name" => "Flame", "src" => $dircalques.'/flame.png', "width" => 434, "height" => 368);

$i = 1;
foreach ($_SESSION['calques'] as $k => $v) {
	$i = $k + 1;
}

$json_tab = json_encode($_SESSION['calques']);
/*
if ($json_tab === "[]")
	echo "<br /><br />{}";
else
	echo "<br /><br />".$json_tab;
*/

$json_img = json_encode($_SESSION['img']);

if (!isset($_SESSION['checked']) || $_SESSION['checked'] == "") {
	$_SESSION['checked'] = "Licorne";
}
?>