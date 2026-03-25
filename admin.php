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
            </ul>
        </nav>
    </header>

    <main>
        <section>
        <!-- Formulaire d'ajout de menu -->
    <h3>Ajouter un nouveau menu</h3>
    
    <form action="admin.php" method="POST">
        <label for="titre">Titre du menu :</label>
        <input type="text" id="titre" name="titre" placeholder="Ex: Menu de Noël..." required>

        <label for="description">Description :</label>
        <textarea id="description" name="description" placeholder="Présentation du menu..." required></textarea>

        <label for="theme">Thème :</label>
        <select id="theme" name="theme">
            <option value="classique">Classique</option>
            <option value="noel">Noël</option>
            <option value="paques">Pâques</option>
            <option value="anniversaire">Anniversaire</option>
            <option value="mariage">Mariage</option>
            <option value="evenement">Évènement personnalisé</option>
        </select>

        <label for="nb_pers_min">Nombre de personnes minimal :</label>
        <input type="number" id="nb_pers_min" name="nb_pers_min" min="1" value="1" required>

        <label for="prix">Prix (pour le nb de pers. min) :</label>
        <input type="number" id="prix" name="prix" step="0.01" placeholder="0.00" required>

        <label for="regime">Régime :</label>
        <select id="regime" name="regime">
            <option value="classique">Classique</option>
            <option value="vegetarien">Végétarien</option>
            <option value="vegan">Vegan</option>
        </select>

        <label for="stock">Stock disponible :</label>
        <input type="number" id="stock" name="stock" min="0" placeholder="Ex: 50" required>

        <button type="submit">Enregistrer le menu</button>
    </form>
</section>
    </main>

    <footer>
        <p>&copy; 2026 Vite et Gourmand. Tous droits réservés.</p>
    </footer>
</body>
</html>