<template>
  <div v-if="show" class="fixed inset-0 z-50 flex items-center justify-center">
    <!-- Backdrop -->
    <div class="fixed inset-0 bg-black bg-opacity-50 transition-opacity" @click="closeModal"></div>
    
    <!-- Modal -->
    <div class="bg-white rounded-lg shadow-xl overflow-hidden w-full max-w-md mx-4 z-10 transform transition-all">
      <!-- Header -->
      <div class="px-6 py-4 bg-gray-100 border-b flex justify-between items-center">
        <h3 class="text-lg font-medium" :class="typeClasses.title">{{ title }}</h3>
        <button @click="closeModal" class="text-gray-400 hover:text-gray-500 focus:outline-none">
          <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
          </svg>
        </button>
      </div>
      
      <!-- Body -->
      <div class="px-6 py-4">
        <div class="flex items-start">
          <!-- Icon -->
          <div v-if="type" class="flex-shrink-0 mr-3">
            <svg v-if="type === 'success'" class="h-6 w-6 text-green-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
            </svg>
            <svg v-else-if="type === 'error'" class="h-6 w-6 text-red-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
            </svg>
            <svg v-else-if="type === 'info'" class="h-6 w-6 text-blue-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
            <svg v-else-if="type === 'warning'" class="h-6 w-6 text-yellow-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
            </svg>
          </div>
          
          <!-- Message -->
          <div>
            <p class="text-gray-700">{{ message }}</p>
          </div>
        </div>
      </div>
      
      <!-- Footer -->
      <div class="px-6 py-3 bg-gray-50 border-t flex justify-end space-x-3">
        <button 
          v-if="isConfirmation"
          @click="cancel"
          class="px-4 py-2 rounded-md text-gray-700 bg-gray-200 hover:bg-gray-300 font-medium focus:outline-none"
        >
          No
        </button>
        <button 
          @click="confirm"
          class="px-4 py-2 rounded-md text-white font-medium focus:outline-none"
          :class="typeClasses.button"
        >
          {{ isConfirmation ? 'Yes' : buttonText }}
        </button>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  name: 'ModalAlert',
  props: {
    show: {
      type: Boolean,
      default: false
    },
    title: {
      type: String,
      default: 'Notification'
    },
    message: {
      type: String,
      required: true
    },
    type: {
      type: String,
      default: 'info',
      validator: (value) => ['success', 'error', 'info', 'warning'].includes(value)
    },
    buttonText: {
      type: String,
      default: 'OK'
    },
    autoDismiss: {
      type: Boolean,
      default: false
    },
    dismissTimeout: {
      type: Number,
      default: 3000
    },
    isConfirmation: {
      type: Boolean,
      default: false
    }
  },
  computed: {
    typeClasses() {
      const classes = {
        success: {
          title: 'text-green-700',
          button: 'bg-green-600 hover:bg-green-700'
        },
        error: {
          title: 'text-red-700',
          button: 'bg-red-600 hover:bg-red-700'
        },
        info: {
          title: 'text-blue-700',
          button: 'bg-blue-600 hover:bg-blue-700'
        },
        warning: {
          title: 'text-yellow-700',
          button: 'bg-yellow-600 hover:bg-yellow-700'
        }
      };
      return classes[this.type] || classes.info;
    }
  },
  methods: {
    closeModal() {
      this.$emit('close');
    },
    confirm() {
      if (this.isConfirmation) {
        this.$emit('confirm');
      }
      this.closeModal();
    },
    cancel() {
      this.closeModal();
    },
    startAutoDismissTimer() {
      if (this.autoDismiss && !this.isConfirmation) {
        setTimeout(() => {
          this.closeModal();
        }, this.dismissTimeout);
      }
    }
  },
  watch: {
    show(newVal) {
      if (newVal) {
        this.startAutoDismissTimer();
      }
    }
  },
  mounted() {
    if (this.show) {
      this.startAutoDismissTimer();
    }
  }
};
</script>