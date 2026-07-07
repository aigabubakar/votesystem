<div class="row g-4">
    <!-- Student Info -->
    <div class="col-md-4">
        <div class="card h-100 bg-gradient-primary text-white">
            <div class="card-body text-center p-4">
                <div class="rounded-circle bg-white text-primary d-inline-flex align-items-center justify-content-center mb-3" style="width: 80px; height: 80px; font-size: 2rem; font-weight: bold;">
                    <?= substr($student['first_name'], 0, 1) ?>
                </div>
                <h4 class="fw-bold mb-1"><?= html_escape($student['first_name'] . ' ' . $student['last_name']) ?></h4>
                <p class="text-white-50 mb-3"><?= html_escape($student['student_id']) ?></p>
                
                <div class="bg-white bg-opacity-25 rounded p-2 small mb-2 text-start">
                    <i class="fa-solid fa-building me-2"></i> <?= html_escape($student['dept_name']) ?>
                </div>
                <div class="bg-white bg-opacity-25 rounded p-2 small text-start">
                    <i class="fa-solid fa-layer-group me-2"></i> Level <?= html_escape($student['level']) ?>
                </div>
            </div>
        </div>
    </div>

    <!-- Active Elections -->
    <div class="col-md-8">
        <div class="card h-100">
            <div class="card-header bg-white pb-0 border-0 pt-4 px-4">
                <h5 class="fw-bold text-dark mb-0">Active Elections</h5>
            </div>
            <div class="card-body p-4">
                <?php if (empty($active_elections)): ?>
                    <div class="text-center py-5">
                        <div class="text-muted mb-3"><i class="fa-solid fa-box-archive fa-3x"></i></div>
                        <h6>No active elections right now.</h6>
                        <p class="text-muted small">Check back later or contact your department.</p>
                    </div>
                <?php else: ?>
                    <div class="row g-3">
                        <?php foreach ($active_elections as $e): ?>
                            <div class="col-12">
                                <div class="border rounded p-3 d-flex flex-column flex-sm-row justify-content-between align-items-sm-center gap-3">
                                    <div>
                                        <h6 class="fw-bold mb-1 text-primary"><?= html_escape($e['title']) ?></h6>
                                        <div class="small text-muted mb-2">Session: <?= html_escape($e['session']) ?></div>
                                        <div class="small mt-1">
                                            <i class="fa-solid fa-clock text-warning"></i> Ends: <?= date('d M Y, h:i A', strtotime($e['end_date'])) ?>
                                            <br><span class="fw-bold text-danger countdown-timer" data-endtime="<?= date('c', strtotime($e['end_date'])) ?>"></span>
                                        </div>
                                    </div>
                                    
                                    <div>
                                        <?php if (in_array($e['id'], array_column($voted_elections, 'election_id'))): ?>
                                            <button class="btn btn-success" disabled>
                                                <i class="fa-solid fa-check-circle me-1"></i> Voted
                                            </button>
                                        <?php else: ?>
                                            <a href="<?= site_url('voter/vote/ballot/' . $e['id']) ?>" class="btn btn-primary px-4 shadow-sm">
                                                Vote Now <i class="fa-solid fa-arrow-right ms-1"></i>
                                            </a>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<!-- Voting History -->
<div class="card mt-4">
    <div class="card-header bg-white pt-4 px-4 border-bottom">
        <h5 class="fw-bold text-dark mb-0">Voting History</h5>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="bg-light">
                    <tr>
                        <th class="ps-4">Election</th>
                        <th>Date Voted</th>
                        <th class="text-end pe-4">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($voted_elections)): ?>
                        <tr>
                            <td colspan="3" class="text-center py-4 text-muted">You haven't participated in any elections yet.</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($voted_elections as $v): ?>
                            <tr>
                                <td class="ps-4 fw-bold"><?= html_escape($v['election_title']) ?></td>
                                <td><?= date('d M Y, h:i A', strtotime($v['voted_at'])) ?></td>
                                <td class="text-end pe-4">
                                    <div class="d-flex justify-content-end gap-2">
                                        <a href="<?= site_url('voter/vote/receipt/' . $v['election_id']) ?>" class="btn btn-sm btn-outline-secondary">
                                            <i class="fa-solid fa-receipt me-1"></i> Receipt
                                        </a>
                                        <?php if ($v['election_status'] === 'published'): ?>
                                            <a href="<?= site_url('voter/vote/results/' . $v['election_id']) ?>" class="btn btn-sm btn-primary">
                                                <i class="fa-solid fa-chart-pie me-1"></i> Results
                                            </a>
                                        <?php endif; ?>
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
