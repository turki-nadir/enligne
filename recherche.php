<?php
session_start();
require_once 'settings/bdd.inc.php';        // base de données
require_once 'settings/init.inc.php';       //pour affichager les erreurs
include_once 'includes/header.inc.php';     //renvoi à la page PHP incluant le header


if (isset($_GET['recherche'])) {          //permet la recherche d'une parametre 
    $recherche = $_GET['recherche'];        //pour injecter une variable dans l'URL 
    $sth = $bdd->prepare("SELECT id, titre, texte, DATE_FORMAT (date, '%d/%m/%Y') as date_fr FROM articles WHERE (titre LIKE :recherche OR texte LIKE :recherche)");  //préparation d'une requete SQL de recherche de termes avec LIKE dans les champs titres et texte
    $sth->bindValue(':recherche', "%$recherche%", PDO::PARAM_STR);      //permet la sécurisation des variables
    $sth->execute();                   // exécution 

    $count = $sth->rowCount();             //Compter le nombre d'occurence pour notre recherche

    $tab_result = $sth->fetchAll(PDO::FETCH_ASSOC);   //stocker la résultat sous forme d'un tableau
                                                
}
?>

<div class="span7 hero-unit">

    
    <?php
    if ($count >= 1) {                      //verifcation si un article correspond à la recherche
        ?><div class = "alert alert-success text-center" role = "alert">   <!--pour indiquer le nombre d'article trouvé -->
            <?php echo $count ?> résultat(s) trouvé(s) pour <strong>"<?php echo $recherche ?>"</strong>
        </div>   <?php
        foreach ($tab_result as $value) {     //boucle foreach pour parcourir le tableau 
            ?>
            <h2><?php echo $value['titre']; ?></h2>     <!-- elle fait appel a  la valeur 'titre-->
            <img src="img/<?php echo $value['id']; ?>.jpg" width="100px" alt="titre"/>    
            <p style="text-align: justify;"><?php echo $value['texte']; ?></p>      <!-- fait appel au base de donnée-->
            <p><em><u>Publié le : <?php echo $value['date_fr']; ?></u></em></p>        <!-- celle fait appel à a variable 'date_fr'-->
        <?php
        if (isset($_COOKIE['sid'])AND $_SESSION['connexion'] == TRUE) {                        //permet au utilisateur connecté la possibilité de modifier les articles 
            ?>
                <a href="article.php?id=<?php echo $value['id']; ?>">Modifier cet Article</a>  <!-- redirection  -->
                <?php
            }
        }
    } else {
        ?><div class="alert alert-error" role="alert">   <!--il indique au utilisateur que la recherche n'a rien trouvé -->
            <strong>Désolé !!! </strong><?php echo $count ?> résultat trouvé pour <strong>"<?php echo $recherche ?>"</strong>
        </div>   <?php
}
    ?>
</div>

<?php
include_once 'includes/menu.inc.php';       //redirection
include_once 'includes/footer.inc.php';     //redirection 
?>  