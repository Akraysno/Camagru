<?php
session_start();
foreach ($_SESSION as $k => $v) {
	unset($_SESSION[$k]);
}
header ("Location: $_SERVER[HTTP_REFERER]" );
?>