<?php


$pattern_mail = '/^[\w\-\+]+(\.[\w\-]+)*@[\w\-]+(\.[\w\-]+)*\.[\w\-]{2,4}$/';
preg_match($pattern_mail, $_GET['email'], $matches_mail);

if (isset($_GET['email']) && $_GET['email'] != "" && $matches_mail)
{
	include('connect_bdd.php');

	$char = 'abcdefghijklmnopqrstuvwxyz0123456789';
	$code = str_shuffle($char);
	$code = strtoupper(substr($code, 1, 10));
	try {
		$recup_mail = $bdd->prepare("SELECT `id`, `login` FROM `Users` WHERE `mail` = :mail");
		$recup_mail->bindParam(':mail', $_GET['email']);
		$recup_mail->execute();
		$tab_pass = $recup_mail->fetchAll();
		if (!$tab_pass) {
			echo "mail fail";
		}
		else {
			$login = $tab_pass[0]['login'];
			$id = $tab_pass[0]['id'];
			$verif_valid = $bdd->prepare("SELECT `code` FROM `Validation` WHERE `ID_Users` = :id");
			$verif_valid->bindParam(':id', $id);
			$verif_valid->execute();
			$tab_pass = $verif_valid->fetchAll();
			if (!$tab_pass) {
				echo "deja valide";
			}
			else {
				// login trouve et non valide
				$update_code = $bdd->prepare("UPDATE `Validation` SET `code` = :code WHERE `ID_Users` = :id");
				$update_code->bindParam(':code', $code);
				$update_code->bindParam(':id', $id);
				$update_code->execute();
				// update le code dans la base
				
				$_SESSION['validation']['email'] = $_GET['email'];
				$_SESSION['validation']['code'] = $code;
				$_SESSION['validation']['login'] = $login;
			}
		}
	} catch (PDOException $e) {
		print "Erreur !: " . $e->getMessage() . "<br/>";
		die();
	}
}

if (isset($_SESSION['validation']['email']) && $_SESSION['validation']['email'] != "" || isset($_SESSION['validation']['login']) && $_SESSION['validation']['login'] != "" || isset($_SESSION['validation']['code']) && $_SESSION['validation']['code'] != "") {
	echo "Mail: ";
	$mail = $_SESSION['validation']['email']; // Déclaration de l'adresse de destination.
	if (!preg_match("#^[a-z0-9._-]+@(hotmail|live|msn).[a-z]{2,4}$#", $mail)) {
		// On filtre les serveurs qui rencontrent des bogues.
		$passage_ligne = "\r\n";
	} else {
		$passage_ligne = "\n";
	}

	//$adresse = "http://".$_SERVER['SERVER_NAME'].':'.$_SERVER['SERVER_PORT'].$_SERVER["REQUEST_URI"];
	$adresse = "http://".$_SERVER['SERVER_NAME'].':'.$_SERVER['SERVER_PORT']."/includes/verif_validation.php";

	$link = $adresse."?email=".$mail."&code=".$code;

	//=====Déclaration des messages au format texte et au format HTML.
	$message_txt = $_SESSION['validation']['login'].", bienvenue sur Camagru ! Votre code de validation est : $code, vous pouvez utiliser le liens suivant pour activer votre compte: $link";
	$message_html = "<html><head></head><body><b>".$_SESSION['validation']['login'].", bienvenue sur Camagru !</b> Votre code de validation est : $code, vous pouvez utiliser cliquer <a href='$link'>ici</a> pour activer votre compte.</body></html>";
	//=====Création de la boundary
	$boundary = "-----=".md5(rand());
	//=====Définition du sujet.
	$sujet = "Camagru - Validation";
	//=====Création du header de l'e-mail.
	$header = "From: \"Camagru\"<camagru@mail.fr>".$passage_ligne;
	$header.= "Reply-to: \"Camagru\" <camagru@mail.fr>".$passage_ligne;
	$header.= "MIME-Version: 1.0".$passage_ligne;
	$header.= "Content-Type: multipart/alternative;".$passage_ligne." boundary=\"$boundary\"".$passage_ligne;
	//=====Création du message.
	$message = $passage_ligne."--".$boundary.$passage_ligne;
	//=====Ajout du message au format texte.
	$message.= "Content-Type: text/plain; charset=\"ISO-8859-1\"".$passage_ligne;
	$message.= "Content-Transfer-Encoding: 8bit".$passage_ligne;
	$message.= $passage_ligne.$message_txt.$passage_ligne;
	//==========
	$message.= $passage_ligne."--".$boundary.$passage_ligne;
	//=====Ajout du message au format HTML
	$message.= "Content-Type: text/html; charset=\"ISO-8859-1\"".$passage_ligne;
	$message.= "Content-Transfer-Encoding: 8bit".$passage_ligne;
	$message.= $passage_ligne.$message_html.$passage_ligne;
	//==========
	$message.= $passage_ligne."--".$boundary."--".$passage_ligne;
	$message.= $passage_ligne."--".$boundary."--".$passage_ligne;
	//=====Envoi de l'e-mail.
	mail($mail,$sujet,$message,$header);

	unset($_SESSION['validation']);
}
?>