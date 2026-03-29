<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <title inertia>{{ config('app.name', 'CardioSecure') }}</title>
        @routes
        @vite(['resources/css/app.css', 'resources/js/app.js'])
        @inertiaHead
        <link rel="manifest" href="/manifest.webmanifest">
        <meta name="theme-color" content="#1e40af">
        <script>
            if ('serviceWorker' in navigator) {
                navigator.serviceWorker.register('/build/sw.js', { scope: '/' })
                    .catch(function (err) { console.warn('SW registration failed:', err); });
            }
            // Capture beforeinstallprompt globally so it is available regardless of which page mounts first
            window._pwaInstallPrompt = null;
            window.addEventListener('beforeinstallprompt', function (e) {
                e.preventDefault();
                window._pwaInstallPrompt = e;
                window.dispatchEvent(new CustomEvent('pwa-install-ready'));
            });
        </script>
    </head>
    <body class="antialiased bg-gray-50">
        @inertia
    </body>
</html>
