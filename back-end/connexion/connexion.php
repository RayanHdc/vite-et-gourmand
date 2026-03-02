<?php 
$host = "localhost";
$username = "jose";
$password = "AdminVite&Gourmand1";
$database = "vite_et_gourmand";

$dsn = "mysql:host=$host;dbname=$database;charset=utf8mb4";


try {
    $pdo = new PDO($dsn, $username, $password);

    echo "Connexion réussie à la base de données !";
} catch (PDOException $e) {
    die("Erreur de connexion : " . $e->getMessage());
}

?>