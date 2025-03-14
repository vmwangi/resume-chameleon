<template>
  <div v-if="visible" class="mb-6 bg-blue-50 border-l-4 border-blue-400 p-4 rounded-lg">
    <div class="flex items-start justify-between gap-3">
      <div class="flex-1 flex items-start">
        <div class="flex-shrink-0 pt-0.5">
          <svg class="h-5 w-5 text-blue-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
          </svg>
        </div>
        <div class="ml-3">
          <p class="text-sm text-blue-800 leading-relaxed">
            Please verify all information before exporting your resume. Ensure all details are accurate and tailored to the job you're applying for.
          </p>
        </div>
      </div>
      <button 
        @click="close"
        class="p-1 -m-1 transition-colors duration-200 hover:bg-blue-100 rounded-full focus:outline-none focus:ring-2 focus:ring-blue-400 focus:ring-offset-2"
        aria-label="Close notification"
      >
        <svg 
          class="h-5 w-5 text-blue-500 hover:text-blue-700 transition-colors" 
          fill="none" 
          viewBox="0 0 24 24" 
          stroke="currentColor" 
          stroke-width="2"
        >
          <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
        </svg>
      </button>
    </div>
  </div>
</template>

<script>
export default {
  data() {
    return {
      visible: true
    }
  },
  methods: {
    close() {
      this.visible = false
    }
  }
}
</script>