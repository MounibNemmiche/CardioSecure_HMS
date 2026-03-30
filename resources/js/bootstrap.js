import axios from 'axios';
window.axios = axios;

window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';

// Hostinger shared hosting blocks PUT/PATCH/DELETE HTTP methods.
// Spoof them as POST with _method so Laravel's method spoofing handles it.
axios.interceptors.request.use((config) => {
    const method = config.method?.toUpperCase();
    if (method === 'PUT' || method === 'PATCH' || method === 'DELETE') {
        if (config.data instanceof FormData) {
            config.data.append('_method', method);
        } else if (typeof config.data === 'string') {
            try {
                const parsed = JSON.parse(config.data);
                parsed._method = method;
                config.data = JSON.stringify(parsed);
            } catch {
                config.data = JSON.stringify({ _method: method });
            }
        } else {
            config.data = { ...config.data, _method: method };
        }
        config.method = 'post';
    }
    return config;
});
