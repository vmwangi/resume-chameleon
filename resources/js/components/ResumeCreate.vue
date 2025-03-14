<template>
  <div class="container mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <div class="max-w-4xl mx-auto">
      <header class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900">Create New Resume</h1>
        <p class="mt-2 text-gray-600">Build your professional resume step by step</p>
      </header>
      
      <!-- Creation Method Selection -->
      <div v-if="creationStep === 'method'" class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
        <div class="px-6 py-5 border-b border-gray-200 bg-gray-50">
          <h2 class="text-xl font-semibold text-gray-900">Choose a Method</h2>
        </div>
        
        <div class="p-6 space-y-6">
          <div class="grid grid-cols-1 gap-6 sm:grid-cols-3">
            <!-- Upload Resume Option -->
            <div 
              @click="selectMethod('upload')"
              class="relative rounded-lg border border-gray-300 bg-white px-6 py-5 shadow-sm flex flex-col items-center space-y-3 hover:border-emerald-500 hover:ring-1 hover:ring-emerald-500 focus-within:ring-2 focus-within:ring-offset-2 focus-within:ring-emerald-500 cursor-pointer transition-all duration-200"
            >
              <div class="flex-shrink-0 h-14 w-14 rounded-full bg-emerald-100 flex items-center justify-center">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-emerald-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12" />
                </svg>
              </div>
              <div class="text-center">
                <h3 class="text-lg font-medium text-gray-900">Upload Resume</h3>
                <p class="mt-1 text-sm text-gray-500">Upload your existing resume in PDF format</p>
              </div>
            </div>
            
            <!-- Create From Scratch Option -->
            <div 
              @click="selectMethod('create')"
              class="relative rounded-lg border border-gray-300 bg-white px-6 py-5 shadow-sm flex flex-col items-center space-y-3 hover:border-emerald-500 hover:ring-1 hover:ring-emerald-500 focus-within:ring-2 focus-within:ring-offset-2 focus-within:ring-emerald-500 cursor-pointer transition-all duration-200"
            >
              <div class="flex-shrink-0 h-14 w-14 rounded-full bg-emerald-100 flex items-center justify-center">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-emerald-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                </svg>
              </div>
              <div class="text-center">
                <h3 class="text-lg font-medium text-gray-900">Create From Scratch</h3>
                <p class="mt-1 text-sm text-gray-500">Build a new resume step by step</p>
              </div>
            </div>
            
            <!-- Use Existing Resume Option -->
            <div 
              @click="selectMethod('existing')"
              class="relative rounded-lg border border-gray-300 bg-white px-6 py-5 shadow-sm flex flex-col items-center space-y-3 hover:border-emerald-500 hover:ring-1 hover:ring-emerald-500 focus-within:ring-2 focus-within:ring-offset-2 focus-within:ring-emerald-500 cursor-pointer transition-all duration-200"
            >
              <div class="flex-shrink-0 h-14 w-14 rounded-full bg-emerald-100 flex items-center justify-center">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-emerald-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7v8a2 2 0 002 2h6M8 7V5a2 2 0 012-2h4.586a1 1 0 01.707.293l4.414 4.414a1 1 0 01.293.707V15a2 2 0 01-2 2h-2M8 7H6a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2v-2" />
                </svg>
              </div>
              <div class="text-center">
                <h3 class="text-lg font-medium text-gray-900">Use Existing Resume</h3>
                <p class="mt-1 text-sm text-gray-500">Select from your saved resumes</p>
              </div>
            </div>
          </div>
        </div>
      </div>
      
      <!-- Upload Resume Section -->
      <div v-if="creationStep === 'upload'" class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
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
                @dragover.prevent="!uploading && (isDragging = true)"
                @dragleave.prevent="!uploading && (isDragging = false)"
                @drop.prevent="!uploading && handleFileDrop"
                v-tooltip="uploading ? 'Please wait until the current upload is complete' : ''"
              >
                <div class="space-y-1 text-center">
                  <svg class="mx-auto h-12 w-12 text-gray-400" stroke="currentColor" fill="none" viewBox="0 0 48 48" aria-hidden="true"> 
                    <path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                  </svg>
                  <div class="flex text-sm text-gray-600">
                    <label for="file-upload" class="relative cursor-pointer bg-white rounded-md font-medium text-emerald-600 hover:text-emerald-500 focus-within:outline-none focus-within:ring-2 focus-within:ring-offset-2 focus-within:ring-emerald-500" :class="{ 'opacity-50 cursor-not-allowed': uploading }">
                      <span>Upload a file</span>
                      <input id="file-upload" name="resume_file" type="file" class="sr-only" accept=".pdf" @change="handleFileChange" :disabled="uploading">
                    </label>
                    <p class="pl-1">or drag and drop</p>
                  </div>
                  <p class="text-xs text-gray-500">PDF up to 10MB</p>
                </div>
              </div>

              <!-- Error Display -->
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
                    @click="selectedFile = null"
                    class="text-gray-400 hover:text-gray-500"
                    :class="{ 'opacity-50 cursor-not-allowed': uploading }"
                    :disabled="uploading"
                    v-tooltip="uploading ? 'Please wait until the upload is complete' : ''"
                  >
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                  </button>
                </div>
              </div>
              
              <div class="mt-6 flex justify-end">
                <button 
                  @click="uploadResume"
                  class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white transition-colors duration-200"
                  :class="[
                    !selectedFile || uploading 
                      ? 'bg-gray-400 cursor-not-allowed' 
                      : 'bg-emerald-600 hover:bg-emerald-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-emerald-500'
                  ]"
                  :disabled="!selectedFile || uploading"
                  v-tooltip="!selectedFile ? 'Please select a file first' : (uploading ? 'Upload in progress' : '')">
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
                :disabled="tailoring"
              >
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
                @click="createResumeFromUpload"
                class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white transition-colors duration-200"
                :class="[
                  !form.title || !form.job_description || tailoring 
                    ? 'bg-gray-400 cursor-not-allowed' 
                    : 'bg-emerald-600 hover:bg-emerald-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-emerald-500'
                ]"
                :disabled="!form.title || !form.job_description || tailoring"
                v-tooltip="!form.title ? 'Please enter a resume title' : (!form.job_description ? 'Please enter a job description' : (tailoring ? 'Tailoring in progress' : ''))"
              >
                <svg v-if="tailoring" class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                  <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                  <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
                {{ tailoring ? 'Tailoring Resume...' : 'Create Tailored Resume' }}
              </button>
            </div>
          </div>
        </div>
      </div>
      
      <!-- Select Existing Resume Section -->
      <div v-if="creationStep === 'existing'" class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
        <div class="px-6 py-5 border-b border-gray-200 bg-gray-50 flex justify-between items-center">
          <h2 class="text-xl font-semibold text-gray-900">Select Existing Resume</h2>
          <button 
            @click="creationStep = 'method';"
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
                @click="selectMethod('create')"
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
                  @click="selectedResumeId = resume.id;"
                >
                  <h3 class="text-lg font-medium text-gray-900">{{ resume.title }}</h3>
                  <p class="mt-1 text-sm text-gray-500">Last updated: {{ formatDate(resume.updated_at) }}</p>
                </div>
              </div>
              
              <div class="mt-6 flex justify-end">
                <button 
                  @click="proceedWithExistingResume"
                  class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white transition-colors duration-200"
                  :class="[
                    !selectedResumeId 
                      ? 'bg-gray-400 cursor-not-allowed' 
                      : 'bg-emerald-600 hover:bg-emerald-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-emerald-500'
                  ]"
                  :disabled="!selectedResumeId"
                  v-tooltip="!selectedResumeId ? 'Please select a resume first' : ''"
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
                >
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
                  @click="selectedResumeId = null;"
                  class="inline-flex items-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-emerald-500 transition-colors duration-200"
                  :class="{ 'opacity-50 cursor-not-allowed': creating }"
                  :disabled="creating"
                  v-tooltip="creating ? 'Please wait until the creation process is complete' : ''"
                >
                  Back
                </button>
                <button 
                  @click="createResumeFromExisting"
                  class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white transition-colors duration-200 ml-3"
                  :class="[
                    !form.title || !form.job_description || creating 
                      ? 'bg-gray-400 cursor-not-allowed' 
                      : 'bg-emerald-600 hover:bg-emerald-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-emerald-500'
                  ]"
                  :disabled="!form.title || !form.job_description || creating"
                  v-tooltip="!form.title ? 'Please enter a resume title' : (!form.job_description ? 'Please enter a job description' : (creating ? 'Creation in progress' : ''))"
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
      
      <!-- Create From Scratch Section -->
      <div v-if="creationStep === 'create'" class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
        <div class="px-6 py-5 border-b border-gray-200 bg-gray-50">
          <div class="flex items-center justify-between">
            <h2 class="text-xl font-semibold text-gray-900">Resume Details</h2>
            <div class="flex items-center">
              <button 
                v-if="currentStep === 1"
                @click="creationStep = 'method';"
                class="inline-flex items-center px-3 py-1.5 border border-gray-300 shadow-sm text-sm font-medium rounded text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-emerald-500 transition-colors duration-200 mr-4"
              >
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
                Back
              </button>
              <span class="text-sm text-gray-500 mr-2">Step {{ currentStep }} of {{ totalSteps }}</span>
              <div class="flex space-x-1">
                <div 
                  v-for="step in totalSteps" 
                  :key="step"
                  :class="[
                    'h-2 w-8 rounded-full',
                    currentStep >= step ? 'bg-emerald-500' : 'bg-gray-200'
                  ]"
                ></div>
              </div>
            </div>
          </div>
        </div>
        
        <form @submit.prevent="submitForm" class="p-6">
          <!-- Step 1: Basic Information & Personal Details (Combined) -->
          <div v-if="currentStep === 1" class="space-y-6">
            <div>
              <label for="title" class="block text-sm font-medium text-gray-700">Resume Title</label>
              <input 
                id="title"
                type="text" 
                v-model="form.title"  
                class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-emerald-500 focus:border-emerald-500 sm:text-sm"
                placeholder="e.g., Software Developer Resume - Google"
                required 
              >
              <p class="mt-1 text-xs text-gray-500">Give your resume a name to help you identify it later</p>
            </div>
            
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-2">Personal Information</label>
              <div class="bg-gray-50 p-4 rounded-md border border-gray-200">
                <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                  <div>
                    <label for="fullName" class="block text-xs font-medium text-gray-500">Full Name</label>
                    <input 
                      id="fullName"
                      type="text" 
                      v-model="generalDetails.fullName" 
                      class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-emerald-500 focus:border-emerald-500 sm:text-sm"
                      placeholder="John Doe"
                      required 
                    >
                  </div>
                  
                  <div>
                    <label for="email" class="block text-xs font-medium text-gray-500">Email</label>
                    <input 
                      id="email"
                      type="email" 
                      v-model="generalDetails.email" 
                      class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-emerald-500 focus:border-emerald-500 sm:text-sm"
                      placeholder="john.doe@example.com"
                      required>
                  </div>
                  
                  <div>
                    <label for="phone" class="block text-xs font-medium text-gray-500">Phone</label>
                    <input 
                      id="phone"
                      type="tel" 
                      v-model="generalDetails.phone" 
                      class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-emerald-500 focus:border-emerald-500 sm:text-sm"
                      placeholder="(123) 456-7890"
                      required>
                  </div>
                  
                  <div>
                    <label for="location" class="block text-xs font-medium text-gray-500">Location</label>
                    <input 
                      id="location"
                      type="text" 
                      v-model="generalDetails.location" 
                      class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-emerald-500 focus:border-emerald-500 sm:text-sm"
                      placeholder="San Francisco, CA"
                      required>
                  </div>
                </div>
                
                <div class="mt-4">
                  <label for="summary" class="block text-xs font-medium text-gray-500">Professional Summary</label>
                  <textarea 
                    id="summary"
                    v-model="generalDetails.summary" 
                    rows="4" 
                    class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-emerald-500 focus:border-emerald-500 sm:text-sm"
                    placeholder="Experienced software developer with 5+ years of experience..."
                    required>
                    </textarea>
                </div>
              </div>
            </div>
          </div>
          
          <!-- Step 2: Work Experience -->
          <div v-if="currentStep === 2" class="space-y-6">
            <div>
              <div class="flex items-center justify-between mb-2">
                <label class="block text-sm font-medium text-gray-700">Work Experience</label>
                <button 
                  type="button" 
                  @click="addWorkExperience"
                  class="inline-flex items-center px-2.5 py-1.5 border border-transparent text-xs font-medium rounded text-emerald-700 bg-emerald-100 hover:bg-emerald-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-emerald-500 transition-colors duration-200"
                >
                  Add Position
                </button>
              </div>
              
              <div v-if="workExperience.length === 0" class="bg-gray-50 p-6 rounded-md border border-gray-200 text-center">
                <p class="text-gray-500">No work experience added yet</p>
                <button 
                  type="button" 
                  @click="addWorkExperience"
                  class="mt-2 inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-emerald-700 bg-emerald-100 hover:bg-emerald-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-emerald-500 transition-colors duration-200"
                >
                  Add Work Experience
                </button>
              </div>
              
              <div v-else class="space-y-4">
                <div 
                  v-for="(job, index) in workExperience" 
                  :key="index"
                  class="bg-gray-50 p-4 rounded-md border border-gray-200"
                >
                  <div class="flex justify-between items-start mb-4">
                    <h3 class="text-sm font-medium text-gray-900">Position {{ index + 1 }}</h3>
                    <button 
                      type="button" 
                      @click="removeWorkExperience(index)"
                      class="text-red-600 hover:text-red-800 text-sm"
                    >
                      Remove
                    </button>
                  </div>
                  
                  <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                    <div>
                      <label :for="`job-title-${index}`" class="block text-xs font-medium text-gray-500">Job Title</label>
                      <input 
                        :id="`job-title-${index}`"
                        type="text" 
                        v-model="job.title" 
                        class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-emerald-500 focus:border-emerald-500 sm:text-sm"
                        placeholder="Software Engineer"
                        required>
                    </div>
                    
                    <div>
                      <label :for="`company-${index}`" class="block text-xs font-medium text-gray-500">Company</label>
                      <input 
                        :id="`company-${index}`"
                        type="text" 
                        v-model="job.company" 
                        class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-emerald-500 focus:border-emerald-500 sm:text-sm"
                        placeholder="Google"
                        required>
                    </div>
                    
                    <div>
                      <label :for="`start-date-${index}`" class="block text-xs font-medium text-gray-500">Start Date</label>
                      <input 
                        :id="`start-date-${index}`"
                        type="month" 
                        v-model="job.startDate" 
                        class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-emerald-500 focus:border-emerald-500 sm:text-sm"
                        required>
                    </div>
                    
                    <div>
                      <label :for="`end-date-${index}`" class="block text-xs font-medium text-gray-500">End Date</label>
                      <input 
                        :id="`end-date-${index}`"
                        type="month" 
                        v-model="job.endDate" 
                        class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-emerald-500 focus:border-emerald-500 sm:text-sm"
                        :required="!job.current"
                        :disabled="job.current">
                      <div class="mt-1 flex items-center">
                        <input 
                          :id="`current-job-${index}`"
                          type="checkbox" 
                          v-model="job.current" 
                          class="focus:ring-emerald-500 h-4 w-4 text-emerald-600 border-gray-300 rounded">
                        <label :for="`current-job-${index}`" class="ml-2 block text-xs text-gray-500">I currently work here</label>
                      </div>
                    </div>
                  </div>
                  
                  <div class="mt-4">
                    <label :for="`description-${index}`" class="block text-xs font-medium text-gray-500">Description</label>
                    <textarea 
                      :id="`description-${index}`"
                      v-model="job.description" 
                      rows="3" 
                      class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-emerald-500 focus:border-emerald-500 sm:text-sm"
                      placeholder="Describe your responsibilities and achievements"
                      required></textarea>
                  </div>
                </div>
              </div>
            </div>
          </div>
          
          <!-- Step 3: Education -->
          <div v-if="currentStep === 3" class="space-y-6">
            <div>
              <div class="flex items-center justify-between mb-2">
                <label class="block text-sm font-medium text-gray-700">Education</label>
                <button 
                  type="button" 
                  @click="addEducation"
                  class="inline-flex items-center px-2.5 py-1.5 border border-transparent text-xs font-medium rounded text-emerald-700 bg-emerald-100 hover:bg-emerald-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-emerald-500 transition-colors duration-200"
                >
                  Add Education
                </button>
              </div>
              
              <div v-if="education.length === 0" class="bg-gray-50 p-6 rounded-md border border-gray-200 text-center">
                <p class="text-gray-500">No education added yet</p>
                <button 
                  type="button" 
                  @click="addEducation"
                  class="mt-2 inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-emerald-700 bg-emerald-100 hover:bg-emerald-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-emerald-500 transition-colors duration-200"
                >
                  Add Education
                </button>
              </div>
              
              <div v-else class="space-y-4">
                <div 
                  v-for="(edu, index) in education" 
                  :key="index"
                  class="bg-gray-50 p-4 rounded-md border border-gray-200"
                >
                  <div class="flex justify-between items-start mb-4">
                    <h3 class="text-sm font-medium text-gray-900">Education {{ index + 1 }}</h3>
                    <button 
                      type="button" 
                      @click="removeEducation(index)"
                      class="text-red-600 hover:text-red-800 text-sm"
                    >
                      Remove
                    </button>
                  </div>
                  
                  <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                    <div>
                      <label :for="`institution-${index}`" class="block text-xs font-medium text-gray-500">Institution</label>
                      <input 
                        :id="`institution-${index}`"
                        type="text" 
                        v-model="edu.institution" 
                        class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-emerald-500 focus:border-emerald-500 sm:text-sm"
                        placeholder="Stanford University"
                        required>
                    </div>
                    
                    <div>
                      <label :for="`degree-${index}`" class="block text-xs font-medium text-gray-500">Degree</label>
                      <input 
                        :id="`degree-${index}`"
                        type="text" 
                        v-model="edu.degree" 
                        class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-emerald-500 focus:border-emerald-500 sm:text-sm"
                        placeholder="Bachelor of Science in Computer Science"
                        required>
                    </div>
                    
                    <div>
                      <label :for="`edu-start-date-${index}`" class="block text-xs font-medium text-gray-500">Start Date</label>
                      <input 
                        :id="`edu-start-date-${index}`"
                        type="month" 
                        v-model="edu.startDate" 
                        class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-emerald-500 focus:border-emerald-500 sm:text-sm"
                        required>
                    </div>
                    
                    <div>
                      <label :for="`edu-end-date-${index}`" class="block text-xs font-medium text-gray-500">End Date</label>
                      <input 
                        :id="`edu-end-date-${index}`"
                        type="month" 
                        v-model="edu.endDate" 
                        class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-emerald-500 focus:border-emerald-500 sm:text-sm"
                        :required="!edu.current"
                        :disabled="edu.current">
                      <div class="mt-1 flex items-center">
                        <input 
                          :id="`current-edu-${index}`"
                          type="checkbox" 
                          v-model="edu.current" 
                          class="focus:ring-emerald-500 h-4 w-4 text-emerald-600 border-gray-300 rounded">
                        <label :for="`current-edu-${index}`" class="ml-2 block text-xs text-gray-500">I'm currently studying here</label>
                      </div>
                    </div>
                  </div>
                  
                  <div class="mt-4">
                    <label :for="`edu-description-${index}`" class="block text-xs font-medium text-gray-500">Description</label>
                    <textarea 
                      :id="`edu-description-${index}`"
                      v-model="edu.description" 
                      rows="3" 
                      class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-emerald-500 focus:border-emerald-500 sm:text-sm"
                      placeholder="Relevant coursework, achievements, etc."></textarea>
                  </div>
                </div>
              </div>
            </div>
          </div>
          
          <!-- Step 4: Skills & Interests -->
          <div v-if="currentStep === 4" class="space-y-6">
            <div>
              <label for="skills" class="block text-sm font-medium text-gray-700">Skills</label>
              <div class="mt-1 flex rounded-md shadow-sm">
                <input 
                  id="skills"
                  type="text" 
                  v-model="newSkill" 
                  @keydown.enter.prevent="addSkill"
                  class="flex-1 min-w-0 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-emerald-500 focus:border-emerald-500 sm:text-sm"
                  placeholder="Add a skill and press Enter"
                >
                <button 
                  type="button"
                  @click="addSkill"
                  class="ml-3 inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-emerald-600 hover:bg-emerald-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-emerald-500 transition-colors duration-200"
                >
                  Add
                </button>
              </div>
              
              <div class="mt-2 flex flex-wrap gap-2">
                <div 
                  v-for="(skill, index) in skills" 
                  :key="index"
                  class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-emerald-100 text-emerald-800"
                >
                  {{ skill }}
                  <button 
                    type="button" 
                    @click="removeSkill(index)" 
                    class="ml-1.5 inline-flex items-center justify-center w-4 h-4 rounded-full text-emerald-400 hover:text-emerald-600 focus:outline-none"
                  >
                    <span class="sr-only">Remove</span>
                    <svg class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                  </button>
                </div>
              </div>
              <p v-if="skills.length === 0" class="mt-2 text-sm text-gray-600">Please add at least one skill</p>
            </div> 

            <div>
              <label for="professional-qualifications" class="block text-sm font-medium text-gray-700">Professional Qualifications</label>
              <div class="mt-1 flex rounded-md shadow-sm">
                <input
                  id="professional-qualifications"
                  type="text"
                  v-model="newQualification"
                  @keydown.enter.prevent="addQualification"
                  class="flex-1 min-w-0 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-emerald-500 focus:border-emerald-500 sm:text-sm"
                  placeholder="Add a qualification and press Enter"
                >
                <button
                  type="button"
                  @click="addQualification"
                  class="ml-3 inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-emerald-600 hover:bg-emerald-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-emerald-500 transition-colors duration-200"
                >
                  Add
                </button>
              </div>

              <div class="mt-2 flex flex-wrap gap-2">
                <div
                  v-for="(qualification, index) in professionalQualifications"
                  :key="index"
                  class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-emerald-100 text-emerald-800"
                >
                  {{ qualification.name }} ({{ qualification.issuer }}, {{ formatDate(qualification.date) }})
                  <button
                    type="button"
                    @click="removeQualification(index)"
                    class="ml-1.5 inline-flex items-center justify-center w-4 h-4 rounded-full text-emerald-400 hover:text-emerald-600 focus:outline-none"
                  >
                    <span class="sr-only">Remove</span>
                    <svg class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                  </button>
                </div>
              </div>
              <p v-if="professionalQualifications.length === 0" class="mt-2 text-sm text-gray-600">Please add at least one qualification</p>
            </div>
            
            <div>
              <label for="references" class="block text-sm font-medium text-gray-700">References</label>
              <textarea 
                id="references"
                v-model="referencesText" 
                rows="3" 
                class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-emerald-500 focus:border-emerald-500 sm:text-sm"
                placeholder="Add references or write 'References available upon request' (optional)"></textarea>
            </div>
          </div>
          
          <!-- Job Description Step (after creating draft resume) -->
          <div v-if="currentStep === 5 && draftResumeCreated" class="space-y-6">
            <div class="bg-green-50 border-l-4 border-green-400 p-4 mb-6">
              <div class="flex">
                <div class="flex-shrink-0">
                  <svg class="h-5 w-5 text-green-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                  </svg>
                </div>
                <div class="ml-3">
                  <p class="text-sm text-green-700">
                    Resume draft successfully created! Now add a job description to tailor your resume.
                  </p>
                  <p class="mt-1 text-sm text-green-600">
                    We'll use your job description to tailor your resume to match the requirements.
                  </p>
                </div>
              </div>
            </div>
            
            <div>
              <label for="job_description" class="block text-sm font-medium text-gray-700">Job Description</label>
              <textarea 
                id="job_description"
                v-model="form.job_description" 
                rows="8" 
                class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-emerald-500 focus:border-emerald-500 sm:text-sm"
                placeholder="Paste the full job description here"
                required
                :disabled="submitting"
              ></textarea>
              <p class="mt-1 text-xs text-gray-500">This helps us tailor your resume to match the job requirements</p>
            </div>
          </div>
          
          <!-- Navigation Buttons -->
          <div class="mt-8 flex justify-between">
            <button 
              v-if="currentStep > 1"
              type="button" 
              @click="prevStep"
              class="inline-flex items-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-emerald-500 transition-colors duration-200"
              :class="{ 'opacity-50 cursor-not-allowed': submitting }"
              :disabled="submitting"
              v-tooltip="submitting ? 'Please wait until the current process is complete' : ''"
            >
              <svg class="mr-2 h-5 w-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                <path fill-rule="evenodd" d="M12.707 5.293a1 1 0 010 1.414L9.414 10l3.293 3.293a1 1 0 01-1.414 1.414l-4-4a1 1 0 010-1.414l4-4a1 1 0 011.414 0z" clip-rule="evenodd" />
              </svg>
              Previous
            </button>
            <div v-else class="w-20"></div>
            
            <button 
              v-if="currentStep < totalSteps"
              type="button" 
              @click="nextStep"
              class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white transition-colors duration-200"
              :class="[
                !isStepValid 
                  ? 'bg-gray-400 cursor-not-allowed' 
                  : 'bg-emerald-600 hover:bg-emerald-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-emerald-500'
              ]"
              :disabled="!isStepValid"
              v-tooltip="!isStepValid ? getStepValidationMessage() : ''"
            >
              Next
              <svg class="ml-2 h-5 w-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd" />
              </svg>
            </button>
            
            <button 
              v-else
              type="submit"
              class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white transition-colors duration-200"
              :class="[
                submitting || !isStepValid 
                  ? 'bg-gray-400 cursor-not-allowed' 
                  : 'bg-emerald-600 hover:bg-emerald-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-emerald-500'
              ]"
              :disabled="submitting || !isStepValid"
              v-tooltip="submitting ? 'Creating your resume...' : (!isStepValid ? getStepValidationMessage() : '')"
            >
              <svg v-if="submitting" class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
              </svg>
              {{ submitting ? 'Creating...' : 'Create Resume' }}
            </button>
          </div>
        </form>
      </div>
    </div>
  </div>
</template>

<script>
import axios from 'axios';
import { debounce } from 'lodash';

export default {
  data() {
    return {
      // Creation method state
      creationStep: 'method', // 'method', 'upload', 'create', 'existing'
      
      // Upload resume state
      selectedFile: null,
      isDragging: false,
      uploading: false,
      uploadedResumeData: null,
      
      // Existing resume state
      resumes: [],
      loadingResumes: false,
      selectedResumeId: null,

      professionalQualifications: [],
      newQualification: '',
      
      // Create from scratch state
      currentStep: 1,
      totalSteps: 4, // Reduced from 5 to 4 since we combined steps 1 and 2
      submitting: false,
      tailoring: false,
      creating: false,
      draftResumeCreated: false,
      draftResumeId: null,
      
      // Form data
      form: {
        title: '',
        job_description: ''
      },
      generalDetails: {
        fullName: '',
        email: '',
        phone: '',
        location: '',
        summary: ''
      },
      workExperience: [],
      education: [],
      skills: [],
      newSkill: '', 
      referencesText: '',
      formErrors: {
        title: '',
        job_description: ''
      }
    };
  },
  computed: {
    isStepValid() {
      // Validate current step
      switch (this.currentStep) {
        case 1: // Basic Info & Personal Details
          return this.form.title && 
                 this.generalDetails.fullName && 
                 this.generalDetails.email && 
                 this.generalDetails.phone && 
                 this.generalDetails.location && 
                 this.generalDetails.summary;
        case 2: // Work Experience
          if (this.workExperience.length === 0) return false;
          return this.workExperience.every(job => 
            job.title && 
            job.company && 
            job.startDate && 
            (job.current || job.endDate) && 
            job.description
          );
        case 3: // Education
          if (this.education.length === 0) return false;
          return this.education.every(edu => 
            edu.institution && 
            edu.degree && 
            edu.startDate && 
            (edu.current || edu.endDate)
          );
        case 4: // Skills & Interests
          return this.skills.length > 0;
        case 5: // Job Description (after draft resume)
          return !!this.form.job_description;
        default:
          return true;
      }
    }
  },  
  methods: {
    addQualification() {
      if (this.newQualification.trim()) {
        const [name, issuer, date] = this.newQualification.split('|');
        this.professionalQualifications.push({
          name: name.trim(),
          issuer: issuer.trim(),
          date: date.trim(),
          current: true,
        });
        this.newQualification = ''; 
      }
    },

    removeQualification(index) {
      this.professionalQualifications.splice(index, 1); 
    },

    handleBeforeUnload(event) { 
      // Show confirmation dialog if there are unsaved changes
      if (this.form.title || this.generalDetails.fullName) {
        event.preventDefault();
        event.returnValue = '';
        return '';
      }
    },

    // Upload resume methods
    handleFileChange(event) {
      const file = event.target.files[0];
      if (file) {
        if (file.type !== 'application/pdf') {
          alert('Please select a PDF file');
          event.target.value = '';
          return;
        }
        
        if (file.size > 10 * 1024 * 1024) { // 10MB
          alert('File size must be less than 10MB');
          event.target.value = '';
          return;
        }
        
        this.selectedFile = file;
      }
    },
    
    handleFileDrop(event) {
      this.isDragging = false;
      const file = event.dataTransfer.files[0];
      if (file && file.type === 'application/pdf') {
        this.selectedFile = file; 
      } else {
        alert('Please drop a PDF file');
      }
    },

    async uploadResume() {
      if (!this.selectedFile) return;

      this.uploading = true;
      const formData = new FormData();
      
      // Ensure correct field name and append properly
      formData.append('resume_file', this.selectedFile, this.selectedFile.name);

      try {
        const response = await axios.post('/api/resumes/upload', formData, {
          headers: {
            'Authorization': `Bearer ${localStorage.getItem('auth_token')}`,
            'Content-Type': 'multipart/form-data'
          }
        });

        // Ensure `this.form` exists before assigning a title
        if (!this.form) this.form = {};

        this.uploadedResumeData = response.data.data;
        this.form.title = response.data.suggested_title ?? ''; // Handle possible undefined title
      } catch (error) {
        if (error.response?.status === 422) {
          this.formErrors = error.response.data.errors;
        } else {
          console.error('Upload error:', error);
        }
      } finally {
        this.uploading = false;
      }
    }, 

    
    // Extract title from job description
    async extractTitleFromJobDescription() {
      if (!this.form.job_description || this.form.job_description.trim().length < 50) return;
      
      try {
        // Only attempt to extract title if we have enough job description text
        if (this.uploadedResumeData) {
          const response = await axios.post('/api/resumes/tailor', {
            resume_data: this.uploadedResumeData,
            job_description: this.form.job_description
          }, {
            headers: { 'Authorization': `Bearer ${localStorage.getItem('auth_token')}` }
          });
          
          if (response.data.suggested_title) {
            this.form.title = response.data.suggested_title; 
          }
        }
      } catch (error) {
        console.error('Error extracting title from job description:', error);
        // Fall back to the basic regex method if AI extraction fails
        this.extractTitleWithRegex();
      }
    },
    
    // Fallback method using regex
    extractTitleWithRegex() {
      if (!this.form.job_description) return;
      
      // Look for common job title patterns
      const titleRegex = /(?:job title|position|role|title)(?:\s*:\s*|\s+is\s+)([^.,:;()\n]+)/i;
      const match = this.form.job_description.match(titleRegex);
      
      if (match && match[1]) {
        const jobTitle = match[1].trim();
        if (jobTitle.length > 0) {
          this.form.title = jobTitle; 
        }
      }
    },

    // Method selection
    selectMethod(method) {
      this.creationStep = method;  
      if (method === 'existing') {
        this.fetchResumes();
      }
    },
    
    async createResumeFromUpload() {
      if (!this.form.title || !this.form.job_description || this.tailoring) return;

      this.tailoring = true;
      this.formErrors = { title: '', job_description: '' };

      try {
        // 1. Upload and parse resume
        const formData = new FormData();
        formData.append('resume_file', this.selectedFile);
        
        const uploadResponse = await axios.post('/api/resumes/upload', formData, {
          headers: {
            'Content-Type': 'multipart/form-data',
            'Authorization': `Bearer ${localStorage.getItem('auth_token')}`
          }
        });

        // 2. Directly tailor the uploaded resume
        const tailoredResponse = await axios.post('/api/resumes/tailor', {
          resume_id: uploadResponse.data.data.id,
          job_description: this.form.job_description,
          title: this.form.title
        }, {
          headers: { 'Authorization': `Bearer ${localStorage.getItem('auth_token')}` }
        });

        // 3. Redirect to tailored resume
        this.$router.push(`/dashboard/resume/${tailoredResponse.data.resume_id}`);
        
        // 4. Clear upload data
        this.selectedFile = null;
        this.uploadedResumeData = null;

      } catch (error) {
        console.error('Error:', error.response?.data || error.message);
        alert(error.response?.data?.error || 'Failed to create resume');
      } finally {
        this.tailoring = false;
      }
    },

    // Helper method to validate and fix resume data structure
    validateAndFixResumeData(resumeData) {
      if (!resumeData) {
        console.error('Resume data is null or undefined');
        return;
      }

      console.log('Validating resume data structure:', resumeData);

      // Ensure general_details exists and has all required fields
      if (!resumeData.general_details || typeof resumeData.general_details !== 'object') {
        console.log('Creating empty general_details');
        resumeData.general_details = {};
      }

      const requiredFields = ['fullName', 'email', 'phone', 'location', 'summary'];
      requiredFields.forEach(field => {
        if (!resumeData.general_details[field]) {
          console.log(`Adding missing field in general_details: ${field}`);
          resumeData.general_details[field] = '';
        }
      });

      // Ensure work_experience is an array
      if (!Array.isArray(resumeData.work_experience)) {
        console.log('Creating empty work_experience array');
        resumeData.work_experience = [];
      }

      // Ensure each work experience has the required fields
      resumeData.work_experience.forEach((job, index) => {
        console.log(`Validating work experience ${index}:`, job);

        // Convert job_title to title if needed
        if (job.job_title && !job.title) {
          job.title = job.job_title;
        }

        // Convert start_date to startDate if needed
        if (job.start_date && !job.startDate) {
          job.startDate = job.start_date;
        }

        // Convert end_date to endDate if needed
        if (job.end_date && !job.endDate) {
          job.endDate = job.end_date;
        }

        // Convert responsibilities array to description string if needed
        if (Array.isArray(job.responsibilities) && !job.description) {
          job.description = job.responsibilities.join(' ');
        }

        // Ensure current field exists
        if (job.current === undefined) {
          job.current = job.endDate ? false : true;
        }
      });

      // Ensure education is an array
      if (!Array.isArray(resumeData.education)) {
        console.log('Creating empty education array');
        resumeData.education = [];
      }

      // Ensure each education entry has the required fields
      resumeData.education.forEach((edu, index) => {
        console.log(`Validating education ${index}:`, edu);

        // Convert graduation_date to endDate if needed
        if (edu.graduation_date && !edu.endDate) {
          edu.endDate = edu.graduation_date;
        }

        // Ensure current field exists
        if (edu.current === undefined) {
          edu.current = false;
        }

        // Ensure startDate exists
        if (!edu.startDate) {
          edu.startDate = '';
        }

        // Ensure description exists
        if (!edu.description) {
          edu.description = '';
        }
      });

      // Ensure skills has items array
      if (!resumeData.skills || !resumeData.skills.items) {
        console.log('Creating empty skills.items array');
        resumeData.skills = { items: [] };
      }

      // Ensure referees has text property
      if (!resumeData.referees || !resumeData.referees.text) {
        console.log('Creating empty referees.text');
        resumeData.referees = { text: '' };
      }

      // Ensure professional_qualifications has items array
      if (!resumeData.professional_qualifications || !resumeData.professional_qualifications.items) {
        console.log('Creating empty professional_qualifications.items array');
        resumeData.professional_qualifications = { items: [] };
      }

      console.log('Validated resume data:', resumeData);
    },

    async createResumeFromExisting() {
      if (!this.form.title || !this.form.job_description || !this.selectedResumeId || this.creating) return;

      this.creating = true;

      try {
        // Directly update and tailor existing resume
        const response = await axios.post('/api/resumes/from-existing', {
          source_resume_id: this.selectedResumeId,
          job_description: this.form.job_description,
          title: this.form.title
        }, {
          headers: { 'Authorization': `Bearer ${localStorage.getItem('auth_token')}` }
        });

        // Redirect to the new resume view page using the returned resume_id
        this.$router.push(`/dashboard/resume/${response.data.resume_id}`);

        // Reset form and selection
        this.form.title = '';
        this.form.job_description = '';
        this.selectedResumeId = null;
        this.creationStep = 'method';

      } catch (error) {
        console.error('Error:', error.response?.data || error.message);
        alert(error.response?.data?.error || 'Failed to create resume');
      } finally {
        this.creating = false;
      }
    },
    
    // Existing resume methods
    async fetchResumes() {
      try {
        this.loadingResumes = true;
        const res = await axios.get('/api/resumes', {
          params: { status: 'completed' },
          headers: { 'Authorization': `Bearer ${localStorage.getItem('auth_token')}` }
        });
        this.resumes = res.data; 
      } catch (error) {
        console.error('Error fetching resumes:', error);
      } finally {
        this.loadingResumes = false;
      }
    },

    async generateCoverLetter(resumeId) {
      try {
        const response = await axios.post('/api/resumes/cover-letter', {
          resume_id: resumeId,
          structured: true
        }, {
          headers: { Authorization: `Bearer ${localStorage.getItem('auth_token')}` }
        });
        
        if (response.data.cover_letter) {
          this.resume.cover_letter = response.data.cover_letter;
        }
      } catch (error) {
        console.error('Error generating cover letter:', error);
      }
    },
    
    proceedWithExistingResume() {
      if (!this.selectedResumeId) return;
      
      // Find the selected resume to pre-fill the title
      const selectedResume = this.resumes.find(r => r.id === this.selectedResumeId);
      if (selectedResume) {
        this.form.title = `${selectedResume.title} - Copy`;
      }
    },
    
    // Create from scratch methods
    nextStep() {
      if (this.currentStep < this.totalSteps) {
        this.currentStep++; 
        window.scrollTo(0, 0);
      } else if (this.currentStep === this.totalSteps && !this.draftResumeCreated) {
        // Create draft resume without job description
        this.createDraftResume();
      }
    },
    
    prevStep() {
      if (this.currentStep > 1) {
        this.currentStep--; 
        window.scrollTo(0, 0);
      }
    },
    
    addWorkExperience() {
      this.workExperience.push({
        title: '',
        company: '',
        startDate: '',
        endDate: '',
        current: false,
        description: ''
      }); 
    },
    
    removeWorkExperience(index) {
      this.workExperience.splice(index, 1); 
    },
    
    addEducation() {
      this.education.push({
        institution: '',
        degree: '',
        startDate: '',
        endDate: '',
        current: false,
        description: ''
      }); 
    },
    
    removeEducation(index) {
      this.education.splice(index, 1); 
    },
    
    addSkill() {
      if (this.newSkill.trim()) {
        this.skills.push(this.newSkill.trim());
        this.newSkill = ''; 
      }
    },
    
    removeSkill(index) {
      this.skills.splice(index, 1); 
    },
    
    async createDraftResume() {
      this.submitting = true;
      
      try {
        // Prepare the data for draft resume (without job description)
        const data = {
          title: this.form.title,
          general_details: JSON.stringify(this.generalDetails),
          work_experience: JSON.stringify(this.workExperience),
          education: JSON.stringify(this.education),
          referees: JSON.stringify({
            text: this.referencesText
          }),
          skills: JSON.stringify({
            items: this.skills
          })
        };
        
        // Create the draft resume
        const createResponse = await axios.post('/api/resumes', data, {
          headers: { 'Authorization': `Bearer ${localStorage.getItem('auth_token')}` }
        });
        
        // Set draft resume created flag and ID
        this.draftResumeCreated = true;
        this.draftResumeId = createResponse.data.id;
        
        // Move to job description step
        this.totalSteps = 5; // Add one more step for job description
        this.currentStep = 5;
      } catch (error) {
        console.error('Error creating draft resume:', error);
        alert('There was an error creating your resume. Please try again.');
      } finally {
        this.submitting = false;
      }
    },
    
    async submitForm() {
      if (!this.draftResumeCreated) {
        // This should not happen as we now create a draft first
        this.createDraftResume();
        return;
      }
      
      this.submitting = true;
      
      try {
        // Prepare the resume data
        const resumeData = {
          general_details: this.generalDetails,
          work_experience: this.workExperience,
          education: this.education,
          skills: { items: this.skills },
          referees: { text: this.referencesText }
        };
        
        // Ensure all required fields are present
        this.validateAndFixResumeData(resumeData);
        
        // Tailor the existing draft resume with job description
        const tailorResponse = await axios.post('/api/resumes/tailor', {
          resume_id: this.draftResumeId,
          job_description: this.form.job_description,
          title: this.form.title
        }, {
          headers: { 'Authorization': `Bearer ${localStorage.getItem('auth_token')}` }
        });
        
        // Redirect to the resume view page
        this.$router.push(`/dashboard/resume/${this.draftResumeId}`);
      } catch (error) {
        console.error('Error finalizing resume:', error);
        alert('There was an error finalizing your resume. Please try again.');
      } finally {
        this.submitting = false;
      }
    },
    
    validateForm() {
      let isValid = true;
      this.formErrors = {
        title: '',
        job_description: ''
      };
      
      if (!this.form.title.trim()) {
        this.formErrors.title = 'Please enter a resume title';
        isValid = false;
      }
      
      if (!this.form.job_description.trim()) {
        this.formErrors.job_description = 'Please enter a job description';
        isValid = false;
      }
      
      return isValid;
    },
    
    formatDate(dateString) {
      const options = { year: 'numeric', month: 'long', day: 'numeric' };
      return new Date(dateString).toLocaleDateString(undefined, options);
    },
    
    // Get validation message for tooltip
    getStepValidationMessage() {
      switch (this.currentStep) {
        case 1:
          if (!this.form.title) return 'Please enter a resume title';
          if (!this.generalDetails.fullName) return 'Please enter your full name';
          if (!this.generalDetails.email) return 'Please enter your email';
          if (!this.generalDetails.phone) return 'Please enter your phone number';
          if (!this.generalDetails.location) return 'Please enter your location';
          if (!this.generalDetails.summary) return 'Please enter a professional summary';
          return 'Please complete all personal information fields';
        case 2:
          if (this.workExperience.length === 0) return 'Please add at least one work experience';
          return 'Please complete all work experience fields';
        case 3:
          if (this.education.length === 0) return 'Please add at least one education entry';
          return 'Please complete all education fields';
        case 4:
          if (this.skills.length === 0) return 'Please add at least one skill';
          return 'Please complete the skills section';
        case 5:
          if (!this.form.job_description) return 'Please enter a job description';
          return 'Please complete the job description';
        default:
          return 'Please complete all required fields';
      }
    }
  },
  watch: {
    'form.job_description': debounce(function(newVal) {
      if (newVal && !this.form.title) {
        this.extractTitleFromJobDescription();
      }
    }, 500),
    'generalDetails.current': function(newVal) {
      if (newVal) {
        this.generalDetails.endDate = '';
      }
    },
    'creationStep': function(newVal) {
      if (newVal === 'method') {
        this.selectedFile = null;
        this.uploadedResumeData = null;
        this.selectedResumeId = null;
        this.form.title = '';
        this.form.job_description = '';
      }
    }
  },
  mounted() {
    window.addEventListener('beforeunload', this.handleBeforeUnload);
  },
  beforeDestroy() {
    window.removeEventListener('beforeunload', this.handleBeforeUnload);
  }
};
</script>

<style scoped>
/* Add any component-specific styles here */
</style> 