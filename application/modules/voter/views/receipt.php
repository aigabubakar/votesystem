<div class="row justify-content-center">
    <div class="col-md-8 col-lg-6">
        <div class="card shadow border-0 mt-4">
            <div class="card-header bg-success text-white text-center py-4">
                <i class="fa-solid fa-circle-check fa-4x mb-3"></i>
                <h3 class="fw-bold mb-0">Vote Submitted!</h3>
            </div>
            
            <div class="card-body p-5">
                <p class="text-center text-muted mb-4">Your vote for <strong><?= html_escape($election['title']) ?></strong> has been securely recorded.</p>
                
                <div class="bg-light rounded p-4 border mb-4">
                    <h5 class="fw-bold text-dark border-bottom pb-2 mb-3">Voting Receipt</h5>
                    <div class="d-flex justify-content-between mb-2">
                        <span class="text-muted">Voter Name:</span>
                        <span class="fw-bold text-dark"><?= html_escape($student['first_name'] . ' ' . $student['last_name']) ?></span>
                    </div>
                    <div class="d-flex justify-content-between mb-2">
                        <span class="text-muted">Matric ID:</span>
                        <span class="fw-bold text-dark"><?= html_escape($student['student_id']) ?></span>
                    </div>
                    <div class="d-flex justify-content-between mb-2">
                        <span class="text-muted">Timestamp:</span>
                        <span class="fw-bold text-dark"><?= date('d M Y, h:i:s A', strtotime($voted_at)) ?></span>
                    </div>
                    <div class="d-flex justify-content-between">
                        <span class="text-muted">Status:</span>
                        <span class="badge bg-success">Verified</span>
                    </div>
                </div>
                
                <div class="alert alert-info small text-center mb-0 border-0">
                    <i class="fa-solid fa-lock me-2"></i> Your ballot is anonymous. This receipt proves you participated, but does not show who you voted for.
                </div>
            </div>
            
            <div class="card-footer bg-white border-top-0 p-4 text-center">
                <button onclick="window.print()" class="btn btn-outline-secondary me-2">
                    <i class="fa-solid fa-print me-1"></i> Print Receipt
                </button>
                <a href="<?= site_url('voter') ?>" class="btn btn-primary">
                    Return to Dashboard
                </a>
            </div>
        </div>
    </div>
</div>

<style>
@media print {
    .voter-navbar, .voter-footer, .btn { display: none !important; }
    .voter-content { margin: 0 !important; padding: 0 !important; background: white; }
    .card { box-shadow: none !important; border: 1px solid #ccc !important; }
    body { background-color: white !important; }
}
</style>
