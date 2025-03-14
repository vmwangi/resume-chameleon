<template>
  <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden mb-6">
    <div class="px-6 py-4 border-b border-gray-200 bg-gray-50 flex justify-between items-center">
      <h2 class="text-lg font-medium text-gray-900">Skills</h2>
      <div class="flex space-x-3">
        <button
          @click="$emit('regenerate', 'skills')"
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
      <div v-if="!editing" class="flex flex-wrap gap-2">
        <span
          v-for="(skill, index) in skills.items"
          :key="index"
          class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-emerald-100 text-emerald-800"
        >
          {{ skill }}
        </span>
        <p v-if="!skills || !skills.items || skills.items.length === 0" class="text-sm text-gray-500">No skills added yet</p>
      </div>
      <div v-else>
        <div class="flex flex-wrap gap-2 mb-4">
          <div
            v-for="(skill, index) in editableSkills.items"
            :key="index"
            class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-emerald-100 text-emerald-800"
          >
            {{ skill }}
            <button
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

        <div class="mt-1 flex rounded-md shadow-sm">
          <input
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
        <p class="mt-1 text-xs text-gray-500">Skills will be automatically formatted in sentence case (first letter capitalized)</p>
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
    skills: {
      type: Object,
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
      editableSkills: JSON.parse(JSON.stringify(this.skills)),
      newSkill: ''
    };
  },
  watch: {
    skills: {
      handler(newValue) {
        this.editableSkills = JSON.parse(JSON.stringify(newValue));
      },
      deep: true
    }
  },
  methods: {
    addSkill() {
      if (this.newSkill.trim()) {
        const formattedSkill = this.toSentenceCase(this.newSkill.trim());
        
        // Check for duplicates
        if (!this.editableSkills.items.includes(formattedSkill)) {
          this.editableSkills.items.push(formattedSkill);
        } else {
          alert('This skill already exists!');
        }
        
        this.newSkill = '';
      }
    },
    removeSkill(index) {
      this.editableSkills.items.splice(index, 1);
    },
    toSentenceCase(str) {
      if (!str || typeof str !== 'string') return '';
      return str.charAt(0).toUpperCase() + str.slice(1).toLowerCase();
    },
    toggleEdit() {
      if (this.editing) {
        this.$emit('save', this.editableSkills);
      } else {
        this.$emit('toggle-edit');
      }
    }
  }
}
</script>