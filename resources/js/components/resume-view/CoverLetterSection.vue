<template>
  <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden mb-6">
    <div class="px-6 py-4 border-b border-gray-200 bg-gray-50 flex justify-between items-center">
      <div class="flex items-center">
        <h2 class="text-lg font-medium text-gray-900">Cover Letter</h2>
        <span class="ml-2 inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-emerald-100 text-emerald-800">
          Customized
        </span>
      </div>
      <div class="flex space-x-3">
        <button 
          @click="toggleEdit"
          class="text-emerald-600 hover:text-emerald-700 text-sm font-medium"
        >
          {{ editing ? 'Save' : 'Edit' }}
        </button>
        <button 
          @click="$emit('view-cover-letter')"
          class="inline-flex items-center px-3 py-1.5 border border-gray-300 shadow-sm text-sm font-medium rounded text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-emerald-500 transition-colors duration-200"
        >
          <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
          </svg>
          Preview
        </button>
      </div>
    </div>
    
    <!-- Cover Letter Content Preview (when not editing) -->
    <div v-if="!editing" class="p-6">
      <div class="bg-gray-50 rounded-lg p-4 border border-gray-200">
        <div class="flex flex-col md:flex-row md:justify-between mb-4">
          <div class="mb-2 md:mb-0">
            <p class="text-sm font-medium text-gray-700">{{ coverLetterData.subject }}</p>
            <p class="text-xs text-gray-500">For: {{ coverLetterData.company.name }}</p>
          </div>
          <div class="text-right">
            <p class="text-sm font-medium text-gray-700">{{ coverLetterData.applicant.name }}</p>
            <p class="text-xs text-gray-500">{{ coverLetterData.date }}</p>
          </div>
        </div>
        
        <div class="space-y-2 text-sm text-gray-600">
          <p class="italic">{{ coverLetterData.greeting }}</p>
          <p>{{ coverLetterData.introduction }}</p>
          <p>{{ coverLetterData.background }}</p>
          <p>{{ coverLetterData.skillsAlignment }}</p>
          <p>{{ coverLetterData.closing }}</p>
        </div>
      </div>
    </div>
    
    <!-- Cover Letter Edit Form -->
    <div class="p-6" v-if="editing">
      <div class="mb-6">
        <label class="block text-sm font-medium text-gray-700 mb-2">Applicant Information</label>
        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
          <div>
            <label class="block text-xs font-medium text-gray-500">Full Name</label>
            <input 
              type="text" 
              v-model="editableCoverLetter.applicant.name" 
              class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-emerald-500 focus:border-emerald-500 sm:text-sm"
            >
          </div>
          <div>
            <label class="block text-xs font-medium text-gray-500">Address</label>
            <input 
              type="text" 
              v-model="editableCoverLetter.applicant.address" 
              class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-emerald-500 focus:border-emerald-500 sm:text-sm"
            >
          </div>
          <div>
            <label class="block text-xs font-medium text-gray-500">Email</label>
            <input 
              type="email" 
              v-model="editableCoverLetter.applicant.email" 
              class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-emerald-500 focus:border-emerald-500 sm:text-sm"
            >
          </div>
          <div>
            <label class="block text-xs font-medium text-gray-500">Phone</label>
            <input 
              type="text" 
              v-model="editableCoverLetter.applicant.phone" 
              class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-emerald-500 focus:border-emerald-500 sm:text-sm"
            >
          </div>
        </div>
      </div>
      
      <div class="mb-6">
        <label class="block text-sm font-medium text-gray-700 mb-2">Date</label>
        <input 
          type="text" 
          v-model="editableCoverLetter.date" 
          class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-emerald-500 focus:border-emerald-500 sm:text-sm"
        >
      </div>
      
      <div class="mb-6">
        <label class="block text-sm font-medium text-gray-700 mb-2">Company Information</label>
        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
          <div>
            <label class="block text-xs font-medium text-gray-500">Company Name</label>
            <input 
              type="text" 
              v-model="editableCoverLetter.company.name" 
              class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-emerald-500 focus:border-emerald-500 sm:text-sm"
            >
          </div>
          <div>
            <label class="block text-xs font-medium text-gray-500">Company Address</label>
            <input 
              type="text" 
              v-model="editableCoverLetter.company.address"  
              class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-emerald-500 focus:border-emerald-500 sm:text-sm"
            >
          </div>
        </div>
      </div>
      
      <div class="mb-6">
        <label class="block text-sm font-medium text-gray-700 mb-2">Subject</label>
        <input 
          type="text" 
          v-model="editableCoverLetter.subject" 
          class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-emerald-500 focus:border-emerald-500 sm:text-sm"
        >
      </div>
      
      <div class="mb-6">
        <label class="block text-sm font-medium text-gray-700 mb-2">Greeting</label>
        <input 
          type="text" 
          v-model="editableCoverLetter.greeting"  
          class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-emerald-500 focus:border-emerald-500 sm:text-sm"
        >
      </div>
      
      <div class="mb-6">
        <label class="block text-sm font-medium text-gray-700 mb-2">Introduction</label>
        <textarea 
          v-model="editableCoverLetter.introduction" 
          rows="3" 
          class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-emerald-500 focus:border-emerald-500 sm:text-sm"
        ></textarea>
      </div>
      
      <div class="mb-6">
        <label class="block text-sm font-medium text-gray-700 mb-2">Background</label>
        <textarea 
          v-model="editableCoverLetter.background" 
          rows="4" 
          class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-emerald-500 focus:border-emerald-500 sm:text-sm"
        ></textarea>
      </div>
      
      <div class="mb-6">
        <label class="block text-sm font-medium text-gray-700 mb-2">Skills Alignment</label>
        <textarea 
          v-model="editableCoverLetter.skillsAlignment" 
          rows="4" 
          class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-emerald-500 focus:border-emerald-500 sm:text-sm"
        ></textarea>
      </div>
      
      <div class="mb-6">
        <label class="block text-sm font-medium text-gray-700 mb-2">Closing</label>
        <textarea 
          v-model="editableCoverLetter.closing" 
          rows="3" 
          class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-emerald-500 focus:border-emerald-500 sm:text-sm"
        ></textarea>
      </div>
      <div class="sm:col-span-2 flex justify-end mt-4">
        <button
          @click="$emit('cancel-edit')"
          class="mr-3 inline-flex items-center px-3 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-emerald-500 transition-colors duration-200"
        >
          Cancel
        </button>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  props: {
    coverLetterData: {
      type: Object,
      required: true
    },
    editing: {
      type: Boolean,
      default: false
    }
  },
  data() {
    return {
      editableCoverLetter: JSON.parse(JSON.stringify(this.coverLetterData))
    };
  },
  watch: {
    coverLetterData: {
      handler(newValue) {
        this.editableCoverLetter = JSON.parse(JSON.stringify(newValue));
      },
      deep: true
    }
  },
  methods: {
    toggleEdit() {
      if (this.editing) {
        this.$emit('save', this.editableCoverLetter);
      } else {
        this.$emit('toggle-edit');
      }
    }
  }
}
</script>