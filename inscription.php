<?PHP
session_start();
include('includes/verif_log.php');
include('./includes/connect_bdd.php');
/*

TODO verifier via regex les champs

remplir le formulaire
=> redirection vers inscription (ou une autre page)
=> verification des champs 
	champ bon => envoie email + verification (envoie d'un numero via mail a mettre dans le formulaire de verif pour valider le compte (mode ankama))
	Chanps mauvais => affichage formulaire avec message d'erreur

Formulaire valide + verification faite = redirection vers index.php

if ($i === 0){
	header("Location: connect.php");
}
*/

?>

<html>
	<head>
		<title>Inscription - Camagru</title>
		<link rel="stylesheet" href="style.css">
		<link rel="icon" href="images/42b.png" />
	</head>
	<body>
		<div id="page">
			<div id="main_bloc">
				<?php include('includes/header.php'); ?>
				<h2>Creer un compte</h2>
				<?php
				if (isset($_SESSION['error_inscription'])) {
					$_SESSION['error_inscription'] == "loginmdpfail";
					$_SESSION['error_inscription'] == "loginmailfail";
					$_SESSION['error_inscription'] == "mdpmailfail";
					$_SESSION['error_inscription'] == "loginmdpmailfail";
					
					echo '<div id="div_error" style="color: red;">';
					if ($_SESSION['error_inscription'] === "loginexists") {
						echo "Identifiant déjà utilisé.";
					}
					if ($_SESSION['error_inscription'] === "mailexists") {
						echo "Adresse mail déjà utilisée.";
					}
					if ($_SESSION['error_inscription'] === "bothexist") {
						echo "Identifiant et adresse mail déjà utilisés.";
					}
					if ($_SESSION['error_inscription'] === "loginfail" || $_SESSION['error_inscription'] === "loginmdpfail" || $_SESSION['error_inscription'] === "loginmailfail") {
						echo "Identifiant non valide. (6 caractères minimum, caractères autorisés: 0-9, a-z, A-Z, -, _)<br />";
					}
					if ($_SESSION['error_inscription'] === "mailfail" || $_SESSION['error_inscription'] === "loginmailfail" || $_SESSION['error_inscription'] === "mdpmailfail") {
						echo "Adresse mail non valide.";
					}
					if ($_SESSION['error_inscription'] === "mdpfail" || $_SESSION['error_inscription'] === "loginmdpfail" || $_SESSION['error_inscription'] === "mdpmailfail") {
						echo "Mot de passe non valide. (6 caractères minimum, caractères autorisés: 0-9, a-z, A-Z, -, _, @, &, %)<br />";
					}
					if ($_SESSION['error_inscription'] === "empty") {
						echo "Tous les champs doivent être remplis.";
					}
					$_SESSION['error_inscription'] = "";
					echo '</div>';
				} 
				?>
				<form method="POST" action="includes/verif_inscription.php">
					<table>
						<tr>
							<td>Identifiant :</td>
							<td><input type="text" name="login" placeholder="login" maxlength="9" value=""/></td>
						</tr>
						<tr>
							<td>E-mail :</td>
							<td><input type="email" name="mail" placeholder="adresse@mail.fr" value=""/></td>
						</tr>
						<tr>
							<td>Mot de passe :</td>
							<td><input type="password" name="passwd" maxlength="9" placeholder="passwd" value="" style="required pattern='[a-zA-Z0-9]+';"/></td>
						</tr>
					</table>
					<br />
					<input type="submit" name="submit" value="OK" />
				</form>
			</div>
			<?php include('includes/footer.php'); ?>
		</div>
	</body>
</html>