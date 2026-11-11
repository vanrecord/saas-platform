<template>
  <TenantLayout>
    <Head title="Create Patient" />
    <div class="px-4 py-4">
      <nav class="flex pt-4 mb-6" aria-label="Breadcrumb">
        <ol class="inline-flex items-center space-x-1 md:space-x-3">
          <li class="inline-flex items-center">
            <Link :href="route('tenant.dashboard')" class="inline-flex items-center text-sm font-medium text-gray-700 hover:text-gray-900 dark:text-gray-400 dark:hover:text-white">
              <svg class="mr-2 w-4 h-4" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                <path d="M10.707 2.293a1 1 0 00-1.414 0l-7 7a1 1 0 001.414 1.414L4 10.414V17a1 1 0 001 1h2a1 1 0 001-1v-2a1 1 0 011-1h2a1 1 0 011 1v2a1 1 0 001 1h2a1 1 0 001-1v-6.586l.293.293a1 1 0 001.414-1.414l-7-7z"></path>
              </svg>
              {{ tm('Home') || 'Home' }}
            </Link>
          </li>
          <li>
            <div class="flex items-center">
              <svg class="w-6 h-6 text-gray-400" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path>
              </svg>
              <Link :href="backUrl" class="ml-1 text-sm font-medium text-gray-700 hover:text-gray-900 md:ml-2 dark:text-gray-400 dark:hover:text-white">
                {{ returnTo === 'register' ? (tm('Return_to_Register') || 'Return to Register') : (tm('Patients') || 'Patients') }}
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

      <form @submit.prevent="submit">
        <div class="col-span-2 bg-white rounded-md dark:bg-darker shadow-sm">
          <div class="flex items-center justify-between p-4 border-b dark:border-primary">
            <h4 class="text-lg font-semibold text-gray-500 dark:text-light">{{ tm('Create_Patient') || 'Create Patient' }}</h4>
            <button
              type="button"
              @click="showEHealthModal = true"
              class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded bg-[#00a65a] hover:bg-[#008d4c] text-white text-xs font-semibold shadow-sm transition-colors"
            >
              <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <rect x="3" y="3" width="7" height="7"></rect>
                <rect x="14" y="3" width="7" height="7"></rect>
                <rect x="3" y="14" width="7" height="7"></rect>
                <rect x="14" y="14" width="7" height="7"></rect>
                <path d="M7 7h.01M17 7h.01M7 17h.01M17 17h.01"></path>
              </svg>
              <span>e-Health</span>
            </button>
          </div>

          <!-- Section 1: Patient Information -->
          <div class="flex items-center justify-between p-4 border-b bg-gray-50 dark:bg-darker dark:border-primary">
            <h5 class="text-sm font-semibold text-gray-600 dark:text-light">{{ tm('Patient_Information') || 'Patient Information' }}</h5>
          </div>
          <div class="p-4">
            <div class="flex flex-wrap -mb-8 -mr-6 p-4">
              <text-input
                v-model.number="form.id_number"
                type="number"
                required="required"
                :error="form.errors.id_number"
                class="pb-2 pr-6 w-full lg:w-1/2"
                :label="tm('ID_Patient') || 'ID Patient'"
              />
              <text-input
                v-model="form.firstname"
                required="required"
                :error="form.errors.firstname"
                class="pb-2 pr-6 w-full lg:w-1/2"
                :label="tm('First_Name') || 'First Name'"
              />
              <text-input
                v-model="form.lastname"
                required="required"
                :error="form.errors.lastname"
                class="pb-2 pr-6 w-full lg:w-1/2"
                :label="tm('Last_Name') || 'Last Name'"
              />
              <select-input
                v-model="form.gender"
                :error="form.errors.gender"
                class="pb-2 pr-6 w-full lg:w-1/2"
                :label="tm('Gender') || 'Gender'"
                :options="genderOptions"
              />
              <date-input
                v-model="form.dob"
                required="required"
                :label="tm('employee.Date of Birth')"
                :error="form.errors.dob"
                :max-date="new Date()"
                class="pb-2 pr-6 w-full lg:w-1/2"
              />

              <text-input
                v-model.number="form.age"
                type="number"
                min="0"
                max="150"
                :error="form.errors.age"
                class="pb-2 pr-6 w-full lg:w-1/2"
                :label="tm('Age') || 'Age'"
              />
            </div>
          </div>

          <!-- Section 2: Contact Information -->
          <div class="flex items-center justify-between mt-6 p-4 border-b bg-gray-50 dark:bg-darker dark:border-primary">
            <h5 class="text-sm font-semibold text-gray-600 dark:text-light">{{ tm('Contact_Information') || 'Contact Information' }}</h5>
          </div>
          <div class="p-4">
            <div class="flex flex-wrap -mb-8 -mr-6 p-4">
              <text-input
                v-model="form.phone"
                :error="form.errors.phone"
                class="pb-2 pr-6 w-full lg:w-1/2"
                :label="tm('phone') || 'Phone'"
              />
              <text-input
                v-model="form.email"
                type="email"
                :error="form.errors.email"
                class="pb-2 pr-6 w-full lg:w-1/2"
                :label="tm('Email') || 'Email'"
              />
              <text-input
                v-model="form.profession"
                :error="form.errors.profession"
                class="pb-2 pr-6 w-full lg:w-1/2"
                :label="tm('Profession') || 'Profession / Occupation'"
              />
              <textarea-input
                v-model="form.address"
                :error="form.errors.address"
                :rows="3"
                class="pb-2 pr-6 w-full"
                :label="tm('Address') || 'Address'"
              />
            </div>
          </div>

          <!-- Section 3: Avatar -->
          <div class="flex items-center justify-between mt-6 p-4 border-b bg-gray-50 dark:bg-darker dark:border-primary">
            <h5 class="text-sm font-semibold text-gray-600 dark:text-light">{{ tm('Avatar') || 'Avatar' }}</h5>
          </div>
          <div class="p-4">
            <div class="flex flex-wrap items-center gap-6 p-4">
              <div class="w-24 h-24 rounded-full border-2 border-dashed border-gray-300 overflow-hidden flex items-center justify-center bg-gray-50">
                <img v-if="avatarPreview" :src="avatarPreview" alt="Avatar preview" class="w-full h-full object-cover" />
                <svg v-else class="w-10 h-10 text-gray-300" fill="currentColor" viewBox="0 0 24 24">
                  <path d="M12 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm0 2c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z" />
                </svg>
              </div>
              <div class="flex items-center gap-3">
                <button
                  type="button"
                  @click="triggerFileInput"
                  class="px-4 py-2 border border-gray-300 rounded-md shadow-sm text-xs font-semibold text-gray-700 bg-white hover:bg-gray-50 transition"
                >
                  {{ tm('Choose_Avatar') || 'Choose Avatar' }}
                </button>
                <button
                  v-if="avatarPreview"
                  type="button"
                  @click="removeAvatar"
                  class="px-4 py-2 border border-red-300 rounded-md shadow-sm text-xs font-semibold text-red-600 bg-white hover:bg-red-50 transition"
                >
                  {{ tm('Remove') || 'Remove' }}
                </button>
                <input
                  ref="fileInput"
                  type="file"
                  accept="image/jpeg,image/png,image/webp,image/svg+xml"
                  @change="handleAvatarChange"
                  class="hidden"
                />
              </div>
              <div v-if="form.errors.avatar" class="text-xs text-red-500 font-semibold w-full">
                {{ form.errors.avatar }}
              </div>
            </div>
          </div>

          <!-- Bottom Actions -->
          <div class="p-4 border-t dark:border-primary">
            <div class="flex items-center justify-end px-4 py-4 space-x-2">
              <save-button></save-button>
              <back-button :url="backUrl"></back-button>
            </div>
          </div>
        </div>
      </form>
    </div>

    <!-- e-Health Modal -->
    <div v-if="showEHealthModal" class="fixed inset-0 z-50 overflow-y-auto flex items-center justify-center p-4">
      <div class="fixed inset-0 bg-slate-900/50 backdrop-blur-sm" @click="showEHealthModal = false"></div>
      <div class="relative bg-white rounded-xl max-w-md w-full p-6 shadow-2xl border border-slate-200 space-y-4">
        <div class="flex items-center justify-between border-b border-slate-100 pb-3">
          <div class="flex items-center gap-2 text-[#00a65a]">
            <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
              <rect x="3" y="3" width="7" height="7"></rect>
              <rect x="14" y="3" width="7" height="7"></rect>
              <rect x="3" y="14" width="7" height="7"></rect>
              <rect x="14" y="14" width="7" height="7"></rect>
            </svg>
            <h3 class="text-sm font-bold text-slate-900">e-Health Card Reader</h3>
          </div>
          <button type="button" @click="showEHealthModal = false" class="text-slate-400 hover:text-slate-600">✕</button>
        </div>
        <p class="text-xs text-slate-500">Scan an e-Health QR code or patient membership card to autofill information.</p>
        <div class="flex justify-end pt-2">
          <button type="button" @click="showEHealthModal = false" class="px-4 py-2 rounded text-xs font-bold text-white bg-[#00a65a] hover:bg-[#008d4c] transition-colors">
            Close
          </button>
        </div>
      </div>
    </div>
  </TenantLayout>
</template>

<script>
import { defineComponent } from 'vue'
import TenantLayout from '@/Layouts/TenantLayout.vue'
import TextInput from '@/Components/Form/TextInput.vue'
import SelectInput from '@/Components/Forms/SelectInput.vue'
import TextareaInput from '@/Components/Form/TextareaInput.vue'
import BackButton from '@/Components/Button/Back.vue'
import SaveButton from '@/Components/Button/Save.vue'
import DateInput from "@/Components/Forms/DateInput.vue"
import { useI18n } from 'vue-i18n'
import { lang } from '@/Lang'
import { Head, Link } from '@inertiajs/inertia-vue3'
import { useToast } from 'vue-toastification'


export default defineComponent({
  components: {
    TenantLayout,
    Head,
    Link,
    TextInput,
    SelectInput,
    TextareaInput,
    BackButton,
    SaveButton,
    DateInput
  },
  props: {
    suggestedIdNumber: [Number, String],
    returnTo: {
      type: String,
      default: '',
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
  computed: {
    backUrl() {
      if (this.returnTo === 'register' || this.form?.return_to === 'register') {
        return route('tenant.visit.create')
      }
      return route('patients.index')
    },
    genderOptions() {
      return {
        '': this.tm('Select_Gender') || 'Select Gender',
        'M': this.tm('Male') || 'Male',
        'F': this.tm('Female') || 'Female',
      }
    },
  },
  data() {
    return {
      showEHealthModal: false,
      avatarPreview: null,
      isSyncing: false,
      form: this.$inertia.form({
        id_number: this.suggestedIdNumber || '',
        firstname: '',
        lastname: '',
        gender: '',
        dob: '',
        age: null,
        phone: '',
        email: '',
        profession: '',
        address: '',
        avatar: null,
        remove_avatar: false,
        return_to: this.returnTo || (typeof window !== 'undefined' ? new URLSearchParams(window.location.search).get('return_to') || '' : ''),
      }),
    }
  },
  watch: {
    'form.dob': function (val) {
      if (this.isSyncing) return
      this.syncAgeFromDob(val)
    },
    'form.age': function (val) {
      if (this.isSyncing) return
      this.syncDobFromAge(val)
    },
  },
  methods: {
    calculateAgeFromDob(dobVal) {
      if (!dobVal) return null
      let birthDate = null
      if (dobVal instanceof Date) {
        birthDate = dobVal
      } else if (typeof dobVal === 'string') {
        const trimmed = dobVal.trim()
        if (!trimmed) return null
        if (trimmed.includes('/')) {
          const parts = trimmed.split('/')
          if (parts.length === 3) {
            birthDate = new Date(Number(parts[2]), Number(parts[1]) - 1, Number(parts[0]))
          }
        } else if (trimmed.includes('-')) {
          const parts = trimmed.split('-')
          if (parts.length === 3) {
            birthDate = new Date(Number(parts[0]), Number(parts[1]) - 1, Number(parts[2]))
          }
        } else {
          birthDate = new Date(trimmed)
        }
      }

      if (!birthDate || isNaN(birthDate.getTime())) return null

      const today = new Date()
      const todayMidnight = new Date(today.getFullYear(), today.getMonth(), today.getDate())
      const birthMidnight = new Date(birthDate.getFullYear(), birthDate.getMonth(), birthDate.getDate())

      // Do not allow future Date of Birth
      if (birthMidnight > todayMidnight) {
        return null
      }

      let age = today.getFullYear() - birthDate.getFullYear()
      const m = today.getMonth() - birthDate.getMonth()
      if (m < 0 || (m === 0 && today.getDate() < birthDate.getDate())) {
        age--
      }
      return age >= 0 ? age : null
    },
    calculateDobFromAge(ageVal) {
      if (ageVal === '' || ageVal === null || ageVal === undefined) return null
      const num = Number(ageVal)
      if (!Number.isInteger(num) || num < 0 || num > 150) {
        return null
      }

      const today = new Date()
      const birthYear = today.getFullYear() - num
      let month = today.getMonth() + 1
      let day = today.getDate()

      // Handle leap year edge case (Feb 29 on non-leap year)
      if (month === 2 && day === 29) {
        const isLeapYear = (birthYear % 4 === 0 && birthYear % 100 !== 0) || (birthYear % 400 === 0)
        if (!isLeapYear) {
          day = 28
        }
      }

      const mm = String(month).padStart(2, '0')
      const dd = String(day).padStart(2, '0')
      return `${birthYear}-${mm}-${dd}`
    },
    syncAgeFromDob(val) {
      let dob = typeof val === 'string' ? val : (val instanceof Date ? val : val?.target?.value)
      if (!dob) {
        if (this.form.age !== null && this.form.age !== '') {
          this.isSyncing = true
          this.form.age = null
          this.$nextTick(() => { this.isSyncing = false })
        }
        return
      }

      const calculatedAge = this.calculateAgeFromDob(dob)
      if (calculatedAge !== null) {
        if (this.form.age !== calculatedAge) {
          this.isSyncing = true
          this.form.age = calculatedAge
          this.$nextTick(() => { this.isSyncing = false })
        }
      } else {
        if (this.form.age !== null) {
          this.isSyncing = true
          this.form.age = null
          this.$nextTick(() => { this.isSyncing = false })
        }
      }
    },
    syncDobFromAge(val) {
      if (val === '' || val === null || val === undefined) {
        if (this.form.dob !== '') {
          this.isSyncing = true
          this.form.dob = ''
          this.$nextTick(() => { this.isSyncing = false })
        }
        return
      }

      const calculatedDob = this.calculateDobFromAge(val)
      if (calculatedDob !== null) {
        if (this.form.dob !== calculatedDob) {
          this.isSyncing = true
          this.form.dob = calculatedDob
          this.$nextTick(() => { this.isSyncing = false })
        }
      }
    },
    onDobChange(val) {
      this.syncAgeFromDob(val)
    },
    triggerFileInput() {
      this.$refs.fileInput.click()
    },
    handleAvatarChange(e) {
      const file = e.target.files?.[0]
      if (file) {
        this.form.avatar = file
        this.form.remove_avatar = false
        this.avatarPreview = URL.createObjectURL(file)
      }
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
      this.form.post(this.route('patients.store'), {
        preserveScroll: true,
        preserveState: true,
        onSuccess: () => {
          this.toast.success(this.tm('PatientCreatedSuccessfully') || 'Patient has been created successfully')
        },
      })
    },
  },
})
</script>
