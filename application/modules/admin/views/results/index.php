<div class="d-flex justify-content-between align-items-center mb-4">
    <h3 class="mb-0 text-dark fw-bold">Election Results</h3>
</div>

<div class="card max-w-800">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead>
                    <tr>
                        <th>Election</th>
                        <th>Session</th>
                        <th>Status</th>
                        <th class="text-end">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($elections)): ?>
                        <tr>
                            <td colspan="4" class="text-center py-5 text-muted">No elections found to show results for.</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($elections as $e): ?>
                            <tr>
                                <td class="fw-bold text-dark"><?= html_escape($e['title']) ?></td>
                                <td><?= html_escape($e['session']) ?></td>
                                <td>
                                    <span class="badge badge-<?= $e['status'] ?>"><?= ucfirst($e['status']) ?></span>
                                </td>
                                <td class="text-end">
                                    <?php if ($e['status'] === 'pending'): ?>
                                        <span class="text-muted small">Not Started</span>
                                    <?php else: ?>
                                        <a href="<?= site_url('admin/results/view/' . $e['id']) ?>" class="btn btn-sm btn-primary">
                                            <i class="fa-solid fa-chart-pie me-1"></i> View Results
                                        </a>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
