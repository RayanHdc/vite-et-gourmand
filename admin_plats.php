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
$queryTypes = $pdo->query("SELECT * FROM type_plat ORDER BY type_id ASC");
$types = $queryTypes->fetchAll(PDO::FETCH_ASSOC);


// Traitement du formulaire d'ajout de plat
    if(!empty($_POST)) {
        $titre_plat = $_POST['titre_plat'] ?? '';
        $type_id = $_POST['type_id'] ?? null;
        $allergene_choisis = $_POST['allergene'] ?? [];

        // Validation des données
        if ($titre_plat !== '' && $type_id !== null) {
            

            // Insertion du plat dans la base de données
            $insertPlat = $pdo->prepare("INSERT INTO plat (titre_plat, type_id) VALUES (:titre, :type)");
            $insertPlat->execute([
                ':titre' => $titre_plat,
                ':type' => $type_id
            ]);

            $nouveau_plat_id = $pdo->lastInsertId();

            // Insertion des allergènes associés au plat
            if (!empty($allergene_choisis)) {
                $insertAllergenePlat = $pdo->prepare("INSERT INTO plat_allergene (plat_id, allergene_id) VALUES (:plat, :allergene)");

                foreach ($allergene_choisis as $allergene_id) {
                    $insertAllergenePlat->execute([
                        ':plat' => $nouveau_plat_id,
                        ':allergene' => $allergene_id
                    ]);
                }
            }
            echo "<h3>Plat ajouté avec succès !</h3>";
        } else {
            echo "<h3>Veuillez remplir tous les champs du formulaire.</h3>";
        }
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
            <li><a href="admin.php">Gérer les menus</a></li>
            <li><a href="admin_plats.php">Gérer les plats</a></li>
            <li><a href="admin_settings.php">Gérer les paramètres</a></li>
        </ul>
    </nav>

    <section>
        <h2>Ajouter un nouveau plat</h2>

        <form method="POST" action="admin_plats.php">
            <div>
                <label for="titre_plat">Nom du plat :</label>
                <input type="text" id="titre_plat" name="titre_plat" required>
            </div>
            <br>

            <div>
                <label for="type_id">Type de plat :</label>
                <select id="type_id" name="type_id" required>
                    <option value="">-- Sélectionnez un type --</option>
                    <?php foreach ($types as $t): ?>
                        <option value="<?= $t['type_id'] ?>"><?= $t['libelle'] ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <br>

            <div>
                <h3>Allergènes :</h3>

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
            </div>
            <br><br>
            
            <button type="submit">Enregistrer le plat</button>
        </form>
    </section>



    <p>Utilisez les liens ci-dessus pour gérer les différentes sections du site.</p>
</body>
</html>