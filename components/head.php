<!DOCTYPE html>
<html lang="fr">
    <head>
        <!-- Encodage des caractères standards du web -->
        <meta charset="UTF-8">

        <!-- Minimum de responsive design -->
        <meta name="viewport" content="width=device-width, initial-scale=1.0">

        <!-- Titre de la page -->
        <title>Cinema<?= isset($title) ? " - $title" : "" ?></title>

        <!-- Favicon -->
        <link rel="apple-touch-icon" sizes="180x180" href="assets/favicon/apple-touch-icon.png">
        <link rel="icon" type="image/png" sizes="32x32" href="assets/favicon/favicon-32x32.png">
        <link rel="icon" type="image/png" sizes="16x16" href="assets/favicon/favicon-16x16.png">
        <link rel="manifest" href="assets/favicon/site.webmanifest">
        <link rel="mask-icon" href="assets/favicon/safari-pinned-tab.svg" color="#5bbad5">
        <meta name="msapplication-TileColor" content="#da532c">
        <meta name="theme-color" content="#ffffff">

        <!-- Seo -->
        <meta name="robots" content="index,follow">
        <meta name="description" content="<?= isset($description) ? $description : '' ?>" >
        <meta name="author" content="Bléza PLEGNON">
        <meta name="publisher" content="dwwm-newyork">
        <meta name="keywords" content="cinema,">

        <!-- Font awesome -->
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" integrity="sha512-iecdLmaskl7CVkqkXNQ/ZH/XLlvWZOJyj7Yy7tcenmpD1ypASozpmT/E0iPtmFIB46ZmdtAc9eNBvH0H/ZpiBw==" crossorigin="anonymous" referrerpolicy="no-referrer" />

        <!-- Google font -->
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Pacifico&family=Poppins&display=swap" rel="stylesheet">

        <!-- Bootstrap 5 -->
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-9ndCyUaIbzAi2FUVXJi0CjmCapSmO7SnpJef0486qhLnuZ2cdeRhO02iuK6FUUVM" crossorigin="anonymous">

        <!-- Ma feuille de style -->
        <link rel="stylesheet" href="assets/styles/app.css">
    </head>

    <body class="bg-light">