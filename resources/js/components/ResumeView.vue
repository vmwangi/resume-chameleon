<template>
<div class="container mx-auto px-4 sm:px-6 lg:px-8 py-8">
  <!-- Loading indicator -->
  <div v-if="pageLoading" class="flex justify-center items-center h-64">
    <div class="animate-spin rounded-full h-12 w-12 border-t-2 border-b-2 border-emerald-500"></div>
    <p class="ml-3 text-lg text-gray-600">Loading resume...</p>
  </div>
  
  <div v-else class="max-w-5xl mx-auto">
    <!-- Header with title and action buttons -->
    <ResumeHeader 
      :title="resume.title" 
      :updated-at="resume.updated_at"
      :editing="editing.title"
      @start-editing-title="startEditingTitle"
      @save-title="saveTitle"
      @view-job-description="viewJobDescription"
      @download-resume="downloadResume"
      @download-cover-letter="downloadCoverLetter"
    />

    <!-- Disclaimer -->
    <DisclaimerSection />

    <!-- Regeneration limit warning -->
    <RegenerationErrorAlert :error="regenerationError" />

    <!-- Personal Information Section -->
    <PersonalInfoSection 
      :personal-info="parsedResume.general_details"
      :editing="editing.personalInfo"
      :regenerating="regenerating.general_details"
      :regeneration-limit="regenerationLimits.general_details"
      @regenerate="regenerateSection"
      @toggle-edit="toggleEdit('personalInfo')"
      @cancel-edit="cancelEdit('personalInfo')"
      @save="savePersonalInfo"
    />

    <!-- Work Experience Section -->  
    <WorkExperienceSection 
      :work-experience="parsedResume.work_experience"
      :editing="editing.workExperience"
      :regenerating="regenerating.work_experience"
      :regeneration-limit="regenerationLimits.work_experience"
      @regenerate="regenerateSection"
      @toggle-edit="toggleEdit('workExperience')"
      @cancel-edit="cancelEdit('workExperience')"
      @save="saveWorkExperience"
    />

    <!-- Education Section --> 
    <EducationSection 
      :education="parsedResume.education"
      :editing="editing.education"
      :regenerating="regenerating.education"
      :regeneration-limit="regenerationLimits.education"
      @regenerate="regenerateSection"
      @toggle-edit="toggleEdit('education')"
      @cancel-edit="cancelEdit('education')"
      @save="saveEducation"
    />

    <!-- Professional Qualifications Section -->  
    <ProfessionalQualificationsSection 
      :qualifications="parsedResume.professional_qualifications"
      :editing="editing.professionalQualifications"
      :regenerating="regenerating.professional_qualifications"
      :regeneration-limit="regenerationLimits.professional_qualifications"
      @regenerate="regenerateSection"
      @toggle-edit="toggleEdit('professionalQualifications')"
      @cancel-edit="cancelEdit('professionalQualifications')"
      @save="saveProfessionalQualifications"
    />

    <!-- Skills Section --> 
    <SkillsSection 
      :skills="parsedResume.skills"
      :editing="editing.skills"
      :regenerating="regenerating.skills"
      :regeneration-limit="regenerationLimits.skills"
      @regenerate="regenerateSection"
      @toggle-edit="toggleEdit('skills')"
      @cancel-edit="cancelEdit('skills')"
      @save="saveSkills"
    /> 

    <!-- Referees Section --> 
    <RefereesSection 
      :referees="parsedResume.referees"
      :editing="editing.referees"
      :regenerating="regenerating.referees"
      :regeneration-limit="regenerationLimits.referees"
      @regenerate="regenerateSection"
      @toggle-edit="toggleEdit('referees')"
      @cancel-edit="cancelEdit('referees')"
      @save="saveReferees"
    />

    <!-- Cover Letter Section - Redesigned -->
    <CoverLetterSection 
      :cover-letter-data="coverLetterData"
      :editing="editing.coverLetter"
      @toggle-edit="toggleEdit('coverLetter')"
      @cancel-edit="cancelEdit('coverLetter')"
      @save="saveCoverLetter"
      @view-cover-letter="viewCoverLetter"
    />

    <!-- Job Description Modal -->
    <JobDescriptionModal 
      :show="showJobDescriptionModal"
      :job-description="resume.job_description"
      @close="showJobDescriptionModal = false"
    />

    <!-- Cover Letter Modal -->
    <CoverLetterModal 
      :show="showCoverLetterModal"
      :cover-letter-data="coverLetterData"
      :raw-cover-letter="resume.cover_letter"
      @close="showCoverLetterModal = false"
      @copy="copyCoverLetter"
    />

    <!-- Success Notification -->
    <div 
      v-if="showSuccessNotification" 
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
                <span>{{ successMessage }}</span>
              </p>
            </div>
            <div class="order-2 flex-shrink-0 sm:order-3 sm:ml-2">
              <button 
                type="button" 
                @click="showSuccessNotification = false"
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
  </div>
</div>
</template>

<script>
import axios from 'axios';
import ResumeHeader from './resume-view/ResumeHeader.vue';
import DisclaimerSection from './resume-view/DisclaimerSection.vue';
import RegenerationErrorAlert from './resume-view/RegenerationErrorAlert.vue';
import PersonalInfoSection from './resume-view/PersonalInfoSection.vue';
import WorkExperienceSection from './resume-view/WorkExperienceSection.vue';
import EducationSection from './resume-view/EducationSection.vue';
import SkillsSection from './resume-view/SkillsSection.vue';
import ProfessionalQualificationsSection from './resume-view/ProfessionalQualificationsSection.vue';
import RefereesSection from './resume-view/RefereesSection.vue';
import CoverLetterSection from './resume-view/CoverLetterSection.vue';
import JobDescriptionModal from './resume-view/JobDescriptionModal.vue';
import CoverLetterModal from './resume-view/CoverLetterModal.vue';

export default {
  components: {
    ResumeHeader,
    DisclaimerSection,
    RegenerationErrorAlert,
    PersonalInfoSection,
    WorkExperienceSection,
    EducationSection,
    SkillsSection,
    ProfessionalQualificationsSection,
    RefereesSection,
    CoverLetterSection,
    JobDescriptionModal,
    CoverLetterModal
  },
  data() {
    return {
      resume: {
        title: '',
        general_details: {},
        work_experience: [],
        education: [],
        skills: { items: [] }, 
        referees: { text: '' },
        job_description: '',
        cover_letter: '',
        updated_at: ''
      },
      parsedResume: {
        general_details: {},
        work_experience: [],
        education: [],
        skills: { items: [] }, 
        referees: [],
        professionalQualifications: { items: [] }
      },
      editableResume: {
        general_details: {},
        work_experience: [],
        education: [],
        skills: { items: [] }, 
        referees: [],
        professionalQualifications: { items: [] }
      },
      editing: {
        title: false,
        personalInfo: false,
        workExperience: false,
        education: false,
        skills: false,
        professionalQualifications: false,
        referees: false,
        coverLetter: false
      },
      editableTitle: '',
      showJobDescriptionModal: false,
      showCoverLetterModal: false,
      newSkill: '',
      newInterest: '',
      editableCoverLetter: '',
      coverLetterData: {
        applicant: {
          name: '',
          address: '',
          email: '',
          phone: ''
        },
        date: '',
        company: {
          name: '',
          address: ''
        },
        subject: '',
        greeting: '',
        introduction: '',
        background: '',
        skillsAlignment: '',
        closing: ''
      },
      pageLoading: true,
      regenerating: {
        general_details: false,
        work_experience: false,
        education: false,
        skills: false,
        professional_qualifications: false,
        referees: false
      },
      regenerationLimits: {
        general_details: 0,
        work_experience: 0,
        education: 0,
        skills: 0,
        professional_qualifications: 0,
        referees: 0
      },
      regenerationError: null,
      backupData: {}, // For storing original data when editing
      showSuccessNotification: false,
      successMessage: ''
    };
  },
  mounted() {
    this.fetchResume();
    this.fetchRegenerationLimits();
  },
  methods: {
    // Parse field method
    parseField(field) {
      if (!field) return null;

      try {
        let parsedData = typeof field === 'string' ? JSON.parse(field) : field;

        if (typeof field === 'string') {
          try {
            parsedData = JSON.parse(field);
          } catch (e) {
            return field;
          }
        } else {
          parsedData = field;
        }

        // Handle work_experience field
        if (field === this.resume.work_experience) { 
          return parsedData.map(job => ({
            title: job.title || '',
            company: job.company || '',
            location: job.location || '',
            startDate: job.startDate || job.start_date || '',
            endDate: job.endDate || job.end_date || '',
            current: job.current || (job.endDate === 'Present' || job.end_date === 'Present'),
            description: job.description || job.responsibilities || ''
          })); 
        }

        // Handle education field
        if (field === this.resume.education) {
          if (Array.isArray(parsedData)) {
            return parsedData.map(edu => ({
              institution: edu.institution || '',
              degree: edu.degree || '',
              startDate: edu.startDate || edu.start_date || '',
              endDate: edu.endDate || edu.end_date || '',
              current: edu.current || (edu.endDate === 'Present' || edu.end_date === 'Present'),
              description: edu.description || ''
            }));
          }
        }

        // Handle skills field
        if (field === this.resume.skills) {
          return {
            items: parsedData.items || []
          };
        }

        // Handle professional_qualifications field
        if (field === this.resume.professional_qualifications) {
          // Handle string format (could be JSON string)
          if (typeof parsedData === 'string') {
            try {
              parsedData = JSON.parse(parsedData);
            } catch (e) {
              // If it's not valid JSON, treat as a simple string qualification
              return { items: [{ name: parsedData }] };
            }
          }
          
          // Handle nested structure
          if (parsedData && parsedData.professional_qualifications && parsedData.professional_qualifications.items) {
            return { 
              items: parsedData.professional_qualifications.items.map(item => 
                typeof item === 'string' ? { name: item } : item
              ) 
            };
          }
          
          // Handle array format
          if (Array.isArray(parsedData)) {
            return { 
              items: parsedData.map(item => 
                typeof item === 'string' ? { name: item } : item
              ) 
            };
          }
          
          // Handle object with items
          if (parsedData && parsedData.items) {
            return { 
              items: parsedData.items.map(item => 
                typeof item === 'string' ? { name: item } : item
              ) 
            };
          }
          
          return { items: [] };
        }

        // Handle referees field - updated section
        if (field === this.resume.referees) {
          // Handle both new and legacy formats
          if (parsedData.text) {
            try {
              // Parse nested JSON string
              const textParsed = JSON.parse(parsedData.text);
              return Array.isArray(textParsed) ? textParsed : [];
            } catch (e) {
              console.error('Error parsing referees text:', e, parsedData.text);
              return [];
            }
          }
          // Legacy array format
          if (Array.isArray(parsedData)) {
            return parsedData;
          }
          return [];
        }

        return parsedData;
      } catch (e) {
        console.error('Error parsing field:', e, field);
        // Return appropriate defaults
        if (field === this.resume.skills || field === this.resume.professional_qualifications) {
          return { items: [] };
        }
        if (field === this.resume.referees) {
          return [];
        }
        if (field === this.resume.work_experience || field === this.resume.education) {
          return [];
        }
        if (field === this.resume.general_details) {
          return {};
        }
        return field;
      }
    },

    // Format date for input[type="month"]
    formatDateForInput(dateString) {
      if (!dateString || dateString === 'Present') return '';
      
      // Try to parse the date
      const date = new Date(dateString);
      if (isNaN(date)) return dateString;
      
      // Format as YYYY-MM for input[type="month"]
      const year = date.getFullYear();
      const month = String(date.getMonth() + 1).padStart(2, '0');
      return `${year}-${month}`;
    },

    // Updated fetchResume method to handle 404 redirect
    async fetchResume() {
      this.pageLoading = true;
      try {
        const id = this.$route.params.id;
        const response = await axios.get(`/api/resumes/${id}`);
        
        // Store raw resume data
        this.resume = response.data;

        // Parse all fields with nested structure handling
        this.parsedResume = this.parseResumeData(response.data);

        // Create editable copy with proper structure
        this.editableResume = {
          general_details: { ...this.parsedResume.general_details },
          work_experience: [...(this.parsedResume.work_experience || [])].map(we => ({
            title: we.title || '',
            company: we.company || '',
            startDate: we.startDate || '',
            endDate: we.endDate || '',
            current: we.current || false,
            description: Array.isArray(we.description) ? [...we.description] : [we.description || '']
          })),
          education: [...(this.parsedResume.education || [])].map(edu => ({
            degree: edu.degree || '',
            institution: edu.institution || '',
            startDate: edu.startDate || '',
            endDate: edu.endDate || '',
            current: edu.current || false,
            description: edu.description || ''
          })),
          skills: {
            items: [...(this.parsedResume.skills?.items || [])]
          },
          professional_qualifications: {
            items: [...(this.parsedResume.professional_qualifications?.items || [])]
          },
          referees: [...(this.parsedResume.referees || [])].map(ref => ({
            name: ref.name || '',
            position: ref.position || '',
            company: ref.company || '',
            contact: ref.contact || '',
            relationship: ref.relationship || ''
          }))
        };

        // Handle cover letter parsing
        if (this.resume.cover_letter) {
          try {
            this.coverLetterData = typeof this.resume.cover_letter === 'string' 
              ? JSON.parse(this.resume.cover_letter)
              : this.resume.cover_letter;
          } catch (e) {
            console.error('Error parsing cover letter:', e);
            this.coverLetterData = this.getDefaultCoverLetterData();
          }
        } else {
          // If no cover letter exists, generate one
          this.generateCoverLetter(this.resume.id);
        }
      } catch (error) {
        console.error('Error fetching resume:', error);
        // Check if the error is a 404 from the API
        if (error.response?.status === 404 && 
            error.response?.data?.error === "Resume not found or does not belong to the user") {
          // Redirect to 404 page
          this.$router.push('/404');
        }
      } finally {
        this.pageLoading = false;
      }
    },

    // Add this method to generate a cover letter if one doesn't exist
    async generateCoverLetter(resumeId) {
      try {
        const response = await axios.post('/api/resumes/cover-letter', {
          resume_id: resumeId,
          structured: true
        }, {
          headers: { Authorization: `Bearer ${localStorage.getItem('auth_token')}` }
        });
        
        if (response.data.cover_letter) {
          this.coverLetterData = response.data.cover_letter;
          this.resume.cover_letter = JSON.stringify(response.data.cover_letter);
        } else {
          this.coverLetterData = this.getDefaultCoverLetterData();
        }
      } catch (error) {
        console.error('Error generating cover letter:', error);
        this.coverLetterData = this.getDefaultCoverLetterData();
      }
    },

    getDefaultCoverLetterData() {
      return {
        applicant: {
          name: this.parsedResume.general_details.fullName || '',
          address: this.parsedResume.general_details.location || '',
          email: this.parsedResume.general_details.email || '',
          phone: this.parsedResume.general_details.phone || ''
        },
        date: new Date().toLocaleDateString('en-US', { year: 'numeric', month: 'long', day: 'numeric' }),
        company: {
          name: '',
          address: ''
        },
        subject: 'Application for Position',
        greeting: 'Dear Hiring Manager,',
        introduction: '',
        background: '',
        skillsAlignment: '',
        closing: ''
      };
    },

    formatDate(dateString) {
      if (!dateString || dateString === 'Present') return 'Present';
      const date = new Date(dateString);
      return isNaN(date) ? dateString : date.toLocaleDateString('en-US', {
        year: 'numeric',
        month: 'short'
      });
    },
    
    startEditingTitle() {
      this.editableTitle = this.resume.title;
      this.editing.title = true;
    },
    
    async saveTitle(title) {
      if (!title.trim()) {
        return;
      }
      
      try {
        const id = this.$route.params.id;
        await axios.put(`/api/resumes/${id}`, {
          title: title
        }, {
          headers: { 'Authorization': `Bearer ${localStorage.getItem('auth_token')}` }
        });
        
        this.resume.title = title;
        this.editing.title = false;
      } catch (error) {
        console.error('Error updating title:', error);
        alert('Failed to update title. Please try again.');
      }
    },
    
    toggleEdit(section) {
      if (this.editing[section]) {
        // Save changes
        this.saveSection(section);
      } else {
        // Enter edit mode
        // Backup current data for cancel functionality
        this.backupData[section] = JSON.parse(JSON.stringify(this.editableResume));
        
        if (section === 'coverLetter') {
          this.parseCoverLetter();
          this.backupData.coverLetter = JSON.parse(JSON.stringify(this.coverLetterData));
        } else if (section === 'education') {
          // Ensure dates are properly formatted for the date input fields
          this.editableResume.education.forEach(edu => {
            if (edu.startDate) {
              // Format as YYYY-MM for input[type="month"]
              edu.startDate = this.formatDateForInput(edu.startDate);
            }
            if (edu.endDate && !edu.current) {
              edu.endDate = this.formatDateForInput(edu.endDate);
            }
          });
        } else if (section === 'workExperience') {
          // Ensure dates are properly formatted for the date input fields
          this.editableResume.work_experience.forEach(job => {
            if (job.startDate) {
              job.startDate = this.formatDateForInput(job.startDate);
            }
            if (job.endDate && !job.current) {
              job.endDate = this.formatDateForInput(job.endDate);
            }
          });
        } else if (section === 'professionalQualifications') {
          // Ensure dates are properly formatted for the date input fields
          if (this.editableResume.professionalQualifications && this.editableResume.professionalQualifications.items) {
            this.editableResume.professionalQualifications.items.forEach(qual => {
              if (qual.date) {
                qual.date = this.formatDateForInput(qual.date);
              }
              if (qual.expirationDate && !qual.current) {
                qual.expirationDate = this.formatDateForInput(qual.expirationDate);
              }
            });
          }
        }
        
        this.editing[section] = true;
      }
    },

    cancelEdit(section) {
      if (section === 'coverLetter' && this.backupData.coverLetter) {
        this.coverLetterData = JSON.parse(JSON.stringify(this.backupData.coverLetter));
      } else if (this.backupData[section]) {
        // Restore from backup
        this.editableResume = JSON.parse(JSON.stringify(this.backupData[section]));
      }
      
      // Exit edit mode
      this.editing[section] = false;
    },

    async saveSection(section) {
      try {
        const id = this.$route.params.id;
        let updateData = {};
        let payload = {};
        
        if (section === 'coverLetter') {
          // Convert the form data back to JSON string
          const coverLetterJson = JSON.stringify(this.coverLetterData);
          payload = { cover_letter: coverLetterJson };
        } else {
          switch (section) {
            case 'personalInfo':
              // Convert full name to title case
              if (this.editableResume.general_details.fullName) {
                this.editableResume.general_details.fullName = this.toTitleCase(this.editableResume.general_details.fullName);
              }
              updateData.general_details = JSON.stringify(this.editableResume.general_details);
              break;
            case 'workExperience':
              updateData.work_experience = JSON.stringify(this.editableResume.work_experience);
              break;
            case 'education':
              updateData.education = JSON.stringify(this.editableResume.education);
              break;
            case 'skills':
              // Format skills in sentence case before saving
              if (this.editableResume.skills && this.editableResume.skills.items) {
                this.editableResume.skills.items = this.editableResume.skills.items.map(
                  skill => this.toSentenceCase(skill)
                );
              }
              updateData.skills = JSON.stringify(this.editableResume.skills);
              break;
            case 'professionalQualifications':
              // Format qualification names in title case before saving
              if (this.editableResume.professionalQualifications && this.editableResume.professionalQualifications.items) {
                this.editableResume.professionalQualifications.items.forEach(qual => {
                  qual.name = this.toTitleCase(qual.name);
                  qual.issuer = this.toTitleCase(qual.issuer);
                });
              }
              updateData.professional_qualifications = JSON.stringify(this.editableResume.professionalQualifications);
              break;
            case 'referees':
              updateData.referees = JSON.stringify(this.editableResume.referees);
              break;
          }
          payload = updateData;
        }
        
        await axios.put(`/api/resumes/${id}`, payload, {
          headers: { 'Authorization': `Bearer ${localStorage.getItem('auth_token')}` }
        });
        
        if (section === 'coverLetter') {
          this.resume.cover_letter = JSON.stringify(this.coverLetterData);
        } else {
          // Update the resume object with the edited data
          Object.assign(this.parsedResume, this.editableResume);
        }
        
        // Exit edit mode
        this.editing[section] = false;
      } catch (error) {
        console.error(`Error saving ${section}:`, error);
        alert(`Failed to save changes to ${section}. Please try again.`);
      }
    },

    // Handler methods for each section
    savePersonalInfo(data) {
      this.editableResume.general_details = data;
      this.saveSection('personalInfo');
    },

    saveWorkExperience(data) {
      this.editableResume.work_experience = data;
      this.saveSection('workExperience');
    },

    saveEducation(data) {
      this.editableResume.education = data;
      this.saveSection('education');
    },

    saveSkills(data) {
      this.editableResume.skills = data;
      this.saveSection('skills');
    },

    saveProfessionalQualifications(data) {
      this.editableResume.professionalQualifications = data;
      this.saveSection('professionalQualifications');
    },

    saveReferees(data) {
      this.editableResume.referees = data;
      this.saveSection('referees');
    },

    saveCoverLetter(data) {
      this.coverLetterData = data;
      this.saveSection('coverLetter');
    },

    viewJobDescription() {
      this.showJobDescriptionModal = true;
    },

    viewCoverLetter() {
      this.showCoverLetterModal = true;
    },

    downloadResume() {
      // Get the auth token
      const token = localStorage.getItem('auth_token');
      
      // Create a URL with the token as a query parameter
      const url = `/download/resume/${this.resume.id}?token=${token}`;
      
      // Open the URL in a new tab
      window.open(url, '_blank');
    },

    downloadCoverLetter() {
      // Get the auth token
      const token = localStorage.getItem('auth_token');
      
      // Create a URL with the token as a query parameter
      const url = `/download/cover_letter/${this.resume.id}?token=${token}`;
      
      // Open the URL in a new tab
      window.open(url, '_blank');
    },

    copyCoverLetter() {
      let textToCopy = '';
      
      if (this.coverLetterData) {
        // Format JSON structure into readable text
        textToCopy = `${this.coverLetterData.applicant.name}
${this.coverLetterData.applicant.address}
${this.coverLetterData.applicant.email}
${this.coverLetterData.applicant.phone}

${this.coverLetterData.date}

${this.coverLetterData.company.name}
${this.coverLetterData.company.address}

Subject: ${this.coverLetterData.subject}

${this.coverLetterData.greeting}

${this.coverLetterData.introduction}

${this.coverLetterData.background}

${this.coverLetterData.skillsAlignment}

${this.coverLetterData.closing}

Best regards,
${this.coverLetterData.applicant.name}`;
      } else {
        // Copy raw text
        textToCopy = this.resume.cover_letter;
      }

      // Create temporary element to copy text
      const el = document.createElement('textarea');
      el.value = textToCopy.trim();
      document.body.appendChild(el);
      el.select();
      document.execCommand('copy');
      document.body.removeChild(el);
      
      // Show feedback
      alert('Cover letter copied to clipboard!');
    },

    parseCoverLetter() {
      try {
        // Try to parse as JSON
        const parsed = this.parseField(this.resume.cover_letter);
        if (parsed && typeof parsed === 'object') {
          this.coverLetterData = parsed;
        }
      } catch (e) {
        // If not valid JSON, initialize with default values and personal info
        this.coverLetterData = {
          applicant: {
            name: this.parsedResume.general_details.fullName || '',
            address: this.parsedResume.general_details.location || '',
            email: this.parsedResume.general_details.email || '',
            phone: this.parsedResume.general_details.phone || ''
          },
          date: new Date().toLocaleDateString('en-US', { year: 'numeric', month: 'long', day: 'numeric' }),
          company: {
            name: '',
            address: ''
          },
          subject: 'Application for Position',
          greeting: 'Dear Hiring Manager,',
          introduction: '',
          background: '',
          skillsAlignment: '',
          closing: ''
        };
      }
    },

    // Helper method to convert string to sentence case
    toSentenceCase(str) {
      if (!str || typeof str !== 'string') return '';
      return str.charAt(0).toUpperCase() + str.slice(1).toLowerCase();
    },

    // Helper method for title case
    toTitleCase(str) {
      if (!str || typeof str !== 'string') return '';
      return str.split(' ')
        .map(word => word.charAt(0).toUpperCase() + word.slice(1).toLowerCase())
        .join(' ');
    },

    async regenerateSection(section) {
      if (this.regenerating[section]) return;

      this.regenerating[section] = true;
      this.regenerationError = null;

      try {
        const id = this.$route.params.id;
        const response = await axios.post(`/api/resumes/${id}/regenerate/${section}`, {
          section: section
        }, {
          headers: { 'Authorization': `Bearer ${localStorage.getItem('auth_token')}` }
        });

        // Update all resume data structures
        this.resume = response.data.data;
        
        // Re-parse all fields
        this.parsedResume = this.parseResumeData(response.data.data);
        this.editableResume = JSON.parse(JSON.stringify(this.parsedResume));
        
        // Parse education field if it's a string
        if (typeof this.parsedResume.education === 'string') {
          this.parsedResume.education = JSON.parse(this.parsedResume.education).education || [];
        }

        // Show success notification
        this.successMessage = `${this.sectionToTitle(section)} regenerated successfully.`;
        this.showSuccessNotification = true;
        setTimeout(() => {
          this.showSuccessNotification = false;
        }, 5000);

        // Refresh regeneration limits
        this.fetchRegenerationLimits();

      } catch (error) {
        console.error('Error regenerating section:', error);
        this.regenerationError = error.response?.data?.error || 'Failed to regenerate section. Please try again.';
      } finally {
        this.regenerating[section] = false;
      }
    },

    parseResumeData(resumeData) {
      const parseField = (field) => {
        if (!field) return null;
        
        if (typeof field === 'string') {
          try {
            return JSON.parse(field);
          } catch (e) {
            return field;
          }
        }
        return field;
      };

      // Special handling for nested structures
      const parseNestedSection = (data, sectionKey) => {
        const parsed = parseField(data);
        const items = parsed?.[sectionKey] || parsed || [];
        
        // Add specific handling for work experience descriptions
        if (sectionKey === 'work_experience') {
          return items.map(item => ({
            ...item,
            description: Array.isArray(item.description) ? 
              item.description : 
              (item.description ? [item.description] : [])
          }));
        }

        if (sectionKey === 'referees') {
          return Array.isArray(items) ? items : [];
        }
        
        // Handle the double-nested professional_qualifications structure
        if (sectionKey === 'professional_qualifications') {
          // Check if we have the double-nested structure
          if (parsed && parsed.professional_qualifications && parsed.professional_qualifications.items) {
            return { 
              items: parsed.professional_qualifications.items.map(item => 
                typeof item === 'string' ? { name: item } : item
              ) 
            };
          }
          // Handle array of strings format
          if (Array.isArray(parsed)) {
            return { 
              items: parsed.map(item => 
                typeof item === 'string' ? { name: item } : item
              ) 
            };
          }
          // Handle object with items
          if (parsed && parsed.items) {
            return { 
              items: parsed.items.map(item => 
                typeof item === 'string' ? { name: item } : item
              ) 
            };
          }
          return { items: [] };
        }
        
        return items;
      };

      return {
        general_details: parseField(resumeData.general_details) || {},
        work_experience: parseNestedSection(resumeData.work_experience, 'work_experience'),
        education: parseNestedSection(resumeData.education, 'education') || [],
        skills: parseNestedSection(resumeData.skills, 'skills') || { items: [] },
        professional_qualifications: parseNestedSection(resumeData.professional_qualifications, 'professional_qualifications') || { items: [] },
        referees: parseNestedSection(resumeData.referees, 'referees') || []
      };
    },

    async fetchRegenerationLimits() {
      try {
        const id = this.$route.params.id;
        const authToken = localStorage.getItem("auth_token");
        if (!authToken) {
          throw new Error("Authentication token is missing.");
        }

        const response = await axios.get(`/api/resumes/${id}/regeneration-limits`, {
          headers: { Authorization: `Bearer ${authToken}` }
        });
        
        if (response.data && response.data.limits) {
          this.regenerationLimits = response.data.limits;
        }
      } catch (error) {
        console.error('Error fetching regeneration limits:', error);
        // Set default values to prevent UI errors
        this.regenerationLimits = {
          general_details: 0,
          work_experience: 0,
          education: 0,
          skills: 0,
          professional_qualifications: 0,
          referees: 0
        };
      }
    },

    sectionToTitle(section) {
      const titles = {
        'general_details': 'Personal Information',
        'work_experience': 'Work Experience',
        'education': 'Education',
        'skills': 'Skills',
        'professional_qualifications': 'Professional Qualifications',
        'referees': 'Referees'
      };
      
      return titles[section] || section;
    },

    $toast(options) {
      // Check if the global $toast method exists
      if (typeof this.$root.$toast === 'function') {
        this.$root.$toast(options);
      } else if (window.toast) {
        window.toast(options);
      } else {
        // Fallback to alert if no toast library is available
        alert(options.description || options.title);
      }
    },

    showAlert(options) {
      if (this.$root && typeof this.$root.showAlert === 'function') {
        this.$root.showAlert(options);
      } else {
        // Fallback to toast if root showAlert is not available
        this.$toast?.({
          title: options.title,
          description: options.message,
          type: options.type,
          duration: 3000
        }) || alert(options.message);
      }
    },

    showSuccess(message) {
      this.successMessage = message;
      this.showSuccessNotification = true;
      setTimeout(() => {
        this.showSuccessNotification = false;
      }, 5000); // Display for 5 seconds
    }
  }
};
</script>

<style>
.tooltip {
  transition: opacity 0.3s;
  pointer-events: none;
}
</style>