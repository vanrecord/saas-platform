<template>
  <div v-if="show" class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
    <!-- Backdrop -->
    <div class="fixed inset-0 bg-slate-900/50 backdrop-blur-sm transition-opacity" @click="close"></div>

    <div class="flex min-h-screen items-center justify-center p-4 text-center sm:p-0">
      <div
        class="relative transform overflow-hidden rounded-2xl bg-white text-left shadow-2xl transition-all sm:my-8 sm:w-full sm:max-w-lg border border-slate-200"
      >
        <!-- Modal Header -->
        <div class="flex items-center justify-between border-b border-slate-100 px-6 py-4 bg-slate-50/50">
          <div class="flex items-center gap-2.5">
            <span class="w-8 h-8 rounded-lg bg-blue-100 text-[#0D63D6] flex items-center justify-center font-bold text-sm">
              <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z" />
              </svg>
            </span>
            <div>
              <h3 class="text-base font-bold text-[#152B45]" id="modal-title">Quick Register Patient</h3>
              <p class="text-xs text-slate-500">Create a new patient record immediately</p>
            </div>
          </div>
          <button
            type="button"
            @click="close"
            class="rounded-lg p-1 text-slate-400 hover:bg-slate-100 hover:text-slate-600 transition"
          >
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
            </svg>
          </button>
        </div>

        <!-- Form Body -->
        <form @submit.prevent="submit" class="p-6 space-y-4">
          <!-- First & Last Name -->
          <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div>
              <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">
                First Name <span class="text-red-500">*</span>
              </label>
              <input
                v-model="form.firstname"
                type="text"
                required
                placeholder="Given name"
                class="w-full rounded-lg border border-slate-300 px-3.5 py-2 text-sm text-[#152B45] placeholder-slate-400 focus:border-[#0D63D6] focus:ring-2 focus:ring-blue-100 focus:outline-none transition"
              />
              <p v-if="errors.firstname" class="mt-1 text-xs text-red-500">{{ errors.firstname }}</p>
            </div>

            <div>
              <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">
                Last Name <span class="text-red-500">*</span>
              </label>
              <input
                v-model="form.lastname"
                type="text"
                required
                placeholder="Family name"
                class="w-full rounded-lg border border-slate-300 px-3.5 py-2 text-sm text-[#152B45] placeholder-slate-400 focus:border-[#0D63D6] focus:ring-2 focus:ring-blue-100 focus:outline-none transition"
              />
              <p v-if="errors.lastname" class="mt-1 text-xs text-red-500">{{ errors.lastname }}</p>
            </div>
          </div>

          <!-- Phone & Gender Row -->
          <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div>
              <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">
                Phone Number
              </label>
              <input
                v-model="form.phone"
                type="tel"
                placeholder="e.g. 012 345 678"
                class="w-full rounded-lg border border-slate-300 px-3.5 py-2 text-sm text-[#152B45] placeholder-slate-400 focus:border-[#0D63D6] focus:ring-2 focus:ring-blue-100 focus:outline-none transition"
              />
              <p v-if="errors.phone" class="mt-1 text-xs text-red-500">{{ errors.phone }}</p>
            </div>

            <div>
              <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">
                Gender
              </label>
              <select
                v-model="form.gender"
                class="w-full rounded-lg border border-slate-300 px-3.5 py-2 text-sm text-[#152B45] focus:border-[#0D63D6] focus:ring-2 focus:ring-blue-100 focus:outline-none transition bg-white"
              >
                <option value="male">Male</option>
                <option value="female">Female</option>
                <option value="other">Other</option>
              </select>
            </div>
          </div>

          <!-- Date of Birth & Profession Row -->
          <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div>
              <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">
                Date of Birth
              </label>
              <input
                v-model="form.dob"
                type="date"
                class="w-full rounded-lg border border-slate-300 px-3.5 py-2 text-sm text-[#152B45] focus:border-[#0D63D6] focus:ring-2 focus:ring-blue-100 focus:outline-none transition bg-white"
              />
            </div>

            <div>
              <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">
                Profession
              </label>
              <input
                v-model="form.profession"
                type="text"
                placeholder="e.g. Teacher, Engineer"
                class="w-full rounded-lg border border-slate-300 px-3.5 py-2 text-sm text-[#152B45] placeholder-slate-400 focus:border-[#0D63D6] focus:ring-2 focus:ring-blue-100 focus:outline-none transition"
              />
            </div>
          </div>

          <!-- Address -->
          <div>
            <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">
              Address / City
            </label>
            <input
              v-model="form.address"
              type="text"
              placeholder="e.g. Phnom Penh, Cambodia"
              class="w-full rounded-lg border border-slate-300 px-3.5 py-2 text-sm text-[#152B45] placeholder-slate-400 focus:border-[#0D63D6] focus:ring-2 focus:ring-blue-100 focus:outline-none transition"
            />
          </div>

          <!-- Footer Buttons -->
          <div class="pt-3 border-t border-slate-100 flex items-center justify-end gap-3">
            <button
              type="button"
              @click="close"
              class="px-4 py-2 text-xs font-semibold text-slate-600 hover:bg-slate-100 rounded-lg transition"
            >
              Cancel
            </button>
            <button
              type="submit"
              :disabled="loading"
              class="px-5 py-2 text-xs font-bold text-white bg-[#0D63D6] hover:bg-[#094bb5] disabled:opacity-50 rounded-lg shadow-sm transition flex items-center gap-1.5"
            >
              <svg v-if="loading" class="w-3.5 h-3.5 animate-spin" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
              </svg>
              <span>{{ loading ? 'Saving...' : 'Register Patient' }}</span>
            </button>
          </div>
        </form>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, reactive } from 'vue'
import axios from 'axios'

const props = defineProps({
  show: {
    type: Boolean,
    default: false
  }
})

const emit = defineEmits(['close', 'created'])

const loading = ref(false)
const errors = reactive({})

const form = reactive({
  firstname: '',
  lastname: '',
  phone: '',
  gender: 'male',
  dob: '',
  profession: '',
  address: ''
})

function close() {
  form.firstname = ''
  form.lastname = ''
  form.phone = ''
  form.gender = 'male'
  form.dob = ''
  form.profession = ''
  form.address = ''
  Object.keys(errors).forEach(key => delete errors[key])
  emit('close')
}

async function submit() {
  if (!form.firstname.trim() && !form.lastname.trim()) {
    errors.firstname = 'Patient name is required.'
    return
  }

  loading.value = true
  Object.keys(errors).forEach(key => delete errors[key])

  try {
    const payload = {
      ...form,
      full_name: `${form.firstname} ${form.lastname}`.trim(),
    }
    const response = await axios.post('/api/patients/quick-create', payload)
    emit('created', response.data)
    close()
  } catch (err) {
    if (err.response?.data?.errors) {
      Object.assign(errors, err.response.data.errors)
    } else {
      errors.firstname = 'Failed to create patient. Please try again.'
    }
  } finally {
    loading.value = false
  }
}
</script>
