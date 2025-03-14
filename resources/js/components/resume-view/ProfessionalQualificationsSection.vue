<template>
  <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden mb-6">
    <div class="px-6 py-4 border-b border-gray-200 bg-gray-50 flex justify-between items-center">
      <h2 class="text-lg font-medium text-gray-900">Professional Qualifications</h2>
      <div class="flex space-x-3">
        <button
          @click="$emit('regenerate', 'professional_qualifications')"
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
        <div v-if="processedQualifications.length > 0">
          <div class="flex flex-wrap gap-2">
            <div 
              v-for="(qual, index) in processedQualifications" 
              :key="index" 
              class="px-3 py-1.5 bg-gray-50 rounded-md text-sm border border-gray-100 hover:border-emerald-100 transition-colors"
            >
              {{ qual.name }}
            </div>
          </div>
        </div>
        <p v-else class="text-sm text-gray-500">No professional qualifications added yet</p>
      </div>
      <div v-else>
        <div v-if="editableQualifications.items && editableQualifications.items.length > 0">
          <div v-for="(qual, index) in editableQualifications.items" :key="index" class="mb-6 border border-gray-200 rounded-md p-4">
            <div class="flex justify-between items-start mb-4">
              <h3 class="text-sm font-medium text-gray-900">Qualification {{ index + 1 }}</h3>
              <button
                @click="removeQualification(index)"
                class="text-red-600 hover:text-red-800 text-sm"
              >
                Remove
              </button>
            </div>

            <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
              <div>
                <label class="block text-xs font-medium text-gray-500">Qualification Name</label>
                <input
                  type="text"
                  v-model="qual.name"
                  class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-emerald-500 focus:border-emerald-500 sm:text-sm"
                  placeholder="AWS Certified Solutions Architect"
                >
              </div>

              <div>
                <label class="block text-xs font-medium text-gray-500">Issuing Organization</label>
                <input
                  type="text"
                  v-model="qual.issuer"
                  class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-emerald-500 focus:border-emerald-500 sm:text-sm"
                  placeholder="Amazon Web Services"
                >
              </div>

              <div>
                <label class="block text-xs font-medium text-gray-500">Issue Date</label>
                <input
                  type="month"
                  v-model="qual.date"
                  class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-emerald-500 focus:border-emerald-500 sm:text-sm"
                >
              </div>

              <div>
                <label class="block text-xs font-medium text-gray-500">Expiration Date</label>
                <input
                  type="month"
                  v-model="qual.expirationDate"
                  class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-emerald-500 focus:border-emerald-500 sm:text-sm"
                  :disabled="qual.current"
                >
                <div class="mt-1 flex items-center">
                  <input
                    type="checkbox"
                    v-model="qual.current"
                    class="focus:ring-emerald-500 h-4 w-4 text-emerald-600 border-gray-300 rounded"
                  >
                  <label class="ml-2 block text-xs text-gray-500">No expiration / Does not expire</label>
                </div>
              </div>
            </div>
          </div>
        </div>

        <button
          type="button"
          @click="addQualification"
          class="mt-4 inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-emerald-700 bg-emerald-100 hover:bg-emerald-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-emerald-500 transition-colors duration-200"
        >
          <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-1" viewBox="0 0 20 20" fill="currentColor">
            <path fill-rule="evenodd" d="M10 3a1 1 0 011 1v5h5a1 1 0 110 2h-5v5a1 1 0 11-2 0v-5H4a1 1 0 110-2h5V4a1 1 0 011-1z" clip-rule="evenodd" />
          </svg>
          Add Qualification
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
    qualifications: {
      type: [Object, String],
      required: true,
      default: () => ({ items: [] })
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
      editableQualifications: { items: [] }
    };
  },
  computed: {
    processedQualifications() {
      // Handle the nested structure or string format of qualifications
      try {
        // If qualifications is a string, try to parse it
        if (typeof this.qualifications === 'string') {
          const parsed = JSON.parse(this.qualifications);
          // Check for double-nested structure
          if (parsed.professional_qualifications?.items) {
            return parsed.professional_qualifications.items.map(item => 
              typeof item === 'string' ? { name: item } : item
            );
          }
          // Check for simple items array
          if (parsed.items) {
            return parsed.items.map(item => 
              typeof item === 'string' ? { name: item } : item
            );
          }
          // If parsed is an array, assume it's an array of qualifications
          if (Array.isArray(parsed)) {
            return parsed.map(item => 
              typeof item === 'string' ? { name: item } : item
            );
          }
          return [];
        } 
        
        // If qualifications is an object
        if (typeof this.qualifications === 'object') {
          // Handle double-nested structure
          if (this.qualifications.professional_qualifications?.items) {
            return this.qualifications.professional_qualifications.items.map(item => 
              typeof item === 'string' ? { name: item } : item
            );
          }
          // Handle simple items array
          if (this.qualifications.items) {
            return this.qualifications.items.map(item => 
              typeof item === 'string' ? { name: item } : item
            );
          }
          // If qualifications is an array, assume it's an array of qualifications
          if (Array.isArray(this.qualifications)) {
            return this.qualifications.map(item => 
              typeof item === 'string' ? { name: item } : item
            );
          }
        }
        
        return [];
      } catch (e) {
        console.error('Error parsing qualifications:', e);
        return [];
      }
    }
  },
  watch: {
    qualifications: {
      handler() {
        this.initializeEditableQualifications();
      },
      immediate: true,
      deep: true
    },
    editing(newVal) {
      if (newVal) {
        this.initializeEditableQualifications();
      }
    }
  },
  methods: {
    initializeEditableQualifications() {
      try {
        let items = [];
        
        // Handle different formats of qualifications data
        if (typeof this.qualifications === 'string') {
          const parsed = JSON.parse(this.qualifications);
          // Check for double-nested structure
          if (parsed.professional_qualifications?.items) {
            items = parsed.professional_qualifications.items;
          } else if (parsed.items) {
            items = parsed.items;
          } else if (Array.isArray(parsed)) {
            items = parsed;
          }
        } else if (this.qualifications.professional_qualifications?.items) {
          // Handle double-nested structure
          items = this.qualifications.professional_qualifications.items;
        } else if (this.qualifications.items) {
          items = this.qualifications.items;
        } else if (Array.isArray(this.qualifications)) {
          items = this.qualifications;
        }
        
        // Create a deep copy and format dates
        const formattedItems = JSON.parse(JSON.stringify(items || [])).map(qual => {
          // Handle both simple string format and object format
          if (typeof qual === 'string') {
            return {
              name: qual,
              issuer: '',
              date: '',
              expirationDate: '',
              current: false
            };
          }
          
          return {
            name: qual.name || '',
            issuer: qual.issuer || '',
            date: qual.date ? this.formatDateForInput(qual.date) : '',
            expirationDate: qual.expirationDate ? this.formatDateForInput(qual.expirationDate) : '',
            current: Boolean(qual.current)
          };
        });
        
        this.editableQualifications = { items: formattedItems };
      } catch (e) {
        console.error('Error initializing editable qualifications:', e);
        this.editableQualifications = { items: [] };
      }
    },
    formatDate(dateString) {
      if (!dateString || dateString === 'Present') return 'Present';
      const date = new Date(dateString);
      return isNaN(date) ? dateString : date.toLocaleDateString('en-US', {
        year: 'numeric',
        month: 'short'
      });
    },
    formatDateForInput(dateString) {
      if (!dateString || dateString === 'Present') return '';
      
      // Try to parse the date
      const date = new Date(dateString);
      if (isNaN(date)) return dateString;
      
      // Format as YYYY-MM for input[type="month"]
      const year = date.getFullYear();
      const month = String(date.getMonth() + 1).padStart(2, '0');
      return `${year}-${month}`;
    },
    addQualification() {
      this.editableQualifications.items.push({
        name: '',
        issuer: '',
        date: '',
        expirationDate: '',
        current: false
      });
    },
    removeQualification(index) {
      this.editableQualifications.items.splice(index, 1);
    },
    toggleEdit() {
      if (this.editing) {
        // Format qualification names in title case before saving
        this.editableQualifications.items.forEach(qual => {
          qual.name = this.toTitleCase(qual.name);
          qual.issuer = this.toTitleCase(qual.issuer);
        });
        this.$emit('save', this.editableQualifications);
      } else {
        this.$emit('toggle-edit');
      }
    },
    toTitleCase(str) {
      if (!str || typeof str !== 'string') return '';
      return str.split(' ')
        .map(word => word.charAt(0).toUpperCase() + word.slice(1).toLowerCase())
        .join(' ');
    }
  }
}
</script>