<?php
require_once 'includes/host.php';

session_start();

// Vérification des droits d'accès
if (!isset($_SESSION['user_role_id']) || $_SESSION['user_role_id'] != 1) {
    header("Location: index.php");
    exit();
} else {
    echo "<h2>Bienvenue, " . $_SESSION['user_email'] . " !</h2>";
    echo "<p>Vous avez accès à l'administration du site.</p>";
}

?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="assets/css/style.css">
    <title>Admin - Vite et Gourmand</title>
</head>
<body>
    <header>
        <nav>
            <ul>
                <li><a href="index.php">Accueil</a></li>
                <li><a href="contact.php">Contact</a></li>
                <li><a href="inscription.php">Inscription</a></li>
                <li><a href="connexion.php">Connexion</a></li>
                <li><a href="admin_plats.php">Gérer les plats</a></li>
            </ul>
        </nav>
    </header>

    <main>
        
    </main>

    <footer>
        <p>&copy; 2026 Vite et Gourmand. Tous droits réservés.</p>
    </footer>
</body>
</html>