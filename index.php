<?php 
require_once 'includes/host.php';

$sql = "SELECT * FROM regime";

$requete = $pdo->query($sql);

$regimes = $requete->fetchAll(PDO::FETCH_ASSOC);

var_dump($regimes);