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

    <script src="js/scripts.js"></script>

    <script>
        <?php if(isset($_GET['section'])): ?>
            showSection('<?php echo htmlspecialchars($_GET['section']); ?>');
        <?php else: ?>
            showSection('home');
        <?php endif; ?>
    </script>
</body>
</html>