<?php
session_start();
include('connect_bdd.php');

$pattern_login = '/^([0-9a-zA-Z]|-|_)*$/';
$pattern_mdp = '/^([0-9a-zA-Z]|-|_|&|%|@)*$/';
$pattern_mail = '/^[\w\-\+]+(\.[\w\-]+)*@[\w\-]+(\.[\w\-]+)*\.[\w\-]{2,4}$/';

if (isset($_POST['login']) && $_POST['login'] != "" && isset($_POST['mail']) && $_POST['mail'] != "" && isset($_POST['passwd']) && $_POST['passwd'] != "" && isset($_POST['submit']) && $_POST['submit'] == "OK")
{
	$error = 0;
	$regex_error = 0;

	try {
		preg_match($pattern_login, $_POST['login'], $matches_login);
		preg_match($pattern_mdp, $_POST['passwd'], $matches_mdp);
		preg_match($pattern_mail, $_POST['mail'], $matches_mail);
 
		if (!isset($matches_login) || !isset($matches_login[0]) || $matches_login[0] != $_POST['login'])
			$regex_error += 1;
		if (!isset($matches_mdp) || !isset($matches_mdp[0]) || $matches_mdp[0] != $_POST['passwd'])
			$regex_error += 2;
		if (!isset($matches_mail) || !isset($matches_mail[0]) || $matches_mail[0] != $_POST['mail'])
			$regex_error += 4;
		if (strlen($_POST['login']) < 6)
			$regex_error += 8;
		if (strlen($_POST['passwd']) < 6)
			$regex_error += 16;
		
		if ($regex_error != 0) {
			if ($regex_error == 1 || $regex_error == 8 || $regex_error == 9) {
				$_SESSION['error_inscription'] = "loginfail";
			}
			if ($regex_error == 2 || $regex_error == 16 || $regex_error == 18) {
				$_SESSION['error_inscription'] = "mdpfail";
			}
			if ($regex_error == 4) {
				$_SESSION['error_inscription'] = "mailfail";
			}
			if ($regex_error == 3 || $regex_error == 10 || $regex_error == 19 || $regex_error == 24) {
				$_SESSION['error_inscription'] = "loginmdpfail";
			}
			if ($regex_error == 6 || $regex_error == 20) {
				$_SESSION['error_inscription'] = "mdpmailfail";
			}
			if ($regex_error == 5 || $regex_error == 12) {
				$_SESSION['error_inscription'] = "loginmailfail";
			}
			if ($regex_error == 7 || $regex_error == 21 || $regex_error == 14 || $regex_error == 28) {
				$_SESSION['error_inscription'] = "loginmdpmailfail";
			}
		}
		else {
			$verif_login = $bdd->prepare("SELECT * FROM `Users` WHERE `login` = :login");
			$verif_login->bindParam(':login', $login);
			$login = $_POST['login'];
			$verif_login->execute();
			$tab_pass = $verif_login->fetchAll();
			if ($tab_pass) {
				$error += 1;
				echo "login pris<br>";
			}

			$verif_mail = $bdd->prepare("SELECT * FROM `Users` WHERE `mail` = :mail");
			$verif_mail->bindParam(':mail', $mail);
			$mail = $_POST['mail'];
			$verif_mail->execute();
			$tab_pass = $verif_mail->fetchAll();
			if ($tab_pass) {
				$error += 2;
				echo "mail pris<br>";
			}
			if ($error != 0) {
				if ($error == 1) {
					$_SESSION['error_inscription'] = "loginexists";
				}
				if ($error == 2) {
					$_SESSION['error_inscription'] = "mailexists";
				}
				if ($error == 3) {
					$_SESSION['error_inscription'] = "bothexists";
				}
			}
		}
	
		if ($error != 0 || $regex_error != 0) {
			header('Location: ../inscription.php');
		}
		else {

			$char = 'abcdefghijklmnopqrstuvwxyz0123456789';
			$code = str_shuffle($char);
			$code = strtoupper(substr($code, 1, 10));
			echo $code;
			
			$insert_users = $bdd->prepare("INSERT INTO Users(login, passwd, mail, valid, admin) VALUES (:login, :pass, :mail, :valid, :admin)");
			$insert_users->bindParam(':login', $login);
			$insert_users->bindParam(':pass', $pass);
			$insert_users->bindParam(':mail', $mail);
			$insert_users->bindParam(':valid', $valid);
			$insert_users->bindParam(':admin', $admin);

			$login = $_POST['login'];
			$pass = hash("whirlpool", $_POST['passwd']);
			$mail = $_POST['mail'];
			$valid = '0';
			$admin = '0';
			$insert_users->execute();

			$id_user = $bdd->lastInsertId();
			echo "<br>$id_user<br>";
			echo "<br>$code<br>";
			$insert_valid = $bdd->prepare("INSERT INTO Validation(code, ID_Users) VALUES (:code, :id_user)");
			$insert_valid->bindParam(':code', $code);
			$insert_valid->bindParam(':id_user', $id_user);

			$verif = $insert_valid->execute();
			//$err = $insert_valid->errorCode();
			//echo "verif: $verif<br>error: $err<br>";
			
			$_SESSION['validation']['email'] = $_POST['mail'];
			$_SESSION['validation']['login'] = $_POST['login'];
			$_SESSION['validation']['code'] = $code;
			
			include('send_validation.php');

			header('Location: ../validation.php');
		}
	} catch (PDOException $e) {
		print "Erreur !: " . $e->getMessage() . "<br/>";
		die();
	}
}
else {
	$_SESSION['error_inscription'] = "empty";
	header('Location: ../inscription.php');
}
?>