<div class="d-flex justify-content-between align-items-center mb-4">
    <h3 class="mb-0 text-dark fw-bold">Election Results</h3>
    <a href="<?= site_url('voter') ?>" class="btn btn-outline-secondary">
        <i class="fa-solid fa-arrow-left me-1"></i> Back to Dashboard
    </a>
</div>

<div class="card mb-4 bg-gradient-primary text-white border-0 shadow-sm">
    <div class="card-body p-4 d-flex justify-content-between align-items-center">
        <div>
            <h4 class="fw-bold mb-1 text-white"><?= html_escape($election['title']) ?></h4>
            <div class="text-lemon">Session: <?= html_escape($election['session']) ?></div>
        </div>
        <div class="text-end">
            <span class="badge bg-white text-primary mb-1">Concluded</span>
            <div class="small">Ended: <?= date('d M Y', strtotime($election['end_date'])) ?></div>
        </div>
    </div>
</div>

<?php if (empty($positions)): ?>
    <div class="alert alert-info">No positions or candidates found for this election.</div>
<?php else: ?>
    <div class="row g-4">
        <?php foreach ($positions as $pos): ?>
            <div class="col-lg-6">
                <div class="card h-100 shadow-sm border-0">
                    <div class="card-header bg-white pt-4 pb-2 border-bottom">
                        <h5 class="mb-0 fw-bold text-dark"><?= html_escape($pos['title']) ?></h5>
                    </div>
                    <div class="card-body">
                        <?php 
                        $candidates = $pos['candidates'];
                        $total_position_votes = $pos['total_votes'] ?? 0;
                        
                        if (empty($candidates)):
                        ?>
                            <p class="text-muted text-center my-3">No candidates.</p>
                        <?php else: ?>
                            
                            <?php foreach ($candidates as $cIndex => $c): 
                                $percentage = $total_position_votes > 0 ? round(($c['vote_count'] / $total_position_votes) * 100, 1) : 0;
                                $is_winner = !empty($c['is_winner']);
                                $bar_color = $is_winner ? 'bg-success' : 'bg-secondary';
                            ?>
                                <div class="mb-3 pb-3 <?= $cIndex < count($candidates)-1 ? 'border-bottom' : '' ?>">
                                    <div class="d-flex justify-content-between align-items-end mb-2">
                                        <div class="d-flex align-items-center gap-2">
                                            <?php if (!empty($c['photo'])): ?>
                                                <img src="<?= base_url($c['photo']) ?>" alt="Photo" class="rounded-circle" width="40" height="40" style="object-fit: cover;">
                                            <?php elseif (!empty($c['profile_photo'])): ?>
                                                <img src="<?= base_url($c['profile_photo']) ?>" alt="Photo" class="rounded-circle" width="40" height="40" style="object-fit: cover;">
                                            <?php else: ?>
                                                <div class="rounded-circle bg-light text-secondary d-flex align-items-center justify-content-center fw-bold" style="width: 40px; height: 40px;">
                                                    <?= substr($c['first_name'], 0, 1) ?>
                                                </div>
                                            <?php endif; ?>
                                            <div>
                                                <span class="fw-bold text-dark d-block">
                                                    <?= html_escape($c['first_name'] . ' ' . $c['last_name']) ?>
                                                    <?php if ($is_winner): ?>
                                                        <i class="fa-solid fa-trophy text-warning ms-1" title="Winner"></i>
                                                    <?php endif; ?>
                                                </span>
                                            </div>
                                        </div>
                                        <div class="text-end">
                                            <span class="fw-bold fs-5 text-dark"><?= number_format($c['vote_count']) ?></span> 
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
                </div>
            </div>
        <?php endforeach; ?>
    </div>
<?php endif; ?>
