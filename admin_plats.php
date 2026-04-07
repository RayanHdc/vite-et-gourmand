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

            
            // On sauvegarde le message dans la session
            $_SESSION['message_succes'] = "Plat ajouté avec succès !";
            
            // On redirige la page vers elle-même pour vider le formulaire (PRG)
            header("Location: admin_plats.php");
            exit();
        } else {
            echo "<h3>Veuillez remplir tous les champs du formulaire.</h3>";
        }
    }

        // Requête pour afficher les plats avec leurs types et allergènes associés

        $sqlAffichage = "SELECT 
        p.titre_plat, 
        t.libelle AS type_nom, /* type de plat */
        GROUP_CONCAT(a.libelle SEPARATOR ', ') AS liste_allergenes     /* liste des allergènes  */

        FROM plat p    /*Equivalent à FROM plat AS p */
        LEFT JOIN type_plat t ON p.type_id = t.type_id      /*LEFT JOIN pour inclure les plats sans type*/
        LEFT JOIN plat_allergene pa ON p.plat_id = pa.plat_id
        LEFT JOIN allergene a ON pa.allergene_id = a.allergene_id

        GROUP BY p.plat_id      /* Groupement par plat pour éviter les doublons dans l'affichage */
        ORDER BY p.titre_plat ASC
        ";
        
        $queryListePlats = $pdo->query($sqlAffichage);
        $liste_plats = $queryListePlats->fetchAll(PDO::FETCH_ASSOC);
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
        <?php if (isset($_SESSION['message_succes'])): ?>
            <h3 style="color: green;"><?= $_SESSION['message_succes'] ?></h3>
            <?php unset($_SESSION['message_succes']); // On efface le message pour qu'il disparaisse à la prochaine actualisation ?>
        <?php endif; ?>

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


    <hr style="margin: 40px 0;"> <section>
        <h2>Liste des plats enregistrés</h2>
        
        <table border="1" style="width: 100%; text-align: left; border-collapse: collapse;">
            <thead>
                <tr style="background-color: #f2f2f2;">
                    <th style="padding: 10px;">Nom du plat</th>
                    <th style="padding: 10px;">Type</th>
                    <th style="padding: 10px;">Allergènes</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($liste_plats)): ?>
                    <tr>
                        <td colspan="3" style="padding: 10px; text-align: center;">Aucun plat n'a été ajouté pour le moment.</td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($liste_plats as $plat): ?>
                        <tr>
                            <td style="padding: 10px;"><?= htmlspecialchars($plat['titre_plat']) ?></td>
                            <td style="padding: 10px;"><?= htmlspecialchars($plat['type_nom']) ?></td>
                            <td style="padding: 10px;"><?= htmlspecialchars($plat['liste_allergenes'] ?? 'Aucun') ?></td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </section>                    


    <p>Utilisez les liens ci-dessus pour gérer les différentes sections du site.</p>
</body>
</html>