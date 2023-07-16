<?php
session_start();

    // Si les données arrivent au serveur via la méthode "POST",
    if ( $_SERVER['REQUEST_METHOD'] === "POST" ) 
    {

        $postClean = [];
        $errors    = [];
        
        // Protection contre les failes de type XSS
            // https://fr.wikipedia.org/wiki/Cross-site_scripting

        foreach ($_POST as $key => $value)
        {
            $postClean[$key] = htmlspecialchars(trim($value));
        }
        
        // Protection contre les failles de type CSRF
        // https://www.vaadata.com/blog/fr/attaques-csrf-principes-impacts-exploitations-bonnes-pratiques-securite/#:~:text=Les%20jetons%20CSRF%20sont%20des,serveur%20et%20envoy%C3%A9es%20au%20client.
        
        // Si le jéton de sécurité provenant du formulaire n'est pas le même que celui généré par le système,
        if ( !isset($postClean['csrf_token']) || !isset($_SESSION['csrf_token']) ) 
        {
            // Effectuons une redirection vers la page de laquelle proviennent les données
            // Arrêtons l'exécution du script
            return header("Location: $_SERVER[HTTP_REFERER]");
        }
        if ( empty($postClean['csrf_token']) || empty($_SESSION['csrf_token']) ) 
        {
            // Effectuons une redirection vers la page de laquelle proviennent les données
            // Arrêtons l'exécution du script 
            return header("Location: $_SERVER[HTTP_REFERER]");
        }
        if ( $postClean['csrf_token'] !== $_SESSION['csrf_token'] ) 
        {
            // Effectuons une redirection vers la page de laquelle proviennent les données
            // Arrêtons l'exécution du script
            return header("Location: $_SERVER[HTTP_REFERER]");
        }
        unset($_SESSION['csrf_token']);
        
        
        // Protection contre les robots spameurs
        if ( isset($postClean['honey_pot']) && !empty($postClean['honey_pot']) ) 
        {
            // Effectuons une redirection vers la page de laquelle proviennent les données
            // Arrêtons l'exécution du script
            return header("Location: $_SERVER[HTTP_REFERER]");
        }

        
        // Validons les données du formulaire
        if ( isset($postClean['name']) ) 
        {
            $name = htmlspecialchars_decode($postClean['name']);
            
            if ( empty($name) )
            {
                $errors['name'] = "Le nom du film est obligatoire.";
            }
            else if( ! preg_match('/^[a-zA-ZáàâäãåçéèêëíìîïñóòôöõúùûüýÿæœÁÀÂÄÃÅÇÉÈÊËÍÌÎÏÑÓÒÔÖÕÚÙÛÜÝŸÆŒ0-9-_\'&! ]+$/', $name) )
            {
                $errors['name'] = "Le nom du film n'est pas valide.";
            }
            else if( mb_strlen($name) > 255 )
            {
                $errors['name'] = "Le nom du film ne doit pas dépasser 255 caractères.";
            }
        }

                
        if ( isset($postClean['actors']) )
        {
            $actors = htmlspecialchars_decode($postClean['actors']);
            
            if ( empty($actors) )
            {
                $errors['actors'] = "Le nom du ou des acteurs est obligatoire.";
            }
            else if( ! preg_match('/^[a-zA-ZáàâäãåçéèêëíìîïñóòôöõúùûüýÿæœÁÀÂÄÃÅÇÉÈÊËÍÌÎÏÑÓÒÔÖÕÚÙÛÜÝŸÆŒ0-9,-_\'&! ]+$/', $actors) )
            {
                $errors['actors'] = "Le nom du ou des acteurs n'est pas valide.";
            }
            else if( mb_strlen($actors) > 255 )
            {
                $errors['actors'] = "Le nom du ou des acteurs ne doit pas dépasser 255 caractères.";
            }
        }


        if ( isset($postClean['review']) ) 
        {
            if ( $postClean['review'] !== "" ) 
            {
                if ( ! is_numeric($postClean['review']) ) 
                {
                    $errors['review'] = "Veuillez entrer une note valide.";
                }
                else if( ($postClean['review'] < "0") || ($postClean['review'] > "5") )
                {
                    $errors['review'] = "La note doit être comprise entre 0 et 5.";
                }
            }
        }


        if ( isset($postClean['comment']) ) 
        {
            $comment = htmlspecialchars_decode($postClean['comment']);

            if ( $postClean['comment'] !== "" )
            {
                if( ! preg_match('/^[a-zA-ZáàâäãåçéèêëíìîïñóòôöõúùûüýÿæœÁÀÂÄÃÅÇÉÈÊËÍÌÎÏÑÓÒÔÖÕÚÙÛÜÝŸÆŒ0-9.-_&!\'\n\r ]+$/', $comment) )
                {
                    $errors['comment'] = "Seuls les chiffres, les lettres, le tiret, l'undescore sont acceptés.";
                }
                else if ( mb_strlen($comment) > 1000 ) 
                {
                    $errors['comment'] = "Le commentaire ne doit pas dépasser 1000 caractères.";
                }
            }
        }
        
        // S'il y a au moins une erreur
        if (count($errors) > 0) 
        {
            // Sauvegarder les messages d'erreurs en session
            $_SESSION['formErrors'] = $errors;
            
            // Sauvegarder les anciennes données du formulaire en session
            $_SESSION['old'] = $postClean;

            // Rediriger l'utilisateur vers la page de laquelle proviennent les informations
            // Arrêter l'exécution du script.
            return header("Location: $_SERVER[HTTP_REFERER]");
        }
        
        // Dans le cas contraire,
        
        // Arrondir la note à un chiffre après la virgule, si elle est envoyée
        if ( isset($postClean['review']) && !empty($postClean['review']) ) 
        {
            $reviewRounded = round($postClean['review'], 1);
        }
        
        // Etablir une connexion avec la base de données
        require __DIR__ . "/db/connexion.php";

        // Effectuer la requête d'insertion des données dans la table "film"
        $req = $db->prepare("INSERT INTO film (name, actors, review, comment, created_at, updated_at) VALUES (:name, :actors, :review, :comment, now(), now() ) ");

        $req->bindValue(":name", $postClean['name']);
        $req->bindValue(":actors", $postClean['actors']);
        $req->bindValue(":review", isset($reviewRounded) ? $reviewRounded : '' );
        $req->bindValue(":comment", $postClean['comment']);

        $req->execute();
        $req->closeCursor();

        // Générer un message flash de succès
        $_SESSION['success'] = "<em>$postClean[name]</em> a été ajouté à la liste avec succès.";
        
        // Effectuer une redirection vers la page d'accueil
        // Arrêter l'exécution du script.
        return header("Location: index.php");
    }

    $_SESSION['csrf_token'] = bin2hex(random_bytes(30));
        
?>
<?php $title = "Création du film"; ?>
<?php $description = "Création du film"; ?>
<?php require __DIR__ . "/components/head.php"; ?>
    
    <?php require __DIR__ . "/components/nav.php"; ?>

        <main class="container">
            <h1 class="text-center my-3 display-5">Nouveau film</h1>

            <div class="container">
                <div class="row">
                    <div class="col-md-9 col-lg-6 mx-auto shadow p-4 bg-white">

                        <?php if( isset($_SESSION['formErrors']) && !empty($_SESSION['formErrors']) ) : ?>
                            <div class="alert alert-danger" role="alert">
                                <ul>
                                    <?php foreach($_SESSION['formErrors'] as $error) : ?>
                                        <li><?= $error; ?></li>
                                    <?php endforeach ?>
                                </ul>
                            </div>
                        <?php unset($_SESSION['formErrors']); ?>
                        <?php endif ?>

                        <form method="POST">
                            <div class="mb-3">
                                <label for="name">Nom du film <span class="text-danger">*</span> : </label>
                                <input type="text" name="name" id="name" class="form-control" autofocus value="<?= isset($_SESSION['old']['name']) ? $_SESSION['old']['name'] : ''; unset($_SESSION['old']['name']); ?>">
                            </div>
                            <div class="mb-3">
                                <label for="actors">Nom du ou des acteurs <span class="text-danger">*</span> : </label>
                                <input type="text" name="actors" id="actors" class="form-control" value="<?= isset($_SESSION['old']['actors']) ? $_SESSION['old']['actors'] : ''; unset($_SESSION['old']['actors']); ?>">
                            </div>
                            <div class="mb-3">
                                <label for="review">La note du film / 5 : </label>
                                <input type="number" step="0.1" min="0" max="5" name="review" id="review" class="form-control" value="<?= isset($_SESSION['old']['review']) ? $_SESSION['old']['review'] : ''; unset($_SESSION['old']['review']); ?>">
                            </div>
                            <div class="mb-3">
                                <label for="comment">Un commentaire : </label>
                                <textarea name="comment" id="comment" class="form-control" rows="4"><?= isset($_SESSION['old']['comment']) ? $_SESSION['old']['comment'] : ''; unset($_SESSION['old']['comment']); ?></textarea>
                            </div>
                            <div class="mb-3 d-none">
                                <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token']; ?>">
                            </div>
                            <div class="mb-3 d-none">
                                <input type="hidden" name="honey_pot" value="">
                            </div>
                            <div class="mb-3">
                                <input formnovalidate type="submit" class="btn btn-primary shadow">
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </main>
        
    <?php require __DIR__ . "/components/footer.php"; ?>

<?php require __DIR__ . "/components/foot.php"; ?>