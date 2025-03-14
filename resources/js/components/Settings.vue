<template>
  <div class="container mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <div class="max-w-3xl mx-auto">
      <header class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900">Account Settings</h1>
        <p class="mt-2 text-gray-600">Manage your profile and preferences</p>
      </header>

      <div v-if="loading && !cameFromInternalNavigation" class="flex justify-center py-12">
        <div class="animate-spin rounded-full h-12 w-12 border-t-2 border-b-2 border-emerald-500"></div>
      </div>
      
      <div v-else class="space-y-8">
        <!-- User Profile Section -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
          <div class="px-6 py-5 border-b border-gray-200 bg-gray-50">
            <h2 class="text-xl font-semibold text-gray-900">Profile Information</h2>
          </div>
          
          <div class="p-6">
            <form @submit.prevent="updateProfile" class="space-y-6">
              <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                <div>
                  <label for="name" class="block text-sm font-medium text-gray-700">Full Name</label>
                  <input 
                    id="name"
                    type="text" 
                    v-model="form.name" 
                    class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-emerald-500 focus:border-emerald-500 sm:text-sm"
                    placeholder="Your full name"
                  >
                </div>
                
                <div>
                  <label for="email" class="block text-sm font-medium text-gray-700">Email Address</label>
                  <input 
                    id="email"
                    type="email" 
                    v-model="form.email" 
                    class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 bg-gray-50 text-gray-500 cursor-not-allowed sm:text-sm" 
                    disabled
                  >
                  <p class="mt-1 text-xs text-gray-500">Email cannot be changed</p>
                </div>
              </div>
              
              <div class="flex justify-end" v-if="isNameChanged">
                <button 
                  type="button" 
                  class="mr-3 inline-flex items-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-emerald-500 transition-colors duration-200"
                  @click="resetForm"
                >
                  Cancel
                </button>
                <button 
                  type="submit" 
                  class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-emerald-600 hover:bg-emerald-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-emerald-500 transition-colors duration-200"
                  :disabled="updating"
                >
                  <svg v-if="updating" class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                  </svg>
                  {{ updating ? 'Saving...' : 'Save Changes' }}
                </button>
              </div>
            </form>
          </div>
        </div>
        
        <!-- Notification Preferences -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
          <div class="px-6 py-5 border-b border-gray-200 bg-gray-50">
            <h2 class="text-xl font-semibold text-gray-900">Notification Preferences</h2>
          </div>
          
          <div class="p-6">
            <form @submit.prevent="updateNotifications" class="space-y-6">
              <div class="space-y-4">
                <div class="flex items-start">
                  <div class="flex items-center h-5">
                    <input 
                      id="email_updates" 
                      type="checkbox" 
                      v-model="notifications.email_updates" 
                      class="focus:ring-emerald-500 h-4 w-4 text-emerald-600 border-gray-300 rounded"
                    >
                  </div>
                  <div class="ml-3 text-sm">
                    <label for="email_updates" class="font-medium text-gray-700">Email Updates</label>
                    <p class="text-gray-500">Receive emails about new features and improvements</p>
                  </div>
                </div>
                
                <div class="flex items-start">
                  <div class="flex items-center h-5">
                    <input 
                      id="job_alerts_tips" 
                      type="checkbox" 
                      v-model="notifications.job_alerts_tips" 
                      class="focus:ring-emerald-500 h-4 w-4 text-emerald-600 border-gray-300 rounded"
                    >
                  </div>
                  <div class="ml-3 text-sm">
                    <label for="job_alerts_tips" class="font-medium text-gray-700">Job Alerts & Resume Tips</label>
                    <p class="text-gray-500">Get notified about job opportunities and resume improvement suggestions</p>
                  </div>
                </div>
              </div>
              
              <div class="flex justify-end" v-if="areNotificationsChanged">
                <button 
                  type="submit" 
                  class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-emerald-600 hover:bg-emerald-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-emerald-500 transition-colors duration-200"
                  :disabled="updatingNotifications"
                >
                  <svg v-if="updatingNotifications" class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                  </svg>
                  {{ updatingNotifications ? 'Saving...' : 'Save Preferences' }}
                </button>
              </div>
            </form>
          </div>
        </div>
        
        <!-- Account Management -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
          <div class="px-6 py-5 border-b border-gray-200 bg-gray-50">
            <h2 class="text-xl font-semibold text-gray-900">Account Management</h2>
          </div>
          
          <div class="p-6">
            <div class="space-y-6">
              <div>
                <h3 class="text-lg font-medium text-gray-900">Delete Account</h3>
                <p class="mt-1 text-sm text-gray-500">
                  Once you delete your account, all of your data will be permanently removed. This action cannot be undone.
                </p>
                <div class="mt-4">
                  <button 
                    type="button" 
                    @click="confirmDeleteAccount"
                    class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-red-600 hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 transition-colors duration-200"
                  >
                    Delete Account
                  </button>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
      
      <!-- Success Notification -->
      <div 
        v-if="showSuccess" 
        class="fixed bottom-0 inset-x-0 pb-2 sm:pb-5"
      >
        <div class="max-w-md mx-auto px-2 sm:px-4">
          <div class="p-2 rounded-lg bg-emerald-600 shadow-lg sm:p-3">
            <div class="flex items-center justify-between flex-wrap">
              <div class="w-0 flex-1 flex items-center">
                <span class="flex p-2 rounded-lg bg-emerald-800">
                  <svg class="h-6 w-6 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                  </svg>
                </span>
                <p class="ml-3 font-medium text-white truncate">
                  <span>Changes saved successfully!</span>
                </p>
              </div>
              <div class="order-2 flex-shrink-0 sm:order-3 sm:ml-2">
                <button 
                  type="button" 
                  @click="showSuccess = false"
                  class="-mr-1 flex p-2 rounded-md hover:bg-emerald-500 focus:outline-none focus:ring-2 focus:ring-white"
                >
                  <span class="sr-only">Dismiss</span>
                  <svg class="h-6 w-6 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                  </svg>
                </button>
              </div>
            </div>
          </div>
        </div>
      </div>
      
      <!-- Delete Account Confirmation Modal -->
      <div v-if="showDeleteModal" class="fixed z-10 inset-0 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
          <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" aria-hidden="true" @click="showDeleteModal = false"></div>

          <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

          <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
            <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
              <div class="sm:flex sm:items-start">
                <div class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-red-100 sm:mx-0 sm:h-10 sm:w-10">
                  <svg class="h-6 w-6 text-red-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                  </svg>
                </div>
                <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left">
                  <h3 class="text-lg leading-6 font-medium text-gray-900" id="modal-title">Delete Account</h3>
                  <div class="mt-2">
                    <p class="text-sm text-gray-500">
                      Are you sure you want to delete your account? All of your data will be permanently removed.
                      This action cannot be undone.
                    </p>
                  </div>
                </div>
              </div>
            </div>
            <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
              <button 
                type="button" 
                class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-red-600 text-base font-medium text-white hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 sm:ml-3 sm:w-auto sm:text-sm"
                @click="deleteAccount"
                :disabled="deleting"
              >
                <svg v-if="deleting" class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                  <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                  <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
                {{ deleting ? 'Deleting...' : 'Delete Account' }}
              </button>
              <button 
                type="button" 
                class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm"
                @click="showDeleteModal = false"
              >
                Cancel
              </button>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import axios from 'axios';
import { useRoute } from 'vue-router';

export default {
  name: 'Settings', // Add name for keep-alive
  data() { 
    return { 
      form: {
        name: '',
        email: ''
      },
      notifications: {
        email_updates: false,
        job_alerts_tips: false
      },
      originalForm: {},
      originalNotifications: {},
      loading: true,
      updating: false,
      updatingNotifications: false,
      showSuccess: false,
      cameFromInternalNavigation: false,
      showDeleteModal: false,
      deleting: false
    }; 
  },
  computed: {
    isNameChanged() {
      return this.form.name !== this.originalForm.name;
    },
    areNotificationsChanged() {
      return this.notifications.email_updates !== this.originalNotifications.email_updates ||
             this.notifications.job_alerts_tips !== this.originalNotifications.job_alerts_tips;
    }
  },
  created() {
    // Check if we came from another authenticated page
    const route = useRoute();
    const previousRoute = this.$router.options.history.state.back;
    
    if (previousRoute && 
        (previousRoute.includes('/dashboard') || 
         previousRoute.includes('/resumes') || 
         previousRoute.includes('/settings'))) {
      this.cameFromInternalNavigation = true;
    }
    
    this.fetchUser();
  },
  activated() {
    // This is called when the component is activated from keep-alive cache
    // We can use this to refresh data without showing loading indicators
    if (this.$route.meta.requiresAuth) {
      this.cameFromInternalNavigation = true;
      this.fetchUser();
    }
  },
  methods: {
    async fetchUser() {
      this.loading = true;
      try {
        const res = await axios.get('/api/user', {
          headers: { Authorization: `Bearer ${localStorage.getItem('auth_token')}` }
        });
        this.form = {
          name: res.data.name || '',
          email: res.data.email || ''
        };
        this.originalForm = { ...this.form };
        
        // Fetch notification preferences if available
        if (res.data.notification_preferences) {
          this.notifications = {
            email_updates: res.data.notification_preferences.email_updates || false,
            job_alerts_tips: res.data.notification_preferences.job_alerts_tips || false
          };
        }
        this.originalNotifications = { ...this.notifications };
      } catch (error) {
        console.error('Error fetching user:', error);
        this.$router.push('/login');
      } finally {
        this.loading = false;
      }
    },
    resetForm() {
      this.form = { ...this.originalForm };
    },
    async updateProfile() {
      this.updating = true;
      try {
        await axios.put('/api/user', this.form, {
          headers: { Authorization: `Bearer ${localStorage.getItem('auth_token')}` }
        });
        this.originalForm = { ...this.form };
        this.showSuccessMessage();
      } catch (error) {
        console.error('Update error:', error);
        alert('Error updating profile details');
      } finally {
        this.updating = false;
      }
    },
    async updateNotifications() {
      this.updatingNotifications = true;
      try {
        await axios.put('/api/user/notifications', this.notifications, {
          headers: { Authorization: `Bearer ${localStorage.getItem('auth_token')}` }
        });
        this.originalNotifications = { ...this.notifications };
        this.showSuccessMessage();
      } catch (error) {
        console.error('Update notifications error:', error);
        alert('Error updating notification preferences');
      } finally {
        this.updatingNotifications = false;
      }
    },
    confirmDeleteAccount() {
      this.showDeleteModal = true;
    },
    async deleteAccount() {
      this.deleting = true;
      try {
        await axios.delete('/api/user', {
          headers: { Authorization: `Bearer ${localStorage.getItem('auth_token')}` }
        });
        localStorage.removeItem('auth_token');
        this.showDeleteModal = false;
        this.$router.push('/');
      } catch (error) {
        console.error('Delete account error:', error);
        alert('Error deleting account');
      } finally {
        this.deleting = false;
      }
    },
    showSuccessMessage() {
      this.showSuccess = true;
      setTimeout(() => {
        this.showSuccess = false;
      }, 5000);
    }
  }
};
</script>