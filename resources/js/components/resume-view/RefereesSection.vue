<template>
  <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden mb-6">
    <div class="px-6 py-4 border-b border-gray-200 bg-gray-50 flex justify-between items-center">
      <h2 class="text-lg font-medium text-gray-900">Referees</h2>
      <div class="flex space-x-3">
        <button
          @click="$emit('regenerate', 'referees')"
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
        <div v-for="(referee, index) in referees" :key="index" class="mb-6 last:mb-0">
          <h3 class="text-base font-medium text-gray-900">{{ referee.name }}</h3>
          <p class="text-sm font-medium text-emerald-600">{{ referee.position }} at {{ referee.company }}</p>
          <p class="mt-1 text-sm text-gray-800">Contact: {{ formatContact(referee.contact) }}</p>
          <p class="mt-1 text-sm text-gray-800">Relationship: {{ referee.relationship }}</p>
        </div>
        <p v-if="!referees.length" class="text-sm text-gray-500">No referees added yet</p>
      </div>
      <div v-else>
        <div v-for="(referee, index) in editableReferees" :key="index" class="mb-6 border border-gray-200 rounded-md p-4">
          <div class="flex justify-between items-start mb-4">
            <h3 class="text-sm font-medium text-gray-900">Referee {{ index + 1 }}</h3>
            <button
              @click="removeReferee(index)"
              class="text-red-600 hover:text-red-800 text-sm"
            >
              Remove
            </button>
          </div>

          <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
            <div>
              <label class="block text-xs font-medium text-gray-500">Name</label>
              <input
                type="text"
                v-model="referee.name"
                class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-emerald-500 focus:border-emerald-500 sm:text-sm"
              >
            </div>

            <div>
              <label class="block text-xs font-medium text-gray-500">Position</label>
              <input
                type="text"
                v-model="referee.position"
                class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-emerald-500 focus:border-emerald-500 sm:text-sm"
              >
            </div>

            <div>
              <label class="block text-xs font-medium text-gray-500">Company</label>
              <input
                type="text"
                v-model="referee.company"
                class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-emerald-500 focus:border-emerald-500 sm:text-sm"
              >
            </div>

            <div>
              <label class="block text-xs font-medium text-gray-500">Contact</label>
              <input
                type="text"
                v-model="referee.contact"
                class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-emerald-500 focus:border-emerald-500 sm:text-sm"
              >
            </div>

            <div class="sm:col-span-2">
              <label class="block text-xs font-medium text-gray-500">Relationship</label>
              <input
                type="text"
                v-model="referee.relationship"
                class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-emerald-500 focus:border-emerald-500 sm:text-sm"
              >
            </div>
          </div>
        </div>

        <button
          type="button"
          @click="addReferee"
          class="mt-4 inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-emerald-700 bg-emerald-100 hover:bg-emerald-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-emerald-500 transition-colors duration-200"
        >
          <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-1" viewBox="0 0 20 20" fill="currentColor">
            <path fill-rule="evenodd" d="M10 3a1 1 0 011 1v5h5a1 1 0 110 2h-5v5a1 1 0 11-2 0v-5H4a1 1 0 110-2h5V4a1 1 0 011-1z" clip-rule="evenodd" />
          </svg>
          Add Referee
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
    referees: {
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
      editableReferees: []
    };
  },
  watch: {
    referees: {
      handler(newValue) {
        this.editableReferees = JSON.parse(JSON.stringify(newValue));
      },
      deep: true
    }
  },
  created() {
    this.editableReferees = JSON.parse(JSON.stringify(this.referees));
  },
  methods: {
    formatContact(contact) {
      if (!contact) return '';
      const phone = contact.replace(/\D/g, '');
      if (phone.length === 10) {
        return `(${phone.slice(0,3)}) ${phone.slice(3,6)}-${phone.slice(6)}`;
      }
      return contact;
    },
    addReferee() {
      this.editableReferees.push({
        name: '',
        position: '',
        company: '',
        contact: '',
        relationship: ''
      });
    },
    removeReferee(index) {
      this.editableReferees.splice(index, 1);
    },
    toggleEdit() {
      if (this.editing) {
        this.$emit('save', this.editableReferees);
      } else {
        this.$emit('toggle-edit');
      }
    }
  }
}
</script>