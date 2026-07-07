<div class="d-flex justify-content-between align-items-center mb-4">
    <h3 class="mb-0 text-dark fw-bold">Manage Students (Voters)</h3>
    <div>
        <button class="btn btn-outline-success me-2" data-bs-toggle="modal" data-bs-target="#importModal">
            <i class="fa-solid fa-file-csv me-2"></i> Import CSV
        </button>
        <a href="<?= site_url('admin/students/create') ?>" class="btn btn-primary">
            <i class="fa-solid fa-user-plus me-2"></i> Add Student
        </a>
    </div>
</div>

<!-- Filters -->
<div class="card mb-4 bg-light border-0">
    <div class="card-body py-3">
        <form action="<?= site_url('admin/students') ?>" method="get" class="row g-3 align-items-center no-loading">
            <div class="col-md-4">
                <input type="text" name="q" class="form-control" placeholder="Search by name or ID..." value="<?= html_escape($filters['search']) ?>">
            </div>
            <div class="col-md-4">
                <select name="dept_id" class="form-select">
                    <option value="">All Departments</option>
                    <?php foreach ($departments as $d): ?>
                        <option value="<?= $d['id'] ?>" <?= ($filters['dept_id'] == $d['id']) ? 'selected' : '' ?>>
                            <?= html_escape($d['name']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-4">
                <button type="submit" class="btn btn-secondary">Filter</button>
                <a href="<?= site_url('admin/students') ?>" class="btn btn-link text-decoration-none">Clear</a>
            </div>
        </form>
    </div>
</div>

<div class="card">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead>
                    <tr>
                        <th>Student</th>
                        <th>Contact Info</th>
                        <th>Department & Level</th>
                        <th>Verifications</th>
                        <th>Status</th>
                        <th class="text-end">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($students)): ?>
                        <tr>
                            <td colspan="6" class="text-center py-5">
                                <div class="empty-state">
                                    <div class="empty-state-icon"><i class="fa-solid fa-users"></i></div>
                                    <h5>No Students Found</h5>
                                    <p>Try adjusting your search filters or add a new student.</p>
                                </div>
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($students as $s): ?>
                            <tr>
                                <td>
                                    <div class="fw-bold text-dark"><?= html_escape($s['first_name'] . ' ' . $s['last_name']) ?></div>
                                    <div class="small text-muted fw-bold"><?= html_escape($s['student_id']) ?></div>
                                </td>
                                <td>
                                    <div class="small"><i class="fa-regular fa-envelope me-1 text-muted"></i> <?= html_escape($s['email']) ?></div>
                                    <?php if ($s['phone']): ?>
                                        <div class="small"><i class="fa-solid fa-phone me-1 text-muted"></i> <?= html_escape($s['phone']) ?></div>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <div class="text-dark"><?= html_escape($s['dept_name']) ?></div>
                                    <div class="small text-muted">Lvl: <?= html_escape($s['level']) ?></div>
                                </td>
                                <td>
                                    <div class="d-flex flex-column gap-1 align-items-start">
                                        <?php if ($s['is_email_verified']): ?>
                                            <span class="verification-badge verified"><i class="fa-solid fa-check"></i> Email</span>
                                        <?php else: ?>
                                            <span class="verification-badge unverified"><i class="fa-solid fa-xmark"></i> Email</span>
                                        <?php endif; ?>
                                        
                                        <?php if ($s['is_matric_verified']): ?>
                                            <span class="verification-badge verified"><i class="fa-solid fa-check"></i> Matric</span>
                                        <?php else: ?>
                                            <span class="verification-badge unverified"><i class="fa-solid fa-xmark"></i> Matric</span>
                                        <?php endif; ?>
                                    </div>
                                </td>
                                <td>
                                    <?php if ($s['is_active']): ?>
                                        <span class="badge badge-active">Active</span>
                                    <?php else: ?>
                                        <span class="badge badge-closed">Inactive</span>
                                    <?php endif; ?>
                                </td>
                                <td class="text-end">
                                    <div class="dropdown">
                                        <button class="btn btn-sm btn-light border" type="button" data-bs-toggle="dropdown">
                                            <i class="fa-solid fa-ellipsis-vertical"></i>
                                        </button>
                                        <ul class="dropdown-menu dropdown-menu-end shadow-sm border-0">
                                            <li><a class="dropdown-item" href="<?= site_url('admin/students/edit/' . $s['id']) ?>"><i class="fa-solid fa-pen-to-square me-2 text-muted"></i> Edit Student</a></li>
                                            
                                            <?php if ($s['is_active']): ?>
                                                <li><a class="dropdown-item text-warning btn-confirm" href="<?= site_url('admin/students/toggle/' . $s['id']) ?>" data-message="Deactivate this student? They won't be able to log in."><i class="fa-solid fa-ban me-2"></i> Deactivate</a></li>
                                            <?php else: ?>
                                                <li><a class="dropdown-item text-success btn-confirm" href="<?= site_url('admin/students/toggle/' . $s['id']) ?>" data-message="Activate this student?"><i class="fa-solid fa-check-circle me-2"></i> Activate</a></li>
                                            <?php endif; ?>

                                            <li><hr class="dropdown-divider"></li>
                                            <li><a class="dropdown-item text-danger btn-confirm" href="<?= site_url('admin/students/delete/' . $s['id']) ?>" data-message="Delete this student permanently?"><i class="fa-solid fa-trash-can me-2"></i> Delete</a></li>
                                        </ul>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Import Modal -->
<div class="modal fade" id="importModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <form action="<?= site_url('admin/students/import') ?>" method="post" enctype="multipart/form-data" class="no-loading">
                <input type="hidden" name="<?= $this->security->get_csrf_token_name() ?>" value="<?= $this->security->get_csrf_hash() ?>">
                <div class="modal-header border-bottom-0">
                    <h5 class="modal-title fw-bold">Import Students via CSV</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body py-0">
                    <p class="small text-muted mb-3">Upload a CSV file with the following headers (exact order, with headers in first row):<br>
                    <code>First Name, Last Name, Matric Number, Email, Department, Level</code></p>
                    <div class="mb-3">
                        <input type="file" name="csv_file" class="form-control" accept=".csv" required>
                    </div>
                </div>
                <div class="modal-footer border-top-0">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Import</button>
                </div>
            </form>
        </div>
    </div>
</div>
