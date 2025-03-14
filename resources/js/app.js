import './bootstrap';
import { createApp } from 'vue';
import App from './App.vue';
import router from './router';
import axios from 'axios';
import ModalAlert from './components/ModalAlert.vue';
import '../css/app.css';

const app = createApp(App);

// Register global components
app.component('ModalAlert', ModalAlert);

// Configure axios defaults
axios.defaults.baseURL = import.meta.env.VITE_APP_API_URL;
axios.defaults.withCredentials = true;

// Add request interceptor 
axios.interceptors.request.use(config => {
    const token = localStorage.getItem('auth_token');
    config.headers['Accept'] = 'application/json';
    if (token) {
        config.headers['Authorization'] = `Bearer ${token}`;
    }
    return config;
});

// Add response interceptor
axios.interceptors.response.use(
  response => response,
  error => {
    if (error.response?.status === 401) {
      localStorage.removeItem('auth_token');
      // Dispatch a custom event that components can listen for
      window.dispatchEvent(new CustomEvent('logout'));
      router.push('/login');
    }
    return Promise.reject(error);
  }
);

// Add global event listeners for auth state changes
window.addEventListener('login', () => {
  // This will be used by components to update their auth state
  window.dispatchEvent(new Event('storage'));
});

window.addEventListener('logout', () => {
  // This will be used by components to update their auth state
  window.dispatchEvent(new Event('storage'));
});

// Simple toast notification function
window.toast = function(options) {
  const toast = document.createElement('div');
  toast.className = `fixed bottom-4 right-4 p-4 rounded-md shadow-lg z-50 ${
    options.type === 'error' ? 'bg-red-500' : 
    options.type === 'success' ? 'bg-green-500' : 
    options.type === 'warning' ? 'bg-yellow-500' : 'bg-blue-500'
  } text-white`;
  
  toast.innerHTML = `
    <div class="flex items-center">
      <div class="flex-shrink-0">
        ${options.type === 'error' ? 
          '<svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" /></svg>' : 
          options.type === 'success' ? 
          '<svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" /></svg>' : 
          options.type === 'warning' ? 
          '<svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" /></svg>' : 
          '<svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>'
        }
      </div>
      <div class="ml-3">
        <p class="font-medium">${options.title || ''}</p>
        <p class="text-sm">${options.description || ''}</p>
      </div>
    </div>
  `;
  
  document.body.appendChild(toast);
  
  setTimeout(() => {
    toast.classList.add('opacity-0', 'transition-opacity', 'duration-500');
    setTimeout(() => {
      document.body.removeChild(toast);
    }, 500);
  }, options.duration || 3000);
};

app.use(router);
app.mount('#app');