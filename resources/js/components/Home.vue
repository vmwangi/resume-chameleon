<template>
  <div>
    <!-- Hero Section - Adjusted to add more space between navbar and content -->
    <section class="bg-gradient-to-b from-white to-gray-50 pt-28 pb-20 sm:pt-32 sm:pb-28">
      <div class="container mx-auto px-4 sm:px-6 lg:px-8">
        <div class="max-w-4xl mx-auto text-center">
          <div class="flex justify-center mb-6">
            <div class="w-16 h-16">
              <svg viewBox="0 0 36 36" fill="none" xmlns="http://www.w3.org/2000/svg" class="w-full h-full">
                <!-- Chameleon body (circle background) -->
                <circle cx="18" cy="18" r="18" fill="#10B981"/>
                
                <!-- Chameleon body -->
                <path d="M28 14.5C28 14.5 26 12 23 13C20 14 18 16 18 16C18 16 16 14 13 13C10 12 8 14.5 8 14.5V23C8 23 10 25.5 13 24.5C16 23.5 18 21 18 21C18 21 20 23.5 23 24.5C26 25.5 28 23 28 23V14.5Z" fill="#0D9668"/>
                
                <!-- Chameleon body details -->
                <path d="M28 14.5C28 14.5 26 17 23 18C20 19 18 16 18 16C18 16 16 19 13 18C10 17 8 14.5 8 14.5V23C8 23 10 25.5 13 24.5C16 23.5 18 21 18 21C18 21 20 23.5 23 24.5C26 25.5 28 23 28 23V14.5Z" fill="white"/>
                
                <!-- Chameleon eyes -->
                <circle cx="12" cy="12" r="2" fill="black"/>
                <circle cx="24" cy="12" r="2" fill="black"/>
                <circle cx="12" cy="11.5" r="0.5" fill="white"/>
                <circle cx="24" cy="11.5" r="0.5" fill="white"/>
                
                <!-- Chameleon crest -->
                <path d="M18 6C18 6 16 8 16 10C16 12 18 14 18 14C18 14 20 12 20 10C20 8 18 6 18 6Z" fill="#0D9668"/>
                
                <!-- Chameleon tail -->
                <path d="M8 14.5C8 14.5 6 15 4 17C2 19 3 22 5 23C7 24 8 23 8 23" stroke="#0D9668" stroke-width="1.5" fill="none"/>
                
                <!-- Chameleon legs -->
                <path d="M10 19C8 20 7 22 8 24" stroke="#0D9668" stroke-width="1.5"/>
                <path d="M26 19C28 20 29 22 28 24" stroke="#0D9668" stroke-width="1.5"/>
              </svg>
            </div>
          </div>
          <h1 class="text-4xl sm:text-5xl md:text-6xl font-extrabold text-gray-900 tracking-tight">
            <span class="block">Transform Your Resume,</span>
            <span class="block text-emerald-600">Land Your Dream Job</span>
          </h1>
          <p class="mt-6 text-xl text-gray-600 max-w-2xl mx-auto">
            Resume Chameleon adapts your resume to match job descriptions, increasing your interview chances by up to 70%.
          </p>
          <div class="mt-10 flex flex-col sm:flex-row justify-center gap-4">
            <router-link 
              to="/login" 
              class="px-6 py-3 rounded-md shadow-sm text-white bg-emerald-600 hover:bg-emerald-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-emerald-500 transition-colors duration-200 font-medium"
            >
              Create Your Resume for Free
            </router-link>
            <router-link 
              to="/pricing" 
              class="px-6 py-3 rounded-md text-emerald-700 bg-emerald-100 hover:bg-emerald-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-emerald-500 transition-colors duration-200 font-medium"
            >
              View Pricing
            </router-link>
          </div>
        </div>
      </div>
    </section>

    <!-- Demo Section - Updated with Supademo integration -->
    <section class="py-16 sm:py-24 bg-white">
      <div class="container mx-auto px-4 sm:px-6 lg:px-8">
        <div class="max-w-3xl mx-auto text-center mb-12">
          <h2 class="text-3xl font-bold text-gray-900">See It In Action</h2>
          <p class="mt-4 text-lg text-gray-600">
            Watch how Resume Chameleon transforms a generic resume into a job-winning application
          </p>
        </div>
        
        <!-- Supademo integration with custom styling -->
        <div class="max-w-5xl mx-auto rounded-xl overflow-hidden shadow-lg">
          <div class="relative w-full" style="max-height: 80vh; max-height: 80svh;">
            <div style="position: relative; box-sizing: content-box; width: 100%; aspect-ratio: 2.0454545454545454; padding: 0;">
              <iframe 
                src="https://app.supademo.com/embed/cm84hpoia0fsb10u1d9qqghod?embed_v=2" 
                loading="lazy" 
                title="Resume Chameleon Demo" 
                allow="clipboard-write" 
                frameborder="0" 
                webkitallowfullscreen="true" 
                mozallowfullscreen="true" 
                allowfullscreen 
                class="w-full h-full"
                style="position: absolute; top: 0; left: 0; width: 100%; height: 100%; border-radius: 0.75rem;"
              ></iframe>
            </div>
          </div>
          <div class="p-6 text-center bg-white">
            <router-link 
              to="/login" 
              class="inline-flex items-center px-6 py-3 rounded-md text-white bg-emerald-600 hover:bg-emerald-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-emerald-500 transition-colors duration-200 font-medium"
            >
              Try it yourself
            </router-link>
          </div>
        </div>
      </div>
    </section>

    <!-- Features Section -->
    <section class="py-16 sm:py-24 bg-gray-50">
      <div class="container mx-auto px-4 sm:px-6 lg:px-8">
        <div class="max-w-3xl mx-auto text-center mb-16">
          <h2 class="text-3xl font-bold text-gray-900">How It Works</h2>
          <p class="mt-4 text-lg text-gray-600">
            Our AI-powered platform makes it easy to create tailored resumes in minutes
          </p>
        </div>
        
        <div class="grid grid-cols-1 gap-10 md:grid-cols-3 max-w-5xl mx-auto">
          <div class="bg-white rounded-xl p-8 text-center shadow-sm hover:shadow-md transition-shadow duration-300">
            <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-emerald-100 text-emerald-600 mb-6">
              <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
              </svg>
            </div>
            <h3 class="text-lg font-medium text-gray-900">1. Upload Your Resume</h3>
            <p class="mt-3 text-gray-600">
              Start with your existing resume or create a new one from scratch using our intuitive builder
            </p>
          </div>
          
          <div class="bg-white rounded-xl p-8 text-center shadow-sm hover:shadow-md transition-shadow duration-300">
            <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-emerald-100 text-emerald-600 mb-6">
              <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
              </svg>
            </div>
            <h3 class="text-lg font-medium text-gray-900">2. Paste Job Description</h3>
            <p class="mt-3 text-gray-600">
              Add the job description you're applying for and our AI will analyze keyword matches
            </p>
          </div>
          
          <div class="bg-white rounded-xl p-8 text-center shadow-sm hover:shadow-md transition-shadow duration-300">
            <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-emerald-100 text-emerald-600 mb-6">
              <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
              </svg>
            </div>
            <h3 class="text-lg font-medium text-gray-900">3. Download & Apply</h3>
            <p class="mt-3 text-gray-600">
              Get your tailored resume and matching cover letter, ready to submit to employers
            </p>
          </div>
        </div>
      </div>
    </section>

    <!-- Testimonials Section -->
    <section class="py-16 sm:py-24 bg-white">
      <div class="container mx-auto px-4 sm:px-6 lg:px-8">
        <div class="max-w-3xl mx-auto text-center mb-16">
          <h2 class="text-3xl font-bold text-gray-900">What Our Users Say</h2>
          <p class="mt-4 text-lg text-gray-600">
            Join thousands of job seekers who have found success with Resume Chameleon
          </p>
        </div>
        
        <div class="grid grid-cols-1 gap-8 md:grid-cols-3 max-w-5xl mx-auto">
          <div class="bg-white rounded-xl shadow-sm p-8 border border-gray-100">
            <div class="text-emerald-500 mb-4">
              <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="currentColor">
                <path d="M14.017 21v-7.391c0-5.704 3.731-9.57 8.983-10.609l.995 2.151c-2.432.917-3.995 3.638-3.995 5.849h4v10h-9.983zm-14.017 0v-7.391c0-5.704 3.748-9.57 9-10.609l.996 2.151c-2.433.917-3.996 3.638-3.996 5.849h3.983v10h-9.983z" />
              </svg>
            </div>
            <p class="text-gray-700">
              "Resume Chameleon helped me land my dream job! The tailored resume made all the difference in getting noticed by recruiters."
            </p>
            <div class="mt-6 flex items-center">
              <div class="flex-shrink-0">
                <div class="h-10 w-10 rounded-full bg-emerald-100 flex items-center justify-center text-emerald-700 font-bold">
                  JD
                </div>
              </div>
              <div class="ml-3">
                <p class="text-sm font-medium text-gray-900">John D.</p>
                <p class="text-sm text-gray-500">Software Engineer</p>
              </div>
            </div>
          </div>
          
          <div class="bg-white rounded-xl shadow-sm p-8 border border-gray-100">
            <div class="text-emerald-500 mb-4">
              <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="currentColor">
                <path d="M14.017 21v-7.391c0-5.704 3.731-9.57 8.983-10.609l.995 2.151c-2.432.917-3.995 3.638-3.995 5.849h4v10h-9.983zm-14.017 0v-7.391c0-5.704 3.748-9.57 9-10.609l.996 2.151c-2.433.917-3.996 3.638-3.996 5.849h3.983v10h-9.983z" />
              </svg>
            </div>
            <p class="text-gray-700">
              "The easiest way to create professional resumes. I was able to customize everything to match exactly what employers were looking for."
            </p>
            <div class="mt-6 flex items-center">
              <div class="flex-shrink-0">
                <div class="h-10 w-10 rounded-full bg-emerald-100 flex items-center justify-center text-emerald-700 font-bold">
                  SM
                </div>
              </div>
              <div class="ml-3">
                <p class="text-sm font-medium text-gray-900">Sarah M.</p>
                <p class="text-sm text-gray-500">Marketing Specialist</p>
              </div>
            </div>
          </div>
          
          <div class="bg-white rounded-xl shadow-sm p-8 border border-gray-100">
            <div class="text-emerald-500 mb-4">
              <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="currentColor">
                <path d="M14.017 21v-7.391c0-5.704 3.731-9.57 8.983-10.609l.995 2.151c-2.432.917-3.995 3.638-3.995 5.849h4v10h-9.983zm-14.017 0v-7.391c0-5.704 3.748-9.57 9-10.609l.996 2.151c-2.433.917-3.996 3.638-3.996 5.849h3.983v10h-9.983z" />
              </svg>
            </div>
            <p class="text-gray-700">
              "I love how customizable everything is! The AI suggestions helped me highlight skills I didn't even know were relevant."
            </p>
            <div class="mt-6 flex items-center">
              <div class="flex-shrink-0">
                <div class="h-10 w-10 rounded-full bg-emerald-100 flex items-center justify-center text-emerald-700 font-bold">
                  MR
                </div>
              </div>
              <div class="ml-3">
                <p class="text-sm font-medium text-gray-900">Mike R.</p>
                <p class="text-sm text-gray-500">Product Manager</p>
              </div>
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- CTA Section -->
    <section class="py-16 sm:py-24 bg-emerald-600">
      <div class="container mx-auto px-4 sm:px-6 lg:px-8">
        <div class="max-w-3xl mx-auto text-center">
          <h2 class="text-3xl font-bold text-white">Ready to land your dream job?</h2>
          <p class="mt-4 text-lg text-emerald-50">
            Create your first tailored resume for free and see the difference
          </p>
          <div class="mt-8">
            <router-link 
              to="/login" 
              class="inline-flex items-center px-6 py-3 rounded-md text-emerald-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-offset-emerald-600 focus:ring-white transition-colors duration-200 font-medium"
            >
              Get Started Now
            </router-link>
          </div>
        </div>
      </div>
    </section>
  </div>
</template>