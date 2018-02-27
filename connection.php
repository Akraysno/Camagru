 <?php
session_start();
include('includes/verif_log.php');
?>
<!DOCTYPE html>
<html lang="">
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
		<title>Camagru</title>
		<link rel="icon" type="image/png" href="images/42b.png" />
		<meta name="Author" content="gauffret" charset="UTF-8">
		<link rel="stylesheet" type="text/css" href="style.css">
		<script>
			function forgot_passwd() { //TODO A MODIFIER
				var mail = document.getElementById("email_field").value;
				if (!mail)
				{
					document.getElementById('info').innerHTML = 'Veuillez remplir le champs "E-mail" avec votre adresse mail';
					document.getElementById('info').style.color = 'red';
				}
				else {
					var xhr = getXMLHttpRequest();
					xhr.open("POST", "includes/forgot_passwd.php", true);
					xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
					xhr.onreadystatechange = function() {
						if(xhr.readyState == 4 && xhr.status == 200) { 
							console.log("Mail send:");

							console.log(xhr.responseText);

							if (xhr.responseText === "mail fail") {
								document.getElementById('info').innerHTML = 'L\'adresse '+mail+' ne correspond à aucun compte.';
								document.getElementById('info').style.color = 'red';
							}
							else {
								document.getElementById('info').innerHTML = 'Un code de validation à été envoyé à l\'adresse '+mail+'.';
								document.getElementById('info').style.color = 'green';
							}
						}
					}
					xhr.send("email="+mail);
				}
			}
		</script>
	</head>
	<body>
		<div id="page">
			<div id="main_bloc">
				<?php include('includes/header.php'); ?>
				<h2>Connexion</h2>
				<?php 
				if (isset($_SESSION['error_connect'])) {
					echo '<div id="div_error" style="color: red;">';
					if ($_SESSION['error_connect'] === "error") {
						echo "Identifiant ou mot de passe incorrect.<br />";
					}
					if ($_SESSION['error_connect'] === "login" || $_SESSION['error_connect'] === "both") {
						echo "Identifiant non valide. (6 caractères minimum, caractères autorisés: 0-9, a-z, A-Z, -, _)<br />";
					}
					if ($_SESSION['error_connect'] === "passwd" || $_SESSION['error_connect'] === "both") {
						echo "Mot de passe non valide. (6 caractères minimum, caractères autorisés: 0-9, a-z, A-Z, -, _, @, &, %)<br />";
					}
					if ($_SESSION['error_connect'] === "empty") {
						echo "Tous les champs doivent être remplis.<br />";
					}
					$_SESSION['error_connect'] = "";
					echo '</div>';
				} 
				?>
				<form method="POST" action="includes/verif_connect.php">
					<table>
						<tr>
							<td>Identifiant:</td>
							<td><input type="text" name="login" placeholder="login" value=""/></td>
						</tr>
						<tr>
							<td>Mot de passe:</td>
							<td><input type="password" name="passwd" placeholder="passwd" value=""/></td>
						</tr>
					</table>
					<a id="send_mail" onclick="forgot_passwd();" >Mot de passe perdu ?</a> <!-- envoyer un mail apres confirmation de l'adresse -->
					<br />
					<br />
					<input type="submit" name="submit" value="OK" />
				</form>
			</div>
			<?php include('includes/footer.php'); ?>
		</div>
	</body>
</html>