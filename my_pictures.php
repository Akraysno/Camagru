<?php
session_start();
include('includes/verif_log.php');
$dirshot = "./Screenshot";
$dircalques = "./Calques";
include('includes/connect_bdd.php');
$nb_files = 0;
$recup_photo = $bdd->prepare("SELECT `owner`, `ID_owner`, `Users`.`id` AS 'ID_Users', `Photos`.`id` AS 'id', `name`, `file_name`, `date` FROM `Photos` RIGHT JOIN `Users` ON `Users`.`id` = `Photos`.`ID_owner` WHERE `Users`.`login` = :login ORDER BY `date` DESC");
$recup_photo->bindParam(':login', $_SESSION['user_logged']);
$recup_photo->execute();
$result = $recup_photo->fetchAll();
foreach ($result as $raw) {
	if (isset($raw['file_name']) && $raw['file_name']) {
		$nb_files++;
	}
}
$user = $_SESSION['user_logged'];
$json_tab = json_encode($result);

// TODO rajouter des filtres de recherche sur owner / date (cette semaine, moins de 24h)
// TODO faire un rendu avec seulement quelques images par pages
// TODO securiser les champs s'ils sont null
// TODO rajouter alt/title sur les images meme quand les images existes pas
// TODO input text pour le titre + modif dans la base de donne lorsque valide (via un bouton a cote du champs)

?>

<!DOCTYPE html>
<html lang="">
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
		<title>Mes photos - Camagru</title>
		<link rel="icon" type="image/png" href="images/42b.png" />
		<meta name="Author" content="gauffret" charset="UTF-8">
		<link rel="stylesheet" type="text/css" href="style.css">
		<script type="text/javascript" src="script.js"></script>
		<script>
			var data;
			data = <?php echo $json_tab; ?>;
			
			function create_image(tab) {
				var elem = document.createElement('div'),
					title = document.createElement('input'),
					//title = document.createElement('p'),
					cross = document.createElement('img'),
					img = document.createElement('img'),
					change_name = document.createElement('button'),
					src;

				elem.id = "photo_img"+tab['id'];
				elem.className = "photo_img";
				cross.name = "cross"+tab['id'];
				cross.src = "images/bin.jpg";
				cross.style = "position: relative; width: 30px; height: 30px; top: 5px; left: 30px;";
				cross.onclick = function () {
					if (confirm("Are you sure you want to erase this photo ?")) {
						var xhr = getXMLHttpRequest();
						var args;
						/([0-9]+)/.exec(this.name);
						var nb = RegExp.$1;
						args = "login=<?php echo $user; ?>&id="+tab['id'];
						xhr.open("POST", "includes/suppr_photo.php", true);
						xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
						xhr.onreadystatechange = function() {
							if(xhr.readyState == 4 && xhr.status == 200) 
							{
								console.log(xhr.responseText);
								document.getElementById("photo_img"+nb).remove();
								var val = document.getElementById('nb_files').innerHTML;
								/([0-9]+)/.exec(val);
								nb = RegExp.$1;
								nb -= 1;
								document.getElementById('nb_files').innerHTML = nb;
							}
						}
						xhr.send(args);
					}
				};
				title.type = 'text';
				title.value = tab['name'].replace(".png", "");
				//title.innerHTML = tab['name'];
				title.style = "position: relative; top: -5px; left: -8px; width: 200px;";
				title.maxLength = 32;
				title.name = "name"+tab['id'];
				change_name.innerHTML = "Change";
				change_name.name = "change"+tab['id'];
				change_name.style = "position: relative; left: -5px; top: -5px";
				change_name.onclick = function() {
					alert("WIP");
					var xhr = getXMLHttpRequest();
					var args;
					/([0-9]+)/.exec(this.name);
					var nb = RegExp.$1;
					args = "login=<?php echo $user; ?>&id="+tab['id'];
					xhr.open("POST", "includes/modif_picture_name.php", true);
					xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
					xhr.onreadystatechange = function() {
						if(xhr.readyState == 4 && xhr.status == 200) 
						{
							console.log(xhr.responseText);
						}
					}
					xhr.send(args);
				}
				src = "<?php echo $dirshot; ?>/"+tab['file_name'];
				img.src = src;
				img.height = 240;
				img.width = 320;
				console.log(src);

				elem.appendChild(img);
				elem.appendChild(title);
				elem.appendChild(change_name);
				elem.appendChild(cross);
				document.getElementById("gallery_area").appendChild(elem);
			}

			function add_photos(data) {
				var len = data.length;

				for (var i = 0; i < len; i++) {
					if (data[i]['name'] && data[i]['name'] != "")
						create_image(data[i]);
				}
			}

		</script>
	</head>
	<body>
		<div id="page">
			<div id="main_bloc">
				<?php include('includes/header.php'); ?>
				<div id="gallery"><br />
					<?php echo '<span id="gallery_total">Nombre de résultats: <span id="nb_files">'.$nb_files.'</span></span><br />'; ?>
					<div id='gallery_area'>
					</div>
				</div>
			</div>
			<?php include('includes/footer.php'); ?>
		</div>
		<script>
			add_photos(data);
		</script>
	</body>
</html>