<?php

session_start();


if ($_POST['action'] && $_POST['action'] === "add" && 
	is_numeric($_POST['i']) && $_POST['i'] >= 0 && 
	$_POST['name'] && $_POST['name'] != "" && 
	$_POST['pos_x'] && $_POST['pos_x'] != "" && 
	$_POST['pos_y'] && $_POST['pos_y'] != "" && 
	$_POST['width'] && $_POST['width'] != "" && 
	$_POST['height'] && $_POST['height'] != "" && 
	$_POST['src'] && $_POST['src'] != "") {
	$i = $_POST['i'];
	$_SESSION['calques'][$i]['name'] = $_POST['name'];
	$_SESSION['calques'][$i]['pos_x'] = $_POST['pos_x'];
	$_SESSION['calques'][$i]['pos_y'] = $_POST['pos_y'];
	$_SESSION['calques'][$i]['src'] = $_POST['src'];
	$_SESSION['calques'][$i]['width'] = $_POST['width'];
	$_SESSION['calques'][$i]['height'] = $_POST['height'];
	$_SESSION['nb_calques']++;
}

if ($_POST['action'] && $_POST['action'] === "del" &&
	is_numeric($_POST['i']) && $_POST['i'] >= 0) {
	unset($_SESSION['calques'][$_POST['i']]);
	$_SESSION['nb_calques']--;
}

if ($_POST['action'] && $_POST['action'] === "mod" &&
	is_numeric($_POST['i']) && $_POST['i'] >= 0 && 
	$_POST['field']) {
	if (!$_POST['value'] || $_POST['value'] == "")
		$_POST['value'] = 0;
	$value = intval($_POST['value']);
	if (is_int($value)) {
		if ($_POST['field'] === "pos_x" || 
			$_POST['field'] === "pos_y" || 
			$_POST['field'] === "width" || 
			$_POST['field'] === "height") {
			$_SESSION['calques'][$_POST['i']][$_POST['field']] = $value;
		}
	}
}


/*

echo "name : ".$_SESSION['calques'][$i]['name']."
<br>posx : ".$_SESSION['calques'][$i]['pos_x']."
<br>pos y :".$_SESSION['calques'][$i]['pos_y']."
<br>src : ".$_SESSION['calques'][$i]['src']."
<br>w : ".$_SESSION['calques'][$i]['width']."
<br>h : ".$_SESSION['calques'][$i]['height']."
<br>name_img : ".$_SESSION['calques'][$i]['name_img']."
<br>nb : ".$_SESSION['nb_calques']."<br>";
	
*/

//print_r($_SESSION['calques']);
?>