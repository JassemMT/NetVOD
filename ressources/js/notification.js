// Gestion des notifications toast

function closeToast() {
    const toast = document.getElementById('notificationToast');
    if (toast) {
        toast.style.animation = 'slide-out-right 0.3s ease forwards';
        setTimeout(() => toast.remove(), 300);
    }
}

// Auto-fermeture selon la durée définie
document.addEventListener('DOMContentLoaded', function() {
    const toast = document.getElementById('notificationToast');
    
    if (toast) {
        const duration = parseInt(toast.dataset.duration) || 5000;
        
        setTimeout(() => {
            closeToast();
        }, duration);
    }
});