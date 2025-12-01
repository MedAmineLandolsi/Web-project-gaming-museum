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
                    content: "Cyberpunk 2077 a connu un parcours tumultueux depuis son lancement. Après des années de correctifs et d'améliorations, le jeu propose aujourd'hui une expérience bien plus aboutie.\n\nL'extension Phantom Liberty a notamment apporté un nouveau souffle au titre avec une campagne additionnelle de qualité et des mécaniques de jeu améliorées. Les graphismes sont superbes et l'atmosphère de Night City est plus immersive que jamais.\n\nSi vous avez abandonné le jeu à sa sortie, c'est le moment de lui donner une seconde chance !",
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
                    content: "Alors que Rockstar Games a officiellement annoncé le développement de GTA 6, la communauté s'interroge sur les détails du prochain opus.\n\nBasé à Vice City, le jeu proposerait pour la première fois un duo de protagonistes, dont une femme protagoniste. Les fuites suggèrent également une carte massive et un niveau de détail jamais vu dans un jeu open-world.\n\nLa sortie est attendue pour 2025, mais Rockstar garde le silence sur les détails officiels.",
                    category: "news",
                    image: "gta6-news.jpg",
                    author: "Sarah Games",
                    created_at: "2024-03-12",
                    status: "published",
                    tags: "gta, rockstar, open world",
                    comment_count: 42
                },
                {
                    id: 3,
                    title: "Guide Débutant : Les Bases du Streaming Gaming",
                    content: "Vous souhaitez vous lancer dans le streaming gaming ? Voici les bases essentielles pour bien commencer.\n\n1. Choisissez votre plateforme (Twitch, YouTube Gaming, etc.)\n2. Investissez dans un bon microphone\n3. Configurez OBS Studio correctement\n4. Créez un planning régulier\n5. Interagissez avec votre communauté\n\nLe streaming demande de la patience et de la régularité, mais c'est une expérience très enrichissante !",
                    category: "tutorial",
                    image: "streaming-guide.jpg",
                    author: "Mike Data",
                    created_at: "2024-03-10",
                    status: "published",
                    tags: "streaming, tutoriel, twitch",
                    comment_count: 15
                }
            ];
        }

        if (this.state.comments.length === 0) {
            this.state.comments = [
                {
                    id: 1,
                    article_id: 1,
                    author: "GamerPro92",
                    content: "Je suis complètement d'accord avec l'article ! J'ai réessayé Cyberpunk récemment et la différence est impressionnante.",
                    created_at: "2024-03-16T14:23:00"
                },
                {
                    id: 2,
                    article_id: 1,
                    author: "TechLover",
                    content: "L'extension Phantom Liberty est vraiment excellente. L'histoire de Solomon Reed est captivante !",
                    created_at: "2024-03-16T12:47:00"
                },
                {
                    id: 3,
                    article_id: 2,
                    author: "GTAFan",
                    content: "J'espère que GTA 6 conservera l'humour et la satire des opus précédents. C'est ce qui fait le charme de la série !",
                    created_at: "2024-03-15T09:15:00"
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

    // CORRECTION : Ajout de la méthode getCommentsByArticleId
    getCommentsByArticleId(articleId) {
        return this.state.comments.filter(comment => comment.article_id === parseInt(articleId));
    }

    // Ancienne méthode gardée pour la compatibilité
    getCommentsByArticle(articleId) {
        return this.getCommentsByArticleId(articleId);
    }

    // Setters
    addArticle(article) {
        const newArticle = {
            ...article,
            id: this.generateId(),
            created_at: new Date().toISOString().split('T')[0],
            comment_count: 0,
            status: article.status || 'pending'
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
            created_at: new Date().toISOString()
        };
        
        this.state.comments.push(newComment);
        
        // Mettre à jour le compteur de commentaires de l'article
        const article = this.getArticleById(comment.article_id);
        if (article) {
            article.comment_count = this.getCommentsByArticleId(comment.article_id).length;
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
                article.comment_count = this.getCommentsByArticleId(comment.article_id).length;
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
        const articleIds = this.state.articles.map(a => a.id);
        const commentIds = this.state.comments.map(c => c.id);
        const allIds = [...articleIds, ...commentIds];
        return allIds.length > 0 ? Math.max(...allIds) + 1 : 1;
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