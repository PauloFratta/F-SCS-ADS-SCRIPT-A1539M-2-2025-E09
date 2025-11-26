<?php
session_start();
require_once '../config/db.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = filter_var(trim($_POST['email']), FILTER_SANITIZE_EMAIL);
    $password = $_POST['password'];

    if (empty($email) || empty($password)) {
        $_SESSION['error'] = "Preencha todos os campos.";
        header("Location: ../index.php?section=login");
        exit;
    }

    try {
        // Busca o usuário pelo email
        $stmt = $pdo->prepare("SELECT id, name, password FROM users WHERE email = :email");
        $stmt->execute(['email' => $email]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        // Verifica se usuário existe E se a senha bate com o hash
        if ($user && password_verify($password, $user['password'])) {

            // SUCESSO: Salva dados na sessão
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_name'] = $user['name'];

            // Redireciona para o Dashboard
            header("Location: ../index.php?section=dashboard");
            exit;
        } else {
            $_SESSION['error'] = "E-mail ou senha incorretos.";
            header("Location: ../index.php?section=login");
            exit;
        }

    } catch (PDOException $e) {
        $_SESSION['error'] = "Erro no sistema.";
        header("Location: ../index.php?section=login");
        exit;
    }
} else {
    header("Location: ../index.php");
    exit;
}
?>