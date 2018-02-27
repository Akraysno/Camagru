<?php
session_start();
$dirshot = "./Screenshot";
$dircalques = "./Calques";
include('includes/connect_bdd.php');
$nb_files = 0;
$recup_photo = $bdd->prepare("SELECT `owner`, `ID_owner`, `Users`.`id`, `name`, `file_name`, `date` FROM `Photos` RIGHT JOIN `Users` ON `Users`.`id` = `Photos`.`ID_owner`");
$recup_photo->execute();
$result = $recup_photo->fetchAll();
foreach ($result as $raw) {
	if (isset($raw['file_name']) && $raw['file_name']) {
		$nb_files++;
	}
}

// TODO rajouter des filtres de recherche sur owner / date (cette semaine, moins de 24h)
// TODO faire un rendu avec seulement quelques images par pages
// TODO securiser les champs s'ils sont null
// TODO rajouter alt/title sur les images meme quand les images existes pas

?>

<!DOCTYPE html>
<html lang="">
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
		<title>Gallery - Camagru</title>
		<link rel="icon" type="image/png" href="images/42b.png" />
		<meta name="Author" content="gauffret" charset="UTF-8">
		<link rel="stylesheet" type="text/css" href="style.css">
	</head>
	<body>
		<div id="page">
			<div id="main_bloc">
				<?php include('includes/header.php'); ?>
				<div id="gallery"><br />
					<?php echo '<span id="gallery_total">Nombre de résultats: '.$nb_files.'</span><br />'; ?>
					<div id='gallery_area'>
						<?php
						if ($result) {
							foreach ($result as $raw) {
								if (isset($raw['file_name']) && $raw['file_name']) {
									echo "<div id='gallery_img'>";
									//alt="photo by '.$raw['owner'].'" title="by '.$raw['owner'].' ('.$raw['date'].')"
									echo '<img src="'.$dirshot.'/'.$raw['file_name'].'"  width="320" height=" 240"/><br/>';
									echo "<figcaption class='legende'>".$raw['name']."</figcaption>";
									echo "</div>";
								}
							}
						}
						?>
					</div>
				</div>
			</div>
			<?php include('includes/footer.php'); ?>
		</div>
	</body>
</html>