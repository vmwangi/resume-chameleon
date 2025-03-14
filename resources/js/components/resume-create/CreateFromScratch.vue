<template>
  <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
    <div class="px-6 py-5 border-b border-gray-200 bg-gray-50">
      <div class="flex items-center justify-between">
        <h2 class="text-xl font-semibold text-gray-900">Resume Details</h2>
        <div class="flex items-center">
          <button 
            v-if="currentStep === 1"
            @click="$emit('update:creationStep', 'method')"
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
          />
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
                />
              </div>
              
              <div>
                <label for="email" class="block text-xs font-medium text-gray-500">Email</label>
                <input 
                  id="email"
                  type="email" 
                  v-model="generalDetails.email" 
                  class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-emerald-500 focus:border-emerald-500 sm:text-sm"
                  placeholder="john.doe@example.com"
                  required />
              </div>
              
              <div>
                <label for="phone" class="block text-xs font-medium text-gray-500">Phone</label>
                <input 
                  id="phone"
                  type="tel" 
                  v-model="generalDetails.phone" 
                  class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-emerald-500 focus:border-emerald-500 sm:text-sm"
                  placeholder="(123) 456-7890"
                  required />
              </div>
              
              <div>
                <label for="location" class="block text-xs font-medium text-gray-500">Location</label>
                <input 
                  id="location"
                  type="text" 
                  v-model="generalDetails.location" 
                  class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-emerald-500 focus:border-emerald-500 sm:text-sm"
                  placeholder="San Francisco, CA"
                  required />
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
                required />
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
                    required />
                </div>
                
                <div>
                  <label :for="`company-${index}`" class="block text-xs font-medium text-gray-500">Company</label>
                  <input 
                    :id="`company-${index}`"
                    type="text" 
                    v-model="job.company" 
                    class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-emerald-500 focus:border-emerald-500 sm:text-sm"
                    placeholder="Google"
                    required />
                </div>
                
                <div>
                  <label :for="`start-date-${index}`" class="block text-xs font-medium text-gray-500">Start Date</label>
                  <input 
                    :id="`start-date-${index}`"
                    type="month" 
                    v-model="job.startDate" 
                    class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-emerald-500 focus:border-emerald-500 sm:text-sm"
                    required />
                </div>
                
                <div>
                  <label :for="`end-date-${index}`" class="block text-xs font-medium text-gray-500">End Date</label>
                  <input 
                    :id="`end-date-${index}`"
                    type="month" 
                    v-model="job.endDate" 
                    class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-emerald-500 focus:border-emerald-500 sm:text-sm"
                    :required="!job.current"
                    :disabled="job.current" />
                  <div class="mt-1 flex items-center">
                    <input 
                      :id="`current-job-${index}`"
                      type="checkbox" 
                      v-model="job.current" 
                      class="focus:ring-emerald-500 h-4 w-4 text-emerald-600 border-gray-300 rounded" />
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
                  required />
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
                    required />
                </div>
                
                <div>
                  <label :for="`degree-${index}`" class="block text-xs font-medium text-gray-500">Degree</label>
                  <input 
                    :id="`degree-${index}`"
                    type="text" 
                    v-model="edu.degree" 
                    class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-emerald-500 focus:border-emerald-500 sm:text-sm"
                    placeholder="Bachelor of Science in Computer Science"
                    required />
                </div>
                
                <div>
                  <label :for="`edu-start-date-${index}`" class="block text-xs font-medium text-gray-500">Start Date</label>
                  <input 
                    :id="`edu-start-date-${index}`"
                    type="month" 
                    v-model="edu.startDate" 
                    class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-emerald-500 focus:border-emerald-500 sm:text-sm"
                    required />
                </div>
                
                <div>
                  <label :for="`edu-end-date-${index}`" class="block text-xs font-medium text-gray-500">End Date</label>
                  <input 
                    :id="`edu-end-date-${index}`"
                    type="month" 
                    v-model="edu.endDate" 
                    class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-emerald-500 focus:border-emerald-500 sm:text-sm"
                    :required="!edu.current"
                    :disabled="edu.current" />
                  <div class="mt-1 flex items-center">
                    <input 
                      :id="`current-edu-${index}`"
                      type="checkbox" 
                      v-model="edu.current" 
                      class="focus:ring-emerald-500 h-4 w-4 text-emerald-600 border-gray-300 rounded" />
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
                  placeholder="Relevant coursework, achievements, etc." />
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
              :value="newSkill"
              @input="$emit('update:newSkill', $event.target.value)"
              @keydown.enter.prevent="addSkill"
              class="flex-1 min-w-0 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-emerald-500 focus:border-emerald-500 sm:text-sm"
              placeholder="Add a skill and press Enter" />
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
          <label for="references" class="block text-sm font-medium text-gray-700">References</label>
          <textarea 
            id="references"
            :value="referencesText"
            @input="$emit('update:referencesText', $event.target.value)"
            rows="3" 
            class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-emerald-500 focus:border-emerald-500 sm:text-sm"
            placeholder="Add references or write 'References available upon request' (optional)" />
        </div>
      </div>
      
      <!-- Step 5: Job Description -->
      <div v-if="currentStep === 5" class="space-y-6">
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
            :disabled="submitting" />
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
          :title="submitting ? 'Please wait until the current process is complete' : ''"
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
          :title="!isStepValid ? getStepValidationMessage() : ''"
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
          :title="submitting ? 'Creating your resume...' : (!isStepValid ? getStepValidationMessage() : '')"
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
</template>

<script>
export default {
  name: 'CreateFromScratch',
  props: {
    currentStep: Number,
    totalSteps: Number,
    form: Object,
    generalDetails: Object,
    workExperience: Array,
    education: Array,
    skills: Array,
    newSkill: String,
    referencesText: String,
    submitting: Boolean,
    draftResumeCreated: Boolean
  },
  methods: {
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
        this.$emit('update:newSkill', ''); // Clear the input after adding
      }
    },
    removeSkill(index) {
      this.skills.splice(index, 1);
    },
    prevStep() {
      if (this.currentStep > 1) {
        this.$emit('update:currentStep', this.currentStep - 1);
        window.scrollTo(0, 0);
      }
    },
    nextStep() {
      if (this.currentStep < this.totalSteps) {
        this.$emit('update:currentStep', this.currentStep + 1);
        window.scrollTo(0, 0);
      }
    },
    submitForm() {
      this.$emit('submit-form');
    },
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
  computed: {
    isStepValid() {
      switch (this.currentStep) {
        case 1:
          return this.form.title && 
                 this.generalDetails.fullName && 
                 this.generalDetails.email && 
                 this.generalDetails.phone && 
                 this.generalDetails.location && 
                 this.generalDetails.summary;
        case 2:
          if (this.workExperience.length === 0) return false;
          return this.workExperience.every(job => 
            job.title && 
            job.company && 
            job.startDate && 
            (job.current || job.endDate) && 
            job.description
          );
        case 3:
          if (this.education.length === 0) return false;
          return this.education.every(edu => 
            edu.institution && 
            edu.degree && 
            edu.startDate && 
            (edu.current || edu.endDate)
          );
        case 4:
          return this.skills.length > 0;
        case 5:
          return !!this.form.job_description;
        default:
          return true;
      }
    }
  }
};
</script>