<section id="dashboard" class="dashboard" style="display: none;">
    <div class="container">
        <div class="dashboard-header">
            <h2>Meu Dashboard</h2>
        </div>
        <div class="dashboard-grid">

            <div class="dashboard-card">
                <h3>📊 Visão Geral</h3>
                <div style="display: flex; justify-content: space-around; margin-top: 20px; text-align: center;">
                    <div>
                        <div style="font-size: 2rem; font-weight: bold; color: var(--danger);"><?php echo $stats['todo']; ?></div>
                        <div style="font-size: 0.9rem; color: #777;">A Fazer</div>
                    </div>
                    <div>
                        <div style="font-size: 2rem; font-weight: bold; color: var(--warning);"><?php echo $stats['progress']; ?></div>
                        <div style="font-size: 0.9rem; color: #777;">Em Andamento</div>
                    </div>
                    <div>
                        <div style="font-size: 2rem; font-weight: bold; color: var(--success);"><?php echo $stats['done']; ?></div>
                        <div style="font-size: 0.9rem; color: #777;">Concluídas</div>
                    </div>
                </div>
                <div style="margin-top: 20px; padding-top: 20px; border-top: 1px solid #eee; text-align: center; color: #555;">
                    Total: <strong><?php echo $stats['total']; ?></strong> tarefas
                </div>
            </div>

            <div class="dashboard-card">
                <h3>📅 Próximas Entregas</h3>
                <?php if (empty($upcoming_tasks)): ?>
                    <div style="text-align: center; color: #999; padding: 30px;">Tudo em dia!</div>
                <?php else: ?>
                    <ul class="task-list">
                        <?php foreach ($upcoming_tasks as $task): ?>
                            <li class="task-item">
                                <div class="task-info">
                                    <h4 style="margin-bottom: 2px;"><?php echo htmlspecialchars($task['title']); ?></h4>
                                    <div class="task-meta">
                                        <span style="color: var(--primary);">Vence: <?php echo date('d/m', strtotime($task['deadline'])); ?></span>
                                    </div>
                                </div>
                                <span class="task-priority" style="background-color: #eee; color: #555; font-size: 0.7rem; padding: 2px 6px; border-radius: 4px;">
                                    <?php echo ucfirst($task['priority']); ?>
                                </span>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                <?php endif; ?>
            </div>

            <div class="dashboard-card">
                <h3>⚠️ Atenção (Atrasadas)</h3>
                <?php if (empty($overdue_tasks)): ?>
                    <div style="text-align: center; color: var(--success); padding: 30px;">
                        <span style="font-size: 2rem;">🎉</span><br>Nenhuma pendência!
                    </div>
                <?php else: ?>
                    <ul class="task-list">
                        <?php foreach ($overdue_tasks as $task): ?>
                            <li class="task-item" style="border-left: 3px solid var(--danger); padding-left: 10px;">
                                <div class="task-info">
                                    <h4 style="color: var(--danger);"><?php echo htmlspecialchars($task['title']); ?></h4>
                                    <div class="task-meta">
                                        <span>Venceu: <?php echo date('d/m', strtotime($task['deadline'])); ?></span>
                                    </div>
                                </div>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                <?php endif; ?>
            </div>

        </div>
    </div>
</section>