// Push notification event handler — injected alongside VitePWA service worker
self.addEventListener('push', function (event) {
    if (!event.data) return;

    const data = event.data.json();

    event.waitUntil(
        self.registration.showNotification(data.title || 'CardioSecure', {
            body: data.body || "E' ora di assumere il tuo farmaco.",
            icon: data.icon || '/icons/icon-192.png',
            badge: '/icons/icon-192.png',
            vibrate: [200, 100, 200],
            tag: 'medication-reminder',
        })
    );
});

self.addEventListener('notificationclick', function (event) {
    event.notification.close();
    event.waitUntil(
        clients.openWindow('/patient/medications')
    );
});
