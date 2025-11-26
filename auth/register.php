<?php
session_start();
require_once '../config/db.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = htmlspecialchars(trim($_POST['name']));
    $email = filter_var(trim($_POST['email']), FILTER_SANITIZE_EMAIL);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];

    if (empty($name) || empty($email) || empty($password) || empty($confirm_password)) {
        $_SESSION['error'] = "Por favor, preencha todos os campos.";
        header("Location: ../index.php?section=register");
        exit;
    }

    if ($password !== $confirm_password) {
        $_SESSION['error'] = "As senhas não coincidem.";
        header("Location: ../index.php?section=register");
        exit;
    }

    $stmt = $pdo->prepare("SELECT id FROM users WHERE email = :email");
    $stmt->execute(['email' => $email]);

    if ($stmt->rowCount() > 0) {
        $_SESSION['error'] = "Este e-mail já está cadastrado.";
        header("Location: ../index.php?section=register");
        exit;
    }

    $password_hash = password_hash($password, PASSWORD_DEFAULT);

    try {
        $stmt = $pdo->prepare("INSERT INTO users (name, email, password) VALUES (:name, :email, :password)");
        $stmt->execute([
            'name' => $name,
            'email' => $email,
            'password' => $password_hash
        ]);

        $_SESSION['success'] = "Cadastro realizado com sucesso! Faça login.";
        header("Location: ../index.php?section=login");
        exit;

    } catch (PDOException $e) {
        $_SESSION['error'] = "Erro ao cadastrar: " . $e->getMessage();
        header("Location: ../index.php?section=register");
        exit;
    }
} else {
    header("Location: ../index.php");
    exit;
}
?>