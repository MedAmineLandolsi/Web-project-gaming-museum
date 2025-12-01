// Script principal de l'application
document.addEventListener('DOMContentLoaded', function() {
    initializeApp();
});

function initializeApp() {
    // Initialiser le menu mobile
    initMobileMenu();
    
    // Initialiser le routeur
    initRouter();
    
    // Initialiser les écouteurs d'événements globaux
    initEventListeners();
    
    // Vérifier l'authentification
    checkAuthStatus();
}

function initMobileMenu() {
    const mobileMenu = document.querySelector('.mobile-menu');
    const navLinks = document.querySelector('.nav-links');
    
    if (mobileMenu && navLinks) {
        mobileMenu.addEventListener('click', function() {
            navLinks.classList.toggle('active');
            mobileMenu.classList.toggle('active');
        });
    }
}

function initRouter() {
    // Configuration des routes
    AppRouter.addRoute('/', handleHomeRoute);
    AppRouter.addRoute('/frontoffice/blog.html', handleBlogRoute);
    AppRouter.addRoute('/frontoffice/blog-single.html', handleBlogSingleRoute);
    AppRouter.addRoute('/backoffice/dashboard.html', handleDashboardRoute);
    AppRouter.addRoute('/backoffice/blog-admin.html', handleBlogAdminRoute);
    AppRouter.addRoute('/backoffice/blog-edit.html', handleBlogEditRoute);
}

function initEventListeners() {
    // Écouter les mises à jour des articles
    AppState.on('articlesUpdated', function() {
        // Recharger les composants concernés
        if (window.location.pathname.includes('blog')) {
            loadBlogArticles();
        }
    });
    
    // Écouter les mises à jour des commentaires
    AppState.on('commentsUpdated', function() {
        if (window.location.pathname.includes('blog-single')) {
            loadArticleComments();
        }
    });
}

function checkAuthStatus() {
    // Mettre à jour l'interface en fonction de l'authentification
    const adminLinks = document.querySelectorAll('.admin-btn, [href*="backoffice"]');
    const isAdmin = AppState.isAdminUser();
    
    adminLinks.forEach(link => {
        if (isAdmin) {
            link.style.display = 'block';
        } else {
            link.style.display = 'none';
        }
    });
}

// Gestionnaires de routes
function handleHomeRoute() {
    console.log('Page d\'accueil chargée');
    // Logique spécifique à la page d'accueil
}

function handleBlogRoute() {
    loadBlogArticles();
}

function handleBlogSingleRoute(path) {
    const articleId = getArticleIdFromUrl();
    if (articleId) {
        loadSingleArticle(articleId);
    }
}

function handleDashboardRoute() {
    if (!AppState.isAdminUser()) {
        window.location.href = 'login.html';
        return;
    }
    loadDashboard();
}

function handleBlogAdminRoute() {
    if (!AppState.isAdminUser()) {
        window.location.href = 'login.html';
        return;
    }
    loadBlogAdmin();
}

function handleBlogEditRoute() {
    if (!AppState.isAdminUser()) {
        window.location.href = 'login.html';
        return;
    }
    loadBlogEdit();
}

// Fonctions utilitaires
function getArticleIdFromUrl() {
    const urlParams = new URLSearchParams(window.location.search);
    return urlParams.get('id');
}

function formatDate(dateString) {
    const options = { year: 'numeric', month: 'long', day: 'numeric' };
    return new Date(dateString).toLocaleDateString('fr-FR', options);
}

// Fonctions de chargement des données
function loadBlogArticles() {
    const articlesGrid = document.querySelector('.blog-grid');
    if (!articlesGrid) return;
    
    const articles = AppState.getArticles();
    
    articlesGrid.innerHTML = articles.map(article => `
        <article class="blog-card" data-category="${article.category}">
            <div class="blog-image">
                <img src="assets/images/${article.image}" alt="${article.title}" onerror="this.src='assets/images/placeholder.jpg'">
            </div>
            <div class="blog-content">
                <span class="blog-category">${getCategoryLabel(article.category)}</span>
                <h3 class="blog-title">
                    <a href="frontoffice/blog-single.html?id=${article.id}" data-route="/frontoffice/blog-single.html?id=${article.id}">
                        ${article.title}
                    </a>
                </h3>
                <p class="blog-excerpt">${article.content.substring(0, 150)}...</p>
                <div class="blog-meta">
                    <span class="blog-date">${formatDate(article.created_at)}</span>
                    <span class="blog-comments">${article.comment_count} commentaires</span>
                </div>
            </div>
        </article>
    `).join('');
}

function loadSingleArticle(articleId) {
    const article = AppState.getArticleById(articleId);
    if (!article) {
        document.body.innerHTML = '<div class="container"><h1>Article non trouvé</h1></div>';
        return;
    }
    
    // Mettre à jour le titre de la page
    document.title = `${article.title} - Musée de Gaming`;
    
    // Remplir les données de l'article
    const elements = {
        title: document.querySelector('.blog-title'),
        category: document.querySelector('.blog-category'),
        date: document.querySelector('.blog-date'),
        author: document.querySelector('.blog-author'),
        content: document.querySelector('.blog-content'),
        image: document.querySelector('.blog-hero-image img'),
        tags: document.querySelector('.blog-tags')
    };
    
    if (elements.title) elements.title.textContent = article.title;
    if (elements.category) elements.category.textContent = getCategoryLabel(article.category);
    if (elements.date) elements.date.textContent = `Publié le ${formatDate(article.created_at)}`;
    if (elements.author) elements.author.textContent = `Par ${article.author}`;
    if (elements.content) elements.content.innerHTML = article.content.replace(/\n/g, '<br>');
    if (elements.image) elements.image.src = `assets/images/${article.image}`;
    if (elements.tags) {
        elements.tags.innerHTML = article.tags.split(',').map(tag => 
            `<span class="tag">${tag.trim()}</span>`
        ).join('');
    }
    
    // Charger les commentaires
    loadArticleComments(articleId);
}

function loadArticleComments(articleId) {
    const commentsList = document.querySelector('.comments-list');
    const commentsTitle = document.querySelector('.comments-title');
    
    if (!commentsList) return;
    
    const comments = AppState.getCommentsByArticle(articleId);
    
    if (commentsTitle) {
        commentsTitle.textContent = `Commentaires (${comments.length})`;
    }
    
    commentsList.innerHTML = comments.map(comment => `
        <div class="comment">
            <div class="comment-header">
                <strong class="comment-author">${comment.author}</strong>
                <span class="comment-date">${formatDate(comment.created_at)}</span>
            </div>
            <p class="comment-content">${comment.content}</p>
        </div>
    `).join('');
}

function getCategoryLabel(category) {
    const categories = {
        'review': 'Test & Review',
        'news': 'Actualité',
        'tutorial': 'Tutoriel',
        'trends': 'Tendances',
        'interview': 'Interview'
    };
    return categories[category] || category;
}

// Gestion du formulaire de commentaire
document.addEventListener('submit', function(e) {
    if (e.target.classList.contains('comment-form')) {
        e.preventDefault();
        submitComment(e.target);
    }
    
    if (e.target.classList.contains('article-form')) {
        e.preventDefault();
        submitArticle(e.target);
    }
});

function submitComment(form) {
    const formData = new FormData(form);
    const articleId = formData.get('article_id');
    const author = formData.get('author');
    const content = formData.get('content');
    
    if (!author || !content) {
        alert('Veuillez remplir tous les champs');
        return;
    }
    
    const comment = {
        article_id: parseInt(articleId),
        author: author.trim(),
        content: content.trim()
    };
    
    AppState.addComment(comment);
    form.reset();
    alert('Commentaire publié avec succès!');
}

function submitArticle(form) {
    if (!AppState.isAdminUser()) {
        alert('Accès non autorisé');
        return;
    }
    
    const formData = new FormData(form);
    const articleData = {
        title: formData.get('title'),
        content: formData.get('content'),
        category: formData.get('category'),
        tags: formData.get('tags'),
        status: formData.get('status'),
        author: 'Admin'
    };
    
    const articleId = formData.get('id');
    
    if (articleId) {
        // Modification
        AppState.updateArticle(articleId, articleData);
        alert('Article modifié avec succès!');
    } else {
        // Création
        AppState.addArticle(articleData);
        alert('Article créé avec succès!');
    }
    
    window.location.href = 'backoffice/blog-admin.html';
}