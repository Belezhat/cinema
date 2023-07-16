<?php
session_start();

    // Si l'identifiant du film n'existe pas dans la barre d'URL ou 
    // qu'il est vide
    if ( !isset($_GET['film_id']) || empty($_GET['film_id']) )
    {
        // Effectuer une redirection vers la page d'accueil
        return header("Location: index.php");
    }

    // Récupérer cet identifiant en protégeant le serveur contre les failles de type XSS.
    $filmId = (int) htmlspecialchars($_GET['film_id']);

    // Etablir une connexion avec la base de données
    require __DIR__ . "/db/connexion.php";

    // Vérifier dans la table "film" si l'identifiant du film à modifier correspond bien 
    // à celui d'un film déja existant.
    $req = $db->prepare("SELECT * FROM film WHERE id=:id LIMIT 1");
    $req->bindValue(":id", $filmId);
    $req->execute();

    // Comptons le nombre d'enregistrements récupéré
    $row = $req->rowCount();

    // Si ce n'est pas égal à 1
    if ( $row !== 1 )
    {
        // Effectuer une redirection vers la page d'accueil
        // Puis, arrêter l'exécution du script.
        return header("Location: index.php");
    }

    // Dans le cas contraire, récupérer les données du film à modifier
    $film = $req->fetch();


    // Effectuer la requête de suppression du film dans sa table
    $deleteReq = $db->prepare("DELETE FROM film WHERE id=:id");
    $deleteReq->bindValue(":id", $film['id']);
    $deleteReq->execute();
    $deleteReq->closeCursor();

    
    // Générer un message flash de succès
    $_SESSION['success'] = "<em>$film[name]</em> a été supprimé de la liste.";


    // Effectuer uen redirection vers la page d'accueil
    return header("Location: index.php");