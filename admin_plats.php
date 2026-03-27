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

// Récupération des allergènes pour les afficher dans l'administration
$query = $pdo->query("SELECT * FROM allergene ORDER BY libelle ASC");
$allergene = $query->fetchAll(PDO::FETCH_ASSOC);

// Récupération des types de plats pour les afficher dans l'administration
$queryTypes = $pdo->query("SELECT * FROM type_plat ORDER BY libelle ASC");
$types = $queryTypes->fetchAll(PDO::FETCH_ASSOC);


// Traitement du formulaire d'ajout de plat
    if(!empty($_POST)) {
        $titre_plat = $_POST['titre_plat'] ?? '';
        $type_id = $_POST['type_id'] ?? null;
        $allergene_choisis = $_POST['allergene'] ?? [];
    }

?>

<!DOCTYPE html> 
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin - Gestion du site</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <h1>Administration du site</h1>
    <nav>
        <ul>
            <li><a href="admin_users.php">Gérer les utilisateurs</a></li>
            <li><a href="admin_content.php">Gérer le contenu</a></li>
            <li><a href="admin_settings.php">Gérer les paramètres</a></li>
        </ul>
    </nav>
    <section>
        <h2>Allergènes</h2>
        <ul>
            <?php foreach ($allergene as $all): ?>
                <li>
                    <label>
                        <input type="checkbox" name="allergene[]" value="<?= $all['allergene_id'] ?>">
                        <?= $all['libelle'] ?>
                    </label>
                </li>
            <?php endforeach; ?>
        </ul>
    </section>

    <p>Utilisez les liens ci-dessus pour gérer les différentes sections du site.</p>
</body>
</html>