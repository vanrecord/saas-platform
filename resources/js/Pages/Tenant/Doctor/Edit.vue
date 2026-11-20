<template>
  <TenantLayout>
    <Head :title="tm('Edit_Doctor') || 'Edit Doctor'" />
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
              <Link :href="route('doctors.index')" class="ml-1 text-sm font-medium text-gray-700 hover:text-gray-900 md:ml-2 dark:text-gray-400 dark:hover:text-white">
                {{ tm('Doctors') || 'Doctors' }}
              </Link>
            </div>
          </li>
          <li>
            <div class="flex items-center">
              <svg class="w-6 h-6 text-gray-400" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path>
              </svg>
              <span class="ml-1 text-sm font-medium text-gray-700 hover:text-gray-900 md:ml-2 dark:text-gray-400 dark:hover:text-white">
                {{ tm('Edit') || 'Edit' }}
              </span>
            </div>
          </li>
        </ol>
      </nav>

      <div class="mb-4">
        <span class="ml-1 text-xl font-medium text-gray-500 md:ml-2">
          {{ tm('Edit_Doctor') || 'Edit Doctor' }}
        </span>
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
                {{ tm('Email') }}
              </label>
              <div class="flex-1 w-full mt-1 sm:mt-0">
                <input
                  id="email"
                  v-model="form.email"
                  type="email"
                  class="w-full rounded border-gray-300 shadow-sm focus:border-primary focus:ring focus:ring-primary focus:ring-opacity-50 text-sm dark:bg-dark dark:border-gray-600 dark:text-white"
                  :class="{ 'border-red-500 focus:border-red-500 ring-1 ring-red-500': form.errors.email }"
                />
                <div v-if="form.errors.email" class="text-xs text-red-500 mt-1 font-semibold">
                  {{ form.errors.email }}
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- SECTION 2: Doctor Profile -->
        <div class="bg-white rounded-md shadow-sm border border-gray-200 dark:bg-darker dark:border-primary overflow-hidden">
          <!-- Blue Header Banner matching User Profile -->
          <div class="bg-[#5b86e5] text-white px-6 py-3 font-semibold text-sm">
            {{ tm('Profile') || 'Profile' }}
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

            <!-- Primary Mobile -->
            <div class="flex flex-col sm:flex-row sm:items-center">
              <label for="primary_mobile" class="w-full sm:w-44 md:w-52 text-left sm:text-right pr-6 font-medium text-xs sm:text-sm text-gray-700 dark:text-gray-300 shrink-0">
                {{ tm('Primary_Mobile') || tm('Phone_1') || 'Primary Mobile' }}
              </label>
              <div class="flex-1 w-full mt-1 sm:mt-0">
                <input
                  id="primary_mobile"
                  v-model="form.primary_mobile"
                  type="text"
                  class="w-full rounded border-gray-300 shadow-sm focus:border-primary focus:ring focus:ring-primary focus:ring-opacity-50 text-sm dark:bg-dark dark:border-gray-600 dark:text-white"
                  :class="{ 'border-red-500 focus:border-red-500 ring-1 ring-red-500': form.errors.primary_mobile }"
                />
                <div v-if="form.errors.primary_mobile" class="text-xs text-red-500 mt-1 font-semibold">
                  {{ form.errors.primary_mobile }}
                </div>
              </div>
            </div>

            <!-- Secondary Mobile -->
            <div class="flex flex-col sm:flex-row sm:items-center">
              <label for="secondary_mobile" class="w-full sm:w-44 md:w-52 text-left sm:text-right pr-6 font-medium text-xs sm:text-sm text-gray-700 dark:text-gray-300 shrink-0">
                {{ tm('Secondary_Mobile') || tm('Phone_2') || 'Secondary Mobile' }}
              </label>
              <div class="flex-1 w-full mt-1 sm:mt-0">
                <input
                  id="secondary_mobile"
                  v-model="form.secondary_mobile"
                  type="text"
                  class="w-full rounded border-gray-300 shadow-sm focus:border-primary focus:ring focus:ring-primary focus:ring-opacity-50 text-sm dark:bg-dark dark:border-gray-600 dark:text-white"
                  :class="{ 'border-red-500 focus:border-red-500 ring-1 ring-red-500': form.errors.secondary_mobile }"
                />
                <div v-if="form.errors.secondary_mobile" class="text-xs text-red-500 mt-1 font-semibold">
                  {{ form.errors.secondary_mobile }}
                </div>
              </div>
            </div>

            <!-- Specialties -->
            <div class="flex flex-col sm:flex-row sm:items-start pt-1">
              <label class="w-full sm:w-44 md:w-52 text-left sm:text-right pr-6 font-medium text-xs sm:text-sm text-gray-700 dark:text-gray-300 shrink-0 pt-2">
                {{ tm('Specialties') || 'Specialties' }}
              </label>
              <div class="flex-1 w-full mt-1 sm:mt-0">
                <div class="border border-gray-300 dark:border-gray-600 rounded p-2 bg-white dark:bg-dark min-h-[42px] flex flex-wrap gap-1.5 items-center">
                  <span
                    v-for="sId in form.specialties"
                    :key="sId"
                    class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-semibold bg-blue-100 text-blue-800 dark:bg-blue-900/50 dark:text-blue-200"
                  >
                    {{ getSpecialtyName(sId) }}
                    <button type="button" @click="toggleSpecialty(sId)" class="hover:text-red-500 font-bold ml-1 cursor-pointer">×</button>
                  </span>
                  <select
                    @change="onSpecialtySelect"
                    class="border-0 text-xs text-gray-500 focus:ring-0 py-1 px-1 bg-transparent cursor-pointer flex-1 min-w-[140px]"
                  >
                    <option value="">+ {{ tm('Add_specialty') || 'Add specialty...' }}</option>
                    <option
                      v-for="sp in specialtyList"
                      :key="sp.id"
                      :value="sp.id"
                      :disabled="form.specialties.includes(sp.id)"
                    >
                      {{ sp.name }}
                    </option>
                  </select>
                </div>
                <div v-if="form.errors.specialties" class="text-xs text-red-500 mt-1 font-semibold">
                  {{ form.errors.specialties }}
                </div>
              </div>
            </div>

            <!-- Description -->
            <div class="flex flex-col sm:flex-row sm:items-start pt-1">
              <label class="w-full sm:w-44 md:w-52 text-left sm:text-right pr-6 font-medium text-xs sm:text-sm text-gray-700 dark:text-gray-300 shrink-0 pt-2">
                {{ tm('Description') || 'Description' }}
              </label>
              <div class="flex-1 w-full mt-1 sm:mt-0">
                <RichTextEditor v-model="form.description" />
                <div v-if="form.errors.description" class="text-xs text-red-500 mt-1 font-semibold">
                  {{ form.errors.description }}
                </div>
              </div>
            </div>

            <!-- Avatar Dropzone matching User Create/Edit -->
            <div class="flex flex-col sm:flex-row sm:items-start pt-1">
              <label class="w-full sm:w-44 md:w-52 text-left sm:text-right pr-6 font-medium text-xs sm:text-sm text-gray-700 dark:text-gray-300 shrink-0 pt-2">
                {{ tm('Avatar') }}
              </label>
              <div class="flex-1 w-full mt-1 sm:mt-0">
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
                  <button
                    v-if="avatarPreview"
                    type="button"
                    @click.stop="removeAvatar"
                    class="absolute top-2 right-2 text-gray-400 hover:text-red-500 transition p-1 cursor-pointer"
                    title="Remove avatar"
                  >
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                  </button>

                  <div v-if="avatarPreview" class="flex flex-col items-center gap-2">
                    <img :src="avatarPreview" alt="Avatar preview" class="w-20 h-20 rounded-full object-cover border border-gray-200 shadow-sm" />
                    <span class="text-xs text-gray-500">Click or drag new image to replace</span>
                  </div>

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

          <!-- Footer Actions matching User Edit -->
          <div class="flex items-center justify-end px-8 py-4 bg-gray-50 border-t border-gray-100 dark:bg-darker dark:border-primary space-x-2">
            <save-button :loading="form.processing">
              {{ tm('Save') }}
            </save-button>
            <back-button :url="route('doctors.index')">
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
import RichTextEditor from '@/Components/Form/RichTextEditor.vue'
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
    RichTextEditor,
    SaveButton,
    BackButton,
  },
  props: {
    doctor: {
      type: Object,
      required: true,
    },
    specialties: {
      type: Array,
      default: () => [],
    },
    specialOptions: {
      type: Array,
      default: () => [],
    },
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
      avatarPreview: this.doctor?.avatar_url || null,
      form: this.$inertia.form({
        _method: 'PUT',
        username: this.doctor?.username || '',
        email: this.doctor?.email || '',
        firstname: this.doctor?.firstname || '',
        lastname: this.doctor?.lastname || '',
        date_of_birth: this.doctor?.date_of_birth || '',
        gender: this.doctor?.gender ? (this.doctor.gender.charAt(0).toUpperCase() === 'M' ? 'M' : 'F') : 'F',
        primary_mobile: this.doctor?.primary_mobile || '',
        secondary_mobile: this.doctor?.secondary_mobile || '',
        specialties: this.doctor?.specialties || [],
        description: this.doctor?.description || '',
        avatar: null,
        remove_avatar: false,
      }),
    }
  },
  computed: {
    specialtyList() {
      return this.specialties && this.specialties.length ? this.specialties : this.specialOptions
    },
  },
  methods: {
    getSpecialtyName(id) {
      const sp = this.specialtyList.find((s) => s.id === id)
      return sp ? sp.name : `#${id}`
    },
    toggleSpecialty(id) {
      const idx = this.form.specialties.indexOf(id)
      if (idx > -1) {
        this.form.specialties.splice(idx, 1)
      } else {
        this.form.specialties.push(id)
      }
    },
    onSpecialtySelect(e) {
      const val = parseInt(e.target.value, 10)
      if (val && !this.form.specialties.includes(val)) {
        this.form.specialties.push(val)
      }
      e.target.value = ''
    },
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
      this.form.post(route('doctors.update', this.doctor.id), {
        preserveScroll: true,
        onSuccess: () => {
          this.toast.success(this.tm('Doctor_updated_successfully') || 'Doctor updated successfully.')
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
