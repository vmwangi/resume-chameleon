<template>
  <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
    <div class="px-6 py-5 border-b border-gray-200 bg-gray-50">
      <h2 class="text-xl font-semibold text-gray-900">Choose a Method</h2>
    </div>
    
    <div class="p-6 space-y-6">
      <div class="grid grid-cols-1 gap-6 sm:grid-cols-3">
        <div 
          @click="$emit('select-method', 'upload')"
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
        
        <div 
          @click="$emit('select-method', 'create')"
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
        
        <div 
          @click="$emit('select-method', 'existing')"
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
</template>

<script>
export default {
  name: 'MethodSelection'
};
</script>