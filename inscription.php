<?php
?>


<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Inscription - Vite & Gourmand</title>
</head>
<body>
    <form method=POST action="inscription.php">
        <input type="text" name="nom" placeholder="Nom" required>
        <input type="text" name="prenom" placeholder="Prénom" required>
        <input type="email" name="email" placeholder="Email" required>
        <input type="password" name="password" placeholder="Mot de passe" required>
        <input type="password" name="confirm_password" placeholder="Confirmer le mot de passe" required>
        <input type="tel" name="telephone" placeholder="Numéro de téléphone" required>
        <input type="text" name="addresse_postale" placeholder="Adresse postale (N°, Rue et Code Postal)" required>
        <input type="text" name="ville" placeholder="Ville" required>
        <input type="text" name="pays" placeholder="Pays" required>
        <button type="submit">S'inscrire</button>
    </form>
</body>
</html>