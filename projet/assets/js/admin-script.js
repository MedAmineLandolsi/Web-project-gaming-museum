// =============================================
// SCRIPT ADMIN - PANEL D'ADMINISTRATION
// =============================================

class AdminPanel {
    constructor() {
        this.sidebarOpen = false;
        this.init();
    }

    init() {
        this.setupSidebar();
        this.setupDashboard();
        this.setupDataTables();
        this.setupFormHandlers();
        this.setupRealTimeUpdates();
        this.setupSearchFunctionality();
        this.setupNotificationSystem();
        this.setupAnalytics();
    }

    // =============================================
    // SIDEBAR ET NAVIGATION
    // =============================================

    setupSidebar() {
        this.setupSidebarToggle();
        this.setupActiveNavItems();
        this.setupMobileBehavior();
    }

    setupSidebarToggle() {
        const menuToggle = document.getElementById('menuToggle');
        const sidebar = document.querySelector('.admin-sidebar');

        if (menuToggle && sidebar) {
            menuToggle.addEventListener('click', () => {
                this.toggleSidebar(sidebar);
            });

            // Fermer la sidebar en cliquant à l'extérieur sur mobile
            document.addEventListener('click', (e) => {
                if (window.innerWidth <= 1024 && this.sidebarOpen) {
                    if (!sidebar.contains(e.target) && !menuToggle.contains(e.target)) {
                        this.closeSidebar(sidebar);
                    }
                }
            });

            // Fermer la sidebar au redimensionnement
            window.addEventListener('resize', this.debounce(() => {
                if (window.innerWidth > 1024 && !this.sidebarOpen) {
                    this.openSidebar(sidebar);
                }
            }, 250));
        }
    }

    toggleSidebar(sidebar) {
        if (this.sidebarOpen) {
            this.closeSidebar(sidebar);
        } else {
            this.openSidebar(sidebar);
        }
    }

    openSidebar(sidebar) {
        sidebar.classList.add('open');
        this.sidebarOpen = true;
        document.body.style.overflow = 'hidden';
    }

    closeSidebar(sidebar) {
        sidebar.classList.remove('open');
        this.sidebarOpen = false;
        document.body.style.overflow = '';
    }

    setupActiveNavItems() {
        const navItems = document.querySelectorAll('.nav-item a');
        
        navItems.forEach(item => {
            item.addEventListener('click', (e) => {
                e.preventDefault();
                
                // Retirer la classe active de tous les éléments
                document.querySelectorAll('.nav-item.active').forEach(activeItem => {
                    activeItem.classList.remove('active');
                });
                
                // Ajouter la classe active à l'élément cliqué
                item.parentElement.classList.add('active');
                
                // Navigation réelle
                const href = item.getAttribute('href');
                if (href && href !== '#') {
                    setTimeout(() => {
                        window.location.href = href;
                    }, 300);
                }
                
                // Fermer la sidebar sur mobile après le clic
                if (window.innerWidth <= 1024) {
                    this.closeSidebar(document.querySelector('.admin-sidebar'));
                }
            });
        });
    }

    setupMobileBehavior() {
        // Adapter le comportement pour mobile
        window.addEventListener('resize', this.debounce(() => {
            const sidebar = document.querySelector('.admin-sidebar');
            if (window.innerWidth <= 1024) {
                this.closeSidebar(sidebar);
            } else {
                this.openSidebar(sidebar);
            }
        }, 250));
    }

    // =============================================
    // DASHBOARD ET STATISTIQUES
    // =============================================

    setupDashboard() {
        this.animateStatistics();
        this.setupChartAnimations();
        this.setupQuickActions();
        this.setupRealTimeStats();
    }

    animateStatistics() {
        const statsObserver = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    const statValues = entry.target.querySelectorAll('.stat-value');
                    statValues.forEach(stat => this.animateCounter(stat));
                    statsObserver.unobserve(entry.target);
                }
            });
        }, { threshold: 0.5 });

        const statsSection = document.querySelector('.stats-overview');
        if (statsSection) {
            statsObserver.observe(statsSection);
        }
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

    setupChartAnimations() {
        const chartObserver = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    const chartFills = entry.target.querySelectorAll('.chart-fill');
                    chartFills.forEach((fill, index) => {
                        setTimeout(() => {
                            const width = fill.style.width || fill.dataset.width || '0%';
                            fill.style.width = '0%';
                            fill.style.transition = 'width 1s ease';
                            
                            setTimeout(() => {
                                fill.style.width = width;
                            }, 100);
                        }, index * 200);
                    });
                    chartObserver.unobserve(entry.target);
                }
            });
        }, { threshold: 0.3 });

        const chartSections = document.querySelectorAll('.popular-games, .chart-container');
        chartSections.forEach(section => {
            chartObserver.observe(section);
        });
    }

    setupQuickActions() {
        const actionButtons = document.querySelectorAll('.action-btn');
        
        actionButtons.forEach(btn => {
            btn.addEventListener('click', () => {
                const action = btn.dataset.action || btn.querySelector('.action-text').textContent;
                this.handleQuickAction(action, btn);
            });
        });
    }

    handleQuickAction(action, button) {
        console.log('Action rapide:', action);
        
        // Animation de feedback
        button.style.transform = 'scale(0.95)';
        setTimeout(() => {
            button.style.transform = '';
        }, 150);
        
        // Actions spécifiques
        switch (action) {
            case 'add-user':
                this.openUserModal();
                break;
            case 'create-community':
                this.openCommunityModal();
                break;
            case 'moderate-content':
                this.openModerationPanel();
                break;
            case 'view-reports':
                this.openReportsPanel();
                break;
            default:
                this.showNotification(`Action "${action}" exécutée`, 'success');
        }
    }

    setupRealTimeStats() {
        // Mise à jour en temps réel des statistiques
        setInterval(() => {
            this.updateRealTimeStats();
        }, 30000); // Toutes les 30 secondes
    }

    async updateRealTimeStats() {
        try {
            const response = await fetch('/projet/admin/api/stats');
            if (response.ok) {
                const stats = await response.json();
                this.updateStatsDisplay(stats);
            }
        } catch (error) {
            console.error('Erreur mise à jour stats:', error);
        }
    }

    updateStatsDisplay(stats) {
        // Mettre à jour l'affichage des statistiques
        Object.keys(stats).forEach(key => {
            const element = document.querySelector(`[data-stat="${key}"]`);
            if (element) {
                this.animateValueChange(element, stats[key]);
            }
        });
    }

    animateValueChange(element, newValue) {
        const oldValue = parseInt(element.textContent.replace(/\D/g, ''));
        if (oldValue !== newValue) {
            element.textContent = newValue.toLocaleString();
            element.style.color = newValue > oldValue ? 'var(--success-green)' : 'var(--danger-red)';
            setTimeout(() => {
                element.style.color = '';
            }, 2000);
        }
    }

    // =============================================
    // GESTION DES DONNÉES ET TABLEAUX
    // =============================================

    setupDataTables() {
        this.setupTableSorting();
        this.setupTableFilters();
        this.setupBulkActions();
        this.setupRowActions();
        this.setupCommunityActions();
    }

    setupTableSorting() {
        const sortableHeaders = document.querySelectorAll('th[data-sort]');
        
        sortableHeaders.forEach(header => {
            header.style.cursor = 'pointer';
            header.addEventListener('click', () => {
                this.sortTable(header);
            });
        });
    }

    sortTable(header) {
        const table = header.closest('table');
        const columnIndex = Array.from(header.parentNode.children).indexOf(header);
        const isNumeric = header.dataset.sort === 'numeric';
        const currentOrder = header.dataset.order || 'asc';
        const newOrder = currentOrder === 'asc' ? 'desc' : 'asc';
        
        // Mettre à jour l'indicateur de tri
        document.querySelectorAll('th[data-order]').forEach(th => {
            th.removeAttribute('data-order');
            th.classList.remove('sorting-asc', 'sorting-desc');
        });
        
        header.dataset.order = newOrder;
        header.classList.add(`sorting-${newOrder}`);
        
        // Trier les lignes
        const tbody = table.querySelector('tbody');
        const rows = Array.from(tbody.querySelectorAll('tr'));
        
        rows.sort((a, b) => {
            const aValue = a.children[columnIndex].textContent.trim();
            const bValue = b.children[columnIndex].textContent.trim();
            
            let comparison = 0;
            if (isNumeric) {
                comparison = parseFloat(aValue) - parseFloat(bValue);
            } else {
                comparison = aValue.localeCompare(bValue, 'fr', { sensitivity: 'base' });
            }
            
            return newOrder === 'asc' ? comparison : -comparison;
        });
        
        // Réorganiser les lignes
        rows.forEach(row => tbody.appendChild(row));
    }

    setupTableFilters() {
        const filterInputs = document.querySelectorAll('input[data-filter]');
        
        filterInputs.forEach(input => {
            input.addEventListener('input', this.debounce(() => {
                this.filterTable(input);
            }, 300));
        });
    }

    filterTable(input) {
        const filterValue = input.value.toLowerCase();
        const targetTable = input.dataset.filter;
        const table = document.querySelector(targetTable);
        
        if (!table) return;
        
        const rows = table.querySelectorAll('tbody tr');
        
        rows.forEach(row => {
            const rowText = row.textContent.toLowerCase();
            row.style.display = rowText.includes(filterValue) ? '' : 'none';
        });
    }

    setupBulkActions() {
        const selectAll = document.querySelector('.select-all');
        const rowCheckboxes = document.querySelectorAll('.row-select');
        const bulkActions = document.querySelector('.bulk-actions');
        
        if (selectAll) {
            selectAll.addEventListener('change', (e) => {
                const isChecked = e.target.checked;
                rowCheckboxes.forEach(checkbox => {
                    checkbox.checked = isChecked;
                });
                this.toggleBulkActions(bulkActions, isChecked);
            });
        }
        
        rowCheckboxes.forEach(checkbox => {
            checkbox.addEventListener('change', () => {
                const anyChecked = Array.from(rowCheckboxes).some(cb => cb.checked);
                this.toggleBulkActions(bulkActions, anyChecked);
            });
        });
    }

    toggleBulkActions(bulkActions, show) {
        if (bulkActions) {
            bulkActions.style.display = show ? 'block' : 'none';
        }
    }

    setupRowActions() {
        this.setupEditActions();
        this.setupDeleteActions();
        this.setupViewActions();
    }

    setupEditActions() {
        const editButtons = document.querySelectorAll('.icon-btn.edit, .btn-edit');
        
        editButtons.forEach(btn => {
            btn.addEventListener('click', (e) => {
                e.preventDefault();
                const row = e.target.closest('tr') || e.target.closest('.admin-card');
                this.handleEdit(row, btn);
            });
        });
    }

    setupDeleteActions() {
        const deleteButtons = document.querySelectorAll('.icon-btn.delete, .btn-delete');
        
        deleteButtons.forEach(btn => {
            btn.addEventListener('click', (e) => {
                e.preventDefault();
                const row = e.target.closest('tr') || e.target.closest('.admin-card');
                this.handleDelete(row, btn);
            });
        });
    }

    setupViewActions() {
        const viewButtons = document.querySelectorAll('.icon-btn.view, .btn-view');
        
        viewButtons.forEach(btn => {
            btn.addEventListener('click', (e) => {
                e.preventDefault();
                const row = e.target.closest('tr') || e.target.closest('.admin-card');
                this.handleView(row, btn);
            });
        });
    }

    handleEdit(row, button) {
        const itemId = row.dataset.id || button.dataset.id;
        console.log('Édition de l\'élément:', itemId);
        
        // Animation de feedback
        button.style.transform = 'scale(1.2)';
        setTimeout(() => {
            button.style.transform = '';
        }, 300);
        
        // Redirection ou ouverture de modal
        const editUrl = button.dataset.editUrl || `/projet/admin/edit/${itemId}`;
        if (button.dataset.modal) {
            this.openEditModal(itemId);
        } else {
            window.location.href = editUrl;
        }
    }

    handleDelete(row, button) {
        const itemId = row.dataset.id || button.dataset.id;
        const itemName = row.dataset.name || 'cet élément';
        
        if (confirm(`Êtes-vous sûr de vouloir supprimer ${itemName} ? Cette action est irréversible.`)) {
            this.performDelete(itemId, row, button);
        }
    }

    async performDelete(itemId, row, button) {
        try {
            button.disabled = true;
            button.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';
            
            const response = await fetch(`/projet/admin/delete/${itemId}`, {
                method: 'DELETE',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            });
            
            if (response.ok) {
                // Animation de suppression
                row.style.opacity = '0';
                row.style.transform = 'translateX(-100%)';
                
                setTimeout(() => {
                    row.remove();
                    this.showNotification('Élément supprimé avec succès', 'success');
                }, 300);
            } else {
                throw new Error('Erreur lors de la suppression');
            }
        } catch (error) {
            console.error('Erreur:', error);
            this.showNotification('Erreur lors de la suppression', 'error');
            button.disabled = false;
            button.innerHTML = '<i class="fas fa-trash"></i>';
        }
    }

    handleView(row, button) {
        const itemId = row.dataset.id || button.dataset.id;
        const viewUrl = button.dataset.viewUrl || `/projet/admin/view/${itemId}`;
        
        window.open(viewUrl, '_blank');
    }

    setupCommunityActions() {
        const joinButtons = document.querySelectorAll('.join-community-btn');
        joinButtons.forEach(button => {
            button.addEventListener('click', () => this.handleJoinCommunity(button));
        });

        // Gestion des boutons "Quitter"
        const quitButtons = document.querySelectorAll('.leave-community-btn');
        quitButtons.forEach(button => {
            button.addEventListener('click', () => this.handleLeaveCommunity(button));
        });
    }

    async handleJoinCommunity(button) {
        if (button.disabled) {
            return;
        }

        const communauteId = button.dataset.communauteId;
        const communauteName = button.dataset.communauteName || 'cette communauté';

        if (!communauteId) {
            this.showNotification('Impossible de déterminer la communauté.', 'warning');
            return;
        }

        const originalContent = button.innerHTML;
        button.disabled = true;
        button.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';

        try {
            const response = await fetch('/projet/api/join-community', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({ communaute_id: communauteId })
            });

            // Vérifier si la réponse est OK
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }

            // Essayer de parser le JSON
            let result;
            try {
                const text = await response.text();
                result = JSON.parse(text);
            } catch (parseError) {
                console.error('Erreur parsing JSON:', parseError);
                throw new Error('Réponse invalide du serveur');
            }

            if (result.success) {
                button.innerHTML = '<i class="fas fa-check"></i> Rejoint';
                button.classList.remove('btn-outline-success');
                button.classList.add('btn-success');
                button.disabled = true;
                this.showNotification(result.message || `Vous avez rejoint ${communauteName}.`, 'success');
            } else {
                button.disabled = false;
                button.innerHTML = originalContent;
                this.showNotification(result.message || 'Impossible de rejoindre cette communauté.', 'warning');
            }
        } catch (error) {
            console.error('Erreur join communauté:', error);
            button.disabled = false;
            button.innerHTML = originalContent;
            this.showNotification('Erreur lors de la tentative de rejoindre la communauté. Vérifiez votre connexion.', 'error');
        }
    }

    async handleLeaveCommunity(button) {
        if (button.disabled) {
            return;
        }

        const communauteId = button.dataset.communauteId;
        const communauteName = button.dataset.communauteName || 'cette communauté';

        if (!communauteId) {
            this.showNotification('Impossible de déterminer la communauté.', 'warning');
            return;
        }

        const originalContent = button.innerHTML;
        button.disabled = true;
        button.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';

        try {
            const response = await fetch('/projet/api/leave-community', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({ communaute_id: communauteId })
            });

            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }

            let result;
            try {
                const text = await response.text();
                result = JSON.parse(text);
            } catch (parseError) {
                console.error('Erreur parsing JSON:', parseError);
                throw new Error('Réponse invalide du serveur');
            }

            if (result.success) {
                // Transformer le bouton "Quitter" en "Rejoindre"
                button.classList.remove('btn-danger', 'leave-community-btn');
                button.classList.add('btn-outline-success', 'join-community-btn');
                button.setAttribute('data-communaute-id', communauteId);
                button.setAttribute('data-communaute-name', communauteName);
                button.innerHTML = '<i class="fas fa-user-plus"></i>';
                button.disabled = false;
                button.title = 'Rejoindre cette communauté';
                
                // Réattacher l'événement "Rejoindre"
                button.removeEventListener('click', arguments.callee);
                button.addEventListener('click', () => this.handleJoinCommunity(button));
                
                this.showNotification(result.message || `Vous avez quitté ${communauteName} avec succès.`, 'success');
            } else {
                button.disabled = false;
                button.innerHTML = originalContent;
                this.showNotification(result.message || 'Impossible de quitter cette communauté.', 'warning');
            }
        } catch (error) {
            console.error('Erreur quit communauté:', error);
            button.disabled = false;
            button.innerHTML = originalContent;
            this.showNotification('Erreur lors de la tentative de quitter la communauté. Vérifiez votre connexion.', 'error');
        }
    }

    // =============================================
    // GESTION DES FORMULAIRES
    // =============================================

    setupFormHandlers() {
        this.setupFormValidation();
        this.setupFormSubmissions();
        this.setupFileUploads();
        this.setupRichTextEditors();
    }

    setupFormValidation() {
        const forms = document.querySelectorAll('form[data-validate]');
        
        forms.forEach(form => {
            form.addEventListener('submit', (e) => {
                if (!this.validateAdminForm(form)) {
                    e.preventDefault();
                    this.showFormErrors(form);
                }
            });
        });
    }

    validateAdminForm(form) {
        let isValid = true;
        const requiredFields = form.querySelectorAll('[required]');
        
        requiredFields.forEach(field => {
            if (!field.value.trim()) {
                isValid = false;
                this.showFieldError(field, 'Ce champ est obligatoire');
            } else {
                this.clearFieldError(field);
            }
        });
        
        return isValid;
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

    setupFormSubmissions() {
        const forms = document.querySelectorAll('form');
        
        forms.forEach(form => {
            form.addEventListener('submit', (e) => {
                this.handleFormSubmission(form, e);
            });
        });
    }

    handleFormSubmission(form, event) {
        const submitBtn = form.querySelector('button[type="submit"]');
        
        if (submitBtn) {
            // Sauvegarder le texte original
            if (!submitBtn.dataset.originalText) {
                submitBtn.dataset.originalText = submitBtn.innerHTML;
            }
            
            // Afficher l'état de chargement
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Traitement...';
            
            // Restaurer après 30s maximum
            setTimeout(() => {
                submitBtn.disabled = false;
                submitBtn.innerHTML = submitBtn.dataset.originalText;
            }, 30000);
        }
    }

    setupFileUploads() {
        const fileInputs = document.querySelectorAll('input[type="file"][data-preview]');
        
        fileInputs.forEach(input => {
            input.addEventListener('change', (e) => {
                this.handleAdminFileUpload(e.target);
            });
        });
    }

    handleAdminFileUpload(input) {
        const files = input.files;
        const previewId = input.dataset.preview;
        const previewContainer = document.getElementById(previewId);
        
        if (!previewContainer) return;
        
        previewContainer.innerHTML = '';
        
        Array.from(files).forEach(file => {
            const previewItem = document.createElement('div');
            previewItem.className = 'file-preview-item';
            
            if (file.type.startsWith('image/')) {
                const reader = new FileReader();
                reader.onload = (e) => {
                    previewItem.innerHTML = `
                        <img src="${e.target.result}" alt="${file.name}">
                        <div class="file-info">
                            <span class="file-name">${file.name}</span>
                            <span class="file-size">${this.formatFileSize(file.size)}</span>
                        </div>
                    `;
                };
                reader.readAsDataURL(file);
            } else {
                previewItem.innerHTML = `
                    <div class="file-icon">
                        <i class="fas fa-file"></i>
                    </div>
                    <div class="file-info">
                        <span class="file-name">${file.name}</span>
                        <span class="file-size">${this.formatFileSize(file.size)}</span>
                    </div>
                `;
            }
            
            previewContainer.appendChild(previewItem);
        });
    }

    formatFileSize(bytes) {
        if (bytes === 0) return '0 Bytes';
        const k = 1024;
        const sizes = ['Bytes', 'KB', 'MB', 'GB'];
        const i = Math.floor(Math.log(bytes) / Math.log(k));
        return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
    }

    setupRichTextEditors() {
        const textareas = document.querySelectorAll('textarea[data-rich-text]');
        
        textareas.forEach(textarea => {
            this.initAdminRichText(textarea);
        });
    }

    initAdminRichText(textarea) {
        // Implémentation d'éditeur riche pour l'admin
        const toolbar = document.createElement('div');
        toolbar.className = 'rich-text-toolbar';
        toolbar.innerHTML = `
            <button type="button" data-command="bold" title="Gras"><i class="fas fa-bold"></i></button>
            <button type="button" data-command="italic" title="Italique"><i class="fas fa-italic"></i></button>
            <button type="button" data-command="underline" title="Souligné"><i class="fas fa-underline"></i></button>
            <button type="button" data-command="insertUnorderedList" title="Liste à puces"><i class="fas fa-list-ul"></i></button>
            <button type="button" data-command="insertOrderedList" title="Liste numérotée"><i class="fas fa-list-ol"></i></button>
            <button type="button" data-command="createLink" title="Lien"><i class="fas fa-link"></i></button>
            <button type="button" data-command="formatBlock" data-value="h2" title="Titre"><i class="fas fa-heading"></i></button>
            <button type="button" data-command="formatBlock" data-value="blockquote" title="Citation"><i class="fas fa-quote-right"></i></button>
        `;
        
        textarea.parentNode.insertBefore(toolbar, textarea);
        
        toolbar.addEventListener('click', (e) => {
            const button = e.target.closest('button');
            if (button) {
                const command = button.dataset.command;
                const value = button.dataset.value;
                
                textarea.focus();
                
                if (command === 'createLink') {
                    const url = prompt('Entrez l\'URL:');
                    if (url) {
                        document.execCommand(command, false, url);
                    }
                } else if (value) {
                    document.execCommand(command, false, value);
                } else {
                    document.execCommand(command, false, null);
                }
            }
        });
    }

    // =============================================
    // RECHERCHE ET FILTRES
    // =============================================

    setupSearchFunctionality() {
        this.setupGlobalSearch();
        this.setupAdvancedFilters();
        this.setupSearchHistory();
    }

    setupGlobalSearch() {
        const searchInput = document.querySelector('.search-input');
        const searchBtn = document.querySelector('.search-btn');
        
        if (searchInput && searchBtn) {
            const performSearch = () => {
                const query = searchInput.value.trim();
                if (query) {
                    this.executeSearch(query);
                }
            };
            
            searchBtn.addEventListener('click', performSearch);
            searchInput.addEventListener('keypress', (e) => {
                if (e.key === 'Enter') {
                    performSearch();
                }
            });
        }
    }

    executeSearch(query) {
        console.log('Recherche:', query);
        // Implémentation de la recherche
        this.showNotification(`Recherche pour: ${query}`, 'info');
        
        // Simulation de résultats
        setTimeout(() => {
            this.displaySearchResults(this.generateMockResults(query));
        }, 1000);
    }

    generateMockResults(query) {
        return [
            { type: 'Membre', title: `Utilisateur "${query}"`, url: '#' },
            { type: 'Communauté', title: `Communauté "${query}"`, url: '#' },
            { type: 'Publication', title: `Publication "${query}"`, url: '#' }
        ];
    }

    displaySearchResults(results) {
        // Afficher les résultats de recherche
        console.log('Résultats:', results);
    }

    setupAdvancedFilters() {
        const filterToggles = document.querySelectorAll('.filter-toggle');
        
        filterToggles.forEach(toggle => {
            toggle.addEventListener('click', () => {
                const filterPanel = document.querySelector('.advanced-filters');
                if (filterPanel) {
                    filterPanel.classList.toggle('open');
                }
            });
        });
    }

    setupSearchHistory() {
        // Gérer l'historique de recherche
        this.loadSearchHistory();
    }

    loadSearchHistory() {
        const history = JSON.parse(localStorage.getItem('admin_search_history') || '[]');
        return history;
    }

    saveSearchHistory(query) {
        const history = this.loadSearchHistory();
        const newHistory = [query, ...history.filter(item => item !== query)].slice(0, 10);
        localStorage.setItem('admin_search_history', JSON.stringify(newHistory));
    }

    // =============================================
    // NOTIFICATIONS ET ALERTES
    // =============================================

    setupNotificationSystem() {
        this.setupAutoDismiss();
        this.setupNotificationBadges();
        this.setupRealTimeNotifications();
    }

    setupAutoDismiss() {
        // Auto-dismiss des alertes
        const alerts = document.querySelectorAll('.alert');
        alerts.forEach(alert => {
            setTimeout(() => {
                const bsAlert = new bootstrap.Alert(alert);
                bsAlert.close();
            }, 5000);
        });
    }

    setupNotificationBadges() {
        // Mise à jour des badges de notification
        this.updateNotificationBadges();
    }

    updateNotificationBadges() {
        const badges = document.querySelectorAll('.nav-count, .notif-badge');
        
        // Simulation de mise à jour
        setInterval(() => {
            badges.forEach(badge => {
                if (Math.random() > 0.8) {
                    const current = parseInt(badge.textContent) || 0;
                    badge.textContent = current + 1;
                    
                    // Animation
                    badge.style.animation = 'none';
                    setTimeout(() => {
                        badge.style.animation = '';
                    }, 10);
                }
            });
        }, 30000);
    }

    setupRealTimeNotifications() {
        // Écouter les nouvelles notifications en temps réel
        this.setupWebSocketNotifications();
    }

    setupWebSocketNotifications() {
        // Simulation de notifications WebSocket
        setInterval(() => {
            if (Math.random() > 0.9) {
                this.showNotification('Nouvelle activité détectée', 'info');
            }
        }, 60000);
    }

    showNotification(message, type = 'info') {
        const notification = document.createElement('div');
        notification.className = `alert alert-${type} alert-dismissible fade show`;
        notification.innerHTML = `
            <i class="fas fa-${this.getNotificationIcon(type)} me-2"></i>
            ${message}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        `;
        
        const container = document.querySelector('.admin-main');
        container.insertBefore(notification, container.firstChild);
        
        // Auto-dismiss
        setTimeout(() => {
            if (notification.parentNode) {
                const bsAlert = new bootstrap.Alert(notification);
                bsAlert.close();
            }
        }, 5000);
    }

    getNotificationIcon(type) {
        const icons = {
            'success': 'check-circle',
            'error': 'exclamation-circle',
            'warning': 'exclamation-triangle',
            'info': 'info-circle'
        };
        return icons[type] || 'info-circle';
    }

    // =============================================
    // ANALYTIQUES ET RAPPORTS
    // =============================================

    setupAnalytics() {
        this.setupDataCharts();
        this.setupReportGeneration();
        this.setupExportFunctionality();
    }

    setupDataCharts() {
        // Initialiser les graphiques et visualisations
        this.initCharts();
    }

    initCharts() {
        const chartContainers = document.querySelectorAll('[data-chart]');
        
        chartContainers.forEach(container => {
            const chartType = container.dataset.chart;
            this.renderChart(container, chartType);
        });
    }

    renderChart(container, type) {
        // Implémentation basique de graphique
        console.log(`Rendu du graphique ${type} dans`, container);
        
        // Simulation de rendu de graphique
        container.innerHTML = `<div class="chart-placeholder">Graphique ${type} - Données en cours de chargement...</div>`;
        
        setTimeout(() => {
            container.innerHTML = `<div class="chart-rendered">Graphique ${type} - Données chargées</div>`;
        }, 1000);
    }

    setupReportGeneration() {
        const reportButtons = document.querySelectorAll('[data-report]');
        
        reportButtons.forEach(button => {
            button.addEventListener('click', () => {
                const reportType = button.dataset.report;
                this.generateReport(reportType);
            });
        });
    }

    generateReport(reportType) {
        this.showNotification(`Génération du rapport ${reportType} en cours...`, 'info');
        
        // Simulation de génération de rapport
        setTimeout(() => {
            this.showNotification(`Rapport ${reportType} généré avec succès`, 'success');
            this.downloadReport(reportType);
        }, 2000);
    }

    downloadReport(reportType) {
        // Simulation de téléchargement
        const link = document.createElement('a');
        link.href = '#'; // URL réelle du rapport
        link.download = `rapport_${reportType}_${new Date().toISOString().split('T')[0]}.pdf`;
        link.click();
    }

    setupExportFunctionality() {
        const exportButtons = document.querySelectorAll('[data-export]');
        
        exportButtons.forEach(button => {
            button.addEventListener('click', () => {
                const format = button.dataset.export;
                this.exportData(format);
            });
        });
    }

    exportData(format) {
        this.showNotification(`Export ${format} en cours...`, 'info');
        
        setTimeout(() => {
            this.showNotification(`Données exportées en ${format}`, 'success');
        }, 1500);
    }

    // =============================================
    // FONCTIONNALITÉS AVANCÉES
    // =============================================

    setupRealTimeUpdates() {
        this.setupLiveData();
        this.setupActivityFeed();
        this.setupSystemMonitoring();
    }

    setupLiveData() {
        // Mise à jour en temps réel des données
        setInterval(() => {
            this.updateLiveData();
        }, 10000);
    }

    updateLiveData() {
        // Mettre à jour les données en temps réel
        console.log('Mise à jour des données en temps réel');
    }

    setupActivityFeed() {
        // Flux d'activité en temps réel
        this.updateActivityFeed();
    }

    updateActivityFeed() {
        const activityFeed = document.querySelector('.activity-feed');
        if (activityFeed) {
            // Mettre à jour le flux d'activité
        }
    }

    setupSystemMonitoring() {
        // Surveillance du système
        this.monitorSystemHealth();
    }

    monitorSystemHealth() {
        setInterval(() => {
            this.checkSystemHealth();
        }, 60000);
    }

    checkSystemHealth() {
        // Vérifier l'état du système
        console.log('Vérification de la santé du système');
    }

    // =============================================
    // MODALES ET FENÊTRES
    // =============================================

    openUserModal() {
        this.showNotification('Ouverture du modal utilisateur', 'info');
        // Implémentation du modal utilisateur
    }

    openCommunityModal() {
        this.showNotification('Ouverture du modal communauté', 'info');
        // Implémentation du modal communauté
    }

    openModerationPanel() {
        this.showNotification('Ouverture du panel de modération', 'info');
        // Implémentation du panel de modération
    }

    openReportsPanel() {
        this.showNotification('Ouverture du panel de rapports', 'info');
        // Implémentation du panel de rapports
    }

    openEditModal(itemId) {
        this.showNotification(`Ouverture du modal d'édition pour ${itemId}`, 'info');
        // Implémentation du modal d'édition
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
            window.adminPanel = new AdminPanel();
        });
    }
}

// Initialisation du panel admin
AdminPanel.init();

// Export pour utilisation globale
window.AdminPanel = AdminPanel;

console.log('Script AdminPanel chargé avec succès');