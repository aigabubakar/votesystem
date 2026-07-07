<div class="d-flex justify-content-between align-items-center mb-4">
    <h3 class="mb-0 text-dark fw-bold"><?= $page_title ?></h3>
    <a href="<?= site_url('admin/departments') ?>" class="btn btn-outline-secondary">
        <i class="fa-solid fa-arrow-left me-2"></i> Back
    </a>
</div>

<div class="card max-w-600">
    <div class="card-body">
        <?= form_open($action) ?>
            
            <div class="mb-3">
                <label class="form-label">Department Name <span class="text-danger">*</span></label>
                <input type="text" name="name" class="form-control" value="<?= set_value('name', $department['name'] ?? '') ?>" required placeholder="e.g. Computer Science">
                <?= form_error('name', '<div class="invalid-feedback d-block">', '</div>') ?>
            </div>

            <div class="mb-4">
                <label class="form-label">Department Code <span class="text-danger">*</span></label>
                <input type="text" name="code" class="form-control" value="<?= set_value('code', $department['code'] ?? '') ?>" required placeholder="e.g. CSC">
                <div class="form-text">A short abbreviation.</div>
                <?= form_error('code', '<div class="invalid-feedback d-block">', '</div>') ?>
            </div>

            <div class="text-end border-top pt-3">
                <button type="submit" class="btn btn-primary px-4">
                    <i class="fa-solid fa-save me-2"></i> Save Department
                </button>
            </div>

        <?= form_close() ?>
    </div>
</div>
