<template>
  <div v-if="show" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black bg-opacity-50">
    <div class="w-full max-w-md bg-white rounded-lg shadow-xl">
      <div class="p-6">
        <h2 class="text-2xl font-semibold text-gray-800 mb-4">
          Welcome to Resume Chameleon!
        </h2>
        <p class="text-gray-600 mb-6">
          Help us tailor our service to your needs by answering a few quick questions.
        </p> 
        <form @submit.prevent="submitOnboarding" class="space-y-4">
          <!-- Objective with the platform -->
          <div>
            <label for="objective" class="block text-sm font-medium text-gray-700 mb-1">
              What's your main objective with our platform? *
            </label>
            <select
              v-model="objective"
              id="objective"
              required
              class="w-full px-3 py-2 text-gray-700 border rounded-md focus:outline-none focus:ring-2 focus:ring-emerald-500"
            >
              <option value="">Select an objective</option>
              <option value="Create first resume">Create my first resume</option>
              <option value="Update existing resume">Update my existing resume</option>
              <option value="Create multiple versions">Create multiple versions for different jobs</option>
              <option value="Get better response rate">Get better response rate from applications</option>
              <option value="Other">Other (please specify)</option>
            </select>
          </div>

          <!-- Other Objective Input -->
          <div v-if="objective === 'Other'">
            <label for="otherObjective" class="block text-sm font-medium text-gray-700 mb-1">
              Please specify your objective *
            </label>
            <input
              v-model="otherObjective"
              id="otherObjective"
              type="text"
              required
              class="w-full px-3 py-2 text-gray-700 border rounded-md focus:outline-none focus:ring-2 focus:ring-emerald-500"
              placeholder="Please specify"
            >
          </div>

          <!-- Biggest challenge -->
          <div>
            <label for="challenge" class="block text-sm font-medium text-gray-700 mb-1">
              What's your biggest challenge when creating resumes? *
            </label>
            <select
              v-model="challenge"
              id="challenge"
              required
              class="w-full px-3 py-2 text-gray-700 border rounded-md focus:outline-none focus:ring-2 focus:ring-emerald-500"
            >
              <option value="">Select a challenge</option>
              <option value="Writing content">Writing effective content</option>
              <option value="Formatting">Formatting and design</option>
              <option value="Tailoring">Tailoring to specific job descriptions</option>
              <option value="ATS optimization">Getting past ATS systems</option>
              <option value="Time consuming">Too time consuming</option>
              <option value="Other">Other (please specify)</option>
            </select>
          </div>

          <!-- Other Challenge Input -->
          <div v-if="challenge === 'Other'">
            <label for="otherChallenge" class="block text-sm font-medium text-gray-700 mb-1">
              Please specify your challenge *
            </label>
            <input
              v-model="otherChallenge"
              id="otherChallenge"
              type="text"
              required
              class="w-full px-3 py-2 text-gray-700 border rounded-md focus:outline-none focus:ring-2 focus:ring-emerald-500"
              placeholder="Please specify"
            >
          </div>

          <!-- Where did you hear about us? -->
          <div>
            <label for="referralSource" class="block text-sm font-medium text-gray-700 mb-1">
              Where did you hear about us? *
            </label>
            <select
              v-model="referralSource"
              id="referralSource"
              required
              class="w-full px-3 py-2 text-gray-700 border rounded-md focus:outline-none focus:ring-2 focus:ring-emerald-500"
            >
              <option value="">Select an option</option>
              <option value="Google">Google Search</option>
              <option value="Social Media">Social Media</option>
              <option value="Friend">Friend or Colleague</option>
              <option value="Job Board">Job Board</option>
              <option value="Career Advisor">Career Advisor</option>
              <option value="Other">Other (please specify)</option>
            </select>
          </div>

          <!-- Other Referral Source Input -->
          <div v-if="referralSource === 'Other'">
            <label for="otherReferralSource" class="block text-sm font-medium text-gray-700 mb-1">
              Please specify where you heard about us *
            </label>
            <input
              v-model="otherReferralSource"
              id="otherReferralSource"
              type="text"
              required
              class="w-full px-3 py-2 text-gray-700 border rounded-md focus:outline-none focus:ring-2 focus:ring-emerald-500"
              placeholder="Please specify"
            >
          </div>

          <!-- Submit Button -->
          <div class="pt-4">
            <button
              type="submit"
              :disabled="!isFormComplete || isSubmitting"
              class="w-full px-4 py-2 text-white bg-emerald-600 rounded-md hover:bg-emerald-700 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:ring-offset-2 transition-colors disabled:opacity-50 disabled:cursor-not-allowed"
            >
              <span v-if="isSubmitting">Submitting...</span>
              <span v-else>Get Started!</span>
            </button>
          </div>
        </form>
      </div>
    </div>
  </div>
</template>

<script>
import { ref, computed, onMounted } from 'vue';
import axios from 'axios';

export default {
  name: 'OnboardingModal',
  emits: ['completed'],
  setup(props, { emit }) {
    const show = ref(false);
    const objective = ref('');
    const challenge = ref('');
    const referralSource = ref('');
    const otherObjective = ref('');
    const otherChallenge = ref('');
    const otherReferralSource = ref('');
    const isSubmitting = ref(false);

    // Computed property to check if all required fields are filled
    const isFormComplete = computed(() => {
      return (
        objective.value &&
        challenge.value &&
        referralSource.value &&
        (objective.value !== 'Other' || otherObjective.value) &&
        (challenge.value !== 'Other' || otherChallenge.value) &&
        (referralSource.value !== 'Other' || otherReferralSource.value)
      );
    });

    // Check if user has completed onboarding
    const checkOnboardingStatus = async () => {
      try {
        const response = await axios.get('/api/onboarding/check');
        show.value = !response.data.completed;
      } catch (error) {
        console.error('Error checking onboarding status:', error);
      }
    };

    // Submit onboarding form
    const submitOnboarding = async () => {
      if (!isFormComplete.value) return;
      
      isSubmitting.value = true;
      
      try {
        const finalObjective = objective.value === 'Other' ? otherObjective.value : objective.value;
        const finalChallenge = challenge.value === 'Other' ? otherChallenge.value : challenge.value;
        const finalReferralSource = referralSource.value === 'Other' ? otherReferralSource.value : referralSource.value;
        
        const response = await axios.post('/api/onboarding', {
          objective: finalObjective,
          challenge: finalChallenge,
          referral_source: finalReferralSource,
          custom_objective: objective.value === 'Other' ? otherObjective.value : null,
          custom_challenge: challenge.value === 'Other' ? otherChallenge.value : null,
          custom_referral_source: referralSource.value === 'Other' ? otherReferralSource.value : null,
        });

        if (response.status === 200) {
          show.value = false;
          emit('completed');
        }
      } catch (error) {
        console.error('Error submitting onboarding:', error);
      } finally {
        isSubmitting.value = false;
      }
    };

    onMounted(() => {
      checkOnboardingStatus();
    });

    return {
      show,
      objective,
      challenge,
      referralSource,
      otherObjective,
      otherChallenge,
      otherReferralSource,
      isFormComplete,
      isSubmitting,
      submitOnboarding
    };
  }
}
</script>