<?php
/**
 * Page de déconnexion
 * NFE114 - Système de cabinet médical
 */

require_once 'includes/config.php';

// Détruire la session
session_start();
session_unset();
session_destroy();

// Rediriger vers la page d'accueil
header("Location: index.php");
exit();
?>
