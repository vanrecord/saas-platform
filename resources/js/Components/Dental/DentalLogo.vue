<template>
  <div class="flex items-center gap-3 select-none">
    <!-- Dental Tooth Icon with Gradient -->
    <div class="relative flex items-center justify-center flex-shrink-0 w-11 h-11 sm:w-12 sm:h-12 rounded-xl bg-gradient-to-br from-blue-50 to-sky-100 p-2 shadow-sm border border-blue-100/70">
      <svg class="w-full h-full" viewBox="0 0 44 44" fill="none" xmlns="http://www.w3.org/2000/svg">
        <defs>
          <linearGradient id="dentalToothGrad" x1="0%" y1="0%" x2="100%" y2="100%">
            <stop offset="0%" stop-color="#087CC1" />
            <stop offset="100%" stop-color="#35B8ED" />
          </linearGradient>
        </defs>
        <!-- Modern Tooth Contour -->
        <path
          d="M13.5 5.5C8.8 5.5 5 9.2 5 15C5 21 7.6 26.2 11.2 32.8C13.8 37.5 17.5 37.8 19.5 33.5C20.8 30.5 21.2 26 22 26C22.8 26 23.2 30.5 24.5 33.5C26.5 37.8 30.2 37.5 32.8 32.8C36.4 26.2 39 21 39 15C39 9.2 35.2 5.5 30.5 5.5C26.8 5.5 24 8 22 10C20 8 17.2 5.5 13.5 5.5Z"
          stroke="url(#dentalToothGrad)"
          stroke-width="3"
          stroke-linecap="round"
          stroke-linejoin="round"
          fill="#FFFFFF"
        />
        <!-- Inner Highlight Arc -->
        <path
          d="M12.5 13C10.5 15 9.8 18 10.2 21"
          stroke="url(#dentalToothGrad)"
          stroke-width="2.5"
          stroke-linecap="round"
        />
      </svg>
    </div>

    <!-- Branding Text -->
    <div class="flex flex-col">
      <span class="text-lg sm:text-xl font-bold tracking-tight text-[#10234B] leading-none">
        Dental Clinic
      </span>
      <span v-if="showTagline" class="text-[11px] sm:text-xs font-medium text-[#5B6E8C] tracking-wide mt-1">
        Healthy Smile, Better Life
      </span>
    </div>
  </div>
</template>

<script setup>
defineProps({
  showTagline: {
    type: Boolean,
    default: true
  }
})
</script>

