<?php
session_start();
require_once '../config/db.php';

if (!isset($_SESSION['user_id']) || $_SERVER["REQUEST_METHOD"] != "POST") {
    header("Location: ../index.php");
    exit;
}

$id = filter_var($_POST['id'], FILTER_VALIDATE_INT);
$title = htmlspecialchars(trim($_POST['title']));
$user_id = $_SESSION['user_id'];

if ($id && !empty($title)) {
    try {
        $stmt = $pdo->prepare("UPDATE board_columns SET title = :title WHERE id = :id AND user_id = :user_id");
        $stmt->execute(['title' => $title, 'id' => $id, 'user_id' => $user_id]);

        header("Location: ../index.php?section=kanban&success=column_updated");
        exit;
    } catch (PDOException $e) {
        header("Location: ../index.php?section=kanban&error=update_failed");
        exit;
    }
}

header("Location: ../index.php?section=kanban");
exit;
?>