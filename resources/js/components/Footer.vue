<template>
  <footer class="border-t border-gray-100 w-full mt-auto" :class="compact ? 'bg-gray-100' : 'bg-white'">
    <!-- Compact footer for logged-in users -->
    <div v-if="compact" class="container mx-auto px-4 sm:px-6 lg:px-8 py-6">
      <div class="flex justify-center">
        <p class="text-sm text-gray-500 text-center">
          &copy; {{ new Date().getFullYear() }} Resume Chameleon. All rights reserved.
        </p>
      </div>
    </div>
    
    <!-- Full footer for non-authenticated users -->
    <div v-else class="container mx-auto px-4 sm:px-6 lg:px-8 py-8">
      <div class="grid grid-cols-1 md:grid-cols-4 gap-8">
        <!-- Logo and description -->
        <div class="col-span-1 md:col-span-2">
          <div class="flex items-center">
            <div class="w-8 h-8 mr-2">
              <svg viewBox="0 0 36 36" fill="none" xmlns="http://www.w3.org/2000/svg" class="w-full h-full">
                <path d="M18 36C27.9411 36 36 27.9411 36 18C36 8.05887 27.9411 0 18 0C8.05887 0 0 8.05887 0 18C0 27.9411 8.05887 36 18 36Z" fill="#10B981"/>
                <path d="M27 12C27 12 25.5 14.5 22.5 14.5C19.5 14.5 18 12 18 12C18 12 16.5 14.5 13.5 14.5C10.5 14.5 9 12 9 12V22C9 22 10.5 24.5 13.5 24.5C16.5 24.5 18 22 18 22C18 22 19.5 24.5 22.5 24.5C25.5 24.5 27 22 27 22V12Z" fill="white"/>
                <circle cx="13.5" cy="10.5" r="1.5" fill="white"/>
                <circle cx="22.5" cy="10.5" r="1.5" fill="white"/>
                <path d="M18 16C18 16 18 19 18 20C18 21 16 23 16 23" stroke="white" stroke-width="0.75"/>
              </svg>
            </div>
            <span class="text-xl font-bold text-emerald-600">Resume Chameleon</span>
          </div>
          <p class="mt-4 text-gray-600 max-w-md">
            Resume Chameleon helps you adapt your resume to match job descriptions, increasing your chances of landing interviews and securing your dream job.
          </p>
          <div class="mt-4 flex space-x-4">
            <a href="#" class="text-gray-400 hover:text-emerald-600 transition-colors duration-200">
              <span class="sr-only">Twitter</span>
              <svg class="h-6 w-6" fill="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                <path d="M8.29 20.251c7.547 0 11.675-6.253 11.675-11.675 0-.178 0-.355-.012-.53A8.348 8.348 0 0022 5.92a8.19 8.19 0 01-2.357.646 4.118 4.118 0 001.804-2.27 8.224 8.224 0 01-2.605.996 4.107 4.107 0 00-6.993 3.743 11.65 11.65 0 01-8.457-4.287 4.106 4.106 0 001.27 5.477A4.072 4.072 0 012.8 9.713v.052a4.105 4.105 0 003.292 4.022 4.095 4.095 0 01-1.853.07 4.108 4.108 0 003.834 2.85A8.233 8.233 0 012 18.407a11.616 11.616 0 006.29 1.84" />
              </svg>
            </a>
            <a href="#" class="text-gray-400 hover:text-emerald-600 transition-colors duration-200">
              <span class="sr-only">LinkedIn</span>
              <svg class="h-6 w-6" fill="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                <path d="M19 0h-14c-2.761 0-5 2.239-5 5v14c0 2.761 2.239 5 5 5h14c2.762 0 5-2.239 5-5v-14c0-2.761-2.238-5-5-5zm-11 19h-3v-11h3v11zm-1.5-12.268c-.966 0-1.75-.79-1.75-1.764s.784-1.764 1.75-1.764 1.75.79 1.75 1.764-.783 1.764-1.75 1.764zm13.5 12.268h-3v-5.604c0-3.368-4-3.113-4 0v5.604h-3v-11h3v1.765c1.396-2.586 7-2.777 7 2.476v6.759z" />
              </svg>
            </a>
          </div>
        </div>
        
        <!-- Links -->
        <div>
          <h3 class="text-sm font-semibold text-gray-900 tracking-wider uppercase">Product</h3>
          <ul class="mt-4 space-y-3">
            <li>
              <router-link to="/" class="text-base text-gray-600 hover:text-emerald-600 transition-colors duration-200">
                Home
              </router-link>
            </li>
            <li>
              <router-link to="/pricing" class="text-base text-gray-600 hover:text-emerald-600 transition-colors duration-200">
                Pricing
              </router-link>
            </li>
            <li>
              <router-link to="/login" class="text-base text-gray-600 hover:text-emerald-600 transition-colors duration-200">
                Sign In
              </router-link>
            </li>
          </ul>
        </div>
        
        <!-- Support -->
        <div>
          <h3 class="text-sm font-semibold text-gray-900 tracking-wider uppercase">Support</h3>
          <ul class="mt-4 space-y-3">
            <li>
              <router-link to="/terms" class="text-base text-gray-600 hover:text-emerald-600 transition-colors duration-200">
                Terms of Service
              </router-link>
            </li>
            <li>
              <router-link to="/privacy" class="text-base text-gray-600 hover:text-emerald-600 transition-colors duration-200">
                Privacy Policy
              </router-link>
            </li>
            <li>
              <router-link to="/contact" class="text-base text-gray-600 hover:text-emerald-600 transition-colors duration-200">
                Contact Us
              </router-link>
            </li>
          </ul>
        </div>
      </div>
      
      <div class="mt-8 pt-8 border-t border-gray-100">
        <p class="text-sm text-gray-500 text-center">
          &copy; {{ new Date().getFullYear() }} Resume Chameleon. All rights reserved.
        </p>
      </div>
    </div>
  </footer>
</template>

<script>
export default {
  props: {
    compact: {
      type: Boolean,
      default: false
    }
  }
}
</script>

<style scoped>
footer {
  transition: all 0.3s ease;
  flex-shrink: 0;
}
</style>