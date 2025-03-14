<template>
  <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden mb-6">
    <div class="px-6 py-4 border-b border-gray-200 bg-gray-50 flex justify-between items-center">
      <h2 class="text-lg font-medium text-gray-900">Work Experience</h2>
      <div class="flex space-x-3">
        <button
          @click="$emit('regenerate', 'work_experience')"
          class="text-emerald-600 hover:text-emerald-700 text-sm font-medium flex items-center"
          :disabled="regenerating"
          :class="{'opacity-50 cursor-not-allowed': regenerating}"
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
      <div v-if="!editing">
        <div v-for="(job, index) in workExperience" :key="index" class="mb-6 last:mb-0">
          <div class="flex justify-between">
            <h3 class="text-base font-medium text-gray-900">{{ job.title }}</h3>
            <span class="text-sm text-gray-500">
              {{ formatDate(job.startDate) }} - {{ job.current ? 'Present' : formatDate(job.endDate) }}
            </span>
          </div>
          <p class="text-sm font-medium text-emerald-600">{{ job.company }}</p>
          <p v-for="(desc, descIndex) in Array.isArray(job.description) ? job.description : [job.description]" 
             :key="descIndex"
             class="mt-2 text-sm text-gray-800">
            {{ desc }}
          </p>
        </div>
      </div>
      <div v-else>
        <div v-for="(job, index) in editableExperience" :key="index" class="mb-6 border border-gray-200 rounded-md p-4">
          <div class="flex justify-between items-start mb-4">
            <h3 class="text-sm font-medium text-gray-900">Position {{ index + 1 }}</h3>
            <button
              @click="removeWorkExperience(index)"
              class="text-red-600 hover:text-red-800 text-sm"
            >
              Remove
            </button>
          </div>

          <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
            <div>
              <label class="block text-xs font-medium text-gray-500">Job Title</label>
              <input
                type="text"
                v-model="job.title"
                class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-emerald-500 focus:border-emerald-500 sm:text-sm"
              >
            </div>

            <div>
              <label class="block text-xs font-medium text-gray-500">Company</label>
              <input
                type="text"
                v-model="job.company"
                class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-emerald-500 focus:border-emerald-500 sm:text-sm"
              >
            </div>

            <div>
              <label class="block text-xs font-medium text-gray-500">Start Date</label>
              <input
                type="month"
                v-model="job.startDate"
                class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-emerald-500 focus:border-emerald-500 sm:text-sm"
              >
            </div>

            <div>
              <label class="block text-xs font-medium text-gray-500">End Date</label>
              <input
                type="month"
                v-model="job.endDate"
                class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-emerald-500 focus:border-emerald-500 sm:text-sm"
                :disabled="job.current"
              >
              <div class="mt-1 flex items-center">
                <input
                  type="checkbox"
                  v-model="job.current"
                  class="focus:ring-emerald-500 h-4 w-4 text-emerald-600 border-gray-300 rounded"
                >
                <label class="ml-2 block text-xs text-gray-500">I currently work here</label>
              </div>
            </div>
          </div>

          <div class="mt-4">
            <label class="block text-xs font-medium text-gray-500">Description</label>
            <p class="mt-1 text-xs text-gray-500">Include detailed responsibilities and quantifiable achievements with metrics where possible. Aim for 3 sentences: responsibilities, achievements with metrics, and relevance to the job.</p>
            <textarea
              v-model="job.description"
              rows="5"
              class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-emerald-500 focus:border-emerald-500 sm:text-sm"
              placeholder="Describe your responsibilities and achievements with specific metrics (e.g., 'Increased sales by 20%', 'Managed a team of 15 people', 'Reduced costs by $50,000 annually')"
            ></textarea>
          </div>
        </div>

        <button
          type="button"
          @click="addWorkExperience"
          class="mt-4 inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-emerald-700 bg-emerald-100 hover:bg-emerald-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-emerald-500 transition-colors duration-200"
        >
          <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-1" viewBox="0 0 20 20" fill="currentColor">
            <path fill-rule="evenodd" d="M10 3a1 1 0 011 1v5h5a1 1 0 110 2h-5v5a1 1 0 11-2 0v-5H4a1 1 0 110-2h5V4a1 1 0 011-1z" clip-rule="evenodd" />
          </svg>
          Add Position
        </button>
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
    workExperience: {
      type: Array,
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
      editableExperience: []
    };
  },
  created() {
    this.initializeEditableExperience();
  },
  watch: {
    workExperience: {
      handler() {
        this.initializeEditableExperience();
      },
      deep: true
    }
  },
  methods: {
    initializeEditableExperience() {
      this.editableExperience = this.workExperience.map(job => {
        const formattedJob = { ...job };
        
        // Format startDate for the month input if it exists
        if (formattedJob.startDate) {
          formattedJob.startDate = this.formatDateForInput(formattedJob.startDate);
        }
        
        // Format endDate for the month input if it exists and job is not current
        if (formattedJob.endDate && !formattedJob.current) {
          formattedJob.endDate = this.formatDateForInput(formattedJob.endDate);
        }
        
        return formattedJob;
      });
    },
    formatDateForInput(dateString) {
      if (!dateString) return '';
      // Convert YYYY-MM-DD to YYYY-MM format required by month input
      const date = new Date(dateString);
      if (isNaN(date)) return dateString;
      
      const year = date.getFullYear();
      // Month is 0-indexed in JS, so add 1 and pad with leading zero if needed
      const month = String(date.getMonth() + 1).padStart(2, '0');
      
      return `${year}-${month}`;
    },
    formatDate(dateString) {
      if (!dateString || dateString === 'Present') return 'Present';
      const date = new Date(dateString);
      return isNaN(date) ? dateString : date.toLocaleDateString('en-US', {
        year: 'numeric',
        month: 'short'
      });
    },
    addWorkExperience() {
      this.editableExperience.push({
        title: '',
        company: '',
        startDate: '',
        endDate: '',
        current: false,
        description: ''
      });
    },
    removeWorkExperience(index) {
      this.editableExperience.splice(index, 1);
    },
    toggleEdit() {
      if (this.editing) {
        this.$emit('save', this.editableExperience);
      } else {
        this.$emit('toggle-edit');
      }
    }
  }
}
</script>