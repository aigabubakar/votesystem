/**
 * NUSSA Vote – Main JavaScript
 */

// ── Auto-dismiss alerts ──────────────────────────────────────
document.querySelectorAll('.alert').forEach(alert => {
    setTimeout(() => {
        alert.style.opacity = '0';
        alert.style.transform = 'translateY(-10px)';
        alert.style.transition = 'all 0.5s ease';
        setTimeout(() => alert.remove(), 500);
    }, 5000);
});

// ── Mobile sidebar toggle ─────────────────────────────────────
const sidebarToggle = document.getElementById('sidebarToggle');
const sidebar       = document.getElementById('sidebar');
const overlay       = document.getElementById('sidebarOverlay');

if (sidebarToggle && sidebar) {
    sidebarToggle.addEventListener('click', () => {
        sidebar.classList.toggle('open');
        overlay && overlay.classList.toggle('show');
    });
    overlay && overlay.addEventListener('click', () => {
        sidebar.classList.remove('open');
        overlay.classList.remove('show');
    });
}

// ── Candidate selection ───────────────────────────────────────
document.querySelectorAll('.candidate-card[data-candidate]').forEach(card => {
    card.addEventListener('click', function () {
        if (this.classList.contains('voted')) return;

        const positionId = this.dataset.position;
        const input      = document.getElementById('vote_' + positionId);

        // Deselect siblings
        document.querySelectorAll(`.candidate-card[data-position="${positionId}"]`).forEach(c => {
            c.classList.remove('selected');
        });

        // Select this
        this.classList.add('selected');
        if (input) input.value = this.dataset.candidate;
    });
});

// ── Confirm delete ────────────────────────────────────────────
window.confirmDelete = function (url, name = 'this item') {
    const overlay = document.getElementById('confirmOverlay');
    const msg     = document.getElementById('confirmMsg');
    const btn     = document.getElementById('confirmBtn');

    if (!overlay) {
        if (confirm(`Delete ${name}? This cannot be undone.`)) {
            window.location.href = url;
        }
        return;
    }

    msg.textContent = `Are you sure you want to delete "${name}"? This action cannot be undone.`;
    btn.href = url;
    overlay.classList.add('show');
};

window.closeConfirm = function () {
    const overlay = document.getElementById('confirmOverlay');
    if (overlay) overlay.classList.remove('show');
};

// ── Vote via AJAX ─────────────────────────────────────────────
window.submitVote = function (electionId, positionId) {
    const input     = document.getElementById('vote_' + positionId);
    const candidateId = input ? input.value : null;

    if (!candidateId) {
        showToast('Please select a candidate first.', 'warning');
        return;
    }

    const btn = document.getElementById('voteBtn_' + positionId);
    if (btn) {
        btn.disabled = true;
        btn.innerHTML = '<span class="spinner"></span> Recording...';
    }

    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content || '';
    const csrfName  = document.querySelector('meta[name="csrf-name"]')?.content || 'nussa_csrf';

    const formData = new FormData();
    formData.append('election_id',  electionId);
    formData.append('position_id',  positionId);
    formData.append('candidate_id', candidateId);
    formData.append(csrfName, csrfToken);

    fetch(BASE_URL + 'voter/cast', {
        method: 'POST',
        body: formData,
        headers: { 'X-Requested-With': 'XMLHttpRequest' }
    })
    .then(res => res.json())
    .then(data => {
        if (data.success) {
            showToast(data.message || 'Vote recorded!', 'success');
            // Mark position as voted
            const section = document.getElementById('pos_section_' + positionId);
            if (section) {
                const selected = section.querySelector('.candidate-card.selected');
                if (selected) {
                    selected.classList.add('voted');
                    selected.classList.remove('selected');
                    selected.querySelector('.vote-checkmark') && selected.querySelector('.vote-checkmark').classList.remove('hidden');
                }
                const voteBtn = section.querySelector('.vote-btn-wrap');
                if (voteBtn) {
                    voteBtn.innerHTML = '<span class="badge badge-green">✓ Vote Recorded</span>';
                }
            }
            // Update CSRF token
            if (data.csrf_token && data.csrf_name) {
                document.querySelector('meta[name="csrf-token"]').content = data.csrf_token;
            }
        } else {
            showToast(data.message || 'Failed to record vote.', 'danger');
            if (btn) {
                btn.disabled = false;
                btn.innerHTML = 'Cast Vote';
            }
        }
    })
    .catch(() => {
        showToast('Network error. Please try again.', 'danger');
        if (btn) {
            btn.disabled = false;
            btn.innerHTML = 'Cast Vote';
        }
    });
};

// ── Toast Notifications ───────────────────────────────────────
window.showToast = function (message, type = 'info') {
    const container = getOrCreateToastContainer();
    const toast = document.createElement('div');
    toast.className = `alert alert-${type}`;
    toast.style.cssText = 'margin-bottom: 10px; animation: slide-down 0.3s ease;';
    toast.innerHTML = `${getIcon(type)} ${message}`;
    container.appendChild(toast);
    setTimeout(() => {
        toast.style.opacity = '0';
        toast.style.transform = 'translateY(-10px)';
        toast.style.transition = 'all 0.4s ease';
        setTimeout(() => toast.remove(), 400);
    }, 4000);
};

function getOrCreateToastContainer() {
    let c = document.getElementById('toastContainer');
    if (!c) {
        c = document.createElement('div');
        c.id = 'toastContainer';
        c.style.cssText = 'position:fixed;top:20px;right:20px;z-index:9999;width:340px;';
        document.body.appendChild(c);
    }
    return c;
}

function getIcon(type) {
    const icons = { success: '✓', danger: '✕', warning: '⚠', info: 'ℹ' };
    return icons[type] || 'ℹ';
}

// ── Dynamic position loading (Candidates form) ─────────────────
const electionSelect  = document.getElementById('election_id_select');
const positionSelect  = document.getElementById('position_id_select');

if (electionSelect && positionSelect) {
    electionSelect.addEventListener('change', function () {
        const eid = this.value;
        positionSelect.innerHTML = '<option value="">Loading...</option>';
        if (!eid) { positionSelect.innerHTML = '<option value="">-- Select Election First --</option>'; return; }

        fetch(BASE_URL + 'admin/positions/' + eid, {
            headers: { 'X-Requested-With': 'XMLHttpRequest' }
        })
        .then(r => r.json())
        .then(positions => {
            positionSelect.innerHTML = '<option value="">-- Select Position --</option>';
            positions.forEach(p => {
                const o = document.createElement('option');
                o.value = p.id;
                o.textContent = p.title;
                positionSelect.appendChild(o);
            });
        })
        .catch(() => {
            positionSelect.innerHTML = '<option value="">Error loading positions</option>';
        });
    });
}

// ── Animate stats on load ─────────────────────────────────────
document.querySelectorAll('.stat-value[data-value]').forEach(el => {
    const target  = parseInt(el.dataset.value, 10);
    const duration = 1200;
    const start   = performance.now();
    function step(now) {
        const progress = Math.min((now - start) / duration, 1);
        const eased = 1 - Math.pow(1 - progress, 3);
        el.textContent = Math.floor(eased * target).toLocaleString();
        if (progress < 1) requestAnimationFrame(step);
    }
    requestAnimationFrame(step);
});

// ── Results chart bars ────────────────────────────────────────
window.addEventListener('load', () => {
    document.querySelectorAll('.vote-bar[data-pct]').forEach(bar => {
        const pct = parseFloat(bar.dataset.pct);
        setTimeout(() => { bar.style.width = pct + '%'; }, 300);
    });
});
