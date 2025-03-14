<template>
  <div class="min-h-screen bg-gray-50 flex flex-col justify-center py-12 sm:px-6 lg:px-8">
    <div class="sm:mx-auto sm:w-full sm:max-w-md">
      <div class="text-center">
        <h2 class="text-center text-3xl font-extrabold text-gray-900">
          Welcome to Resume Chameleon
        </h2>
        <p class="mt-2 text-center text-sm text-gray-600">
          Sign in to create and manage your tailored resumes
        </p>
      </div>
    </div>

    <div class="mt-8 sm:mx-auto sm:w-full sm:max-w-md">
      <div class="bg-white py-8 px-4 shadow sm:rounded-lg sm:px-10">
        <div class="space-y-6">
          <button
            @click="signInWithGoogle"
            class="w-full flex justify-center items-center py-3 px-4 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-emerald-500 transition-colors duration-200"
          >
            <svg class="w-5 h-5 mr-2" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
              <path d="M12.24 10.285V14.4h6.806c-.275 1.765-2.056 5.174-6.806 5.174-4.095 0-7.439-3.389-7.439-7.574s3.345-7.574 7.439-7.574c2.33 0 3.891.989 4.785 1.849l3.254-3.138C18.189 1.186 15.479 0 12.24 0c-6.635 0-12 5.365-12 12s5.365 12 12 12c6.926 0 11.52-4.869 11.52-11.726 0-.788-.085-1.39-.189-1.989H12.24z" fill="currentColor"/>
            </svg>
            Continue with Google
          </button>
        </div>

        <div class="mt-6">
          <div class="relative">
            <div class="absolute inset-0 flex items-center">
              <div class="w-full border-t border-gray-300"></div>
            </div>
            <div class="relative flex justify-center text-sm">
              <span class="px-2 bg-white text-gray-500">
                Or
              </span>
            </div>
          </div>

          <div class="mt-6">
            <router-link 
              to="/pricing" 
              class="w-full flex justify-center py-2 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-emerald-600 hover:bg-emerald-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-emerald-500 transition-colors duration-200"
            >
              View Pricing Plans
            </router-link>
          </div>
        </div>

        <div class="mt-6">
          <p class="text-center text-xs text-gray-600">
            By signing in, you agree to our
            <router-link to="/terms" class="font-medium text-emerald-600 hover:text-emerald-500">
              Terms of Service
            </router-link>
            and
            <router-link to="/privacy" class="font-medium text-emerald-600 hover:text-emerald-500">
              Privacy Policy
            </router-link>.
          </p>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { onMounted, watch } from 'vue';
import { useRoute, useRouter } from 'vue-router';

const route = useRoute();
const router = useRouter();

// Check for token in URL (for OAuth callbacks)
onMounted(() => {
  const token = route.query.token;
  if (token) {
    localStorage.setItem('auth_token', token);
    // Trigger auth state update
    window.dispatchEvent(new CustomEvent('login'));
    router.push('/dashboard');
  }
});

// Also check for token in the authenticated route
watch(() => route.fullPath, () => {
  if (route.path === '/authenticated' && route.query.token) {
    const token = route.query.token;
    localStorage.setItem('auth_token', token);
    // Trigger auth state update
    window.dispatchEvent(new CustomEvent('login'));
    router.push('/dashboard');
  }
});

const signInWithGoogle = () => {
  window.location.href = '/login/google';
};
</script>