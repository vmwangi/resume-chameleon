<template>
  <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
    <div class="px-6 py-5 border-b border-gray-200 bg-gray-50 flex justify-between items-center">
      <h2 class="text-xl font-semibold text-gray-900">Select Existing Resume</h2>
      <button 
        @click="$emit('update:creationStep', 'method')"
        class="inline-flex items-center px-3 py-1.5 border border-gray-300 shadow-sm text-sm font-medium rounded text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-emerald-500 transition-colors duration-200"
      >
        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
        </svg>
        Back
      </button>
    </div>
    
    <div class="p-6 space-y-6">
      <div v-if="loadingResumes" class="flex justify-center py-6">
        <div class="animate-spin rounded-full h-10 w-10 border-t-2 border-b-2 border-emerald-500"></div>
      </div>
      
      <div v-else-if="resumes.length === 0" class="text-center py-6">
        <div class="mx-auto h-16 w-16 text-gray-400">
          <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
          </svg>
        </div>
        <h3 class="mt-2 text-lg font-medium text-gray-900">No resumes found</h3>
        <p class="mt-1 text-gray-500">You need to create a resume first</p>
        <div class="mt-4">
          <button 
            @click="$emit('select-method', 'create')"
            class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-emerald-600 hover:bg-emerald-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-emerald-500 transition-colors duration-200"
          >
            Create New Resume
          </button>
        </div>
      </div>
      
      <div v-else>
        <div v-if="!selectedResumeId">
          <div class="space-y-4">
            <div 
              v-for="resume in resumes" 
              :key="resume.id"
              class="border border-gray-200 rounded-md p-4 hover:border-emerald-500 hover:bg-emerald-50 cursor-pointer transition-colors duration-200"
              :class="{ 'border-emerald-500 bg-emerald-50': selectedResumeId === resume.id }"
              @click="$emit('update:selectedResumeId', resume.id)"
            >
              <h3 class="text-lg font-medium text-gray-900">{{ resume.title }}</h3>
              <p class="mt-1 text-sm text-gray-500">Last updated: {{ formatDate(resume.updated_at) }}</p>
            </div>
          </div>
          
          <div class="mt-6 flex justify-end">
            <button 
              @click="$emit('proceed-with-resume')"
              class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white transition-colors duration-200"
              :class="[
                !selectedResumeId 
                  ? 'bg-gray-400 cursor-not-allowed' 
                  : 'bg-emerald-600 hover:bg-emerald-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-emerald-500'
              ]"
              :disabled="!selectedResumeId"
              :title="!selectedResumeId ? 'Please select a resume first' : ''"
            >
              Continue
            </button>
          </div>
        </div>
        
        <div v-else>
          <div>
            <label for="new-resume-title" class="block text-sm font-medium text-gray-700">New Resume Title</label>
            <input 
              id="new-resume-title"
              type="text" 
              v-model="form.title" 
              class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-emerald-500 focus:border-emerald-500 sm:text-sm"
              placeholder="e.g., Software Developer Resume - Google"
              required 
              :disabled="creating"
            />
          </div>
          
          <div class="mt-4">
            <label for="job-description-existing" class="block text-sm font-medium text-gray-700">Job Description</label>
            <textarea 
              id="job-description-existing"
              v-model="form.job_description" 
              rows="8" 
              class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-emerald-500 focus:border-emerald-500 sm:text-sm"
              placeholder="Paste the full job description here to tailor your resume"
              required 
              :disabled="creating"
            ></textarea>
            <p class="mt-1 text-xs text-gray-500">This helps us tailor your resume to match the job requirements</p>
            <p class="mt-1 text-xs text-gray-600">We'll update your existing resume with the job description to tailor a suitable resume.</p>
          </div>
          
          <div class="mt-6 flex justify-end">
            <button 
              @click="$emit('update:selectedResumeId', null)"
              class="inline-flex items-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-emerald-500 transition-colors duration-200"
              :class="{ 'opacity-50 cursor-not-allowed': creating }"
              :disabled="creating"
              :title="creating ? 'Please wait until the creation process is complete' : ''"
            >
              Back
            </button>
            <button 
              @click="$emit('create-from-existing')"
              class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white transition-colors duration-200 ml-3"
              :class="[
                !form.title || !form.job_description || creating 
                  ? 'bg-gray-400 cursor-not-allowed' 
                  : 'bg-emerald-600 hover:bg-emerald-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-emerald-500'
              ]"
              :disabled="!form.title || !form.job_description || creating"
              :title="!form.title ? 'Please enter a resume title' : (!form.job_description ? 'Please enter a job description' : (creating ? 'Creation in progress' : ''))"
            >
              <svg v-if="creating" class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
              </svg>
              {{ creating ? 'Creating...' : 'Update & Tailor Resume' }}
            </button>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  name: 'ExistingResume',
  props: {
    resumes: Array,
    loadingResumes: Boolean,
    selectedResumeId: [String, Number],
    form: Object,
    creating: Boolean
  },
  methods: {
    formatDate(dateString) {
      const options = { year: 'numeric', month: 'long', day: 'numeric' };
      return new Date(dateString).toLocaleDateString(undefined, options);
    }
  }
};
</script>