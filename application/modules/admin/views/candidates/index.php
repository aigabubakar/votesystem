<div class="d-flex justify-content-between align-items-center mb-4">
    <h3 class="mb-0 text-dark fw-bold"><?= $page_title ?></h3>
    <a href="<?= site_url('admin/candidates/create') ?>" class="btn btn-primary">
        <i class="fa-solid fa-plus me-2"></i> Add Candidate
    </a>
</div>

<!-- Election Filter -->
<div class="card mb-4 bg-light border-0">
    <div class="card-body py-3">
        <form action="" method="get" class="d-flex align-items-center gap-3">
            <label class="fw-bold mb-0 text-nowrap">Filter by Election:</label>
            <select class="form-select" onchange="window.location.href=this.value;">
                <option value="<?= site_url('admin/candidates') ?>">All Elections</option>
                <?php foreach ($elections as $e): ?>
                    <option value="<?= site_url('admin/candidates/election/' . $e['id']) ?>" 
                        <?= (isset($election) && $election['id'] == $e['id']) ? 'selected' : '' ?>>
                        <?= html_escape($e['title']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </form>
    </div>
</div>

<div class="card">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead>
                    <tr>
                        <th>Candidate</th>
                        <th>Election & Position</th>
                        <th>Status</th>
                        <th class="text-end">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($candidates)): ?>
                        <tr>
                            <td colspan="4" class="text-center py-5">
                                <div class="empty-state">
                                    <div class="empty-state-icon"><i class="fa-solid fa-users-slash"></i></div>
                                    <h5>No Candidates Found</h5>
                                    <p>Select an election or add a new candidate.</p>
                                </div>
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($candidates as $c): ?>
                            <tr>
                                <td>
                                    <div class="d-flex align-items-center gap-3">
                                        <?php if ($c['photo']): ?>
                                            <img src="<?= base_url($c['photo']) ?>" alt="Photo" class="rounded-circle" width="48" height="48" style="object-fit: cover;">
                                        <?php else: ?>
                                            <div class="rounded-circle bg-secondary text-white d-flex align-items-center justify-content-center" style="width: 48px; height: 48px; font-weight: bold;">
                                                <?= substr($c['first_name'], 0, 1) ?>
                                            </div>
                                        <?php endif; ?>
                                        <div>
                                            <div class="fw-bold text-dark"><?= html_escape($c['first_name'] . ' ' . $c['last_name']) ?></div>
                                            <div class="small text-muted"><?= html_escape($c['student_id']) ?></div>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <div class="fw-bold text-primary"><?= html_escape($c['position_title']) ?></div>
                                    <div class="small text-muted"><?= html_escape($c['election_title']) ?></div>
                                </td>
                                <td>
                                    <span class="badge badge-<?= $c['status'] ?>"><?= ucfirst($c['status']) ?></span>
                                </td>
                                <td class="text-end">
                                    <div class="btn-group">
                                        <?php if ($c['status'] === 'pending' || $c['status'] === 'rejected'): ?>
                                            <a href="<?= site_url('admin/candidates/approve/' . $c['id']) ?>" class="btn btn-sm btn-outline-success" title="Approve">
                                                <i class="fa-solid fa-check"></i>
                                            </a>
                                        <?php endif; ?>
                                        <?php if ($c['status'] === 'pending' || $c['status'] === 'approved'): ?>
                                            <a href="<?= site_url('admin/candidates/reject/' . $c['id']) ?>" class="btn btn-sm btn-outline-warning" title="Reject">
                                                <i class="fa-solid fa-xmark"></i>
                                            </a>
                                        <?php endif; ?>
                                        <a href="<?= site_url('admin/candidates/delete/' . $c['id']) ?>" class="btn btn-sm btn-outline-danger btn-confirm" title="Delete">
                                            <i class="fa-solid fa-trash"></i>
                                        </a>
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
