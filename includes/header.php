<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>TasKer - Gerenciamento de Tarefas</title>
    <link rel="stylesheet" href="styles/globals.css" />
    <style>
        .column-actions { opacity: 0.3; transition: opacity 0.2s; cursor: pointer; font-size: 1.1rem; margin-right: 8px; }
        .column-header:hover .column-actions { opacity: 1; }
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