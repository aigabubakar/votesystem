<div class="d-flex justify-content-between align-items-center mb-4">
    <h3 class="mb-0 text-dark fw-bold">My Profile</h3>
</div>

<div class="row">
    <div class="col-lg-8">
        <div class="card mb-4">
            <div class="card-body">
                <h5 class="card-title fw-bold text-primary mb-4 border-bottom pb-2">Personal Details</h5>
                
                <?= form_open('voter/profile/update') ?>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label text-muted small">First Name</label>
                            <div class="fw-bold"><?= html_escape($student['first_name']) ?></div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label text-muted small">Last Name</label>
                            <div class="fw-bold"><?= html_escape($student['last_name']) ?></div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label text-muted small">Matriculation / Student ID</label>
                            <div class="fw-bold"><?= html_escape($student['student_id']) ?></div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label text-muted small">Department & Level</label>
                            <div class="fw-bold"><?= html_escape($student['dept_name']) ?> (<?= $student['level'] ?>L)</div>
                        </div>
                    </div>
                    
                    <hr>

                    <div class="row mt-3">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Email Address</label>
                            <input type="email" name="email" class="form-control" value="<?= set_value('email', $student['email']) ?>" required>
                            <?= form_error('email', '<div class="invalid-feedback d-block">', '</div>') ?>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Phone Number</label>
                            <input type="text" name="phone" class="form-control" value="<?= set_value('phone', $student['phone']) ?>">
                        </div>
                    </div>

                    <div class="text-end pt-2">
                        <button type="submit" class="btn btn-primary px-4">Save Changes</button>
                    </div>
                <?= form_close() ?>
            </div>
        </div>
        
        <div class="card">
            <div class="card-body">
                <h5 class="card-title fw-bold text-danger mb-4 border-bottom pb-2">Change Password</h5>
                
                <?= form_open('voter/profile/password') ?>
                    <div class="mb-3">
                        <label class="form-label">Current Password <span class="text-danger">*</span></label>
                        <input type="password" name="current_password" class="form-control" required>
                        <?= form_error('current_password', '<div class="invalid-feedback d-block">', '</div>') ?>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">New Password <span class="text-danger">*</span></label>
                            <input type="password" name="new_password" class="form-control" required minlength="6">
                            <?= form_error('new_password', '<div class="invalid-feedback d-block">', '</div>') ?>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Confirm New Password <span class="text-danger">*</span></label>
                            <input type="password" name="confirm_password" class="form-control" required>
                            <?= form_error('confirm_password', '<div class="invalid-feedback d-block">', '</div>') ?>
                        </div>
                    </div>
                    
                    <div class="text-end pt-2">
                        <button type="submit" class="btn btn-danger px-4">Change Password</button>
                    </div>
                <?= form_close() ?>
            </div>
        </div>
    </div>
    
    <div class="col-lg-4 mt-4 mt-lg-0">
        <div class="card bg-light">
            <div class="card-body">
                <h6 class="fw-bold mb-3"><i class="fa-solid fa-shield-halved text-success me-2"></i> Account Verification</h6>
                
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <span class="text-muted">Email Verified</span>
                    <?php if($student['is_email_verified']): ?>
                        <span class="badge bg-success"><i class="fa-solid fa-check"></i> Yes</span>
                    <?php else: ?>
                        <span class="badge bg-warning text-dark"><i class="fa-solid fa-triangle-exclamation"></i> No</span>
                    <?php endif; ?>
                </div>
                
                <div class="d-flex justify-content-between align-items-center">
                    <span class="text-muted">Matric ID Verified</span>
                    <?php if($student['is_matric_verified']): ?>
                        <span class="badge bg-success"><i class="fa-solid fa-check"></i> Yes</span>
                    <?php else: ?>
                        <span class="badge bg-warning text-dark"><i class="fa-solid fa-triangle-exclamation"></i> No</span>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>
