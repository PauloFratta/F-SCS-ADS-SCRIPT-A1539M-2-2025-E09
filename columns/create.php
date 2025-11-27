<?php
session_start();
require_once '../config/db.php';

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_SESSION['user_id'])) {
    $title = htmlspecialchars(trim($_POST['title']));
    $user_id = $_SESSION['user_id'];

    if (!empty($title)) {
        try {
            // Insere a nova coluna
            $stmt = $pdo->prepare("INSERT INTO board_columns (user_id, title) VALUES (:user_id, :title)");
            $stmt->execute(['user_id' => $user_id, 'title' => $title]);

            header("Location: ../index.php?section=kanban&success=column_created");
            exit;
        } catch (PDOException $e) {
            header("Location: ../index.php?section=kanban&error=create_failed");
            exit;
        }
    }
}
header("Location: ../index.php?section=kanban");
exit;
?>