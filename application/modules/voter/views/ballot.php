<div class="text-center mb-4">
    <h2 class="fw-bold text-dark"><?= html_escape($election['title']) ?></h2>
    <p class="text-muted lead">Please cast your vote carefully. You can only vote once per election.</p>
</div>

<div class="alert alert-warning d-flex align-items-center mb-4 border-0 border-start border-warning border-5">
    <i class="fa-solid fa-circle-info fa-2x me-3"></i>
    <div>
        <strong>Instructions:</strong> Click on a candidate's card to select them. If a position allows multiple votes, you can select up to the maximum number allowed. Click "Submit Ballot" at the bottom when you are done.
    </div>
</div>

<?= form_open('voter/vote/submit/' . $election['id'], ['id' => 'ballot-form', 'class' => 'no-loading']) ?>
    
    <?php if (empty($ballot_data)): ?>
        <div class="text-center py-5">
            <h4>No positions or candidates available.</h4>
        </div>
    <?php else: ?>
        
        <?php foreach ($ballot_data as $position_id => $data): ?>
            <div class="card mb-5 border-0 shadow-sm">
                <div class="card-header bg-gradient-primary text-white p-3">
                    <h5 class="mb-0 fw-bold text-white"><?= html_escape($data['position_title']) ?></h5>
                    <?php if ($data['max_votes'] > 1): ?>
                        <small class="text-lemon">Select up to <?= $data['max_votes'] ?> candidates</small>
                    <?php else: ?>
                        <small class="text-lemon">Select 1 candidate</small>
                    <?php endif; ?>
                </div>
                
                <div class="card-body bg-light">
                    <?php if (empty($data['candidates'])): ?>
                        <p class="text-muted text-center my-3">No candidates for this position.</p>
                    <?php else: ?>
                        <div class="row g-3 justify-content-center" id="position-<?= $position_id ?>">
                            
                            <!-- Abstain Option -->
                            <div class="col-sm-6 col-md-4 col-lg-3">
                                <label class="candidate-card h-100 text-center" data-position="<?= $position_id ?>">
                                    <?php if ($data['max_votes'] > 1): ?>
                                        <input type="checkbox" name="votes[<?= $position_id ?>][]" value="abstain" class="d-none">
                                    <?php else: ?>
                                        <input type="radio" name="votes[<?= $position_id ?>]" value="abstain" class="d-none" checked>
                                    <?php endif; ?>
                                    
                                    <div class="card-body d-flex flex-column justify-content-center align-items-center py-4 h-100 w-100">
                                        <div class="w-100">
                                            <div class="rounded-circle bg-secondary text-white d-inline-flex align-items-center justify-content-center mb-3" style="width: 80px; height: 80px; font-size: 1.5rem;">
                                                <i class="fa-solid fa-ban"></i>
                                            </div>
                                            <h6 class="fw-bold mb-0">Abstain</h6>
                                            <div class="small text-muted mt-2">(Do not vote for this position)</div>
                                        </div>
                                    </div>
                                    <div class="selection-indicator"><i class="fa-solid fa-check"></i></div>
                                </label>
                            </div>

                            <!-- Candidates -->
                            <?php foreach ($data['candidates'] as $c): ?>
                                <div class="col-sm-6 col-md-4 col-lg-3">
                                    <label class="candidate-card h-100 text-center" data-position="<?= $position_id ?>">
                                        <?php if ($data['max_votes'] > 1): ?>
                                            <input type="checkbox" name="votes[<?= $position_id ?>][]" value="<?= $c['id'] ?>" class="d-none">
                                        <?php else: ?>
                                            <input type="radio" name="votes[<?= $position_id ?>]" value="<?= $c['id'] ?>" class="d-none">
                                        <?php endif; ?>
                                        
                                        <div class="card-body py-4 d-flex flex-column align-items-center justify-content-between h-100 w-100">
                                            <div class="w-100">
                                                <?php if (!empty($c['photo'])): ?>
                                                    <img src="<?= base_url($c['photo']) ?>" alt="Photo" class="rounded-circle mb-3 shadow-sm" width="80" height="80" style="object-fit: cover; border: 3px solid white;">
                                                <?php elseif (!empty($c['profile_photo'])): ?>
                                                    <img src="<?= base_url($c['profile_photo']) ?>" alt="Photo" class="rounded-circle mb-3 shadow-sm" width="80" height="80" style="object-fit: cover; border: 3px solid white;">
                                                <?php else: ?>
                                                    <div class="rounded-circle bg-primary text-white d-inline-flex align-items-center justify-content-center mb-3 shadow-sm" style="width: 80px; height: 80px; font-size: 2rem; font-weight: bold; border: 3px solid white;">
                                                        <?= substr($c['first_name'], 0, 1) ?>
                                                    </div>
                                                <?php endif; ?>
                                                
                                                <h6 class="fw-bold mb-1"><?= html_escape($c['first_name'] . ' ' . $c['last_name']) ?></h6>
                                            </div>
                                            
                                            <?php if ($c['manifesto']): ?>
                                                <button type="button" class="btn btn-sm btn-link p-0 mt-2 text-decoration-none" data-bs-toggle="modal" data-bs-target="#manifestoModal<?= $c['id'] ?>">
                                                    Read Manifesto
                                                </button>
                                                
                                                <!-- Manifesto Modal -->
                                                <div class="modal fade" id="manifestoModal<?= $c['id'] ?>" tabindex="-1" aria-hidden="true" onclick="event.stopPropagation()">
                                                    <div class="modal-dialog modal-dialog-centered">
                                                        <div class="modal-content">
                                                            <div class="modal-header border-0 pb-0">
                                                                <h5 class="modal-title fw-bold"><?= html_escape($c['first_name']) ?>'s Manifesto</h5>
                                                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                            </div>
                                                            <div class="modal-body text-start pt-2">
                                                                <p style="white-space: pre-wrap;"><?= html_escape($c['manifesto']) ?></p>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            <?php endif; ?>
                                        </div>
                                        <div class="selection-indicator"><i class="fa-solid fa-check"></i></div>
                                    </label>
                                </div>
                            <?php endforeach; ?>

                        </div>
                    <?php endif; ?>
                </div>
            </div>
        <?php endforeach; ?>
        
        <div class="card border-0 bg-transparent mb-5 pb-5">
            <div class="card-body text-center pt-4 border-top">
                <h5 class="mb-4">Are you ready to submit your ballot?</h5>
                <p class="text-muted small mb-4">Make sure you have reviewed your selections. This action cannot be undone.</p>
                <button type="button" class="btn btn-primary btn-lg px-5 rounded-pill shadow-lg btn-confirm" data-message="Submit your ballot now? You cannot change your votes after submission.">
                    <i class="fa-solid fa-paper-plane me-2"></i> Submit Ballot
                </button>
            </div>
        </div>

    <?php endif; ?>

<?= form_close() ?>

<script>
// Specific logic to handle max votes per position area
document.addEventListener('DOMContentLoaded', () => {
    const ballotData = <?= json_encode($ballot_data) ?>;
    
    Object.keys(ballotData).forEach(positionId => {
        const maxVotes = parseInt(ballotData[positionId].max_votes) || 1;
        const container = document.getElementById(`position-${positionId}`);
        if(!container) return;
        
        const cards = container.querySelectorAll('.candidate-card');
        
        cards.forEach(card => {
            card.addEventListener('click', function(e) {
                // Prevent modal clicks from triggering selection
                if(e.target.closest('.modal') || e.target.closest('[data-bs-toggle]')) return;
                
                const input = this.querySelector('input');
                const isAbstain = input.value === 'abstain';
                
                if (input.type === 'radio') {
                    cards.forEach(c => c.classList.remove('selected'));
                    this.classList.add('selected');
                    input.checked = true;
                } else {
                    // Checkbox logic for max_votes > 1
                    if (this.classList.contains('selected')) {
                        this.classList.remove('selected');
                        input.checked = false;
                    } else {
                        // If clicking abstain, clear others
                        if (isAbstain) {
                            cards.forEach(c => {
                                c.classList.remove('selected');
                                c.querySelector('input').checked = false;
                            });
                            this.classList.add('selected');
                            input.checked = true;
                        } else {
                            // If clicking a candidate, clear abstain
                            const abstainCard = Array.from(cards).find(c => c.querySelector('input').value === 'abstain');
                            if(abstainCard && abstainCard.classList.contains('selected')) {
                                abstainCard.classList.remove('selected');
                                abstainCard.querySelector('input').checked = false;
                            }
                            
                            const currentSelected = container.querySelectorAll('.candidate-card.selected').length;
                            if (currentSelected < maxVotes) {
                                this.classList.add('selected');
                                input.checked = true;
                            } else {
                                alert(`You can only select a maximum of ${maxVotes} candidates for this position.`);
                            }
                        }
                    }
                }
            });
        });
        
        // Initialize state (select abstain by default for radios)
        const checked = container.querySelector('input:checked');
        if(checked) {
            checked.closest('.candidate-card').classList.add('selected');
        }
    });
});
</script>
