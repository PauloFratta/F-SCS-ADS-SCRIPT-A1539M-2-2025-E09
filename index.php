<?php
session_start();
require_once 'config/db.php';

// Variáveis para o Kanban e Dashboard
$columns = [];
$tasks_by_column = [];
$stats = ['total' => 0, 'todo' => 0, 'progress' => 0, 'done' => 0];
$overdue_tasks = [];
$upcoming_tasks = [];

if (isset($_SESSION['user_id'])) {
    $user_id = $_SESSION['user_id'];

    try {
        // --- 1. LÓGICA DE MIGRAÇÃO (Cria colunas padrão se não existirem) ---
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

        // --- 2. BUSCA E REORDENAÇÃO DE COLUNAS ---
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

        // --- 3. BUSCA TAREFAS E GERA ESTATÍSTICAS ---
        $stmt = $pdo->prepare("SELECT * FROM tasks WHERE user_id = :user_id ORDER BY deadline ASC");
        $stmt->execute(['user_id' => $user_id]);
        $all_tasks = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $today = date('Y-m-d');

        foreach ($all_tasks as $task) {
            if ($task['column_id']) {
                $tasks_by_column[$task['column_id']][] = $task;
            }

            // Estatísticas
            $stats['total']++;
            $current_col_name = $col_names[$task['column_id']] ?? '';

            if ($current_col_name == 'A Fazer') $stats['todo']++;
            elseif ($current_col_name == 'Concluído') $stats['done']++;
            else $stats['progress']++;

            if ($current_col_name != 'Concluído') {
                if ($task['deadline'] < $today) {
                    $overdue_tasks[] = $task;
                } elseif ($task['deadline'] >= $today) {
                    if (count($upcoming_tasks) < 5) $upcoming_tasks[] = $task;
                }
            }
        }

    } catch (PDOException $e) {
        // Silêncio em produção
    }
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>TasKer - Gerenciamento de Tarefas</title>
    <link rel="stylesheet" href="styles/globals.css" />
    <style>
        /* Estilo para o botão de editar coluna */
        .column-actions {
            opacity: 0.3;
            transition: opacity 0.2s;
            cursor: pointer;
            font-size: 1.1rem;
            margin-right: 8px;
        }
        .column-header:hover .column-actions {
            opacity: 1;
        }
    </style>
</head>
<body>
    <header>
        <div class="container">
            <nav class="navbar">
                <div class="logo">Tas<span>Ker</span></div>
                <ul class="nav-links">
                    <li><a href="#" onclick="showSection('home')">Início</a></li>
                    <li><a href="#" onclick="showSection('features')">Funcionalidades</a></li>
                    <li><a href="#" onclick="showSection('dashboard')">Dashboard</a></li>
                    <li><a href="#" onclick="showSection('kanban')">Projetos</a></li>
                </ul>
                <div class="auth-buttons">
                    <?php if(isset($_SESSION['user_id'])): ?>
                        <div style="display: flex; align-items: center; gap: 15px;">
                            <span style="font-weight: 600; color: var(--dark);">
                                Olá, <?php echo htmlspecialchars(explode(' ', $_SESSION['user_name'])[0]); ?>
                            </span>
                            <a href="auth/logout.php" class="btn btn-outline" style="border-color: var(--danger); color: var(--danger);">Sair</a>
                        </div>
                    <?php else: ?>
                        <button class="btn btn-outline" onclick="showSection('login')">Entrar</button>
                        <button class="btn btn-primary" onclick="showSection('register')">Cadastrar</button>
                    <?php endif; ?>
                </div>
            </nav>
        </div>
    </header>

    <section id="home" class="hero">
        <div class="container">
            <h1>Organize suas tarefas, aumente sua produtividade</h1>
            <p>TasKer é uma plataforma web centralizada e intuitiva para o gerenciamento de tarefas e projetos.</p>
            <?php if(!isset($_SESSION['user_id'])): ?>
                <button class="btn btn-primary" onclick="showSection('register')">Comece Agora</button>
            <?php else: ?>
                <button class="btn btn-primary" onclick="showSection('kanban')">Ir para Projetos</button>
            <?php endif; ?>
        </div>
    </section>

    <section id="features" class="features" style="display: none;">
        <div class="container">
            <div class="section-title">
                <h2>Como o TasKer pode ajudar você</h2>
                <p>Nossa plataforma foi desenvolvida para atender às necessidades de diferentes perfis</p>
            </div>
            <div class="features-grid">
                <div class="feature-card">
                    <div class="feature-icon">📚</div>
                    <h3>Para Estudantes</h3>
                    <p>Organize cronogramas de estudo, datas de entrega de trabalhos e atividades em grupo.</p>
                </div>
                <div class="feature-card">
                    <div class="feature-icon">💼</div>
                    <h3>Para Freelancers</h3>
                    <p>Gerencie múltiplos projetos e clientes simultaneamente com controle claro de prazos.</p>
                </div>
                <div class="feature-card">
                    <div class="feature-icon">👥</div>
                    <h3>Para Equipes</h3>
                    <p>Delegue tarefas, visualize o progresso da equipe e mantenha o alinhamento.</p>
                </div>
            </div>
        </div>
    </section>

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

    <div id="login" class="auth-form-container" style="display: none;">
        <form class="auth-form" action="auth/login.php" method="POST"><h2>Entrar</h2><div class="form-group"><label>E-mail</label><input type="email" name="email" required></div><div class="form-group"><label>Senha</label><input type="password" name="password" required></div><button class="auth-submit">Entrar</button><div class="auth-switch">Não tem conta? <a href="#" onclick="showSection('register')">Cadastre-se</a></div></form>
    </div>
    <div id="register" class="auth-form-container" style="display: none;">
        <form class="auth-form" action="auth/register.php" method="POST"><h2>Cadastro</h2><div class="form-group"><label>Nome</label><input type="text" name="name" required></div><div class="form-group"><label>E-mail</label><input type="email" name="email" required></div><div class="form-group"><label>Senha</label><input type="password" name="password" required></div><div class="form-group"><label>Confirmar</label><input type="password" name="confirm_password" required></div><button class="auth-submit">Cadastrar</button><div class="auth-switch">Já tem conta? <a href="#" onclick="showSection('login')">Entre</a></div></form>
    </div>

    <div id="columnModal" class="modal" style="display: none;">
        <div class="modal-content"><div class="modal-header"><h2>Nova Coluna</h2><span class="close-modal" onclick="closeColumnModal()">&times;</span></div><form action="columns/create.php" method="POST"><div class="form-group"><label>Nome da Coluna</label><input type="text" name="title" required placeholder="Ex: Revisão"></div><button type="submit" class="btn btn-primary" style="width: 100%;">Criar</button></form></div>
    </div>

    <div id="editColumnModal" class="modal" style="display: none;">
        <div class="modal-content">
            <div class="modal-header"><h2>Editar Coluna</h2><span class="close-modal" onclick="closeEditColumnModal()">&times;</span></div>
            <form action="columns/update.php" method="POST">
                <input type="hidden" id="edit-col-id" name="id">
                <div class="form-group"><label>Nome da Coluna</label><input type="text" id="edit-col-title" name="title" required></div>
                <div style="display: flex; gap: 10px; margin-top: 15px;">
                    <button type="submit" class="btn btn-primary" style="flex: 1;">Salvar</button>
                    <a id="delete-col-btn" href="#" class="btn" style="background-color: #e74c3c; color: white; padding: 10px;">Excluir</a>
                </div>
            </form>
        </div>
    </div>

    <div id="taskModal" class="modal" style="display: none;">
        <div class="modal-content"><div class="modal-header"><h2>Nova Tarefa</h2><span class="close-modal" onclick="closeTaskModal()">&times;</span></div><form action="tasks/create.php" method="POST"><div class="form-group"><label>Título</label><input type="text" name="title" required></div><div class="form-group"><label>Descrição</label><textarea name="description" rows="3"></textarea></div><div class="form-group" style="display: flex; gap: 15px;"><div style="flex: 1;"><label>Prazo</label><input type="date" name="deadline" required></div><div style="flex: 1;"><label>Prioridade</label><select name="priority" style="width:100%"><option value="low">Baixa</option><option value="medium">Média</option><option value="high">Alta</option></select></div></div><button type="submit" class="btn btn-primary" style="width: 100%; margin-top: 15px;">Salvar</button></form></div>
    </div>

    <div id="editTaskModal" class="modal" style="display: none;">
        <div class="modal-content"><div class="modal-header"><h2>Detalhes</h2><span class="close-modal" onclick="closeEditModal()">&times;</span></div><form action="tasks/update.php" method="POST"><input type="hidden" id="edit-id" name="id"><div class="form-group"><label>Título</label><input type="text" id="edit-title" name="title" required></div><div class="form-group"><label>Descrição</label><textarea id="edit-desc" name="description" rows="3"></textarea></div><div class="form-group" style="display: flex; gap: 15px;"><div style="flex: 1;"><label>Prazo</label><input type="date" id="edit-deadline" name="deadline" required></div><div style="flex: 1;"><label>Prioridade</label><select id="edit-priority" name="priority" style="width:100%"><option value="low">Baixa</option><option value="medium">Média</option><option value="high">Alta</option></select></div></div><div class="form-group"><label>Mover para:</label><select id="edit-status" name="status" style="width: 100%; padding: 10px;"><?php foreach($columns as $col): ?><option value="<?php echo $col['id']; ?>"><?php echo htmlspecialchars($col['title']); ?></option><?php endforeach; ?></select></div><div style="display: flex; gap: 10px; margin-top: 15px;"><button type="submit" class="btn btn-primary" style="flex: 1;">Salvar</button><a id="delete-btn" href="#" class="btn" style="background-color: #e74c3c; color: white; padding: 10px;">Excluir</a></div></form></div>
    </div>

    <footer>
        <div class="container">
            <div class="footer-content">
                <div class="footer-column"><h3>TasKer</h3><p>Uma plataforma web para gerenciamento de tarefas e projetos.</p></div>
                <div class="footer-column"><h3>Links Rápidos</h3><ul class="footer-links"><li><a href="#" onclick="showSection('home')">Início</a></li><li><a href="#" onclick="showSection('features')">Funcionalidades</a></li><li><a href="#" onclick="showSection('dashboard')">Dashboard</a></li><li><a href="#" onclick="showSection('kanban')">Projetos</a></li></ul></div>
                <div class="footer-column"><h3>Contato</h3><ul class="footer-links"><li><a href="mailto:contato@tasker.com">contato@tasker.com</a></li><li><a href="#">Política de Privacidade</a></li><li><a href="#">Termos de Uso</a></li></ul></div>
            </div>
            <div class="copyright"><p>&copy; 2025 TasKer. Todos os direitos reservados.</p></div>
        </div>
    </footer>

    <script>
        function showSection(sectionId) {
            ['home','features','dashboard','kanban','login','register'].forEach(id => document.getElementById(id).style.display = 'none');
            document.getElementById(sectionId).style.display = 'block';
            window.scrollTo(0, 0);
        }

        function openTaskModal() { document.getElementById('taskModal').style.display = 'flex'; }
        function closeTaskModal() { document.getElementById('taskModal').style.display = 'none'; }

        function openColumnModal() { document.getElementById('columnModal').style.display = 'flex'; }
        function closeColumnModal() { document.getElementById('columnModal').style.display = 'none'; }

        // Funções para Editar Coluna
        function openEditColumnModal(id, title) {
            document.getElementById('edit-col-id').value = id;
            document.getElementById('edit-col-title').value = title;
            document.getElementById('delete-col-btn').href = 'columns/delete.php?id=' + id;
            document.getElementById('editColumnModal').style.display = 'flex';
        }
        function closeEditColumnModal() { document.getElementById('editColumnModal').style.display = 'none'; }

        function openEditModal(task) {
            document.getElementById('edit-id').value = task.id;
            document.getElementById('edit-title').value = task.title;
            document.getElementById('edit-desc').value = task.description;
            document.getElementById('edit-deadline').value = task.deadline;
            document.getElementById('edit-priority').value = task.priority;
            document.getElementById('edit-status').value = task.column_id || task.current_column_id;
            document.getElementById('delete-btn').href = 'tasks/delete.php?id=' + task.id;
            document.getElementById('editTaskModal').style.display = 'flex';
        }
        function closeEditModal() { document.getElementById('editTaskModal').style.display = 'none'; }

        window.onclick = function(e) { if(e.target.className === 'modal') e.target.style.display = 'none'; }

        <?php if(isset($_GET['section'])): ?>
            showSection('<?php echo htmlspecialchars($_GET['section']); ?>');
        <?php else: ?>
            showSection('home');
        <?php endif; ?>
    </script>
</body>
</html>