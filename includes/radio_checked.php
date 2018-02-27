<?php
session_start();
if (isset($_POST['new_check']) && $_POST['new_check'] !== "" && ($_POST['new_check'] === "Licorne" || $_POST['new_check'] === "TrollFace" || $_POST['new_check'] === "Flame"))
{
	$_SESSION['checked'] = $_POST['new_check'];
}
?>