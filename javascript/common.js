function showAlert(title, message, type) {
    const alertDiv = document.createElement('div');
    alertDiv.style.cssText = `
        position: fixed;
        top: 20px;
        right: 20px;
        padding: 20px;
        background: ${type === 'success' ? '#28a745' : type === 'error' ? '#dc3545' : '#0040ff'};
        color: white;
        border-radius: 5px;
        box-shadow: 0 4px 6px rgba(0,0,0,0.3);
        z-index: 10000;
        min-width: 300px;
        animation: slideIn 0.3s ease;
    `;
    
    alertDiv.innerHTML = `
        <strong>${title}</strong>
        ${message ? '<br>' + message : ''}
        <button onclick="this.parentElement.remove()" style="float: right; background: none; border: none; color: white; font-size: 20px; cursor: pointer; margin-left: 20px;">&times;</button>
    `;
    
    document.body.appendChild(alertDiv);
    
    setTimeout(() => {
        if (alertDiv.parentElement) {
            alertDiv.style.animation = 'slideOut 0.3s ease';
            setTimeout(() => alertDiv.remove(), 300);
        }
    }, 3000);
}

function showConfirm(title, message, callback) {
    const overlay = document.createElement('div');
    overlay.style.cssText = `
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(0,0,0,0.5);
        z-index: 10000;
        display: flex;
        justify-content: center;
        align-items: center;
    `;
    
    const confirmDiv = document.createElement('div');
    confirmDiv.style.cssText = `
        background: white;
        padding: 30px;
        border-radius: 10px;
        max-width: 400px;
        box-shadow: 0 4px 6px rgba(0,0,0,0.3);
    `;
    
    confirmDiv.innerHTML = `
        <h3 style="margin-top: 0;">${title}</h3>
        <p>${message}</p>
        <div style="text-align: right; margin-top: 20px;">
            <button class="btn btn-secondary" onclick="this.closest('div').parentElement.remove()" style="margin-right: 10px;">Cancel</button>
            <button class="btn btn-danger" onclick="
                this.closest('div').parentElement.remove();
                if (typeof ${callback.name} === 'function') ${callback.name}();
            ">Confirm</button>
        </div>
    `;
    
    overlay.appendChild(confirmDiv);
    document.body.appendChild(overlay);
    
    const cancelBtn = confirmDiv.querySelector('.btn-secondary');
    const confirmBtn = confirmDiv.querySelector('.btn-danger');
    
    cancelBtn.onclick = () => overlay.remove();
    confirmBtn.onclick = () => {
        overlay.remove();
        if (callback) callback();
    };
}

const style = document.createElement('style');
style.textContent = `
    @keyframes slideIn {
        from {
            transform: translateX(400px);
            opacity: 0;
        }
        to {
            transform: translateX(0);
            opacity: 1;
        }
    }
    @keyframes slideOut {
        from {
            transform: translateX(0);
            opacity: 1;
        }
        to {
            transform: translateX(400px);
            opacity: 0;
        }
    }
`;
document.head.appendChild(style);

