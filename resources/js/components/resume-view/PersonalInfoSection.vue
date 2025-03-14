<template>
  <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden mb-6">
    <div class="px-6 py-4 border-b border-gray-200 bg-gray-50 flex justify-between items-center">
      <h2 class="text-lg font-medium text-gray-900">Personal Information</h2>
      <div class="flex space-x-3">
        <button 
          @click="$emit('regenerate', 'general_details')"
          class="text-emerald-600 hover:text-emerald-700 text-sm font-medium flex items-center"
          :disabled="regenerating"
          :class="{ 'opacity-50 cursor-not-allowed' : regenerating }"
        >
          <svg v-if="regenerating" class="animate-spin -ml-1 mr-2 h-4 w-4 text-emerald-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
          </svg>
          <svg v-else xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
          </svg>
          {{ regenerating ? 'Regenerating...' : 'Regenerate' }}
          <span v-if="regenerationLimit > 0" class="ml-1 text-xs text-gray-500">({{ regenerationLimit }}/3)</span>
        </button>
        <button 
          @click="toggleEdit"
          class="text-emerald-600 hover:text-emerald-700 text-sm font-medium"
        >
          {{ editing ? 'Save' : 'Edit' }}
        </button>
      </div>
    </div>
    <div class="p-6">
      <div v-if="!editing" class="grid grid-cols-1 gap-4 sm:grid-cols-2">
        <div>
          <h3 class="text-sm font-medium text-gray-500">Full Name</h3>
          <p class="mt-1 text-sm text-gray-800">{{ personalInfo.fullName }}</p>
        </div>
        <div>
          <h3 class="text-sm font-medium text-gray-500">Email</h3>
          <p class="mt-1 text-sm text-gray-800">{{ personalInfo.email }}</p>
        </div>
        <div>
          <h3 class="text-sm font-medium text-gray-500">Phone</h3>
          <p class="mt-1 text-sm text-gray-800">{{ personalInfo.phone }}</p>
        </div>
        <div>
          <h3 class="text-sm font-medium text-gray-500">Location</h3>
          <p class="mt-1 text-sm text-gray-800">{{ personalInfo.location }}</p>
        </div>
        <div class="sm:col-span-2">
          <h3 class="text-sm font-medium text-gray-500">Professional Summary</h3>
          <p class="mt-1 text-sm text-gray-800">{{ personalInfo.summary }}</p>
        </div>
      </div>
      <div v-else class="grid grid-cols-1 gap-4 sm:grid-cols-2">
        <div>
          <label class="block text-sm font-medium text-gray-500">Full Name</label>
          <input 
            type="text" 
            v-model="editableInfo.fullName" 
            class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-emerald-500 focus:border-emerald-500 sm:text-sm"
          >
        </div>
        <div>
          <label class="block text-sm font-medium text-gray-500">Email</label>
          <input 
            type="email" 
            v-model="editableInfo.email" 
            class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-emerald-500 focus:border-emerald-500 sm:text-sm"
          >
        </div>
        <div>
          <label class="block text-sm font-medium text-gray-500">Phone</label>
          <input 
            type="tel" 
            v-model="editableInfo.phone" 
            class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-emerald-500 focus:border-emerald-500 sm:text-sm"
          >
        </div>
        <div>
          <label class="block text-sm font-medium text-gray-500">Location</label>
          <input 
            type="text" 
            v-model="editableInfo.location" 
            class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-emerald-500 focus:border-emerald-500 sm:text-sm"
          >
        </div>
        <div class="sm:col-span-2">
          <label class="block text-sm font-medium text-gray-500">Professional Summary</label>
          <textarea 
            v-model="editableInfo.summary" 
            rows="4" 
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
  </div>
</template>

<script>
export default {
  props: {
    personalInfo: {
      type: Object,
      required: true
    },
    editing: {
      type: Boolean,
      default: false
    },
    regenerating: {
      type: Boolean,
      default: false
    },
    regenerationLimit: {
      type: Number,
      default: 0
    }
  },
  data() {
    return {
      editableInfo: { ...this.personalInfo }
    };
  },
  watch: {
    personalInfo: {
      handler(newValue) {
        this.editableInfo = { ...newValue };
      },
      deep: true
    }
  },
  methods: {
    toggleEdit() {
      if (this.editing) {
        this.$emit('save', this.editableInfo);
      } else {
        this.$emit('toggle-edit');
      }
    }
  }
}
</script>