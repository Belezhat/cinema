<?php
session_start();

    // Etablir une connexion avec la base de données
    require __DIR__ . "/db/connexion.php";

    // Effectuer la requête de sélection des données en base
    $req = $db->prepare("SELECT * FROM film ORDER BY created_at DESC");
    $req->execute();

    // Récupérer les données
    $films = $req->fetchAll();
    $req->closeCursor();
?>
<?php $title = "Bienvenue"; ?>
<?php $description = "Bienvenue"; ?>
<?php require __DIR__ . "/components/head.php"; ?>
    
    <?php require __DIR__ . "/components/nav.php"; ?>

        <main class="container">
            <h1 class="text-center my-3 display-5">Liste des films</h1>

            <?php if( isset($_SESSION['success']) && !empty($_SESSION['success']) ) : ?>
                <div class="text-center alert alert-success alert-dismissible fade show" role="alert">
                    <?= $_SESSION['success']; ?>    
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
                <?php unset($_SESSION['success']); ?>
            <?php endif ?>

            <div class="d-flex justify-content-end align-items-center my-3">
                <a href="create.php" class="btn btn-primary">Nouveau film</a>
            </div>


            <?php if( isset($films) && !empty($films) ) : ?>
                <div class="container mt-5">
                    <div class="row">
                        <div class="col-md-8 col-lg-6 mx-auto">
                            <?php foreach($films as $film) : ?>
                                <div class="card text-start my-3 shadow border-radius">
                                  <div class="card-body">
                                    <p class="card-text">Nom du film : <?= $film['name'] ?></p>
                                    <p class="card-text">Nom du ou des acteurs : <?= $film['actors'] ?></p>
                                    <hr>
                                    <a data-bs-toggle="modal" data-bs-target="#modal<?= $film['id'] ?>" title="Lire les détails" href="" class="text-dark mx-2"><i class="fa-solid fa-eye"></i></a>
                                    <a title="Modifier" href="edit.php?film_id=<?=$film['id']?>" class="text-secondary mx-2"><i class="fa-solid fa-pen-to-square"></i></a>
                                    <a onclick="return confirm('Confirmer la suppression ?')" title="Supprimer" href="delete.php?film_id=<?=$film['id']?>" class="text-danger mx-2"><i class="fa-solid fa-trash-can"></i></a>
                                  </div>
                                </div>

                                <!-- Modal -->
                                <div class="modal fade" id="modal<?= $film['id'] ?>" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
                                    <div class="modal-dialog">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h1 class="modal-title fs-5"><?= $film['name'] ?></h1>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                            </div>
                                            <div class="modal-body">
                                                <p>Note : <?= ($film['review'] === "") ? "Non renseignée" : $film['review']; ?></p>
                                                <p>Commentaire : <?= ($film['comment'] === "") ? "Non renseigné" : nl2br($film['comment']); ?></p>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-primary" data-bs-dismiss="modal">Fermer</button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach ?>
                        </div>
                    </div>
                </div>
            <?php else : ?>
                <p class="text-center mt-5 fs-3">Aucun film ajouté à la liste pour l'instant</p>
            <?php endif ?>
        </main>
        
    <?php require __DIR__ . "/components/footer.php"; ?>

<?php require __DIR__ . "/components/foot.php"; ?>