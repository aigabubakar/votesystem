/**
 * NUSSA Vote – Application Scripts
 */

document.addEventListener('DOMContentLoaded', () => {

    // ----------------------------------------------------------------
    // 1. Sidebar Toggle (Admin)
    // ----------------------------------------------------------------
    const sidebar = document.getElementById('sidebar');
    const sidebarToggle = document.getElementById('sidebar-toggle');
    const sidebarOverlay = document.getElementById('sidebar-overlay');

    if (sidebarToggle && sidebar && sidebarOverlay) {
        sidebarToggle.addEventListener('click', () => {
            sidebar.classList.toggle('show');
            sidebarOverlay.classList.toggle('show');
        });

        sidebarOverlay.addEventListener('click', () => {
            sidebar.classList.remove('show');
            sidebarOverlay.classList.remove('show');
        });
    }

    // ----------------------------------------------------------------
    // 2. Navbar Toggle (Voter Mobile)
    // ----------------------------------------------------------------
    const voterNav = document.getElementById('voter-nav');
    const voterToggle = document.getElementById('voter-toggle');

    if (voterToggle && voterNav) {
        voterToggle.addEventListener('click', () => {
            voterNav.classList.toggle('show');
        });
    }

    // ----------------------------------------------------------------
    // 3. Flash Messages Auto-dismiss
    // ----------------------------------------------------------------
    const flashAlerts = document.querySelectorAll('.alert-dismissible:not(.no-auto-dismiss)');
    flashAlerts.forEach(alert => {
        setTimeout(() => {
            if (typeof bootstrap !== 'undefined') {
                const bsAlert = new bootstrap.Alert(alert);
                bsAlert.close();
            } else {
                alert.remove();
            }
        }, 5000);
    });

    // ----------------------------------------------------------------
    // 4. Confirm Dialogs (Delete actions)
    // ----------------------------------------------------------------
    const confirmButtons = document.querySelectorAll('.btn-confirm');
    confirmButtons.forEach(btn => {
        btn.addEventListener('click', function(e) {
            e.preventDefault();
            const url = this.getAttribute('href') || this.dataset.url;
            const message = this.dataset.message || 'Are you sure you want to perform this action?';
            
            showConfirmDialog(message, () => {
                if (url) {
                    window.location.href = url;
                } else if (this.closest('form')) {
                    this.closest('form').submit();
                }
            });
        });
    });

    function showConfirmDialog(message, onConfirm) {
        // Create modal structure
        const overlay = document.createElement('div');
        overlay.className = 'confirm-overlay';
        
        const box = document.createElement('div');
        box.className = 'confirm-box';
        
        const title = document.createElement('h5');
        title.innerText = 'Please Confirm';
        
        const text = document.createElement('p');
        text.innerText = message;
        
        const actions = document.createElement('div');
        actions.className = 'd-flex gap-2 justify-content-center mt-3';
        
        const btnCancel = document.createElement('button');
        btnCancel.className = 'btn btn-outline-secondary btn-sm px-4';
        btnCancel.innerText = 'Cancel';
        
        const btnOk = document.createElement('button');
        btnOk.className = 'btn btn-danger btn-sm px-4';
        btnOk.innerText = 'Confirm';
        
        // Assemble
        actions.appendChild(btnCancel);
        actions.appendChild(btnOk);
        box.appendChild(title);
        box.appendChild(text);
        box.appendChild(actions);
        overlay.appendChild(box);
        document.body.appendChild(overlay);
        
        // Events
        const close = () => overlay.remove();
        btnCancel.addEventListener('click', close);
        overlay.addEventListener('click', (e) => {
            if (e.target === overlay) close();
        });
        
        btnOk.addEventListener('click', () => {
            const btnText = btnOk.innerText;
            btnOk.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>';
            btnOk.disabled = true;
            onConfirm();
            // Don't close immediately if navigating, let the browser handle it
            setTimeout(close, 2000); 
        });
    }

    // ----------------------------------------------------------------
    // 5. Button Loading State on Form Submit
    // ----------------------------------------------------------------
    const formsWithLoading = document.querySelectorAll('form:not(.no-loading)');
    formsWithLoading.forEach(form => {
        form.addEventListener('submit', function() {
            // Check HTML5 validation
            if (!this.checkValidity()) {
                return;
            }
            
            const submitBtns = this.querySelectorAll('button[type="submit"]');
            submitBtns.forEach(btn => {
                if (!btn.classList.contains('btn-loading')) {
                    btn.classList.add('btn-loading');
                }
            });
        });
    });

    // ----------------------------------------------------------------
    // 6. Real-time Countdown Timer
    // ----------------------------------------------------------------
    const countdownElements = document.querySelectorAll('.countdown-timer');
    if (countdownElements.length > 0) {
        setInterval(() => {
            const now = new Date().getTime();
            
            countdownElements.forEach(el => {
                const endTime = new Date(el.dataset.endtime).getTime();
                const distance = endTime - now;
                
                if (distance < 0) {
                    el.innerHTML = "Expired";
                    el.classList.remove('text-danger');
                    el.classList.add('text-muted');
                    return;
                }
                
                const days = Math.floor(distance / (1000 * 60 * 60 * 24));
                const hours = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
                const minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
                const seconds = Math.floor((distance % (1000 * 60)) / 1000);
                
                let text = "";
                if (days > 0) text += days + "d ";
                if (hours > 0 || days > 0) text += hours + "h ";
                text += minutes + "m " + seconds + "s";
                
                el.innerHTML = "Closes in: " + text;
            });
        }, 1000);
    }

});
