<div class="d-flex justify-content-between align-items-center mb-4">
    <h3 class="mb-0 text-dark fw-bold"><?= $page_title ?></h3>
    <a href="<?= site_url('admin/students') ?>" class="btn btn-outline-secondary">
        <i class="fa-solid fa-arrow-left me-2"></i> Back
    </a>
</div>

<div class="card max-w-800">
    <div class="card-body">
        <?= form_open($action) ?>
            
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label">First Name <span class="text-danger">*</span></label>
                    <input type="text" name="first_name" class="form-control" value="<?= set_value('first_name', $student['first_name'] ?? '') ?>" required>
                    <?= form_error('first_name', '<div class="invalid-feedback d-block">', '</div>') ?>
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">Last Name <span class="text-danger">*</span></label>
                    <input type="text" name="last_name" class="form-control" value="<?= set_value('last_name', $student['last_name'] ?? '') ?>" required>
                    <?= form_error('last_name', '<div class="invalid-feedback d-block">', '</div>') ?>
                </div>
            </div>

            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label">Student / Matric ID <span class="text-danger">*</span></label>
                    <input type="text" name="student_id" class="form-control" value="<?= set_value('student_id', $student['student_id'] ?? '') ?>" <?= isset($student) ? 'readonly' : 'required' ?>>
                    <?php if (isset($student)): ?>
                        <div class="form-text">Student ID cannot be changed after creation.</div>
                    <?php endif; ?>
                    <?= form_error('student_id', '<div class="invalid-feedback d-block">', '</div>') ?>
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">Email Address <span class="text-danger">*</span></label>
                    <input type="email" name="email" class="form-control" value="<?= set_value('email', $student['email'] ?? '') ?>" required>
                    <?= form_error('email', '<div class="invalid-feedback d-block">', '</div>') ?>
                </div>
            </div>

            <div class="row">
                <div class="col-md-4 mb-3">
                    <label class="form-label">Phone Number</label>
                    <input type="text" name="phone" class="form-control" value="<?= set_value('phone', $student['phone'] ?? '') ?>">
                </div>
                <div class="col-md-4 mb-3">
                    <label class="form-label">Gender <span class="text-danger">*</span></label>
                    <select name="gender" class="form-select" required>
                        <option value="">Select...</option>
                        <option value="male" <?= set_select('gender', 'male', isset($student) && $student['gender'] === 'male') ?>>Male</option>
                        <option value="female" <?= set_select('gender', 'female', isset($student) && $student['gender'] === 'female') ?>>Female</option>
                    </select>
                </div>
                <div class="col-md-4 mb-3">
                    <label class="form-label">Level <span class="text-danger">*</span></label>
                    <select name="level" class="form-select" required>
                        <option value="">Select...</option>
                        <?php foreach (['100', '200', '300', '400', '500'] as $lvl): ?>
                            <option value="<?= $lvl ?>" <?= set_select('level', $lvl, isset($student) && $student['level'] == $lvl) ?>><?= $lvl ?> Level</option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>

            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label">Department <span class="text-danger">*</span></label>
                    <select name="department_id" class="form-select" required>
                        <option value="">Select...</option>
                        <?php foreach ($departments as $dept): ?>
                            <option value="<?= $dept['id'] ?>" <?= set_select('department_id', $dept['id'], isset($student) && $student['department_id'] == $dept['id']) ?>>
                                <?= $dept['name'] ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-6 mb-4">
                    <label class="form-label">Password <?= isset($student) ? '' : '<span class="text-danger">*</span>' ?></label>
                    <input type="password" name="password" class="form-control" <?= isset($student) ? '' : 'required' ?> minlength="6">
                    <?php if (isset($student)): ?>
                        <div class="form-text">Leave blank to keep current password.</div>
                    <?php endif; ?>
                    <?= form_error('password', '<div class="invalid-feedback d-block">', '</div>') ?>
                </div>
            </div>

            <div class="text-end border-top pt-3">
                <button type="submit" class="btn btn-primary px-4">
                    <i class="fa-solid fa-save me-2"></i> Save Student
                </button>
            </div>

        <?= form_close() ?>
    </div>
</div>
