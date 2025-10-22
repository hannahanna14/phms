import axios from 'axios';
window.axios = axios;

window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';

// Function to get current CSRF token
const getCSRFToken = () => {
    const token = document.head.querySelector('meta[name="csrf-token"]');
    return token ? token.content : null;
};

// Function to refresh CSRF token
const refreshCSRFToken = async () => {
    try {
        const response = await fetch('/csrf-token', {
            method: 'GET',
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        });
        
        if (response.ok) {
            const data = await response.json();
            const tokenMeta = document.head.querySelector('meta[name="csrf-token"]');
            if (tokenMeta && data.csrf_token) {
                tokenMeta.content = data.csrf_token;
                window.axios.defaults.headers.common['X-CSRF-TOKEN'] = data.csrf_token;
                console.log('CSRF token refreshed successfully');
                return data.csrf_token;
            }
        }
    } catch (error) {
        console.error('Failed to refresh CSRF token:', error);
    }
    return null;
};

// Set initial CSRF token for all requests
const token = getCSRFToken();
if (token) {
    window.axios.defaults.headers.common['X-CSRF-TOKEN'] = token;
} else {
    console.error('CSRF token not found: https://laravel.com/docs/csrf#csrf-x-csrf-token');
}

// Add request interceptor to ensure fresh CSRF token
window.axios.interceptors.request.use(
    config => {
        const currentToken = getCSRFToken();
        if (currentToken) {
            config.headers['X-CSRF-TOKEN'] = currentToken;
        }
        return config;
    },
    error => Promise.reject(error)
);

// Add response interceptor to handle CSRF token expiration
window.axios.interceptors.response.use(
    response => response,
    async error => {
        if (error.response && error.response.status === 419) {
            console.log('CSRF token expired, attempting to refresh...');
            
            // Try to refresh the token
            const newToken = await refreshCSRFToken();
            
            if (newToken && error.config) {
                // Retry the original request with new token
                error.config.headers['X-CSRF-TOKEN'] = newToken;
                return window.axios.request(error.config);
            } else {
                // If refresh fails, redirect to login
                console.log('CSRF token refresh failed, redirecting to login...');
                window.location.href = '/login';
            }
        }
        
        return Promise.reject(error);
    }
);

// Make refresh function globally available
window.refreshCSRFToken = refreshCSRFToken;
