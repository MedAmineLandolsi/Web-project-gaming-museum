// IA features disabled: moderation & recommendations removed
async function moderatePublicationIA() {
    console.info('moderatePublicationIA: disabled (endpoint removed)');
    const resultDiv = document.getElementById('moderation-result');
    if (resultDiv) {
        resultDiv.textContent = 'Modération IA désactivée.';
        resultDiv.className = 'text-muted';
    }
    return;
}

async function loadRecommendationsIA(userId = null) {
    console.info('loadRecommendationsIA: disabled (endpoint removed)');
    return [];
}

function displayRecommendationsIA(communities) {
    // no-op: recommendations IA removed
    return;
}

// Recommendations IA disabled — no automatic call on DOMContentLoaded

// Publication creator filter: attach handlers if the input exists
document.addEventListener('DOMContentLoaded', function () {
    const creatorInput = document.getElementById('creatorFilter');
    const clearBtn = document.getElementById('clearCreatorFilter');
    const creatorSelect = document.getElementById('creatorSelect');
    const creatorSelectHeader = document.getElementById('creatorSelectHeader');
    const clearHeaderBtn = document.getElementById('clearCreatorSelectHeader');
    if (creatorInput) {
        creatorInput.addEventListener('input', function () {
            applyPublicationCreatorFilter(this.value);
        });
    }
    if (creatorSelect) {
        creatorSelect.addEventListener('change', function () {
            // If a select value is chosen, apply filter by author id. If empty, show all (or fallback to text input)
            const val = this.value;
            if (!val) {
                applyPublicationCreatorFilter('');
            } else {
                applyPublicationCreatorFilterById(val);
            }
        });
    }
    if (creatorSelectHeader) {
        creatorSelectHeader.addEventListener('change', function () {
            const val = this.value;
            if (!val) {
                applyPublicationCreatorFilter('');
            } else {
                applyPublicationCreatorFilterById(val);
            }
        });
    }
    if (clearBtn) {
        clearBtn.addEventListener('click', function () {
            if (creatorInput) {
                creatorInput.value = '';
                applyPublicationCreatorFilter('');
                creatorInput.focus();
            }
            if (creatorSelect) {
                creatorSelect.value = '';
            }
            if (creatorSelectHeader) {
                creatorSelectHeader.value = '';
            }
            if (clearHeaderBtn) {
                // no-op, button exists for DOM only
            }
        });
    }
});

// Show/hide publication cards based on creator name (case-insensitive substring)
function applyPublicationCreatorFilter(query) {
    const q = (query || '').toString().trim().toLowerCase();
    const cards = document.querySelectorAll('.publication-card-enhanced');
    cards.forEach(card => {
        const authorEl = card.querySelector('.publication-author-name');
        const authorText = authorEl ? authorEl.textContent.trim().toLowerCase() : '';
        if (!q) {
            card.style.removeProperty('display');
        } else {
            card.style.display = authorText.indexOf(q) !== -1 ? '' : 'none';
        }
    });
}

// Filter publications by auteur id stored in `data-auteur-id` attribute
function applyPublicationCreatorFilterById(auteurId) {
    const id = auteurId ? auteurId.toString() : '';
    const cards = document.querySelectorAll('.publication-card-enhanced');
    cards.forEach(card => {
        const aid = card.getAttribute('data-auteur-id') || '';
        if (!id) {
            card.style.removeProperty('display');
        } else {
            card.style.display = (aid === id) ? '' : 'none';
        }
    });
}

    // Initialize category filter if present
    if (document.getElementById('categoryFilter')) {
           // Removed category filter initialization
    }
// Tri intelligent des publications par IA
async function sortPublicationsIA() {
    console.info('sortPublicationsIA: disabled (endpoint removed)');
    return;
}

// Affichage des publications triées par IA
function displayPublicationsIA(publications) {
    const container = document.getElementById('publications-list');
    if (!container) return;
    container.innerHTML = '';
    if (publications.length === 0) {
        container.innerHTML = '<p>Aucune publication trouvée.</p>';
        return;
    }
    publications.forEach(pub => {
        const div = document.createElement('div');
        div.className = 'publication-item';
        div.textContent = pub.titre + ' (Score IA: ' + (pub.ia_score ?? '-') + ')';
        container.appendChild(div);
    });
}
// Recherche intelligente via API OpenAI
async function searchCommunity(query) {
    try {
        // Build URL for search API
        let url = '/projet/api/search.php';

        const response = await fetch(url, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ query })
        });

        if (!response.ok) {
            console.error('searchCommunity: response not ok', response.status, response.statusText);
            displaySearchResults([]);
            return;
        }

        const text = await response.text();
        let results;
        try {
            results = JSON.parse(text);
        } catch (err) {
            console.error('searchCommunity: invalid JSON', text, err);
            displaySearchResults([]);
            return;
        }

        if (results.error) {
            console.warn('searchCommunity: API returned error', results.error);
            displaySearchResults([]);
            return;
        }

        // If API returned an object with results + debug, pass it through
        if (results && typeof results === 'object' && results.results) {
            if (results.debug) console.info('search debug:', results.debug);
            displaySearchResults(results);
        } else {
            displaySearchResults(results);
        }
    } catch (err) {
        console.error('searchCommunity: network or unexpected error', err);
        displaySearchResults([]);
    }
}

// Affichage des résultats dans un élément HTML
function displaySearchResults(results) {
    const container = document.getElementById('search-results');
    if (!container) return;
    container.innerHTML = '';
    console.debug('displaySearchResults:', results);

    // Hide or show the full communities list depending on whether search results are present
    const fullList = document.getElementById('communities-list');
    function showFullList(show) {
        if (!fullList) return;
        fullList.style.display = show ? '' : 'none';
    }

    // If we received an object { results: [...], debug: {...} }
    let debugInfo = null;
    if (results && typeof results === 'object' && results.results && Array.isArray(results.results)) {
        debugInfo = results.debug || null;
        results = results.results;
    }

    if (!Array.isArray(results)) {
        if (!results || (typeof results === 'object' && Object.keys(results).length === 0)) {
            container.innerHTML = '<p>Aucun résultat trouvé.</p>';
            showFullList(true);
            return;
        }
        // Coerce to array
        results = [results];
    }

    if (results.length === 0) {
        container.innerHTML = '<p>Aucun résultat trouvé.</p>';
        showFullList(true);
        return;
    }

    // If we have search results, hide the full communities list to avoid duplicate/confusing display
    showFullList(false);

    results.forEach(item => {
        // Build a community-like card for each result to match front view
        const cardCol = document.createElement('div');
        cardCol.className = 'col-lg-6 col-md-6 mb-5';

        const card = document.createElement('div');
        card.className = 'community-card p-4 h-100';

        // Header with avatar and title
        const header = document.createElement('div');
        header.className = 'd-flex align-items-center mb-3';

        const avatarWrapper = document.createElement('div');
        if (item.avatar) {
            const img = document.createElement('img');
            img.src = item.avatar;
            img.alt = item.nom || 'avatar';
            img.className = 'rounded-circle me-3';
            img.style.width = '60px';
            img.style.height = '60px';
            img.style.objectFit = 'cover';
            avatarWrapper.appendChild(img);
        } else {
            const initials = document.createElement('div');
            initials.className = 'avatar me-3';
            initials.style.width = '60px';
            initials.style.height = '60px';
            initials.style.fontSize = '1.5rem';
            const nameText = item.nom || '';
            let initialsText = nameText.trim().substring(0,2).toUpperCase();
            initials.textContent = initialsText;
            avatarWrapper.appendChild(initials);
        }

        const meta = document.createElement('div');
        meta.className = 'flex-grow-1';
        const title = document.createElement('h5');
        title.className = 'mb-1';
        const link = document.createElement('a');
        link.href = '/projet/communautes/' + (item.id || '');
        link.className = 'text-decoration-none text-white';
        link.textContent = item.nom || 'Communauté';
        title.appendChild(link);
        meta.appendChild(title);

        // Creator info (best effort)
        const smallCreator = document.createElement('small');
        smallCreator.className = 'text-muted d-block';
        const creatorName = (item.prenom || '') + (item.prenom && item.nom ? ' ' : '') + (item.nom_createur || item.nom || '');
        let creatorText = '';
        if (item.prenom || item.createur_prenom) {
            creatorText = (item.prenom || item.createur_prenom) + ' ' + (item.createur_nom || item.nom_createur || item.createur_nom || '');
        } else if (item.createur_id) {
            creatorText = 'ID ' + item.createur_id;
        }
        if (creatorText) {
            smallCreator.innerHTML = '<i class="fas fa-user me-1"></i>Créée par <a class="text-decoration-none text-primary" href="/projet/membres/' + (item.createur_id || '') + '">' + creatorText + '</a>';
            meta.appendChild(smallCreator);
        }

        header.appendChild(avatarWrapper);
        header.appendChild(meta);
        card.appendChild(header);

        // Description
        const pDesc = document.createElement('p');
        pDesc.className = 'text-muted mb-3';
        const desc = item.description || '';
        pDesc.textContent = desc.length > 150 ? desc.substring(0,150) + '...' : desc;
        card.appendChild(pDesc);

        // Badges (category + visibility)
        const badges = document.createElement('div');
        badges.className = 'd-flex justify-content-between align-items-center mb-3 flex-wrap gap-2';
        const catBadge = document.createElement('span');
        catBadge.className = 'badge bg-primary';
        catBadge.innerHTML = '<i class="fas fa-tag me-1"></i>' + (item.categorie || '');
        badges.appendChild(catBadge);
        const visBadge = document.createElement('span');
        visBadge.className = 'badge bg-' + ((item.visibilite || '') === 'publique' ? 'success' : 'warning');
        visBadge.innerHTML = '<i class="fas fa-' + (((item.visibilite || '') === 'publique') ? 'globe' : 'lock') + ' me-1"></i>' + (item.visibilite ? (item.visibilite.charAt(0).toUpperCase() + item.visibilite.slice(1)) : '');
        badges.appendChild(visBadge);
        card.appendChild(badges);

        // Actions
        const actions = document.createElement('div');
        actions.className = 'd-flex flex-wrap gap-3 mt-3';
        const detailsBtn = document.createElement('a');
        detailsBtn.href = '/projet/communautes/' + (item.id || '');
        detailsBtn.className = 'btn btn-info btn-sm flex-fill';
        detailsBtn.innerHTML = '<i class="fas fa-info-circle me-1"></i>Détails';
        actions.appendChild(detailsBtn);
        const pubsBtn = document.createElement('a');
        pubsBtn.href = '/projet/publications?communaute=' + (item.id || '');
        pubsBtn.className = 'btn btn-primary btn-sm flex-fill';
        pubsBtn.innerHTML = '<i class="fas fa-eye me-1"></i>Publications';
        actions.appendChild(pubsBtn);
        card.appendChild(actions);

        cardCol.appendChild(card);
        container.appendChild(cardCol);
    });

    // After rendering results, apply any active category filter
        // Removed category filter application after rendering results


    // Removed applyCategoryFilter function
    // Do not render server debug info in production UI. Keep debug only in console.
    if (debugInfo) console.info('search debug (hidden from UI):', debugInfo);
    // Properly close displaySearchResults
}

// Exemple d'utilisation :
// searchCommunity('nom de la communauté');
// =============================================
// SCRIPT PRINCIPAL - PROJET COMMUNAUTAIRE
// =============================================

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
        
        // Gestion des formulaires
        this.setupFormHandling();
        
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
                    const response = await fetch('/projet/api/leave-community', {
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
            const response = await fetch('/projet/api/like', {
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
            const response = await fetch('/projet/api/comment', {
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
            window.communityApp = new CommunityApp();
        });
    }
}

// Initialisation de l'application
CommunityApp.init();

// Export pour utilisation globale
window.CommunityApp = CommunityApp;

console.log('Script CommunityApp chargé avec succès');