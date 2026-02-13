<!-- Toast CSS & JS -->
<style>
.toast {
    position: fixed;
    top: 20px;
    right: 20px;
    min-width: 250px;
    padding: 16px 24px;
    color: white;
    border-radius: 6px;
    box-shadow: 0 4px 12px rgba(0,0,0,0.15);
    font-size: 15px;
    font-weight: 500;
    z-index: 9999;
    animation: slideIn 0.3s ease, fadeOut 0.5s 2.5s forwards;
}
@keyframes slideIn {
    from { right: -100%; opacity: 0; }
    to { right: 20px; opacity: 1; }
}
@keyframes fadeOut {
    to { opacity: 0; visibility: hidden; }
}
</style>
<script>
function showToast(message, type) {
    const toast = document.createElement('div');
    toast.className = 'toast';
    toast.style.backgroundColor = type === 'success' ? '#4CAF50' : '#f44336';
    toast.innerHTML = (type === 'success' ? '✅ ' : '❌ ') + message;
    document.body.appendChild(toast);
    setTimeout(() => toast.remove(), 3000);
}
</script>