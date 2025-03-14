<template>
  <div class="min-h-screen flex items-center justify-center bg-gray-50 py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-md w-full space-y-8 text-center">
      <div class="flex flex-col items-center justify-center">
        <div class="relative">
          <div class="h-16 w-16 relative">
            <div class="absolute inset-0 rounded-full border-t-4 border-emerald-500 animate-spin"></div>
            <div class="absolute inset-3 rounded-full bg-white flex items-center justify-center">
              <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-emerald-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
              </svg>
            </div>
          </div>
        </div>
        <h2 class="mt-6 text-2xl font-bold text-gray-900">Authenticating</h2>
        <p class="mt-2 text-sm text-gray-600">Please wait while we securely sign you in</p>
      </div>
    </div>
  </div>
</template>
 
<script setup>
import { onMounted } from 'vue';
import { useRouter, useRoute } from 'vue-router';

const router = useRouter();
const route = useRoute();

onMounted(() => {
  const token = route.query.token;
  if (token) {
    localStorage.setItem('auth_token', token);
    window.dispatchEvent(new Event('storage'));
    
    // Add a slight delay for better UX
    setTimeout(() => {
      router.push('/dashboard');
    }, 1500);
  } else {
    router.push('/login');
  }
});
</script>