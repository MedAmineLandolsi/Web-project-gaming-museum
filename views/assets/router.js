// Routeur simple pour la navigation SPA-like
class Router {
    constructor() {
        this.routes = {};
        this.currentRoute = '';
        this.init();
    }

    init() {
        // Écouter les clics sur les liens
        document.addEventListener('click', (e) => {
            if (e.target.matches('[data-route]') || e.target.closest('[data-route]')) {
                e.preventDefault();
                const link = e.target.closest('[data-route]');
                const route = link.getAttribute('data-route');
                this.navigate(route);
            }
        });

        // Écouter les changements d'URL
        window.addEventListener('popstate', () => {
            this.handleRoute(window.location.pathname);
        });

        // Route initiale
        this.handleRoute(window.location.pathname);
    }

    addRoute(path, callback) {
        this.routes[path] = callback;
    }

    navigate(path) {
        history.pushState(null, null, path);
        this.handleRoute(path);
    }

    handleRoute(path) {
        this.currentRoute = path;
        
        // Trouver la route correspondante
        const route = Object.keys(this.routes).find(route => {
            if (route.includes(':')) {
                const routeParts = route.split('/');
                const pathParts = path.split('/');
                if (routeParts.length === pathParts.length) {
                    return routeParts.every((part, index) => {
                        return part.startsWith(':') || part === pathParts[index];
                    });
                }
            }
            return route === path;
        });

        if (route && this.routes[route]) {
            this.routes[route](path);
        } else {
            // Route par défaut
            console.log('Route non trouvée:', path);
        }
    }
}

// Instance globale du routeur
const AppRouter = new Router();