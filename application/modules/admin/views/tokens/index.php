<div class="d-flex justify-content-between align-items-center mb-4">
    <h3 class="mb-0 text-dark fw-bold">Manage Voting Tokens</h3>
    <div>
        <a href="<?= site_url('admin/tokens/export') ?>" class="btn btn-outline-success me-2">
            <i class="fa-solid fa-file-csv me-2"></i> Export Tokens
        </a>
        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#generateModal">
            <i class="fa-solid fa-key me-2"></i> Generate Tokens
        </button>
    </div>
</div>

<div class="row mb-4">
    <div class="col-md-4">
        <div class="card bg-primary text-white border-0 shadow-sm h-100">
            <div class="card-body">
                <h6 class="text-white-50 text-uppercase fw-bold mb-2">Total Tokens</h6>
                <h2 class="mb-0 fw-bold"><?= count($tokens) ?></h2>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card bg-success text-white border-0 shadow-sm h-100">
            <div class="card-body">
                <h6 class="text-white-50 text-uppercase fw-bold mb-2">Unused Tokens</h6>
                <?php $unused = count(array_filter($tokens, fn($t) => $t['status'] === 'unused')); ?>
                <h2 class="mb-0 fw-bold"><?= $unused ?></h2>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card bg-secondary text-white border-0 shadow-sm h-100">
            <div class="card-body">
                <h6 class="text-white-50 text-uppercase fw-bold mb-2">Used Tokens</h6>
                <h2 class="mb-0 fw-bold"><?= count($tokens) - $unused ?></h2>
            </div>
        </div>
    </div>
</div>

<div class="card border-0 shadow-sm">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th class="ps-4">Token String</th>
                        <th>Election</th>
                        <th>Status</th>
                        <th>Used By (Matric)</th>
                        <th>Used At</th>
                        <th>Generated At</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($tokens)): ?>
                        <tr>
                            <td colspan="5" class="text-center py-5 text-muted">
                                <i class="fa-solid fa-key fa-3x mb-3 text-light"></i>
                                <h5>No Tokens Generated</h5>
                                <p>Generate tokens to distribute to voters.</p>
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($tokens as $t): ?>
                            <tr>
                                <td class="ps-4">
                                    <code class="fs-6 text-dark fw-bold bg-light px-2 py-1 rounded"><?= html_escape($t['token']) ?></code>
                                </td>
                                <td>
                                    <?php if ($t['election_title']): ?>
                                        <span class="text-dark fw-bold"><?= html_escape($t['election_title']) ?></span>
                                    <?php else: ?>
                                        <span class="text-muted fst-italic">Global</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?php if ($t['status'] === 'unused'): ?>
                                        <span class="badge bg-success bg-opacity-10 text-success border border-success">Unused</span>
                                    <?php else: ?>
                                        <span class="badge bg-secondary bg-opacity-10 text-secondary border border-secondary">Used</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?php if ($t['matric_number']): ?>
                                        <span class="fw-bold text-dark"><?= html_escape($t['matric_number']) ?></span>
                                    <?php else: ?>
                                        <span class="text-muted fst-italic">N/A</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?= $t['used_at'] ? date('M d, Y h:i A', strtotime($t['used_at'])) : '<span class="text-muted fst-italic">N/A</span>' ?>
                                </td>
                                <td>
                                    <span class="small text-muted"><?= date('M d, Y', strtotime($t['created_at'])) ?></span>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Generate Modal -->
<div class="modal fade" id="generateModal" tabindex="-1">
    <div class="modal-dialog modal-sm modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <form action="<?= site_url('admin/tokens/generate') ?>" method="post" class="no-loading">
                <input type="hidden" name="<?= $this->security->get_csrf_token_name() ?>" value="<?= $this->security->get_csrf_hash() ?>">
                <div class="modal-header border-bottom-0">
                    <h5 class="modal-title fw-bold">Generate Tokens</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body py-0">
                    <p class="small text-muted mb-3">Specify an optional prefix and how many random voter tokens you want to generate. Max 5,000 at once.</p>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Map to Election</label>
                        <select name="election_id" class="form-select">
                            <option value="">Global (Valid for all active elections)</option>
                            <?php foreach ($elections as $e): ?>
                                <option value="<?= $e['id'] ?>"><?= html_escape($e['title']) ?></option>
                            <?php endforeach; ?>
                        </select>
                        <div class="form-text">If mapped to a specific election, the student can only use this token to vote in that election.</div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Prefix (Optional)</label>
                        <input type="text" name="prefix" class="form-control" placeholder="e.g. NUNSA" maxlength="10">
                        <div class="form-text">Will be prepended to the 8-character token (e.g. NUNSA-XXXXXXXX).</div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Number of Tokens</label>
                        <input type="number" name="amount" class="form-control form-control-lg" min="1" max="5000" value="50" required>
                    </div>
                </div>
                <div class="modal-footer border-top-0">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Generate</button>
                </div>
            </form>
        </div>
    </div>
</div>
