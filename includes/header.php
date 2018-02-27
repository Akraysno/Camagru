<div id='menu'>
    <a class='menu_page' href='index.php'>Accueil</a>
    <a class='menu_page' href='gallery.php'>Gallerie</a>
    <?php 

    include('connect_bdd.php');
    $error = 1;
    ///TODO ajouter un bouton pour pourvoir modifier le mot de passe
    //TODO faire requete pour recup mdp et login de la bdd et verifier si c'est bon
    if (isset($_SESSION['user_logged']) && $_SESSION['user_logged'] !== "") {
        $user = $_SESSION['user_logged'];
        $pass = $_SESSION['user_mdp'];
        
        
        $recup_users = $bdd->prepare("SELECT `login`, `passwd` FROM `Users` WHERE `login` = :login");
        $recup_users->bindParam(':login', $user);

      //  $login = $_SESSION['user_logged'];

        $recup_users->execute();
        $tab_pass = $recup_users->fetchAll();
        //print_r($tab_pass);

        if (!$tab_pass) {
            $error = 1;
        } else {
            foreach ($tab_pass as $line) {
                if ($line['login'] === $user && $line['passwd'] === $pass)
                    $error = 0;
            }
        }
    }
    if ($error == 1) {
        echo "<a class='menu_page2' href='connection.php'>Connexion</a>";
        echo "<a class='menu_page2' href='inscription.php'>Inscription</a>";
    }
    else {
        echo "<a class='menu_page' href='my_pictures.php'>Mes photos</a>";
		echo "<a class='menu_page2' href='includes/deconnection.php'>Deconnexion</a>";
        echo "<a class='menu_page2' href='#'>Mon compte</a>";
	}
    ?>
</div>