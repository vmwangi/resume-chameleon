<template>
  <div class="container mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <!-- Loading indicator -->
    <div v-if="pageLoading && !cameFromInternalNavigation" class="flex justify-center items-center h-64">
      <div class="animate-spin rounded-full h-12 w-12 border-t-2 border-b-2 border-emerald-500"></div>
      <p class="ml-3 text-lg text-gray-600">Loading dashboard...</p>
    </div>
    
    <div v-else class="max-w-5xl mx-auto">
      <!-- Header with Create New Resume button on the same line -->
      <div class="flex justify-between items-center mb-8">
        <div>
          <h1 class="text-3xl font-bold text-gray-900">Your Dashboard</h1>
          <p class="mt-1 text-gray-600">Manage your resumes and track your applications</p>
        </div>
        <router-link 
          to="/dashboard/resume/create" 
          class="inline-flex items-center px-3 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-emerald-600 hover:bg-emerald-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-emerald-500 transition-colors duration-200"
        >
          <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1.5" viewBox="0 0 20 20" fill="currentColor">
            <path fill-rule="evenodd" d="M10 3a1 1 0 011 1v5h5a1 1 0 110 2h-5v5a1 1 0 01-2 0v-5H4a1 1 0 010-2h5V4a1 1 0 011-1z" clip-rule="evenodd" />
          </svg>
          Create New Resume
        </router-link>
      </div>

      <!-- View Resumes Section -->
      <section class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
        <div class="px-6 py-5 border-b border-gray-200 bg-gray-50">
          <h2 class="text-xl font-semibold text-gray-900">Your Resumes</h2>
        </div>
        
        <div v-if="loading && !cameFromInternalNavigation" class="p-6 flex justify-center">
          <div class="animate-spin rounded-full h-10 w-10 border-t-2 border-b-2 border-emerald-500"></div>
        </div>
        
        <div v-else-if="resumes.length === 0" class="p-10 text-center">
          <div class="mx-auto h-24 w-24 text-gray-400">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
            </svg>
          </div>
          <h3 class="mt-2 text-lg font-medium text-gray-900">No resumes yet</h3>
          <p class="mt-1 text-gray-500">Get started by creating your first resume</p>
          <div class="mt-6">
            <router-link 
              to="/dashboard/resume/create" 
              class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-emerald-600 hover:bg-emerald-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-emerald-500 transition-colors duration-200"
            >
              Create Resume
            </router-link>
          </div>
        </div>
        
        <div v-else class="divide-y divide-gray-200">
          <div 
            v-for="resume in paginatedResumes" 
            :key="resume.id"
            class="p-6 hover:bg-gray-50 transition-colors duration-150"
          >
            <div class="flex items-center justify-between">
              <div>
                <router-link 
                  :to="'/dashboard/resume/' + resume.id"
                  class="text-lg font-medium text-emerald-600 hover:text-emerald-700"
                >
                  {{ resume.title }}
                </router-link>
                <p class="mt-1 text-sm text-gray-500">Last updated: {{ formatDate(resume.updated_at) }}</p>
              </div>
              <div class="flex space-x-3">
                <router-link 
                  :to="'/dashboard/resume/' + resume.id"
                  class="inline-flex items-center px-3 py-1.5 border border-gray-300 shadow-sm text-sm font-medium rounded text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-emerald-500 transition-colors duration-200"
                >
                  <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                  </svg>
                  View
                </router-link>
              </div>
            </div>
          </div>
          <!-- Pagination -->
          <div v-if="totalPages > 1" class="py-4 flex justify-center items-center space-x-2">
            <button 
              @click="currentPage = Math.max(1, currentPage - 1)"
              :disabled="currentPage === 1"
              class="px-3 py-1 text-gray-600 hover:text-emerald-600 disabled:text-gray-400"
            >
              &lt;
            </button>
            <button 
              v-for="page in totalPages" 
              :key="page"
              @click="currentPage = page"
              :class="[
                'px-3 py-1 rounded-full text-sm',
                currentPage === page 
                  ? 'bg-emerald-600 text-white' 
                  : 'text-gray-600 hover:text-emerald-600'
              ]"
            >
              {{ page }}
            </button>
            <button 
              @click="currentPage = Math.min(totalPages, currentPage + 1)"
              :disabled="currentPage === totalPages"
              class="px-3 py-1 text-gray-600 hover:text-emerald-600 disabled:text-gray-400"
            >
              &gt;
            </button>
          </div>
        </div>
      </section>
    </div>
  </div>
</template>

<script>
import axios from 'axios';
import { useRoute } from 'vue-router';

export default {
  name: 'Dashboard',
  data() {
    return { 
      resumes: [],
      loading: true,
      pageLoading: true,
      cameFromInternalNavigation: false,
      currentPage: 1,
      itemsPerPage: 10
    };
  },
  computed: {
    paginatedResumes() {
      const start = (this.currentPage - 1) * this.itemsPerPage;
      const end = start + this.itemsPerPage;
      return this.resumes.slice(start, end);
    },
    totalPages() {
      return Math.ceil(this.resumes.length / this.itemsPerPage);
    }
  },
  created() {
    const route = useRoute();
    const previousRoute = this.$router.options.history.state.back;
    
    if (previousRoute && 
        (previousRoute.includes('/dashboard') || 
         previousRoute.includes('/resumes') || 
         previousRoute.includes('/settings'))) {
      this.cameFromInternalNavigation = true;
    }
    
    this.fetchResumes();
  },
  activated() {
    if (this.$route.meta.requiresAuth) {
      this.cameFromInternalNavigation = true;
      this.fetchResumes();
    }
  },
  methods: {
    async fetchResumes() {
      this.loading = true;
      this.pageLoading = true;
      try {
        const res = await axios.get('/api/resumes', { 
          headers: { 
            Authorization: `Bearer ${localStorage.getItem('auth_token')}` 
          } 
        });
        this.resumes = res.data;
      } catch (error) {
        console.error('Error fetching resumes:', error);
      } finally {
        this.loading = false;
        this.pageLoading = false;
      }
    },
    formatDate(dateString) {
      const options = { year: 'numeric', month: 'long', day: 'numeric' };
      return new Date(dateString).toLocaleDateString(undefined, options);
    }
  }
};
</script>