<?php
session_start();

require_once 'controllers/dashboard_controller.php';

require_once 'includes/header.php';

require_once 'views/home.php';
require_once 'views/features.php';
require_once 'views/dashboard.php';
require_once 'views/kanban.php';

require_once 'views/modals.php';

require_once 'includes/footer.php';
?>