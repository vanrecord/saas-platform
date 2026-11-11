<template>
  <TenantLayout>
    <Head :title="tm('Create_Doctor') || 'Create Doctor'" />
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
                {{ tm('Create') || 'Create' }}
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

                  <!-- Preview if selected -->
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
            <select-input
              v-model="form.specialties"
              :error="form.errors.specialties"
              class="pb-2 pr-6 w-full lg:w-1/2"
              :label="tm('Specialties')"
              :options="specialOptions"
            />

            <!-- Description with Rich Text Editor -->
            <div class="pt-2">
              <label class="block text-xs sm:text-sm font-medium text-gray-700 dark:text-gray-200 mb-2">
                {{ tm('Description') || 'Description' }}
              </label>
              <RichTextEditor v-model="form.description" />
              <!-- <EditorContainerQuill 
                v-model="form_event.message" 
                required="required" 
                :error="form_event.errors.message" 
                :label="tm('telegramBroadcastMessage.Message')"
                toolbarType="essential"
                class="w-full lg:w-1/2 pr-6"
              /> -->
            </div>
          </div>
        </div>

        <!-- Footer Actions -->
        <div class="flex items-center justify-end px-8 py-4 bg-gray-50 border-t border-gray-100 dark:bg-darker dark:border-primary space-x-2">
          <save-button :loading="form.processing">
            {{ tm('Save') }}
          </save-button>
          <back-button :url="route('doctors.index')">
            {{ tm('Cancel') }}
          </back-button>
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
import SaveButton from '@/Components/Button/Save.vue'
import BackButton from '@/Components/Button/Back.vue'
import SelectInput from '@/Components/Forms/SelectInput.vue'
import { useToast } from 'vue-toastification'
import { useI18n } from 'vue-i18n'
import { lang } from '@/Lang'

export default defineComponent({
  components: {
    TenantLayout,
    Head,
    Link,
    RichTextEditor,
    SaveButton,
    BackButton,
    SelectInput
  },
  props: {
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
      form: this.$inertia.form({
        username: '',
        email: '',
        firstname: '',
        lastname: '',
        date_of_birth: '',
        gender: 'F',
        primary_mobile: '',
        secondary_mobile: '',
        specialties: [],
        description: '',
        avatar: null,
        remove_avatar: false,
      }),
      avatarPreview: null,
      selectedFileName: '',
      isDragging: false,
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
      this.form.post(route('doctors.store'), {
        preserveScroll: true,
        onSuccess: () => {
          this.toast.success(this.tm('Doctor_created_successfully') || 'Doctor created successfully.')
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
