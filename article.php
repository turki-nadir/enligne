<?php
session_start();    //recuperation des variable 

require_once 'settings/bdd.inc.php';    //base de donnée
require_once 'settings/init.inc.php';   //pour affichage les erreurs
               

if (isset($_SESSION['commentaire']) and $_SESSION['commentaire'] == TRUE) {
    unset($_SESSION['commentaire']);
}
                //si l'id est present il affiche la formulaire 
if (isset($_GET['id'])) {               // verifier la presence d'un ID
    $id = $_GET['id'];  // déclaré l'id         
                          //l'id est présent alors on  récupére l'article 
    $sth = $bdd->prepare("SELECT id, titre, texte, DATE_FORMAT (date, '%d/%m/%Y') as date_fr FROM articles WHERE id =$id"); //préparation de la requete pour récupération ld
    $sth->bindValue(':id', 1, PDO::PARAM_INT);  //Sécurisé la valeur  numerique de type 'PARAM_INT'
    $sth->execute();                     //execution 
    $tab_articles = $sth->fetchAll(PDO::FETCH_ASSOC);   //stocker la résultat sous forme d'un tableau
    
    
            //insertion des champs du formulaire avec les éléments récupérés par la requéte
    $titre = $tab_articles [0] ['titre'];   //création d'une variable titre récuperer dans le tableau 
    $article = $tab_articles [0] ['texte']; //pareil avec le contenu de l'article
    $bouton = "Modifier";   //pour que la bouton Modifier devient cliquable
    $case = "checked";  //insertion de la class checked
    
    
    
    //si pas d'id est present dans l'URL, on affiche pas le formulaire 
    
} else {
    $tab_articles = null;
    $titre = "";    
    $article = "";
    $bouton = "Ajouter";    // et change l'intitulé de bouton  'ajouter' car il s'agit d'un nouvel article
    $case = "";
    $id = "";
}
                //gestion de l'ajout d'un nouvel article
if (isset($_POST['Ajouter'])) { //condition permettant de lancer le php si le bouton submit a été cliqué
                //      print_r($_FILES);   permet l'affiche de l'image sous la forme du tableau
                //      exit(); stop le(s) script(s) d'affiche des données bruts
    $date_ajout = date("Y-m-d");    //fonction affichant la date
    $_POST['date'] = $date_ajout;
                 //    print_r($_POST);  affiche la date en données brut
    $_POST['publie'] = isset($_POST['publie']) ? 1 : 0; //condition ternaire controlant la présence d'une image lors du post 
                //    print_r($_POST);  affiche toutes les entrées du tableau en données brut
    
                   //Vérification que les champs du formulaire ont bien été complétés
    if($_POST['titre'] == NULL || $_POST['texte'] == NULL){?>
            <script type="text/javascript">
                    alert("Veuillez compléter tout les champs !");
                </script>
        <?php
        
    }else{
    
    if ($_FILES['image']['error'] == 0) {   //tester si l'image est valide
        $sth = $bdd->prepare("INSERT INTO articles (titre, texte, publie, date) VALUES(:titre, :texte, :publie, :date)");   //préparer la requette  pour l'excution 
        $sth->bindValue(':titre', $_POST['titre'], PDO::PARAM_STR); //Sécurise les valeurs du variable de type PARAM_INT
        $sth->bindValue(':texte', $_POST['texte'], PDO::PARAM_STR);
        $sth->bindValue(':publie', $_POST['publie'], PDO::PARAM_INT);
        $sth->bindValue(':date', $_POST['date'], PDO::PARAM_STR);
        $sth->execute();    //execution de la requette 
        $id = $bdd->lastInsertId();
        
        
        move_uploaded_file($_FILES['image']['tmp_name'], dirname(__FILE__) . "/img/$id.jpg");   //permet de déplacer les images du formulaire vers le dossier img, en renommant l'image par un nom associé à Id de l'article
        $_SESSION['ajout_article'] = TRUE;  //Création d'une session d'ajout d'article
        header("location: article.php");    //Recharge la page article en fin de processus
        
    } else {
        
        echo "Image erreur";    //Gestion des erreur liée a l'image
        
    }
}
}
//Gestion de la modification d'un article
if (isset($_POST['Modifier'])) {    //si le bonton modifier a été cliqué
    $id_form = $_POST['id'];
    $sth = $bdd->prepare("UPDATE articles SET titre= :titre, texte=:texte WHERE id=$id_form");  //préparation de la requete de mise à jour
    $sth->bindValue(':titre', $_POST['titre'], PDO::PARAM_STR); //Sécurise les valeurs des variables qui peut être introduite dans la base. Cette valeur est forcement numerique du fait du PARAM_INT
    $sth->bindValue(':texte', $_POST['texte'], PDO::PARAM_STR);
    $sth->execute();
    $_SESSION['modif_article'] = TRUE; //session de modif d'article ouverte pour gestion message
    header('Location: index.php'); //retour a la page Article.php
} 
//Affichage du formulaire
else {  // sinon lancer le html seul
    include_once 'includes/header.inc.php'; //renvoi à la page PHP incluant le header
    ?>



    <div class="span8"> <!-- mise en page du formulaire par la grille de Bootstrap-->
        
      
        
                        <!-- Gestion du message d'ajout d'un article -->
    <?php
    if (isset($_SESSION['ajout_article']) AND $_SESSION['ajout_article'] == TRUE) { // condition permettant l'affichage d'un message de confirmation de l'envoi de l'article
        ?>
            <div class = "alert alert-success text-center" role = "alert">  <!--Ajoute une alerte indiquant que l'article est bien chargé-->
                <strong>Félicitations</strong> Votre article a été ajouté!!!
            </div>
            <?php
        }
        ?>



        <form action="article.php" method="post" enctype="multipart/form-data" id="form_article">   <!-- Balise form pour le formulaire de création d'articles-->

            <div class="clearfix">
                <label for="titre">Titre</label>
                <div class="input"><input type="text" name="titre" id="titre" value="<?php echo $titre ?>"></div>   <!-- insertion de php permettant de récuperer les données des articles pour les mettre dans les champs correspondants -->
            </div>

            <div class="clearfix">
                <label for="texte">Texte</label>
                <div class="input"><textarea name="texte" id="texte"><?php echo $article ?></textarea></div>    <!-- insertion de php permettant de récuperer les données des articles pour les mettre dans les champs correspondants -->
            </div>

            <div class="clearfix">
                <label for="image">Image</label>
                <div class="input"><input type="file" name="image" id="image"></div>
            </div>

            <div class="clearfix">
                <label for="publie">Publié</label>
                <div class="input"><input type="checkbox" <?php echo $case ?> name="publie" id="publie"></div>  <!-- insertion de php permettant de récuperer les données des articles pour les mettre dans les champs correspondants -->
            </div>

            <div class="clearfix">
                <div class="input"><input type="hidden" name="id" id="id" value="<?php echo $id ?>"></div>  <!-- insertion de php permettant de récuperer les données des articles pour les mettre dans les champs correspondants -->
            </div>

            <div class="form-actions">
                <input type="submit" name="<?php echo $bouton ?>" value="<?php echo $bouton ?>" class="btn btn-large btn-primary"></div>    <!-- insertion de php permettant de récuperer les données des articles pour les mettre dans les champs correspondants -->


        </form>

    </div>

    <?php
    include_once 'includes/menu.inc.php';
    include_once 'includes/footer.inc.php';
}
