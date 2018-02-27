<?php
$servername = "localhost";
$dbname = "Camagru";
$username = "root";
$password = "pass";
$options = array(
    PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8',
);

$connect = mysqli_connect($servername, $username, $password);
if (mysqli_connect_error()) {
	echo "Echec lors de la connexion à MySQL : (" . mysqli_connect_errno() . ") " . mysqli_connect_error() . "<br>";
}
else
{
	if (!mysqli_query($connect, "DROP DATABASE IF EXISTS Camagru"))
		echo mysqli_error($connect)."ERROR DROP DB<br>";
	if (!mysqli_query($connect, "CREATE DATABASE Camagru"))
		echo mysqli_error($connect)."ERROR CREATE DB<br>";
}

/*
TODO : Creer BDD Camagru via PDO
try {
    $bdd = new PDO('mysql:host=$servername;dbname=myDB', $username, $password);
    echo "connection bdd ok<br>";
    $bdd->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $drop = $bdd->prepare("DROP DATABASE IF EXISTS :name");
    $drop->bindParam(':name', $name);

    $name = 'Camagru';
    $drop->execute();

    $create = $bdd->preprare("CREATE DATABSE :name");
    $create->bindParam(':name', $name);
    $name = 'Camagru';
    $create->execute();
} catch (PDOException $e) {
    print "Erreur !: " . $e->getMessage() . "<br/>";
    die();
}*/
try {
    $bdd = new PDO('mysql:host='.$servername.';dbname='.$dbname, $username, $password, $options);
	//echo "Connection done<br/>";
	
    $drop = $bdd->prepare("DROP TABLE IF EXISTS `Users`");
    $drop->execute();
	$drop = $bdd->prepare("DROP TABLE IF EXISTS `Validation`");
	$drop->execute();
	$drop = $bdd->prepare("DROP TABLE IF EXISTS `Photos`");
	$drop->execute();
	//echo "Tables created<br />";

    $create_users = $bdd->prepare("CREATE TABLE Users(`id` INT PRIMARY KEY UNIQUE AUTO_INCREMENT NOT NULL, `login` VARCHAR(256) NOT NULL, `passwd` VARCHAR(256) NOT NULL, `mail` VARCHAR(256) NOT NULL, `valid` INT DEFAULT '0', `admin` INT DEFAULT '0')");
    $create_users->execute();
    
    $create_valid = $bdd->prepare("CREATE TABLE Validation(`id` INT PRIMARY KEY UNIQUE AUTO_INCREMENT NOT NULL, `code` VARCHAR(10) NOT NULL, `ID_Users` INT NOT NULL)");
    $create_valid->execute();
	
	$create_users = $bdd->prepare("CREATE TABLE Photos(`id` INT PRIMARY KEY UNIQUE AUTO_INCREMENT NOT NULL, `owner` VARCHAR(256) NOT NULL, ID_owner INT NOT NULL, `name` VARCHAR(100) NOT NULL, `date` DATETIME NOT NULL, `file_name` VARCHAR(256) NOT NULL)");
	$create_users->execute();

    $insert_users = $bdd->prepare("INSERT INTO Users(login, passwd, mail, valid, admin) VALUES (:login, :pass, :mail, :valid, :admin)");
    $insert_users->bindParam(':login', $login);
    $insert_users->bindParam(':pass', $pass);
    $insert_users->bindParam(':mail', $mail);
    $insert_users->bindParam(':valid', $valid);
    $insert_users->bindParam(':admin', $admin);

    $login = 'FirstGuy';
    $pass = hash("whirlpool", 'password');
    $mail = 'admin42@mail.com';
    $valid = '1';
    $admin = '0';
    $insert_users->execute();
    $login = 'administrator';
	$pass = hash("whirlpool", 'administrator');
	$mail = 'test@mail.com';
    $valid = '1';
    $admin = '1';
    $insert_users->execute();
    header('Location: connection.php');
} catch (PDOException $e) {
    print "Erreur !: " . $e->getMessage() . "<br/>";
    die();
}
?>