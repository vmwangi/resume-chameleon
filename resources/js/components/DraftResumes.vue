<template>
  <div v-if="loading" class="mt-6 mb-8 flex justify-center">
    <div class="animate-spin rounded-full h-10 w-10 border-t-2 border-b-2 border-emerald-500"></div>
    <p class="ml-3 text-lg text-gray-600">Loading drafts...</p>
  </div>
  
  <div v-else-if="draftResumes.length > 0" class="mt-6 mb-8">
    <h2 class="text-xl font-semibold text-gray-900 mb-4">Resume Drafts</h2>
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
      <div class="px-6 py-5 border-b border-gray-200 bg-gray-50">
        <h3 class="text-lg font-medium text-gray-900">Continue where you left off</h3>
      </div>
      
      <div class="p-6">
        <div class="space-y-4">
          <div 
            v-for="resume in draftResumes" 
            :key="resume.id"
            class="border border-gray-200 rounded-md p-4 hover:border-emerald-500 hover:bg-emerald-50 transition-colors duration-200"
          >
            <div class="flex justify-between items-center">
              <div>
                <h4 class="text-lg font-medium text-gray-900">{{ resume.title || 'Untitled Resume' }}</h4>
                <p class="mt-1 text-sm text-gray-500">Last updated: {{ formatDate(resume.updated_at) }}</p>
                <p v-if="resume.creation_step" class="mt-1 text-sm text-emerald-600">
                  {{ getStepDescription(resume.creation_step) }}
                </p>
              </div>
              <div class="flex space-x-2">
                <button 
                  @click="continueDraft(resume)"
                  class="inline-flex items-center px-3 py-1.5 border border-transparent text-sm font-medium rounded-md text-white bg-emerald-600 hover:bg-emerald-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-emerald-500 transition-colors duration-200"
                >
                  Continue
                </button>
                <button 
                  @click="deleteDraft(resume.id)"
                  class="inline-flex items-center px-3 py-1.5 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 transition-colors duration-200"
                >
                  Delete
                </button>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import axios from 'axios';

export default {
  data() {
    return {
      draftResumes: [],
      loading: true
    };
  },
  mounted() {
    this.fetchDraftResumes();
  },
  methods: {
    async fetchDraftResumes() {
      this.loading = true;
      try {
        const response = await axios.get('/api/resumes', {
          params: { status: 'draft' },
          headers: { 'Authorization': `Bearer ${localStorage.getItem('auth_token')}` }
        });
        this.draftResumes = response.data;
      } catch (error) {
        console.error('Error fetching draft resumes:', error);
      } finally {
        this.loading = false;
      }
    },
    
    continueDraft(resume) {
      // Check if draft has creation_step and draft_data to determine the correct route
      if (!resume.creation_step || !resume.draft_data) {
        // If no creation step or draft data, go to the resume edit page
        this.$router.push(`/dashboard/resume/${resume.id}`);
        return;
      }
      
      // Handle different creation steps
      switch (resume.creation_step) {
        case 'method':
        case 'upload':
        case 'existing':
        case 'create':
          // For resume creation drafts, go to the resume creation page with draft_id
          this.$router.push({
            path: '/dashboard/resume/create',
            query: { draft_id: resume.id }
          });
          break;
          
        default:
          // For other drafts or if creation step is unknown, go to the resume edit page
          this.$router.push(`/dashboard/resume/${resume.id}`);
          break;
      }
    },
    
    async deleteDraft(id) {
      if (!confirm('Are you sure you want to delete this draft?')) return;
      
      try {
        await axios.delete(`/api/resumes/${id}`, {
          headers: { 'Authorization': `Bearer ${localStorage.getItem('auth_token')}` }
        });
        this.draftResumes = this.draftResumes.filter(resume => resume.id !== id);
      } catch (error) {
        console.error('Error deleting draft resume:', error);
        alert('Failed to delete draft. Please try again.');
      }
    },
    
    formatDate(dateString) {
      const options = { year: 'numeric', month: 'long', day: 'numeric', hour: '2-digit', minute: '2-digit' };
      return new Date(dateString).toLocaleDateString(undefined, options);
    },
    
    getStepDescription(step) {
      switch (step) {
        case 'method':
          return 'Choose creation method';
        case 'upload':
          return 'Upload resume';
        case 'create':
          return 'Create from scratch';
        case 'existing':
          return 'Create from existing resume';
        default:
          return 'In progress';
      }
    }
  }
};
</script>