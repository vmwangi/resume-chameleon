<template>
  <div v-if="isOpen" class="fixed inset-0 z-50 flex items-center justify-center">
    <!-- Backdrop -->
    <div class="fixed inset-0 bg-black bg-opacity-50" @click="closeModal"></div>
    
    <!-- Modal -->
    <div class="bg-white rounded-xl shadow-md w-full max-w-md mx-4 z-10">
      <div class="p-6">
        <h2 class="text-2xl font-bold text-gray-900 mb-4">Share Your Feedback</h2>
        
        <div v-if="submitted" class="py-6">
          <div class="text-emerald-600 mb-6">
            <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-emerald-100 text-emerald-600 mb-4">
              <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
              </svg>
            </div>
            <p class="text-center text-lg font-medium">Thank you for your feedback!</p>
            <p class="text-center text-gray-600 mt-1">We appreciate your input to help improve Resume Chameleon.</p>
          </div>
          <button 
            @click="closeModal" 
            class="w-full py-3 px-4 bg-emerald-600 hover:bg-emerald-700 text-white font-medium rounded-md transition-colors duration-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-emerald-500"
          >
            Close
          </button>
        </div>
        
        <form v-else @submit.prevent="submitFeedback" class="space-y-6">
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2" for="feedback">
              How can we improve Resume Chameleon?
            </label>
            <textarea 
              id="feedback" 
              v-model="feedbackText" 
              class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500"
              rows="4"
              placeholder="Share your thoughts, suggestions, or report issues..."
              required
            ></textarea>
          </div>
          
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">
              How likely are you to recommend Resume Chameleon to a friend?
            </label>
            <div class="flex justify-between text-xs text-gray-500 mb-2">
              <span>Not likely</span>
              <span>Very likely</span>
            </div>
            <div class="flex justify-between">
              <template v-for="n in 11" :key="n">
                <button 
                  type="button"
                  @click="score = n - 1" 
                  class="w-8 h-8 rounded-full flex items-center justify-center focus:outline-none focus:ring-2 focus:ring-emerald-500 transition-colors duration-200"
                  :class="score === n - 1 ? 'bg-emerald-600 text-white' : 'bg-gray-100 hover:bg-gray-200 text-gray-700'"
                >
                  {{ n - 1 }}
                </button>
              </template>
            </div>
          </div>
          
          <div class="flex justify-end space-x-3 pt-2">
            <button 
              type="button" 
              @click="closeModal" 
              class="py-2 px-4 border border-gray-300 rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-emerald-500 transition-colors duration-200"
            >
              Cancel
            </button>
            <button 
              type="submit" 
              class="py-2 px-4 bg-emerald-600 hover:bg-emerald-700 text-white font-medium rounded-md transition-colors duration-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-emerald-500"
              :disabled="isSubmitting || !feedbackText || score === null"
            >
              {{ isSubmitting ? 'Submitting...' : 'Submit Feedback' }}
            </button>
          </div>
        </form>
      </div>
    </div>
  </div>
</template>

<script>
import axios from 'axios';

export default {
  name: 'FeedbackModal',
  data() {
    return {
      isOpen: false,
      feedbackText: '',
      score: null,
      isSubmitting: false,
      submitted: false
    };
  },
  methods: {
    openModal() {
      this.isOpen = true;
      this.feedbackText = '';
      this.score = null;
      this.submitted = false;
    },
    closeModal() {
      this.isOpen = false;
      if (this.submitted) {
        this.$emit('feedback-success');
      }
    },
    async submitFeedback() {
      if (!this.feedbackText || this.score === null) {
        return;
      }
      
      this.isSubmitting = true;
      
      try {
        await axios.post('/api/feedback', {
          text: this.feedbackText,
          score: this.score
        });
        
        this.submitted = true;
      } catch (error) {
        console.error('Error submitting feedback:', error);
        alert('There was an error submitting your feedback. Please try again.');
      } finally {
        this.isSubmitting = false;
      }
    }
  }
};
</script>