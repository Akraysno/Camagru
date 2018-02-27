<?php
//page qui permet de verifier qu'un utilisateur est correctement identifié

include('includes/connect_bdd.php');
try {	
	if (!isset($_SESSION['user_logged']) || !isset($_SESSION['user_mdp'])) {
		if ($_SERVER['PHP_SELF'] !== "/connection.php" && $_SERVER['PHP_SELF'] !== "/inscription.php")
			header('Location: connection.php');
	}
	if (isset($_SESSION['end_session'])) {
		$date_now = new DateTime();
		if ($date_now > $_SESSION['end_session'])
			header('Location: includes/deconnection.php');
	}
	if (isset($_SESSION['user_logged']) && isset($_SESSION['user_mdp'])) {
		$login = $_SESSION['user_logged'];
		$passwd = $_SESSION['user_mdp'];
	}
	else {
		$login = "";
		$passwd = "";
	}
	$recup_users = $bdd->prepare("SELECT `login`, `passwd`, `valid`  FROM `Users` WHERE `login` = :login");
	$recup_users->bindParam(':login', $login);
	$recup_users->execute();
	$tab_pass = $recup_users->fetchAll();
	if (!$tab_pass) {
		if ($_SERVER['PHP_SELF'] !== "/connection.php" && $_SERVER['PHP_SELF'] !== "/inscription.php")
			header('Location: connection.php');
	} else {
		foreach ($tab_pass as $line) {
			if ($line['passwd'] === $passwd) {
				if ($line['valid'] === '1') {
					if ($_SERVER['PHP_SELF'] === "/validation.php" || $_SERVER['PHP_SELF'] === "/inscription.php" || $_SERVER['PHP_SELF'] === "/connection.php")
						header("Location: index.php");
				} else {
					if ($_SERVER['PHP_SELF'] !== "/validation.php")
						header('Location: validation.php');
				}
			}
			else {
				if ($_SERVER['PHP_SELF'] !== "/connection.php" && $_SERVER['PHP_SELF'] !== "/inscription.php")
					header('Location: connection.php');
			}
		}
	}
} catch (PDOException $e) {
	print "Erreur !: " . $e->getMessage() . "<br/>";
	die();
}
?>