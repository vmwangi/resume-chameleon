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
    
    <OnboardingModal v-if="isAuthenticated" @completed="onboardingCompleted" />
    
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

    <FeedbackModal ref="feedbackModal" @feedback-success="showFeedbackSuccessNotification" />

    <div
      v-if="showNotification"
      class="fixed bottom-20 right-5 bg-emerald-600 text-white p-3 rounded-md shadow-md transition-opacity duration-300"
    >
      {{ notificationMessage }}
    </div>

    <ModalAlert 
      :show="showModalAlert" 
      :title="modalAlertData.title" 
      :message="modalAlertData.message" 
      :type="modalAlertData.type"
      :autoDismiss="modalAlertData.autoDismiss"
      :isConfirmation="modalAlertData.isConfirmation"
      @close="showModalAlert = false"
      @confirm="handleConfirm" 
    </ModalAlert> 
  </div>
</template>

<script>
import NavBar from './components/NavBar.vue';
import Footer from './components/Footer.vue';
import FeedbackModal from './components/FeedbackModal.vue';
import OnboardingModal from './components/OnboardingModal.vue';
import ModalAlert from './components/ModalAlert.vue';
import { onMounted, ref, provide } from 'vue';

export default {
  components: { 
    NavBar, 
    Footer,
    FeedbackModal,
    OnboardingModal,
    ModalAlert
  },
  setup() {
    const isAuthenticated = ref(!!localStorage.getItem('auth_token'));
    const userData = ref(null);
    const userDataLoading = ref(false);
    const feedbackModal = ref(null);
    const showNotification = ref(false);
    const notificationMessage = ref('');
    const showModalAlert = ref(false);
    const modalAlertData = ref({
      type: 'info',
      title: '',
      message: '',
      autoDismiss: false,
      isConfirmation: false,
      onConfirm: null // Store the callback
    });

    provide('userData', userData);
    provide('userDataLoading', userDataLoading);

    const updateAuthState = (authState) => {
      isAuthenticated.value = authState;
    };

    const openFeedbackModal = () => {
      feedbackModal.value.openModal();
    };

    const showFeedbackSuccessNotification = () => {
      notificationMessage.value = 'Thank you for your feedback! We appreciate it.';
      showNotification.value = true;
      setTimeout(() => {
        showNotification.value = false;
      }, 3000);
    };

    const onboardingCompleted = () => {
      showNotification.value = true;
      notificationMessage.value = 'Thanks for completing the onboarding survey!';
      setTimeout(() => {
        showNotification.value = false;
      }, 3000);
    };

    const showAlert = (data) => {
      modalAlertData.value = {
        type: data.type || 'info',
        title: data.title || 'Notification',
        message: data.message || '',
        autoDismiss: data.autoDismiss !== undefined ? data.autoDismiss : true,
        isConfirmation: data.isConfirmation || false,
        onConfirm: data.onConfirm || null // Store the callback
      };
      showModalAlert.value = true;
    };

    const handleConfirm = () => {
      if (modalAlertData.value.onConfirm) {
        modalAlertData.value.onConfirm(); // Execute the stored callback
      }
    };

    provide('showAlert', showAlert);

    onMounted(() => {
      window.addEventListener('storage', () => {
        isAuthenticated.value = !!localStorage.getItem('auth_token');
      });
      window.addEventListener('login', () => {
        isAuthenticated.value = true;
      });
      window.addEventListener('logout', () => {
        isAuthenticated.value = false;
      });
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
      showAlert,
      handleConfirm
    };
  }
};
</script>