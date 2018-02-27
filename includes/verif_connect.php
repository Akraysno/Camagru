<?php
session_start();
include('connect_bdd.php');
$pattern_login = '/^([0-9a-zA-Z]|-|_)*$/';
$pattern_mdp = '/^([0-9a-zA-Z]|-|_|&|%|@)*$/';

// TODO ne pas se connecter si compte non valider
if (isset($_POST['login']) && $_POST['login'] != "" && isset($_POST['passwd']) && $_POST['passwd'] != "")
{
	preg_match($pattern_login, $_POST['login'], $matches_login);
	preg_match($pattern_mdp, $_POST['passwd'], $matches_mdp);
	$error_regex = 0;


	if (!isset($matches_login) || !isset($matches_login[0]) || $matches_login[0] != $_POST['login'])
		$error_regex += 1;

	/*if (isset($matches_login) && isset($matches_login[0])) {
		if ($matches_login[0] != $_POST['login']) {
			$_SESSION['error_connect'] = "login";
			$error_regex += 1;
		}
	}
	else {
		$_SESSION['error_connect'] = "login";
		$error_regex += 1;
	}*/

	if (!isset($matches_mdp) && !isset($matches_mdp[0]) || $matches_mdp[0] != $_POST['passwd'])
		$error_regex += 2;


	if (strlen($_POST['passwd']) < 6)
		$error_regex += 4;
	
	if (strlen($_POST['login']) < 6)
		$error_regex += 8;
	/*
	if (isset($matches_mdp) && isset($matches_mdp[0])) {
		if ($matches_mdp[0] != $_POST['passwd']) {
			$_SESSION['error_connect'] = "passwd";
			$error_regex += 2;
		}
	}
	else {
		$_SESSION['error_connect'] = "passwd";
		$error_regex += 2;
	}
	*/
	
	if ($error_regex != 0) {
		if ($error_regex == 1 || $error_regex == 8 || $error_regex == 9) {
			//login
			$_SESSION['error_connect'] = "login";
		}
		if ($error_regex == 2 || $error_regex == 4 || $error_regex == 6) {
			//mdp
			$_SESSION['error_connect'] = "passwd";
		}
		if ($error_regex == 3 || $error_regex == 5 || $error_regex == 7 || $error_regex == 10 || $error_regex == 11 || $error_regex == 12 || $error_regex == 13 || $error_regex == 14 || $error_regex == 15) {
			//both
			$_SESSION['error_connect'] = "both";
		}
		header("Location: ../connection.php");
	}
	else {
		$insert_users = $bdd->prepare("SELECT `passwd` FROM `Users` WHERE `login` = :login");
		$insert_users->bindParam(':login', $login);

		$login = $_POST['login'];
		$passwd = $_POST['passwd'];

		$insert_users->execute();
		$tab_pass = $insert_users->fetchAll();
		if (!$tab_pass) {
			$_SESSION['error_connect'] = "error";
			header("Location: ../connection.php");
		} else {
			foreach ($tab_pass as $line) {
				if ($line['passwd'] === hash("whirlpool", $passwd))
				{
					$_SESSION['user_logged'] = $login;
					$_SESSION['user_mdp'] = hash("whirlpool", $passwd);
					$_SESSION['end_session'] = new DateTime('NOW +1 hour');
					header("Location: ./../index.php");
				}
				else
				{
					$_SESSION['error_connect'] = "error";
					header("Location: ../connection.php");
				}
			}
		}
	}
}
else {
	$_SESSION['error_connect'] = "empty";
	header("Location: ../connection.php");
}
?>