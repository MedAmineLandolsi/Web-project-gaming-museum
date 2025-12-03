// validation.js
class FormValidator {
    constructor(formId) {
        this.form = document.getElementById(formId);
        this.errors = [];
    }
    
    required(value, fieldName) {
        if (!value || value.trim() === '') {
            this.errors.push(`${fieldName} est obligatoire`);
            return false;
        }
        return true;
    }
    
    minLength(value, min, fieldName) {
        if (value.length < min) {
            this.errors.push(`${fieldName} doit faire au moins ${min} caractères`);
            return false;
        }
        return true;
    }
    
    maxLength(value, max, fieldName) {
        if (value.length > max) {
            this.errors.push(`${fieldName} ne doit pas dépasser ${max} caractères`);
            return false;
        }
        return true;
    }
    
    isEmail(email) {
        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        if (!emailRegex.test(email)) {
            this.errors.push('Format d\'email invalide');
            return false;
        }
        return true;
    }
    
    isUrl(url) {
        try {
            new URL(url);
            return true;
        } catch (_) {
            this.errors.push('URL invalide');
            return false;
        }
    }
    
    isAlpha(value, fieldName) {
        const alphaRegex = /^[a-zA-ZÀ-ÿ\s\-]+$/;
        if (!alphaRegex.test(value)) {
            this.errors.push(`${fieldName} ne doit contenir que des lettres`);
            return false;
        }
        return true;
    }
    
    showErrors() {
        const container = this.form.parentElement;
        const oldAlerts = container.querySelectorAll('.alert-danger');
        oldAlerts.forEach(alert => alert.remove());
        
        if (this.errors.length > 0) {
            const errorHtml = this.errors.map(error => 
                `<div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <i class="fas fa-exclamation-triangle me-2"></i>
                    ${error}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>`
            ).join('');
            
            this.form.insertAdjacentHTML('beforebegin', errorHtml);
        }
    }
    
    clearErrors() {
        this.errors = [];
    }
    
    isValid() {
        return this.errors.length === 0;
    }
}