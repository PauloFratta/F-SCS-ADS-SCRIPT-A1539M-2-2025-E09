<?php
session_start();
require_once '../config/db.php';

   if (isset($_GET['id']) && isset($_SESSION['user_id'])) {
        $id = $_GET['id'];
        $user_id = $_SESSION['user_id'];

        try {
            $stmt = $pdo->prepare("DELETE FROM tasks WHARE id = :id AND user_id = user_id");
            $stmt->execute(['id' => $id, 'user_id' => $user_id]);

            header("Location: ../index.php?section=kanban&success=task_deleted");
            exit;
        } catch (PDOException $e) {
            header("Location: ../index.php?section=kanban&error=delete_feiled");
            exit;
        }
    } else {
        header("Location: ../index.php");
        exit;
    }
?>