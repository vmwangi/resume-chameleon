<template>
  <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
    <div class="px-6 py-5 border-b border-gray-200 bg-gray-50 flex justify-between items-center">
      <h2 class="text-xl font-semibold text-gray-900">Upload Your Resume</h2>
    </div>
    
    <div class="p-6 space-y-6">
      <div v-if="!uploadedResumeData">
        <div class="max-w-lg mx-auto">
          <label class="block text-sm font-medium text-gray-700">Resume File (PDF only)</label>
          <div 
            class="mt-1 flex justify-center px-6 pt-5 pb-6 border-2 border-gray-300 border-dashed rounded-md"
            :class="{ 'border-emerald-500': isDragging, 'opacity-50 cursor-not-allowed': uploading }"
            :title="uploading ? 'Please wait until the current upload is complete' : ''"
            @dragover.prevent="!uploading && $emit('update:isDragging', true)"
            @dragleave.prevent="!uploading && $emit('update:isDragging', false)"
            @drop.prevent="!uploading && $emit('handle-file-drop', $event)"
          >
            <div class="space-y-1 text-center">
              <svg class="mx-auto h-12 w-12 text-gray-400" stroke="currentColor" fill="none" viewBox="0 0 48 48" aria-hidden="true">
                <path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
              </svg>
              <div class="flex text-sm text-gray-600">
                <label 
                  for="file-upload" 
                  class="relative cursor-pointer bg-white rounded-md font-medium text-emerald-600 hover:text-emerald-500 focus-within:outline-none focus-within:ring-2 focus-within:ring-offset-2 focus-within:ring-emerald-500" 
                  :class="{ 'opacity-50 cursor-not-allowed': uploading }"
                >
                  <span>Upload a file</span>
                  <input 
                    id="file-upload" 
                    name="resume_file" 
                    type="file" 
                    class="sr-only" 
                    accept=".pdf" 
                    @change="$emit('handle-file-change', $event)" 
                    :disabled="uploading"
                  >
                </label>
                <p class="pl-1">or drag and drop</p>
              </div>
              <p class="text-xs text-gray-500">PDF up to 10MB</p>
            </div>
          </div>

          <div v-if="formErrors.resume_file" class="mt-2 text-red-600 text-sm">
            <ul>
              <li v-for="error in formErrors.resume_file" :key="error">{{ error }}</li>
            </ul>
          </div>
          
          <div v-if="selectedFile" class="mt-4">
            <div class="flex items-center justify-between bg-gray-50 p-3 rounded-md border border-gray-200">
              <div class="flex items-center">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-400 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z" />
                </svg>
                <span class="text-sm text-gray-700 truncate">{{ selectedFile.name }}</span>
              </div>
              <button 
                @click="$emit('update:selectedFile', null)"
                class="text-gray-400 hover:text-gray-500"
                :class="{ 'opacity-50 cursor-not-allowed': uploading }"
                :disabled="uploading"
                :title="uploading ? 'Please wait until the upload is complete' : ''"
              >
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
              </button>
            </div>
          </div>
          
          <div class="mt-6 flex justify-end">
            <button 
              @click="$emit('upload-resume')"
              class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white transition-colors duration-200"
              :class="[
                !selectedFile || uploading 
                  ? 'bg-gray-400 cursor-not-allowed' 
                  : 'bg-emerald-600 hover:bg-emerald-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-emerald-500'
              ]"
              :disabled="!selectedFile || uploading"
              :title="!selectedFile ? 'Please select a file first' : (uploading ? 'Upload in progress' : '')"
            >
              <svg 
                v-if="uploading" 
                class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" 
                xmlns="http://www.w3.org/2000/svg" 
                fill="none" 
                viewBox="0 0 24 24"
              >
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
              </svg>
              {{ uploading ? 'Processing...' : 'Upload & Process' }}
            </button>
          </div>
        </div>
      </div>
      
      <div v-else>
        <div class="bg-green-50 border-l-4 border-green-400 p-4 mb-6">
          <div class="flex">
            <div class="flex-shrink-0">
              <svg class="h-5 w-5 text-green-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
              </svg>
            </div>
            <div class="ml-3">
              <p class="text-sm text-green-700">
                Resume successfully processed! Please add a job description to tailor your resume.
              </p>
              <p class="mt-1 text-sm text-green-700">
                We'll use the details from your uploaded resume along with the job description to create a tailored version.
              </p>
            </div>
          </div>
        </div>
        
        <div>
          <label for="resume-title" class="block text-sm font-medium text-gray-700">Resume Title</label>
          <input 
            id="resume-title"
            type="text" 
            v-model="form.title" 
            class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-emerald-500 focus:border-emerald-500 sm:text-sm"
            placeholder="e.g., Software Developer Resume - Google"
            required 
            :disabled="tailoring" />
          <div v-if="formErrors.title" class="mt-1 text-sm text-red-600">{{ formErrors.title }}</div>
        </div>
        
        <div class="mt-4">
          <label for="job-description" class="block text-sm font-medium text-gray-700">Job Description</label>
          <textarea 
            id="job-description"
            v-model="form.job_description" 
            rows="8" 
            class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-emerald-500 focus:border-emerald-500 sm:text-sm"
            placeholder="Paste the full job description here to tailor your resume"
            required 
            :disabled="tailoring"
          ></textarea>
          <div v-if="formErrors.job_description" class="mt-1 text-sm text-red-600">{{ formErrors.job_description }}</div>
          <p class="mt-1 text-xs text-gray-500">This helps us tailor your resume to match the job requirements</p>
        </div>
        
        <div class="mt-6 flex justify-end">
          <button 
            @click="$emit('create-resume')"
            class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white transition-colors duration-200"
            :class="[
              !form.title || !form.job_description || tailoring 
                ? 'bg-gray-400 cursor-not-allowed' 
                : 'bg-emerald-600 hover:bg-emerald-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-emerald-500'
            ]"
            :disabled="!form.title || !form.job_description || tailoring"
            :title="!form.title ? 'Please enter a resume title' : (!form.job_description ? 'Please enter a job description' : (tailoring ? 'Tailoring in progress' : ''))"
          >
            <svg 
              v-if="tailoring" 
              class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" 
              xmlns="http://www.w3.org/2000/svg" 
              fill="none" 
              viewBox="0 0 24 24"
            >
              <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
              <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
            {{ tailoring ? 'Tailoring Resume...' : 'Create Tailored Resume' }}
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  name: 'ResumeUpload',
  props: {
    selectedFile: File,
    isDragging: Boolean,
    uploading: Boolean,
    uploadedResumeData: Object,
    form: Object,
    formErrors: Object,
    tailoring: Boolean
  }
};
</script>