// Notification System with Confirmation Dialog

function showNotification(message, type = 'info', showConfirm = false, onConfirm = null, onCancel = null) {
    // Remove existing notifications
    const existingNotif = document.getElementById('customNotification');
    if (existingNotif) {
        existingNotif.remove();
    }

    const notification = document.createElement('div');
    notification.id = 'customNotification';
    notification.style.cssText = `
        position: fixed;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
        background: linear-gradient(135deg, #1a1a1a, #0a0a0a);
        border: 3px solid ${getColorByType(type)};
        padding: 2rem;
        z-index: 10000;
        min-width: 400px;
        max-width: 500px;
        box-shadow: 0 20px 60px ${getColorByType(type)}66;
        animation: slideIn 0.3s ease;
    `;

    const overlay = document.createElement('div');
    overlay.id = 'notificationOverlay';
    overlay.style.cssText = `
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(0, 0, 0, 0.8);
        z-index: 9999;
        backdrop-filter: blur(5px);
    `;

    const title = document.createElement('div');
    title.style.cssText = `
        font-family: 'Press Start 2P', cursive;
        font-size: 0.7rem;
        color: ${getColorByType(type)};
        margin-bottom: 1rem;
        text-shadow: 0 0 10px ${getColorByType(type)};
        text-align: center;
    `;
    title.textContent = getTitleByType(type);

    const messageDiv = document.createElement('div');
    messageDiv.style.cssText = `
        font-family: 'VT323', monospace;
        font-size: 1.1rem;
        color: #ffffff;
        margin-bottom: 1.5rem;
        line-height: 1.6;
        text-align: center;
        padding: 1rem;
        background: rgba(0, 255, 65, 0.05);
        border-left: 3px solid ${getColorByType(type)};
    `;
    messageDiv.textContent = message;

    const buttonContainer = document.createElement('div');
    buttonContainer.style.cssText = `
        display: flex;
        gap: 1rem;
        justify-content: center;
    `;

    if (showConfirm) {
        const confirmBtn = document.createElement('button');
        confirmBtn.textContent = 'CONFIRMER';
        confirmBtn.style.cssText = `
            flex: 1;
            padding: 0.8rem 1.5rem;
            background: ${getColorByType(type)};
            border: none;
            color: #0a0a0a;
            font-family: 'Press Start 2P', cursive;
            font-size: 0.6rem;
            cursor: pointer;
            transition: all 0.3s;
            font-weight: bold;
        `;
        confirmBtn.onmouseover = () => {
            confirmBtn.style.boxShadow = `0 0 20px ${getColorByType(type)}`;
            confirmBtn.style.transform = 'translateY(-2px)';
        };
        confirmBtn.onmouseout = () => {
            confirmBtn.style.boxShadow = 'none';
            confirmBtn.style.transform = 'translateY(0)';
        };
        confirmBtn.onclick = () => {
            overlay.remove();
            notification.remove();
            if (onConfirm) onConfirm();
        };

        const cancelBtn = document.createElement('button');
        cancelBtn.textContent = 'ANNULER';
        cancelBtn.style.cssText = `
            flex: 1;
            padding: 0.8rem 1.5rem;
            background: transparent;
            border: 2px solid #FF006E;
            color: #FF006E;
            font-family: 'Press Start 2P', cursive;
            font-size: 0.6rem;
            cursor: pointer;
            transition: all 0.3s;
        `;
        cancelBtn.onmouseover = () => {
            cancelBtn.style.background = 'rgba(255, 0, 110, 0.1)';
            cancelBtn.style.boxShadow = '0 0 20px rgba(255, 0, 110, 0.3)';
        };
        cancelBtn.onmouseout = () => {
            cancelBtn.style.background = 'transparent';
            cancelBtn.style.boxShadow = 'none';
        };
        cancelBtn.onclick = () => {
            overlay.remove();
            notification.remove();
            if (onCancel) onCancel();
        };

        buttonContainer.appendChild(confirmBtn);
        buttonContainer.appendChild(cancelBtn);
    } else {
        const okBtn = document.createElement('button');
        okBtn.textContent = 'OK';
        okBtn.style.cssText = `
            padding: 0.8rem 2rem;
            background: ${getColorByType(type)};
            border: none;
            color: #0a0a0a;
            font-family: 'Press Start 2P', cursive;
            font-size: 0.6rem;
            cursor: pointer;
            transition: all 0.3s;
            font-weight: bold;
        `;
        okBtn.onmouseover = () => {
            okBtn.style.boxShadow = `0 0 20px ${getColorByType(type)}`;
            okBtn.style.transform = 'translateY(-2px)';
        };
        okBtn.onmouseout = () => {
            okBtn.style.boxShadow = 'none';
            okBtn.style.transform = 'translateY(0)';
        };
        okBtn.onclick = () => {
            overlay.remove();
            notification.remove();
        };

        buttonContainer.appendChild(okBtn);
    }

    notification.appendChild(title);
    notification.appendChild(messageDiv);
    notification.appendChild(buttonContainer);

    document.body.appendChild(overlay);
    document.body.appendChild(notification);

    // Add animation
    const style = document.createElement('style');
    style.textContent = `
        @keyframes slideIn {
            from {
                opacity: 0;
                transform: translate(-50%, -60%);
            }
            to {
                opacity: 1;
                transform: translate(-50%, -50%);
            }
        }
    `;
    if (!document.head.querySelector('style[data-notification-style]')) {
        style.setAttribute('data-notification-style', 'true');
        document.head.appendChild(style);
    }
}

function showToast(message, type = 'info', duration = 3000) {
    const existingToast = document.querySelector('.custom-toast');
    if (existingToast) {
        existingToast.remove();
    }

    const toast = document.createElement('div');
    toast.className = 'custom-toast';
    toast.style.cssText = `
        position: fixed;
        top: 2rem;
        right: 2rem;
        background: linear-gradient(135deg, #1a1a1a, #0a0a0a);
        border: 2px solid ${getColorByType(type)};
        padding: 1rem 1.5rem;
        z-index: 10001;
        min-width: 300px;
        box-shadow: 0 10px 30px ${getColorByType(type)}66;
        animation: slideInRight 0.3s ease, slideOutRight 0.3s ease ${duration - 300}ms;
        display: flex;
        align-items: center;
    `;

    const icon = document.createElement('span');
    icon.style.cssText = `
        font-size: 1.2rem;
        margin-right: 1rem;
    `;
    icon.textContent = getIconByType(type);

    const messageSpan = document.createElement('span');
    messageSpan.style.cssText = `
        font-family: 'VT323', monospace;
        font-size: 1rem;
        color: #ffffff;
    `;
    messageSpan.textContent = message;

    toast.appendChild(icon);
    toast.appendChild(messageSpan);

    document.body.appendChild(toast);

    // Add animations
    const style = document.createElement('style');
    style.textContent = `
        @keyframes slideInRight {
            from {
                opacity: 0;
                transform: translateX(100%);
            }
            to {
                opacity: 1;
                transform: translateX(0);
            }
        }
        @keyframes slideOutRight {
            from {
                opacity: 1;
                transform: translateX(0);
            }
            to {
                opacity: 0;
                transform: translateX(100%);
            }
        }
    `;
    if (!document.head.querySelector('style[data-toast-style]')) {
        style.setAttribute('data-toast-style', 'true');
        document.head.appendChild(style);
    }

    setTimeout(() => {
        toast.remove();
    }, duration);
}

function getColorByType(type) {
    const colors = {
        success: '#00FF41',
        error: '#FF0055',
        warning: '#FF9500',
        info: '#BD00FF'
    };
    return colors[type] || colors.info;
}

function getTitleByType(type) {
    const titles = {
        success: '◄ SUCCÈS ►',
        error: '◄ ERREUR ►',
        warning: '◄ ATTENTION ►',
        info: '◄ INFORMATION ►'
    };
    return titles[type] || titles.info;
}

function getIconByType(type) {
    const icons = {
        success: '✓',
        error: '✗',
        warning: '⚠',
        info: 'ℹ'
    };
    return icons[type] || icons.info;
}