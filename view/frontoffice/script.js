// =============================================
// SCRIPT PRINCIPAL - PROJET COMMUNAUTAIRE
// =============================================
// NOTE: Le bloc original des fonctionnalités IA a été temporairement commenté
// car il contenait des définitions hors classe provoquant une erreur de
// syntaxe et empêchait le chargement du reste du script. Les pages qui
// utilisent l'assistant IA incluent déjà des handlers inline depuis le
// contrôleur (`PublicationController::addAIAssistantToForm`) — ces handlers
// restent actifs. Nous garderons ce bloc désactivé pour éviter les conflits
// et corrigerons / refactorerons proprement ultérieurement si besoin.

/*
    // Bloc IA désactivé volontairement (voir commentaire ci-dessus)
*/
// Compatibility patch early: si une instance / prototype `CommunityApp` existe
// (ancienne version chargée avant cette mise à jour), rediriger le nom
// legacy `setupFormSubmissions` vers `setupFormHandling` si disponible.
try {
    if (typeof window !== 'undefined' && typeof window.CommunityApp !== 'undefined' && window.CommunityApp.prototype) {
        if (typeof window.CommunityApp.prototype.setupFormSubmissions !== 'function' &&
            typeof window.CommunityApp.prototype.setupFormHandling === 'function') {
            window.CommunityApp.prototype.setupFormSubmissions = window.CommunityApp.prototype.setupFormHandling;
            console.info('Compat early: setupFormSubmissions() redirigé vers setupFormHandling()');
        }
    }
} catch (e) {
    // Ne pas bloquer le chargement si l'accès échoue
}

// Poller et patcher le prototype si nécessaire : remplace setupEventListeners
// par une enveloppe qui capture les erreurs et applique une fallback safe.
(function patchSetupEventListeners() {
    let attempts = 0;
    const maxAttempts = 20;
    const interval = 100; // ms

    const tryPatch = () => {
        attempts++;
        try {
            if (window.CommunityApp && window.CommunityApp.prototype && !window.CommunityApp.prototype.__patched_setupEventListeners) {
                const proto = window.CommunityApp.prototype;
                if (typeof proto.setupEventListeners === 'function') {
                    const orig = proto.setupEventListeners;
                    proto.setupEventListeners = function(...args) {
                        try {
                            return orig.apply(this, args);
                        } catch (err) {
                            console.warn('Patched wrapper caught error in setupEventListeners:', err);
                            // essayer d'appeler les sous-méthodes de manière safe
                            try {
                                if (typeof this.setupMobileNavigation === 'function') this.setupMobileNavigation();
                                if (typeof this.setupActionButtons === 'function') this.setupActionButtons();
                                if (typeof this.setupCardInteractions === 'function') this.setupCardInteractions();
                                // Form handling fallback
                                if (typeof this.setupFormHandling === 'function') {
                                    this.setupFormHandling();
                                } else if (typeof this.setupFormSubmissions === 'function') {
                                    this.setupFormSubmissions();
                                }
                                if (typeof this.setupModalHandlers === 'function') this.setupModalHandlers();
                            } catch (e2) {
                                console.error('Fallback setupEventListeners also failed:', e2);
                            }
                        }
                    };
                    proto.__patched_setupEventListeners = true;
                    console.info('Prototype CommunityApp.setupEventListeners patched (compat)');
                }
            }
        } catch (e) {
            // ignore
        }

        if (!window.CommunityApp || !window.CommunityApp.prototype || !window.CommunityApp.prototype.__patched_setupEventListeners) {
            if (attempts < maxAttempts) {
                setTimeout(tryPatch, interval);
            } else {
                console.warn('Unable to patch CommunityApp.setupEventListeners after multiple attempts');
            }
        }
    };

    tryPatch();
})();
// Guard global rapide : si le script a déjà été chargé, on stoppe l'exécution
if (window.__communityAppScriptLoaded) {
    console.warn('Script frontoffice déjà chargé - exécution ignorée');
} else {
    window.__communityAppScriptLoaded = true;

    if (typeof window.CommunityApp === 'undefined') {
class CommunityApp {
    constructor() {
        this.init();
    }

    init() {
        this.setupEventListeners();
        this.setupAnimations();
        this.setupFormHandling();
        this.setupUIInteractions();
        this.setupScrollEffects();
        this.initAIAssistant();
    }

    // =============================================
    // CONFIGURATION DES ÉVÉNEMENTS
    // =============================================

    setupEventListeners() {
        // Navigation mobile
        this.setupMobileNavigation();
        
        // Boutons d'action
        this.setupActionButtons();
        
        // Interactions des cartes
        this.setupCardInteractions();
        
        // Gestion des formulaires (compatibilité avec anciennes versions)
        if (this.safeCall('setupFormHandling')) {
            // méthode moderne appelée
        } else if (this.safeCall('setupFormSubmissions')) {
            // fallback legacy
        } else {
            console.warn('Aucune méthode de gestion des formulaires trouvée');
        }
        
        // Modal interactions
        this.setupModalHandlers();
    }

    setupMobileNavigation() {
        const mobileMenu = document.getElementById('mobileMenu');
        const navLinks = document.getElementById('navLinks');

        if (mobileMenu && navLinks) {
            mobileMenu.addEventListener('click', () => {
                navLinks.classList.toggle('active');
                mobileMenu.classList.toggle('active');
            });

            // Fermer le menu en cliquant à l'extérieur
            document.addEventListener('click', (e) => {
                if (!mobileMenu.contains(e.target) && !navLinks.contains(e.target)) {
                    navLinks.classList.remove('active');
                    mobileMenu.classList.remove('active');
                }
            });
        }
    }

    setupActionButtons() {
        // Boutons de suppression avec confirmation
        const deleteButtons = document.querySelectorAll('.btn-delete, [data-action="delete"]');
        deleteButtons.forEach(btn => {
            btn.addEventListener('click', (e) => {
                if (!confirm('Êtes-vous sûr de vouloir supprimer cet élément ? Cette action est irréversible.')) {
                    e.preventDefault();
                    e.stopPropagation();
                }
            });
        });

        // Boutons d'édition
        const editButtons = document.querySelectorAll('.btn-edit, [data-action="edit"]');
        editButtons.forEach(btn => {
            btn.addEventListener('click', (e) => {
                console.log('Édition de l\'élément:', e.target.closest('[data-id]'));
                // Ajouter la logique d'édition spécifique ici
            });
        });

        // Boutons de like/partage
        this.setupSocialInteractions();
    }

    setupSocialInteractions() {
        // Système de likes
        const likeButtons = document.querySelectorAll('.btn-like, [data-action="like"]');
        likeButtons.forEach(btn => {
            btn.addEventListener('click', (e) => {
                e.preventDefault();
                const target = e.currentTarget;
                const publicationId = target.dataset.publicationId;
                
                this.toggleLike(publicationId, target);
            });
        });

        // Système de partage
        const shareButtons = document.querySelectorAll('.btn-share, [data-action="share"]');
        shareButtons.forEach(btn => {
            btn.addEventListener('click', (e) => {
                e.preventDefault();
                this.shareContent(e.currentTarget);
            });
        });

        // --- Gestion du bouton Quitter Communauté ---
        const quitButtons = document.querySelectorAll('.leave-community');
        quitButtons.forEach(btn => {
            btn.addEventListener('click', async function(e) {
                e.preventDefault();
                if (btn.disabled) return;
                btn.disabled = true;
                btn.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i>';
                const form = btn.closest('form');
                const communauteId = form.querySelector('[name="communaute_id"]').value;
                try {
                    const response = await fetch((window.BASE_URL || '') + '/api/leave-community', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/x-www-form-urlencoded',
                        },
                        body: 'communaute_id=' + encodeURIComponent(communauteId)
                    });
                    const text = await response.text();
                    let data;
                    try { data = JSON.parse(text); } catch { data = {success:false,message:'Réponse invalide'}; }
                    if (data.success) {
                        btn.classList.remove('btn-danger');
                        btn.classList.add('btn-secondary');
                        btn.innerHTML = '<i class="fas fa-user-plus me-2"></i>Rejoindre';
                        btn.disabled = false;
                        btn.classList.remove('leave-community');
                        btn.classList.add('join-community');
                        // Redirige ou reload si besoin :
                        // location.reload();
                    } else {
                        btn.innerHTML = '<i class="fas fa-user-minus me-2"></i>Quitter';
                        btn.disabled = false;
                        alert(data.message || 'Erreur inconnue');
                    }
                } catch (er) {
                    btn.innerHTML = '<i class="fas fa-user-minus me-2"></i>Quitter';
                    btn.disabled = false;
                    alert('Erreur réseau ou serveur.');
                }
            });
        });
        // --- Fin gestion quitter ---
    }

    // =============================================
    // ANIMATIONS ET EFFETS VISUELS
    // =============================================

    setupAnimations() {
        this.animateCounters();
        this.setupScrollAnimations();
        this.setupHoverEffects();
        this.setupLoadingStates();
    }

    animateCounters() {
        const counters = document.querySelectorAll('.stat-number, .counter');
        
        const counterObserver = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    this.animateCounter(entry.target);
                    counterObserver.unobserve(entry.target);
                }
            });
        }, { threshold: 0.5 });

        counters.forEach(counter => {
            counterObserver.observe(counter);
        });
    }

    animateCounter(element) {
        const target = parseInt(element.getAttribute('data-target') || element.textContent.replace(/\D/g, ''));
        const duration = 2000;
        const step = target / (duration / 16);
        let current = 0;
        
        const updateCounter = () => {
            current += step;
            if (current < target) {
                element.textContent = Math.floor(current).toLocaleString();
                requestAnimationFrame(updateCounter);
            } else {
                element.textContent = target.toLocaleString();
            }
        };
        
        updateCounter();
    }

    setupScrollAnimations() {
        // Animation au scroll des éléments
        const fadeElements = document.querySelectorAll('.card, .community-card, .publication-card, .game-card, .feature-card');
        
        const fadeObserver = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.style.opacity = '1';
                    entry.target.style.transform = 'translateY(0)';
                    entry.target.classList.add('animated');
                }
            });
        }, { threshold: 0.1 });

        fadeElements.forEach(el => {
            el.style.opacity = '0';
            el.style.transform = 'translateY(30px)';
            el.style.transition = 'opacity 0.6s ease, transform 0.6s ease';
            fadeObserver.observe(el);
        });

        // Animation des barres de progression
        this.animateProgressBars();
    }

    animateProgressBars() {
        const progressBars = document.querySelectorAll('.progress-bar');
        
        const progressObserver = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    const progressBar = entry.target;
                    const width = progressBar.style.width || progressBar.dataset.width || '100%';
                    progressBar.style.width = '0%';
                    
                    setTimeout(() => {
                        progressBar.style.width = width;
                    }, 100);
                    
                    progressObserver.unobserve(progressBar);
                }
            });
        }, { threshold: 0.5 });

        progressBars.forEach(bar => {
            progressObserver.observe(bar);
        });
    }

    setupHoverEffects() {
        // Effets de hover sur les cartes
        const cards = document.querySelectorAll('.card, .community-card, .publication-card');
        cards.forEach(card => {
            card.addEventListener('mouseenter', () => {
                card.style.transform = 'translateY(-10px) scale(1.02)';
            });
            
            card.addEventListener('mouseleave', () => {
                card.style.transform = 'translateY(0) scale(1)';
            });
        });

        // Effets de hover sur les boutons
        const buttons = document.querySelectorAll('.btn, .btn-auth, .action-btn');
        buttons.forEach(btn => {
            btn.addEventListener('mouseenter', () => {
                btn.style.transform = 'translateY(-2px)';
            });
            
            btn.addEventListener('mouseleave', () => {
                btn.style.transform = 'translateY(0)';
            });
        });
    }

    // =============================================
    // GESTION DES FORMULAIRES
    // =============================================

    setupFormHandling() {
        this.disableHTML5Validation();
        this.setupFormValidation();
        this.setupFileUploads();
        this.setupRichTextEditors();
    }

    disableHTML5Validation() {
        // Désactiver la validation HTML5
        const allForms = document.querySelectorAll('form');
        allForms.forEach(form => {
            form.setAttribute('novalidate', 'novalidate');
            form.noValidate = true;
        });
        
        document.addEventListener('invalid', (e) => {
            e.preventDefault();
        }, true);
        
        const requiredFields = document.querySelectorAll('[required]');
        requiredFields.forEach(field => {
            field.removeAttribute('required');
        });
        
        const emailFields = document.querySelectorAll('input[type="email"]');
        emailFields.forEach(field => {
            field.setAttribute('type', 'text');
        });
        
        const urlFields = document.querySelectorAll('input[type="url"]');
        urlFields.forEach(field => {
            field.setAttribute('type', 'text');
        });
    }

    setupFormValidation() {
        const forms = document.querySelectorAll('form[data-validate]');
        
        forms.forEach(form => {
            form.addEventListener('submit', (e) => {
                if (!this.validateForm(form)) {
                    e.preventDefault();
                    this.showFormErrors(form);
                } else {
                    this.showFormLoading(form);
                }
            });
        });

        // Validation en temps réel
        const inputs = document.querySelectorAll('input[data-validate], textarea[data-validate]');
        inputs.forEach(input => {
            input.addEventListener('blur', () => {
                this.validateField(input);
            });
            
            input.addEventListener('input', () => {
                this.clearFieldError(input);
            });
        });
    }

    validateForm(form) {
        let isValid = true;
        const inputs = form.querySelectorAll('input[data-validate], textarea[data-validate], select[data-validate]');
        
        inputs.forEach(input => {
            if (!this.validateField(input)) {
                isValid = false;
            }
        });
        
        return isValid;
    }

    validateField(field) {
        const value = field.value.trim();
        const type = field.dataset.validate;
        let isValid = true;
        let errorMessage = '';

        switch (type) {
            case 'email':
                if (value && !this.isValidEmail(value)) {
                    isValid = false;
                    errorMessage = 'Format d\'email invalide';
                }
                break;
                
            case 'required':
                if (!value) {
                    isValid = false;
                    errorMessage = 'Ce champ est obligatoire';
                }
                break;
                
            case 'minlength':
                const minLength = parseInt(field.dataset.minlength);
                if (value.length < minLength) {
                    isValid = false;
                    errorMessage = `Minimum ${minLength} caractères requis`;
                }
                break;
                
            case 'url':
                if (value && !this.isValidUrl(value)) {
                    isValid = false;
                    errorMessage = 'URL invalide';
                }
                break;
        }

        if (!isValid) {
            this.showFieldError(field, errorMessage);
        } else {
            this.clearFieldError(field);
        }

        return isValid;
    }

    isValidEmail(email) {
        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        return emailRegex.test(email);
    }

    isValidUrl(url) {
        try {
            new URL(url);
            return true;
        } catch {
            return false;
        }
    }

    showFieldError(field, message) {
        this.clearFieldError(field);
        field.classList.add('is-invalid');
        
        const errorDiv = document.createElement('div');
        errorDiv.className = 'invalid-feedback';
        errorDiv.textContent = message;
        
        field.parentNode.appendChild(errorDiv);
    }

    clearFieldError(field) {
        field.classList.remove('is-invalid');
        const existingError = field.parentNode.querySelector('.invalid-feedback');
        if (existingError) {
            existingError.remove();
        }
    }

    showFormErrors(form) {
        const firstInvalid = form.querySelector('.is-invalid');
        if (firstInvalid) {
            firstInvalid.scrollIntoView({ behavior: 'smooth', block: 'center' });
            firstInvalid.focus();
        }
    }

    showFormLoading(form) {
        const submitBtn = form.querySelector('button[type="submit"]');
        if (submitBtn) {
            const originalText = submitBtn.innerHTML;
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Chargement...';
            
            // Restaurer après 30s maximum (au cas où)
            setTimeout(() => {
                submitBtn.disabled = false;
                submitBtn.innerHTML = originalText;
            }, 30000);
        }
    }

    setupFileUploads() {
        const fileInputs = document.querySelectorAll('input[type="file"]');
        
        fileInputs.forEach(input => {
            input.addEventListener('change', (e) => {
                this.handleFileSelection(e.target);
            });
        });
    }

    handleFileSelection(input) {
        const files = input.files;
        const previewContainer = input.parentNode.querySelector('.file-preview');
        
        if (!previewContainer) return;
        
        previewContainer.innerHTML = '';
        
        Array.from(files).forEach(file => {
            if (file.type.startsWith('image/')) {
                const reader = new FileReader();
                reader.onload = (e) => {
                    const preview = document.createElement('div');
                    preview.className = 'file-preview-item';
                    preview.innerHTML = `
                        <img src="${e.target.result}" alt="${file.name}">
                        <span>${file.name}</span>
                    `;
                    previewContainer.appendChild(preview);
                };
                reader.readAsDataURL(file);
            }
        });
    }

    setupRichTextEditors() {
        // Initialisation des éditeurs de texte enrichi
        const textareas = document.querySelectorAll('textarea[data-rich-text]');
        
        textareas.forEach(textarea => {
            this.initSimpleRichText(textarea);
        });
    }

    initSimpleRichText(textarea) {
        // Implémentation basique d'éditeur de texte enrichi
        const toolbar = document.createElement('div');
        toolbar.className = 'rich-text-toolbar';
        toolbar.innerHTML = `
            <button type="button" data-command="bold"><i class="fas fa-bold"></i></button>
            <button type="button" data-command="italic"><i class="fas fa-italic"></i></button>
            <button type="button" data-command="insertUnorderedList"><i class="fas fa-list-ul"></i></button>
            <button type="button" data-command="insertOrderedList"><i class="fas fa-list-ol"></i></button>
        `;
        
        textarea.parentNode.insertBefore(toolbar, textarea);
        
        toolbar.addEventListener('click', (e) => {
            if (e.target.tagName === 'BUTTON' || e.target.parentNode.tagName === 'BUTTON') {
                const button = e.target.tagName === 'BUTTON' ? e.target : e.target.parentNode;
                const command = button.dataset.command;
                
                textarea.focus();
                document.execCommand(command, false, null);
            }
        });
    }

    // =============================================
    // INTERACTIONS DE L'INTERFACE
    // =============================================

    setupUIInteractions() {
        this.setupScrollToTop();
        this.setupTabInterfaces();
        this.setupAccordions();
        this.setupTooltips();
        this.setupNotifications();
    }

    setupScrollToTop() {
        const scrollTopBtn = document.getElementById('scrollTop');
        
        if (scrollTopBtn) {
            window.addEventListener('scroll', () => {
                if (window.pageYOffset > 300) {
                    scrollTopBtn.classList.add('visible');
                } else {
                    scrollTopBtn.classList.remove('visible');
                }
            });

            scrollTopBtn.addEventListener('click', () => {
                window.scrollTo({
                    top: 0,
                    behavior: 'smooth'
                });
            });
        }
    }

    setupTabInterfaces() {
        const tabContainers = document.querySelectorAll('.tab-container, [data-tabs]');
        
        tabContainers.forEach(container => {
            const tabs = container.querySelectorAll('.tab-button, [data-tab]');
            const contents = container.querySelectorAll('.tab-content, [data-tab-content]');
            
            tabs.forEach(tab => {
                tab.addEventListener('click', () => {
                    const tabId = tab.dataset.tab;
                    
                    // Désactiver tous les tabs
                    tabs.forEach(t => t.classList.remove('active'));
                    contents.forEach(c => c.classList.remove('active'));
                    
                    // Activer le tab sélectionné
                    tab.classList.add('active');
                    const content = container.querySelector(`[data-tab-content="${tabId}"]`);
                    if (content) {
                        content.classList.add('active');
                    }
                });
            });
        });
    }

    setupAccordions() {
        const accordions = document.querySelectorAll('.accordion-button, [data-accordion]');
        
        accordions.forEach(accordion => {
            accordion.addEventListener('click', () => {
                const targetId = accordion.dataset.accordionTarget || accordion.getAttribute('aria-controls');
                const target = document.getElementById(targetId);
                
                if (target) {
                    const isExpanded = accordion.getAttribute('aria-expanded') === 'true';
                    
                    accordion.setAttribute('aria-expanded', !isExpanded);
                    target.classList.toggle('show');
                    
                    // Animation
                    if (!isExpanded) {
                        target.style.maxHeight = target.scrollHeight + 'px';
                    } else {
                        target.style.maxHeight = '0';
                    }
                }
            });
        });
    }

    setupTooltips() {
        const tooltipElements = document.querySelectorAll('[data-tooltip]');
        
        tooltipElements.forEach(element => {
            element.addEventListener('mouseenter', (e) => {
                this.showTooltip(e.target);
            });
            
            element.addEventListener('mouseleave', () => {
                this.hideTooltip();
            });
        });
    }

    showTooltip(element) {
        const tooltipText = element.dataset.tooltip;
        const tooltip = document.createElement('div');
        tooltip.className = 'custom-tooltip';
        tooltip.textContent = tooltipText;
        
        document.body.appendChild(tooltip);
        
        const rect = element.getBoundingClientRect();
        tooltip.style.left = rect.left + (rect.width / 2) - (tooltip.offsetWidth / 2) + 'px';
        tooltip.style.top = rect.top - tooltip.offsetHeight - 5 + 'px';
        
        element._tooltip = tooltip;
    }

    hideTooltip() {
        const tooltip = document.querySelector('.custom-tooltip');
        if (tooltip) {
            tooltip.remove();
        }
    }

    setupNotifications() {
        // Auto-dismiss des alertes après 5 secondes
        const alerts = document.querySelectorAll('.alert');
        alerts.forEach(alert => {
            setTimeout(() => {
                const bsAlert = new bootstrap.Alert(alert);
                bsAlert.close();
            }, 5000);
        });
    }

    // =============================================
    // EFFETS DE DÉFILEMENT
    // =============================================

    setupScrollEffects() {
        this.setupParallaxEffects();
        this.setupStickyElements();
        this.setupScrollSpy();
    }

    // Initialisation de l'assistant IA (stub sûr)
    initAIAssistant() {
        if (typeof this.setupAIAssistant === 'function') {
            try {
                this.setupAIAssistant();
            } catch (e) {
                console.warn("Erreur lors de l'initialisation de l'assistant IA:", e);
            }
        }
    }
    setupParallaxEffects() {
        const parallaxElements = document.querySelectorAll('[data-parallax]');
        
        window.addEventListener('scroll', () => {
            const scrolled = window.pageYOffset;
            
            parallaxElements.forEach(element => {
                const speed = element.dataset.parallaxSpeed || 0.5;
                const yPos = -(scrolled * speed);
                element.style.transform = `translateY(${yPos}px)`;
            });
        });
    }

    setupStickyElements() {
        const stickyElements = document.querySelectorAll('.sticky');
        
        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                entry.target.classList.toggle('sticky-pinned', !entry.isIntersecting);
            });
        });

        stickyElements.forEach(element => {
            observer.observe(element);
        });
    }

    setupScrollSpy() {
        const sections = document.querySelectorAll('section[id]');
        const navLinks = document.querySelectorAll('.nav-menu a[href^="#"]');
        
        if (sections.length && navLinks.length) {
            window.addEventListener('scroll', () => {
                let current = '';
                
                sections.forEach(section => {
                    const sectionTop = section.offsetTop;
                    const sectionHeight = section.clientHeight;
                    
                    if (pageYOffset >= sectionTop - 100) {
                        current = section.getAttribute('id');
                    }
                });

                navLinks.forEach(link => {
                    link.classList.remove('active');
                    if (link.getAttribute('href') === `#${current}`) {
                        link.classList.add('active');
                    }
                });
            });
        }
    }

    // =============================================
    // FONCTIONNALITÉS SPÉCIFIQUES
    // =============================================

    setupCardInteractions() {
        // Like des publications
        this.setupPublicationLikes();
        
        // Partage des contenus
        this.setupContentSharing();
        
        // Système de commentaires
        this.setupCommentSystem();
    }

    setupPublicationLikes() {
        document.addEventListener('click', (e) => {
            if (e.target.closest('.publication-like')) {
                const likeBtn = e.target.closest('.publication-like');
                this.handlePublicationLike(likeBtn);
            }
        });
    }

    handlePublicationLike(likeBtn) {
        const publicationId = likeBtn.dataset.publicationId;
        const isLiked = likeBtn.classList.contains('liked');
        
        // Animation visuelle immédiate
        likeBtn.classList.toggle('liked', !isLiked);
        
        // Mise à jour du compteur
        const likeCount = likeBtn.querySelector('.like-count');
        if (likeCount) {
            let count = parseInt(likeCount.textContent);
            count = isLiked ? count - 1 : count + 1;
            likeCount.textContent = count;
        }
        
        // Appel API (simulé)
        this.toggleLike(publicationId, !isLiked);
    }

    async toggleLike(publicationId, like) {
        try {
            const response = await fetch((window.BASE_URL || '') + '/api/like', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({
                    publication_id: publicationId,
                    like: like
                })
            });
            
            if (!response.ok) {
                throw new Error('Erreur lors de la mise à jour du like');
            }
            
            const data = await response.json();
            console.log('Like mis à jour:', data);
            
        } catch (error) {
            console.error('Erreur:', error);
            // Revert visual changes on error
        }
    }

    // Safe invoker: vérifie que la méthode existe et est callable avant d'appeler.
    // Retourne true si la méthode a été appelée, false sinon.
    safeCall(methodName, ...args) {
        try {
            const fn = this[methodName];
            if (typeof fn === 'function') {
                fn.apply(this, args);
                return true;
            }
        } catch (e) {
            console.warn('Erreur lors de l\'appel de ' + methodName + ':', e);
        }
        return false;
    }
    setupContentSharing() {
        document.addEventListener('click', (e) => {
            if (e.target.closest('.share-btn')) {
                const shareBtn = e.target.closest('.share-btn');
                this.shareContent(shareBtn);
            }
        });
    }

    shareContent(shareBtn) {
        const url = shareBtn.dataset.shareUrl || window.location.href;
        const title = shareBtn.dataset.shareTitle || document.title;
        
        if (navigator.share) {
            navigator.share({
                title: title,
                url: url
            }).then(() => {
                console.log('Contenu partagé avec succès');
            }).catch(err => {
                console.log('Erreur de partage:', err);
                this.fallbackShare(url, title);
            });
        } else {
            this.fallbackShare(url, title);
        }
    }

    fallbackShare(url, title) {
        // Fallback pour les navigateurs sans support de l'API Share
        const shareUrl = `https://twitter.com/intent/tweet?text=${encodeURIComponent(title)}&url=${encodeURIComponent(url)}`;
        window.open(shareUrl, '_blank', 'width=600,height=400');
    }

    setupCommentSystem() {
        // Système de commentaires en temps réel
        const commentForms = document.querySelectorAll('.comment-form');
        
        commentForms.forEach(form => {
            form.addEventListener('submit', (e) => {
                e.preventDefault();
                this.submitComment(form);
            });
        });
    }

    async submitComment(form) {
        const formData = new FormData(form);
        const publicationId = form.dataset.publicationId;
        
        try {
            const response = await fetch((window.BASE_URL || '') + '/api/comment', {
                method: 'POST',
                body: formData
            });
            
            if (response.ok) {
                const comment = await response.json();
                this.appendComment(comment, form);
                form.reset();
            } else {
                throw new Error('Erreur lors de l\'envoi du commentaire');
            }
        } catch (error) {
            console.error('Erreur:', error);
            alert('Erreur lors de l\'envoi du commentaire');
        }
    }

    appendComment(comment, form) {
        const commentsContainer = form.closest('.publication-comments').querySelector('.comments-list');
        const commentElement = document.createElement('div');
        commentElement.className = 'comment';
        commentElement.innerHTML = `
            <div class="comment-header">
                <strong>${comment.auteur}</strong>
                <span>${new Date(comment.date).toLocaleString()}</span>
            </div>
            <div class="comment-content">${comment.contenu}</div>
        `;
        
        commentsContainer.appendChild(commentElement);
    }

    setupModalHandlers() {
        // Gestion des modales
        const modalTriggers = document.querySelectorAll('[data-bs-toggle="modal"], [data-modal]');
        
        modalTriggers.forEach(trigger => {
            trigger.addEventListener('click', (e) => {
                const modalId = trigger.dataset.bsTarget || trigger.dataset.modal;
                const modal = document.querySelector(modalId);
                
                if (modal) {
                    this.openModal(modal);
                }
            });
        });
    }

    openModal(modal) {
        modal.style.display = 'block';
        modal.classList.add('show');
        
        // Fermer la modale en cliquant à l'extérieur
        modal.addEventListener('click', (e) => {
            if (e.target === modal) {
                this.closeModal(modal);
            }
        });
        
        // Fermer avec la touche Échap
        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape') {
                this.closeModal(modal);
            }
        });
    }

    closeModal(modal) {
        modal.style.display = 'none';
        modal.classList.remove('show');
    }

    setupLoadingStates() {
        // Gestion des états de chargement
        document.addEventListener('submit', (e) => {
            const form = e.target;
            if (form.tagName === 'FORM') {
                this.setFormLoading(form, true);
            }
        });
    }

    setFormLoading(form, isLoading) {
        const submitBtn = form.querySelector('button[type="submit"]');
        if (submitBtn) {
            if (isLoading) {
                submitBtn.disabled = true;
                submitBtn.dataset.originalText = submitBtn.innerHTML;
                submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Chargement...';
            } else {
                submitBtn.disabled = false;
                submitBtn.innerHTML = submitBtn.dataset.originalText;
            }
        }
    }

    // =============================================
    // UTILITAIRES
    // =============================================

    debounce(func, wait) {
        let timeout;
        return function executedFunction(...args) {
            const later = () => {
                clearTimeout(timeout);
                func(...args);
            };
            clearTimeout(timeout);
            timeout = setTimeout(later, wait);
        };
    }

    throttle(func, limit) {
        let inThrottle;
        return function() {
            const args = arguments;
            const context = this;
            if (!inThrottle) {
                func.apply(context, args);
                inThrottle = true;
                setTimeout(() => inThrottle = false, limit);
            }
        };
    }

    // =============================================
    // INITIALISATION
    // =============================================

    static init() {
        document.addEventListener('DOMContentLoaded', () => {
            // Tentative robuste d'initialisation : essayer plusieurs fois
            // pour laisser le temps à d'autres scripts (cache/anciennes versions)
            // de se charger/terminer et éviter une exception non captée.
            let attempts = 0;
            const maxAttempts = 6;

            const tryInit = () => {
                if (window.communityApp) return; // déjà initialisé
                try {
                    window.communityApp = new CommunityApp();
                    console.info('CommunityApp initialisé avec succès');
                } catch (err) {
                    attempts++;
                    console.warn('Échec initialisation CommunityApp (attempt ' + attempts + '):', err);
                    if (attempts < maxAttempts) {
                        setTimeout(tryInit, 300);
                    } else {
                        console.error('Impossible d\'initialiser CommunityApp après ' + attempts + ' tentatives');
                    }
                }
            };

            tryInit();
        });
    }
}

// Initialisation de l'application
CommunityApp.init();

// Export pour utilisation globale
window.CommunityApp = CommunityApp;

console.log('Script CommunityApp chargé avec succès');

} else {
    console.warn('CommunityApp déjà défini - initialisation ignorée');
}

// Backwards compatibility: si une instance CommunityApp existante utilise l'ancien
// nom de méthode `setupFormSubmissions`, patcher le prototype pour rediriger
// vers `setupFormHandling` si disponible. Cela évite des TypeError sur des
// versions mixtes du script (cache / ancien code).
try {
    if (typeof window.CommunityApp !== 'undefined' && window.CommunityApp.prototype) {
        if (typeof window.CommunityApp.prototype.setupFormSubmissions !== 'function' &&
            typeof window.CommunityApp.prototype.setupFormHandling === 'function') {
            window.CommunityApp.prototype.setupFormSubmissions = window.CommunityApp.prototype.setupFormHandling;
            console.info('Compat: setupFormSubmissions() redirigé vers setupFormHandling()');
        }
    }
} catch (e) {
    console.warn('Erreur lors du patch de compatibilité:', e);
}
    
} // end global guard wrapper
