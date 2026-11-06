<template>
  <AppLayout>
    <!-- Modern Hero Section matching the Design Reference -->
    <HeroSection />

    <!-- About Us Section -->
    <section class="py-20 bg-white relative overflow-hidden" id="about-us">
      <div class="container mx-auto px-4 sm:px-6 lg:px-8 max-w-7xl">
        
        <!-- Section Header -->
        <div class="text-center max-w-3xl mx-auto mb-16">
          <div class="inline-flex items-center gap-2 px-3.5 py-1.5 rounded-full bg-[#E1F1FD] border border-[#BCE3FB] text-[#087CC1] text-xs sm:text-sm font-semibold mb-4">
            <span>About Our Practice</span>
          </div>
          <h2 class="text-3xl sm:text-4xl font-extrabold text-[#10234B] tracking-tight mb-4">
            {{ about_us.aboutus_title || 'Dedicated to Your Lifelong Dental Health' }}
          </h2>
          <div class="text-[#5B6E8C] text-base sm:text-lg leading-relaxed">
            <span v-if="about_us.aboutus__first_description" v-html="about_us.aboutus__first_description"></span>
            <span v-else>
              At our clinic, we combine advanced dental technology with compassionate, patient-centered care. From regular checkups to advanced cosmetic transformations, we ensure every visit is relaxing and stress-free.
            </span>
          </div>
        </div>

        <!-- 4 Modern Feature Cards -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 sm:gap-8">
          
          <!-- Card 1 -->
          <div class="p-6 rounded-3xl bg-[#F4F9FF] border border-blue-100/70 hover:border-[#35B8ED] hover:shadow-xl hover:shadow-blue-500/10 transition-all duration-300 group">
            <div class="w-12 h-12 rounded-2xl bg-gradient-to-br from-[#087CC1] to-[#35B8ED] text-white flex items-center justify-center mb-5 shadow-md shadow-blue-500/20 group-hover:scale-110 transition-transform">
              <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
              </svg>
            </div>
            <h3 class="text-lg font-bold text-[#10234B] mb-2">Preventive Care</h3>
            <p class="text-sm text-[#5B6E8C] leading-relaxed">
              Comprehensive dental checkups, gentle cleanings, and digital screenings to protect your healthy smile.
            </p>
          </div>

          <!-- Card 2 -->
          <div class="p-6 rounded-3xl bg-[#F4F9FF] border border-blue-100/70 hover:border-[#35B8ED] hover:shadow-xl hover:shadow-blue-500/10 transition-all duration-300 group">
            <div class="w-12 h-12 rounded-2xl bg-gradient-to-br from-[#087CC1] to-[#35B8ED] text-white flex items-center justify-center mb-5 shadow-md shadow-blue-500/20 group-hover:scale-110 transition-transform">
              <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z" />
              </svg>
            </div>
            <h3 class="text-lg font-bold text-[#10234B] mb-2">Cosmetic Smiles</h3>
            <p class="text-sm text-[#5B6E8C] leading-relaxed">
              In-office teeth whitening, porcelain veneers, and personalized smile design for radiant confidence.
            </p>
          </div>

          <!-- Card 3 -->
          <div class="p-6 rounded-3xl bg-[#F4F9FF] border border-blue-100/70 hover:border-[#35B8ED] hover:shadow-xl hover:shadow-blue-500/10 transition-all duration-300 group">
            <div class="w-12 h-12 rounded-2xl bg-gradient-to-br from-[#087CC1] to-[#35B8ED] text-white flex items-center justify-center mb-5 shadow-md shadow-blue-500/20 group-hover:scale-110 transition-transform">
              <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z" />
              </svg>
            </div>
            <h3 class="text-lg font-bold text-[#10234B] mb-2">Modern Technology</h3>
            <p class="text-sm text-[#5B6E8C] leading-relaxed">
              Low-radiation digital 3D scans, ultrasonic cleanings, and painless treatment protocols.
            </p>
          </div>

          <!-- Card 4 -->
          <div class="p-6 rounded-3xl bg-[#F4F9FF] border border-blue-100/70 hover:border-[#35B8ED] hover:shadow-xl hover:shadow-blue-500/10 transition-all duration-300 group">
            <div class="w-12 h-12 rounded-2xl bg-gradient-to-br from-[#087CC1] to-[#35B8ED] text-white flex items-center justify-center mb-5 shadow-md shadow-blue-500/20 group-hover:scale-110 transition-transform">
              <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.828 14.828a4 4 0 01-5.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
              </svg>
            </div>
            <h3 class="text-lg font-bold text-[#10234B] mb-2">Gentle & Friendly</h3>
            <p class="text-sm text-[#5B6E8C] leading-relaxed">
              Warm atmosphere and caring staff ensuring anxiety-free visits for adults and children alike.
            </p>
          </div>

        </div>

      </div>
    </section>

    <!-- Trusted By Section -->
    <section v-if="client_lists && client_lists.length > 0" class="py-16 bg-[#F4F9FF]" id="trusted">
      <div class="container mx-auto px-4 sm:px-6 lg:px-8 max-w-7xl">
        
        <div class="text-center max-w-2xl mx-auto mb-10">
          <h2 class="text-2xl sm:text-3xl font-bold text-[#10234B]">
            Trusted by Leading Healthcare Partners
          </h2>
          <div class="h-1 w-20 bg-gradient-to-r from-[#087CC1] to-[#35B8ED] mx-auto mt-3 rounded-full"></div>
        </div>

        <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 gap-6">
          <div
            v-for="client in client_lists"
            :key="client.id || client.client_name"
            class="bg-white rounded-2xl p-5 border border-blue-50 shadow-sm hover:shadow-md transition-all duration-200 flex flex-col items-center justify-center text-center group"
          >
            <div class="h-20 w-full flex items-center justify-center mb-3">
              <img
                :src="$page.props.image_url + client.client_logo"
                :alt="client.client_name"
                class="max-h-16 max-w-full object-contain filter group-hover:scale-105 transition-transform"
              />
            </div>
            <span class="text-xs font-semibold text-[#10234B] line-clamp-1">
              {{ client.client_name }}
            </span>
          </div>
        </div>

      </div>
    </section>

    <!-- Contact Us & Consultation Section -->
    <section class="py-20 bg-white relative" id="contact">
      <div class="container mx-auto px-4 sm:px-6 lg:px-8 max-w-7xl">
        
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-10 lg:gap-14">
          
          <!-- Left Column: Clinic Contact Info & Hours -->
          <div class="lg:col-span-5 flex flex-col justify-between">
            <div>
              <div class="inline-flex items-center gap-2 px-3.5 py-1.5 rounded-full bg-[#E1F1FD] border border-[#BCE3FB] text-[#087CC1] text-xs sm:text-sm font-semibold mb-4">
                <span>Get In Touch</span>
              </div>
              <h2 class="text-3xl sm:text-4xl font-extrabold text-[#10234B] tracking-tight mb-4">
                We'd love to care for your smile
              </h2>
              <p class="text-[#5B6E8C] text-base leading-relaxed mb-8">
                Reach out to schedule an appointment, ask a question about our procedures, or consult on a dental treatment plan.
              </p>

              <!-- Contact Cards -->
              <div class="space-y-4">
                
                <!-- Address -->
                <div class="flex items-start gap-4 p-4 rounded-2xl bg-[#F4F9FF] border border-blue-100/70">
                  <div class="w-10 h-10 rounded-xl bg-white text-[#087CC1] flex items-center justify-center shadow-sm flex-shrink-0">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                    </svg>
                  </div>
                  <div>
                    <h4 class="text-xs uppercase tracking-wider font-bold text-[#5B6E8C]">Clinic Location</h4>
                    <p class="text-sm font-semibold text-[#10234B] mt-0.5">
                      {{ contact_forms.contact_company_address || 'Phnom Penh, Cambodia' }}
                    </p>
                  </div>
                </div>

                <!-- Phone -->
                <div class="flex items-start gap-4 p-4 rounded-2xl bg-[#F4F9FF] border border-blue-100/70">
                  <div class="w-10 h-10 rounded-xl bg-white text-[#087CC1] flex items-center justify-center shadow-sm flex-shrink-0">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" />
                    </svg>
                  </div>
                  <div>
                    <h4 class="text-xs uppercase tracking-wider font-bold text-[#5B6E8C]">Phone Contact</h4>
                    <p class="text-sm font-semibold text-[#10234B] mt-0.5">
                      {{ contact_forms.contact_company_phone || '+855 12 345 678' }}
                    </p>
                  </div>
                </div>

                <!-- Email -->
                <div class="flex items-start gap-4 p-4 rounded-2xl bg-[#F4F9FF] border border-blue-100/70">
                  <div class="w-10 h-10 rounded-xl bg-white text-[#087CC1] flex items-center justify-center shadow-sm flex-shrink-0">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                    </svg>
                  </div>
                  <div>
                    <h4 class="text-xs uppercase tracking-wider font-bold text-[#5B6E8C]">Email Address</h4>
                    <p class="text-sm font-semibold text-[#10234B] mt-0.5">
                      {{ contact_forms.contact_company_email || 'contact@dentalclinic.com' }}
                    </p>
                  </div>
                </div>

              </div>
            </div>

            <!-- Social Links -->
            <div class="pt-8">
              <span class="text-xs uppercase tracking-wider font-bold text-[#5B6E8C] block mb-3">Connect With Us</span>
              <div class="flex items-center gap-3">
                <a
                  v-if="contact_forms.contact_facebook_link"
                  :href="contact_forms.contact_facebook_link"
                  target="_blank"
                  class="w-10 h-10 rounded-full bg-[#E1F1FD] text-[#087CC1] hover:bg-[#087CC1] hover:text-white transition-colors duration-200 flex items-center justify-center shadow-sm"
                  title="Facebook"
                >
                  <svg class="w-4 h-4 fill-current" viewBox="0 0 320 512"><path d="M279.14 288l14.22-92.66h-88.91v-60.13c0-25.35 12.42-50.06 52.24-50.06h40.42V6.26S260.43 0 225.36 0c-73.22 0-121.08 44.38-121.08 124.72v70.62H22.89V288h81.39v224h100.17V288z" /></svg>
                </a>
                <a
                  v-if="contact_forms.contact_telegram_link"
                  :href="contact_forms.contact_telegram_link"
                  target="_blank"
                  class="w-10 h-10 rounded-full bg-[#E1F1FD] text-[#087CC1] hover:bg-[#087CC1] hover:text-white transition-colors duration-200 flex items-center justify-center shadow-sm"
                  title="Telegram"
                >
                  <svg class="w-4 h-4 fill-current" viewBox="0 0 496 512"><path d="M248,8C111.033,8,0,119.033,0,256S111.033,504,248,504,496,392.967,496,256,384.967,8,248,8ZM362.952,176.66c-3.732,39.215-19.881,134.378-28.1,178.3-3.476,18.584-10.322,24.816-16.948,25.425-14.4,1.326-25.338-9.517-39.287-18.661-21.827-14.308-34.158-23.215-55.346-37.177-24.485-16.135-8.612-25,5.342-39.5,3.652-3.793,67.107-61.51,68.335-66.746.153-.655.3-3.1-1.154-4.384s-3.59-.849-5.135-.5q-3.283.746-104.608,69.142-14.845,10.194-26.894,9.934c-8.855-.191-25.888-5.006-38.551-9.123-15.531-5.048-27.875-7.717-26.8-16.291q.84-6.7,18.45-13.7,108.446-47.248,144.628-62.3c68.872-28.647,83.183-33.623,92.511-33.789,2.052-.034,6.639.474,9.61,2.885a10.452,10.452,0,0,1,3.53,6.716A43.765,43.765,0,0,1,362.952,176.66Z"/></svg>
                </a>
                <a
                  v-if="contact_forms.contact_linkedin_link"
                  :href="contact_forms.contact_linkedin_link"
                  target="_blank"
                  class="w-10 h-10 rounded-full bg-[#E1F1FD] text-[#087CC1] hover:bg-[#087CC1] hover:text-white transition-colors duration-200 flex items-center justify-center shadow-sm"
                  title="LinkedIn"
                >
                  <svg class="w-4 h-4 fill-current" viewBox="0 0 448 512"><path d="M100.28 448H7.4V148.9h92.88zM53.79 108.1C24.09 108.1 0 83.5 0 53.8a53.79 53.79 0 0 1 107.58 0c0 29.7-24.1 54.3-53.79 54.3zM447.9 448h-92.68V302.4c0-34.7-.7-79.2-48.29-79.2-48.29 0-55.69 37.7-55.69 76.7V448h-92.78V148.9h89.08v40.8h1.3c12.4-23.5 42.69-48.3 87.88-48.3 94 0 111.28 61.9 111.28 142.3V448z" /></svg>
                </a>
              </div>
            </div>

          </div>

          <!-- Right Column: Consultation / Contact Form Card -->
          <div class="lg:col-span-7">
            <div class="bg-[#F4F9FF] rounded-3xl p-6 sm:p-10 border border-blue-100 shadow-xl shadow-blue-500/5">
              
              <h3 class="text-2xl font-bold text-[#10234B] mb-2">Book a Consultation</h3>
              <p class="text-sm text-[#5B6E8C] mb-6">
                Fill in your details below and our clinic coordinator will contact you promptly.
              </p>

              <form @submit.prevent="submitContactForm" class="space-y-4">
                
                <!-- Full Name -->
                <div>
                  <label class="block text-xs font-bold uppercase tracking-wider text-[#10234B] mb-1.5">Full Name</label>
                  <input
                    type="text"
                    v-model="contactForm.full_name"
                    required
                    placeholder="e.g. John Doe"
                    class="w-full px-4 py-3 rounded-xl bg-white border border-blue-200 text-[#10234B] text-sm focus:outline-none focus:ring-2 focus:ring-[#35B8ED] focus:border-transparent transition-all placeholder:text-gray-400"
                  />
                </div>

                <!-- Email & Phone Grid -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                  <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-[#10234B] mb-1.5">Email Address</label>
                    <input
                      type="email"
                      v-model="contactForm.email_address"
                      required
                      placeholder="john@example.com"
                      class="w-full px-4 py-3 rounded-xl bg-white border border-blue-200 text-[#10234B] text-sm focus:outline-none focus:ring-2 focus:ring-[#35B8ED] focus:border-transparent transition-all placeholder:text-gray-400"
                    />
                  </div>
                  <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-[#10234B] mb-1.5">Phone Number</label>
                    <input
                      type="tel"
                      v-model="contactForm.phone_number"
                      required
                      placeholder="+855 12 345 678"
                      class="w-full px-4 py-3 rounded-xl bg-white border border-blue-200 text-[#10234B] text-sm focus:outline-none focus:ring-2 focus:ring-[#35B8ED] focus:border-transparent transition-all placeholder:text-gray-400"
                    />
                  </div>
                </div>

                <!-- Message / Request -->
                <div>
                  <label class="block text-xs font-bold uppercase tracking-wider text-[#10234B] mb-1.5">Service or Message</label>
                  <textarea
                    rows="4"
                    v-model="contactForm.message"
                    required
                    placeholder="Tell us what service you are looking for (e.g. Cleaning, Whitening, Braces, Toothache)..."
                    class="w-full px-4 py-3 rounded-xl bg-white border border-blue-200 text-[#10234B] text-sm focus:outline-none focus:ring-2 focus:ring-[#35B8ED] focus:border-transparent transition-all placeholder:text-gray-400 resize-none"
                  ></textarea>
                </div>

                <!-- Submit Button -->
                <button
                  type="submit"
                  class="w-full py-4 px-6 rounded-2xl text-white font-semibold text-base bg-gradient-to-r from-[#087CC1] to-[#35B8ED] shadow-lg shadow-blue-500/25 hover:shadow-xl hover:shadow-blue-500/35 hover:scale-[1.01] active:scale-[0.99] transition-all duration-200 cursor-pointer"
                >
                  Send Consultation Request
                </button>

                <p v-if="formSubmitted" class="text-center text-sm font-semibold text-emerald-600 pt-2">
                  Thank you! We have received your inquiry and will be in touch shortly.
                </p>

              </form>

            </div>
          </div>

        </div>

      </div>
    </section>

    <!-- Google Map Embed Container -->
    <div class="w-full bg-[#F4F9FF] pb-16">
      <div class="container mx-auto px-4 sm:px-6 lg:px-8 max-w-7xl">
        <div class="rounded-3xl overflow-hidden border border-blue-100 shadow-md">
          <iframe
            src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3797.651137453154!2d104.89458865655278!3d11.556819802460943!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x3109510826b52179%3A0x1841e2bd834cd3af!2sFirst+Womentech+Asia+Co.%2CLtd!5e0!3m2!1sen!2skh!4v1548929607585"
            width="100%"
            height="380"
            style="border:0;"
            allowfullscreen=""
            loading="lazy"
          ></iframe>
        </div>
      </div>
    </div>

  </AppLayout>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import axios from 'axios'
import AppLayout from '@/Layouts/AppLayout.vue'
import HeroSection from '@/Components/Dental/HeroSection.vue'

const props = defineProps({
  open: [Boolean, String],
  image_url: String,
  config_api_url: String,
  other_settings: [Object, Array],
  home_page: Object,
  users: [Object, Array],
})

const about_us = ref({})
const other_settings_data = ref([])
const client_lists = ref([])
const contact_forms = ref({})

const contactForm = ref({
  full_name: '',
  email_address: '',
  phone_number: '',
  message: '',
})

const formSubmitted = ref(false)

function submitContactForm() {
  formSubmitted.value = true
  setTimeout(() => {
    contactForm.value = {
      full_name: '',
      email_address: '',
      phone_number: '',
      message: '',
    }
  }, 3000)
}

function isValidApiUrl(url) {
  return typeof url === 'string' && url.length > 0 && url !== 'undefined' && url.startsWith('http')
}

function fetchAboutUs() {
  if (!isValidApiUrl(props.config_api_url)) return
  axios.get(props.config_api_url + '/about-us')
    .then(response => {
      if (typeof response.data === 'string') {
        about_us.value = JSON.parse(response.data)
      } else {
        about_us.value = response.data || {}
      }
    })
    .catch(() => {})
}

function fetchOtherSettings() {
  if (!isValidApiUrl(props.config_api_url)) return
  axios.get(props.config_api_url + '/other-settings')
    .then(response => {
      if (typeof response.data === 'string') {
        other_settings_data.value = JSON.parse(response.data)
      } else {
        other_settings_data.value = response.data || []
      }
    })
    .catch(() => {})
}

function fetchClientList() {
  if (!isValidApiUrl(props.config_api_url)) return
  axios.get(props.config_api_url + '/client-list')
    .then(response => {
      client_lists.value = response.data?.data || []
    })
    .catch(() => {})
}

function fetchContactForm() {
  if (!isValidApiUrl(props.config_api_url)) return
  axios.get(props.config_api_url + '/contact-us')
    .then(response => {
      if (typeof response.data === 'string') {
        contact_forms.value = JSON.parse(response.data)
      } else {
        contact_forms.value = response.data || {}
      }
    })
    .catch(() => {})
}

onMounted(() => {
  fetchAboutUs()
  fetchOtherSettings()
  fetchClientList()
  fetchContactForm()
})
</script>
