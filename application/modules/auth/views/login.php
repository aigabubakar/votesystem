<div class="mb-4 text-center">
    <h4 class="mb-1 text-dark">Welcome Back</h4>
    <p class="text-muted small">Please login to your account to continue</p>
</div>

<?= form_open('login', ['class' => 'no-loading']) ?>

    <div class="mb-3">
        <label for="student_id" class="form-label">Student / Matric ID</label>
        <div class="input-group">
            <span class="input-group-text bg-light border-end-0"><i class="fa-solid fa-id-card text-muted"></i></span>
            <input type="text" name="student_id" id="student_id" class="form-control border-start-0" value="<?= set_value('student_id') ?>" required autofocus placeholder="e.g. CSC/2021/001">
        </div>
        <?= form_error('student_id', '<div class="invalid-feedback d-block">', '</div>') ?>
    </div>

    <div class="mb-4">
        <label for="password" class="form-label">Password / Voting Token</label>
        <div class="input-group">
            <span class="input-group-text bg-light border-end-0"><i class="fa-solid fa-key text-muted"></i></span>
            <input type="password" name="password" id="password" class="form-control border-start-0" required placeholder="Token provided by admin">
        </div>
        <?= form_error('password', '<div class="invalid-feedback d-block">', '</div>') ?>
    </div>

    <div class="d-grid mb-3">
        <button type="submit" class="btn btn-primary btn-lg">Login to Portal</button>
    </div>

<?= form_close() ?>
