const CACHE_NAME = 'player-cache-v1';
const ASSETS_TO_CACHE = [
    '/js/player-pwa.js',
    '/css/dashboard.css', // Si utilisé
    // Ajouter d'autres assets statiques ici si nécessaire
];

// Installation : Cache les assets statiques
self.addEventListener('install', (event) => {
    self.skipWaiting();
    event.waitUntil(
        caches.open(CACHE_NAME).then((cache) => {
            return cache.addAll(ASSETS_TO_CACHE);
        })
    );
});

// Activation : Nettoyage anciens caches
self.addEventListener('activate', (event) => {
    event.waitUntil(
        caches.keys().then((cacheNames) => {
            return Promise.all(
                cacheNames.map((cacheName) => {
                    if (cacheName !== CACHE_NAME) {
                        return caches.delete(cacheName);
                    }
                })
            );
        })
    );
    self.clients.claim();
});

// Interception des requêtes
self.addEventListener('fetch', (event) => {
    const url = new URL(event.request.url);

    // Stratégie pour les APIs (playlist json) : Network First, puis Cache (non implémenté pour l'instant)
    // Ici on veut surtout servir les médias depuis le cache s'ils y sont

    event.respondWith(
        caches.match(event.request).then((cachedResponse) => {
            if (cachedResponse) {
                return cachedResponse;
            }

            // Si pas dans le cache, on va chercher sur le réseau
            return fetch(event.request).then((networkResponse) => {
                // Si c'est un média (image/video), on pourrait le cacher dynamiquement ici, 
                // mais on préfère le gérer via le préchargement explicite en JS.
                return networkResponse;
            });
        })
    );
});

// Écoute des messages du client (pour pré-cacher des fichiers spécifiques)
self.addEventListener('message', (event) => {
    if (event.data && event.data.type === 'CACHE_FILES') {
        const files = event.data.files;
        caches.open(CACHE_NAME).then((cache) => {
            cache.addAll(files).then(() => {
                // Notifier le client que c'est fini si besoin
                // console.log('Fichiers mis en cache:', files);
            });
        });
    }
});
