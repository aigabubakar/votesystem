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

    <div class="voter-wrapper">
        <!-- Navbar -->
        <nav class="voter-navbar">
            <div class="voter-navbar-brand">
                <div class="brand-icon">
                    <i class="fa-solid fa-check-to-slot"></i>
                </div>
                <h5><?= $site_name ?></h5>
            </div>

            <!-- Hamburger (Mobile) -->
            <button class="voter-hamburger" id="voter-toggle">
                <i class="fa-solid fa-bars"></i>
            </button>

            <!-- Nav Links -->
            <ul class="voter-nav" id="voter-nav">
                <li>
                    <a href="<?= site_url('voter') ?>" class="<?= (isset($active_nav) && $active_nav === 'dashboard') ? 'active' : '' ?>">
                        <i class="fa-solid fa-house"></i> Dashboard
                    </a>
                </li>
                <li>
                    <a href="<?= site_url('voter/results') ?>" class="<?= (isset($active_nav) && $active_nav === 'results') ? 'active' : '' ?>">
                        <i class="fa-solid fa-square-poll-vertical"></i> Results
                    </a>
                </li>
                <li class="nav-divider"></li>
                <li>
                    <a href="<?= site_url('voter/profile') ?>" class="<?= (isset($active_nav) && $active_nav === 'profile') ? 'active' : '' ?>">
                        <i class="fa-solid fa-user"></i> My Profile
                    </a>
                </li>
                <li>
                    <a href="<?= site_url('logout') ?>" class="text-danger btn-confirm" data-message="Are you sure you want to log out?">
                        <i class="fa-solid fa-arrow-right-from-bracket"></i> Logout
                    </a>
                </li>
            </ul>
        </nav>

        <!-- Main Content -->
        <main class="voter-content">
            <!-- Flash Messages -->
            <?php $this->load->view('partials/flash_messages'); ?>

            <!-- View Content -->
            <?php $this->load->view($content_view); ?>
        </main>

        <!-- Footer -->
        <footer class="voter-footer">
            &copy; <?= date('Y') ?> <?= $site_name ?> - <?= $institution_name ?>. 
            <span class="ms-2">Developed by <a href="https://stackhubs.com.ng" target="_blank" class="text-decoration-none fw-bold text-dark">Stackhubs</a>.</span>
        </footer>
    </div>

    <!-- Support Widget -->
    <?php $this->load->view('partials/support_widget'); ?>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="<?= base_url('assets/js/app.js') ?>"></script>
</body>
</html>
