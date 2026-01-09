<?php
// db.php
$host = 'localhost'; // Cambia si es necesario por sus dstos
$dbname = 'bddatos';
$username = 'root'; // Tu usuario de MySQL
$password = ''; // Tu contraseña de MySQL
// Crear la conexión
try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    echo 'Error de conexión: ' . $e->getMessage();
    exit();
}
