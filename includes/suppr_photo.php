<?php

include('connect_bdd.php');

if (isset($_POST['login']) && $_POST['login'] != "" && isset($_POST['id']) && $_POST['id'] != "") {
	$verif_image = $bdd->prepare("SELECT `Users`.`login`, `Photos`.`name` FROM `Users`, `Photos` WHERE `Photos`.`ID_owner` = `Users`.`id` AND `Photos`.`id` = :id AND `Users`.`login` = :login");//verifier que l'image correspond bien au login
	$verif_image->bindParam(':id', $_POST['id']);
	$verif_image->bindParam(':login', $_POST['login']);
	$verif_image->execute();
	$result = $verif_image->fetch();
	if ($result) {
		$delete_photo = $bdd->prepare("DELETE FROM `Photos` WHERE `id` = :id");
		$delete_photo->bindParam(':id', $_POST['id']);
		$delete_photo->execute();
		unlink("./../Screenshot/".$result['name']);
		echo "Photo supprimee";
	}
	else
		echo "Photo non trouvee";
}
else
	echo "Mauvais user";

?>
