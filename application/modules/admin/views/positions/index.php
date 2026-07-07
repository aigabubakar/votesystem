<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h3 class="mb-1 text-dark fw-bold">Positions</h3>
        <p class="text-muted mb-0">For: <strong><?= html_escape($election['title']) ?></strong></p>
    </div>
    <div class="d-flex gap-2">
        <a href="<?= site_url('admin/elections') ?>" class="btn btn-outline-secondary">
            <i class="fa-solid fa-arrow-left"></i>
        </a>
        <a href="<?= site_url('admin/positions/create/' . $election['id']) ?>" class="btn btn-primary">
            <i class="fa-solid fa-plus me-2"></i> Add Position
        </a>
    </div>
</div>

<div class="card">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead>
                    <tr>
                        <th>Order</th>
                        <th>Position Title</th>
                        <th>Max Votes Allowed</th>
                        <th class="text-end">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($positions)): ?>
                        <tr>
                            <td colspan="4" class="text-center py-4 text-muted">No positions added to this election yet.</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($positions as $p): ?>
                            <tr>
                                <td><span class="badge bg-secondary"><?= $p['sort_order'] ?></span></td>
                                <td class="fw-bold text-dark">
                                    <?= html_escape($p['title']) ?>
                                    <?php if($p['description']): ?>
                                        <div class="small text-muted fw-normal"><?= html_escape($p['description']) ?></div>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <span class="badge bg-info text-dark"><?= $p['max_votes'] ?> Vote(s)</span>
                                </td>
                                <td class="text-end">
                                    <a href="<?= site_url('admin/positions/edit/' . $p['id']) ?>" class="btn btn-sm btn-outline-secondary">
                                        <i class="fa-solid fa-pen"></i> Edit
                                    </a>
                                    <a href="<?= site_url('admin/positions/delete/' . $p['id']) ?>" class="btn btn-sm btn-outline-danger btn-confirm" data-message="Delete this position? All candidates and votes for this position will be lost.">
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
