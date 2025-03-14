<template>
  <div class="bg-gradient-to-b from-white to-gray-50 min-h-screen">
    <!-- Hero Section with decorative elements -->
    <section class="relative pt-28 pb-16 overflow-hidden">
      <div class="absolute inset-0 pointer-events-none">
        <div class="absolute -top-24 -right-24 w-96 h-96 bg-emerald-50 rounded-full opacity-70 blur-3xl"></div>
        <div class="absolute -bottom-24 -left-24 w-96 h-96 bg-emerald-50 rounded-full opacity-70 blur-3xl"></div>
      </div>
      
      <div class="container mx-auto px-4 sm:px-6 lg:px-8 relative">
        <div class="max-w-3xl mx-auto text-center">
          <h1 class="text-4xl font-bold text-gray-900 sm:text-5xl bg-clip-text text-transparent bg-gradient-to-r from-emerald-600 to-emerald-500">
            Contact Us
          </h1>
          <p class="mt-4 text-xl text-gray-600">
            Have questions or feedback? We'd love to hear from you.
          </p>
        </div>
      </div>
    </section>

    <!-- Contact Form Section -->
    <section class="py-16">
      <div class="container mx-auto px-4 sm:px-6 lg:px-8">
        <div class="max-w-3xl mx-auto">
  <div class="bg-white rounded-2xl shadow-lg overflow-hidden transform transition-all duration-300 hover:shadow-xl">
    <div class="p-6 sm:p-10">
      <h2 class="text-2xl font-semibold text-gray-900 mb-6">Send us a message</h2>
      
      <form @submit.prevent="submitForm" class="space-y-6">
        <div class="relative">
          <label for="name" class="block text-sm font-medium text-gray-700 mb-1">
            Name
          </label>
          <input 
            id="name" 
            v-model="form.name" 
            type="text" 
            required 
            class="appearance-none block w-full px-4 py-3 border border-gray-300 rounded-lg shadow-sm placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 transition-colors duration-200"
            placeholder="Your name"
          />
        </div>

        <div class="relative">
          <label for="email" class="block text-sm font-medium text-gray-700 mb-1">
            Email
          </label>
          <input 
            id="email" 
            v-model="form.email" 
            type="email" 
            required 
            class="appearance-none block w-full px-4 py-3 border border-gray-300 rounded-lg shadow-sm placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 transition-colors duration-200"
            placeholder="your.email@example.com"
          />
        </div>

        <div class="relative">
          <label for="message" class="block text-sm font-medium text-gray-700 mb-1">
            Message
          </label>
          <textarea 
            id="message" 
            v-model="form.message" 
            rows="5" 
            required 
            class="appearance-none block w-full px-4 py-3 border border-gray-300 rounded-lg shadow-sm placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 transition-colors duration-200"
            placeholder="How can we help you?"
          ></textarea>
        </div>

        <div>
          <button 
            type="submit" 
            class="w-full flex justify-center items-center py-3 px-6 border border-transparent rounded-lg shadow-sm text-base font-medium text-white bg-gradient-to-r from-emerald-600 to-emerald-500 hover:from-emerald-700 hover:to-emerald-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-emerald-500 transition-all duration-200 transform hover:-translate-y-0.5"
            :disabled="loading"
          >
            <svg v-if="loading" class="animate-spin -ml-1 mr-3 h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
              <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
              <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
            <span v-if="loading">Sending...</span>
            <span v-else>Send Message</span>
          </button>
        </div>
      </form>

      <!-- Success Message -->
      <div v-if="successMessage" class="mt-6 p-4 bg-green-50 rounded-lg border border-green-100 animate-fadeIn">
        <div class="flex">
          <div class="flex-shrink-0">
            <svg class="h-5 w-5 text-green-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
              <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
            </svg>
          </div>
          <div class="ml-3">
            <p class="text-sm font-medium text-green-800">
              {{ successMessage }}
            </p>
          </div>
        </div>
      </div>

      <!-- Error Message -->
      <div v-if="errorMessage" class="mt-6 p-4 bg-red-50 rounded-lg border border-red-100 animate-fadeIn">
        <div class="flex">
          <div class="flex-shrink-0">
            <svg class="h-5 w-5 text-red-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
              <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
            </svg>
          </div>
          <div class="ml-3">
            <p class="text-sm font-medium text-red-800">
              {{ errorMessage }}
            </p>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- Response Time -->
  <div class="mt-8 bg-white rounded-2xl shadow-lg overflow-hidden transform transition-all duration-300 hover:shadow-xl">
    <div class="p-6">
      <div class="flex items-center">
        <div class="flex-shrink-0 bg-emerald-100 p-3 rounded-full">
          <svg class="h-6 w-6 text-emerald-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
          </svg>
        </div>
        <div class="ml-4">
          <h3 class="text-lg font-medium text-gray-900">Response Time</h3>
          <p class="mt-1 text-gray-600">
            We typically respond within 24-48 hours during business days.
          </p>
        </div>
      </div>
    </div>
  </div>

  <!-- FAQ Teaser -->
  <div class="mt-8 bg-white rounded-2xl shadow-lg p-8 text-center overflow-hidden transform transition-all duration-300 hover:shadow-xl">
    <h3 class="text-xl font-semibold text-gray-900">Have more questions?</h3>
    <p class="mt-2 text-gray-600">Check out our frequently asked questions or reach out to our team.</p>
    <div class="mt-6">
      <router-link 
        to="/pricing#faq" 
        class="inline-flex items-center px-4 py-2 border border-transparent text-base font-medium rounded-md text-emerald-700 bg-emerald-100 hover:bg-emerald-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-emerald-500 transition-colors duration-200"
      >
        View FAQs
        <svg class="ml-2 -mr-1 h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
        </svg>
      </router-link>
    </div>
  </div>
</div>
      </div>
    </section>
  </div>
</template>

<script>
import axios from 'axios';

export default {
  data() {
    return {
      form: {
        name: '',
        email: '',
        message: ''
      },
      loading: false,
      successMessage: '',
      errorMessage: ''
    };
  },
  methods: {
    async submitForm() {
      this.loading = true;
      this.successMessage = '';
      this.errorMessage = '';
      
      try {
        const response = await axios.post('/contact', this.form);
        this.successMessage = response.data.message;
        // Reset form
        this.form = {
          name: '',
          email: '',
          message: ''
        };
      } catch (error) {
        console.error('Error submitting form:', error);
        this.errorMessage = error.response?.data?.error || 'An error occurred. Please try again later.';
      } finally {
        this.loading = false;
      }
    }
  }
};
</script>

<style scoped>
@keyframes fadeIn {
  from { opacity: 0; transform: translateY(10px); }
  to { opacity: 1; transform: translateY(0); }
}

.animate-fadeIn {
  animation: fadeIn 0.3s ease-out forwards;
}
</style>