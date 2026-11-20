/* Educ'Academy - Service Worker PWA & Web Push Notifications */

const CACHE_NAME = 'educ-academy-v1.0.0';
const OFFLINE_URL = '/offline.html';

const STATIC_ASSETS = [
    OFFLINE_URL,
    '/favicon.png',
    '/manifest.json',
    '/images/logo.jpeg',
    '/images/icons/icon-192x192.png',
    '/images/icons/icon-512x512.png',
    '/images/icons/icon-maskable-192x192.png',
    '/images/icons/icon-maskable-512x512.png',
    '/images/icons/apple-touch-icon.png'
];

// 1. Installation : mise en cache des ressources d'application indispensables
self.addEventListener('install', event => {
    event.waitUntil(
        caches.open(CACHE_NAME).then(cache => {
            return cache.addAll(STATIC_ASSETS);
        }).then(() => self.skipWaiting())
    );
});

// 2. Activation : nettoyage des anciens caches
self.addEventListener('activate', event => {
    event.waitUntil(
        caches.keys().then(cacheNames => {
            return Promise.all(
                cacheNames.map(name => {
                    if (name !== CACHE_NAME) {
                        return caches.delete(name);
                    }
                })
            );
        }).then(() => self.clients.claim())
    );
});

// 3. Interception réseau (Fetch) : Network-First pour les pages HTML, fallback hors-ligne
self.addEventListener('fetch', event => {
    const request = event.request;

    // Uniquement pour les requêtes HTTP/HTTPS GET
    if (request.method !== 'GET') return;

    // Requêtes de navigation (pages web)
    if (request.mode === 'navigate') {
        event.respondWith(
            fetch(request).catch(() => {
                return caches.match(OFFLINE_URL);
            })
        );
        return;
    }

    // Fichiers statiques d'assets (CSS, JS compilés, images, icônes) : Stale-While-Revalidate
    if (request.url.includes('/build/') || request.url.includes('/images/') || request.url.includes('/fonts/')) {
        event.respondWith(
            caches.match(request).then(cachedResponse => {
                const fetchPromise = fetch(request).then(networkResponse => {
                    if (networkResponse && networkResponse.status === 200) {
                        const responseToCache = networkResponse.clone();
                        caches.open(CACHE_NAME).then(cache => {
                            cache.put(request, responseToCache);
                        });
                    }
                    return networkResponse;
                }).catch(() => cachedResponse);

                return cachedResponse || fetchPromise;
            })
        );
        return;
    }

    // Requêtes par défaut : réseau avec fallback cache
    event.respondWith(
        fetch(request).catch(() => caches.match(request))
    );
});

// 4. Réception d'une notification push Web Push VAPID
self.addEventListener('push', event => {
    let payload = {
        title: "Educ'Academy",
        body: "Une nouvelle notification de votre professeur est disponible.",
        icon: "/images/icons/icon-192x192.png",
        badge: "/images/icons/icon-192x192.png",
        data: {
            url: "/espace-eleve"
        }
    };

    if (event.data) {
        try {
            const data = event.data.json();
            payload = Object.assign(payload, data);
        } catch (e) {
            payload.body = event.data.text();
        }
    }

    const options = {
        body: payload.body,
        icon: payload.icon || "/images/icons/icon-192x192.png",
        badge: payload.badge || "/images/icons/icon-192x192.png",
        vibrate: [200, 100, 200],
        tag: payload.tag || "educ-academy-notification",
        renotify: true,
        data: payload.data || { url: "/espace-eleve" },
        actions: payload.actions || [
            { action: 'open', title: 'Ouvrir l\'application' }
        ]
    };

    event.waitUntil(
        self.registration.showNotification(payload.title, options)
    );
});

// 5. Clic sur la notification : ouvrir ou réactiver la fenêtre de l'application
self.addEventListener('notificationclick', event => {
    event.notification.close();

    const targetUrl = (event.notification.data && event.notification.data.url) 
        ? event.notification.data.url 
        : '/espace-eleve';

    event.waitUntil(
        clients.matchAll({ type: 'window', includeUncontrolled: true }).then(windowClients => {
            for (let client of windowClients) {
                if ('focus' in client) {
                    client.navigate(targetUrl);
                    return client.focus();
                }
            }
            if (clients.openWindow) {
                return clients.openWindow(targetUrl);
            }
        })
    );
});
