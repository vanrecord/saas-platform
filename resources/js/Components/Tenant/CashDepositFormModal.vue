<template>
  <div v-if="show" class="fixed inset-0 z-[70] overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
    <!-- Backdrop (static, does not close on backdrop click) -->
    <div class="fixed inset-0 bg-slate-900/60 backdrop-blur-xs transition-opacity"></div>

    <div class="flex min-h-screen items-center justify-center p-4 text-center sm:p-0">
      <div
        class="relative transform overflow-hidden rounded-2xl bg-white dark:bg-darker text-left shadow-2xl transition-all sm:my-8 sm:w-full sm:max-w-lg border border-slate-200 dark:border-slate-700 animate-in fade-in zoom-in duration-150"
      >
        <!-- Modal Header -->
        <div class="flex items-center justify-between border-b border-slate-100 dark:border-slate-700 px-6 py-4 bg-slate-50/50 dark:bg-dark">
          <div class="flex items-center gap-2.5">
            <span class="w-8 h-8 rounded-lg bg-blue-100 dark:bg-blue-900/30 text-[#0D63D6] flex items-center justify-center font-bold text-sm">
              <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z" />
              </svg>
            </span>
            <div>
              <h3 class="text-base font-bold text-[#152B45] dark:text-light" id="modal-title">
                {{ isEditing ? (tm('Edit_Cash_Deposit') || 'Edit Cash Deposit') : (tm('Create_Cash_Deposit') || 'Create Cash Deposit') }}
              </h3>
              <p class="text-xs text-slate-500 dark:text-slate-400">
                {{ isEditing ? 'Update deposit details' : 'Record a new patient cash or bank deposit' }}
              </p>
            </div>
          </div>
          <button
            type="button"
            @click="close"
            class="rounded-lg p-1 text-slate-400 hover:bg-slate-100 dark:hover:bg-gray-700 hover:text-slate-600 transition"
          >
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
            </svg>
          </button>
        </div>

        <!-- Form Body -->
        <form @submit.prevent="submit" class="p-6 space-y-4">
          <!-- Error Alert -->
          <div v-if="errorMessage" class="p-3 text-xs text-red-700 bg-red-50 dark:bg-red-900/20 dark:text-red-300 rounded-lg border border-red-200 dark:border-red-800">
            {{ errorMessage }}
          </div>

          <!-- 1. Patient Selection with Autocomplete -->
          <div>
            <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-1">
              {{ tm('Patient') || 'Patient' }} <span class="text-red-500">*</span>
            </label>

            <!-- Selected Patient Card -->
            <div
              v-if="selectedPatient"
              class="flex items-center justify-between p-2.5 rounded-lg border border-blue-200 bg-[#EBF3FE]/70 dark:bg-slate-800 dark:border-blue-900/50"
            >
              <div class="flex items-center gap-2.5 min-w-0">
                <div class="w-8 h-8 rounded-full bg-[#0D63D6] text-white flex items-center justify-center font-bold text-xs flex-shrink-0">
                  {{ patientInitials(selectedPatient) }}
                </div>
                <div class="min-w-0">
                  <div class="flex items-center gap-1.5">
                    <span class="font-bold text-[#152B45] dark:text-white text-xs truncate">
                      {{ selectedPatient.full_name || `${selectedPatient.firstname || ''} ${selectedPatient.lastname || ''}`.trim() }}
                    </span>
                    <span v-if="selectedPatient.id_number || selectedPatient.id" class="px-1.5 py-0.5 rounded bg-blue-100 text-[#0D63D6] dark:bg-blue-950 dark:text-blue-300 text-[10px] font-black uppercase font-mono">
                      #{{ selectedPatient.id_number || selectedPatient.id }}
                    </span>
                  </div>
                  <div class="text-[11px] text-slate-500 dark:text-slate-400 mt-0.5">
                    {{ tm('Remain') || 'Remain' }}: <strong class="text-emerald-600 font-mono">${{ Number(selectedPatient.balance || 0).toFixed(2) }}</strong>
                    <span v-if="selectedPatient.phone" class="ml-2">📞 {{ selectedPatient.phone }}</span>
                  </div>
                </div>
              </div>

              <button
                v-if="!isFixedPatient"
                type="button"
                @click="clearSelectedPatient"
                class="ml-2 px-2.5 py-1 rounded border border-slate-300 dark:border-slate-600 text-slate-600 dark:text-slate-300 hover:bg-white dark:hover:bg-slate-700 text-xs font-semibold transition flex-shrink-0"
              >
                {{ tm('Change') || 'Change' }}
              </button>
            </div>

            <!-- Autocomplete Search Input -->
            <div v-else class="relative">
              <div class="relative">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-slate-400">
                  <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                  </svg>
                </div>
                <input
                  v-model="patientSearchQuery"
                  type="text"
                  @input="onPatientSearchInput"
                  :placeholder="tm('Search_patient_placeholder') || 'Search patient (min 2 chars)...'"
                  class="w-full text-xs rounded-lg border-slate-300 dark:border-slate-600 dark:bg-slate-800 dark:text-white pl-9 pr-8 py-2 focus:border-[#0D63D6] focus:ring-2 focus:ring-blue-100 focus:outline-none transition"
                  :class="{ 'border-red-500': errors.patient_id }"
                />
                <div v-if="isSearchingPatient" class="absolute inset-y-0 right-0 pr-3 flex items-center">
                  <svg class="w-4 h-4 text-blue-600 animate-spin" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                  </svg>
                </div>
              </div>

              <!-- Search Results Dropdown -->
              <div
                v-if="patientSearchResults.length > 0"
                class="absolute left-0 right-0 mt-1 bg-white dark:bg-slate-800 rounded-lg shadow-xl border border-slate-200 dark:border-slate-700 z-30 max-h-56 overflow-y-auto divide-y divide-slate-100 dark:divide-slate-700"
              >
                <button
                  v-for="p in patientSearchResults"
                  :key="p.id"
                  type="button"
                  @click="selectPatient(p)"
                  class="w-full text-left px-3 py-2.5 hover:bg-[#EBF3FE]/70 dark:hover:bg-slate-700 flex items-center justify-between transition-colors group cursor-pointer"
                >
                  <div class="flex items-center gap-2.5">
                    <div class="w-7 h-7 rounded-full bg-slate-100 dark:bg-slate-700 text-[#0D63D6] dark:text-blue-400 group-hover:bg-[#0D63D6] group-hover:text-white transition flex items-center justify-center font-bold text-xs flex-shrink-0">
                      {{ patientInitials(p) }}
                    </div>
                    <div>
                      <div class="font-bold text-xs text-slate-800 dark:text-white">
                        {{ p.full_name || `${p.firstname || ''} ${p.lastname || ''}`.trim() }}
                      </div>
                      <div class="text-[10px] text-slate-500 dark:text-slate-400 font-mono">
                        #{{ p.id_number || p.id }} <span v-if="p.phone">&middot; {{ p.phone }}</span>
                      </div>
                    </div>
                  </div>
                  <div class="text-right">
                    <span class="text-[11px] font-mono font-bold text-emerald-600">
                      ${{ Number(p.balance || 0).toFixed(2) }}
                    </span>
                  </div>
                </button>
              </div>
            </div>
            <p v-if="errors.patient_id" class="mt-1 text-xs text-red-500">{{ errors.patient_id }}</p>
          </div>

          <!-- 2. Code & Date Row -->
          <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <!-- Code -->
            <div>
              <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-1">
                {{ tm('Code') || 'Code' }}
              </label>
              <input
                type="text"
                :value="form.formatted_code"
                readonly
                class="w-full rounded-lg border border-slate-300 bg-slate-100 dark:bg-slate-800 dark:border-slate-600 dark:text-slate-300 px-3 py-2 text-xs font-mono font-bold text-slate-600 focus:outline-none"
              />
            </div>

            <!-- Date using DateInput component -->
            <div>
              <DateInput
                v-model="form.date"
                :label="tm('Date') || 'Date'"
                placeholder="YYYY-MM-DD"
                :error="errors.date"
              />
            </div>
          </div>

          <!-- 3. Currency & Amount Row -->
          <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <!-- Currency -->
            <div>
              <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-1">
                {{ tm('Currency') || 'Currency' }} <span class="text-red-500">*</span>
              </label>
              <select
                v-model="form.currency"
                required
                class="w-full rounded-lg border border-slate-300 px-3 py-2 text-xs text-[#152B45] focus:border-[#0D63D6] focus:ring-2 focus:ring-blue-100 focus:outline-none dark:bg-slate-800 dark:border-slate-600 dark:text-white transition"
              >
                <option value="USD">USD ($)</option>
                <option value="KHR">KHR (៛)</option>
              </select>
            </div>

            <!-- Amount -->
            <div>
              <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-1">
                {{ tm('Amount') || 'Amount' }} <span class="text-red-500">*</span>
              </label>
              <div class="relative">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-slate-500 font-bold text-xs">
                  {{ form.currency === 'KHR' ? '៛' : '$' }}
                </div>
                <input
                  type="number"
                  step="0.01"
                  min="0.01"
                  v-model.number="form.amount"
                  required
                  placeholder="0.00"
                  class="w-full rounded-lg border border-slate-300 pl-7 pr-3 py-2 text-xs font-mono font-bold text-[#152B45] focus:border-[#0D63D6] focus:ring-2 focus:ring-blue-100 focus:outline-none dark:bg-slate-800 dark:border-slate-600 dark:text-white transition"
                  :class="{ 'border-red-500': errors.amount }"
                />
              </div>
              <p v-if="errors.amount" class="mt-1 text-xs text-red-500">{{ errors.amount }}</p>
            </div>
          </div>

          <!-- 4. Deposit Type Radio Buttons (Cash vs Bank) -->
          <div>
            <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-1.5">
              {{ tm('Deposit_Type') || 'Deposit Type' }} <span class="text-red-500">*</span>
            </label>
            <div class="grid grid-cols-2 gap-3">
              <!-- Cash Radio -->
              <label
                class="flex items-center gap-2.5 p-3 rounded-lg border cursor-pointer transition select-none"
                :class="form.is_cash
                  ? 'border-[#0D63D6] bg-[#EBF3FE]/80 dark:bg-blue-950/40 text-[#0D63D6] dark:text-blue-300 font-bold'
                  : 'border-slate-200 dark:border-slate-700 hover:bg-slate-50 dark:hover:bg-slate-800 text-slate-700 dark:text-slate-300'"
              >
                <input
                  type="radio"
                  :value="true"
                  v-model="form.is_cash"
                  class="w-4 h-4 text-[#0D63D6] focus:ring-[#0D63D6] border-slate-300"
                />
                <span class="text-xs">{{ tm('Cash') || 'Cash' }}</span>
              </label>

              <!-- Bank Radio -->
              <label
                class="flex items-center gap-2.5 p-3 rounded-lg border cursor-pointer transition select-none"
                :class="!form.is_cash
                  ? 'border-[#0D63D6] bg-[#EBF3FE]/80 dark:bg-blue-950/40 text-[#0D63D6] dark:text-blue-300 font-bold'
                  : 'border-slate-200 dark:border-slate-700 hover:bg-slate-50 dark:hover:bg-slate-800 text-slate-700 dark:text-slate-300'"
              >
                <input
                  type="radio"
                  :value="false"
                  v-model="form.is_cash"
                  class="w-4 h-4 text-[#0D63D6] focus:ring-[#0D63D6] border-slate-300"
                />
                <span class="text-xs">{{ tm('Bank') || 'Bank' }}</span>
              </label>
            </div>
          </div>

          <!-- 5. Note -->
          <div>
            <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-1">
              {{ tm('Note') || 'Note' }}
            </label>
            <textarea
              v-model="form.note"
              rows="3"
              placeholder="Optional remarks..."
              class="w-full rounded-lg border border-slate-300 px-3.5 py-2 text-xs text-[#152B45] focus:border-[#0D63D6] focus:ring-2 focus:ring-blue-100 focus:outline-none dark:bg-slate-800 dark:border-slate-600 dark:text-white transition"
            ></textarea>
          </div>

          <!-- Modal Footer Actions -->
          <div class="flex items-center justify-end gap-2.5 pt-3 border-t border-slate-100 dark:border-slate-700">
            <button
              type="button"
              @click="close"
              class="px-4 py-2 text-xs font-semibold text-slate-600 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800 rounded-lg transition"
            >
              {{ tm('Cancel') || 'Cancel' }}
            </button>
            <button
              type="submit"
              :disabled="isSubmitting"
              class="inline-flex items-center gap-1.5 px-4 py-2 text-xs font-bold text-white bg-[#0D63D6] hover:bg-blue-700 rounded-lg shadow-sm transition disabled:opacity-50 cursor-pointer"
            >
              <svg v-if="isSubmitting" class="w-3.5 h-3.5 animate-spin" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
              </svg>
              <span>{{ isEditing ? (tm('Update') || 'Update') : (tm('Save') || 'Save') }}</span>
            </button>
          </div>
        </form>
      </div>
    </div>
  </div>
</template>

<script>
import axios from 'axios'
import DateInput from '@/Components/Forms/DateInput.vue'
import { useToast } from 'vue-toastification'

export default {
  name: 'CashDepositFormModal',
  components: {
    DateInput,
  },
  props: {
    show: {
      type: Boolean,
      default: false,
    },
    deposit: {
      type: Object,
      default: null,
    },
    patient: {
      type: Object,
      default: null,
    },
    nextCode: {
      type: [Number, String],
      default: 1,
    },
  },
  emits: ['close', 'saved'],
  data() {
    return {
      form: {
        id: null,
        patient_id: '',
        code: '',
        formatted_code: '',
        currency: 'USD',
        amount: '',
        is_cash: true,
        date: '',
        note: '',
      },
      selectedPatient: null,
      patientSearchQuery: '',
      patientSearchResults: [],
      isSearchingPatient: false,
      patientSearchTimer: null,
      isSubmitting: false,
      errorMessage: '',
      errors: {},
    }
  },
  computed: {
    isEditing() {
      return Boolean(this.deposit && this.deposit.id)
    },
    isFixedPatient() {
      return Boolean(this.patient && this.patient.id)
    },
  },
  watch: {
    show(val) {
      if (val) {
        this.initForm()
      }
    },
    deposit: {
      handler() {
        if (this.show) {
          this.initForm()
        }
      },
      deep: true,
    },
  },
  methods: {
    initForm() {
      this.errorMessage = ''
      this.errors = {}
      this.patientSearchQuery = ''
      this.patientSearchResults = []

      const today = new Date().toISOString().slice(0, 10)

      if (this.deposit && this.deposit.id) {
        // Edit mode
        const d = this.deposit
        this.form = {
          id: d.id,
          patient_id: d.patient_id,
          code: d.code_raw || d.code,
          formatted_code: d.code || String(d.id).padStart(6, '0'),
          currency: d.currency || 'USD',
          amount: d.amount,
          is_cash: d.is_cash !== undefined ? Boolean(d.is_cash) : true,
          date: d.date || today,
          note: d.note || '',
        }
        if (d.patient) {
          this.selectedPatient = d.patient
        } else if (this.patient) {
          this.selectedPatient = this.patient
        } else if (d.patient_name) {
          this.selectedPatient = {
            id: d.patient_id,
            full_name: d.patient_name,
            id_number: d.patient_id,
          }
        }
      } else {
        // Create mode
        const codeNum = Number(this.nextCode || 1)
        this.form = {
          id: null,
          patient_id: this.patient ? this.patient.id : '',
          code: codeNum,
          formatted_code: String(codeNum).padStart(6, '0'),
          currency: 'USD',
          amount: '',
          is_cash: true,
          date: today,
          note: '',
        }
        this.selectedPatient = this.patient ? { ...this.patient } : null
      }
    },
    patientInitials(p) {
      if (!p) return '?'
      const first = (p.firstname || '').charAt(0).toUpperCase()
      const last = (p.lastname || '').charAt(0).toUpperCase()
      if (first || last) return `${first}${last}`
      if (p.full_name) {
        const parts = p.full_name.trim().split(/\s+/)
        if (parts.length >= 2) return `${parts[0][0]}${parts[1][0]}`.toUpperCase()
        return parts[0][0].toUpperCase()
      }
      return '?'
    },
    onPatientSearchInput() {
      clearTimeout(this.patientSearchTimer)
      const q = (this.patientSearchQuery || '').trim()

      if (q.length < 2) {
        this.patientSearchResults = []
        this.isSearchingPatient = false
        return
      }

      this.isSearchingPatient = true
      this.patientSearchTimer = setTimeout(async () => {
        try {
          const res = await axios.get(route('cash-deposits.patient-search'), { params: { q } })
          this.patientSearchResults = res.data || []
        } catch (e) {
          this.patientSearchResults = []
        } finally {
          this.isSearchingPatient = false
        }
      }, 250)
    },
    selectPatient(p) {
      this.selectedPatient = p
      this.form.patient_id = p.id
      this.patientSearchQuery = ''
      this.patientSearchResults = []
      delete this.errors.patient_id
    },
    clearSelectedPatient() {
      this.selectedPatient = null
      this.form.patient_id = ''
      this.patientSearchQuery = ''
      this.patientSearchResults = []
    },
    async submit() {
      this.errorMessage = ''
      this.errors = {}

      if (!this.form.patient_id) {
        this.errors.patient_id = 'Please select a patient.'
        return
      }

      if (!this.form.amount || Number(this.form.amount) <= 0) {
        this.errors.amount = 'Deposit amount must be greater than zero.'
        return
      }

      this.isSubmitting = true

      try {
        let res
        if (this.isEditing) {
          res = await axios.put(route('cash-deposits.update', this.form.id), this.form)
        } else {
          res = await axios.post(route('cash-deposits.store'), this.form)
        }

        useToast().success(
          this.isEditing
            ? (this.tm('Cash_deposit_updated_successfully') || 'Cash deposit updated successfully.')
            : (this.tm('Cash_deposit_created_successfully') || 'Cash deposit created successfully.')
        )

        this.$emit('saved', res.data?.data)
        this.close()
      } catch (err) {
        if (err.response && err.response.data) {
          const data = err.response.data
          if (data.errors) {
            this.errors = Object.keys(data.errors).reduce((acc, key) => {
              acc[key] = Array.isArray(data.errors[key]) ? data.errors[key][0] : data.errors[key]
              return acc
            }, {})
          }
          this.errorMessage = data.error || data.message || 'Failed to save cash deposit.'
        } else {
          this.errorMessage = err.message || 'An unexpected error occurred.'
        }
      } finally {
        this.isSubmitting = false
      }
    },
    close() {
      this.$emit('close')
    },
  },
}
</script>
