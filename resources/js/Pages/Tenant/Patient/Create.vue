<template>
  <TenantLayout>
    <Head title="Create Patient" />

    <div class="max-w-6xl mx-auto space-y-4">
      <!-- Breadcrumb and Top Actions -->
      <div class="flex items-center justify-between pb-2">
        <div class="flex items-center gap-2 text-xs text-slate-500">
          <Link :href="route('patients.index')" class="hover:text-blue-600 transition-colors">Patients</Link>
          <span>/</span>
          <span class="text-slate-800 font-bold">New Patient</span>
        </div>

        <!-- Top Right: e-Health Button -->
        <button
          type="button"
          @click="showEHealthModal = true"
          class="inline-flex items-center gap-1.5 px-3.5 py-1.5 rounded bg-[#00a65a] hover:bg-[#008d4c] text-white text-xs font-semibold shadow-sm transition-colors"
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

      <!-- Main Form Card -->
      <div class="bg-white border border-slate-200 rounded-lg p-6 sm:p-8 shadow-sm">
        <form @submit.prevent="submit">
          <!-- 2-Column Layout matching screenshot -->
          <div class="grid grid-cols-1 md:grid-cols-12 gap-8 items-start">
            
            <!-- Left Column: Avatar Box -->
            <div class="md:col-span-3 flex flex-col items-start">
              <label class="text-xs font-semibold text-slate-600 mb-2">Avatar</label>
              
              <!-- Portrait Box -->
              <div class="w-32 h-52 border border-slate-300 rounded bg-white p-2 flex flex-col items-center justify-center shadow-inner">
                <div class="w-full h-full border border-dashed border-slate-300 rounded flex items-center justify-center overflow-hidden bg-slate-50/50">
                  <img
                    v-if="avatarPreview"
                    :src="avatarPreview"
                    alt="Patient Avatar"
                    class="w-full h-full object-cover"
                  />
                  <!-- Broken/Empty Image Icon matching reference -->
                  <div v-else class="text-center p-2 text-slate-300 flex flex-col items-center">
                    <svg class="w-8 h-8 stroke-slate-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                    </svg>
                  </div>
                </div>
              </div>

              <!-- Two Action Buttons Below Avatar Box -->
              <div class="flex items-center gap-1.5 mt-2">
                <!-- Red Button: Clear / Remove Avatar -->
                <button
                  type="button"
                  @click="removeAvatar"
                  title="Remove avatar"
                  class="w-8 h-8 rounded bg-[#d9534f] hover:bg-[#c9302c] text-white flex items-center justify-center transition-colors shadow-sm"
                >
                  <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636" />
                  </svg>
                </button>

                <!-- Blue Button: Browse / Upload Photo -->
                <button
                  type="button"
                  @click="triggerFileInput"
                  title="Choose avatar file"
                  class="w-8 h-8 rounded bg-[#337ab7] hover:bg-[#286090] text-white flex items-center justify-center transition-colors shadow-sm"
                >
                  <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                    <path d="M2 6a2 2 0 012-2h5l2 2h5a2 2 0 012 2v6a2 2 0 01-2 2H4a2 2 0 01-2-2V6z" />
                  </svg>
                </button>
              </div>

              <input
                ref="fileInput"
                type="file"
                accept="image/jpeg,image/png,image/webp,image/svg+xml"
                @change="handleAvatarChange"
                class="hidden"
              />

              <div v-if="form.errors.avatar" class="text-xs text-rose-500 mt-2 font-semibold">
                {{ form.errors.avatar }}
              </div>
            </div>

            <!-- Right Column: Form Fields with left labels matching reference screenshot -->
            <div class="md:col-span-9 space-y-3.5">
              
              <!-- ID Patient * -->
              <div class="flex flex-col sm:flex-row sm:items-center gap-2 sm:gap-4">
                <label class="w-full sm:w-44 text-left sm:text-right text-xs font-semibold text-slate-700">
                  ID Patient <span class="text-red-500">*</span>
                </label>
                <div class="flex-1">
                  <input
                    type="number"
                    v-model.number="form.id_number"
                    required
                    class="w-full border border-slate-300 rounded px-3 py-1.5 text-sm font-mono text-slate-800 focus:border-blue-500 focus:ring-1 focus:ring-blue-500"
                  />
                  <div v-if="form.errors.id_number" class="text-xs text-rose-500 mt-1 font-semibold">
                    {{ form.errors.id_number }}
                  </div>
                </div>
              </div>

              <!-- First name * -->
              <div class="flex flex-col sm:flex-row sm:items-center gap-2 sm:gap-4">
                <label class="w-full sm:w-44 text-left sm:text-right text-xs font-semibold text-slate-700">
                  First name <span class="text-red-500">*</span>
                </label>
                <div class="flex-1">
                  <input
                    type="text"
                    v-model="form.firstname"
                    required
                    maxlength="50"
                    class="w-full border border-slate-300 rounded px-3 py-1.5 text-sm text-slate-800 focus:border-blue-500 focus:ring-1 focus:ring-blue-500"
                  />
                  <div v-if="form.errors.firstname" class="text-xs text-rose-500 mt-1 font-semibold">
                    {{ form.errors.firstname }}
                  </div>
                </div>
              </div>

              <!-- Last name * -->
              <div class="flex flex-col sm:flex-row sm:items-center gap-2 sm:gap-4">
                <label class="w-full sm:w-44 text-left sm:text-right text-xs font-semibold text-slate-700">
                  Last name <span class="text-red-500">*</span>
                </label>
                <div class="flex-1">
                  <input
                    type="text"
                    v-model="form.lastname"
                    required
                    maxlength="50"
                    class="w-full border border-slate-300 rounded px-3 py-1.5 text-sm text-slate-800 focus:border-blue-500 focus:ring-1 focus:ring-blue-500"
                  />
                  <div v-if="form.errors.lastname" class="text-xs text-rose-500 mt-1 font-semibold">
                    {{ form.errors.lastname }}
                  </div>
                </div>
              </div>

              <!-- Gender -->
              <div class="flex flex-col sm:flex-row sm:items-center gap-2 sm:gap-4">
                <label class="w-full sm:w-44 text-left sm:text-right text-xs font-semibold text-slate-700">
                  Gender
                </label>
                <div class="flex-1">
                  <select
                    v-model="form.gender"
                    class="w-full border border-slate-300 rounded px-3 py-1.5 text-sm text-slate-800 bg-white focus:border-blue-500 focus:ring-1 focus:ring-blue-500"
                  >
                    <option value="male">Male</option>
                    <option value="female">Female</option>
                    <option value="other">Other</option>
                  </select>
                  <div v-if="form.errors.gender" class="text-xs text-rose-500 mt-1 font-semibold">
                    {{ form.errors.gender }}
                  </div>
                </div>
              </div>

              <!-- Date of birth -->
              <div class="flex flex-col sm:flex-row sm:items-center gap-2 sm:gap-4">
                <label class="w-full sm:w-44 text-left sm:text-right text-xs font-semibold text-slate-700">
                  Date of birth
                </label>
                <div class="flex-1">
                  <input
                    type="date"
                    v-model="form.dob"
                    @change="onDobChange"
                    placeholder="dd/mm/yyyy"
                    class="w-full border border-slate-300 rounded px-3 py-1.5 text-sm text-slate-800 focus:border-blue-500 focus:ring-1 focus:ring-blue-500"
                  />
                  <div v-if="form.errors.dob" class="text-xs text-rose-500 mt-1 font-semibold">
                    {{ form.errors.dob }}
                  </div>
                </div>
              </div>

              <!-- Age -->
              <div class="flex flex-col sm:flex-row sm:items-center gap-2 sm:gap-4">
                <label class="w-full sm:w-44 text-left sm:text-right text-xs font-semibold text-slate-700">
                  Age
                </label>
                <div class="flex-1">
                  <input
                    type="number"
                    min="0"
                    max="150"
                    v-model.number="form.age"
                    class="w-full border border-slate-300 rounded px-3 py-1.5 text-sm text-slate-800 focus:border-blue-500 focus:ring-1 focus:ring-blue-500"
                  />
                  <div v-if="form.errors.age" class="text-xs text-rose-500 mt-1 font-semibold">
                    {{ form.errors.age }}
                  </div>
                </div>
              </div>

              <!-- Phone -->
              <div class="flex flex-col sm:flex-row sm:items-center gap-2 sm:gap-4">
                <label class="w-full sm:w-44 text-left sm:text-right text-xs font-semibold text-slate-700">
                  Phone
                </label>
                <div class="flex-1">
                  <input
                    type="text"
                    v-model="form.phone"
                    class="w-full border border-slate-300 rounded px-3 py-1.5 text-sm text-slate-800 focus:border-blue-500 focus:ring-1 focus:ring-blue-500"
                  />
                  <div v-if="form.errors.phone" class="text-xs text-rose-500 mt-1 font-semibold">
                    {{ form.errors.phone }}
                  </div>
                </div>
              </div>

              <!-- Email -->
              <div class="flex flex-col sm:flex-row sm:items-center gap-2 sm:gap-4">
                <label class="w-full sm:w-44 text-left sm:text-right text-xs font-semibold text-slate-700">
                  Email
                </label>
                <div class="flex-1">
                  <input
                    type="email"
                    v-model="form.email"
                    class="w-full border border-slate-300 rounded px-3 py-1.5 text-sm text-slate-800 focus:border-blue-500 focus:ring-1 focus:ring-blue-500"
                  />
                  <div v-if="form.errors.email" class="text-xs text-rose-500 mt-1 font-semibold">
                    {{ form.errors.email }}
                  </div>
                </div>
              </div>

              <!-- Profession / Occupation -->
              <div class="flex flex-col sm:flex-row sm:items-center gap-2 sm:gap-4">
                <label class="w-full sm:w-44 text-left sm:text-right text-xs font-semibold text-slate-700">
                  Profession / Occupation
                </label>
                <div class="flex-1">
                  <input
                    type="text"
                    v-model="form.profession"
                    class="w-full border border-slate-300 rounded px-3 py-1.5 text-sm text-slate-800 focus:border-blue-500 focus:ring-1 focus:ring-blue-500"
                  />
                  <div v-if="form.errors.profession" class="text-xs text-rose-500 mt-1 font-semibold">
                    {{ form.errors.profession }}
                  </div>
                </div>
              </div>

              <!-- Address -->
              <div class="flex flex-col sm:flex-row sm:items-start gap-2 sm:gap-4">
                <label class="w-full sm:w-44 text-left sm:text-right text-xs font-semibold text-slate-700 sm:pt-2">
                  Address
                </label>
                <div class="flex-1">
                  <textarea
                    v-model="form.address"
                    rows="3"
                    class="w-full border border-slate-300 rounded px-3 py-1.5 text-sm text-slate-800 focus:border-blue-500 focus:ring-1 focus:ring-blue-500"
                  ></textarea>
                  <div v-if="form.errors.address" class="text-xs text-rose-500 mt-1 font-semibold">
                    {{ form.errors.address }}
                  </div>
                </div>
              </div>

              <!-- Additional Migration Fields (Balance, Card Status) -->
              <div class="pt-2 border-t border-slate-100 space-y-3.5">
                <!-- Balance -->
                <div class="flex flex-col sm:flex-row sm:items-center gap-2 sm:gap-4">
                  <label class="w-full sm:w-44 text-left sm:text-right text-xs font-semibold text-slate-700">
                    Balance ($)
                  </label>
                  <div class="flex-1">
                    <input
                      type="number"
                      step="0.01"
                      v-model.number="form.balance"
                      placeholder="0.00"
                      class="w-full sm:w-48 border border-slate-300 rounded px-3 py-1.5 text-sm font-mono text-slate-800 focus:border-blue-500 focus:ring-1 focus:ring-blue-500"
                    />
                  </div>
                </div>

                <!-- Card Code -->
                <div class="flex flex-col sm:flex-row sm:items-center gap-2 sm:gap-4">
                  <label class="w-full sm:w-44 text-left sm:text-right text-xs font-semibold text-slate-700">
                    Card Code
                  </label>
                  <div class="flex-1">
                    <input
                      type="text"
                      v-model="form.card_code"
                      placeholder="Card barcode or code"
                      class="w-full border border-slate-300 rounded px-3 py-1.5 text-sm font-mono text-slate-800 focus:border-blue-500 focus:ring-1 focus:ring-blue-500"
                    />
                  </div>
                </div>

                <!-- Card Switches -->
                <div class="flex flex-col sm:flex-row sm:items-center gap-2 sm:gap-4">
                  <label class="w-full sm:w-44 text-left sm:text-right text-xs font-semibold text-slate-700">
                    Card Status
                  </label>
                  <div class="flex-1 flex items-center gap-6">
                    <label class="inline-flex items-center gap-2 cursor-pointer text-xs font-semibold text-slate-700">
                      <input
                        type="checkbox"
                        v-model="form.card_activated"
                        class="rounded border-slate-300 text-blue-600 focus:ring-blue-500"
                      />
                      <span>Card Activated</span>
                    </label>

                    <label class="inline-flex items-center gap-2 cursor-pointer text-xs font-semibold text-slate-700">
                      <input
                        type="checkbox"
                        v-model="form.is_trail_card"
                        class="rounded border-slate-300 text-purple-600 focus:ring-purple-500"
                      />
                      <span>Is Trail Card</span>
                    </label>
                  </div>
                </div>
              </div>

              <!-- Action Buttons -->
              <div class="flex items-center justify-end gap-3 pt-6 border-t border-slate-200">
                <Link
                  :href="route('patients.index')"
                  class="px-4 py-2 rounded text-xs font-bold text-slate-600 border border-slate-300 hover:bg-slate-50 transition-colors"
                >
                  Cancel
                </Link>
                <button
                  type="submit"
                  :disabled="form.processing"
                  class="px-5 py-2 rounded text-xs font-bold text-white bg-[#2457c5] hover:bg-[#1d48a3] shadow-sm transition-colors disabled:opacity-50"
                >
                  {{ form.processing ? 'Saving...' : 'Save Patient' }}
                </button>
              </div>

            </div>
          </div>
        </form>
      </div>
    </div>

    <!-- e-Health Modal -->
    <div
      v-if="showEHealthModal"
      class="fixed inset-0 z-50 overflow-y-auto flex items-center justify-center p-4"
    >
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
            <h3 class="text-sm font-bold text-slate-900">e-Health Integration</h3>
          </div>
          <button
            type="button"
            @click="showEHealthModal = false"
            class="text-slate-400 hover:text-slate-600"
          >
            ✕
          </button>
        </div>

        <div class="text-xs text-slate-600 space-y-2">
          <p>
            Connect patient profile with national or institutional e-Health registry.
          </p>
          <div class="p-3 bg-emerald-50 rounded-lg border border-emerald-200 text-emerald-800 space-y-1">
            <div class="font-bold">Status: Ready to Link</div>
            <div>Patient ID: <span class="font-mono font-bold">{{ form.id_number || 'Auto' }}</span></div>
            <div>Full Name: <span class="font-bold">{{ `${form.firstname || ''} ${form.lastname || ''}`.trim() || '—' }}</span></div>
          </div>
        </div>

        <div class="flex justify-end pt-2">
          <button
            type="button"
            @click="showEHealthModal = false"
            class="px-4 py-2 rounded text-xs font-bold text-white bg-[#00a65a] hover:bg-[#008d4c] transition-colors"
          >
            Close
          </button>
        </div>
      </div>
    </div>
  </TenantLayout>
</template>

<script setup>
import { ref } from 'vue'
import { Head, Link, useForm } from '@inertiajs/inertia-vue3'
import TenantLayout from '@/Layouts/TenantLayout.vue'

const props = defineProps({
  suggestedIdNumber: [Number, String],
})

const fileInput = ref(null)
const avatarPreview = ref(null)
const showEHealthModal = ref(false)

const form = useForm({
  id_number: props.suggestedIdNumber ? Number(props.suggestedIdNumber) : 571,
  firstname: '',
  lastname: '',
  gender: 'male',
  dob: '',
  age: 0,
  phone: '',
  email: '',
  profession: '',
  address: '',
  balance: 0.00,
  card_activated: false,
  card_code: '',
  is_trail_card: false,
  avatar: null,
})

function triggerFileInput() {
  fileInput.value?.click()
}

function handleAvatarChange(e) {
  const file = e.target.files?.[0]
  if (file) {
    form.avatar = file
    avatarPreview.value = URL.createObjectURL(file)
  }
}

function removeAvatar() {
  form.avatar = null
  avatarPreview.value = null
  if (fileInput.value) {
    fileInput.value.value = ''
  }
}

function onDobChange() {
  if (form.dob) {
    const birthDate = new Date(form.dob)
    const today = new Date()
    let calculatedAge = today.getFullYear() - birthDate.getFullYear()
    const m = today.getMonth() - birthDate.getMonth()
    if (m < 0 || (m === 0 && today.getDate() < birthDate.getDate())) {
      calculatedAge--
    }
    form.age = Math.max(calculatedAge, 0)
  }
}

function submit() {
  form.post(route('patients.store'), {
    forceFormData: true,
  })
}
</script>
