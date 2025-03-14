<template>
  <nav 
    ref="navbar"
    class="fixed top-0 left-0 right-0 bg-white z-50 transition-transform duration-300"
    :class="{ 
    'translate-y-[-100%]': isHomePage && isScrollingDown && scrollY > 100,
    'border-b border-gray-100': !isHomePage
  }"
  >
    <div class="container mx-auto px-4 sm:px-6 lg:px-8">
      <div class="flex justify-between h-16">
        <!-- Logo and brand -->
        <div class="flex items-center">
          <router-link to="/" class="flex items-center">
            <div class="w-8 h-8 mr-2">
              <svg viewBox="0 0 36 36" fill="none" xmlns="http://www.w3.org/2000/svg" class="w-full h-full">
                <!-- Chameleon body (circle background) -->
                <circle cx="18" cy="18" r="18" fill="#10B981"/>
                
                <!-- Chameleon body -->
                <path d="M28 14.5C28 14.5 26 12 23 13C20 14 18 16 18 16C18 16 16 14 13 13C10 12 8 14.5 8 14.5V23C8 23 10 25.5 13 24.5C16 23.5 18 21 18 21C18 21 20 23.5 23 24.5C26 25.5 28 23 28 23V14.5Z" fill="#0D9668"/>
                
                <!-- Chameleon body details -->
                <path d="M28 14.5C28 14.5 26 17 23 18C20 19 18 16 18 16C18 16 16 19 13 18C10 17 8 14.5 8 14.5V23C8 23 10 25.5 13 24.5C16 23.5 18 21 18 21C18 21 20 23.5 23 24.5C26 25.5 28 23 28 23V14.5Z" fill="white"/>
                
                <!-- Chameleon eyes -->
                <circle cx="12" cy="12" r="2" fill="black"/>
                <circle cx="24" cy="12" r="2" fill="black"/>
                <circle cx="12" cy="11.5" r="0.5" fill="white"/>
                <circle cx="24" cy="11.5" r="0.5" fill="white"/>
                
                <!-- Chameleon crest -->
                <path d="M18 6C18 6 16 8 16 10C16 12 18 14 18 14C18 14 20 12 20 10C20 8 18 6 18 6Z" fill="#0D9668"/>
                
                <!-- Chameleon tail -->
                <path d="M8 14.5C8 14.5 6 15 4 17C2 19 3 22 5 23C7 24 8 23 8 23" stroke="#0D9668" stroke-width="1.5" fill="none"/>
                
                <!-- Chameleon legs -->
                <path d="M10 19C8 20 7 22 8 24" stroke="#0D9668" stroke-width="1.5"/>
                <path d="M26 19C28 20 29 22 28 24" stroke="#0D9668" stroke-width="1.5"/>
              </svg>
            </div>
            <span class="text-xl font-bold text-emerald-600">Resume Chameleon</span>
          </router-link>
        </div>
        
        <!-- Desktop navigation -->
        <div class="hidden md:flex items-center space-x-6">
          <template v-if="isAuthenticated">
            <router-link 
              to="/dashboard" 
              class="text-gray-600 hover:text-emerald-600 transition-colors duration-200 font-medium"
              :class="{ 'text-emerald-600 font-bold': isActive('/dashboard') }"
            >
              Dashboard
            </router-link>
            <router-link 
              to="/resumes" 
              class="text-gray-600 hover:text-emerald-600 transition-colors duration-200 font-medium"
              :class="{ 'text-emerald-600 font-bold': isActive('/resumes') }"
            >
              Resumes
            </router-link>
            <router-link 
              to="/settings" 
              class="text-gray-600 hover:text-emerald-600 transition-colors duration-200 font-medium"
              :class="{ 'text-emerald-600 font-bold': isActive('/settings') }"
            >
              Settings
            </router-link>
            <button 
              @click="logout" 
              class="text-gray-600 hover:text-emerald-600 transition-colors duration-200 font-medium"
            >
              Logout
            </button>
          </template>
          <template v-else>
            <router-link 
              to="/" 
              class="text-gray-600 hover:text-emerald-600 transition-colors duration-200 font-medium"
              :class="{ 'text-emerald-600 font-bold': isActive('/') }"
            >
              Home
            </router-link>
            <router-link 
              to="/pricing" 
              class="text-gray-600 hover:text-emerald-600 transition-colors duration-200 font-medium"
              :class="{ 'text-emerald-600 font-bold': isActive('/pricing') }"
            >
              Pricing
            </router-link>
            <router-link 
              to="/login" 
              class="inline-flex items-center justify-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-emerald-600 hover:bg-emerald-700 transition-colors duration-200"
              :class="{ 'bg-emerald-700': isActive('/login') }"
            >
              Sign In
            </router-link>
          </template>
        </div>
        
        <!-- Mobile menu button -->
        <div class="flex items-center md:hidden">
          <button 
            @click="isMenuOpen = !isMenuOpen" 
            class="inline-flex items-center justify-center p-2 rounded-md text-gray-600 hover:text-emerald-600 hover:bg-gray-100 focus:outline-none"
            aria-expanded="false"
          >
            <span class="sr-only">Open main menu</span>
            <!-- Icon when menu is closed -->
            <svg 
              v-if="!isMenuOpen" 
              class="block h-6 w-6" 
              xmlns="http://www.w3.org/2000/svg" 
              fill="none" 
              viewBox="0 0 24 24" 
              stroke="currentColor" 
              aria-hidden="true"
            >
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
            </svg>
            <!-- Icon when menu is open -->
            <svg 
              v-else 
              class="block h-6 w-6" 
              xmlns="http://www.w3.org/2000/svg" 
              fill="none" 
              viewBox="0 0 24 24" 
              stroke="currentColor" 
              aria-hidden="true"
            >
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
            </svg>
          </button>
        </div>
      </div>
    </div>
    
    <!-- Mobile menu, show/hide based on menu state -->
    <div v-if="isMenuOpen" class="md:hidden bg-white border-b border-gray-200">
      <div class="px-2 pt-2 pb-3 space-y-1 sm:px-3">
        <template v-if="isAuthenticated">
          <router-link 
            to="/dashboard" 
            class="block px-3 py-2 rounded-md text-base font-medium text-gray-600 hover:text-emerald-600 hover:bg-gray-50"
            :class="{ 'text-emerald-600 font-bold bg-gray-50': isActive('/dashboard') }"
            @click="isMenuOpen = false"
          >
            Dashboard
          </router-link>
          <router-link 
            to="/resumes" 
            class="block px-3 py-2 rounded-md text-base font-medium text-gray-600 hover:text-emerald-600 hover:bg-gray-50"
            :class="{ 'text-emerald-600 font-bold bg-gray-50': isActive('/resumes') }"
            @click="isMenuOpen = false"
          >
            Resumes
          </router-link>
          <router-link 
            to="/settings" 
            class="block px-3 py-2 rounded-md text-base font-medium text-gray-600 hover:text-emerald-600 hover:bg-gray-50"
            :class="{ 'text-emerald-600 font-bold bg-gray-50': isActive('/settings') }"
            @click="isMenuOpen = false"
          >
            Settings
          </router-link>
          <button 
            @click="handleLogout" 
            class="block w-full text-left px-3 py-2 rounded-md text-base font-medium text-gray-600 hover:text-emerald-600 hover:bg-gray-50"
          >
            Logout
          </button>
        </template>
        <template v-else>
          <router-link 
            to="/" 
            class="block px-3 py-2 rounded-md text-base font-medium text-gray-600 hover:text-emerald-600 hover:bg-gray-50"
            :class="{ 'text-emerald-600 font-bold bg-gray-50': isActive('/') }"
            @click="isMenuOpen = false"
          >
            Home
          </router-link>
          <router-link 
            to="/pricing" 
            class="block px-3 py-2 rounded-md text-base font-medium text-gray-600 hover:text-emerald-600 hover:bg-gray-50"
            :class="{ 'text-emerald-600 font-bold bg-gray-50': isActive('/pricing') }"
            @click="isMenuOpen = false"
          >
            Pricing
          </router-link>
          <router-link 
            to="/login" 
            class="block px-3 py-2 rounded-md text-base font-medium text-gray-600 hover:text-emerald-600 hover:bg-gray-50"
            :class="{ 'text-emerald-600 font-bold bg-gray-50': isActive('/login') }"
            @click="isMenuOpen = false"
          >
            Sign In
          </router-link>
        </template>
        <div class="border-t border-gray-200 pt-4 mt-4">
          <div class="flex space-x-4 px-3">
            <router-link 
              to="/terms" 
              class="text-sm text-gray-500 hover:text-emerald-600"
              :class="{ 'text-emerald-600 font-bold': isActive('/terms') }"
              @click="isMenuOpen = false"
            >
              Terms
            </router-link>
            <router-link 
              to="/privacy" 
              class="text-sm text-gray-500 hover:text-emerald-600"
              :class="{ 'text-emerald-600 font-bold': isActive('/privacy') }"
              @click="isMenuOpen = false"
            >
              Privacy
            </router-link>
          </div>
        </div>
      </div>
    </div>
  </nav>
  <!-- Spacer to prevent content from hiding under fixed navbar -->
  <div class="h-16"></div>
</template>

<script setup>
import { ref, computed, watch, onMounted, onUnmounted, nextTick } from 'vue';
import { useRouter, useRoute } from 'vue-router';
import axios from 'axios';

const router = useRouter();
const route = useRoute();
const authToken = ref(localStorage.getItem('auth_token'));
const isMenuOpen = ref(false);
const navbar = ref(null);

// Scroll state
const scrollY = ref(0);
const lastScrollY = ref(0);
const isScrollingDown = ref(false);

const isAuthenticated = computed(() => !!authToken.value);
const isHomePage = computed(() => route.path === '/');

// Define emits
const emit = defineEmits(['auth-changed']);

// Enhanced function to check if current route matches the given path
const isActive = (path) => {
  // Exact match
  if (route.path === path) return true;
  
  // Check if it's a nested route
  if (path !== '/' && route.path.startsWith(`${path}/`)) return true;
  
  // Special case for dashboard with nested routes
  if (path === '/dashboard' && route.path.startsWith('/dashboard')) return true;
  
  // Special case for resumes with nested routes
  if (path === '/resumes' && route.path.startsWith('/resumes')) return true;
  
  // Special case for settings with nested routes
  if (path === '/settings' && route.path.startsWith('/settings')) return true;
  
  return false;
};

// Handle scroll events
const handleScroll = () => {
  scrollY.value = window.scrollY;
  isScrollingDown.value = scrollY.value > lastScrollY.value;
  lastScrollY.value = scrollY.value;
};

// Watch for route changes to update auth token and force re-render
watch(() => route.fullPath, async () => {
  // Update auth token from localStorage
  authToken.value = localStorage.getItem('auth_token');
  // Emit auth state change
  emit('auth-changed', !!authToken.value);
  // Reset scroll position tracking when route changes
  scrollY.value = window.scrollY;
  lastScrollY.value = window.scrollY;
  isScrollingDown.value = false;
  // Force component re-render when route changes to ensure active state updates
  await nextTick();
}, { immediate: true });

// Watch for auth token changes
watch(() => authToken.value, (newValue) => {
  emit('auth-changed', !!newValue);
});

// Close mobile menu when window is resized to desktop size
const handleResize = () => {
  if (window.innerWidth >= 768 && isMenuOpen.value) {
    isMenuOpen.value = false;
  }
};

onMounted(() => {
  window.addEventListener('resize', handleResize);
  window.addEventListener('scroll', handleScroll);
  // Emit initial auth state
  emit('auth-changed', isAuthenticated.value);
});

onUnmounted(() => {
  window.removeEventListener('resize', handleResize);
  window.removeEventListener('scroll', handleScroll);
});

const handleLogout = () => {
  isMenuOpen.value = false;
  logout();
};

const logout = async () => {
  try {
    await axios.post('/api/logout', {}, {
      headers: { Authorization: `Bearer ${authToken.value}` }
    });
  } catch (error) {
    console.error('Logout error:', error);
  }
  
  // Always remove token regardless of API response
  localStorage.removeItem('auth_token');
  authToken.value = null;
  
  // Emit auth state change
  emit('auth-changed', false);
  
  // Dispatch a custom event for logout
  window.dispatchEvent(new CustomEvent('logout'));
  
  router.push('/login');
};
</script>