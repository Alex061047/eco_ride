<?php
session_start();
session_unset();
session_destroy();

// Supprime le cookie PHPSESSID
setcookie(session_name(), '', time() - 3600, '/');

header("Location: /"); // Redirection vers l'accueil
exit;
