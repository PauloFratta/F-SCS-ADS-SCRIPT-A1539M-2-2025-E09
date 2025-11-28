<?php
// config/db.php

$host = 'sql207.infinityfree.com';
$dbname = 'if0_40547603_tasker';
$username = 'if0_40547603';
$password = 'koguAvA1yTU';

try {
    // Criação da conexão usando PDO
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);

    // Configura para lançar erros em caso de falha
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

} catch (PDOException $e) {
    die("Erro na conexão com o banco de dados. Verifique as credenciais.");
}
?>