<nav class="span3">
    <h3>Menu</h3>
    <form action="recherche.php" method="get" enctype="multipart/form-data" id="form_recherche">

        <div class="clearfix">
            <div class="input"><input type="text" name="recherche" id="recherche" placeholder="Votre recherche..."/></div>
        </div>

        <div class="form-inline">
            <input type="submit" name="rechercher" value="Rechercher sur le blog" class="btn btn-small btn-primary">
        </div>

    </form>
    <ul class="nav nav-list">
        <li class="nav-header"><a href="index.php">Accueil</a></li>
        <?php
        if (isset($_COOKIE['sid']) AND $_SESSION['connexion'] == TRUE) {        // condition permettant de controler la présence du cookie de connexion
            ?>
            <li class="nav-header"><a href="article.php">Rédiger un article</a></li>  <!--Si cookie présent alors on accede à la page de rédaction d'articles -->
            <li class="nav-header"><a href="connexion.php">Déconnexion</a></li>  <!--Sinon on accede uniquement à la page de connexion -->
            <?php
        } else {
            ?>
            <li class="nav-header"><a href="connexion.php">Se connecter</a></li>  <!--Sinon on accede uniquement à la page de connexion -->
            <?php
        }
        if (isset($_COOKIE['sid']) AND $_SESSION['connexion'] == TRUE) {        // condition permettant de controler la présence du cookie de connexion
            ?>
            <li class="nav-header">Vous êtes connecté</li>
            <?php
        } else {
            ?>
            <li class="nav-header">Vous n'êtes pas connecté</li>
            <?php
        }
        ?>
    </ul>
</nav>
</div>