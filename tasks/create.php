<?php
session_start();
require_once '../config/db.php';

// Verifica se o usuário está logado
if (!isset($_SESSION['user_id'])) {
    header("Location: ../index.php?section=login");
    exit;
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $title = htmlspecialchars(trim($_POST['title']));
    $description = htmlspecialchars(trim($_POST['description']));
    $project = htmlspecialchars(trim($_POST['project']));
    $deadline = $_POST['deadline'];
    $priority = $_POST['priority'];
    $user_id = $_SESSION['user_id'];

    if (empty($title) || empty($deadline)) {
        // Erro simples (idealmente usaríamos sessão para erro, como no login)
        header("Location: ../index.php?section=kanban&error=empty_fields");
        exit;
    }

    try {
        $stmt = $pdo->prepare("INSERT INTO tasks (user_id, title, description, project_name, deadline, priority, status) VALUES (:user_id, :title, :description, :project, :deadline, :priority, 'todo')");

        $stmt->execute([
            'user_id' => $user_id,
            'title' => $title,
            'description' => $description,
            'project' => $project,
            'deadline' => $deadline,
            'priority' => $priority
        ]);

        header("Location: ../index.php?section=kanban&success=task_created");
        exit;

    } catch (PDOException $e) {
        // Em produção, registre o erro em log
        header("Location: ../index.php?section=kanban&error=db_error");
        exit;
    }
} else {
    header("Location: ../index.php");
    exit;
}
?>