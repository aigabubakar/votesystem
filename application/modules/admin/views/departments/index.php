<div class="d-flex justify-content-between align-items-center mb-4">
    <h3 class="mb-0 text-dark fw-bold">Manage Departments</h3>
    <a href="<?= site_url('admin/departments/create') ?>" class="btn btn-primary">
        <i class="fa-solid fa-plus me-2"></i> Add Department
    </a>
</div>

<div class="card max-w-800">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead>
                    <tr>
                        <th>Department Name</th>
                        <th>Code</th>
                        <th class="text-end">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($departments)): ?>
                        <tr>
                            <td colspan="3" class="text-center py-4 text-muted">No departments found.</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($departments as $d): ?>
                            <tr>
                                <td class="fw-bold text-dark"><?= html_escape($d['name']) ?></td>
                                <td><span class="badge bg-secondary"><?= html_escape($d['code']) ?></span></td>
                                <td class="text-end">
                                    <a href="<?= site_url('admin/departments/edit/' . $d['id']) ?>" class="btn btn-sm btn-outline-secondary">
                                        <i class="fa-solid fa-pen"></i> Edit
                                    </a>
                                    <a href="<?= site_url('admin/departments/delete/' . $d['id']) ?>" class="btn btn-sm btn-outline-danger btn-confirm" data-message="Delete this department? Make sure no students or elections are linked to it first.">
                                        <i class="fa-solid fa-trash"></i>
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
