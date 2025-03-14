<template>
  <div v-if="show" class="fixed inset-0 bg-gray-500 bg-opacity-75 flex items-center justify-center z-50">
    <div class="bg-white rounded-lg shadow-xl max-w-2xl w-full mx-4 overflow-hidden">
      <div class="px-6 py-4 border-b border-gray-200 bg-emerald-50 flex justify-between items-center">
        <h3 class="text-lg font-medium text-gray-900">Job Description</h3>
        <button @click="$emit('close')" class="text-gray-400 hover:text-gray-500 focus:outline-none">
          <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
          </svg>
        </button>
      </div>
      <div class="p-6 max-h-[70vh] overflow-y-auto">
        <p class="whitespace-pre-line text-gray-800">{{ jobDescription }}</p>
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
    jobDescription: {
      type: String,
      default: ''
    }
  }
}
</script>