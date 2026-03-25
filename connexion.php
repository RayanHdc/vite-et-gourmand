<?php
require_once 'includes/host.php';

session_start();

//Vérification de la soumission du formulaire
if (!empty($_POST)) {

    $email = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';

    $sql = "SELECT * FROM utilisateur WHERE email = ?";

    $requete = $pdo->prepare($sql);
    $requete->execute([$email]);
    $user = $requete->fetch();

    // Vérification du mot de passe
    if ($user && password_verify($password, $user['password'])) {

        // Stockage des informations de l'utilisateur dans la session
        $_SESSION['user_id'] = $user['utilisateur_id'];
        $_SESSION['user_email'] = $user['email'];
        
        //gestion des rôles / droits d'accès   
        $_SESSION['user_role_id'] = $user['role_id'];

        header("Location: index.php");
        exit();
    } else {
        echo "<h2>Adresse e-mail ou mot de passe incorrect.</h2>";
    }
}

?>


<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="assets/css/style.css">
    <title>Vite et Gourmand</title>
</head>
<body>
    <header>
        <nav>
            <ul>
                <li><a href="index.php">Accueil</a></li>
                <li><a href="contact.php">Contact</a></li>
                <li><a href="inscription.php">Inscription</a></li>
                <li><a href="connexion.php">Connexion</a></li>
            </ul>
        </nav>
    </header>

    <main>
        <form action="connexion.php" method="POST">
            <input type="mail" name="email" placeholder="Adresse e-mail" required>
            <input type="password" name="password" placeholder="Mot de passe" required>
            <button type="submit">Connexion</button>
        </form>
        <a href="#">Mot de passe oublié ?</a>
    </main>

    <footer>
        <p>&copy; 2026 Vite et Gourmand. Tous droits réservés.</p>
    </footer>