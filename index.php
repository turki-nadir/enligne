<?php
session_start();
require_once 'settings/bdd.inc.php';        //base de données
require_once 'settings/init.inc.php';       //pour affichager les erreurs
include_once 'includes/header.inc.php';     //renvoi à la page PHP incluant le headers

$articlesParPage = 2;                                         // le nombre d'article par page 
$numeroPage = isset($_GET['p']) ? $_GET['p'] : 1;             // récupérer le numero de la page dans l'URL
$index = (($numeroPage - 1) * $articlesParPage);              // calculer le numero d'index du début de la page

$nbreMessage = $bdd->prepare("SELECT COUNT(*) as nbArticles FROM articles WHERE publie = :publie");    // requete sql indiquant le nbre de message dans la table
$nbreMessage->bindValue(':publie', 1, PDO::PARAM_INT);                                                 //Sécurise la valeur de la variable qui peut être introduite dans la base. Cette valeur est forcement numerique du fait du PARAM_INT
$nbreMessage->execute();                                                                               //Permet l'execution de la requete

$nbArticles = $nbreMessage->fetchAll(PDO::FETCH_ASSOC);             //création de la variable dans laquelle on injecte le tableau 
$nbreArticle = $nbArticles[0]['nbArticles'];                        // recupérer le nombre d'article dans le tableau

$nbredepages = ceil($nbreArticle / $articlesParPage);       //Affiche le nombre de page 

function indexDepart($numeroPage, $articlesParPage) {           //calculer l'index en fonction de la page 
    $index = (($numeroPage - 1) * $articlesParPage);
    return $index;
}

$indexDynamique = indexDepart($numeroPage, $articlesParPage);       //réutliser la fonction précedente 

$sth = $bdd->prepare("SELECT id, titre, texte, DATE_FORMAT (date, '%d/%m/%Y') as date_fr FROM articles WHERE publie = :publie LIMIT $indexDynamique, $articlesParPage");     //préparation de la requete
$sth->bindValue(':publie', 1, PDO::PARAM_INT);              //Sécurise la valeur du variable de type PARAM_INT
$sth->execute();                                            //execution de la requett

$tab_articles = $sth->fetchAll(PDO::FETCH_ASSOC);           //On créé une variable dans laquelle on injecte le tableau de données
//print_r
?>

<div class="span7 hero-unit">

    <?php
         if (isset($_COOKIE['sid']) AND $_SESSION['connexion']  == TRUE) {        // condition permettant de controler la présence du cookie de connexion
        ?>
        <div class = "alert alert-success text-center" role = "alert">   <!--Affichage d'un message de confirmation de connexion-->
            <strong>Félicitations</strong> Vous êtes connecté!!!
        </div>
        <?php
    }

//    echo "Hello Word !!!";        //echo affiche du texte

    foreach ($tab_articles as $value) {     //boucle foreach et une boucle for, specifique à l'exploitation des tableaux
        ?>
        <h2><?php echo $value['titre']; ?></h2>     <!-- cet appel php permet de faire appel à la valeur 'titre' et l'inserer dans le h2 du HTML-->
        <img src="img/<?php echo $value['id']; ?>.jpg" width="100px" alt="titre"/>      <!-- ce PHP renvoi à une image stockée dans le dossier IMG et dont le n° titre.jpg correspond aux id-->
        <p style="text-align: justify;"><?php echo $value['texte']; ?></p>      <!-- fait appel au texte de la base de donnée-->
        <p><em><u>Publié le : <?php echo $value['date_fr']; ?></u></em></p>        <!-- cet appel php permet de faire appel à la valeur 'date' et l'inserer dans du texte HTML-->
        <?php
        
        if (isset($_COOKIE['sid']) AND $_SESSION['connexion'] == TRUE) {                        //Donne l'accés à la modification des articles uniquement si la connexion est certifié par le cookie
            ?>
            <a href="article.php?id=<?php echo $value['id']; ?>"><p>
                    <button class="btn btn-primary" type="button">Modifier cet article</button>
                </p></a>  <!-- Ce lien redirige vers la page article en ajoutant l'id dans l'URL par la méthode GET   -->
            <?php
        }
    }
    ?>

    <div class="pagination pagination-centered">
        <ul>
            <li><a>Page : </a></li>
            <?php
            for ($i = 1; $i <= $nbredepages; $i++) {                //boucle for permettant la création de boutons en fonction du nombre de pages à afficher
                if ($numeroPage == $i) {                            //ce if compare si la page actuelle affiché correspond au numéro du bouton
                    $ClassBouton = 'active';                        // si la condition est vérifiée, alors il active la class "active" de Bootstrap
                } else {
                    $ClassBouton = '';                              // sinon il laisse l'affichage normal sans class
                }
                ?>
                <li class="<?php echo $ClassBouton ?>"> <a href="index.php?p=<?= $i ?>"><?= $i ?></a> </li>   <!-- boutons affichant le nombre de page avec notre variable classbouton activé ou non fonction de la page actuellement affiché  -->
                <?php
            }
            ?>
        </ul>
    </div>
</div>

<?php
include_once 'includes/menu.inc.php';       //redirection
include_once 'includes/footer.inc.php';     //redirection
?>

