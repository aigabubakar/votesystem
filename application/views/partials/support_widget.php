<!-- Floating Support Widget -->
<style>
.floating-support {
    position: fixed;
    bottom: 30px;
    right: 30px;
    z-index: 1000;
    display: flex;
    flex-direction: column;
    align-items: flex-end;
    gap: 15px;
}
.support-card {
    background: #ffffff;
    border-radius: 15px;
    box-shadow: 0 10px 30px rgba(0,0,0,0.15);
    padding: 20px;
    width: 280px;
    display: none;
    transform: translateY(20px);
    opacity: 0;
    transition: all 0.3s cubic-bezier(0.68, -0.55, 0.265, 1.55);
}
.support-card.show {
    display: block;
    transform: translateY(0);
    opacity: 1;
}
.support-btn {
    width: 60px;
    height: 60px;
    border-radius: 50%;
    background: linear-gradient(135deg, #25D366 0%, #128C7E 100%);
    color: white;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 28px;
    box-shadow: 0 4px 15px rgba(37, 211, 102, 0.4);
    cursor: pointer;
    transition: all 0.3s ease;
    border: none;
    outline: none;
}
.support-btn:hover {
    transform: scale(1.1);
    box-shadow: 0 6px 20px rgba(37, 211, 102, 0.6);
}
.support-btn i {
    transition: transform 0.3s ease;
}
.support-btn.active i {
    transform: rotate(180deg);
}
</style>

<div class="floating-support">
    <div class="support-card" id="supportCard">
        <h6 class="fw-bold mb-2">Need Help? <i class="fa-solid fa-hand-wave text-warning"></i></h6>
        <p class="text-muted small mb-3">Our support team is available to assist you with any voting issues.</p>
        <a href="https://wa.me/2348000000000" target="_blank" class="btn btn-success w-100 rounded-pill fw-bold shadow-sm">
            <i class="fa-brands fa-whatsapp me-2"></i> Chat on WhatsApp
        </a>
    </div>
    
    <button class="support-btn" id="supportBtn" title="Get Support">
        <i class="fa-brands fa-whatsapp"></i>
    </button>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const supportBtn = document.getElementById('supportBtn');
    const supportCard = document.getElementById('supportCard');
    
    if (supportBtn && supportCard) {
        supportBtn.addEventListener('click', function(e) {
            e.preventDefault();
            this.classList.toggle('active');
            
            if (supportCard.classList.contains('show')) {
                supportCard.classList.remove('show');
                setTimeout(() => {
                    if(!supportCard.classList.contains('show')) {
                        supportCard.style.display = 'none';
                    }
                }, 300);
            } else {
                supportCard.style.display = 'block';
                // Trigger reflow
                void supportCard.offsetWidth;
                supportCard.classList.add('show');
            }
        });
        
        // Close when clicking outside
        document.addEventListener('click', function(event) {
            const isClickInside = supportBtn.contains(event.target) || supportCard.contains(event.target);
            if (!isClickInside && supportCard.classList.contains('show')) {
                supportBtn.classList.remove('active');
                supportCard.classList.remove('show');
                setTimeout(() => {
                    supportCard.style.display = 'none';
                }, 300);
            }
        });
    }
});
</script>
