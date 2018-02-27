<?php
$servername = "localhost";
$dbname = "Camagru";
$username = "root";
$password = "pass";
$options = array(
    PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8',
);

try {
    $bdd = new PDO('mysql:host='.$servername.';dbname='.$dbname, $username, $password, $options);
} catch (PDOException $e) {
    print "Erreur !: " . $e->getMessage() . "<br/>";
    die();
}
?>