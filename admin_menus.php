<?php  
require_once 'includes/host.php';
session_start();

// Vérification des droits d'accès
if (!isset($_SESSION['user_role_id']) || $_SESSION['user_role_id'] != 1) {
    header("Location: index.php");
    exit();
}


$themes = $pdo -> query("SELECT * FROM theme ORDER BY libelle ASC")->fetchAll(PDO::FETCH_ASSOC);
$regimes = $pdo -> query("SELECT * FROM regime ORDER BY libelle ASC")->fetchAll(PDO::FETCH_ASSOC);
$plats = $pdo -> query("SELECT plat_id, titre_plat, photo FROM plat ORDER BY titre_plat ASC")->fetchAll(PDO::FETCH_ASSOC);

// Traitement du formulaire d'ajout de menu
if (!empty($_POST)) {
    $titre = $_POST['titre'];
    $description = $_POST['description'];
    $theme_id = ($_POST['theme_id']) ?? null;
    $regime_id = ($_POST['regime_id']) ?? null;
    $nb_pers_min = ($_POST['nb_pers_min']) ?? 1; // Valeur par défaut à 1 si non renseigné (pour éviter les erreurs de base de données)
    $prix = ($_POST['prix_par_personne']) ?? 0;
    $stock = ($_POST['quantite_restante']) ?? 0;
    $conditions = ($_POST['conditions']) ?? '';

    $plats_inclus = $_POST['plat_inclus'] ?? [];

    // Insertion du menu dans la base de données
    if (!empty($titre) && $theme_id !== null && $regime_id !== null) {

        $insertMenu = $pdo->prepare("INSERT INTO menu (titre, description, theme_id, regime_id, nombre_personne_minimum, prix_par_personne, quantite_restante, conditions)
                                VALUES (:titre, :description, :theme_id, :regime_id, :nombre_personne_minimum, :prix_par_personne, :quantite_restante, :conditions)");
        $insertMenu->execute([
            'titre' => $titre,
            'description' => $description,
            'theme_id' => $theme_id,
            'regime_id' => $regime_id,
            'nombre_personne_minimum' => $nb_pers_min,
            'prix_par_personne' => $prix,
            'quantite_restante' => $stock,
            'conditions' => $conditions
        ]);

        // Récupérer l'ID du menu nouvellement créé
        $nouveau_menu_id = $pdo->lastInsertId();

        // Insérer les plats inclus dans le menu
        if (!empty($plats_inclus)) {
            $insertMenuPlat= $pdo->prepare("INSERT INTO menu_plat (menu_id, plat_id) VALUES (:menu_id, :plat_id)");

            foreach ($plats_inclus as $plat_id) {
                $insertMenuPlat->execute([
                    'menu_id' => $nouveau_menu_id,
                    'plat_id' => $plat_id
                ]);
            }
        }

        // Redirection après l'ajout du menu
        $_SESSION['message_succes'] = "Menu ajouté avec succès !";
        header("Location: admin_menus.php");
        exit();

    } else {
        $_SESSION['message_erreur'] = "Veuillez remplir tous les champs obligatoires (Titre, Thème, Régime).";
        header("Location: admin_menus.php");
        exit();
    }
}

        // Requête pour afficher les menus avec leurs thèmes et régimes associés ainsi que prix et nbr personne min

        $sqlAffichage = "SELECT 
        m.menu_id,
        m.titre,
        th.libelle AS theme_nom,
        m.nombre_personne_minimum,
        m.prix_par_personne,
        m.quantite_restante,
        r.libelle AS regime_nom,
        COUNT(mp.plat_id) AS nb_plats,
        GROUP_CONCAT(p.titre_plat SEPARATOR ' <br>• ') AS liste_plats   /*Réunis tout en 1 ligne avec séparation*/

        FROM menu m    /*Equivalent à FROM menu AS m */
        LEFT JOIN theme th ON m.theme_id = th.theme_id      
        LEFT JOIN regime r ON m.regime_id = r.regime_id
        LEFT JOIN menu_plat mp ON m.menu_id = mp.menu_id
        LEFT JOIN plat p ON mp.plat_id = p.plat_id

        GROUP BY m.menu_id
        ORDER BY m.titre ASC";
        
        $queryListeMenus = $pdo->query($sqlAffichage);
        $liste_menus = $queryListeMenus->fetchAll(PDO::FETCH_ASSOC);

?>


<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="style.css">
    <title>Gestion de menu</title>
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
        <section>
            <?php if (isset($_SESSION['message_succes'])): ?>
                <h4 style="color: green; background: #e8f5e9; padding: 10px; border-radius: 5px;">
                <?= $_SESSION['message_succes'] ?></h4>

                <?php unset($_SESSION['message_succes']); // On efface le message pour qu'il ne reste pas à l'infini ?>
            <?php endif; ?>

            <?php if (isset($erreur)): ?>
                <h4 style="color: red; background: #ffebee; padding: 10px; border-radius: 5px;">
                <?= $erreur ?>
                </h4>
            <?php endif; ?>


            <h2>Ajouter un nouveau menu</h2>

            <form action="admin_menus.php" method="post">

                <label for="titre">Titre</label>
                <input type="text" name="titre" id="titre" placeholder="Ex: Menu de Noël..." required>

                <label for="description">Description</label>
                <textarea name="description" id="description" placeholder="Ex: Présentation du menu..."></textarea>

                <label for="conditions">Conditions particulières :</label>
                <textarea name="conditions" id="conditions" placeholder="Ex: Commander 48h à l'avance, conservation..."></textarea>


                <label for="theme_id">Thème :</label>
                <select name="theme_id" id="theme_id">
                    <option value="">-- Sélectionnez un thème --</option>
                    <?php foreach($themes as $theme): ?>
                        <option value="<?= $theme['theme_id'] ?>"><?= htmlspecialchars($theme['libelle']) ?></option>
                    <?php endforeach; ?>
                </select>
    
                <label for="regime_id">Régime :</label>
                <select name="regime_id" id="regime_id">
                    <option value="">-- Sélectionnez un régime --</option>
                    <?php foreach($regimes as $regime): ?>
                        <option value="<?= $regime['regime_id'] ?>"><?= htmlspecialchars($regime['libelle']) ?></option>
                    <?php endforeach; ?>
                </select>

                <label for="nb_pers_min">Nombre de personnes minimal :</label>
                <input type="number" name="nb_pers_min" id="nb_pers_min" min="1" value="1" required>

                <label for="prix">Prix (pour le nb de pers. min) :</label>
                <input type="number" id="prix" name="prix_par_personne" step="0.01" placeholder="0.00" required>

                <label for="stock">Stock disponible :</label>
                <input type="number" id="stock" name="quantite_restante" min="0" placeholder="Ex: 50" required>

    
                <h4>Séléctionnez les plats pour ce menu :</h4>
                <div style="max-height: 200px; overflow-y: auto; border: 1px solid #ccc; padding: 10px;">
                    <?php foreach ($plats as $plat): ?>
                        <label style="display: flex; align-items: center; margin-bottom: 10px;">
                            <input type="checkbox" name="plat_inclus[]" value="<?= $plat['plat_id'] ?>" alt="<?= htmlspecialchars($plat['titre_plat']) ?>">

                            <?php if (!empty($plat['photo'])): ?>
                                <img src="uploads/plats/<?= htmlspecialchars($plat['photo']) ?>" alt="<?= htmlspecialchars($plat['titre_plat']) ?>"
                                style="width: 40px; height: 40px; object-fit: cover; margin-top: 5px; margin-left: 10px; border-radius: 5px; border: 1px solid #ccc;">

                            <?php else: ?>
                                <div style="width: 40px; height: 40px; background-color: #e0e0e0; border-radius: 5px; border: 1px solid #ccc; margin-top: 5px; margin-left: 10px; display: flex; align-items: center; justify-content: center; text-align: center; font-size: 10px; color: #888;"> Aucune photo</div>
                            <?php endif; ?>

                            <span style="font-weight: bold; font-size: 16px; margin-left: 10px;"><?= htmlspecialchars($plat['titre_plat']) ?></span>

                        </label>
                    <?php endforeach; ?>
                </div>

                <br>
                <button type="submit">Enregistrer le menu</button>
            </form>
        </section>

        <hr style="margin: 40px 0;"> <section>
        <h2>Liste des menus enregistrés</h2>
        
        <table border="1" style="width: 100%; text-align: left; border-collapse: collapse;">
            <thead>
                <tr style="background-color: #f2f2f2;">
                    <th style="padding: 10px;">Nom du menu</th>
                    <th style="padding: 10px;">Thème</th>
                    <th style="padding: 10px;">Plats inclus</th>
                    <th style="padding: 10px;">Régimes</th>
                    <th style="padding: 10px;">Nombre personne Min</th>
                    <th style="padding: 10px;">Prix par personne</th>
                    <th style="padding: 10px;">Quantités restantes</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($liste_menus)): ?>
                    <tr>
                        <td colspan="4" style="padding: 10px; text-align: center;">Aucun menu n'a été ajouté pour le moment.</td>
                    </tr>
                            
                <?php else: ?>
                    <?php foreach ($liste_menus as $menu): ?>
                        <tr>
                            <td style="padding: 10px;"><?= htmlspecialchars($menu['titre']) ?></td>
                            <td style="padding: 10px;"><?= htmlspecialchars($menu['theme_nom']) ?></td>
                            <td style="padding: 10px;">
                                <?php if ($menu['nb_plats'] > 0): ?>
                                    <details style="cursor: pointer;">
                                        <summary style="font-weight: bold; color: #007BFF;">
                                            <?= htmlspecialchars($menu['nb_plats']) ?> plat(s)
                                        </summary>
                                        <div style="margin-top: 8px; font-size: 0.9em; color: #555; padding-left: 10px; border-left: 2px solid #007BFF;">
                                            • <?= $menu['liste_plats'] //Affichage des plats ?> 
                                        </div>
                                    </details>
                                <?php else: ?>
                            <span style="color: #999; font-style: italic;">Aucun plat</span>
                        <?php endif; ?>
                    </td>
                            <td style="padding: 10px;"><?= htmlspecialchars($menu['regime_nom'] ?? 'Aucun') ?></td>
                            <td style="padding: 10px;"><?= htmlspecialchars($menu['nombre_personne_minimum']) ?></td>
                            <td style="padding: 10px;"><?= htmlspecialchars($menu['prix_par_personne']) ?></td>
                            <td style="padding: 10px;"><?= htmlspecialchars($menu['quantite_restante']) ?></td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </main>
</body>
</html>