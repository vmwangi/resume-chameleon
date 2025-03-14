<template>
  <div class="container mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <div class="max-w-5xl mx-auto">
      <header class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900">My Resumes</h1>
        <p class="mt-2 text-gray-600">Manage and organize all your tailored resumes</p>
      </header>
      
      <div class="mb-8 flex flex-col sm:flex-row sm:justify-between sm:items-center gap-4">
        <router-link 
          to="/dashboard/resume/create" 
          class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-emerald-600 hover:bg-emerald-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-emerald-500 transition-colors duration-200"
        >
          <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" viewBox="0 0 20 20" fill="currentColor">
            <path fill-rule="evenodd" d="M10 3a1 1 0 011 1v5h5a1 1 0 110 2h-5v5a1 1 0 11-2 0v-5H4a1 1 0 110-2h5V4a1 1 0 011-1z" clip-rule="evenodd" />
          </svg>
          Create New Resume
        </router-link>
        
        <div class="relative w-full sm:w-auto sm:min-w-[250px]">
          <input 
            type="text" 
            v-model="searchQuery" 
            placeholder="Search resumes..." 
            class="block w-full pl-10 pr-3 py-2 border border-gray-300 rounded-md leading-5 bg-white placeholder-gray-500 focus:outline-none focus:placeholder-gray-400 focus:ring-1 focus:ring-emerald-500 focus:border-emerald-500 sm:text-sm"
          />
          <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
            <svg class="h-5 w-5 text-gray-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
              <path fill-rule="evenodd" d="M8 4a4 4 0 100 8 4 4 0 000-8zM2 8a6 6 0 1110.89 3.476l4.817 4.817a1 1 0 01-1.414 1.414l-4.816-4.816A6 6 0 012 8z" clip-rule="evenodd" />
            </svg>
          </div>
        </div>
      </div>

      <div v-if="loading && !cameFromInternalNavigation" class="flex justify-center py-12">
        <div class="animate-spin rounded-full h-12 w-12 border-t-2 border-b-2 border-emerald-500"></div>
      </div>
      
      <div v-else-if="filteredResumes.length === 0" class="bg-white rounded-lg shadow-sm border border-gray-200 p-10 text-center">
        <div class="mx-auto h-24 w-24 text-gray-400">
          <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
          </svg>
        </div>
        <h3 class="mt-2 text-lg font-medium text-gray-900">
          {{ searchQuery ? 'No matching resumes found' : 'No resumes yet' }}
        </h3>
        <p class="mt-1 text-gray-500">
          {{ searchQuery ? 'Try a different search term' : 'Get started by creating your first resume' }}
        </p>
        <div class="mt-6" v-if="!searchQuery">
          <router-link 
            to="/dashboard/resume/create" 
            class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-emerald-600 hover:bg-emerald-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-emerald-500 transition-colors duration-200"
          >
            Create Resume
          </router-link>
        </div>
      </div>
      
      <div v-else class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
        <ul class="divide-y divide-gray-200">
          <li 
            v-for="resume in paginatedResumes" 
            :key="resume.id"
            class="p-4 sm:px-6 hover:bg-gray-50 transition-colors duration-150"
          >
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
              <div class="flex-1 min-w-0">
                <router-link 
                  :to="'/dashboard/resume/' + resume.id"
                  class="text-lg font-medium text-emerald-600 hover:text-emerald-700 truncate"
                >
                  {{ resume.title }}
                </router-link>
                <p class="mt-1 text-sm text-gray-500">
                  Last updated: {{ formatDate(resume.updated_at) }}
                </p>
              </div>
              <div class="flex flex-wrap gap-2">
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
                <button 
                  @click="confirmDelete(resume.id)"
                  class="inline-flex items-center px-3 py-1.5 border border-gray-300 shadow-sm text-sm font-medium rounded text-red-700 bg-white hover:bg-red-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 transition-colors duration-200"
                >
                  <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                  </svg>
                  Delete
                </button>
              </div>
            </div>
          </li>
        </ul>
        <!-- Pagination -->
        <div v-if="totalPages > 1" class="py-4 flex justify-center items-center space-x-2">
          <button 
            @click="currentPage = Math.max(1, currentPage - 1)"
            :disabled="currentPage === 1"
            class="px-3 py-1 text-gray-600 hover:text-emerald-600 disabled:text-gray-400"
          >
            <
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
            >
          </button>
        </div>
      </div>
    </div>

    <!-- Success Notification -->
    <div 
      v-if="showSuccess" 
      class="fixed bottom-0 inset-x-0 pb-2 sm:pb-5"
    >
      <div class="max-w-md mx-auto px-2 sm:px-4">
        <div class="p-2 rounded-lg bg-emerald-600 shadow-lg sm:p-3">
          <div class="flex items-center justify-between flex-wrap">
            <div class="w-0 flex-1 flex items-center">
              <span class="flex p-2 rounded-lg bg-emerald-800">
                <svg class="h-6 w-6 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                </svg>
              </span>
              <p class="ml-3 font-medium text-white truncate">
                <span>Resume deleted successfully!</span>
              </p>
            </div>
            <div class="order-2 flex-shrink-0 sm:order-3 sm:ml-2">
              <button 
                type="button" 
                @click="showSuccess = false"
                class="-mr-1 flex p-2 rounded-md hover:bg-emerald-500 focus:outline-none focus:ring-2 focus:ring-white"
              >
                <span class="sr-only">Dismiss</span>
                <svg class="h-6 w-6 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
              </button>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import axios from 'axios';
import { useRoute } from 'vue-router';
import { inject } from 'vue';

export default {
  name: 'Resumes',
  setup() {
    const showAlert = inject('showAlert');
    return { showAlert };
  },
  data() {
    return { 
      resumes: [],
      loading: true,
      searchQuery: '',
      cameFromInternalNavigation: false,
      currentPage: 1,
      itemsPerPage: 10,
      showSuccess: false
    };
  },
  computed: {
    filteredResumes() {
      if (!this.searchQuery) {
        return this.sortedResumes;
      }
      const query = this.searchQuery.toLowerCase();
      return this.sortedResumes.filter(resume => 
        resume.title.toLowerCase().includes(query)
      );
    },
    sortedResumes() {
      return [...this.resumes].sort((a, b) => 
        new Date(b.updated_at) - new Date(a.updated_at)
      );
    },
    paginatedResumes() {
      const start = (this.currentPage - 1) * this.itemsPerPage;
      const end = start + this.itemsPerPage;
      return this.filteredResumes.slice(start, end);
    },
    totalPages() {
      return Math.ceil(this.filteredResumes.length / this.itemsPerPage);
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
      try {
        const res = await axios.get('/api/resumes', { 
          headers: { 
            Authorization: `Bearer ${localStorage.getItem('auth_token')}` 
          } 
        });
        this.resumes = res.data;
      } catch (error) {
        console.error('Error fetching resumes:', error);
        this.showAlert({
          type: 'error',
          title: 'Fetch Error',
          message: 'Failed to fetch your resumes. Please try again.',
          autoDismiss: true
        });
      } finally {
        this.loading = false;
      }
    },
    formatDate(dateString) {
      const options = { year: 'numeric', month: 'long', day: 'numeric' };
      return new Date(dateString).toLocaleDateString(undefined, options);
    },
    confirmDelete(id) {
      const resume = this.resumes.find(r => r.id === id);
      this.showAlert({
        type: 'warning',
        title: 'Confirm Deletion',
        message: `Are you sure you want to delete "${resume.title}"? This action cannot be undone.`,
        isConfirmation: true,
        onConfirm: () => this.deleteResume(id)
      });
    },
    async deleteResume(id) {
      try {
        await axios.delete(`/api/resumes/${id}`, {
          headers: { Authorization: `Bearer ${localStorage.getItem('auth_token')}` }
        });
        this.resumes = this.resumes.filter(r => r.id !== id);
        this.showSuccess = true;
        setTimeout(() => {
          this.showSuccess = false;
        }, 5000);
      } catch (error) {
        console.error('Error deleting resume:', error);
        this.showAlert({
          type: 'error',
          title: 'Deletion Failed',
          message: 'There was an error deleting the resume. Please try again.',
          autoDismiss: true
        });
      }
    }
  }
};
</script>