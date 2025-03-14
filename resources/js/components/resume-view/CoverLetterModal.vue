<template>
  <div v-if="show" class="fixed inset-0 bg-gray-500 bg-opacity-75 flex items-center justify-center z-50">
    <div class="bg-white rounded-lg shadow-xl max-w-2xl w-full mx-4 overflow-hidden">
      <div class="px-6 py-4 border-b border-gray-200 bg-emerald-50 flex justify-between items-center">
        <h3 class="text-lg font-medium text-gray-900">Cover Letter</h3>
        <div class="flex items-center space-x-4">
          <button 
            @click="$emit('copy')" 
            class="inline-flex items-center px-3 py-1.5 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-emerald-500 transition-colors duration-200"
          >
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-1.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 5H6a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2v-2M8 5a2 2 0 002 2h2a2 2 0 002-2M8 5a2 2 0 012-2h2a2 2 0 012 2" />
            </svg>
            Copy
          </button>
          <button 
            @click="$emit('close')" 
            class="text-gray-400 hover:text-gray-500 focus:outline-none"
          >
            <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
            </svg>
          </button>
        </div>
      </div>
      <div class="p-6 max-h-[70vh] overflow-y-auto">
        <div v-if="coverLetterData">
          <div class="applicant-header text-right mb-4">
            <div>{{ coverLetterData.applicant.name }}</div>
            <div>{{ coverLetterData.applicant.address }}</div>
            <div>{{ coverLetterData.applicant.email }}</div>
            <div>{{ coverLetterData.applicant.phone }}</div>
          </div>

          <div class="date mb-4">
            {{ coverLetterData.date }}
          </div>

          <div class="company-address mb-4">
            <div>{{ coverLetterData.company.name }}</div>
            <div>{{ coverLetterData.company.address }}</div>
          </div>

          <div class="subject mb-4">
            <strong>Subject: {{ coverLetterData.subject }}</strong>
          </div>

          <div class="content">
            <p class="greeting mb-4">{{ coverLetterData.greeting }}</p>
            <p class="introduction mb-4">{{ coverLetterData.introduction }}</p>
            <p class="background mb-4">{{ coverLetterData.background }}</p>
            <p class="skills-alignment mb-4">{{ coverLetterData.skillsAlignment }}</p>
            <p class="closing mb-4">{{ coverLetterData.closing }}</p>
          </div>

          <div class="signature mt-8">
            Best regards,<br>
            {{ coverLetterData.applicant.name }}
          </div>
        </div>
        <div v-else>
          <pre class="whitespace-pre-wrap font-sans text-gray-800">{{ rawCoverLetter }}</pre>
        </div>
      </div>
      <div class="px-6 py-4 border-t border-gray-200 bg-gray-50 flex justify-end">
        <button 
          @click="$emit('close')"
          class="inline-flex items-center px-4 py-2 rounded-md bg-emerald-600 text-white hover:bg-emerald-700 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:ring-offset-2 transition-colors duration-200"
        >
          Close
        </button>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  props: {
    show: {
      type: Boolean,
      default: false
    },
    coverLetterData: {
      type: Object,
      default: null
    },
    rawCoverLetter: {
      type: String,
      default: ''
    }
  }
}
</script>