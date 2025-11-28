<section id="kanban" class="kanban-board" style="display: none;">
    <div class="container">
        <div class="board-header">
            <h2>Projeto: Site Corporativo</h2>
            <div style="display: flex; gap: 10px;">
                <button class="btn btn-outline" onclick="openColumnModal()">+ Nova Coluna</button>
                <button class="btn btn-primary" onclick="openTaskModal()">Nova Tarefa</button>
            </div>
        </div>

        <div class="board-columns">
            <?php if (empty($columns)): ?>
                <p>Carregando quadro...</p>
            <?php else: ?>
                <?php foreach ($columns as $col): ?>
                    <?php
                        $extraClass = 'progress';
                        if($col['title'] == 'A Fazer') $extraClass = 'todo';
                        elseif($col['title'] == 'Concluído') $extraClass = 'done';
                    ?>

                    <div class="column <?php echo $extraClass; ?>">
                        <div class="column-header">
                            <div style="display: flex; align-items: center; gap: 10px; width: 100%;">
                                <span class="column-actions" onclick="openEditColumnModal(<?php echo $col['id']; ?>, '<?php echo addslashes($col['title']); ?>')">✎</span>
                                <h3 class="column-title" style="flex: 1;"><?php echo htmlspecialchars($col['title']); ?></h3>
                            </div>
                            <div class="task-count">
                                <?php echo isset($tasks_by_column[$col['id']]) ? count($tasks_by_column[$col['id']]) : 0; ?>
                            </div>
                        </div>

                        <?php
                        if (isset($tasks_by_column[$col['id']])) {
                            foreach ($tasks_by_column[$col['id']] as $task) {
                                $task['current_column_id'] = $col['id'];
                                $taskJson = htmlspecialchars(json_encode($task), ENT_QUOTES, "UTF-8");

                                echo "<div class='task-card' onclick='openEditModal($taskJson)'>";

                                if ($col['title'] == 'Concluído') {
                                    echo "<h4 style='text-decoration: line-through; color: #888; margin-bottom: 0;'>" . htmlspecialchars($task['title']) . "</h4>";
                                    echo "<div class='task-card-footer' style='margin-top: 10px;'>";
                                    echo "<span>Concluído</span>";
                                    echo "<span style='color: var(--success); font-weight: bold; font-size: 1.2rem;'>✓</span>";
                                    echo "</div>";
                                } else {
                                    $priorityColor = '#2ecc71';
                                    if($task['priority'] == 'medium') $priorityColor = '#f39c12';
                                    if($task['priority'] == 'high') $priorityColor = '#e74c3c';

                                    echo "<h4>" . htmlspecialchars($task['title']) . "</h4>";
                                    echo "<p>" . htmlspecialchars($task['description']) . "</p>";
                                    echo "<div class='task-card-footer'>";
                                    echo "<span>Vence: " . date('d/m', strtotime($task['deadline'])) . "</span>";
                                    echo "<span style='font-size: 0.7rem; padding: 2px 8px; border-radius: 4px; color: white; background-color: $priorityColor;'>" . ucfirst($task['priority']) . "</span>";
                                    echo "</div>";
                                }

                                echo "</div>";
                            }
                        }
                        ?>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>
</section>