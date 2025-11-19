// Gestionnaire d'état global pour l'application
class StateManager {
    constructor() {
        this.state = {
            user: null,
            articles: [],
            comments: [],
            games: [],
            currentArticle: null,
            isAdmin: false
        };
        
        this.init();
    }

    init() {
        // Charger l'état depuis le localStorage
        this.loadState();
        
        // Initialiser les données par défaut
        this.initializeDefaultData();
    }

    // Charger l'état depuis le localStorage
    loadState() {
        const savedState = localStorage.getItem('gamingMuseumState');
        if (savedState) {
            this.state = { ...this.state, ...JSON.parse(savedState) };
        }
    }

    // Sauvegarder l'état dans le localStorage
    saveState() {
        localStorage.setItem('gamingMuseumState', JSON.stringify(this.state));
    }

    // Initialiser les données par défaut
    initializeDefaultData() {
        if (this.state.articles.length === 0) {
            this.state.articles = [
                {
                    id: 1,
                    title: "Cyberpunk 2077 : La Rédemption Est-Elle Au Rendez-Vous ?",
                    content: "Contenu de l'article sur Cyberpunk 2077...",
                    category: "review",
                    image: "cyberpunk-review.jpg",
                    author: "Alex Tech",
                    created_at: "2024-03-15",
                    status: "published",
                    tags: "cyberpunk, cd projekt red, rpg",
                    comment_count: 24
                },
                {
                    id: 2,
                    title: "GTA 6 : Toutes les Rumeurs et Confirmations",
                    content: "Contenu de l'article sur GTA 6...",
                    category: "news",
                    image: "gta6-news.jpg",
                    author: "Sarah Games",
                    created_at: "2024-03-12",
                    status: "published",
                    tags: "gta, rockstar, open world",
                    comment_count: 42
                }
            ];
        }

        if (this.state.comments.length === 0) {
            this.state.comments = [
                {
                    id: 1,
                    article_id: 1,
                    author: "GamerPro92",
                    content: "Je suis complètement d'accord avec l'article !",
                    created_at: "2024-03-16 14:23:00"
                },
                {
                    id: 2,
                    article_id: 1,
                    author: "TechLover",
                    content: "L'extension Phantom Liberty est vraiment excellente.",
                    created_at: "2024-03-16 12:47:00"
                }
            ];
        }

        this.saveState();
    }

    // Getters
    getArticles() {
        return this.state.articles.filter(article => article.status === 'published');
    }

    getAllArticles() {
        return this.state.articles;
    }

    getArticleById(id) {
        return this.state.articles.find(article => article.id === parseInt(id));
    }

    getCommentsByArticle(articleId) {
        return this.state.comments.filter(comment => comment.article_id === parseInt(articleId));
    }

    // Setters
    addArticle(article) {
        const newArticle = {
            ...article,
            id: this.generateId(),
            created_at: new Date().toISOString().split('T')[0],
            comment_count: 0
        };
        
        this.state.articles.push(newArticle);
        this.saveState();
        this.dispatchEvent('articlesUpdated');
        return newArticle;
    }

    updateArticle(id, updates) {
        const index = this.state.articles.findIndex(article => article.id === parseInt(id));
        if (index !== -1) {
            this.state.articles[index] = { ...this.state.articles[index], ...updates };
            this.saveState();
            this.dispatchEvent('articlesUpdated');
            return this.state.articles[index];
        }
        return null;
    }

    deleteArticle(id) {
        this.state.articles = this.state.articles.filter(article => article.id !== parseInt(id));
        // Supprimer aussi les commentaires associés
        this.state.comments = this.state.comments.filter(comment => comment.article_id !== parseInt(id));
        this.saveState();
        this.dispatchEvent('articlesUpdated');
    }

    addComment(comment) {
        const newComment = {
            ...comment,
            id: this.generateId(),
            created_at: new Date().toISOString().replace('T', ' ').substring(0, 19)
        };
        
        this.state.comments.push(newComment);
        
        // Mettre à jour le compteur de commentaires de l'article
        const article = this.getArticleById(comment.article_id);
        if (article) {
            article.comment_count = this.getCommentsByArticle(comment.article_id).length;
        }
        
        this.saveState();
        this.dispatchEvent('commentsUpdated');
        return newComment;
    }

    deleteComment(id) {
        const comment = this.state.comments.find(c => c.id === parseInt(id));
        if (comment) {
            this.state.comments = this.state.comments.filter(c => c.id !== parseInt(id));
            
            // Mettre à jour le compteur de commentaires de l'article
            const article = this.getArticleById(comment.article_id);
            if (article) {
                article.comment_count = this.getCommentsByArticle(comment.article_id).length;
            }
            
            this.saveState();
            this.dispatchEvent('commentsUpdated');
        }
    }

    // Gestion des événements
    on(event, callback) {
        document.addEventListener(`state:${event}`, callback);
    }

    dispatchEvent(event) {
        document.dispatchEvent(new CustomEvent(`state:${event}`));
    }

    // Utilitaires
    generateId() {
        return Math.max(0, ...this.state.articles.map(a => a.id), ...this.state.comments.map(c => c.id)) + 1;
    }

    // Authentification
    login(username, password) {
        // Simulation d'authentification
        if (username === 'admin' && password === 'admin') {
            this.state.user = { username: 'admin', role: 'admin' };
            this.state.isAdmin = true;
            this.saveState();
            return true;
        }
        return false;
    }

    logout() {
        this.state.user = null;
        this.state.isAdmin = false;
        this.saveState();
    }

    isAuthenticated() {
        return this.state.user !== null;
    }

    isAdminUser() {
        return this.state.isAdmin;
    }
}

// Instance globale
const AppState = new StateManager();