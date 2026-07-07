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

    <div class="auth-wrapper">
        <div class="auth-card">
            
            <div class="auth-logo">
                <div class="auth-logo-icon">
                    <i class="fa-solid fa-check-to-slot"></i>
                </div>
                <h2><?= $site_name ?></h2>
                <p><?= $site_tagline ?></p>
            </div>

            <!-- Flash Messages -->
            <?php $this->load->view('partials/flash_messages'); ?>

            <!-- Auth Content -->
            <?php $this->load->view($content_view); ?>
            
            <div class="auth-footer">
                &copy; <?= date('Y') ?> <?= $institution_name ?>.<br>All rights reserved.<br>
                <small>Developed by <a href="https://stackhubs.com.ng" target="_blank" class="text-decoration-none fw-bold text-dark">Stackhubs</a>.</small>
            </div>

        </div>
    </div>

    <!-- Support Widget -->
    <?php $this->load->view('partials/support_widget'); ?>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="<?= base_url('assets/js/app.js') ?>"></script>
</body>
</html>
