<?php
session_start();
require_once '../config/db.php';

if (isset($_GET['id']) && isset($_SESSION['user_id'])) {
    $id = $_GET['id'];
    $user_id = $_SESSION['user_id'];

    try {
        // 1. Deleta as tarefas dessa coluna primeiro (Limpeza)
        $stmtTasks = $pdo->prepare("DELETE FROM tasks WHERE column_id = :id AND user_id = :user_id");
        $stmtTasks->execute(['id' => $id, 'user_id' => $user_id]);

        // 2. Deleta a coluna
        $stmtCol = $pdo->prepare("DELETE FROM board_columns WHERE id = :id AND user_id = :user_id");
        $stmtCol->execute(['id' => $id, 'user_id' => $user_id]);

        header("Location: ../index.php?section=kanban&success=column_deleted");
        exit;
    } catch (PDOException $e) {
        header("Location: ../index.php?section=kanban&error=delete_failed");
        exit;
    }
} else {
    header("Location: ../index.php");
    exit;
}
?>