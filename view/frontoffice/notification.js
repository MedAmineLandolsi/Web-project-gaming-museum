// Custom Notification System for Ludology Vault
// DO NOT override native functions - provide alternatives instead

const notificationStyles = document.createElement('style');
notificationStyles.textContent = `
    .custom-notification-overlay {
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(0, 0, 0, 0.85);
        display: flex;
        align-items: center;
        justify-content: center;
        z-index: 99999;
        opacity: 0;
        visibility: hidden;
        transition: all 0.3s;
        backdrop-filter: blur(5px);
    }

    .custom-notification-overlay.show {
        opacity: 1;
        visibility: visible;
    }

    .custom-notification-box {
        background: linear-gradient(135deg, #1a1a1a 0%, #0a0a0a 100%);
        border: 3px solid #00FF41;
        padding: 2.5rem;
        max-width: 500px;
        width: 90%;
        box-shadow: 0 20px 60px rgba(0, 255, 65, 0.5);
        position: relative;
        transform: scale(0.7);
        transition: transform 0.3s;
        animation: notificationPulse 0.5s ease-out;
    }

    .custom-notification-overlay.show .custom-notification-box {
        transform: scale(1);
    }

    @keyframes notificationPulse {
        0% {
            transform: scale(0.3);
            opacity: 0;
        }
        50% {
            transform: scale(1.05);
        }
        100% {
            transform: scale(1);
            opacity: 1;
        }
    }

    .custom-notification-box::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background-image: 
            repeating-linear-gradient(
                0deg,
                transparent,
                transparent 2px,
                rgba(0, 255, 65, 0.03) 2px,
                rgba(0, 255, 65, 0.03) 4px
            );
        pointer-events: none;
    }

    .custom-notification-box.success {
        border-color: #00FF41;
        box-shadow: 0 20px 60px rgba(0, 255, 65, 0.5);
    }

    .custom-notification-box.error {
        border-color: #FF0055;
        box-shadow: 0 20px 60px rgba(255, 0, 85, 0.5);
    }

    .custom-notification-box.warning {
        border-color: #FF9500;
        box-shadow: 0 20px 60px rgba(255, 149, 0, 0.5);
    }

    .custom-notification-box.info {
        border-color: #BD00FF;
        box-shadow: 0 20px 60px rgba(189, 0, 255, 0.5);
    }

    .notification-header {
        display: flex;
        align-items: center;
        gap: 1rem;
        margin-bottom: 1.5rem;
        padding-bottom: 1rem;
        border-bottom: 2px solid currentColor;
    }

    .notification-icon {
        font-size: 2.5rem;
        animation: iconBounce 0.6s ease-out;
    }

    @keyframes iconBounce {
        0%, 100% { transform: translateY(0); }
        50% { transform: translateY(-10px); }
    }

    .notification-title {
        font-family: 'Press Start 2P', cursive;
        font-size: 0.9rem;
        color: #00FF41;
        text-shadow: 0 0 10px currentColor;
        letter-spacing: 2px;
    }

    .custom-notification-box.success .notification-title {
        color: #00FF41;
    }

    .custom-notification-box.error .notification-title {
        color: #FF0055;
    }

    .custom-notification-box.warning .notification-title {
        color: #FF9500;
    }

    .custom-notification-box.info .notification-title {
        color: #BD00FF;
    }

    .notification-message {
        font-family: 'VT323', monospace;
        font-size: 1.3rem;
        color: #aaaaaa;
        line-height: 1.6;
        margin-bottom: 2rem;
    }

    .notification-buttons {
        display: flex;
        gap: 1rem;
        justify-content: center;
        flex-wrap: wrap;
    }

    .notification-btn {
        padding: 0.8rem 2rem;
        font-family: 'Press Start 2P', cursive;
        font-size: 0.6rem;
        cursor: pointer;
        transition: all 0.3s;
        border: 2px solid;
        background: transparent;
        letter-spacing: 1px;
    }

    .notification-btn-confirm {
        border-color: #00FF41;
        color: #00FF41;
    }

    .notification-btn-confirm:hover {
        background: #00FF41;
        color: #0a0a0a;
        box-shadow: 0 0 20px rgba(0, 255, 65, 0.6);
        transform: translateY(-2px);
    }

    .notification-btn-cancel {
        border-color: #FF0055;
        color: #FF0055;
    }

    .notification-btn-cancel:hover {
        background: #FF0055;
        color: #0a0a0a;
        box-shadow: 0 0 20px rgba(255, 0, 85, 0.6);
        transform: translateY(-2px);
    }

    .notification-btn-ok {
        border-color: #BD00FF;
        color: #BD00FF;
    }

    .notification-btn-ok:hover {
        background: #BD00FF;
        color: #0a0a0a;
        box-shadow: 0 0 20px rgba(189, 0, 255, 0.6);
        transform: translateY(-2px);
    }

    .toast-notification {
        position: fixed;
        top: 2rem;
        right: 2rem;
        background: linear-gradient(135deg, #1a1a1a 0%, #0a0a0a 100%);
        border: 2px solid #00FF41;
        padding: 1.5rem 2rem;
        min-width: 300px;
        max-width: 500px;
        box-shadow: 0 10px 30px rgba(0, 255, 65, 0.4);
        z-index: 99999;
        transform: translateX(150%);
        transition: transform 0.4s cubic-bezier(0.68, -0.55, 0.265, 1.55);
        font-family: 'Press Start 2P', cursive;
        font-size: 0.6rem;
        color: #ffffff;
    }

    .toast-notification.show {
        transform: translateX(0);
    }

    .toast-notification::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background-image: 
            repeating-linear-gradient(
                0deg,
                transparent,
                transparent 2px,
                rgba(0, 255, 65, 0.03) 2px,
                rgba(0, 255, 65, 0.03) 4px
            );
        pointer-events: none;
    }

    .toast-notification.success {
        border-color: #00FF41;
        color: #00FF41;
        box-shadow: 0 10px 30px rgba(0, 255, 65, 0.4);
    }

    .toast-notification.error {
        border-color: #FF0055;
        color: #FF0055;
        box-shadow: 0 10px 30px rgba(255, 0, 85, 0.4);
    }

    .toast-notification.warning {
        border-color: #FF9500;
        color: #FF9500;
        box-shadow: 0 10px 30px rgba(255, 149, 0, 0.4);
    }

    .toast-notification.info {
        border-color: #BD00FF;
        color: #BD00FF;
        box-shadow: 0 10px 30px rgba(189, 0, 255, 0.4);
    }

    .toast-content {
        display: flex;
        align-items: center;
        gap: 1rem;
    }

    .toast-icon {
        font-size: 1.5rem;
    }

    .toast-message {
        flex: 1;
        line-height: 1.4;
    }

    @media (max-width: 768px) {
        .custom-notification-box {
            padding: 2rem;
        }

        .notification-title {
            font-size: 0.7rem;
        }

        .notification-message {
            font-size: 1.1rem;
        }

        .toast-notification {
            right: 1rem;
            top: 1rem;
            min-width: 250px;
            max-width: calc(100% - 2rem);
        }
    }
`;
document.head.appendChild(notificationStyles);

const icons = {
    success: '✓',
    error: '✖',
    warning: '⚠',
    info: 'ℹ'
};

const titles = {
    success: 'SUCCESS',
    error: 'ERROR',
    warning: 'WARNING',
    info: 'INFO'
};

function showNotification(message, type = 'info', showConfirm = false, onConfirm = null, onCancel = null) {
    const existing = document.querySelector('.custom-notification-overlay');
    if (existing) {
        existing.remove();
    }

    const overlay = document.createElement('div');
    overlay.className = 'custom-notification-overlay';

    const box = document.createElement('div');
    box.className = `custom-notification-box ${type}`;

    const header = document.createElement('div');
    header.className = 'notification-header';
    header.innerHTML = `
        <div class="notification-icon">${icons[type]}</div>
        <div class="notification-title">${titles[type]}</div>
    `;

    const messageDiv = document.createElement('div');
    messageDiv.className = 'notification-message';
    messageDiv.textContent = message;

    const buttonsDiv = document.createElement('div');
    buttonsDiv.className = 'notification-buttons';

    if (showConfirm) {
        const confirmBtn = document.createElement('button');
        confirmBtn.className = 'notification-btn notification-btn-confirm';
        confirmBtn.textContent = 'YES';
        confirmBtn.onclick = () => {
            closeNotification(overlay);
            if (onConfirm) onConfirm();
        };

        const cancelBtn = document.createElement('button');
        cancelBtn.className = 'notification-btn notification-btn-cancel';
        cancelBtn.textContent = 'NO';
        cancelBtn.onclick = () => {
            closeNotification(overlay);
            if (onCancel) onCancel();
        };

        buttonsDiv.appendChild(confirmBtn);
        buttonsDiv.appendChild(cancelBtn);
    } else {
        const okBtn = document.createElement('button');
        okBtn.className = 'notification-btn notification-btn-ok';
        okBtn.textContent = 'OK';
        okBtn.onclick = () => closeNotification(overlay);
        buttonsDiv.appendChild(okBtn);
    }

    box.appendChild(header);
    box.appendChild(messageDiv);
    box.appendChild(buttonsDiv);
    overlay.appendChild(box);
    document.body.appendChild(overlay);

    setTimeout(() => overlay.classList.add('show'), 10);

    overlay.addEventListener('click', (e) => {
        if (e.target === overlay) {
            closeNotification(overlay);
            if (showConfirm && onCancel) onCancel();
        }
    });

    if (!showConfirm) {
        setTimeout(() => {
            if (document.body.contains(overlay)) {
                closeNotification(overlay);
            }
        }, 5000);
    }
}

function closeNotification(overlay) {
    overlay.classList.remove('show');
    setTimeout(() => {
        if (document.body.contains(overlay)) {
            overlay.remove();
        }
    }, 300);
}

function showToast(message, type = 'info', duration = 3000) {
    const toast = document.createElement('div');
    toast.className = `toast-notification ${type}`;
    
    const content = document.createElement('div');
    content.className = 'toast-content';
    content.innerHTML = `
        <div class="toast-icon">${icons[type]}</div>
        <div class="toast-message">${message}</div>
    `;
    
    toast.appendChild(content);
    document.body.appendChild(toast);
    
    setTimeout(() => toast.classList.add('show'), 10);
    
    setTimeout(() => {
        toast.classList.remove('show');
        setTimeout(() => {
            if (document.body.contains(toast)) {
                toast.remove();
            }
        }, 400);
    }, duration);
}

// Make functions globally available
window.showNotification = showNotification;
window.showToast = showToast;
window.closeNotification = closeNotification;
