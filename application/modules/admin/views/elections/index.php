<div class="d-flex justify-content-between align-items-center mb-4">
    <h3 class="mb-0 text-dark fw-bold">Manage Elections</h3>
    <a href="<?= site_url('admin/elections/create') ?>" class="btn btn-primary">
        <i class="fa-solid fa-plus me-2"></i> Create Election
    </a>
</div>

<div class="card">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead>
                    <tr>
                        <th>Title & Session</th>
                        <th>Department</th>
                        <th>Duration</th>
                        <th>Status</th>
                        <th class="text-end">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($elections)): ?>
                        <tr>
                            <td colspan="5" class="text-center py-5">
                                <div class="empty-state">
                                    <div class="empty-state-icon"><i class="fa-solid fa-box-archive"></i></div>
                                    <h5>No Elections Found</h5>
                                    <p>Get started by creating a new election.</p>
                                </div>
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($elections as $e): ?>
                            <tr>
                                <td>
                                    <div class="fw-bold text-dark"><?= html_escape($e['title']) ?></div>
                                    <div class="small text-muted"><?= html_escape($e['session']) ?></div>
                                </td>
                                <td>
                                    <?= $e['dept_name'] ? html_escape($e['dept_name']) : '<span class="badge bg-secondary">All Departments</span>' ?>
                                </td>
                                <td>
                                    <div class="small text-muted">
                                        <i class="fa-regular fa-clock me-1"></i> Start: <?= date('d M Y, h:i A', strtotime($e['start_date'])) ?>
                                    </div>
                                    <div class="small text-muted">
                                        <i class="fa-solid fa-flag-checkered me-1"></i> End: <?= date('d M Y, h:i A', strtotime($e['end_date'])) ?>
                                    </div>
                                </td>
                                <td>
                                    <span class="badge badge-<?= $e['status'] ?>"><?= ucfirst($e['status']) ?></span>
                                </td>
                                <td class="text-end">
                                    <div class="dropdown">
                                        <button class="btn btn-sm btn-light border" type="button" data-bs-toggle="dropdown">
                                            <i class="fa-solid fa-ellipsis-vertical"></i>
                                        </button>
                                        <ul class="dropdown-menu dropdown-menu-end shadow-sm border-0">
                                            <li><a class="dropdown-item" href="<?= site_url('admin/positions/' . $e['id']) ?>"><i class="fa-solid fa-list-ul me-2 text-muted"></i> Manage Positions</a></li>
                                            <li><a class="dropdown-item" href="<?= site_url('admin/candidates/election/' . $e['id']) ?>"><i class="fa-solid fa-users me-2 text-muted"></i> Manage Candidates</a></li>
                                            <li><hr class="dropdown-divider"></li>
                                            
                                            <?php if ($e['status'] === 'pending'): ?>
                                                <li><a class="dropdown-item text-success btn-confirm" href="<?= site_url('admin/elections/status/' . $e['id'] . '/active') ?>" data-message="Activate this election now?"><i class="fa-solid fa-play me-2"></i> Activate</a></li>
                                            <?php elseif ($e['status'] === 'active'): ?>
                                                <li><a class="dropdown-item text-warning btn-confirm" href="<?= site_url('admin/elections/status/' . $e['id'] . '/closed') ?>" data-message="Close this election? Voters will no longer be able to vote."><i class="fa-solid fa-stop me-2"></i> Close Election</a></li>
                                            <?php endif; ?>

                                            <li><a class="dropdown-item" href="<?= site_url('admin/elections/edit/' . $e['id']) ?>"><i class="fa-solid fa-pen-to-square me-2 text-muted"></i> Edit Details</a></li>
                                            <li><a class="dropdown-item text-danger btn-confirm" href="<?= site_url('admin/elections/delete/' . $e['id']) ?>" data-message="Are you sure you want to delete this election? This will also delete all associated positions, candidates, and votes!"><i class="fa-solid fa-trash-can me-2"></i> Delete</a></li>
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
