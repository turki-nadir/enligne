<?php
session_start();
require_once 'settings/bdd.inc.php';        // base de données
require_once 'settings/init.inc.php';       //pour affichager les erreurs
include_once 'includes/header.inc.php';     //renvoi à la page PHP incluant le headers
?>

<div class="span8">`    <!-- mise en page du formulaire par la grille Bootstrap-->
    <?php
    if (isset($_SESSION['creationOK']) AND $_SESSION['creationOk'] == TRUE) {      // affiche la message de condirmation de l'envoi de la page 
        ?>
        <div class = "alert alert-success text-center" role = "alert">   <!--confirmer si l'article est belle est bien chargé -->
            <strong>Félicitations</strong> Votre compte a été créé!!!
        </div>
        <?php
    }
    ?>
    
    <?php
    if (isset($_SESSION['connexion']) and $_SESSION['connexion'] == FALSE) {   //verification de la saission 
        ?>
        <div class="alert alert-error" role="alert">
             Votre compte ou mot de passe est incorrect.</div>    <!-- renvoi d'erreur -->
        <?php
    }
    if (isset($_COOKIE['sid'])AND $_SESSION['connexion'] == TRUE) {              // deconnexion si le cookie est present 
        ?>
        <div class="alert alert-error" role="alert">
            <strong>Merci pour Votre Visite!!!</strong> Au revoir
        </div>
        <?php
        setcookie('sid', time() - 30);              // detruire le cookie par un timelaps négatif
        $_SESSION['connexion'] = FALSE;  //deconnexion 
    }
    ?>

    <form action="connexion.php" method="post" enctype="multipart/form-data" id="form_article" name="connexion">  <!-- formulaire de connexion -->

        <div class="clearfix">
            <label for="email">Email</label>
            <div class="email"><input type="email" name="email" id="email" value="Votre Email"></div>
        </div>

        <div class="clearfix">
            <label for="mdp">Mot De Passe</label>
            <div class="input"><input type="password" name="mdp" id="mdp" value="Votre Passe"></div>
        </div>

        <div class="clearfix">
            <input type="submit" name="envoi" value="Envoyer" class="btn btn-large btn-primary"></div>
        </br>
        <div><p>Pas encore membre ? Inscrivez vous <a href="compte.php">ici</a></p></div>

    </form>

</div>

<?php
if (isset($_POST['envoi'])) {          //Vérifie si le bouton est declonché 
    $conex = $bdd->prepare("SELECT * FROM utilisateurs WHERE email =  :email AND mdp = :mdp");
    $conex->bindValue(':mdp', $_POST['mdp'], PDO::PARAM_STR);                   // sécuriser l'accés 
    $conex->bindValue(':email', $_POST['email'], PDO::PARAM_STR);
    $conex->execute();                                          //Execution de la requette

    $count = $conex->rowCount();
    $tab_conex = $conex->fetchAll(PDO::FETCH_ASSOC);
    //Controle du contenu du tableau  


    if ($_POST['email'] == $tab_conex[0]['email'] && $_POST['mdp'] == $tab_conex[0]['mdp']) {    //vérification de la correspondance des données ajoutées 
        $email = $tab_conex[0]['email'];          //créer la variable email
        $sid = md5(time() . $email);            // créer une variable sid à partir du mail et du temps par la fonction md5
        echo $sid;

        $id = $tab_conex[0]['id'];
        $conex = $bdd->prepare("UPDATE utilisateurs SET sid='$sid' WHERE id='$id'");  //permet la mise à jour de la base 
        $conex->execute();
        setcookie('sid', $sid, time() + 300);        //créer une cookies dans un temps bien déterminé exemple 30 secondes
        header('Location: index.php');              //redirection 
        $_SESSION['connexion'] = TRUE;
    } else {
        $_SESSION['connexion'] = FALSE;            // Session de connexion fausse
        header('Location: connexion.php');         //redirection 
    }
}
include_once 'includes/footer.inc.php';
?>