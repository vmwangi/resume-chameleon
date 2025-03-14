<template>
  <div>
    <NavBar @auth-changed="updateAuthState" />
    <div>
      <router-view v-slot="{ Component }">
        <keep-alive include="Dashboard,Resumes,Settings">
          <component :is="Component" />
        </keep-alive>
      </router-view>
    </div>
    <Footer :compact="isAuthenticated" />
    
    <!-- Onboarding Modal -->
    <OnboardingModal v-if="isAuthenticated" @completed="onboardingCompleted" />
    
    <!-- Feedback Button (Only show when logged in) -->
    <button
      v-if="isAuthenticated"
      @click="openFeedbackModal"
      class="fixed bottom-5 right-5 bg-emerald-600 text-white rounded-full w-12 h-12 flex items-center justify-center shadow-md hover:bg-emerald-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-emerald-500 transition-colors duration-200"
      aria-label="Provide feedback"
    >
      <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 8h10M7 12h4m1 8l-4-4H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-3l-4 4z" />
      </svg>
    </button>

    <!-- Feedback Modal -->
    <FeedbackModal ref="feedbackModal" @feedback-success="showFeedbackSuccessNotification" />

    <!-- Notification -->
    <div
      v-if="showNotification"
      class="fixed bottom-20 right-5 bg-emerald-600 text-white p-3 rounded-md shadow-md transition-opacity duration-300"
    >
      {{ notificationMessage }}
    </div>

    <!-- Add this to the template, before the closing </div> -->
    <ModalAlert 
      :show="showModalAlert" 
      :title="modalAlertData.title" 
      :message="modalAlertData.message" 
      :type="modalAlertData.type"
      :autoDismiss="modalAlertData.autoDismiss"
      @close="showModalAlert = false"
    />
  </div>
</template>

<script>
import NavBar from './components/NavBar.vue';
import Footer from './components/Footer.vue';
import FeedbackModal from './components/FeedbackModal.vue';
import OnboardingModal from './components/OnboardingModal.vue';
import { onMounted, ref, provide } from 'vue';
// Add to imports
import ModalAlert from './components/ModalAlert.vue';

export default {
  components: { 
    NavBar, 
    Footer,
    FeedbackModal,
    OnboardingModal,
    ModalAlert
  },
  setup() {
    // Use ref instead of computed to allow manual updates
    const isAuthenticated = ref(!!localStorage.getItem('auth_token'));
    
    // Global state for user data
    const userData = ref(null);
    const userDataLoading = ref(false);
    
    // Feedback modal and notification refs
    const feedbackModal = ref(null);
    const showNotification = ref(false);
    const notificationMessage = ref('');

    // Add to setup()
    const showModalAlert = ref(false);
    const modalAlertData = ref({
      type: 'info',
      title: '',
      message: '',
      autoDismiss: false
    });
    
    // Provide user data to child components
    provide('userData', userData);
    provide('userDataLoading', userDataLoading);
    
    // Method to update auth state from NavBar
    const updateAuthState = (authState) => {
      isAuthenticated.value = authState;
    };
    
    // Method to open feedback modal
    const openFeedbackModal = () => {
      feedbackModal.value.openModal();
    };
    
    // Method to show success notification
    const showFeedbackSuccessNotification = () => {
      notificationMessage.value = 'Thank you for your feedback! We appreciate it.';
      showNotification.value = true;
      setTimeout(() => {
        showNotification.value = false;
      }, 3000);
    };
    
    // Method to handle onboarding completion
    const onboardingCompleted = () => {
      showNotification.value = true;
      notificationMessage.value = 'Thanks for completing the onboarding survey!';
      setTimeout(() => {
        showNotification.value = false;
      }, 3000);
    };

    // Add this method to setup()
    const showAlert = (data) => {
      modalAlertData.value = {
        type: data.type || 'info',
        title: data.title || 'Notification',
        message: data.message || '',
        autoDismiss: data.autoDismiss !== undefined ? data.autoDismiss : true
      };
      showModalAlert.value = true;
    };

    // Add to provide
    provide('showAlert', showAlert);
    
    onMounted(() => {
      // Listen for storage events from other tabs
      window.addEventListener('storage', () => {
        isAuthenticated.value = !!localStorage.getItem('auth_token');
      });
      
      // Listen for custom auth events
      window.addEventListener('login', () => {
        isAuthenticated.value = true;
      });
      
      window.addEventListener('logout', () => {
        isAuthenticated.value = false;
      });
      
      // Check auth state on mount
      isAuthenticated.value = !!localStorage.getItem('auth_token');
    });
    
    return {
      isAuthenticated,
      updateAuthState,
      feedbackModal,
      openFeedbackModal,
      showFeedbackSuccessNotification,
      showNotification,
      notificationMessage,
      onboardingCompleted, 
      showModalAlert,
      modalAlertData,
      showAlert
    };
  }
}
</script>