(function () {
    'use strict';

    const EMAIL_REGEX = /^[^\s@]+@[^\s@]+\.[^\s@]{2,}$/;

    function closestFormGroup(el) {
        return el?.closest?.('.form-group') || el?.parentElement || null;
    }

    function getOrCreateErrorEl(field, id, defaultMessage) {
        const group = closestFormGroup(field);
        if (!group) return null;

        let errorEl = id ? document.getElementById(id) : null;
        if (!errorEl) {
            errorEl = group.querySelector('.error-message');
        }

        if (!errorEl) {
            errorEl = document.createElement('div');
            errorEl.className = 'error-message';
            if (id) errorEl.id = id;
            group.appendChild(errorEl);
        }

        if (defaultMessage && !errorEl.textContent.trim()) {
            errorEl.textContent = defaultMessage;
        }

        return errorEl;
    }

    function setFieldState(field, isValid, errorEl) {
        const group = closestFormGroup(field);
        if (!group) return;

        group.classList.toggle('has-error', !isValid);
        group.classList.toggle('has-success', isValid);

        field.classList.toggle('input-error', !isValid);
        field.classList.toggle('input-valid', isValid);

        if (errorEl) {
            errorEl.classList.toggle('is-visible', !isValid);
            // Fallback: force visibility (avoids CSS specificity issues)
            errorEl.style.display = !isValid ? 'block' : 'none';
        }
    }

    function validateReclamationForm(form) {
        const nom = form.querySelector('#nomClient');
        const email = form.querySelector('#emailClient');
        const type = form.querySelector('#typeReclamation');
        const titre = form.querySelector('#titre');
        const description = form.querySelector('#description');

        let ok = true;

        if (nom) {
            const errorEl = getOrCreateErrorEl(nom, 'nomError', 'Le nom est requis et doit contenir au moins 2 caractères');
            const valid = nom.value.trim().length >= 2;
            setFieldState(nom, valid, errorEl);
            ok = ok && valid;
        }

        if (email) {
            const errorEl = getOrCreateErrorEl(email, 'emailError', 'Veuillez entrer une adresse email valide');
            const valid = EMAIL_REGEX.test(email.value.trim());
            setFieldState(email, valid, errorEl);
            ok = ok && valid;
        }

        if (type) {
            const errorEl = getOrCreateErrorEl(type, 'typeError', 'Veuillez sélectionner un type de réclamation');
            const valid = type.value && type.value.trim() !== '';
            setFieldState(type, valid, errorEl);
            ok = ok && valid;
        }

        if (titre) {
            const errorEl = getOrCreateErrorEl(titre, 'titreError', 'Le titre est requis et doit contenir au moins 5 caractères');
            const valid = titre.value.trim().length >= 5;
            setFieldState(titre, valid, errorEl);
            ok = ok && valid;
        }

        if (description) {
            const errorEl = getOrCreateErrorEl(description, 'descriptionError', 'La description est requise et doit contenir au moins 10 caractères');
            const valid = description.value.trim().length >= 10;
            setFieldState(description, valid, errorEl);
            ok = ok && valid;
        }

        return ok;
    }

    function attachValidation(form) {
        if (!form) return;

        // Disable native HTML5 validation (also set in HTML via novalidate)
        form.noValidate = true;

        const type = form.getAttribute('data-validate');

        if (type === 'reclamation') {
            const fields = ['#nomClient', '#emailClient', '#typeReclamation', '#titre', '#description'];
            fields.forEach(sel => {
                const field = form.querySelector(sel);
                if (!field) return;

                const handler = () => validateReclamationForm(form);
                field.addEventListener('blur', handler);
                field.addEventListener('input', () => {
                    // Light feedback while typing (don’t be too aggressive)
                    if (field.classList.contains('input-error')) handler();
                });
            });

            form.addEventListener('submit', (e) => {
                const valid = validateReclamationForm(form);
                if (!valid) {
                    e.preventDefault();
                    const firstError = form.querySelector('.input-error');
                    firstError?.focus?.();
                }
            });
        }

        if (type === 'reponse') {
            const message = form.querySelector('#messageReponse');
            if (!message) return;

            const errorEl = getOrCreateErrorEl(message, 'messageError', '⚠️ La réponse doit contenir au moins 5 caractères');

            const validate = () => {
                const valid = message.value.trim().length >= 5;
                setFieldState(message, valid, errorEl);
                return valid;
            };

            message.addEventListener('blur', validate);
            message.addEventListener('input', () => {
                if (message.classList.contains('input-error')) validate();
            });

            form.addEventListener('submit', (e) => {
                if (!validate()) {
                    e.preventDefault();
                    message.focus();
                }
            });
        }
    }

    document.addEventListener('DOMContentLoaded', () => {
        document.querySelectorAll('form[data-validate]').forEach(attachValidation);
    });
})();
