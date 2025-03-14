<template>
  <div class="mb-8">
    <div class="flex flex-col">
      <div class="flex items-center justify-between w-full">
        <div class="flex items-center flex-grow max-w-full">
          <h1 v-if="!editing" class="text-3xl font-bold text-gray-900 break-words">{{ title }}</h1>
          <div v-else class="flex items-center w-full max-w-[calc(100%)]">
            <input 
              type="text" 
              v-model="editableTitle" 
              class="text-3xl font-bold text-gray-900 border-b border-emerald-500 focus:outline-none bg-transparent w-full"
              @keydown.enter="saveTitle"
            />
            <button 
              @click="saveTitle"
              class="ml-2 flex-shrink-0 text-emerald-600 hover:text-emerald-700"
            >
              <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
              </svg>
            </button>
          </div>
          <button 
            v-if="!editing"
            @click="startEditingTitle"
            class="ml-2 flex-shrink-0 text-emerald-600 hover:text-emerald-700"
          >
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
              <path d="M13.586 3.586a2 2 0 112.828 2.828l-.793.793-2.828-2.828.793-.793zM11.379 5.793L3 14.172V17h2.828l8.38-8.379-2.83-2.828z" />
            </svg>
          </button>
        </div>
      </div>
      <p class="mt-1 text-gray-600">Last updated: {{ formatDate(updatedAt) }}</p>
      
      <!-- Action buttons directly below last updated date -->
      <div class="mt-4 flex flex-wrap gap-2">
        <button 
          @click="$emit('view-job-description')"
          class="inline-flex items-center px-3 py-1.5 rounded-md bg-emerald-50 text-emerald-700 hover:bg-emerald-100 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:ring-offset-2 transition-colors duration-200"
        >
          <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
          </svg>
          Job Description
        </button>
        <div class="relative group">
          <a 
            @click.prevent="downloadResume"
            class="inline-flex items-center px-3 py-1.5 rounded-md bg-emerald-600 text-white hover:bg-emerald-700 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:ring-offset-2 transition-colors duration-200 cursor-pointer"
          >
            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
            </svg>
            Resume
          </a>
          <div class="absolute bottom-full left-1/2 transform -translate-x-1/2 mb-2 px-3 py-1 bg-gray-900 text-white text-xs rounded opacity-0 group-hover:opacity-100 transition-opacity duration-200 whitespace-nowrap pointer-events-none">
            Download your resume as PDF
            <div class="absolute top-full left-1/2 transform -translate-x-1/2 border-4 border-transparent border-t-gray-900"></div>
          </div>
        </div>
        <div class="relative group">
          <a 
            @click.prevent="downloadCoverLetter"
            class="inline-flex items-center px-3 py-1.5 rounded-md bg-emerald-600 text-white hover:bg-emerald-700 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:ring-offset-2 transition-colors duration-200 cursor-pointer"
          >
            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
            </svg>
            Cover Letter
          </a>
          <div class="absolute bottom-full left-1/2 transform -translate-x-1/2 mb-2 px-3 py-1 bg-gray-900 text-white text-xs rounded opacity-0 group-hover:opacity-100 transition-opacity duration-200 whitespace-nowrap pointer-events-none">
            Download your cover letter as PDF
            <div class="absolute top-full left-1/2 transform -translate-x-1/2 border-4 border-transparent border-t-gray-900"></div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  props: {
    title: {
      type: String,
      required: true
    },
    updatedAt: {
      type: String,
      required: true
    },
    editing: {
      type: Boolean,
      default: false
    }
  },
  data() {
    return {
      editableTitle: this.title
    };
  },
  watch: {
    title(newValue) {
      this.editableTitle = newValue;
    }
  },
  methods: {
    startEditingTitle() {
      this.editableTitle = this.title;
      this.$emit('start-editing-title');
    },
    saveTitle() {
      if (!this.editableTitle.trim()) {
        return;
      }
      this.$emit('save-title', this.editableTitle);
    },
    formatDate(dateString) {
      if (!dateString || dateString === 'Present') return 'Present';
      const date = new Date(dateString);
      return isNaN(date) ? dateString : date.toLocaleDateString('en-US', {
        year: 'numeric',
        month: 'short',
        day: 'numeric'
      });
    },
    async downloadResume() {
      try {
        const token = localStorage.getItem('auth_token');
        const response = await axios.get(`/api/resumes/${this.$route.params.id}/download`, {
          headers: { Authorization: `Bearer ${token}` },
          responseType: 'blob'
        });

        const url = window.URL.createObjectURL(new Blob([response.data]));
        const link = document.createElement('a');
        link.href = url;

        // Extract the filename from the Content-Disposition header
        const disposition = response.headers['content-disposition'];
        const filename = disposition ? disposition.split('filename=')[1].trim().replace(/"/g, '') : 'resume.pdf';

        link.setAttribute('download', filename);
        document.body.appendChild(link);
        link.click();
        document.body.removeChild(link);
      } catch (error) {
        console.error('Error downloading resume:', error);
        alert('Failed to download resume. Please try again.');
      }
    },

    async downloadCoverLetter() {
      try {
        const token = localStorage.getItem('auth_token');
        const response = await axios.get(`/api/resumes/${this.$route.params.id}/download-cover-letter`, {
          headers: { Authorization: `Bearer ${token}` },
          responseType: 'blob'
        });

        const url = window.URL.createObjectURL(new Blob([response.data]));
        const link = document.createElement('a');
        link.href = url;

        // Extract the filename from the Content-Disposition header
        const disposition = response.headers['content-disposition'];
        const filename = disposition ? disposition.split('filename=')[1].trim().replace(/"/g, '') : 'cover_letter.pdf';

        link.setAttribute('download', filename);
        document.body.appendChild(link);
        link.click();
        document.body.removeChild(link);
      } catch (error) {
        console.error('Error downloading cover letter:', error);
        alert('Failed to download cover letter. Please try again.');
      }
    }
  }
}
</script>