<?php
// config/db.php

$host = 'localhost';
$dbname = 'tasker_db';
$username = 'root'; // Padrão do XAMPP
$password = '';     // Padrão do XAMPP (vazio)

try {
    // Criação da conexão usando PDO (mais seguro)
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);

    // Configura para lançar erros em caso de falha
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

} catch (PDOException $e) {
    die("Erro na conexão com o banco de dados: " . $e->getMessage());
}
?>