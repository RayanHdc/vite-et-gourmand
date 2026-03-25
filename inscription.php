<?php
require_once 'includes/host.php';

//Vérification de la soumission du formulaire
if (!empty($_POST)) {

    $mdp = $_POST['password'];
    $mdp_confirm = $_POST['confirm_password'];

    //Hashage du mot de passe et insertion dans la base de données
    if ($mdp === $mdp_confirm) {

        $mdp_hash = password_hash($mdp, PASSWORD_DEFAULT);

        $sql = "INSERT INTO utilisateur (nom, prenom, email, password, telephone, adresse_postale, ville, pays, role_id) VALUES (:nom, :prenom, :email, :password, :telephone, :adresse_postale, :ville, :pays, :role_id)";

        $requete = $pdo->prepare($sql);

        $requete->execute([
            ':nom' => $_POST['nom'],
            ':prenom' => $_POST['prenom'],
            ':email' => $_POST['email'],
            ':password' => $mdp_hash,
            ':telephone' => $_POST['telephone'],
            ':adresse_postale' => $_POST['adresse_postale'],
            ':ville' => $_POST['ville'],
            ':pays' => $_POST['pays'],
            ':role_id' => 3
        ]);

        echo "<h2>Inscription réussie !</h2>";

    } else {
        echo "<h2>Les mots de passe ne correspondent pas.</h2>";
    }
}
?>


<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Inscription - Vite & Gourmand</title>
</head>
<body>
    <form method="POST" action="inscription.php">
        <input type="text" name="nom" placeholder="Nom" required>
        <input type="text" name="prenom" placeholder="Prénom" required>
        <input type="email" name="email" placeholder="Email" required>
        <input type="password" name="password" placeholder="Mot de passe" required>
        <input type="password" name="confirm_password" placeholder="Confirmer le mot de passe" required>
        <input type="tel" name="telephone" placeholder="Numéro de téléphone" required>
        <input type="text" name="adresse_postale" placeholder="Adresse postale (N°, Rue et Code Postal)" required>
        <input type="text" name="ville" placeholder="Ville" required>
        <input type="text" name="pays" placeholder="Pays" required>
        <button type="submit">S'inscrire</button>
    </form>
</body>
</html>