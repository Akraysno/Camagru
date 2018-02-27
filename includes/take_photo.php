<?php
session_start();
$all_calques = $_SESSION['calques'];
$dircalques = "./../Calques";
$dirshot = "./../Screenshot";

// TODO verifier user logged avant de continuer
// TODO modifier le nom de l'image par rapport au champs de ;index si il est remplis
// TODO modifier le nom de l'image => Heure/Date a la place du numero de la photo (ex: admin11023020171219.png pour une photo prise le 19/12/2017 a 11:02:30)

function imagecopymerge_alpha($dst_im, $src_im, $dst_x, $dst_y, $src_x, $src_y, $src_w, $src_h, $pct){
	$cut = imagecreatetruecolor($src_w, $src_h); 
	imagecopy($cut, $dst_im, 0, 0, $dst_x, $dst_y, $src_w, $src_h);
	imagecopy($cut, $src_im, 0, 0, $src_x, $src_y, $src_w, $src_h);
	imagecopymerge($dst_im, $cut, $dst_x, $dst_y, 0, 0, $src_w, $src_h, $pct); 
}

if (isset($_POST['shotbutton']) && $_POST['shotbutton'] == "save" && isset($_POST['camshot'])) {
	if (isset($_POST['shotbutton']) && $_POST['shotbutton'] == "save")
	if (!file_exists($dirshot))
		mkdir($dirshot);
	list($type, $data) = explode(';', $_POST['camshot']);
	list(, $data) = explode(',', $data);
	$data = base64_decode($data);
	$user = $_SESSION['user_logged'];
	if ($data) {
		include('connect_bdd.php');
		$recup_date = $bdd->prepare("SELECT NOW() AS 'date'");
		$recup_date->execute();
		$date_req = $recup_date->fetch();

		$pattern = "/^([0-9]{4})-([0-9]|0[0-9]|1[0-2])-([0-9]|0[0-9]|1[0-9]|2[0-9]|3[0-1]) ([0-1][0-9]|2[0-3]):([0-5][0-9]):([0-5][0-9])$/";
		preg_match_all($pattern, $date_req['date'], $matches);
		$date = $matches[1][0]."-".$matches[2][0]."-".$matches[3][0]." ".$matches[4][0].":".$matches[5][0].":".$matches[6][0];
		
		$tmp = $dirshot . '/tmp.png';
		file_put_contents($tmp, $data);
		$im = imagecreatefrompng($tmp);
		foreach ($all_calques as $k => $v)
		{
			$alpha = imagecreatefrompng("./.".$v["src"]);
			imagecopymerge_alpha($im, $alpha, $v['pos_x'], $v['pos_y'], 0, 0, $v['width'], $v['height'], 100);
		}
		$img_i = 1;
		$num_img = sprintf("%03d", $img_i);
		//$name_image = $user.' - '.$num_img.'.png';
		$name_image = $user.'_'.$matches[1][0].''.$matches[2][0].''.$matches[3][0].''.$matches[4][0].''.$matches[5][0].''.$matches[6][0].'.png';
		$image_name = $dirshot.'/'.$name_image;
		while (file_exists($image_name))
		{
			$img_i++;
			$num_img = sprintf("%03d", $img_i);
			$name_image = $user.' - '.$num_img.'.png';
			$image_name = $dirshot.'/'.$name_image;
		}
		imagepng($im, $image_name);
		if (file_exists($tmp))
			unlink ($tmp);
		imagedestroy($im);
		$_SESSION['id_image']++;
		$_SESSION['last_img'] = $name_image;
		
		$recup_id = $bdd->prepare("SELECT `id` FROM `Users` WHERE `login` = :login");
		$recup_id->bindParam(':login', $user);
		$recup_id->execute();
		$result = $recup_id->fetch();
		print_r($result);
		
		$insert_photo = $bdd->prepare("INSERT INTO Photos(owner, ID_owner, name, date, file_name) VALUES (:owner, :id_owner,:name, :date, :file)");
		$insert_photo->bindParam(':owner', $user);
		$insert_photo->bindParam(':id_owner', $result['id']);
		$insert_photo->bindParam(':name', $name_image);
		$insert_photo->bindParam(':date', $date);
		$insert_photo->bindParam(':file', $name_image);
		$insert_photo->execute();
	}
}
header('location: ./../index.php');
?>