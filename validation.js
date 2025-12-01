// Validation patterns
const patterns = {
    username: /^[a-zA-Z0-9_]{3,20}$/,
    email: /^[^\s@]+@[^\s@]+\.[^\s@]+$/,
    password: /^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&#])[A-Za-z\d@$!%*?&#]{8,}$/,
    phone: /^[\d\s\-\+\(\)]{10,20}$/,
    name: /^[a-zA-Z\s]{2,50}$/
};

// Error messages
const errorMessages = {
    username: {
        empty: 'Username is required',
        invalid: 'Username must be 3-20 characters (letters, numbers, underscore only)'
    },
    email: {
        empty: 'Email is required',
        invalid: 'Please enter a valid email address'
    },
    password: {
        empty: 'Password is required',
        invalid: 'Password must be at least 8 characters with uppercase, lowercase, number and special character'
    },
    confirmPassword: {
        empty: 'Please confirm your password',
        mismatch: 'Passwords do not match'
    },
    firstName: {
        empty: 'First name is required',
        invalid: 'First name must be 2-50 characters (letters only)'
    },
    lastName: {
        empty: 'Last name is required',
        invalid: 'Last name must be 2-50 characters (letters only)'
    },
    phone: {
        invalid: 'Please enter a valid phone number'
    },
    dob: {
        empty: 'Date of birth is required',
        invalid: 'You must be at least 13 years old'
    },
    gender: {
        empty: 'Please select your gender'
    }
};

// Show error message
function showError(input, message) {
    const formGroup = input.closest('.form-group');
    let errorElement = formGroup.querySelector('.error-message');
    
    if (!errorElement) {
        errorElement = document.createElement('span');
        errorElement.className = 'error-message';
        formGroup.appendChild(errorElement);
    }
    
    errorElement.textContent = message;
    errorElement.style.display = 'block';
    input.style.borderColor = '#FF0055';
}
function showRecaptchaError(message) {
    const recaptchaElement = document.querySelector('.g-recaptcha');
    if (!recaptchaElement) return;
    
    let errorElement = recaptchaElement.parentElement.querySelector('.error-message');
    
    if (!errorElement) {
        errorElement = document.createElement('span');
        errorElement.className = 'error-message';
        errorElement.style.display = 'block';
        errorElement.style.marginTop = '0.5rem';
        recaptchaElement.parentElement.appendChild(errorElement);
    }
    
    errorElement.textContent = message;
    errorElement.style.display = 'block';
}

// Clear reCAPTCHA error
function clearRecaptchaError() {
    const recaptchaElement = document.querySelector('.g-recaptcha');
    if (!recaptchaElement) return;
    
    const errorElement = recaptchaElement.parentElement.querySelector('.error-message');
    if (errorElement) {
        errorElement.style.display = 'none';
    }
}

// Clear error message
function clearError(input) {
    const formGroup = input.closest('.form-group');
    const errorElement = formGroup.querySelector('.error-message');
    
    if (errorElement) {
        errorElement.style.display = 'none';
    }
    input.style.borderColor = 'rgba(189, 0, 255, 0.3)';
}
// Validate reCAPTCHA
function validateRecaptcha() {
    const recaptchaResponse = grecaptcha.getResponse();
    
    if (!recaptchaResponse || recaptchaResponse.length === 0) {
        showRecaptchaError(errorMessages.recaptcha.empty);
        return false;
    }
    
    clearRecaptchaError();
    return true;
}
// Validate field
function validateField(input, fieldType) {
    const value = input.value.trim();
    
    // Check if empty
    if (!value && errorMessages[fieldType]?.empty) {
        showError(input, errorMessages[fieldType].empty);
        return false;
    }
    
    // Validate pattern
    switch (fieldType) {
        case 'username':
            if (!patterns.username.test(value)) {
                showError(input, errorMessages.username.invalid);
                return false;
            }
            break;
            
        case 'email':
            if (!patterns.email.test(value)) {
                showError(input, errorMessages.email.invalid);
                return false;
            }
            break;
            
        case 'password':
            if (!patterns.password.test(value)) {
                showError(input, errorMessages.password.invalid);
                return false;
            }
            break;
            
        case 'confirmPassword':
            const password = document.getElementById('password').value;
            if (value !== password) {
                showError(input, errorMessages.confirmPassword.mismatch);
                return false;
            }
            break;
            
        case 'firstName':
        case 'lastName':
            if (!patterns.name.test(value)) {
                showError(input, errorMessages[fieldType].invalid);
                return false;
            }
            break;
            
        case 'phone':
            if (value && !patterns.phone.test(value)) {
                showError(input, errorMessages.phone.invalid);
                return false;
            }
            break;
            
        case 'dob':
            const birthDate = new Date(value);
            const today = new Date();
            const age = today.getFullYear() - birthDate.getFullYear();
            
            if (age < 13) {
                showError(input, errorMessages.dob.invalid);
                return false;
            }
            break;
            
        case 'gender':
            if (!value) {
                showError(input, errorMessages.gender.empty);
                return false;
            }
            break;
    }
    
    clearError(input);
    return true;
}

// Real-time validation
function setupRealtimeValidation() {
    const inputs = {
        username: document.getElementById('username'),
        email: document.getElementById('email'),
        password: document.getElementById('password'),
        confirmpass: document.getElementById('confirmpass'),
        first_name: document.getElementById('first_name'),
        last_name: document.getElementById('last_name'),
        phone_number: document.getElementById('phone_number'),
        dob: document.getElementById('dob'),
        gender: document.getElementById('gender')
    };
    
    // Add blur event listeners
    if (inputs.username) {
        inputs.username.addEventListener('blur', () => validateField(inputs.username, 'username'));
    }
    
    if (inputs.email) {
        inputs.email.addEventListener('blur', () => validateField(inputs.email, 'email'));
    }
    
    if (inputs.password) {
        inputs.password.addEventListener('blur', () => validateField(inputs.password, 'password'));
    }
    
    if (inputs.confirmpass) {
        inputs.confirmpass.addEventListener('blur', () => validateField(inputs.confirmpass, 'confirmPassword'));
    }
    
    if (inputs.first_name) {
        inputs.first_name.addEventListener('blur', () => validateField(inputs.first_name, 'firstName'));
    }
    
    if (inputs.last_name) {
        inputs.last_name.addEventListener('blur', () => validateField(inputs.last_name, 'lastName'));
    }
    
    if (inputs.phone_number) {
        inputs.phone_number.addEventListener('blur', () => validateField(inputs.phone_number, 'phone'));
    }
    
    if (inputs.dob) {
        inputs.dob.addEventListener('blur', () => validateField(inputs.dob, 'dob'));
    }
    
    if (inputs.gender) {
        inputs.gender.addEventListener('change', () => validateField(inputs.gender, 'gender'));
    }
}

// Validate signup form
function validateSignupForm() {
    const username = document.getElementById('username');
    const email = document.getElementById('email');
    const password = document.getElementById('password');
    const confirmpass = document.getElementById('confirmpass');
    const dob = document.getElementById('dob');
    const gender = document.getElementById('gender');
    
    let isValid = true;
    
    isValid = validateField(username, 'username') && isValid;
    isValid = validateField(email, 'email') && isValid;
    isValid = validateField(password, 'password') && isValid;
    isValid = validateField(confirmpass, 'confirmPassword') && isValid;
    isValid = validateField(dob, 'dob') && isValid;
    isValid = validateField(gender, 'gender') && isValid;
    
    return isValid;
}

// Validate login form
function validateLoginForm() {
    const username = document.getElementById('loginUsername');
    const password = document.getElementById('loginPassword');
    
    let isValid = true;
    
    if (!username.value.trim()) {
        showError(username, 'Username or email is required');
        isValid = false;
    } else {
        clearError(username);
    }
    
    if (!password.value.trim()) {
        showError(password, 'Password is required');
        isValid = false;
    } else {
        clearError(password);
    }
    if (!validateRecaptcha()) {
        isValid = false;
    }
    
    return isValid;
}

// Initialize validation on page load
document.addEventListener('DOMContentLoaded', function() {
    setupRealtimeValidation();
    
    // Handle signup form submission
    const signupForm = document.querySelector('form[action*="signup"]');
    if (signupForm) {
        signupForm.addEventListener('submit', function(e) {
            e.preventDefault();
            
            if (validateSignupForm()) {
                const formData = new FormData(this);
                formData.append('action', 'register');
                
                fetch('../controller/userController.php', {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        alert(data.message);
                        window.location.href = 'login.php';
                    } else {
                        alert(data.message);
                    }
                })
                .catch(error => {
                    alert('An error occurred. Please try again.');
                });
            }
        });
    }
    
    // Handle login form submission
    const loginForm = document.querySelector('form[action*="login"]');
    if (loginForm) {
        loginForm.addEventListener('submit', function(e) {
            e.preventDefault();
            
            if (validateLoginForm()) {
                const formData = new FormData(this);
                formData.append('action', 'login');
                const recaptchaResponse = grecaptcha.getResponse();
                formData.append('g-recaptcha-response', recaptchaResponse);
                
                fetch('../controller/userController.php', {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        if (data.role === 'admin') {
                            window.location.href = '../view/backoffice/dashboard.php';
                        } else {
                            window.location.href = '../view/frontoffice/index.php';
                        }
                    } else {
                        alert(data.message);
                        grecaptcha.reset();
                    }
                })
                .catch(error => {
                    alert('An error occurred. Please try again.');
                });
            }
        });
    }
});
