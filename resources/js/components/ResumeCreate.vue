<template>
  <div class="container mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <div class="max-w-4xl mx-auto">
      <Header />
      
      <MethodSelection 
        v-if="creationStep === 'method'" 
        @select-method="selectMethod"
      />
      
      <ResumeUpload 
        v-if="creationStep === 'upload'"
        :selectedFile="selectedFile"
        :isDragging="isDragging"
        :uploading="uploading"
        :uploadedResumeData="uploadedResumeData"
        :form="form"
        :formErrors="formErrors"
        :tailoring="tailoring"
        @update:selectedFile="selectedFile = $event"
        @update:isDragging="isDragging = $event"
        @update:uploading="uploading = $event"
        @update:uploadedResumeData="uploadedResumeData = $event"
        @handle-file-drop="handleFileDrop"
        @handle-file-change="handleFileChange"
        @upload-resume="uploadResume"
        @create-resume="createResumeFromUpload"
      />
      
      <ExistingResume
        v-if="creationStep === 'existing'"
        :resumes="resumes"
        :loadingResumes="loadingResumes"
        :selectedResumeId="selectedResumeId"
        :form="form"
        :creating="creating"
        @update:selectedResumeId="selectedResumeId = $event"
        @update:creationStep="creationStep = $event"
        @select-method="selectMethod"
        @proceed-with-resume="proceedWithExistingResume"
        @create-from-existing="createResumeFromExisting"
      />
      
      <CreateFromScratch
        v-if="creationStep === 'create'"
        :currentStep="currentStep"
        :totalSteps="totalSteps"
        :submitting="submitting"
        :draftResumeCreated="draftResumeCreated"
        :form="form"
        :generalDetails="generalDetails"
        :workExperience="workExperience"
        :education="education"
        :skills="skills"
        :newSkill="newSkill"
        :referencesText="referencesText"
        :professionalQualifications="professionalQualifications"
        :newQualification="newQualification"
        @update:currentStep="currentStep = $event"
        @update:totalSteps="totalSteps = $event"
        @update:submitting="submitting = $event"
        @update:draftResumeCreated="draftResumeCreated = $event"
        @update:draftResumeId="draftResumeId = $event"
        @update:newSkill="newSkill = $event"
        @update:referencesText="referencesText = $event"
        @update:newQualification="newQualification = $event"
        @next-step="nextStep"
        @prev-step="prevStep"
        @add-work-experience="addWorkExperience"
        @remove-work-experience="removeWorkExperience"
        @add-education="addEducation"
        @remove-education="removeEducation"
        @add-skill="addSkill"
        @remove-skill="removeSkill"
        @add-qualification="addQualification"
        @remove-qualification="removeQualification"
        @create-draft="createDraftResume"
        @submit-form="submitForm"
      />
    </div>
  </div>
</template>

<script>
import axios from 'axios';
import { debounce } from 'lodash';
import Header from './resume-create/Header.vue';
import MethodSelection from './resume-create/MethodSelection.vue';
import ResumeUpload from './resume-create/ResumeUpload.vue';
import ExistingResume from './resume-create/ExistingResume.vue';
import CreateFromScratch from './resume-create/CreateFromScratch.vue';
import { inject } from 'vue'; // Add this import

export default {
  components: {
    Header,
    MethodSelection,
    ResumeUpload,
    ExistingResume,
    CreateFromScratch
  },
  setup() {
    const showAlert = inject('showAlert'); // Inject the showAlert method from App.vue

    return {
      showAlert
    };
  },
  data() {
    return {
      creationStep: 'method',
      selectedFile: null,
      isDragging: false,
      uploading: false,
      uploadedResumeData: null,
      resumes: [],
      loadingResumes: false,
      selectedResumeId: null,
      professionalQualifications: [],
      newQualification: '',
      currentStep: 1,
      totalSteps: 4,
      submitting: false,
      tailoring: false,
      creating: false,
      draftResumeCreated: false,
      draftResumeId: null,
      form: {
        title: '',
        job_description: ''
      },
      generalDetails: {
        fullName: '',
        email: '',
        phone: '',
        location: '',
        summary: ''
      },
      workExperience: [],
      education: [],
      skills: [],
      newSkill: '',
      referencesText: '',
      formErrors: {
        title: '',
        job_description: ''
      }
    };
  },
  computed: {
    isStepValid() {
      switch (this.currentStep) {
        case 1:
          return this.form.title && 
                 this.generalDetails.fullName && 
                 this.generalDetails.email && 
                 this.generalDetails.phone && 
                 this.generalDetails.location && 
                 this.generalDetails.summary;
        case 2:
          if (this.workExperience.length === 0) return false;
          return this.workExperience.every(job => 
            job.title && 
            job.company && 
            job.startDate && 
            (job.current || job.endDate) && 
            job.description
          );
        case 3:
          if (this.education.length === 0) return false;
          return this.education.every(edu => 
            edu.institution && 
            edu.degree && 
            edu.startDate && 
            (edu.current || edu.endDate)
          );
        case 4:
          return this.skills.length > 0;
        case 5:
          return !!this.form.job_description;
        default:
          return true;
      }
    }
  },
  methods: {
    handleBeforeUnload(event) { 
      if (this.form.title || this.generalDetails.fullName) {
        event.preventDefault();
        event.returnValue = '';
        return '';
      }
    },
    handleFileChange(event) {
      const file = event.target.files[0];
      if (file) {
        if (file.type !== 'application/pdf') {
          this.showAlert({
            type: 'error',
            title: 'Invalid File Type',
            message: 'Please select a PDF file.',
            autoDismiss: true
          });
          event.target.value = '';
          return;
        }
        
        if (file.size > 10 * 1024 * 1024) { // 10MB
          this.showAlert({
            type: 'error',
            title: 'File Too Large',
            message: 'File size must be less than 10MB.',
            autoDismiss: true
          });
          event.target.value = '';
          return;
        }
        
        this.selectedFile = file;
      }
    },
    handleFileDrop(event) {
      this.isDragging = false;
      const file = event.dataTransfer.files[0];
      if (file && file.type === 'application/pdf') {
        this.selectedFile = file; 
      } else {
        this.showAlert({
          type: 'error',
          title: 'Invalid File Type',
          message: 'Please drop a PDF file.',
          autoDismiss: true
        });
      }
    },
    async uploadResume() {
      if (!this.selectedFile) return;

      this.uploading = true;
      const formData = new FormData();
      
      formData.append('resume_file', this.selectedFile, this.selectedFile.name);

      try {
        const response = await axios.post('/api/resumes/upload', formData, {
          headers: {
            'Authorization': `Bearer ${localStorage.getItem('auth_token')}`,
            'Content-Type': 'multipart/form-data'
          }
        });

        if (!this.form) this.form = {};
        this.uploadedResumeData = response.data.data;
        this.form.title = response.data.suggested_title ?? '';
      } catch (error) {
        if (error.response?.status === 422) {
          this.formErrors = error.response.data.errors;
        } else {
          console.error('Upload error:', error);
          this.showAlert({
            type: 'error',
            title: 'Upload Failed',
            message: 'There was an error uploading your resume. Please try again.',
            autoDismiss: true
          });
        }
      } finally {
        this.uploading = false;
      }
    }, 
    async extractTitleFromJobDescription() {
      if (!this.form.job_description || this.form.job_description.trim().length < 50) return;
      
      try {
        if (this.uploadedResumeData) {
          const response = await axios.post('/api/resumes/tailor', {
            resume_data: this.uploadedResumeData,
            job_description: this.form.job_description
          }, {
            headers: { 'Authorization': `Bearer ${localStorage.getItem('auth_token')}` }
          });
          
          if (response.data.suggested_title) {
            this.form.title = response.data.suggested_title; 
          }
        }
      } catch (error) {
        console.error('Error extracting title from job description:', error);
        this.extractTitleWithRegex();
      }
    },
    extractTitleWithRegex() {
      if (!this.form.job_description) return;
      
      const titleRegex = /(?:job title|position|role|title)(?:\s*:\s*|\s+is\s+)([^.,:;()\n]+)/i;
      const match = this.form.job_description.match(titleRegex);
      
      if (match && match[1]) {
        const jobTitle = match[1].trim();
        if (jobTitle.length > 0) {
          this.form.title = jobTitle; 
        }
      }
    },
    selectMethod(method) {
      this.creationStep = method;  
      if (method === 'existing') {
        this.fetchResumes();
      }
    },
    async createResumeFromUpload() {
      if (!this.form.title || !this.form.job_description || this.tailoring) return;

      this.tailoring = true;
      this.formErrors = { title: '', job_description: '' };

      try {
        const formData = new FormData();
        formData.append('resume_file', this.selectedFile);
        
        const uploadResponse = await axios.post('/api/resumes/upload', formData, {
          headers: {
            'Content-Type': 'multipart/form-data',
            'Authorization': `Bearer ${localStorage.getItem('auth_token')}`
          }
        });

        const tailoredResponse = await axios.post('/api/resumes/tailor', {
          resume_id: uploadResponse.data.data.id,
          job_description: this.form.job_description,
          title: this.form.title
        }, {
          headers: { 'Authorization': `Bearer ${localStorage.getItem('auth_token')}` }
        });

        this.$router.push(`/dashboard/resume/${tailoredResponse.data.resume_id}`);
        
        this.selectedFile = null;
        this.uploadedResumeData = null;
      } catch (error) {
        console.error('Error:', error.response?.data || error.message);
        this.showAlert({
          type: 'error',
          title: 'Resume Creation Failed',
          message: error.response?.data?.error || 'Failed to create resume.',
          autoDismiss: true
        });
      } finally {
        this.tailoring = false;
      }
    },
    validateAndFixResumeData(resumeData) {
      if (!resumeData) {
        console.error('Resume data is null or undefined');
        return;
      }

      console.log('Validating resume data structure:', resumeData);

      if (!resumeData.general_details || typeof resumeData.general_details !== 'object') {
        console.log('Creating empty general_details');
        resumeData.general_details = {};
      }

      const requiredFields = ['fullName', 'email', 'phone', 'location', 'summary'];
      requiredFields.forEach(field => {
        if (!resumeData.general_details[field]) {
          console.log(`Adding missing field in general_details: ${field}`);
          resumeData.general_details[field] = '';
        }
      });

      if (!Array.isArray(resumeData.work_experience)) {
        console.log('Creating empty work_experience array');
        resumeData.work_experience = [];
      }

      resumeData.work_experience.forEach((job, index) => {
        console.log(`Validating work experience ${index}:`, job);

        if (job.job_title && !job.title) job.title = job.job_title;
        if (job.start_date && !job.startDate) job.startDate = job.start_date;
        if (job.end_date && !job.endDate) job.endDate = job.end_date;
        if (Array.isArray(job.responsibilities) && !job.description) {
          job.description = job.responsibilities.join(' ');
        }
        if (job.current === undefined) job.current = job.endDate ? false : true;
      });

      if (!Array.isArray(resumeData.education)) {
        console.log('Creating empty education array');
        resumeData.education = [];
      }

      resumeData.education.forEach((edu, index) => {
        console.log(`Validating education ${index}:`, edu);
        if (edu.graduation_date && !edu.endDate) edu.endDate = edu.graduation_date;
        if (edu.current === undefined) edu.current = false;
        if (!edu.startDate) edu.startDate = '';
        if (!edu.description) edu.description = '';
      });

      if (!resumeData.skills || !resumeData.skills.items) {
        console.log('Creating empty skills.items array');
        resumeData.skills = { items: [] };
      }

      if (!resumeData.referees || !resumeData.referees.text) {
        console.log('Creating empty referees.text');
        resumeData.referees = { text: '' };
      }

      if (!resumeData.professional_qualifications || !resumeData.professional_qualifications.items) {
        console.log('Creating empty professional_qualifications.items array');
        resumeData.professional_qualifications = { items: [] };
      }

      console.log('Validated resume data:', resumeData);
    },
    async createResumeFromExisting() {
      if (!this.form.title || !this.form.job_description || !this.selectedResumeId || this.creating) return;

      this.creating = true;

      try {
        const response = await axios.post('/api/resumes/from-existing', {
          source_resume_id: this.selectedResumeId,
          job_description: this.form.job_description,
          title: this.form.title
        }, {
          headers: { 'Authorization': `Bearer ${localStorage.getItem('auth_token')}` }
        });

        this.$router.push(`/dashboard/resume/${response.data.resume_id}`);

        this.form.title = '';
        this.form.job_description = '';
        this.selectedResumeId = null;
        this.creationStep = 'method';
      } catch (error) {
        console.error('Error:', error.response?.data || error.message);
        this.showAlert({
          type: 'error',
          title: 'Resume Creation Failed',
          message: error.response?.data?.error || 'Failed to create resume.',
          autoDismiss: true
        });
      } finally {
        this.creating = false;
      }
    },
    async fetchResumes() {
      try {
        this.loadingResumes = true;
        const res = await axios.get('/api/resumes', {
          params: { status: 'completed' },
          headers: { 'Authorization': `Bearer ${localStorage.getItem('auth_token')}` }
        });
        this.resumes = res.data; 
      } catch (error) {
        console.error('Error fetching resumes:', error);
        this.showAlert({
          type: 'error',
          title: 'Fetch Error',
          message: 'Failed to fetch your resumes. Please try again.',
          autoDismiss: true
        });
      } finally {
        this.loadingResumes = false;
      }
    },
    async generateCoverLetter(resumeId) {
      try {
        const response = await axios.post('/api/resumes/cover-letter', {
          resume_id: resumeId,
          structured: true
        }, {
          headers: { Authorization: `Bearer ${localStorage.getItem('auth_token')}` }
        });
        
        if (response.data.cover_letter) {
          this.resume.cover_letter = response.data.cover_letter;
        }
      } catch (error) {
        console.error('Error generating cover letter:', error);
        this.showAlert({
          type: 'error',
          title: 'Cover Letter Error',
          message: 'Failed to generate cover letter. Please try again.',
          autoDismiss: true
        });
      }
    },
    proceedWithExistingResume() {
      if (!this.selectedResumeId) return;
      
      const selectedResume = this.resumes.find(r => r.id === this.selectedResumeId);
      if (selectedResume) {
        this.form.title = `${selectedResume.title} - Copy`;
      }
    },
    nextStep() {
      if (this.currentStep < this.totalSteps) {
        this.currentStep++; 
        window.scrollTo(0, 0);
      } else if (this.currentStep === this.totalSteps && !this.draftResumeCreated) {
        this.createDraftResume();
      }
    },
    prevStep() {
      if (this.currentStep > 1) {
        this.currentStep--; 
        window.scrollTo(0, 0);
      }
    },
    addWorkExperience() {
      this.workExperience.push({
        title: '',
        company: '',
        startDate: '',
        endDate: '',
        current: false,
        description: ''
      }); 
    },
    removeWorkExperience(index) {
      this.workExperience.splice(index, 1); 
    },
    addEducation() {
      this.education.push({
        institution: '',
        degree: '',
        startDate: '',
        endDate: '',
        current: false,
        description: ''
      }); 
    },
    removeEducation(index) {
      this.education.splice(index, 1); 
    },
    addSkill() {
      if (this.newSkill.trim()) {
        this.skills.push(this.newSkill.trim());
        this.newSkill = ''; 
      }
    },
    removeSkill(index) {
      this.skills.splice(index, 1); 
    },
    async createDraftResume() {
      this.submitting = true;
      
      try {
        const data = {
          title: this.form.title,
          general_details: JSON.stringify(this.generalDetails),
          work_experience: JSON.stringify(this.workExperience),
          education: JSON.stringify(this.education),
          referees: JSON.stringify({
            text: this.referencesText
          }),
          skills: JSON.stringify({
            items: this.skills
          })
        };
        
        const createResponse = await axios.post('/api/resumes', data, {
          headers: { 'Authorization': `Bearer ${localStorage.getItem('auth_token')}` }
        });
        
        this.draftResumeCreated = true;
        this.draftResumeId = createResponse.data.id;
        
        this.totalSteps = 5;
        this.currentStep = 5;
      } catch (error) {
        console.error('Error creating draft resume:', error);
        this.showAlert({
          type: 'error',
          title: 'Draft Creation Failed',
          message: 'There was an error creating your resume draft. Please try again.',
          autoDismiss: true
        });
      } finally {
        this.submitting = false;
      }
    },
    async submitForm() {
      if (!this.draftResumeCreated) {
        this.createDraftResume();
        return;
      }
      
      this.submitting = true;
      
      try {
        const resumeData = {
          general_details: this.generalDetails,
          work_experience: this.workExperience,
          education: this.education,
          skills: { items: this.skills },
          referees: { text: this.referencesText }
        };
        
        this.validateAndFixResumeData(resumeData);
        
        const tailorResponse = await axios.post('/api/resumes/tailor', {
          resume_id: this.draftResumeId,
          job_description: this.form.job_description,
          title: this.form.title
        }, {
          headers: { 'Authorization': `Bearer ${localStorage.getItem('auth_token')}` }
        });
        
        this.$router.push(`/dashboard/resume/${this.draftResumeId}`);
      } catch (error) {
        console.error('Error finalizing resume:', error);
        this.showAlert({
          type: 'error',
          title: 'Resume Finalization Failed',
          message: 'There was an error finalizing your resume. Please try again.',
          autoDismiss: true
        });
      } finally {
        this.submitting = false;
      }
    },
    validateForm() {
      let isValid = true;
      this.formErrors = {
        title: '',
        job_description: ''
      };
      
      if (!this.form.title.trim()) {
        this.formErrors.title = 'Please enter a resume title';
        isValid = false;
      }
      
      if (!this.form.job_description.trim()) {
        this.formErrors.job_description = 'Please enter a job description';
        isValid = false;
      }
      
      return isValid;
    },
    formatDate(dateString) {
      const options = { year: 'numeric', month: 'long', day: 'numeric' };
      return new Date(dateString).toLocaleDateString(undefined, options);
    },
    getStepValidationMessage() {
      switch (this.currentStep) {
        case 1:
          if (!this.form.title) return 'Please enter a resume title';
          if (!this.generalDetails.fullName) return 'Please enter your full name';
          if (!this.generalDetails.email) return 'Please enter your email';
          if (!this.generalDetails.phone) return 'Please enter your phone number';
          if (!this.generalDetails.location) return 'Please enter your location';
          if (!this.generalDetails.summary) return 'Please enter a professional summary';
          return 'Please complete all personal information fields';
        case 2:
          if (this.workExperience.length === 0) return 'Please add at least one work experience';
          return 'Please complete all work experience fields';
        case 3:
          if (this.education.length === 0) return 'Please add at least one education entry';
          return 'Please complete all education fields';
        case 4:
          if (this.skills.length === 0) return 'Please add at least one skill';
          return 'Please complete the skills section';
        case 5:
          if (!this.form.job_description) return 'Please enter a job description';
          return 'Please complete the job description';
        default:
          return 'Please complete all required fields';
      }
    },
    addQualification() {
      if (this.newQualification.trim()) {
        const [name, issuer, date] = this.newQualification.split('|');
        this.professionalQualifications.push({
          name: name.trim(),
          issuer: issuer.trim(),
          date: date.trim(),
          current: true,
        });
        this.newQualification = ''; 
      }
    },
    removeQualification(index) {
      this.professionalQualifications.splice(index, 1); 
    }
  },
  watch: {
    'form.job_description': debounce(function(newVal) {
      if (newVal && !this.form.title) {
        this.extractTitleFromJobDescription();
      }
    }, 500),
    'generalDetails.current': function(newVal) {
      if (newVal) {
        this.generalDetails.endDate = '';
      }
    },
    'creationStep': function(newVal) {
      if (newVal === 'method') {
        this.selectedFile = null;
        this.uploadedResumeData = null;
        this.selectedResumeId = null;
        this.form.title = '';
        this.form.job_description = '';
      }
    }
  },
  mounted() {
    window.addEventListener('beforeunload', this.handleBeforeUnload);
  },
  beforeDestroy() {
    window.removeEventListener('beforeunload', this.handleBeforeUnload);
  }
};
</script>

<style scoped>
/* Add any component-specific styles here */
</style>