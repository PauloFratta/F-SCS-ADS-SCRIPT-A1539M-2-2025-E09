<?php
// config/db.php

$host = 'MySQL Hostname';
$dbname = 'Database Name';
$username = 'MySQL Username';
$password = 'MySQL Password';

try {
    // Criação da conexão usando PDO
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);

    // Configura para lançar erros em caso de falha
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

} catch (PDOException $e) {
    die("Erro na conexão com o banco de dados. Verifique as credenciais.");
}
?>