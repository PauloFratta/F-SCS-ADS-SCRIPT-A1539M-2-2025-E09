<?php
session_start();
require_once '../config/db.php';

if (!isset($_SESSION['user_id']) || $_SERVER["REQUEST_METHOD"] != "POST") {
    header("Location: ../index.php");
    exit;
}

$id = $_POST['id'];
$title = htmlspecialchars(trim($_POST['title']));
$description = htmlspecialchars(trim($_POST['description']));
$deadline = $_POST['deadline'];
$priority = $_POST['priority'];
$column_id = $_POST['status']; // O select agora enviará o ID da coluna
$user_id = $_SESSION['user_id'];

try {
    // Atualiza usando column_id
    $stmt = $pdo->prepare("UPDATE tasks SET title = :title, description = :description, deadline = :deadline, priority = :priority, column_id = :column_id WHERE id = :id AND user_id = :user_id");

    $stmt->execute([
        'title' => $title,
        'description' => $description,
        'deadline' => $deadline,
        'priority' => $priority,
        'column_id' => $column_id,
        'id' => $id,
        'user_id' => $user_id
    ]);

    header("Location: ../index.php?section=kanban&success=task_updated");
    exit;

} catch (PDOException $e) {
    header("Location: ../index.php?section=kanban&error=update_failed");
    exit;
}
?>