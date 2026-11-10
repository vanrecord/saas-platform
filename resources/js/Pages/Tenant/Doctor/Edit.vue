<template>
  <TenantLayout>
    <Head :title="tm('Edit_Doctor') || 'Edit Doctor'" />
    <div class="px-4 py-4 max-w-7xl mx-auto">
      <!-- Breadcrumb Navigation -->
      <nav class="flex pt-2 mb-4" aria-label="Breadcrumb">
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

      <!-- Main Form Card Matching Screenshot -->
      <form @submit.prevent="submit" class="bg-white rounded shadow-sm border border-gray-200 overflow-hidden dark:bg-darker dark:border-primary">
        <!-- Top Blue Header Banner: "Profile" -->
        <div class="bg-[#5b86e5] px-6 py-3.5 text-white font-medium text-base tracking-wide flex items-center justify-between">
          <span>{{ tm('Profile') || 'Profile' }}</span>
        </div>

        <!-- Two Column Layout matching screenshot -->
        <div class="p-6 md:p-8 grid grid-cols-1 lg:grid-cols-2 gap-x-12 gap-y-6">
          <!-- LEFT COLUMN -->
          <div class="space-y-4">
            <!-- User Name * -->
            <div class="grid grid-cols-1 sm:grid-cols-12 items-center gap-2">
              <label for="username" class="sm:col-span-4 text-xs sm:text-sm font-medium text-gray-700 dark:text-gray-200 sm:text-right pr-4">
                {{ tm('User_Name') || 'User Name' }} <span class="text-red-500 font-bold">*</span>
              </label>
              <div class="sm:col-span-8">
                <input
                  id="username"
                  v-model="form.username"
                  type="text"
                  required
                  class="w-full text-sm rounded border-gray-300 shadow-xs focus:border-[#5b86e5] focus:ring focus:ring-[#5b86e5]/20 dark:bg-dark dark:border-gray-600 dark:text-white"
                  :class="{ 'border-red-500 ring-1 ring-red-500': form.errors.username }"
                />
                <div v-if="form.errors.username" class="text-xs text-red-500 mt-1">
                  {{ form.errors.username }}
                </div>
              </div>
            </div>

            <!-- Email -->
            <div class="grid grid-cols-1 sm:grid-cols-12 items-center gap-2">
              <label for="email" class="sm:col-span-4 text-xs sm:text-sm font-medium text-gray-700 dark:text-gray-200 sm:text-right pr-4">
                {{ tm('Email') || 'Email' }}
              </label>
              <div class="sm:col-span-8">
                <input
                  id="email"
                  v-model="form.email"
                  type="email"
                  class="w-full text-sm rounded border-gray-300 shadow-xs focus:border-[#5b86e5] focus:ring focus:ring-[#5b86e5]/20 dark:bg-dark dark:border-gray-600 dark:text-white"
                  :class="{ 'border-red-500 ring-1 ring-red-500': form.errors.email }"
                />
                <div v-if="form.errors.email" class="text-xs text-red-500 mt-1">
                  {{ form.errors.email }}
                </div>
              </div>
            </div>

            <!-- First Name * -->
            <div class="grid grid-cols-1 sm:grid-cols-12 items-center gap-2">
              <label for="firstname" class="sm:col-span-4 text-xs sm:text-sm font-medium text-gray-700 dark:text-gray-200 sm:text-right pr-4">
                {{ tm('First_Name') || 'First Name' }} <span class="text-red-500 font-bold">*</span>
              </label>
              <div class="sm:col-span-8">
                <input
                  id="firstname"
                  v-model="form.firstname"
                  type="text"
                  required
                  class="w-full text-sm rounded border-gray-300 shadow-xs focus:border-[#5b86e5] focus:ring focus:ring-[#5b86e5]/20 dark:bg-dark dark:border-gray-600 dark:text-white"
                  :class="{ 'border-red-500 ring-1 ring-red-500': form.errors.firstname }"
                />
                <div v-if="form.errors.firstname" class="text-xs text-red-500 mt-1">
                  {{ form.errors.firstname }}
                </div>
              </div>
            </div>

            <!-- Last Name * -->
            <div class="grid grid-cols-1 sm:grid-cols-12 items-center gap-2">
              <label for="lastname" class="sm:col-span-4 text-xs sm:text-sm font-medium text-gray-700 dark:text-gray-200 sm:text-right pr-4">
                {{ tm('Last_Name') || 'Last Name' }} <span class="text-red-500 font-bold">*</span>
              </label>
              <div class="sm:col-span-8">
                <input
                  id="lastname"
                  v-model="form.lastname"
                  type="text"
                  required
                  class="w-full text-sm rounded border-gray-300 shadow-xs focus:border-[#5b86e5] focus:ring focus:ring-[#5b86e5]/20 dark:bg-dark dark:border-gray-600 dark:text-white"
                  :class="{ 'border-red-500 ring-1 ring-red-500': form.errors.lastname }"
                />
                <div v-if="form.errors.lastname" class="text-xs text-red-500 mt-1">
                  {{ form.errors.lastname }}
                </div>
              </div>
            </div>

            <!-- Date of Birth -->
            <div class="grid grid-cols-1 sm:grid-cols-12 items-center gap-2">
              <label for="date_of_birth" class="sm:col-span-4 text-xs sm:text-sm font-medium text-gray-700 dark:text-gray-200 sm:text-right pr-4">
                {{ tm('Date_of_Birth') || 'Date of Birth' }}
              </label>
              <div class="sm:col-span-8">
                <input
                  id="date_of_birth"
                  v-model="form.date_of_birth"
                  type="date"
                  class="w-full text-sm rounded border-gray-300 shadow-xs focus:border-[#5b86e5] focus:ring focus:ring-[#5b86e5]/20 dark:bg-dark dark:border-gray-600 dark:text-white"
                  :class="{ 'border-red-500 ring-1 ring-red-500': form.errors.date_of_birth }"
                />
                <div v-if="form.errors.date_of_birth" class="text-xs text-red-500 mt-1">
                  {{ form.errors.date_of_birth }}
                </div>
              </div>
            </div>

            <!-- Avatar Dropzone + Browse Bar exactly like screenshot -->
            <div class="grid grid-cols-1 sm:grid-cols-12 items-start gap-2 pt-2">
              <label class="sm:col-span-4 text-xs sm:text-sm font-medium text-gray-700 dark:text-gray-200 sm:text-right pr-4 pt-2">
                {{ tm('Avatar') || 'Avatar' }}
              </label>
              <div class="sm:col-span-8 space-y-2">
                <!-- Dashed drop area -->
                <div
                  @click="triggerFileInput"
                  @dragover.prevent="isDragging = true"
                  @dragleave.prevent="isDragging = false"
                  @drop.prevent="handleFileDrop"
                  class="border-2 border-dashed rounded p-6 flex flex-col items-center justify-center relative cursor-pointer transition min-h-[160px] bg-white dark:bg-dark"
                  :class="[
                    isDragging ? 'border-[#5b86e5] bg-[#5b86e5]/5' : 'border-gray-300 dark:border-gray-600 hover:bg-gray-50/50'
                  ]"
                >
                  <!-- Top right remove X button -->
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

                  <!-- Preview if selected or exists -->
                  <div v-if="avatarPreview" class="flex flex-col items-center gap-2">
                    <img :src="avatarPreview" alt="Avatar" class="w-24 h-24 rounded-full object-cover border border-gray-200 shadow-sm" />
                    <span class="text-xs text-gray-500">Click or drop new image to change</span>
                  </div>

                  <!-- Empty state prompt matching screenshot -->
                  <div v-else class="text-center py-4">
                    <p class="text-base text-gray-400 dark:text-gray-500">
                      Drag & drop files here ...
                    </p>
                  </div>
                </div>

                <!-- Bottom input bar with Select file ... and Browse ... button -->
                <div class="flex rounded shadow-xs overflow-hidden border border-gray-300 dark:border-gray-600">
                  <input
                    type="text"
                    readonly
                    :value="selectedFileName || 'Select file ...'"
                    class="flex-1 bg-white dark:bg-dark text-xs sm:text-sm px-3 py-2 text-gray-500 border-0 focus:ring-0 cursor-pointer"
                    @click="triggerFileInput"
                  />
                  <button
                    type="button"
                    @click="triggerFileInput"
                    class="bg-[#5b86e5] hover:bg-[#4a72cf] text-white px-4 py-2 text-xs sm:text-sm font-medium flex items-center gap-1.5 transition shrink-0"
                  >
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12" />
                    </svg>
                    Browse ...
                  </button>
                </div>

                <input
                  ref="fileInput"
                  type="file"
                  accept="image/jpeg,image/png,image/jpg,image/webp,image/gif"
                  @change="handleAvatarChange"
                  class="hidden"
                />
                <div v-if="form.errors.avatar" class="text-xs text-red-500 mt-1">
                  {{ form.errors.avatar }}
                </div>
              </div>
            </div>
          </div>

          <!-- RIGHT COLUMN -->
          <div class="space-y-4">
            <!-- Gender : Male / Female radio buttons -->
            <div class="grid grid-cols-1 sm:grid-cols-12 items-center gap-2">
              <label class="sm:col-span-4 text-xs sm:text-sm font-medium text-gray-700 dark:text-gray-200 sm:text-right pr-4">
                {{ tm('Gender') || 'Gender' }} :
              </label>
              <div class="sm:col-span-8 flex items-center gap-6">
                <label class="inline-flex items-center gap-2 cursor-pointer text-sm text-gray-700 dark:text-gray-300">
                  <input
                    type="radio"
                    v-model="form.gender"
                    value="M"
                    class="text-[#5b86e5] focus:ring-[#5b86e5]"
                  />
                  <span>{{ tm('Male') || 'Male' }}</span>
                </label>
                <label class="inline-flex items-center gap-2 cursor-pointer text-sm text-gray-700 dark:text-gray-300">
                  <input
                    type="radio"
                    v-model="form.gender"
                    value="F"
                    class="text-[#5b86e5] focus:ring-[#5b86e5]"
                  />
                  <span>{{ tm('Female') || 'Female' }}</span>
                </label>
              </div>
            </div>

            <!-- Primary Mobile -->
            <div class="grid grid-cols-1 sm:grid-cols-12 items-center gap-2">
              <label for="primary_mobile" class="sm:col-span-4 text-xs sm:text-sm font-medium text-gray-700 dark:text-gray-200 sm:text-right pr-4">
                {{ tm('Primary_Mobile') || 'Primary Mobile' }}
              </label>
              <div class="sm:col-span-8">
                <input
                  id="primary_mobile"
                  v-model="form.primary_mobile"
                  type="text"
                  class="w-full text-sm rounded border-gray-300 shadow-xs focus:border-[#5b86e5] focus:ring focus:ring-[#5b86e5]/20 dark:bg-dark dark:border-gray-600 dark:text-white"
                  :class="{ 'border-red-500 ring-1 ring-red-500': form.errors.primary_mobile }"
                />
                <div v-if="form.errors.primary_mobile" class="text-xs text-red-500 mt-1">
                  {{ form.errors.primary_mobile }}
                </div>
              </div>
            </div>

            <!-- Secondary Mobile -->
            <div class="grid grid-cols-1 sm:grid-cols-12 items-center gap-2">
              <label for="secondary_mobile" class="sm:col-span-4 text-xs sm:text-sm font-medium text-gray-700 dark:text-gray-200 sm:text-right pr-4">
                {{ tm('Secondary_Mobile') || 'Secondary Mobile' }}
              </label>
              <div class="sm:col-span-8">
                <input
                  id="secondary_mobile"
                  v-model="form.secondary_mobile"
                  type="text"
                  class="w-full text-sm rounded border-gray-300 shadow-xs focus:border-[#5b86e5] focus:ring focus:ring-[#5b86e5]/20 dark:bg-dark dark:border-gray-600 dark:text-white"
                  :class="{ 'border-red-500 ring-1 ring-red-500': form.errors.secondary_mobile }"
                />
                <div v-if="form.errors.secondary_mobile" class="text-xs text-red-500 mt-1">
                  {{ form.errors.secondary_mobile }}
                </div>
              </div>
            </div>

            <!-- Specialties -->
            <div class="grid grid-cols-1 sm:grid-cols-12 items-start gap-2">
              <label class="sm:col-span-4 text-xs sm:text-sm font-medium text-gray-700 dark:text-gray-200 sm:text-right pr-4 pt-2">
                {{ tm('Specialties') || 'Specialties' }}
              </label>
              <div class="sm:col-span-8">
                <div class="border border-gray-300 dark:border-gray-600 rounded p-2 bg-white dark:bg-dark min-h-[42px] flex flex-wrap gap-1.5 items-center">
                  <span
                    v-for="sId in form.specialties"
                    :key="sId"
                    class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-semibold bg-blue-100 text-blue-800"
                  >
                    {{ getSpecialtyName(sId) }}
                    <button type="button" @click="toggleSpecialty(sId)" class="hover:text-red-500">×</button>
                  </span>
                  <select
                    @change="onSpecialtySelect"
                    class="border-0 text-xs text-gray-500 focus:ring-0 py-1 px-1 bg-transparent cursor-pointer flex-1 min-w-[120px]"
                  >
                    <option value="">+ Add specialty...</option>
                    <option
                      v-for="sp in specialties"
                      :key="sp.id"
                      :value="sp.id"
                      :disabled="form.specialties.includes(sp.id)"
                    >
                      {{ sp.name }}
                    </option>
                  </select>
                </div>
              </div>
            </div>

            <!-- Description with Rich Text Editor -->
            <div class="pt-2">
              <label class="block text-xs sm:text-sm font-medium text-gray-700 dark:text-gray-200 mb-2">
                {{ tm('Description') || 'Description' }}
              </label>
              <RichTextEditor v-model="form.description" />
            </div>
          </div>
        </div>

        <!-- Form Action Footer -->
        <div class="flex items-center justify-end gap-3 p-4 border-t bg-gray-50/50 dark:bg-dark dark:border-primary">
          <Link
            :href="route('doctors.index')"
            class="inline-flex items-center px-4 py-2 bg-gray-100 hover:bg-gray-200 dark:bg-gray-700 dark:hover:bg-gray-600 text-gray-700 dark:text-gray-200 border border-transparent rounded font-semibold text-xs uppercase tracking-widest transition"
          >
            {{ tm('Cancel') || 'Cancel' }}
          </Link>
          <button
            type="submit"
            :disabled="form.processing"
            class="inline-flex items-center px-5 py-2 bg-[#5b86e5] hover:bg-[#4a72cf] border border-transparent rounded font-semibold text-xs text-white uppercase tracking-widest disabled:opacity-50 transition shadow-sm"
          >
            {{ form.processing ? (tm('Saving') || 'Saving...') : (tm('Save') || 'Save') }}
          </button>
        </div>
      </form>
    </div>
  </TenantLayout>
</template>

<script>
import { defineComponent } from 'vue'
import { Head, Link } from '@inertiajs/inertia-vue3'
import TenantLayout from '@/Layouts/TenantLayout.vue'
import RichTextEditor from '@/Components/Form/RichTextEditor.vue'
import { useToast } from 'vue-toastification'
import { useI18n } from 'vue-i18n'
import { lang } from '@/Lang'

export default defineComponent({
  components: {
    TenantLayout,
    Head,
    Link,
    RichTextEditor,
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
      avatarPreview: this.doctor?.avatar_url || null,
      selectedFileName: '',
      isDragging: false,
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
  methods: {
    getSpecialtyName(id) {
      const sp = this.specialties.find((s) => s.id === id)
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
      this.selectedFileName = file.name
      this.avatarPreview = URL.createObjectURL(file)
    },
    removeAvatar() {
      this.form.avatar = null
      this.form.remove_avatar = true
      this.selectedFileName = ''
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
