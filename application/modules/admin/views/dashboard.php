<div class="row g-4 mb-4">
    <div class="col-sm-6 col-xl-4">
        <div class="stat-card">
            <div class="stat-icon primary"><i class="fa-solid fa-user-graduate"></i></div>
            <div>
                <div class="stat-value"><?= number_format($stats['total_students']) ?></div>
                <div class="stat-label">Registered Voters</div>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-xl-4">
        <div class="stat-card">
            <div class="stat-icon lemon"><i class="fa-solid fa-box-archive"></i></div>
            <div>
                <div class="stat-value"><?= number_format($stats['active_elections']) ?></div>
                <div class="stat-label">Active Elections</div>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-xl-4">
        <div class="stat-card">
            <div class="stat-icon info"><i class="fa-solid fa-users"></i></div>
            <div>
                <div class="stat-value"><?= number_format($stats['total_candidates']) ?></div>
                <div class="stat-label">Approved Candidates</div>
            </div>
        </div>
    </div>
</div>

<div class="row g-4">
    <div class="col-lg-8">
        <div class="card h-100">
            <div class="card-header d-flex justify-content-between align-items-center">
                <span>Recent Elections</span>
                <a href="<?= site_url('admin/elections') ?>" class="btn btn-sm btn-outline-primary">View All</a>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead>
                            <tr>
                                <th>Title</th>
                                <th>Session</th>
                                <th>Status</th>
                                <th>End Date</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($recent_elections)): ?>
                                <tr>
                                    <td colspan="4" class="text-center py-4 text-muted">No elections found.</td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($recent_elections as $e): ?>
                                    <tr>
                                        <td class="fw-bold"><?= html_escape($e['title']) ?></td>
                                        <td><?= html_escape($e['session']) ?></td>
                                        <td>
                                            <span class="badge badge-<?= $e['status'] ?>"><?= ucfirst($e['status']) ?></span>
                                        </td>
                                        <td><?= date('M d, Y', strtotime($e['end_date'])) ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-lg-4">
        <div class="card h-100 bg-gradient-primary text-white">
            <div class="card-body d-flex flex-column justify-content-center align-items-center text-center p-4">
                <div class="mb-3">
                    <i class="fa-solid fa-bolt text-lemon" style="font-size: 3rem;"></i>
                </div>
                <h4 class="text-white fw-bold">Quick Actions</h4>
                <p class="text-white-50 mb-4 small">Manage your elections and voters instantly.</p>
                
                <div class="d-grid gap-3 w-100">
                    <a href="<?= site_url('admin/elections/create') ?>" class="btn btn-lemon">
                        <i class="fa-solid fa-plus me-2"></i> New Election
                    </a>
                    <a href="<?= site_url('admin/students/create') ?>" class="btn btn-outline-light">
                        <i class="fa-solid fa-user-plus me-2"></i> Add Student
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
