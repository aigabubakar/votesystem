<?php
$alert_types = [
    'success' => 'alert-success',
    'error'   => 'alert-danger',
    'info'    => 'alert-info',
    'warning' => 'alert-warning'
];

foreach ($alert_types as $key => $class) {
    if ($this->session->flashdata($key)) {
        $icon = 'fa-circle-info';
        if ($key === 'success') $icon = 'fa-circle-check';
        if ($key === 'error')   $icon = 'fa-triangle-exclamation';
        if ($key === 'warning') $icon = 'fa-bell';
?>
        <div class="alert <?= $class ?> alert-dismissible fade show" role="alert">
            <i class="fa-solid <?= $icon ?> me-2"></i>
            <?= $this->session->flashdata($key) ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
<?php
    }
}
?>
