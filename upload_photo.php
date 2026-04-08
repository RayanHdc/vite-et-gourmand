<?php
require_once 'includes/host.php';

if (isset($_FILES['photo']) && isset($_POST['plat_id'])) {

    $id_plat = $_POST['plat_id'];

    $nom_fichier = basename($_FILES['photo']['name']);

    $extension = strtolower(pathinfo($nom_fichier, PATHINFO_EXTENSION));

        // Mettre à jour la base de données avec le nom du fichier
        $sql = $pdo->prepare("SELECT titre_plat FROM plat WHERE plat_id = :id_plat");
        $sql->execute(['id_plat' => $id_plat]);
        $plat = $sql->fetch();


        if ($plat) {
        $titre_plat = $plat['titre_plat'];
        $nom_photo = uniqid('plat_') . '.' . preg_replace('/[^a-zA-Z0-9-]/', '_', $titre_plat) . '.' . $extension;

    } else {
        // Sécurité si le nom n'est pas trouvé
        $nom_photo = uniqid('plat_' . $id_plat . '_') . '.' . $extension;
    }

    $chemin_destination = 'uploads/plats/' . $nom_photo;

    // 4. sauvegarde de l'image dans le dossier avec le nouveau nom
    if (move_uploaded_file($_FILES['photo']['tmp_name'], $chemin_destination)) {
        
        // 5. On met à jour la base de données AVEC LE NOUVEAU NOM
        $sql = $pdo->prepare("UPDATE plat SET photo = :photo WHERE plat_id = :id_plat");
        $sql->execute(['photo' => $nom_photo, 'id_plat' => $id_plat]);
        
    }
}

header("Location: admin_plats.php");
exit();
?>