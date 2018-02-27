<?php
session_start();
include('includes/verif_log.php');
//print_r($_SESSION);
if (!isset($_SESSION['calques']))
	$_SESSION['calques'] = array();
if (!isset($_SESSION['nb_calques']))
	$_SESSION['nb_calques'] = 0;
$dirshot = "./Screenshot";
$dircalques = "./Calques";
$all_calques = $_SESSION['calques'];
include('includes/init_php.php');
include('includes/connect_bdd.php');
// TODO : supprimer liste calque lors de la prise photo (maybe)
// TODO : supprimer liste calque lors d'un changement de page
// TODO : Empecher l'acces aux pages de verif dans includes
// TODO : Afficher la derniere photo prise par l'utilisateur courant sous le flux cam
?>

<!DOCTYPE html>
<html lang="">
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
		<title>Camagru</title>
		<link rel="icon" type="image/png" href="images/42b.png" />
		<meta name="Author" content="gauffret" charset="UTF-8">
		<link rel="stylesheet" type="text/css" href="style.css">
		<script type="text/javascript" src="script.js"></script>
		<script>
			i = ((<?php echo $i; ?> < 1) ? (1) : (<?php echo $i; ?>));

			function add_calque(event) {
				var point_x = event.clientX - document.getElementById("preview").offsetLeft + getScrollXY()[0],
					point_y = event.clientY - document.getElementById("preview").offsetTop + getScrollXY()[1],
					name_checked = "",
					json_img = <?php echo $json_img; ?>;
				name_checked = ((document.getElementById('Licorne').checked) ? ("Licorne") : (name_checked));
				name_checked = ((document.getElementById('Flame').checked) ? ("Flame") : (name_checked));
				name_checked = ((document.getElementById('TrollFace').checked) ? ("TrollFace") : (name_checked));
				if (name_checked === "")
					return ;
				console.log(json_img);
				var	data = { 
					name: json_img[name_checked]["name"],
					src: json_img[name_checked]["src"], 
					pos_x: parseInt(point_x - (parseInt(json_img[name_checked]["width"]) / 2)), 
					pos_y: parseInt(point_y - (parseInt(json_img[name_checked]["height"]) / 2)), 
					width: parseInt(json_img[name_checked]["width"]), 
					height: parseInt(json_img[name_checked]["height"]) 
				};				
				add_calque_and_elem(i, data, 1);
				i += 1;
			}
		</script>
	</head>
	<body>
		<div id="page">
			<div id="main_bloc">
				<?php include('includes/header.php'); ?>
				<div id="verif_test"></div>
				<div id="preview" onclick="add_calque(event);">
					<video autoplay id="video"></video>
					<canvas id="canvas"></canvas>
					<div id="allCalques"> </div>
				</div>
				<div id="final">
					<!--	TODO Rajouter une partie pour verifier si une image est presente dans le dossier si la derniere image n'existe plus
afficher la derniere (ordre alphabet)
Si $_SESSION['last_img'] existe pas ne pas afficher d'image -->
					<img src="<?php if (isset($_SESSION['last_img']) && $_SESSION['last_img'] != "" && file_exists($dirshot."/".$_SESSION['last_img'])) { echo $dirshot."/".$_SESSION['last_img']; } else { echo "images/default.png"; } ?>" id="photo" alt="photo">

				</div>
				<div id="elements"></div>
				<div id="button">
					<button id="clearbutton">Clear</button>
				</div>
				<div id="radio_calque">
					<input type="radio" id="Licorne" name="radio" value="Licorne" checked <?php if ($_SESSION['checked'] === "Licorne") echo "checked"; ?> /> <img src="Calques/licorne.png" style="width: 20px;" /> Licorne<br />
					<input type="radio" id="Flame" name="radio" value="Flame" <?php if ($_SESSION['checked'] === "Flame") echo "checked"; ?>/> <img src="Calques/flame.png" style="width: 20px;" /> Flame<br />
					<input type="radio" id="TrollFace" name="radio" value="TrollFace" <?php if ($_SESSION['checked'] === "TrollFace") echo "checked"; ?>/> <img src="Calques/Trollface.png" style="width: 20px;" /> TrollFace<br />
					<form action="includes/take_photo.php" method="POST" id="form_calque">
						<input type="hidden" id="camshot" value="" name="camshot"/>
						<input type="submit" id="shotbutton" name="shotbutton" form="form_calque" value="save" />
					</form>
				</div>
			</div>
			<?php include('includes/footer.php'); ?>
		</div>
		<script type="text/javascript">
			var tab = JSON.parse('<?php echo $json_tab; ?>');
			var len = Object.keys(tab).length;
			var nb = 0;
			//console.log(tab);
			for (var j = 0; nb != len; j++) {
				if (tab[j]) {
					console.log(tab[j]);
					add_calque_and_elem(j, tab[j], 2);
					nb++;
				}
			}
			
			var streaming		= false,
				video			= document.querySelector('#video'),
				cover			= document.querySelector('#cover'),
				canvas			= document.querySelector('#canvas'),
				photo			= document.querySelector('#photo'),
				calque			= document.querySelector('#calque'),
				startbutton		= document.querySelector('#startbutton'),
				clearbutton		= document.querySelector('#clearbutton'),
				shotbutton		= document.querySelector('#shotbutton'),
				image_calque	= document.querySelector('#image_calque'),
				select_calque	= document.querySelector('#select_calque'),
				width 			= 320,
				height 			= 0;
			navigator.getMedia = ( navigator.getUserMedia ||
								  navigator.webkitGetUserMedia ||
								  navigator.mozGetUserMedia ||
								  navigator.msGetUserMedia);
			navigator.getMedia(
				{
					video: true,
					audio: false
				},
				function(stream) {
					if (navigator.mozGetUserMedia) {
						video.mozSrcObject = stream;
					} else {
						var vendorURL = window.URL || window.webkitURL;
						video.src = vendorURL.createObjectURL(stream);
					}
					video.play();
				},
				function(err) {
					console.log("An error occured! " + err);
				}
			);
			video.addEventListener('canplay', function(ev){
				if (!streaming) {
					height = video.videoHeight / (video.videoWidth/width);
					video.setAttribute('width', width);
					video.setAttribute('height', height);
					canvas.setAttribute('width', width);
					canvas.setAttribute('height', height);
					streaming = true;
				}
			}, false);
			function takepicture() {
				canvas.width = width;
				canvas.height = height;
				canvas.getContext('2d').drawImage(video, 0, 0, width, height);
				var data = canvas.toDataURL('image/png');
				photo.setAttribute('src', data);
				document.getElementById('camshot').value = data;
			}
			clearbutton.addEventListener('click', function(ev){
				if (confirm("Are you sure you want to erase the layers?")) { 
					document.getElementById('allCalques').innerHTML = "";
					document.getElementById('elements').innerHTML = "";
					var xhr = getXMLHttpRequest();

					xhr.open("POST", "includes/clear_calques.php", true);
					xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");

					xhr.onreadystatechange = function() {
						if(xhr.readyState == 4 && xhr.status == 200)
							console.log("clear termine");
					}
					xhr.send("");
					i = 1;
				}
			}, false);
			document.getElementById("Licorne").addEventListener('click', function(ev) {
				//console.log(ev.target.value);
				modif_checked("Licorne");
			}, false);
			document.getElementById("TrollFace").addEventListener('click', function(ev) {
				modif_checked("TrollFace");
			}, false);
			document.getElementById("Flame").addEventListener('click', function(ev) {
				modif_checked("Flame");
			}, false);
			shotbutton.addEventListener('click', function(ev){
				takepicture();
				//alert("A faire avec GD !");
			}, false);
			var errorCallback = function(e) {
				console.log('Rejected!', e);
				alert("Webcam disconnected ! Please, reconnect the webcam.");
			};
		</script>
	</body>
</html>