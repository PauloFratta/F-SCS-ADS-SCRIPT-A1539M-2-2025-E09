<?php
session_start();
require_once 'config/db.php';

// Inicializa as variáveis para não dar erro se não tiver tarefas
$todo = [];
$progress = [];
$done = [];

// Se o usuário estiver logado, busca as tarefas dele
if (isset($_SESSION['user_id'])) {
    try {
        $user_id = $_SESSION['user_id'];
        // Busca tarefas ordenadas pela data de criação (mais novas primeiro)
        $stmt = $pdo->prepare("SELECT * FROM tasks WHERE user_id = :user_id ORDER BY created_at DESC");
        $stmt->execute(['user_id' => $user_id]);
        $tasks = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Separa as tarefas por status
        foreach ($tasks as $task) {
            if ($task['status'] == 'todo') {
                $todo[] = $task;
            } elseif ($task['status'] == 'progress') {
                $progress[] = $task;
            } elseif ($task['status'] == 'done') {
                $done[] = $task;
            }
        }
    } catch (PDOException $e) {
        // Em produção, trate o erro silenciosamente ou registre em log
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
                    <button class="btn btn-outline" onclick="showSection('login')">Entrar</button>
                    <button class="btn btn-primary" onclick="showSection('register')">Cadastrar</button>
                </div>
            </nav>
        </div>
    </header>

    <section id="home" class="hero">
        <div class="container">
            <h1>Organize suas tarefas, aumente sua produtividade</h1>
            <p>TasKer é uma plataforma web centralizada e intuitiva para o gerenciamento de tarefas e projetos, ajudando estudantes, freelancers e pequenas equipes a serem mais produtivos.</p>
            <button class="btn btn-primary" onclick="showSection('register')">Comece Agora</button>
        </div>
    </section>

    <section id="features" class="features" style="display: none;">
        <div class="container">
            <div class="section-title">
                <h2>Como o TasKer pode ajudar você</h2>
                <p>Nossa plataforma foi desenvolvida para atender às necessidades de diferentes perfis de usuários</p>
            </div>
            <div class="features-grid">
                <div class="feature-card">
                    <div class="feature-icon">📚</div>
                    <h3>Para Estudantes</h3>
                    <p>Organize cronogramas de estudo, datas de entrega de trabalhos e atividades em grupo. Nunca mais perca um prazo importante.</p>
                </div>
                <div class="feature-card">
                    <div class="feature-icon">💼</div>
                    <h3>Para Freelancers</h3>
                    <p>Gerencie múltiplos projetos e clientes simultaneamente com um controle claro de entregas e prazos.</p>
                </div>
                <div class="feature-card">
                    <div class="feature-icon">👥</div>
                    <h3>Para Equipes</h3>
                    <p>Delegue tarefas, visualize o progresso da equipe e mantenha o alinhamento sobre as responsabilidades.</p>
                </div>
            </div>
        </div>
    </section>

    <section id="dashboard" class="dashboard" style="display: none;">
        <div class="container">
            <div class="dashboard-header">
                <h2>Meu Dashboard</h2>
                <div class="user-info">
                    <div class="user-avatar">
                        <?php
                        // Pega as iniciais do nome logado ou mostra 'G' de Guest
                        echo isset($_SESSION['user_name']) ? strtoupper(substr($_SESSION['user_name'], 0, 2)) : 'G';
                        ?>
                    </div>
                    <span>
                        <?php
                        // Mostra o nome logado ou 'Visitante'
                        echo isset($_SESSION['user_name']) ? htmlspecialchars($_SESSION['user_name']) : 'Visitante';
                        ?>
                    </span>
                    <?php if(isset($_SESSION['user_id'])): ?>
                        <a href="auth/logout.php" style="margin-left: 10px; color: #e74c3c; text-decoration: none; font-size: 0.9rem;">Sair</a>
                    <?php endif; ?>
                </div>
            </div>
            <div class="dashboard-grid">
                <div class="dashboard-card">
                    <h3>📋 Minhas Tarefas</h3>
                    <ul class="task-list">
                        <li class="task-item">
                            <div class="task-info">
                                <h4>Finalizar relatório mensal</h4>
                                <div class="task-meta">
                                    <span>Projeto: Marketing</span>
                                    <span>Vence: 15/10</span>
                                </div>
                            </div>
                            <span class="task-priority priority-high">Alta</span>
                        </li>
                        <li class="task-item">
                            <div class="task-info">
                                <h4>Revisar design do site</h4>
                                <div class="task-meta">
                                    <span>Projeto: Site Corporativo</span>
                                    <span>Vence: 18/10</span>
                                </div>
                            </div>
                            <span class="task-priority priority-medium">Média</span>
                        </li>
                        <li class="task-item">
                            <div class="task-info">
                                <h4>Preparar apresentação</h4>
                                <div class="task-meta">
                                    <span>Projeto: Reunião Trimestral</span>
                                    <span>Vence: 20/10</span>
                                </div>
                            </div>
                            <span class="task-priority priority-low">Baixa</span>
                        </li>
                    </ul>
                </div>
                <div class="dashboard-card">
                    <h3>📅 Hoje</h3>
                    <ul class="task-list">
                        <li class="task-item">
                            <div class="task-info">
                                <h4>Reunião com a equipe</h4>
                                <div class="task-meta">
                                    <span>10:00 - 11:00</span>
                                </div>
                            </div>
                        </li>
                        <li class="task-item">
                            <div class="task-info">
                                <h4>Enviar proposta para cliente</h4>
                                <div class="task-meta">
                                    <span>Até 16:00</span>
                                </div>
                            </div>
                        </li>
                    </ul>
                </div>
                <div class="dashboard-card">
                    <h3>⚠️ Atrasadas</h3>
                    <ul class="task-list">
                        <li class="task-item">
                            <div class="task-info">
                                <h4>Atualizar documentação</h4>
                                <div class="task-meta">
                                    <span>Venceu: 10/10</span>
                                </div>
                            </div>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </section>

    <section id="kanban" class="kanban-board" style="display: none;">
        <div class="container">
            <div class="board-header">
                <h2>Projeto: Site Corporativo</h2>
                <button class="btn btn-primary" onclick="openTaskModal()">Nova Tarefa</button>
            </div>
            <div class="board-columns">

    <div class="column todo">
        <div class="column-header">
            <h3 class="column-title">A Fazer</h3>
            <div class="task-count"><?php echo count($todo); ?></div>
        </div>

        <?php if(empty($todo)): ?>
            <p style="text-align: center; color: #aaa; font-size: 0.9rem; margin-top: 20px;">Nenhuma tarefa aqui</p>
        <?php else: ?>
            <?php foreach($todo as $task): ?>
                <div class="task-card" draggable="true" onclick="editTask(<?php echo $task['id']; ?>)">
                    <h4><?php echo htmlspecialchars($task['title']); ?></h4>
                    <p><?php echo htmlspecialchars($task['description']); ?></p>
                    <div class="task-card-footer">
                        <span>Vence: <?php echo date('d/m', strtotime($task['deadline'])); ?></span>
                        <?php
                            $priorityColor = '#2ecc71'; // Verde (Low)
                            if($task['priority'] == 'medium') $priorityColor = '#f39c12'; // Laranja
                            if($task['priority'] == 'high') $priorityColor = '#e74c3c'; // Vermelho
                        ?>
                        <span style="font-size: 0.7rem; padding: 2px 6px; border-radius: 4px; color: white; background-color: <?php echo $priorityColor; ?>">
                            <?php
                                if($task['priority'] == 'low') echo 'Baixa';
                                elseif($task['priority'] == 'medium') echo 'Média';
                                else echo 'Alta';
                            ?>
                        </span>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>

    <div class="column progress">
        <div class="column-header">
            <h3 class="column-title">Em Andamento</h3>
            <div class="task-count"><?php echo count($progress); ?></div>
        </div>

        <?php foreach($progress as $task): ?>
            <div class="task-card" draggable="true" onclick="editTask(<?php echo $task['id']; ?>)">
                <h4><?php echo htmlspecialchars($task['title']); ?></h4>
                <p><?php echo htmlspecialchars($task['description']); ?></p>
                <div class="task-card-footer">
                    <span>Vence: <?php echo date('d/m', strtotime($task['deadline'])); ?></span>
                    <span style="font-size: 0.7rem; background-color: #eee; padding: 2px 5px; border-radius: 4px;">Andamento</span>
                </div>
            </div>
        <?php endforeach; ?>
    </div>

    <div class="column done">
        <div class="column-header">
            <h3 class="column-title">Concluído</h3>
            <div class="task-count"><?php echo count($done); ?></div>
        </div>

        <?php foreach($done as $task): ?>
            <div class="task-card" draggable="true" onclick="editTask(<?php echo $task['id']; ?>)">
                <h4 style="text-decoration: line-through; color: #888;"><?php echo htmlspecialchars($task['title']); ?></h4>
                <div class="task-card-footer">
                    <span>Concluído</span>
                    <span style="color: green;">✓</span>
                </div>
            </div>
        <?php endforeach; ?>
    </div>

</div>
        </div>
    </section>

    <div id="login" class="auth-form-container" style="display: none;">
        <form class="auth-form" action="auth/login.php" method="POST">
            <h2>Entrar na sua conta</h2>

            <?php if(isset($_SESSION['success'])): ?>
                <div style="background-color: #d4edda; color: #155724; padding: 10px; margin-bottom: 15px; border-radius: 4px; text-align: center;">
                    <?php echo $_SESSION['success']; unset($_SESSION['success']); ?>
                </div>
            <?php endif; ?>

            <?php if(isset($_SESSION['error']) && isset($_GET['section']) && $_GET['section'] == 'login'): ?>
                <div style="background-color: #ffdddd; color: #d32f2f; padding: 10px; margin-bottom: 15px; border-radius: 4px; text-align: center;">
                    <?php echo $_SESSION['error']; unset($_SESSION['error']); ?>
                </div>
            <?php endif; ?>

            <div class="form-group">
                <label for="login-email">E-mail</label>
                <input type="email" id="login-email" name="email" required>
            </div>
            <div class="form-group">
                <label for="login-password">Senha</label>
                <input type="password" id="login-password" name="password" required>
            </div>
            <button type="submit" class="auth-submit">Entrar</button>
            <div class="auth-switch">
                Não tem uma conta? <a href="#" onclick="showSection('register')">Cadastre-se</a>
            </div>
        </form>
    </div>

    <div id="register" class="auth-form-container" style="display: none;">
        <form class="auth-form" action="auth/register.php" method="POST">
            <h2>Criar uma conta</h2>

            <?php if(isset($_SESSION['error']) && isset($_GET['section']) && $_GET['section'] == 'register'): ?>
                <div style="background-color: #ffdddd; color: #d32f2f; padding: 10px; margin-bottom: 15px; border-radius: 4px; text-align: center;">
                    <?php echo $_SESSION['error']; unset($_SESSION['error']); ?>
                </div>
            <?php endif; ?>

            <div class="form-group">
                <label for="register-name">Nome completo</label>
                <input type="text" id="register-name" name="name" required>
            </div>
            <div class="form-group">
                <label for="register-email">E-mail</label>
                <input type="email" id="register-email" name="email" required>
            </div>
            <div class="form-group">
                <label for="register-password">Senha</label>
                <input type="password" id="register-password" name="password" required>
            </div>
            <div class="form-group">
                <label for="register-confirm">Confirmar senha</label>
                <input type="password" id="register-confirm" name="confirm_password" required>
            </div>
            <button type="submit" class="auth-submit">Cadastrar</button>
            <div class="auth-switch">
                Já tem uma conta? <a href="#" onclick="showSection('login')">Entre aqui</a>
            </div>
        </form>
    </div>

    <div id="taskModal" class="modal" style="display: none;">
        <div class="modal-content">
            <div class="modal-header">
                <h2>Nova Tarefa</h2>
                <span class="close-modal" onclick="closeTaskModal()">&times;</span>
            </div>
            <form action="tasks/create.php" method="POST">
                <div class="form-group">
                    <label for="task-title">Título da Tarefa</label>
                    <input type="text" id="task-title" name="title" required placeholder="Ex: Criar wireframe">
                </div>

                <div class="form-group">
                    <label for="task-desc">Descrição</label>
                    <textarea id="task-desc" name="description" rows="3" style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 4px;"></textarea>
                </div>

                <div class="form-group">
                    <label for="task-project">Projeto</label>
                    <input type="text" id="task-project" name="project" placeholder="Ex: Site Institucional">
                </div>

                <div class="form-group" style="display: flex; gap: 15px;">
                    <div style="flex: 1;">
                        <label for="task-deadline">Prazo</label>
                        <input type="date" id="task-deadline" name="deadline" required>
                    </div>
                    <div style="flex: 1;">
                        <label for="task-priority">Prioridade</label>
                        <select id="task-priority" name="priority" style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 4px;">
                            <option value="low">Baixa</option>
                            <option value="medium" selected>Média</option>
                            <option value="high">Alta</option>
                        </select>
                    </div>
                </div>

                <button type="submit" class="btn btn-primary" style="width: 100%; margin-top: 15px;">Salvar Tarefa</button>
            </form>
        </div>
    </div>

    <footer>
        <div class="container">
            <div class="footer-content">
                <div class="footer-column">
                    <h3>TasKer</h3>
                    <p>Uma plataforma web para gerenciamento de tarefas e projetos, desenvolvida para aumentar a produtividade de usuários individuais e pequenas equipes.</p>
                </div>
                <div class="footer-column">
                    <h3>Links Rápidos</h3>
                    <ul class="footer-links">
                        <li><a href="#" onclick="showSection('home')">Início</a></li>
                        <li><a href="#" onclick="showSection('features')">Funcionalidades</a></li>
                        <li><a href="#" onclick="showSection('dashboard')">Dashboard</a></li>
                        <li><a href="#" onclick="showSection('kanban')">Projetos</a></li>
                    </ul>
                </div>
                <div class="footer-column">
                    <h3>Contato</h3>
                    <ul class="footer-links">
                        <li><a href="mailto:contato@tasker.com">contato@tasker.com</a></li>
                        <li><a href="#">Política de Privacidade</a></li>
                        <li><a href="#">Termos de Uso</a></li>
                    </ul>
                </div>
            </div>
            <div class="copyright">
                <p>&copy; 2025 TasKer. Desenvolvido por Matheus Ribeiro De Sales Tine e Kauã Alves Genovez.</p>
            </div>
        </div>
    </footer>

    <script>
        // Função para mostrar apenas a seção selecionada
        function showSection(sectionId) {
            // Esconder todas as seções principais
            document.getElementById('home').style.display = 'none';
            document.getElementById('features').style.display = 'none';
            document.getElementById('dashboard').style.display = 'none';
            document.getElementById('kanban').style.display = 'none';

            // Esconder formulários de autenticação
            document.getElementById('login').style.display = 'none';
            document.getElementById('register').style.display = 'none';

            // Mostrar a seção selecionada
            if (sectionId === 'home') {
                document.getElementById('home').style.display = 'block';
            } else if (sectionId === 'features') {
                document.getElementById('features').style.display = 'block';
            } else if (sectionId === 'dashboard') {
                document.getElementById('dashboard').style.display = 'block';
            } else if (sectionId === 'kanban') {
                document.getElementById('kanban').style.display = 'block';
            } else if (sectionId === 'login') {
                document.getElementById('login').style.display = 'block';
            } else if (sectionId === 'register') {
                document.getElementById('register').style.display = 'block';
            }
            window.scrollTo(0, 0);
        }

        // Funções do Modal (ADICIONADO)
        function openTaskModal() {
            document.getElementById('taskModal').style.display = 'flex';
        }

        function closeTaskModal() {
            document.getElementById('taskModal').style.display = 'none';
        }

        // Fechar modal se clicar fora dele
        window.onclick = function(event) {
            var modal = document.getElementById('taskModal');
            if (event.target == modal) {
                modal.style.display = "none";
            }
        }

        document.addEventListener('DOMContentLoaded', function() {
            const taskCards = document.querySelectorAll('.task-card');
            const columns = document.querySelectorAll('.column');

            taskCards.forEach(card => {
                card.addEventListener('dragstart', () => {
                    card.classList.add('dragging');
                });

                card.addEventListener('dragend', () => {
                    card.classList.remove('dragging');
                });
            });

            columns.forEach(column => {
                column.addEventListener('dragover', e => {
                    e.preventDefault();
                    const afterElement = getDragAfterElement(column, e.clientY);
                    const draggable = document.querySelector('.dragging');
                    if (afterElement == null) {
                        column.appendChild(draggable);
                    } else {
                        column.insertBefore(draggable, afterElement);
                    }
                });
            });

            function getDragAfterElement(container, y) {
                const draggableElements = [...container.querySelectorAll('.task-card:not(.dragging)')];

                return draggableElements.reduce((closest, child) => {
                    const box = child.getBoundingClientRect();
                    const offset = y - box.top - box.height / 2;
                    if (offset < 0 && offset > closest.offset) {
                        return { offset: offset, element: child };
                    } else {
                        return closest;
                    }
                }, { offset: Number.NEGATIVE_INFINITY }).element;
            }
        });

        // Verificar se há uma seção especificada na URL e mantem o login após o reload da página
        <?php if(isset($_GET['section'])): ?>
            showSection('<?php echo htmlspecialchars($_GET['section']); ?>');
        <?php else: ?>
            showSection('home');
        <?php endif; ?>

        function editTask(taskId) {
    // Na próxima etapa vamos fazer isso abrir o modal com os dados!
    console.log("Clicou na tarefa ID: " + taskId);
    alert("Em breve: Editar tarefa " + taskId);
}
    </script>
</body>
</html>