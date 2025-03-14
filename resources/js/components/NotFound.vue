<template>
  <div class="min-h-screen bg-gray-50 flex flex-col">
    <div class="flex-grow flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8">
      <div class="max-w-md w-full text-center">
        <div class="flex justify-center mb-6">
          <div class="w-16 h-16">
            <svg viewBox="0 0 36 36" fill="none" xmlns="http://www.w3.org/2000/svg" class="w-full h-full">
              <!-- Chameleon body (circle background) -->
              <circle cx="18" cy="18" r="18" fill="#10B981" />
              
              <!-- Chameleon body -->
              <path d="M28 14.5C28 14.5 26 12 23 13C20 14 18 16 18 16C18 16 16 14 13 13C10 12 8 14.5 8 14.5V23C8 23 10 25.5 13 24.5C16 23.5 18 21 18 21C18 21 20 23.5 23 24.5C26 25.5 28 23 28 23V14.5Z" fill="#0D9668" />
              
              <!-- Chameleon body details -->
              <path d="M28 14.5C28 14.5 26 17 23 18C20 19 18 16 18 16C18 16 16 19 13 18C10 17 8 14.5 8 14.5V23C8 23 10 25.5 13 24.5C16 23.5 18 21 18 21C18 21 20 23.5 23 24.5C26 25.5 28 23 28 23V14.5Z" fill="white" />
              
              <!-- Chameleon eyes (sad expression) -->
              <circle cx="12" cy="12" r="2" fill="black" />
              <circle cx="24" cy="12" r="2" fill="black" />
              <circle cx="12" cy="11.5" r="0.5" fill="white" />
              <circle cx="24" cy="11.5" r="0.5" fill="white" />
              
              <!-- Chameleon crest -->
              <path d="M18 6C18 6 16 8 16 10C16 12 18 14 18 14C18 14 20 12 20 10C20 8 18 6 18 6Z" fill="#0D9668" />
              
              <!-- Chameleon tail (drooping) -->
              <path d="M8 14.5C8 14.5 6 15 4 17C2 19 3 22 5 23C7 24 8 23 8 23" stroke="#0D9668" stroke-width="1.5" fill="none" />
            </svg>
          </div>
        </div>
        
        <h1 class="text-6xl font-extrabold text-gray-900 tracking-tight mb-4">404</h1>
        <h2 class="text-3xl font-bold text-emerald-600 mb-6">Page Not Found</h2>
        <p class="text-lg text-gray-600 mb-8">
          We couldn't find the page you're looking for. It might have been moved or doesn't exist.
        </p>
        
        <div class="flex flex-col sm:flex-row justify-center gap-4">
          <router-link 
            to="/" 
            class="px-6 py-3 rounded-md shadow-sm text-white bg-emerald-600 hover:bg-emerald-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-emerald-500 transition-colors duration-200 font-medium"
          >
            Back to Home
          </router-link>
          <router-link 
            to="/dashboard" 
            class="px-6 py-3 rounded-md text-emerald-700 bg-emerald-100 hover:bg-emerald-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-emerald-500 transition-colors duration-200 font-medium"
            v-if="isAuthenticated"
          >
            Go to Dashboard
          </router-link>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  name: 'NotFound',
  computed: {
    isAuthenticated() {
      return !!localStorage.getItem('auth_token');
    }
  }
};
</script>