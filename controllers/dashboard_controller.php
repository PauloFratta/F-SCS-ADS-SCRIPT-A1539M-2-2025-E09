<?php
// controllers/dashboard_controller.php
require_once 'config/db.php'; // Caminho relativo será ajustado na index

$columns = [];
$tasks_by_column = [];
$stats = ['total' => 0, 'todo' => 0, 'progress' => 0, 'done' => 0];
$overdue_tasks = [];
$upcoming_tasks = [];

if (isset($_SESSION['user_id'])) {
    $user_id = $_SESSION['user_id'];

    try {
        // 1. LÓGICA DE MIGRAÇÃO
        $checkCols = $pdo->prepare("SELECT COUNT(*) FROM board_columns WHERE user_id = ?");
        $checkCols->execute([$user_id]);

        if ($checkCols->fetchColumn() == 0) {
            $defaults = [
                ['title' => 'A Fazer', 'old_status' => 'todo'],
                ['title' => 'Em Andamento', 'old_status' => 'progress'],
                ['title' => 'Concluído', 'old_status' => 'done']
            ];
            foreach ($defaults as $def) {
                $pdo->prepare("INSERT INTO board_columns (user_id, title) VALUES (?, ?)")->execute([$user_id, $def['title']]);
                $newColId = $pdo->lastInsertId();
                $pdo->prepare("UPDATE tasks SET column_id = ? WHERE status = ? AND user_id = ?")->execute([$newColId, $def['old_status'], $user_id]);
            }
        }

        // 2. BUSCA E REORDENAÇÃO DE COLUNAS
        $stmt = $pdo->prepare("SELECT * FROM board_columns WHERE user_id = :user_id ORDER BY id ASC");
        $stmt->execute(['user_id' => $user_id]);
        $raw_columns = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $col_names = [];
        $done_col = null;

        foreach ($raw_columns as $col) {
            $col_names[$col['id']] = $col['title'];
            if ($col['title'] === 'Concluído') {
                $done_col = $col;
            } else {
                $columns[] = $col;
            }
        }
        if ($done_col) {
            $columns[] = $done_col;
        }

        // 3. BUSCA TAREFAS E ESTATÍSTICAS
        $stmt = $pdo->prepare("SELECT * FROM tasks WHERE user_id = :user_id ORDER BY deadline ASC");
        $stmt->execute(['user_id' => $user_id]);
        $all_tasks = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $today = date('Y-m-d');

        foreach ($all_tasks as $task) {
            if ($task['column_id']) {
                $tasks_by_column[$task['column_id']][] = $task;
            }

            $stats['total']++;
            $current_col_name = $col_names[$task['column_id']] ?? '';

            if ($current_col_name == 'A Fazer') $stats['todo']++;
            elseif ($current_col_name == 'Concluído') $stats['done']++;
            else $stats['progress']++;

            if ($current_col_name != 'Concluído') {
                if ($task['deadline'] < $today) {
                    $overdue_tasks[] = $task;
                } elseif ($task['deadline'] >= $today) {
                    if (count($upcoming_tasks) < 5) {
                        $upcoming_tasks[] = $task;
                    }
                }
            }
        }

    } catch (PDOException $e) {
        // Log de erro (opcional)
    }
}
?>