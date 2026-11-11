<template>
  <TenantLayout>
    <Head :title="tm('Create_User')" />
    <div class="px-4 py-4">
      <!-- Breadcrumb Navigation -->
      <nav class="flex pt-4 mb-6" aria-label="Breadcrumb">
        <ol class="inline-flex items-center space-x-1 md:space-x-3">
          <li class="inline-flex items-center">
            <Link :href="route('tenant.dashboard')" class="inline-flex items-center text-sm font-medium text-gray-700 hover:text-gray-900 dark:text-gray-400 dark:hover:text-white">
              <svg class="mr-2 w-4 h-4" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                <path d="M10.707 2.293a1 1 0 00-1.414 0l-7 7a1 1 0 001.414 1.414L4 10.414V17a1 1 0 001 1h2a1 1 0 001-1v-2a1 1 0 011-1h2a1 1 0 011 1v2a1 1 0 001 1h2a1 1 0 001-1v-6.586l.293.293a1 1 0 001.414-1.414l-7-7z"></path>
              </svg>
              {{ tm('Home') }}
            </Link>
          </li>
          <li>
            <div class="flex items-center">
              <svg class="w-6 h-6 text-gray-400" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path>
              </svg>
              <Link :href="route('users.index')" class="ml-1 text-sm font-medium text-gray-700 hover:text-gray-900 md:ml-2 dark:text-gray-400 dark:hover:text-white">
                {{ tm('Users') }}
              </Link>
            </div>
          </li>
          <li>
            <div class="flex items-center">
              <svg class="w-6 h-6 text-gray-400" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path>
              </svg>
              <span class="ml-1 text-sm font-medium text-gray-700 hover:text-gray-900 md:ml-2 dark:text-gray-400 dark:hover:text-white">
                {{ tm('Create') }}
              </span>
            </div>
          </li>
        </ol>
      </nav>

      <div class="mb-4">
        <h3 class="text-xl font-normal text-gray-800 dark:text-light">
          {{ tm('Create_User') }}
        </h3>
      </div>

      <form @submit.prevent="submit" class="space-y-6">
        <!-- SECTION 1: Authentication -->
        <div class="bg-white rounded-md shadow-sm border border-gray-200 dark:bg-darker dark:border-primary p-6">
          <h5 class="text-sm font-semibold text-gray-700 dark:text-gray-200 mb-2">
            {{ tm('Authentication') }}
          </h5>
          <hr class="border-gray-200 dark:border-gray-700 mb-6" />

          <div class="space-y-4 max-w-4xl">
            <!-- User name -->
            <div class="flex flex-col sm:flex-row sm:items-center">
              <label for="username" class="w-full sm:w-44 md:w-52 text-left sm:text-right pr-6 font-medium text-xs sm:text-sm text-gray-700 dark:text-gray-300 shrink-0">
                {{ tm('User_Name') }} <span class="text-red-500 font-bold">*</span>
              </label>
              <div class="flex-1 w-full mt-1 sm:mt-0">
                <input
                  id="username"
                  v-model="form.username"
                  type="text"
                  required
                  class="w-full rounded border-gray-300 shadow-sm focus:border-primary focus:ring focus:ring-primary focus:ring-opacity-50 text-sm dark:bg-dark dark:border-gray-600 dark:text-white"
                  :class="{ 'border-red-500 focus:border-red-500 ring-1 ring-red-500': form.errors.username }"
                />
                <div v-if="form.errors.username" class="text-xs text-red-500 mt-1 font-semibold">
                  {{ form.errors.username }}
                </div>
              </div>
            </div>

            <!-- Email -->
            <div class="flex flex-col sm:flex-row sm:items-center">
              <label for="email" class="w-full sm:w-44 md:w-52 text-left sm:text-right pr-6 font-medium text-xs sm:text-sm text-gray-700 dark:text-gray-300 shrink-0">
                {{ tm('Email') }} <span class="text-red-500 font-bold">*</span>
              </label>
              <div class="flex-1 w-full mt-1 sm:mt-0">
                <input
                  id="email"
                  v-model="form.email"
                  type="email"
                  required
                  class="w-full rounded border-gray-300 shadow-sm focus:border-primary focus:ring focus:ring-primary focus:ring-opacity-50 text-sm dark:bg-dark dark:border-gray-600 dark:text-white"
                  :class="{ 'border-red-500 focus:border-red-500 ring-1 ring-red-500': form.errors.email }"
                />
                <div v-if="form.errors.email" class="text-xs text-red-500 mt-1 font-semibold">
                  {{ form.errors.email }}
                </div>
              </div>
            </div>

            <!-- Role -->
            <div class="flex flex-col sm:flex-row sm:items-center">
              <label for="role" class="w-full sm:w-44 md:w-52 text-left sm:text-right pr-6 font-medium text-xs sm:text-sm text-gray-700 dark:text-gray-300 shrink-0">
                {{ tm('Role') }}
              </label>
              <div class="flex-1 w-full mt-1 sm:mt-0">
                <select
                  id="role"
                  v-model="form.role"
                  class="w-full rounded border-gray-300 shadow-sm focus:border-primary focus:ring focus:ring-primary focus:ring-opacity-50 text-sm dark:bg-dark dark:border-gray-600 dark:text-white"
                >
                  <option :value="null">-- {{ tm('Select_Role') || 'Select Role' }} --</option>
                  <option v-for="(name, key) in roles" :key="key" :value="name">
                    {{ name }}
                  </option>
                </select>
                <div v-if="form.errors.role" class="text-xs text-red-500 mt-1 font-semibold">
                  {{ form.errors.role }}
                </div>
              </div>
            </div>

            <!-- Status -->
            <div class="flex flex-col sm:flex-row sm:items-center">
              <label class="w-full sm:w-44 md:w-52 text-left sm:text-right pr-6 font-medium text-xs sm:text-sm text-gray-700 dark:text-gray-300 shrink-0">
                {{ tm('Status') }}
              </label>
              <div class="flex-1 w-full mt-1 sm:mt-0 flex items-center gap-3">
                <label class="relative inline-flex items-center cursor-pointer">
                  <input
                    type="checkbox"
                    v-model="form.active"
                    class="sr-only peer"
                  />
                  <div class="w-9 h-5 bg-gray-200 peer-focus:outline-none rounded-full peer dark:bg-gray-700 peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-4 after:w-4 after:transition-all dark:border-gray-600 peer-checked:bg-primary"></div>
                </label>
                <span class="text-xs font-semibold text-gray-700 dark:text-gray-300">
                  {{ form.active ? tm('Active') : tm('Inactive') }}
                </span>
              </div>
            </div>
          </div>
        </div>

        <!-- SECTION 2: e-Health Yoeung -->
        <div class="bg-white rounded-md shadow-sm border border-gray-200 dark:bg-darker dark:border-primary p-6">
          <h5 class="text-sm font-semibold text-gray-700 dark:text-gray-200 mb-2">
            {{ tm('eHealth_Yoeung') || 'e-Health Yoeung' }}
          </h5>
          <hr class="border-gray-200 dark:border-gray-700 mb-6" />

          <div class="space-y-4 max-w-4xl">
            <!-- Is Public -->
            <div class="flex flex-col sm:flex-row sm:items-center">
              <label class="w-full sm:w-44 md:w-52 text-left sm:text-right pr-6 font-medium text-xs sm:text-sm text-gray-700 dark:text-gray-300 shrink-0">
                {{ tm('Is_Public') || 'Is Public' }}
              </label>
              <div class="flex-1 w-full mt-1 sm:mt-0">
                <input
                  type="checkbox"
                  v-model="form.is_publish"
                  class="rounded border-gray-300 text-primary shadow-sm focus:border-primary focus:ring focus:ring-primary focus:ring-opacity-50 h-4 w-4"
                />
              </div>
            </div>

            <!-- Enable Appointment -->
            <div class="flex flex-col sm:flex-row sm:items-center">
              <label class="w-full sm:w-44 md:w-52 text-left sm:text-right pr-6 font-medium text-xs sm:text-sm text-gray-700 dark:text-gray-300 shrink-0">
                {{ tm('Enable_Appointment') || 'Enable Appointment' }}
              </label>
              <div class="flex-1 w-full mt-1 sm:mt-0">
                <input
                  type="checkbox"
                  v-model="form.ehealth_is_doctor_appointment"
                  class="rounded border-gray-300 text-primary shadow-sm focus:border-primary focus:ring focus:ring-primary focus:ring-opacity-50 h-4 w-4"
                />
              </div>
            </div>

            <!-- Appointment Type -->
            <div class="flex flex-col sm:flex-row sm:items-start pt-1">
              <label class="w-full sm:w-44 md:w-52 text-left sm:text-right pr-6 font-medium text-xs sm:text-sm text-gray-700 dark:text-gray-300 shrink-0 pt-2">
                {{ tm('Appointment_Type') || 'Appointment Type' }}
              </label>
              <div class="flex-1 w-full mt-1 sm:mt-0 space-y-3">
                <!-- Option 1: Video Consult -->
                <div class="grid grid-cols-1 md:grid-cols-12 gap-3 items-center">
                  <div class="md:col-span-5 flex items-center gap-2">
                    <input
                      id="opt-video"
                      type="checkbox"
                      v-model="form.consult_video"
                      class="rounded border-gray-300 text-primary shadow-sm focus:border-primary focus:ring focus:ring-primary focus:ring-opacity-50 h-4 w-4"
                    />
                    <label for="opt-video" class="text-xs sm:text-sm text-gray-700 dark:text-gray-300 cursor-pointer">
                      {{ tm('Video_Consult') || 'Video Consult' }}
                    </label>
                  </div>
                  <div class="md:col-span-2 text-left md:text-right">
                    <span class="text-xs sm:text-sm font-medium text-gray-600 dark:text-gray-400">
                      {{ tm('Fee_dollar') || 'Fee ($):' }}
                    </span>
                  </div>
                  <div class="md:col-span-5">
                    <input
                      type="number"
                      step="0.01"
                      min="0"
                      v-model="form.consult_video_fee"
                      placeholder="0.00"
                      class="w-full rounded border-gray-300 shadow-sm focus:border-primary focus:ring focus:ring-primary focus:ring-opacity-50 text-sm dark:bg-dark dark:border-gray-600 dark:text-white"
                    />
                  </div>
                </div>

                <!-- Option 2: Visit Consult -->
                <div class="grid grid-cols-1 md:grid-cols-12 gap-3 items-center">
                  <div class="md:col-span-5 flex items-center gap-2">
                    <input
                      id="opt-visit"
                      type="checkbox"
                      v-model="form.consult_visit"
                      class="rounded border-gray-300 text-primary shadow-sm focus:border-primary focus:ring focus:ring-primary focus:ring-opacity-50 h-4 w-4"
                    />
                    <label for="opt-visit" class="text-xs sm:text-sm text-gray-700 dark:text-gray-300 cursor-pointer">
                      {{ tm('Visit_Consult') || 'Visit Consult' }}
                    </label>
                  </div>
                  <div class="md:col-span-2 text-left md:text-right">
                    <span class="text-xs sm:text-sm font-medium text-gray-600 dark:text-gray-400">
                      {{ tm('Fee_dollar') || 'Fee ($):' }}
                    </span>
                  </div>
                  <div class="md:col-span-5">
                    <input
                      type="number"
                      step="0.01"
                      min="0"
                      v-model="form.consult_visit_fee"
                      placeholder="0.00"
                      class="w-full rounded border-gray-300 shadow-sm focus:border-primary focus:ring focus:ring-primary focus:ring-opacity-50 text-sm dark:bg-dark dark:border-gray-600 dark:text-white"
                    />
                  </div>
                </div>

                <!-- Option 3: House Call -->
                <div class="grid grid-cols-1 md:grid-cols-12 gap-3 items-center">
                  <div class="md:col-span-5 flex items-center gap-2">
                    <input
                      id="opt-house"
                      type="checkbox"
                      v-model="form.consult_house"
                      class="rounded border-gray-300 text-primary shadow-sm focus:border-primary focus:ring focus:ring-primary focus:ring-opacity-50 h-4 w-4"
                    />
                    <label for="opt-house" class="text-xs sm:text-sm text-gray-700 dark:text-gray-300 cursor-pointer">
                      {{ tm('House_Call') || 'House Call' }}
                    </label>
                  </div>
                  <div class="md:col-span-2 text-left md:text-right">
                    <span class="text-xs sm:text-sm font-medium text-gray-600 dark:text-gray-400">
                      {{ tm('Fee_dollar') || 'Fee ($):' }}
                    </span>
                  </div>
                  <div class="md:col-span-5">
                    <input
                      type="number"
                      step="0.01"
                      min="0"
                      v-model="form.consult_house_fee"
                      placeholder="0.00"
                      class="w-full rounded border-gray-300 shadow-sm focus:border-primary focus:ring focus:ring-primary focus:ring-opacity-50 text-sm dark:bg-dark dark:border-gray-600 dark:text-white"
                    />
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- SECTION 3: User Profile -->
        <div class="bg-white rounded-md shadow-sm border border-gray-200 dark:bg-darker dark:border-primary overflow-hidden">
          <!-- Blue Header Banner matching screenshot -->
          <div class="bg-[#5b86e5] text-white px-6 py-3 font-semibold text-sm">
            {{ tm('User_Profile') }}
          </div>

          <div class="p-6 space-y-4 max-w-4xl">
            <!-- First Name -->
            <div class="flex flex-col sm:flex-row sm:items-center">
              <label for="firstname" class="w-full sm:w-44 md:w-52 text-left sm:text-right pr-6 font-medium text-xs sm:text-sm text-gray-700 dark:text-gray-300 shrink-0">
                {{ tm('First_Name') }} <span class="text-red-500 font-bold">*</span>
              </label>
              <div class="flex-1 w-full mt-1 sm:mt-0">
                <input
                  id="firstname"
                  v-model="form.firstname"
                  type="text"
                  required
                  class="w-full rounded border-gray-300 shadow-sm focus:border-primary focus:ring focus:ring-primary focus:ring-opacity-50 text-sm dark:bg-dark dark:border-gray-600 dark:text-white"
                  :class="{ 'border-red-500 focus:border-red-500 ring-1 ring-red-500': form.errors.firstname }"
                />
                <div v-if="form.errors.firstname" class="text-xs text-red-500 mt-1 font-semibold">
                  {{ form.errors.firstname }}
                </div>
              </div>
            </div>

            <!-- Last Name -->
            <div class="flex flex-col sm:flex-row sm:items-center">
              <label for="lastname" class="w-full sm:w-44 md:w-52 text-left sm:text-right pr-6 font-medium text-xs sm:text-sm text-gray-700 dark:text-gray-300 shrink-0">
                {{ tm('Last_Name') }} <span class="text-red-500 font-bold">*</span>
              </label>
              <div class="flex-1 w-full mt-1 sm:mt-0">
                <input
                  id="lastname"
                  v-model="form.lastname"
                  type="text"
                  required
                  class="w-full rounded border-gray-300 shadow-sm focus:border-primary focus:ring focus:ring-primary focus:ring-opacity-50 text-sm dark:bg-dark dark:border-gray-600 dark:text-white"
                  :class="{ 'border-red-500 focus:border-red-500 ring-1 ring-red-500': form.errors.lastname }"
                />
                <div v-if="form.errors.lastname" class="text-xs text-red-500 mt-1 font-semibold">
                  {{ form.errors.lastname }}
                </div>
              </div>
            </div>

            <!-- Gender -->
            <div class="flex flex-col sm:flex-row sm:items-center">
              <label class="w-full sm:w-44 md:w-52 text-left sm:text-right pr-6 font-medium text-xs sm:text-sm text-gray-700 dark:text-gray-300 shrink-0">
                {{ tm('Gender') }} :
              </label>
              <div class="flex-1 w-full mt-1 sm:mt-0 flex items-center gap-6">
                <label class="inline-flex items-center gap-2 cursor-pointer">
                  <input
                    type="radio"
                    v-model="form.gender"
                    value="M"
                    class="text-primary border-gray-300 focus:ring-primary h-4 w-4"
                  />
                  <span class="text-xs sm:text-sm text-gray-700 dark:text-gray-300">{{ tm('Male') }}</span>
                </label>
                <label class="inline-flex items-center gap-2 cursor-pointer">
                  <input
                    type="radio"
                    v-model="form.gender"
                    value="F"
                    class="text-primary border-gray-300 focus:ring-primary h-4 w-4"
                  />
                  <span class="text-xs sm:text-sm text-gray-700 dark:text-gray-300">{{ tm('Female') }}</span>
                </label>
              </div>
            </div>

            <!-- Date of Birth -->
            <div class="flex flex-col sm:flex-row sm:items-center">
              <label class="w-full sm:w-44 md:w-52 text-left sm:text-right pr-6 font-medium text-xs sm:text-sm text-gray-700 dark:text-gray-300 shrink-0">
                {{ tm('Date_of_Birth') }}
              </label>
              <div class="flex-1 w-full mt-1 sm:mt-0">
                <date-input
                  v-model="form.date_of_birth"
                  :error="form.errors.date_of_birth"
                  placeholder="YYYY-MM-DD"
                  class="w-full"
                />
              </div>
            </div>

            <!-- Phone 1 -->
            <div class="flex flex-col sm:flex-row sm:items-center">
              <label for="primary_mobile" class="w-full sm:w-44 md:w-52 text-left sm:text-right pr-6 font-medium text-xs sm:text-sm text-gray-700 dark:text-gray-300 shrink-0">
                {{ tm('Phone_1') || 'Phone 1' }}
              </label>
              <div class="flex-1 w-full mt-1 sm:mt-0">
                <input
                  id="primary_mobile"
                  v-model="form.primary_mobile"
                  type="text"
                  class="w-full rounded border-gray-300 shadow-sm focus:border-primary focus:ring focus:ring-primary focus:ring-opacity-50 text-sm dark:bg-dark dark:border-gray-600 dark:text-white"
                />
              </div>
            </div>

            <!-- Phone 2 -->
            <div class="flex flex-col sm:flex-row sm:items-center">
              <label for="secondary_mobile" class="w-full sm:w-44 md:w-52 text-left sm:text-right pr-6 font-medium text-xs sm:text-sm text-gray-700 dark:text-gray-300 shrink-0">
                {{ tm('Phone_2') || 'Phone 2' }}
              </label>
              <div class="flex-1 w-full mt-1 sm:mt-0">
                <input
                  id="secondary_mobile"
                  v-model="form.secondary_mobile"
                  type="text"
                  class="w-full rounded border-gray-300 shadow-sm focus:border-primary focus:ring focus:ring-primary focus:ring-opacity-50 text-sm dark:bg-dark dark:border-gray-600 dark:text-white"
                />
              </div>
            </div>

            <!-- Avatar -->
            <div class="flex flex-col sm:flex-row sm:items-start pt-1">
              <label class="w-full sm:w-44 md:w-52 text-left sm:text-right pr-6 font-medium text-xs sm:text-sm text-gray-700 dark:text-gray-300 shrink-0 pt-2">
                {{ tm('Avatar') }}
              </label>
              <div class="flex-1 w-full mt-1 sm:mt-0">
                <!-- Dropzone Box exactly like screenshot -->
                <div
                  @click="triggerFileInput"
                  @dragover.prevent="isDragging = true"
                  @dragleave.prevent="isDragging = false"
                  @drop.prevent="handleFileDrop"
                  class="border-2 border-dashed rounded-md p-8 flex flex-col items-center justify-center relative cursor-pointer transition min-h-[140px]"
                  :class="[
                    isDragging ? 'border-primary bg-primary/5' : 'border-gray-300 dark:border-gray-600 bg-white dark:bg-dark hover:bg-gray-50/50'
                  ]"
                >
                  <!-- Close / Clear icon at top right if avatar present -->
                  <button
                    v-if="avatarPreview"
                    type="button"
                    @click.stop="removeAvatar"
                    class="absolute top-2 right-2 text-gray-400 hover:text-red-500 transition p-1"
                    title="Remove avatar"
                  >
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                  </button>

                  <!-- Preview if selected -->
                  <div v-if="avatarPreview" class="flex flex-col items-center gap-2">
                    <img :src="avatarPreview" alt="Avatar preview" class="w-20 h-20 rounded-full object-cover border border-gray-200 shadow-sm" />
                    <span class="text-xs text-gray-500">Click or drag new image to replace</span>
                  </div>

                  <!-- Empty state text matching screenshot -->
                  <div v-else class="text-center">
                    <p class="text-sm text-gray-400 dark:text-gray-500">
                      Drag & drop files here ...
                    </p>
                  </div>

                  <input
                    ref="fileInput"
                    type="file"
                    accept="image/jpeg,image/png,image/jpg,image/webp,image/svg+xml"
                    @change="handleAvatarChange"
                    class="hidden"
                  />
                </div>
                <div v-if="form.errors.avatar" class="text-xs text-red-500 mt-1 font-semibold">
                  {{ form.errors.avatar }}
                </div>
              </div>
            </div>
          </div>

          <!-- Footer Actions -->
          <div class="flex items-center justify-end px-8 py-4 bg-gray-50 border-t border-gray-100 dark:bg-darker dark:border-primary space-x-2">
            <save-button :loading="form.processing">
              {{ tm('Save') }}
            </save-button>
            <back-button :url="route('users.index')">
              {{ tm('Cancel') }}
            </back-button>
          </div>
        </div>
      </form>
    </div>
  </TenantLayout>
</template>

<script>
import { defineComponent } from 'vue'
import { Head, Link } from '@inertiajs/inertia-vue3'
import TenantLayout from '@/Layouts/TenantLayout.vue'
import DateInput from '@/Components/Forms/DateInput.vue'
import SaveButton from '@/Components/Button/Save.vue'
import BackButton from '@/Components/Button/Back.vue'
import { useToast } from 'vue-toastification'
import { useI18n } from 'vue-i18n'
import { lang } from '@/Lang'

export default defineComponent({
  components: {
    TenantLayout,
    Head,
    Link,
    DateInput,
    SaveButton,
    BackButton,
  },
  props: {
    roles: Object,
  },
  setup() {
    const { tm } = useI18n({
      inheritLocale: true,
      messages: lang,
    })
    const toast = useToast()
    return { tm, toast }
  },
  data() {
    return {
      isDragging: false,
      avatarPreview: null,
      form: this.$inertia.form({
        // Section 1: Authentication
        username: '',
        email: '',
        role: null,
        active: true,

        // Section 2: e-Health Yoeung
        is_publish: false,
        ehealth_is_doctor_appointment: false,
        consult_video: false,
        consult_video_fee: '',
        consult_visit: false,
        consult_visit_fee: '',
        consult_house: false,
        consult_house_fee: '',
        ehealth_enable_video_consult: false,
        ehealth_video_consult_fee: '',
        ehealth_enable_visit_consult: false,
        ehealth_visit_consult_fee: '',
        ehealth_enable_house_call: false,
        ehealth_house_call_fee: '',

        // Section 3: User Profile
        firstname: '',
        lastname: '',
        gender: 'F',
        date_of_birth: '',
        primary_mobile: '',
        secondary_mobile: '',
        avatar: null,
        remove_avatar: false,
      }),
    }
  },
  methods: {
    triggerFileInput() {
      this.$refs.fileInput.click()
    },
    handleAvatarChange(e) {
      const file = e.target.files?.[0]
      if (file) {
        this.setAvatarFile(file)
      }
    },
    handleFileDrop(e) {
      this.isDragging = false
      const file = e.dataTransfer.files?.[0]
      if (file) {
        this.setAvatarFile(file)
      }
    },
    setAvatarFile(file) {
      this.form.avatar = file
      this.form.remove_avatar = false
      this.avatarPreview = URL.createObjectURL(file)
    },
    removeAvatar() {
      this.form.avatar = null
      this.form.remove_avatar = true
      this.avatarPreview = null
      if (this.$refs.fileInput) {
        this.$refs.fileInput.value = ''
      }
    },
    submit() {
      this.form.ehealth_enable_video_consult = !!this.form.consult_video
      this.form.ehealth_video_consult_fee = this.form.consult_video_fee || ''
      this.form.ehealth_enable_visit_consult = !!this.form.consult_visit
      this.form.ehealth_visit_consult_fee = this.form.consult_visit_fee || ''
      this.form.ehealth_enable_house_call = !!this.form.consult_house
      this.form.ehealth_house_call_fee = this.form.consult_house_fee || ''

      this.form.post(route('users.store'), {
        preserveScroll: true,
        onSuccess: () => {
          this.toast.success(this.tm('User_created_successfully'))
        },
        onError: (errors) => {
          if (errors) {
            Object.values(errors).flat().forEach((msg) => {
              if (typeof msg === 'string') {
                this.toast.error(msg)
              }
            })
          }
        },
      })
    },
  },
})
</script>
