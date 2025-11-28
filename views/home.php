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