<?php
$host = 'localhost';
$db   = 'c2881399_cierres';
$user = 'c2881399_cierres';
$pass = 'PIvubafi71';

$conn = new mysqli($host, $user, $pass, $db);

if ($conn->connect_error) {
    die("Error crítico de conexión: " . $conn->connect_error);
}

// Soporte para caracteres especiales (tildes, eñes, emojis)
$conn->set_charset("utf8mb4");
?>