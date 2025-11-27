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
        header("Location: ../index.php?section=kanban&error=empty_fields");
        exit;
    }

    try {
        // 1. Busca a primeira coluna disponível do usuário para ser a coluna padrão
        $stmtCol = $pdo->prepare("SELECT id FROM board_columns WHERE user_id = ? ORDER BY id ASC LIMIT 1");
        $stmtCol->execute([$user_id]);
        $firstColumnId = $stmtCol->fetchColumn();

        // Se não existir nenhuma coluna, impede a criação (segurança)
        if (!$firstColumnId) {
            header("Location: ../index.php?section=kanban&error=no_columns_found");
            exit;
        }

        // 2. Insere a tarefa vinculada a essa coluna (column_id)
        // Nota: O campo 'status' antigo pode ser mantido como 'todo' por compatibilidade ou ignorado se o banco permitir
        $stmt = $pdo->prepare("INSERT INTO tasks (user_id, title, description, project_name, deadline, priority, status, column_id) VALUES (:user_id, :title, :description, :project, :deadline, :priority, 'todo', :column_id)");

        $stmt->execute([
            'user_id' => $user_id,
            'title' => $title,
            'description' => $description,
            'project' => $project,
            'deadline' => $deadline,
            'priority' => $priority,
            'column_id' => $firstColumnId
        ]);

        header("Location: ../index.php?section=kanban&success=task_created");
        exit;

    } catch (PDOException $e) {
        // Em produção, registre o erro em log
        // echo $e->getMessage(); exit; // Descomente para debugar se der erro
        header("Location: ../index.php?section=kanban&error=db_error");
        exit;
    }
} else {
    header("Location: ../index.php");
    exit;
}
?>