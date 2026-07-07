<div class="d-flex justify-content-between align-items-center mb-4">
    <h3 class="mb-0 text-dark fw-bold"><?= $page_title ?></h3>
    <a href="<?= site_url('admin/candidates') ?>" class="btn btn-outline-secondary">
        <i class="fa-solid fa-arrow-left me-2"></i> Back
    </a>
</div>

<div class="card max-w-800">
    <div class="card-body">
        
        <!-- Step 1: Select Election -->
        <form action="<?= site_url('admin/candidates/create') ?>" method="post" id="election-select-form" class="no-loading mb-4 pb-4 border-bottom">
            <input type="hidden" name="<?= $this->security->get_csrf_token_name() ?>" value="<?= $this->security->get_csrf_hash() ?>">
            <label class="form-label fw-bold text-primary">1. Select Election</label>
            <div class="d-flex gap-2">
                <select name="election_id" class="form-select flex-grow-1" onchange="document.getElementById('election-select-form').submit()">
                    <option value="">-- Choose Election --</option>
                    <?php foreach ($elections as $e): ?>
                        <option value="<?= $e['id'] ?>" <?= ($selected_election == $e['id']) ? 'selected' : '' ?>>
                            <?= html_escape($e['title']) ?> (<?= $e['status'] ?>)
                        </option>
                    <?php endforeach; ?>
                </select>
                <noscript><button type="submit" class="btn btn-secondary">Go</button></noscript>
            </div>
        </form>

        <!-- Step 2: Candidate Details -->
        <?php if ($selected_election): ?>
            <?php if (empty($positions)): ?>
                <div class="alert alert-warning">This election has no positions created yet. Add positions first.</div>
            <?php else: ?>
                <?= form_open_multipart($action) ?>
                    <input type="hidden" name="election_id" value="<?= $selected_election ?>">
                    
                    <label class="form-label fw-bold text-primary">2. Candidate Details</label>

                    <div class="mb-3">
                        <label class="form-label">Select Position <span class="text-danger">*</span></label>
                        <select name="position_id" class="form-select" required>
                            <option value="">-- Choose Position --</option>
                            <?php foreach ($positions as $p): ?>
                                <option value="<?= $p['id'] ?>" <?= set_select('position_id', $p['id']) ?>>
                                    <?= html_escape($p['title']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                        <?= form_error('position_id', '<div class="invalid-feedback d-block">', '</div>') ?>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Select Student <span class="text-danger">*</span></label>
                        <select name="user_id" class="form-select" required>
                            <option value="">-- Choose Student --</option>
                            <?php foreach ($students as $s): ?>
                                <option value="<?= $s['id'] ?>" <?= set_select('user_id', $s['id']) ?>>
                                    <?= html_escape($s['first_name'] . ' ' . $s['last_name']) ?> (<?= $s['student_id'] ?>) - <?= $s['dept_name'] ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                        <?= form_error('user_id', '<div class="invalid-feedback d-block">', '</div>') ?>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Manifesto / Bio</label>
                        <textarea name="manifesto" class="form-control" rows="4" placeholder="Candidate's goals and background..."><?= set_value('manifesto') ?></textarea>
                    </div>

                    <div class="mb-4">
                        <label class="form-label">Candidate Photo</label>
                        <input type="file" name="photo" class="form-control" accept="image/jpeg, image/png, image/webp">
                        <div class="form-text">Max size: 2MB. Square image recommended.</div>
                    </div>

                    <div class="text-end border-top pt-3">
                        <button type="submit" class="btn btn-primary px-4">
                            <i class="fa-solid fa-save me-2"></i> Add Candidate
                        </button>
                    </div>
                <?= form_close() ?>
            <?php endif; ?>
        <?php else: ?>
            <div class="text-center py-4 text-muted">
                Please select an election from the dropdown above to continue.
            </div>
        <?php endif; ?>

    </div>
</div>
