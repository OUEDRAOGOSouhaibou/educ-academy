/**
 * Educ'Academy - Gestionnaire PWA & Notifications Push
 */

let deferredInstallPrompt = null;

// Conversion de la clé publique VAPID Base64URL vers Uint8Array
function urlBase64ToUint8Array(base64String) {
    const padding = '='.repeat((4 - base64String.length % 4) % 4);
    const base64 = (base64String + padding)
        .replace(/\-/g, '+')
        .replace(/_/g, '/');

    const rawData = window.atob(base64);
    const outputArray = new Uint8Array(rawData.length);

    for (let i = 0; i < rawData.length; ++i) {
        outputArray[i] = rawData.charCodeAt(i);
    }
    return outputArray;
}

export function initPWA() {
    // 1. Enregistrement du Service Worker
    if ('serviceWorker' in navigator) {
        window.addEventListener('load', () => {
            navigator.serviceWorker.register('/sw.js')
                .then(registration => {
                    // Synchronisation périodique si l'utilisateur est déjà abonné
                    syncPushSubscription(registration);
                })
                .catch(err => {
                    console.warn('[PWA] Échec enregistrement Service Worker:', err);
                });
        });
    }

    // 2. Gestion de l'invite d'installation (bannière "Ajouter à l'écran d'accueil")
    window.addEventListener('beforeinstallprompt', e => {
        e.preventDefault();
        deferredInstallPrompt = e;

        // Afficher la bannière d'installation si l'utilisateur ne l'a pas fermée récemment
        const pwaDismissed = localStorage.getItem('ea_pwa_dismissed');
        const installBanner = document.getElementById('pwa-install-banner');
        if (installBanner && !pwaDismissed) {
            installBanner.classList.remove('d-none');
        }

        // Activer tout bouton personnalisé avec la classe .btn-install-pwa
        document.querySelectorAll('.btn-install-pwa').forEach(btn => {
            btn.classList.remove('d-none');
            btn.addEventListener('click', promptPWAInstallation);
        });
    });

    // 3. Détection de l'installation réussie
    window.addEventListener('appinstalled', () => {
        deferredInstallPrompt = null;
        const installBanner = document.getElementById('pwa-install-banner');
        if (installBanner) {
            installBanner.classList.add('d-none');
        }
        localStorage.setItem('ea_pwa_installed', 'true');
    });

    // 4. Fermeture de la bannière
    const closeBtn = document.getElementById('btn-dismiss-pwa-banner');
    if (closeBtn) {
        closeBtn.addEventListener('click', () => {
            const installBanner = document.getElementById('pwa-install-banner');
            if (installBanner) installBanner.classList.add('d-none');
            localStorage.setItem('ea_pwa_dismissed', Date.now().toString());
        });
    }

    // 5. Bouton d'activation des notifications push
    document.querySelectorAll('.btn-toggle-push').forEach(btn => {
        btn.addEventListener('click', togglePushSubscription);
        updatePushButtonState(btn);
    });
}

// Déclenchement de l'invite native d'installation
export function promptPWAInstallation() {
    if (!deferredInstallPrompt) return;

    deferredInstallPrompt.prompt();
    deferredInstallPrompt.userChoice.then(choiceResult => {
        if (choiceResult.outcome === 'accepted') {
            console.log('[PWA] Utilisateur a accepté l\'installation');
        }
        deferredInstallPrompt = null;
    });
}

// Synchronisation de la souscription Push existante
async function syncPushSubscription(registration) {
    if (!('PushManager' in window) || Notification.permission !== 'granted') {
        return;
    }

    try {
        const subscription = await registration.pushManager.getSubscription();
        if (subscription) {
            await sendSubscriptionToServer(subscription);
        }
    } catch (e) {
        console.warn('[PWA Push] Erreur vérification souscription:', e);
    }
}

// Basculer l'abonnement aux notifications push
export async function togglePushSubscription() {
    if (!('serviceWorker' in navigator) || !('PushManager' in window)) {
        alert('Votre navigateur ne supporte pas les notifications push.');
        return;
    }

    try {
        const permission = await Notification.requestPermission();
        if (permission !== 'granted') {
            alert('Vous avez refusé les notifications. Vous pouvez les réactiver dans les paramètres de votre navigateur.');
            return;
        }

        const registration = await navigator.serviceWorker.ready;
        let subscription = await registration.pushManager.getSubscription();

        if (!subscription) {
            // Récupérer la clé publique VAPID du serveur
            const response = await fetch('/push/public-key');
            const data = await response.json();

            if (!data.publicKey) {
                throw new Error('Clé VAPID non disponible sur le serveur.');
            }

            subscription = await registration.pushManager.subscribe({
                userVisibleOnly: true,
                applicationServerKey: urlBase64ToUint8Array(data.publicKey)
            });

            await sendSubscriptionToServer(subscription);
            alert('🔔 Notifications activées avec succès ! Vous recevrez les alertes des cours et validations.');
        } else {
            // Désabonnement
            await fetch('/push/unsubscribe', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
                },
                body: JSON.stringify({ endpoint: subscription.endpoint })
            });
            await subscription.unsubscribe();
            alert('Notifications désactivées pour ce terminal.');
        }

        document.querySelectorAll('.btn-toggle-push').forEach(updatePushButtonState);
    } catch (err) {
        console.error('[PWA Push] Erreur:', err);
        alert('Une erreur est survenue lors de l\'activation des notifications.');
    }
}

// Envoi de la souscription au serveur Laravel
async function sendSubscriptionToServer(subscription) {
    const rawKey = subscription.getKey ? subscription.getKey('p256dh') : '';
    const key = rawKey ? btoa(String.fromCharCode.apply(null, new Uint8Array(rawKey))) : '';
    const rawAuthSecret = subscription.getKey ? subscription.getKey('auth') : '';
    const authSecret = rawAuthSecret ? btoa(String.fromCharCode.apply(null, new Uint8Array(rawAuthSecret))) : '';

    await fetch('/push/subscribe', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
        },
        body: JSON.stringify({
            endpoint: subscription.endpoint,
            keys: {
                p256dh: key,
                auth: authSecret
            }
        })
    });
}

// Mise à jour de l'état visuel du bouton push
async function updatePushButtonState(btn) {
    if (!('serviceWorker' in navigator) || !('PushManager' in window)) {
        btn.style.display = 'none';
        return;
    }

    try {
        const registration = await navigator.serviceWorker.ready;
        const subscription = await registration.pushManager.getSubscription();

        if (subscription && Notification.permission === 'granted') {
            btn.classList.remove('btn-outline-primary', 'btn-outline-light');
            btn.classList.add('btn-success');
            btn.innerHTML = '<i class="bi bi-bell-fill me-1"></i> Notifications actives';
        } else {
            btn.innerHTML = '<i class="bi bi-bell me-1"></i> Activer les notifications';
        }
    } catch (e) {
        // En attente
    }
}
