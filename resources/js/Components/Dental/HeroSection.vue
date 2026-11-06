<template>
  <section class="relative bg-[#F4F9FF] overflow-hidden pt-28 sm:pt-32 lg:pt-36 pb-20 sm:pb-28 lg:pb-36" id="scroll-on-top">
    <!-- Soft Decorative Ambient Glows in Background -->
    <div class="pointer-events-none absolute -top-24 left-1/4 w-96 h-96 bg-sky-200/40 rounded-full blur-3xl -z-10"></div>
    <div class="pointer-events-none absolute top-1/3 -right-20 w-[30rem] h-[30rem] bg-blue-100/50 rounded-full blur-3xl -z-10"></div>

    <div class="container mx-auto px-4 sm:px-6 lg:px-8 max-w-7xl">
      <div class="grid grid-cols-1 lg:grid-cols-12 gap-10 lg:gap-8 items-center">
        
        <!-- Left Column: Typography & CTAs -->
        <div class="lg:col-span-6 flex flex-col items-start text-left z-10">
          
          <!-- Badge: "Trusted Dental Care" -->
          <div class="inline-flex items-center gap-2 px-3.5 py-1.5 rounded-full bg-[#E1F1FD] border border-[#BCE3FB] text-[#087CC1] text-xs sm:text-sm font-semibold mb-6 shadow-sm">
            <!-- Shield Checkmark Icon -->
            <svg class="w-4 h-4 text-[#087CC1] flex-shrink-0" viewBox="0 0 20 20" fill="currentColor">
              <path fill-rule="evenodd" d="M10 1.944A11.954 11.954 0 012.166 5C2.056 5.649 2 6.319 2 7c0 5.225 3.34 9.67 8 11.317C14.66 16.67 18 12.225 18 7c0-.682-.057-1.35-.166-2.001A11.954 11.954 0 0110 1.944zM13.707 8.707a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
            </svg>
            <span>Trusted Dental Care</span>
          </div>

          <!-- Headline: "Let's care for your healthy smile" -->
          <h1 class="text-4xl sm:text-5xl lg:text-6xl font-extrabold text-[#10234B] tracking-tight leading-[1.14] mb-6">
            Let's care for<br />
            your
            <span class="relative inline-block ml-1">
              <span class="bg-gradient-to-r from-[#087CC1] via-[#1E9FE3] to-[#35B8ED] bg-clip-text text-transparent font-extrabold">
                healthy smile
              </span>
              <!-- Stylized Smile Accent Underline -->
              <svg class="absolute -bottom-2 sm:-bottom-2.5 left-0 w-full h-3 sm:h-4 text-[#35B8ED]" viewBox="0 0 200 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M4 6C45 15 155 15 196 6" stroke="currentColor" stroke-width="4.5" stroke-linecap="round"/>
              </svg>
            </span>
          </h1>

          <!-- Supporting Description -->
          <p class="text-[#5B6E8C] text-base sm:text-lg leading-relaxed max-w-xl mb-8">
            With our modern dental clinic, we provide professional and comprehensive dental care for you and your family.
          </p>

          <!-- Action Buttons / Perks -->
          <div class="flex flex-wrap items-center gap-4 mb-8">
            <button
              type="button"
              @click="scrollToContact"
              class="inline-flex items-center justify-center px-7 py-3.5 rounded-full text-white font-semibold text-sm sm:text-base bg-gradient-to-r from-[#087CC1] to-[#35B8ED] shadow-lg shadow-blue-500/25 hover:shadow-xl hover:shadow-blue-500/35 hover:scale-[1.02] active:scale-[0.98] transition-all duration-200 cursor-pointer"
            >
              <span>Book Appointment</span>
              <svg class="w-4 h-4 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/>
              </svg>
            </button>

            <button
              type="button"
              @click="scrollToAbout"
              class="inline-flex items-center justify-center px-6 py-3.5 rounded-full text-[#10234B] bg-white border border-blue-100 hover:border-[#35B8ED] hover:text-[#087CC1] font-semibold text-sm sm:text-base shadow-sm hover:shadow-md transition-all duration-200 cursor-pointer"
            >
              Learn More
            </button>
          </div>

          <!-- Trust Indicators / Micro Perks -->
          <div class="grid grid-cols-3 gap-4 pt-4 border-t border-blue-100/60 w-full max-w-lg">
            <div class="flex items-center gap-2">
              <div class="w-8 h-8 rounded-full bg-blue-50 text-[#087CC1] flex items-center justify-center font-bold text-xs">
                ✓
              </div>
              <span class="text-xs sm:text-sm font-medium text-[#10234B]">Certified Doctors</span>
            </div>
            <div class="flex items-center gap-2">
              <div class="w-8 h-8 rounded-full bg-blue-50 text-[#087CC1] flex items-center justify-center font-bold text-xs">
                ✓
              </div>
              <span class="text-xs sm:text-sm font-medium text-[#10234B]">Modern Tech</span>
            </div>
            <div class="flex items-center gap-2">
              <div class="w-8 h-8 rounded-full bg-blue-50 text-[#087CC1] flex items-center justify-center font-bold text-xs">
                ✓
              </div>
              <span class="text-xs sm:text-sm font-medium text-[#10234B]">Gentle Care</span>
            </div>
          </div>

        </div>

        <!-- Right Column: Dental Clinic Illustration / Mascot & Dashboard Monitor -->
        <div class="lg:col-span-6 relative flex justify-center lg:justify-end items-center">
          
          <!-- Hero Illustration Container -->
          <div class="relative w-full max-w-xl group">
            
            <!-- Soft Backdrop Blur Card -->
            <div class="relative rounded-3xl overflow-hidden bg-white/40 p-2 sm:p-3 border border-white/80 shadow-2xl shadow-blue-500/10 backdrop-blur-sm transition-all duration-300 group-hover:shadow-blue-500/20">
              
              <img
                src="/images/dental/hero-illustration.png"
                alt="Dental Clinic modern equipment, tooth mascot, and management dashboard"
                class="w-full h-auto object-cover rounded-2xl transition-transform duration-500 ease-out group-hover:scale-[1.01]"
                loading="eager"
              />

              <!-- Floating Quick Stat Badge (Top Right) -->
              <div class="hidden sm:flex absolute top-5 right-5 items-center gap-2.5 px-3.5 py-2 bg-white/95 rounded-2xl shadow-lg shadow-slate-200/60 border border-blue-50 backdrop-blur-md animate-bounce-subtle">
                <span class="flex h-3 w-3 relative">
                  <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75"></span>
                  <span class="relative inline-flex rounded-full h-3 w-3 bg-emerald-500"></span>
                </span>
                <span class="text-xs font-bold text-[#10234B]">Online Booking Active</span>
              </div>

              <!-- Floating Rating Pill (Bottom Left) -->
              <div class="hidden sm:flex absolute bottom-5 left-5 items-center gap-2 px-3.5 py-2 bg-white/95 rounded-2xl shadow-lg shadow-slate-200/60 border border-blue-50 backdrop-blur-md">
                <div class="flex text-amber-400 text-xs">
                  ★★★★★
                </div>
                <span class="text-xs font-bold text-[#10234B]">4.9 / 5.0 (1.2k+ Reviews)</span>
              </div>

            </div>

          </div>

        </div>

      </div>
    </div>

    <!-- Bottom Layered Waves matching the visual reference -->
    <div class="absolute bottom-0 left-0 right-0 w-full overflow-hidden leading-none pointer-events-none">
      <svg
        class="relative block w-full h-12 sm:h-16 lg:h-24 text-white"
        viewBox="0 0 1440 120"
        fill="none"
        xmlns="http://www.w3.org/2000/svg"
        preserveAspectRatio="none"
      >
        <!-- Background light wave -->
        <path
          d="M0,40 C320,110 520,0 820,50 C1120,100 1320,20 1440,30 L1440,120 L0,120 Z"
          fill="#35B8ED"
          fill-opacity="0.25"
        />
        <!-- Mid tone wave -->
        <path
          d="M0,60 C360,120 640,30 960,70 C1200,100 1380,50 1440,60 L1440,120 L0,120 Z"
          fill="#087CC1"
          fill-opacity="0.35"
        />
        <!-- Foreground clean wave transitioning into white content section -->
        <path
          d="M0,80 C360,130 720,50 1080,85 C1260,105 1380,75 1440,80 L1440,120 L0,120 Z"
          fill="#FFFFFF"
        />
      </svg>
    </div>
  </section>
</template>

<script setup>
function scrollToContact() {
  const el = document.getElementById('contact')
  if (el) {
    el.scrollIntoView({ behavior: 'smooth' })
  }
}

function scrollToAbout() {
  const el = document.getElementById('about-us')
  if (el) {
    el.scrollIntoView({ behavior: 'smooth' })
  }
}
</script>

<style scoped>
@keyframes bounceSubtle {
  0%, 100% {
    transform: translateY(0);
  }
  50% {
    transform: translateY(-4px);
  }
}

.animate-bounce-subtle {
  animation: bounceSubtle 3s ease-in-out infinite;
}
</style>

