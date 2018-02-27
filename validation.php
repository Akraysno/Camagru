<?php
session_start();
?>
<html>
	<head>
		<title>Validation - Camagru</title>
		<link rel="stylesheet" href="style.css">
		<link rel="icon" href="images/42b.png" />
		<script type="text/javascript" src="script.js"></script>
		<script>
			function send_mail() {
				var mail = document.getElementById("email_field").value;
				if (!mail)
				{
					document.getElementById('info').innerHTML = 'Veuillez remplir le champs "E-mail" avec votre adresse mail';
					document.getElementById('info').style.color = 'red';
				}
				else {
					var xhr = getXMLHttpRequest();
					xhr.open("POST", "includes/send_validation.php", true);
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

			function valid_account() {
				var mail = document.getElementById("email_field").value,
					code = document.getElementById("code_field").value;
				if (!mail || !code)
				{
					if (!mail && code)
						document.getElementById('info').innerHTML = 'Veuillez remplir le champs "E-mail".';
					if (mail && !code)
						document.getElementById('info').innerHTML = 'Veuillez remplir le champs "Code".';
					if (!mail && !code)
						document.getElementById('info').innerHTML = 'Veuillez remplir les champs "E-mail" et "Code".';
					document.getElementById('info').style.color = 'red';
				}
				else {
					var xhr = getXMLHttpRequest(),
						url = "includes/verif_validation.php?email="+mail+"&code="+code;
					xhr.open("GET", url, true);
					xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
					xhr.onreadystatechange = function() {
						if(xhr.readyState == 4 && xhr.status == 200) {
							console.log(xhr.responseText);
							if (xhr.responseText === "nowvalid") {
								document.getElementById('info').innerHTML = 'Le compte à été validé.<br />Vous pouvez dès à présent vous connecter.';
								document.getElementById('info').style.color = 'green';
							} else {
								if (xhr.responseText === "mailfail")
									document.getElementById('info').innerHTML = 'L\'adresse '+mail+' ne correspond à aucun compte.';
								if (xhr.responseText === "valid")
									document.getElementById('info').innerHTML = 'Le compte à déjà été validé.';
								if (xhr.responseText === "codefail")
									document.getElementById('info').innerHTML = 'Le code indiqué est incorrect.';
								if (xhr.responseText === "wrongcode")
									document.getElementById('info').innerHTML = "Code non valide. (10 caractères, caractères autorisés: 0-9, A-Z)";
								if (xhr.responseText === "wrongmail")
									document.getElementById('info').innerHTML = "Adresse mail non valide.";
								if (xhr.responseText === "wrongcodemail")
									document.getElementById('info').innerHTML = "Code non valide. (10 caractères, caractères autorisés: 0-9, A-Z)<br />Adresse mail non valide.";
								document.getElementById('info').style.color = 'red';
							}
						}
					}
					xhr.send();
				}
			}
		</script>
	</head>
	<body>
		<div id="page">
			<div id="main_bloc">
				<?php include('includes/header.php'); ?>

				<h2>Validation de votre compte</h2>
				<div id="info"></div>
				<table>
					<tr>
						<td>E-mail :</td>
						<td><input type="email" id="email_field" name="email" placeholder="adresse@mail.fr" value=""/></td>
					</tr>
					<tr>
						<td>Code :</td>
						<td><input type="text" id="code_field" name="code" placeholder="CODE" maxlength="10" value=""/></td>
					</tr>
				</table>
				<a id="send_mail" onclick="send_mail();">Renvoyer un code de validation.</a>
				<br />
				<br />
				<button id="valid_account" onclick="valid_account();">Valider</button>
			</div>
			<?php include('includes/footer.php'); ?>
		</div>
	</body>
</html>