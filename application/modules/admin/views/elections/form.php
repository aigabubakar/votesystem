<div class="d-flex justify-content-between align-items-center mb-4">
    <h3 class="mb-0 text-dark fw-bold"><?= $page_title ?></h3>
    <a href="<?= site_url('admin/elections') ?>" class="btn btn-outline-secondary">
        <i class="fa-solid fa-arrow-left me-2"></i> Back
    </a>
</div>

<div class="card max-w-800">
    <div class="card-body">
        <?= form_open($action) ?>
            
            <div class="mb-3">
                <label class="form-label">Election Title <span class="text-danger">*</span></label>
                <input type="text" name="title" class="form-control" value="<?= set_value('title', $election['title'] ?? '') ?>" required>
                <?= form_error('title', '<div class="invalid-feedback d-block">', '</div>') ?>
            </div>

            <div class="mb-3">
                <label class="form-label">Description</label>
                <textarea name="description" class="form-control" rows="3"><?= set_value('description', $election['description'] ?? '') ?></textarea>
            </div>

            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label">Academic Session <span class="text-danger">*</span></label>
                    <input type="text" name="session" class="form-control" value="<?= set_value('session', $election['session'] ?? date('Y').'/'.(date('Y')+1)) ?>" required placeholder="e.g. 2025/2026">
                    <?= form_error('session', '<div class="invalid-feedback d-block">', '</div>') ?>
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">Target Department</label>
                    <select name="department_id" class="form-select">
                        <option value="">All Departments (General Election)</option>
                        <?php foreach ($departments as $dept): ?>
                            <option value="<?= $dept['id'] ?>" <?= set_select('department_id', $dept['id'], isset($election) && $election['department_id'] == $dept['id']) ?>>
                                <?= $dept['name'] ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>

            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label">Start Date & Time <span class="text-danger">*</span></label>
                    <input type="datetime-local" name="start_date" class="form-control" value="<?= set_value('start_date', isset($election) ? date('Y-m-d\TH:i', strtotime($election['start_date'])) : '') ?>" required>
                    <?= form_error('start_date', '<div class="invalid-feedback d-block">', '</div>') ?>
                </div>
                <div class="col-md-6 mb-4">
                    <label class="form-label">End Date & Time <span class="text-danger">*</span></label>
                    <input type="datetime-local" name="end_date" class="form-control" value="<?= set_value('end_date', isset($election) ? date('Y-m-d\TH:i', strtotime($election['end_date'])) : '') ?>" required>
                    <?= form_error('end_date', '<div class="invalid-feedback d-block">', '</div>') ?>
                </div>
            </div>

            <div class="text-end border-top pt-3">
                <button type="submit" class="btn btn-primary px-4">
                    <i class="fa-solid fa-save me-2"></i> Save Election
                </button>
            </div>

        <?= form_close() ?>
    </div>
</div>
