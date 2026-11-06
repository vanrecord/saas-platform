<template>
  <footer class="bg-[#10234B] text-white pt-16 pb-8 border-t border-blue-950">
    <div class="container mx-auto px-4 sm:px-6 lg:px-8 max-w-7xl">
      <div class="grid grid-cols-1 md:grid-cols-12 gap-10 pb-12 border-b border-white/10">
        
        <!-- Brand & Mission Column -->
        <div class="md:col-span-5 flex flex-col items-start">
          <div class="flex items-center gap-3 mb-4">
            <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-[#087CC1] to-[#35B8ED] p-2 flex items-center justify-center shadow-md">
              <svg class="w-full h-full text-white" viewBox="0 0 44 44" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path
                  d="M13.5 5.5C8.8 5.5 5 9.2 5 15C5 21 7.6 26.2 11.2 32.8C13.8 37.5 17.5 37.8 19.5 33.5C20.8 30.5 21.2 26 22 26C22.8 26 23.2 30.5 24.5 33.5C26.5 37.8 30.2 37.5 32.8 32.8C36.4 26.2 39 21 39 15C39 9.2 35.2 5.5 30.5 5.5C26.8 5.5 24 8 22 10C20 8 17.2 5.5 13.5 5.5Z"
                  stroke="#FFFFFF"
                  stroke-width="3"
                  stroke-linecap="round"
                  stroke-linejoin="round"
                  fill="none"
                />
              </svg>
            </div>
            <div>
              <span class="text-xl font-bold tracking-tight text-white block">Dental Clinic</span>
              <span class="text-xs text-sky-300 font-medium tracking-wide">Healthy Smile, Better Life</span>
            </div>
          </div>
          <p class="text-slate-300 text-sm leading-relaxed max-w-sm mb-6">
            Providing modern, comfortable, and comprehensive dental care services for you and your whole family.
          </p>
          <div class="flex items-center gap-3">
            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-white/10 text-xs font-medium text-sky-200">
              <span class="w-2 h-2 rounded-full bg-emerald-400"></span>
              Emergency Care Available
            </span>
          </div>
        </div>

        <!-- Quick Links -->
        <div class="md:col-span-3">
          <h3 class="text-sm font-semibold text-white uppercase tracking-wider mb-4">Quick Navigation</h3>
          <ul class="space-y-2.5 text-sm text-slate-300">
            <li>
              <a href="#scroll-on-top" class="hover:text-[#35B8ED] transition-colors duration-200">Home</a>
            </li>
            <li>
              <a href="#about-us" class="hover:text-[#35B8ED] transition-colors duration-200">About Us</a>
            </li>
            <li>
              <a href="#contact" class="hover:text-[#35B8ED] transition-colors duration-200">Contact Us</a>
            </li>
            <li v-if="!$page.props.user">
              <Link :href="route().has('register') ? route('register') : '/register'" class="hover:text-[#35B8ED] transition-colors duration-200">Register</Link>
            </li>
            <li v-if="!$page.props.user">
              <Link :href="route().has('login') ? route('login') : '/login'" class="hover:text-[#35B8ED] transition-colors duration-200">Log In</Link>
            </li>
          </ul>
        </div>

        <!-- Legal & Hours -->
        <div class="md:col-span-4">
          <h3 class="text-sm font-semibold text-white uppercase tracking-wider mb-4">Clinic Hours & Policy</h3>
          <ul class="space-y-2 text-sm text-slate-300">
            <li class="flex justify-between">
              <span>Mon - Fri:</span>
              <span class="text-white font-medium">8:00 AM - 7:00 PM</span>
            </li>
            <li class="flex justify-between">
              <span>Saturday:</span>
              <span class="text-white font-medium">8:00 AM - 5:00 PM</span>
            </li>
            <li class="flex justify-between">
              <span>Sunday:</span>
              <span class="text-sky-300 font-medium">Emergency On Call</span>
            </li>
          </ul>
        </div>

      </div>

      <!-- Copyright Bottom Bar -->
      <div class="pt-8 flex flex-col sm:flex-row items-center justify-between gap-4 text-xs text-slate-400">
        <p>
          &copy; {{ new Date().getFullYear() }} Dental Clinic. All rights reserved.
        </p>
        <p class="text-slate-400 flex items-center gap-1">
          Made with care for your healthy smile
        </p>
      </div>
    </div>
  </footer>
</template>

<script setup>
import { Link } from '@inertiajs/inertia-vue3'
</script>
