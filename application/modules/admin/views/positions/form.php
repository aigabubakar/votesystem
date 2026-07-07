<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h3 class="mb-1 text-dark fw-bold"><?= $page_title ?></h3>
        <p class="text-muted mb-0">For: <strong><?= html_escape($election['title']) ?></strong></p>
    </div>
    <a href="<?= site_url('admin/positions/' . $election['id']) ?>" class="btn btn-outline-secondary">
        <i class="fa-solid fa-arrow-left me-2"></i> Back
    </a>
</div>

<div class="card max-w-600">
    <div class="card-body">
        <?= form_open($action) ?>
            
            <div class="mb-3">
                <label class="form-label">Position Title <span class="text-danger">*</span></label>
                <input type="text" name="title" class="form-control" value="<?= set_value('title', $position['title'] ?? '') ?>" required placeholder="e.g. President, Secretary General">
                <?= form_error('title', '<div class="invalid-feedback d-block">', '</div>') ?>
            </div>

            <div class="mb-3">
                <label class="form-label">Description (Optional)</label>
                <textarea name="description" class="form-control" rows="2"><?= set_value('description', $position['description'] ?? '') ?></textarea>
            </div>

            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label">Max Votes <span class="text-danger">*</span></label>
                    <input type="number" name="max_votes" class="form-control" value="<?= set_value('max_votes', $position['max_votes'] ?? 1) ?>" min="1" required>
                    <div class="form-text">How many candidates can a voter choose? Usually 1.</div>
                    <?= form_error('max_votes', '<div class="invalid-feedback d-block">', '</div>') ?>
                </div>
                <div class="col-md-6 mb-4">
                    <label class="form-label">Display Order</label>
                    <input type="number" name="sort_order" class="form-control" value="<?= set_value('sort_order', $position['sort_order'] ?? 0) ?>">
                    <div class="form-text">Lower numbers display first on the ballot.</div>
                </div>
            </div>

            <div class="text-end border-top pt-3">
                <button type="submit" class="btn btn-primary px-4">
                    <i class="fa-solid fa-save me-2"></i> Save Position
                </button>
            </div>

        <?= form_close() ?>
    </div>
</div>
