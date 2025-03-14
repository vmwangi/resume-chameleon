<template>
  <div class="bg-gray-50 py-16 sm:py-24">
    <div class="container mx-auto px-4 sm:px-6 lg:px-8">
      <div class="max-w-4xl mx-auto text-center">
        <h1 class="text-3xl font-bold text-gray-900 sm:text-4xl">
          Simple, Transparent Pricing
        </h1>
        <p class="mt-4 text-lg text-gray-600">
          No subscriptions. No hidden fees. Just pay for what you need.
        </p>
      </div>
      
      <!-- Pricing Card -->
      <div class="mt-12 max-w-lg mx-auto">
        <div class="bg-white rounded-xl shadow-md overflow-hidden">
          <div class="px-6 py-8 bg-emerald-600 sm:p-10 sm:pb-6">
            <div class="flex items-center justify-center">
              <h3 class="inline-flex px-4 py-1 rounded-full text-sm font-semibold tracking-wide uppercase bg-white text-emerald-600">
                Pay Per Resume
              </h3>
            </div>
            <div class="mt-4 flex items-baseline text-center justify-center">
              <span class="text-5xl font-bold text-white">Free</span>
              <span class="ml-1 text-xl font-semibold text-emerald-50">to start</span>
            </div>
            <p class="mt-5 text-lg text-center text-emerald-50">
              Your first 2 tailored resumes are on us
            </p>
          </div>
          <div class="px-6 pt-6 pb-8 bg-white sm:p-10">
            <ul class="space-y-4">
              <li class="flex items-start">
                <div class="flex-shrink-0">
                  <svg class="h-6 w-6 text-emerald-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                  </svg>
                </div>
                <p class="ml-3 text-base text-gray-700">
                  First 2 tailored resumes + cover letters: <span class="font-medium">Free</span>
                </p>
              </li>
              <li class="flex items-start">
                <div class="flex-shrink-0">
                  <svg class="h-6 w-6 text-emerald-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                  </svg>
                </div>
                <p class="ml-3 text-base text-gray-700">
                  Additional tailored resumes: <span class="font-medium">$2.99 each</span>
                </p>
              </li>
              <li class="flex items-start">
                <div class="flex-shrink-0">
                  <svg class="h-6 w-6 text-emerald-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                  </svg>
                </div>
                <p class="ml-3 text-base text-gray-700">
                  AI-powered keyword optimization
                </p>
              </li>
              <li class="flex items-start">
                <div class="flex-shrink-0">
                  <svg class="h-6 w-6 text-emerald-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                  </svg>
                </div>
                <p class="ml-3 text-base text-gray-700">
                  Matching cover letter generation
                </p>
              </li>
              <li class="flex items-start">
                <div class="flex-shrink-0">
                  <svg class="h-6 w-6 text-emerald-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                  </svg>
                </div>
                <p class="ml-3 text-base text-gray-700">
                  PDF downloads
                </p>
              </li>
            </ul>
            <div class="mt-8">
              <router-link 
                to="/login"
                class="block w-full text-center px-5 py-3 border border-transparent text-base font-medium rounded-md text-white bg-emerald-600 hover:bg-emerald-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-emerald-500 transition-colors duration-200"
              >
                Get Started
              </router-link>
            </div>
          </div>
        </div>
      </div>
      
      <!-- FAQs Section -->
      <div class="mt-16 max-w-3xl mx-auto">
        <h2 class="text-2xl font-bold text-gray-900 text-center mb-8">
          Frequently Asked Questions
        </h2>
        
        <div class="space-y-6 bg-white rounded-xl shadow-sm p-6">
          <div class="border-b border-gray-100 pb-6">
            <h3 class="text-lg font-medium text-emerald-600">Are the first 2 resumes really free?</h3>
            <div class="mt-3">
              <p class="text-gray-700">
                Yes, your first 2 tailored resumes and cover letters are completely free! We want you to experience the value of our service before you decide to purchase more.
              </p>
            </div>
          </div>
          
          <div class="border-b border-gray-100 pb-6">
            <h3 class="text-lg font-medium text-emerald-600">How much do additional resumes cost?</h3>
            <div class="mt-3">
              <p class="text-gray-700">
                Each additional tailored resume with cover letter costs $2.99. This includes AI optimization, formatting, and downloadable PDF files.
              </p>
            </div>
          </div>
          
          <div class="border-b border-gray-100 pb-6">
            <h3 class="text-lg font-medium text-emerald-600">Can I download my resumes?</h3>
            <div class="mt-3">
              <p class="text-gray-700">
                Yes, you can download both resumes and cover letters in PDF format. Your documents are also saved in your account so you can access them anytime.
              </p>
            </div>
          </div>
          
          <div class="border-b border-gray-100 pb-6">
            <h3 class="text-lg font-medium text-emerald-600">How does the AI optimization work?</h3>
            <div class="mt-3">
              <p class="text-gray-700">
                Our AI analyzes the job description you provide and compares it with your resume. It then suggests changes to highlight relevant skills and experience, increasing your match rate with applicant tracking systems.
              </p>
            </div>
          </div>
          
          <div class="border-b border-gray-100 pb-6">
            <h3 class="text-lg font-medium text-emerald-600">Can I retry generating content after creating a tailored resume?</h3>
            <div class="mt-3">
              <p class="text-gray-700">
                Yes! After creating your tailored resume, you can regenerate specific sections if you're not satisfied with the results. You can regenerate sections up to 3 times per hour, allowing you to fine-tune your resume until it perfectly matches your needs.
              </p>
            </div>
          </div>
          
          <div>
            <h3 class="text-lg font-medium text-emerald-600">Do I need to verify the details in my tailored resume?</h3>
            <div class="mt-3">
              <p class="text-gray-700">
                While our AI creates high-quality tailored resumes that are ready for application, we strongly recommend that you review all details before submitting. The AI optimizes your existing information but you should verify that all dates, job titles, and accomplishments are accurate and truthfully represent your experience.
              </p>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>