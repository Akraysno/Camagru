<?php
session_start();
include('connect_bdd.php');

$pattern_code = '/^([0-9A-Z])*$/';
$pattern_mail = '/^[\w\-\+]+(\.[\w\-]+)*@[\w\-]+(\.[\w\-]+)*\.[\w\-]{2,4}$/';

if (isset($_GET['email']) && $_GET['email'] != "" && isset($_GET['code']) && $_GET['code'] != "") {
	$wrong_pattern = 0;
	
	$code = strtoupper($_GET['code']);
	
	preg_match($pattern_code, $code, $matches_code);
	preg_match($pattern_mail, $_GET['email'], $matches_mail);

	if (!isset($matches_code) || !isset($matches_code[0]) || $matches_code[0] != $code)
		$wrong_pattern += 1;
	if (!isset($matches_mail) || !isset($matches_mail[0]) || $matches_mail[0] != $_GET['email'])
		$wrong_pattern += 2;
	if (strlen($code) != 10)
		$wrong_pattern += 4;
	
	if ($wrong_pattern != 0) {
		if ($wrong_pattern == 1 || $wrong_pattern == 4 || $wrong_pattern == 5)
			echo "wrongcode";
		if ($wrong_pattern == 2)
			echo "wrongmail";
		if ($wrong_pattern == 3 || $wrong_pattern == 6 || $wrong_pattern == 75)
			echo "wrongcodemail";
	} else {
		try {
			$verif_mail = $bdd->prepare("SELECT `id` FROM `Users` WHERE `Users`.`mail` = :mail");
			$verif_mail->bindParam(':mail', $_GET['email']);
			$verif_mail->execute();
			$tab_pass = $verif_mail->fetchAll();
			if ($tab_pass) {
				$id_user = $tab_pass[0]['id'];
				$verif_code = $bdd->prepare("SELECT `code` FROM `Validation` WHERE `ID_Users` = :id");
				$verif_code->bindParam(':id', $id_user);
				$verif_code->execute();
				$tab_pass = $verif_code->fetchAll();
				if (!$tab_pass) {
					echo "valid";
				} else {
					if ($code === $tab_pass[0]['code']) {
						$query = "UPDATE `Users` SET `valid` = '1' WHERE `id` = ".$id_user;
						$bdd->query($query);
						$delete_valid = $bdd->prepare("DELETE FROM `Validation` WHERE `ID_Users` = :id");
						$delete_valid->bindParam(':id', $id_user);
						$delete_valid->execute();
						echo "nowvalid";
					}
					else {
						echo "codefail";
					}
				}
			}
			else {
				echo "mailfail";
			}
		} catch (PDOException $e) {
			print "Erreur !: " . $e->getMessage() . "<br/>";
			die();
		}
	}
}
?>