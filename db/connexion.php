<?php
/* Connexion à une base MySQL avec l'invocation de pilote */
$dsnDb = 'mysql:dbname=cinema;host=127.0.0.1;port=3306';
$userDb = 'root';
$passwordDb = '';

try 
{
    $db = new PDO($dsnDb, $userDb, $passwordDb);
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} 
catch (PDOException $e) 
{
    die("Database connection error : " . $e->getMessage());
}
?>