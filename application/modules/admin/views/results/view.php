<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h3 class="mb-1 text-dark fw-bold">Results: <?= html_escape($election['title']) ?></h3>
        <p class="text-muted mb-0">Session: <?= html_escape($election['session']) ?> &bull; Status: <span class="badge badge-<?= $election['status'] ?>"><?= ucfirst($election['status']) ?></span></p>
    </div>
    <div class="d-flex gap-2">
        <a href="<?= site_url('admin/results') ?>" class="btn btn-outline-secondary">
            <i class="fa-solid fa-arrow-left"></i>
        </a>
        <button onclick="window.print()" class="btn btn-primary d-none d-md-block">
            <i class="fa-solid fa-print me-2"></i> Print Report
        </button>
        <?php if ($election['status'] === 'closed'): ?>
            <a href="<?= site_url('admin/results/publish/' . $election['id']) ?>" class="btn btn-success btn-confirm" data-message="Publish this election? Students will be able to see the results.">
                <i class="fa-solid fa-bullhorn me-1"></i> Publish Results
            </a>
        <?php endif; ?>
    </div>
</div>

<div class="row g-4 mb-4">
    <div class="col-sm-6 col-md-4">
        <div class="stat-card">
            <div class="stat-icon info"><i class="fa-solid fa-users"></i></div>
            <div>
                <div class="stat-value"><?= number_format($statistics['total_registered']) ?></div>
                <div class="stat-label">Total Registered Voters</div>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-md-4">
        <div class="stat-card">
            <div class="stat-icon success"><i class="fa-solid fa-check-to-slot"></i></div>
            <div>
                <div class="stat-value"><?= number_format($statistics['total_voted']) ?></div>
                <div class="stat-label">Total Votes Cast (Turnout)</div>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-md-4">
        <div class="stat-card">
            <div class="stat-icon warning"><i class="fa-solid fa-percent"></i></div>
            <div>
                <div class="stat-value"><?= $statistics['turnout_pct'] ?>%</div>
                <div class="stat-label">Overall Turnout %</div>
            </div>
        </div>
    </div>
</div>



<?php if (empty($grouped)): ?>
    <div class="alert alert-info text-center py-4">
        No positions or candidates found for this election.
    </div>
<?php else: ?>
    <div class="row g-4">
        <?php foreach ($grouped as $position_id => $data): ?>
            <div class="col-lg-6">
                <div class="card h-100 shadow-sm border-0">
                    <div class="card-header bg-white pt-3 pb-2 border-bottom d-flex justify-content-between align-items-center">
                        <h5 class="mb-0 fw-bold text-dark"><?= html_escape($data['position_title']) ?></h5>
                    </div>
                    <div class="card-body">
                        <?php 
                        $candidates = $data['candidates'];
                        $total_position_votes = $data['total_votes_cast'];
                        
                        $has_winner = false;
                        foreach ($candidates as $cand) {
                            if ($cand['is_winner']) {
                                $has_winner = true;
                                break;
                            }
                        }
                        
                        
                        if (empty($candidates)):
                        ?>
                            <p class="text-muted text-center my-3">No candidates for this position.</p>
                        <?php else: ?>
                            
                            <?php foreach ($candidates as $index => $c): 
                                $percentage = $total_position_votes > 0 ? round(($c['vote_count'] / $total_position_votes) * 100, 1) : 0;
                                
                                $bar_color = $c['is_winner'] ? 'bg-success' : 'bg-primary';
                            ?>
                                <div class="mb-3 pb-3 <?= $index < count($candidates)-1 ? 'border-bottom' : '' ?>">
                                    <div class="d-flex justify-content-between align-items-end mb-2">
                                        <div class="d-flex align-items-center gap-3">
                                            <?php if (!empty($c['photo'])): ?>
                                                <img src="<?= base_url($c['photo']) ?>" alt="Photo" class="rounded-circle" width="48" height="48" style="object-fit: cover;">
                                            <?php elseif (!empty($c['profile_photo'])): ?>
                                                <img src="<?= base_url($c['profile_photo']) ?>" alt="Photo" class="rounded-circle" width="48" height="48" style="object-fit: cover;">
                                            <?php else: ?>
                                                <div class="rounded-circle bg-light text-secondary d-flex align-items-center justify-content-center fw-bold" style="width: 48px; height: 48px;">
                                                    <?= substr($c['first_name'], 0, 1) ?>
                                                </div>
                                            <?php endif; ?>
                                            <div>
                                                <div class="fw-bold text-dark fs-6">
                                                    <?= html_escape($c['first_name'] . ' ' . $c['last_name']) ?>
                                                    <?php if ($c['is_winner']): ?>
                                                        <span class="badge bg-success ms-2"><i class="fa-solid fa-trophy me-1"></i> Winner</span>
                                                    <?php endif; ?>
                                                </div>
                                                <?php if ($election['status'] === 'closed' && !$has_winner): ?>
                                                    <a href="<?= site_url('admin/results/declare_winner/' . $election['id'] . '/' . $position_id . '/' . $c['candidate_id']) ?>" class="btn btn-sm btn-outline-primary mt-1 btn-confirm" data-message="Declare <?= html_escape($c['first_name']) ?> as the winner for this position?">Declare Winner</a>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                        <div class="text-end">
                                            <span class="fw-bold fs-5"><?= number_format($c['vote_count']) ?></span> 
                                            <span class="text-muted small d-block">votes (<?= $percentage ?>%)</span>
                                        </div>
                                    </div>
                                    <div class="progress" style="height: 8px;">
                                        <div class="progress-bar <?= $bar_color ?>" role="progressbar" style="width: <?= $percentage ?>%" aria-valuenow="<?= $percentage ?>" aria-valuemin="0" aria-valuemax="100"></div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                            
                        <?php endif; ?>
                    </div>
                    <div class="card-footer bg-white text-end text-muted small border-top-0">
                        Total votes cast for position: <strong><?= number_format($total_position_votes) ?></strong>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
<?php endif; ?>

<style>
@media print {
    .sidebar, .topbar, .btn, .alert-dismissible { display: none !important; }
    .admin-main { margin-left: 0 !important; padding: 0 !important; }
    .card { border: none !important; box-shadow: none !important; }
    .row { page-break-inside: avoid; }
    body { background-color: white !important; }
}
</style>
