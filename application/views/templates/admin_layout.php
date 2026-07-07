<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= isset($page_title) ? $page_title : $site_name ?></title>
    
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Custom CSS -->
    <link rel="stylesheet" href="<?= base_url('assets/css/style.css') ?>">
</head>
<body>

    <div class="admin-wrapper">
        <!-- Sidebar Overlay (Mobile) -->
        <div class="sidebar-overlay" id="sidebar-overlay"></div>

        <!-- Sidebar -->
        <aside class="sidebar" id="sidebar">
            <div class="sidebar-brand">
                <div class="sidebar-brand-icon">
                    <i class="fa-solid fa-check-to-slot"></i>
                </div>
                <div>
                    <h4><?= $site_name ?></h4>
                    <small>Admin Portal</small>
                </div>
            </div>

            <ul class="sidebar-menu">
                <li class="sidebar-menu-label">Main Menu</li>
                <li>
                    <a href="<?= site_url('admin') ?>" class="<?= (isset($active_nav) && $active_nav === 'dashboard') ? 'active' : '' ?>">
                        <div class="nav-icon"><i class="fa-solid fa-chart-pie"></i></div>
                        <span>Dashboard</span>
                    </a>
                </li>
                <li>
                    <a href="<?= site_url('admin/elections') ?>" class="<?= (isset($active_nav) && $active_nav === 'elections') ? 'active' : '' ?>">
                        <div class="nav-icon"><i class="fa-solid fa-box-archive"></i></div>
                        <span>Elections</span>
                    </a>
                </li>
                <li>
                    <a href="<?= site_url('admin/candidates') ?>" class="<?= (isset($active_nav) && $active_nav === 'candidates') ? 'active' : '' ?>">
                        <div class="nav-icon"><i class="fa-solid fa-users"></i></div>
                        <span>Candidates</span>
                    </a>
                </li>
                <li>
                    <a href="<?= site_url('admin/students') ?>" class="<?= (isset($active_nav) && $active_nav === 'students') ? 'active' : '' ?>">
                        <div class="nav-icon"><i class="fa-solid fa-user-graduate"></i></div>
                        <span>Students</span>
                    </a>
                </li>
                <li>
                    <a href="<?= site_url('admin/departments') ?>" class="<?= (isset($active_nav) && $active_nav === 'departments') ? 'active' : '' ?>">
                        <div class="nav-icon"><i class="fa-solid fa-building"></i></div>
                        <span>Departments</span>
                    </a>
                </li>
                <li>
                    <a href="<?= site_url('admin/tokens') ?>" class="<?= (isset($active_nav) && $active_nav === 'tokens') ? 'active' : '' ?>">
                        <div class="nav-icon"><i class="fa-solid fa-key"></i></div>
                        <span>Tokens</span>
                    </a>
                </li>
                
                <li class="sidebar-menu-label">Reports</li>
                <li>
                    <a href="<?= site_url('admin/results') ?>" class="<?= (isset($active_nav) && $active_nav === 'results') ? 'active' : '' ?>">
                        <div class="nav-icon"><i class="fa-solid fa-square-poll-vertical"></i></div>
                        <span>Results</span>
                    </a>
                </li>
            </ul>

            <div class="sidebar-footer">
                <div class="user-info">
                    <div class="user-avatar">
                        <?= substr($current_user['name'], 0, 1) ?>
                    </div>
                    <div>
                        <div class="user-name"><?= $current_user['name'] ?></div>
                        <div class="user-role">Administrator</div>
                    </div>
                </div>
            </div>
        </aside>

        <!-- Main Content -->
        <main class="admin-main">
            <!-- Topbar -->
            <header class="topbar">
                <div class="topbar-left">
                    <button class="topbar-toggler" id="sidebar-toggle">
                        <i class="fa-solid fa-bars"></i>
                    </button>
                    <div class="topbar-title">
                        <?= isset($page_title) ? $page_title : 'Dashboard' ?>
                    </div>
                </div>
                <div class="topbar-right d-flex align-items-center">
                    <a href="<?= site_url('admin/profile') ?>" class="btn btn-sm btn-outline-primary me-3 rounded-pill px-3">
                        <i class="fa-solid fa-user me-1"></i>
                        <span class="d-none d-md-inline">Profile</span>
                    </a>
                    <a href="<?= site_url('logout') ?>" class="btn-logout btn-confirm" data-message="Are you sure you want to log out?">
                        <i class="fa-solid fa-arrow-right-from-bracket"></i>
                        <span class="d-none d-md-inline">Logout</span>
                    </a>
                </div>
            </header>

            <!-- Content Area -->
            <div class="admin-content">
                <!-- Flash Messages -->
                <?php $this->load->view('partials/flash_messages'); ?>

                <!-- View Content -->
                <?php $this->load->view($content_view); ?>
            </div>

            <footer class="admin-footer">
                <span>&copy; <?= date('Y') ?> <?= $site_name ?>. All rights reserved. Developed by <a href="https://stackhubs.com.ng" target="_blank" class="text-decoration-none fw-bold text-dark">Stackhubs</a>.</span>
            </footer>
        </main>
    </div>

    <!-- Support Widget -->
    <?php $this->load->view('partials/support_widget'); ?>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="<?= base_url('assets/js/app.js') ?>"></script>
</body>
</html>
