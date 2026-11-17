<template>
  <div v-if="show" class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
    <!-- Backdrop -->
    <div class="fixed inset-0 bg-slate-900/50 backdrop-blur-xs transition-opacity" @click="close"></div>

    <div class="flex min-h-screen items-center justify-center p-2 sm:p-4 text-center">
      <div
        class="relative transform overflow-hidden rounded-xl bg-white dark:bg-darker text-left shadow-2xl transition-all sm:my-8 w-full max-w-[95vw] lg:max-w-7xl xl:max-w-[1450px] border border-slate-200 dark:border-slate-700 animate-in fade-in zoom-in duration-150"
      >
        <!-- Modal Top Slim Header -->
        <div class="flex items-center justify-between border-b border-slate-200 dark:border-slate-700 px-4 py-2.5 bg-slate-50/70 dark:bg-dark">
          <div class="flex items-center gap-2">
            <button
              type="button"
              @click="resetToCreate"
              title="Add New Prescription"
              class="w-6 h-6 flex items-center justify-center text-slate-500 hover:text-slate-800 dark:text-slate-400 dark:hover:text-white font-bold text-lg leading-none rounded hover:bg-slate-200 dark:hover:bg-slate-700 transition"
            >
              +
            </button>
            <span class="text-xs font-bold text-slate-700 dark:text-slate-300">
              {{ isEditing ? (tm('Edit_Prescription') || 'Edit Prescription') : (tm('Create_Prescription') || 'Create Prescription') }}
            </span>
          </div>
          <button
            type="button"
            @click="close"
            class="text-slate-400 hover:text-slate-600 dark:hover:text-slate-200 font-bold text-lg leading-none p-1 rounded transition"
          >
            &times;
          </button>
        </div>

        <!-- Form Body -->
        <form @submit.prevent="submit" class="p-4 sm:p-6 space-y-4">
          <!-- Patient and Medical History Section Card -->
          <div class="border border-slate-200 dark:border-slate-700 rounded-lg p-3.5 bg-white dark:bg-slate-800 flex flex-wrap items-center justify-between gap-3 shadow-2xs">
            <div class="flex items-center gap-4 flex-wrap flex-1">
              <!-- Patient Search / Selection (Enter 2+ chars like Visit Registration) -->
              <div class="flex items-center gap-2">
                <label class="text-xs font-semibold text-slate-700 dark:text-slate-300 whitespace-nowrap">
                  {{ tm('Patient') || 'Patient' }} <span class="text-red-500">*</span>
                </label>

                <!-- When Patient is Selected -->
                <div
                  v-if="selectedPatient"
                  class="flex items-center justify-between p-1.5 sm:p-2 rounded-lg border border-blue-200 bg-[#EBF3FE]/70 dark:bg-slate-900 dark:border-blue-900/50 min-w-[220px] sm:min-w-[260px]"
                >
                  <div class="flex items-center gap-2 min-w-0">
                    <div class="w-6 h-6 rounded-full bg-[#0D63D6] text-white flex items-center justify-center font-bold text-[10px] flex-shrink-0 shadow-2xs">
                      {{ patientInitials(selectedPatient) }}
                    </div>
                    <div class="min-w-0">
                      <div class="flex items-center gap-1.5">
                        <span class="font-bold text-[#152B45] dark:text-white text-xs truncate max-w-[120px] sm:max-w-[160px]">
                          {{ selectedPatient.full_name || `${selectedPatient.firstname || ''} ${selectedPatient.lastname || ''}`.trim() }}
                        </span>
                        <span class="px-1 py-0.2 rounded bg-blue-100 text-[#0D63D6] dark:bg-blue-950 dark:text-blue-300 text-[10px] font-black uppercase">
                          #{{ selectedPatient.id_number || selectedPatient.id }}
                        </span>
                      </div>
                      <div v-if="selectedPatient.phone" class="text-[10px] text-slate-500 truncate">
                        📞 {{ selectedPatient.phone }}
                      </div>
                    </div>
                  </div>

                  <button
                    type="button"
                    @click="clearSelectedPatient"
                    class="ml-2 px-2 py-0.5 rounded border border-slate-300 dark:border-slate-600 text-slate-600 dark:text-slate-300 hover:bg-white dark:hover:bg-slate-800 text-[11px] font-bold transition flex-shrink-0"
                  >
                    {{ tm('Change') || 'Change' }}
                  </button>
                </div>

                <!-- Search Input When No Patient Selected -->
                <div v-else class="relative min-w-[220px] sm:min-w-[280px]">
                  <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-2.5 flex items-center pointer-events-none text-slate-400">
                      <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                      </svg>
                    </div>
                    <input
                      v-model="patientSearchQuery"
                      type="text"
                      @input="onPatientSearchInput"
                      :placeholder="tm('Search_patient_placeholder') || 'Search patient (2+ chars)...'"
                      class="w-full text-xs rounded-lg border-slate-300 dark:border-slate-600 dark:bg-slate-900 dark:text-white pl-8 pr-7 py-1.5 shadow-2xs focus:border-blue-500 focus:ring-1 focus:ring-blue-500"
                    />
                    <div v-if="isSearchingPatient" class="absolute inset-y-0 right-0 pr-2 flex items-center">
                      <svg class="w-3.5 h-3.5 text-blue-600 animate-spin" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                      </svg>
                    </div>
                  </div>

                  <!-- Auto-suggest Dropdown -->
                  <div
                    v-if="patientSearchResults.length > 0"
                    class="absolute left-0 right-0 mt-1 bg-white dark:bg-slate-800 rounded-lg shadow-xl border border-slate-200 dark:border-slate-700 z-30 max-h-56 overflow-y-auto divide-y divide-slate-100 dark:divide-slate-700"
                  >
                    <button
                      v-for="p in patientSearchResults"
                      :key="p.id"
                      type="button"
                      @click="selectPatient(p)"
                      class="w-full text-left px-3 py-2 hover:bg-[#EBF3FE]/70 dark:hover:bg-slate-700 flex items-center justify-between transition-colors group"
                    >
                      <div class="flex items-center gap-2">
                        <div class="w-6 h-6 rounded-full bg-slate-100 dark:bg-slate-700 text-[#0D63D6] dark:text-blue-400 group-hover:bg-[#0D63D6] group-hover:text-white transition flex items-center justify-center font-bold text-[10px] flex-shrink-0">
                          {{ patientInitials(p) }}
                        </div>
                        <div>
                          <div class="text-xs font-bold text-[#152B45] dark:text-white">
                            {{ p.full_name }}
                          </div>
                          <div class="text-[10px] text-slate-500 dark:text-slate-400">
                            ID: #{{ p.id_number || p.id }} <span v-if="p.phone" class="ml-1">• 📞 {{ p.phone }}</span>
                          </div>
                        </div>
                      </div>
                      <span class="text-[11px] font-bold text-[#0D63D6] group-hover:translate-x-0.5 transition-transform flex-shrink-0">
                        {{ tm('Select') || 'Select' }} →
                      </span>
                    </button>
                  </div>

                  <!-- No Results Notice -->
                  <div
                    v-else-if="patientSearchQuery.trim().length >= 2 && !isSearchingPatient && hasSearchedPatient"
                    class="absolute left-0 right-0 mt-1 bg-white dark:bg-slate-800 rounded-lg shadow-lg border border-slate-200 dark:border-slate-700 p-2.5 text-center z-30 text-xs text-slate-500"
                  >
                    {{ tm('No_existing_patient_found') || 'No patient found' }} "<span class="font-bold text-slate-700 dark:text-slate-200">{{ patientSearchQuery }}</span>".
                  </div>
                </div>
              </div>

              <!-- Medical History Dropdown -->
              <div class="flex items-center gap-2">
                <select
                  v-model="form.medical_history_id"
                  class="rounded-md border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-900 px-3 py-1.5 text-xs text-slate-800 dark:text-slate-100 focus:border-blue-500 focus:ring-1 focus:ring-blue-500 min-w-[220px] sm:min-w-[260px]"
                >
                  <option value="">{{ tm('Select_Medical_History') || 'Select Medical History' }}</option>
                  <option v-for="mh in patientHistories" :key="mh.id" :value="mh.id">
                    {{ mh.label }}
                  </option>
                </select>
              </div>
            </div>

            <!-- Add Medicine Button on the right -->
            <div>
              <button
                type="button"
                @click="addMedicineRow"
                class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-semibold text-emerald-600 hover:text-emerald-700 dark:text-emerald-400 border border-emerald-500 hover:border-emerald-600 rounded-md bg-white hover:bg-emerald-50/60 dark:bg-slate-900 transition shadow-2xs"
              >
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4" />
                </svg>
                <span>{{ tm('Add_Medicine') || 'Add Medicine' }}</span>
              </button>
            </div>
          </div>

          <!-- Prescription Medicine Details Table -->
          <div class="overflow-x-auto border border-slate-200 dark:border-slate-700 rounded-lg shadow-2xs">
            <table class="w-full border-collapse text-xs">
              <thead>
                <tr class="bg-slate-50 dark:bg-slate-800 text-slate-700 dark:text-slate-200 font-semibold border-b border-slate-200 dark:border-slate-700 text-left">
                  <th class="p-2.5 border-r border-slate-200 dark:border-slate-700 min-w-[200px]">
                    Medicine <span class="text-red-500">*</span>
                  </th>
                  <th class="p-2.5 border-r border-slate-200 dark:border-slate-700 w-24 text-center">
                    Dose
                  </th>
                  <th class="p-2.5 border-r border-slate-200 dark:border-slate-700 w-24 text-center">
                    Qty <span class="text-red-500">*</span>
                  </th>
                  <th class="p-2.5 border-r border-slate-200 dark:border-slate-700 w-24 text-center">
                    U/D <span class="text-red-500">*</span>
                  </th>
                  <th class="p-2.5 border-r border-slate-200 dark:border-slate-700 w-28 text-center">
                    Total
                  </th>
                  <th class="p-2.5 border-r border-slate-200 dark:border-slate-700 min-w-[180px]">
                    Usage Time <span class="text-red-500">*</span>
                  </th>
                  <th class="p-2.5 border-r border-slate-200 dark:border-slate-700 min-w-[140px]">
                    Note
                  </th>
                  <th class="p-2.5 text-center w-24">
                    Action
                  </th>
                </tr>
              </thead>
              <tbody class="divide-y divide-slate-200 dark:divide-slate-700 bg-white dark:bg-slate-900">
                <tr v-for="(row, idx) in form.details" :key="idx" class="hover:bg-slate-50/50 dark:hover:bg-slate-800/40">
                  <!-- Medicine Select -->
                  <td class="p-2 border-r border-slate-200 dark:border-slate-700">
                    <select
                      v-model="row.medicine_id"
                      required
                      class="w-full rounded border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-900 px-2.5 py-1.5 text-xs text-slate-800 dark:text-slate-100 focus:border-blue-500 focus:ring-1 focus:ring-blue-500"
                    >
                      <option value="" disabled>Select Medicine</option>
                      <option v-for="m in medicines" :key="m.id" :value="m.id">
                        {{ m.name }}
                      </option>
                    </select>
                  </td>

                  <!-- Dose -->
                  <td class="p-2 border-r border-slate-200 dark:border-slate-700">
                    <input
                      type="number"
                      step="any"
                      min="0"
                      v-model.number="row.dose"
                      class="w-full rounded border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-900 px-2 py-1.5 text-xs text-center text-slate-800 dark:text-slate-100 focus:border-blue-500 focus:ring-1 focus:ring-blue-500"
                    />
                  </td>

                  <!-- Qty -->
                  <td class="p-2 border-r border-slate-200 dark:border-slate-700">
                    <input
                      type="number"
                      step="any"
                      min="0.01"
                      required
                      v-model.number="row.qty"
                      class="w-full rounded border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-900 px-2 py-1.5 text-xs text-center text-slate-800 dark:text-slate-100 focus:border-blue-500 focus:ring-1 focus:ring-blue-500"
                    />
                  </td>

                  <!-- U/D -->
                  <td class="p-2 border-r border-slate-200 dark:border-slate-700">
                    <input
                      type="number"
                      step="any"
                      min="0.01"
                      required
                      v-model.number="row.ud"
                      class="w-full rounded border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-900 px-2 py-1.5 text-xs text-center text-slate-800 dark:text-slate-100 focus:border-blue-500 focus:ring-1 focus:ring-blue-500"
                    />
                  </td>

                  <!-- Total (reactive display) -->
                  <td class="p-2 border-r border-slate-200 dark:border-slate-700 text-center font-semibold text-slate-700 dark:text-slate-300 bg-slate-50/50 dark:bg-slate-800/50 whitespace-nowrap">
                    {{ calculateRowTotal(row) }} គ្រាប់
                  </td>

                  <!-- Usage Time 4 Checkboxes (2x2 grid) -->
                  <td class="p-2 border-r border-slate-200 dark:border-slate-700">
                    <div class="grid grid-cols-2 gap-x-2 gap-y-1.5 text-[11px] text-slate-700 dark:text-slate-300">
                      <label class="flex items-center gap-1.5 cursor-pointer select-none">
                        <input
                          type="checkbox"
                          v-model="row.morning"
                          class="rounded border-slate-300 text-blue-600 focus:ring-blue-500 w-3.5 h-3.5"
                        />
                        <span>Morning</span>
                      </label>
                      <label class="flex items-center gap-1.5 cursor-pointer select-none">
                        <input
                          type="checkbox"
                          v-model="row.afternoon"
                          class="rounded border-slate-300 text-blue-600 focus:ring-blue-500 w-3.5 h-3.5"
                        />
                        <span>Afternoon</span>
                      </label>
                      <label class="flex items-center gap-1.5 cursor-pointer select-none">
                        <input
                          type="checkbox"
                          v-model="row.evening"
                          class="rounded border-slate-300 text-blue-600 focus:ring-blue-500 w-3.5 h-3.5"
                        />
                        <span>Evening</span>
                      </label>
                      <label class="flex items-center gap-1.5 cursor-pointer select-none">
                        <input
                          type="checkbox"
                          v-model="row.night"
                          class="rounded border-slate-300 text-blue-600 focus:ring-blue-500 w-3.5 h-3.5"
                        />
                        <span>Night</span>
                      </label>
                    </div>
                  </td>

                  <!-- Note -->
                  <td class="p-2 border-r border-slate-200 dark:border-slate-700">
                    <textarea
                      :ref="`noteInput_${idx}`"
                      v-model="row.note"
                      rows="2"
                      placeholder="Note"
                      class="w-full rounded border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-900 p-1.5 text-[11px] text-slate-800 dark:text-slate-100 resize-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500"
                    ></textarea>
                  </td>

                  <!-- Action Buttons -->
                  <td class="p-2 text-center whitespace-nowrap">
                    <div class="flex items-center justify-center gap-1.5">
                      <button
                        type="button"
                        @click="focusNote(idx)"
                        class="inline-block px-2.5 py-1 text-[11px] font-bold text-white bg-[#00A0B0] hover:bg-[#008b99] rounded shadow-2xs transition"
                      >
                        Note
                      </button>
                      <button
                        v-if="form.details.length > 1"
                        type="button"
                        @click="removeMedicineRow(idx)"
                        class="p-1 text-red-500 hover:text-red-700 rounded hover:bg-red-50 dark:hover:bg-red-950/30 transition"
                        title="Remove row"
                      >
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                        </svg>
                      </button>
                    </div>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>

          <!-- Error Alert if any -->
          <div v-if="errorMessage" class="p-3 bg-red-50 border border-red-200 rounded-lg text-xs text-red-700">
            {{ errorMessage }}
          </div>

          <!-- Modal Footer -->
          <div class="pt-4 border-t border-slate-200 dark:border-slate-700 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
            <!-- Explanatory Dosage / Usage Text in Khmer (from screenshot) -->
            <div class="text-xs text-slate-600 dark:text-slate-400 font-khmer leading-relaxed">
              Dose = កម្រិតថ្នាំ/ខ. 500mg , Qty = ចំនួនគ្រាប់សម្រាប់ញ៉ាំក្នុងមួយដង , U/D = ចំនួនថ្ងៃត្រូវញ៉ាំ , Usage time = ពេលវេលាញ៉ាំថ្នាំ
            </div>

            <!-- Footer Buttons: Following MedicineModal Style -->
            <div class="flex items-center space-x-3 self-end sm:self-auto shrink-0">
              <button
                type="button"
                @click="close"
                class="px-4 py-2 text-xs font-semibold text-slate-600 hover:text-slate-800 border border-slate-300 rounded-lg hover:bg-slate-50 dark:text-gray-300 dark:border-gray-600 dark:hover:bg-gray-700 transition"
              >
                {{ tm('Cancel') || 'Cancel' }}
              </button>

              <button
                type="submit"
                :disabled="form.processing"
                class="inline-flex items-center px-4 py-2 text-xs font-semibold text-white bg-primary hover:bg-primary-dark rounded-lg transition shadow-xs disabled:opacity-50"
              >
                <svg
                  v-if="form.processing"
                  class="animate-spin -ml-1 mr-2 h-4 w-4 text-white"
                  fill="none"
                  viewBox="0 0 24 24"
                >
                  <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                  <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
                <span>{{ form.processing ? (tm('Saving') || 'Saving...') : (isEditing ? (tm('Save') || 'Update Prescription') : (tm('Save') || 'Save')) }}</span>
              </button>
            </div>
          </div>
        </form>
      </div>
    </div>
  </div>
</template>

<script>
import { defineComponent } from 'vue'
import { useForm } from '@inertiajs/inertia-vue3'
import { useI18n } from 'vue-i18n'
import { lang } from '@/Lang'
import { useToast } from 'vue-toastification'
import axios from 'axios'

export default defineComponent({
  name: 'PrescriptionModal',
  props: {
    show: {
      type: Boolean,
      default: false,
    },
    prescription: {
      type: Object,
      default: null,
    },
    patients: {
      type: Array,
      default: () => [],
    },
    medicines: {
      type: Array,
      default: () => [],
    },
  },
  emits: ['close', 'saved'],
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
      errorMessage: '',
      patientHistories: [],
      selectedPatient: null,
      patientSearchQuery: '',
      patientSearchResults: [],
      isSearchingPatient: false,
      hasSearchedPatient: false,
      patientSearchTimer: null,
      form: useForm({
        patient_id: '',
        medical_history_id: '',
        prescription: '',
        details: [
          this.createEmptyRow(),
        ],
      }),
    }
  },
  computed: {
    isEditing() {
      return Boolean(this.prescription && this.prescription.id)
    },
  },
  watch: {
    show(newVal) {
      if (newVal) {
        this.initForm()
      } else {
        this.errorMessage = ''
      }
    },
    'form.patient_id'(newId) {
      if (newId) {
        this.fetchPatientHistories(newId)
      } else {
        this.patientHistories = []
      }
    },
  },
  methods: {
    createEmptyRow() {
      return {
        medicine_id: '',
        dose: '',
        qty: '',
        ud: '',
        morning: true,
        afternoon: false,
        evening: true,
        night: false,
        note: '',
      }
    },
    formatPatientName(p) {
      if (!p) return ''
      const name = p.full_name || `${p.firstname || ''} ${p.lastname || ''}`.trim()
      const idNum = p.id_number ? ` (#${p.id_number})` : ''
      return (name || `Patient #${p.id}`) + idNum
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
        this.hasSearchedPatient = false
        return
      }

      this.isSearchingPatient = true
      this.patientSearchTimer = setTimeout(async () => {
        try {
          const res = await axios.get('/api/patients/search', { params: { q } })
          this.patientSearchResults = res.data || []
          this.hasSearchedPatient = true
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
      this.hasSearchedPatient = false
      this.fetchPatientHistories(p.id)
    },
    clearSelectedPatient() {
      this.selectedPatient = null
      this.form.patient_id = ''
      this.form.medical_history_id = ''
      this.patientHistories = []
      this.patientSearchQuery = ''
      this.patientSearchResults = []
      this.hasSearchedPatient = false
    },
    initForm() {
      this.errorMessage = ''
      this.patientSearchQuery = ''
      this.patientSearchResults = []
      this.hasSearchedPatient = false

      if (this.prescription && this.prescription.id) {
        const p = this.prescription
        this.form.patient_id = p.patient_id || ''
        this.form.medical_history_id = p.medical_history_id || ''
        this.form.prescription = p.prescription || ''

        if (p.patient) {
          this.selectedPatient = {
            ...p.patient,
            full_name: p.patient.full_name || `${p.patient.firstname || ''} ${p.patient.lastname || ''}`.trim(),
          }
        } else if (p.patient_id && this.patients && this.patients.length > 0) {
          const found = this.patients.find((pt) => pt.id === p.patient_id)
          if (found) {
            this.selectedPatient = {
              ...found,
              full_name: found.full_name || `${found.firstname || ''} ${found.lastname || ''}`.trim(),
            }
          } else {
            this.selectedPatient = null
          }
        } else {
          this.selectedPatient = null
        }

        if (p.prescription_details && p.prescription_details.length > 0) {
          this.form.details = p.prescription_details.map((d) => ({
            id: d.id,
            medicine_id: d.medicine_id || '',
            dose: d.dose ?? '',
            qty: d.dose_amount ?? '',
            ud: d.duration ?? d.unit_dose ?? '',
            morning: Boolean(d.morning),
            afternoon: Boolean(d.afternoon),
            evening: Boolean(d.evening),
            night: Boolean(d.night),
            note: d.note || '',
          }))
        } else {
          this.form.details = [this.createEmptyRow()]
        }

        if (this.form.patient_id) {
          this.fetchPatientHistories(this.form.patient_id)
        }
      } else {
        this.selectedPatient = null
        this.form.patient_id = ''
        this.form.medical_history_id = ''
        this.form.prescription = ''
        this.form.details = [this.createEmptyRow()]
        this.patientHistories = []
      }
    },
    resetToCreate() {
      this.selectedPatient = null
      this.patientSearchQuery = ''
      this.patientSearchResults = []
      this.hasSearchedPatient = false
      this.form.patient_id = ''
      this.form.medical_history_id = ''
      this.form.prescription = ''
      this.form.details = [this.createEmptyRow()]
      this.patientHistories = []
    },
    addMedicineRow() {
      this.form.details.push(this.createEmptyRow())
    },
    removeMedicineRow(idx) {
      if (this.form.details.length > 1) {
        this.form.details.splice(idx, 1)
      }
    },
    calculateRowTotal(row) {
      const morning = row.morning ? 1 : 0
      const afternoon = row.afternoon ? 1 : 0
      const evening = row.evening ? 1 : 0
      const night = row.night ? 1 : 0
      const times = morning + afternoon + evening + night
      const count = times > 0 ? times : 1
      const qty = parseFloat(row.qty) || 0
      const ud = parseFloat(row.ud) || 0
      const total = qty * count * ud
      return total % 1 === 0 ? total : total.toFixed(1)
    },
    focusNote(idx) {
      const ref = this.$refs[`noteInput_${idx}`]
      if (ref && ref[0]) {
        ref[0].focus()
      } else if (ref && ref.focus) {
        ref.focus()
      }
    },
    async fetchPatientHistories(patientId) {
      try {
        const res = await axios.get(route('prescriptions.patient-histories', patientId))
        this.patientHistories = res.data || []
      } catch (e) {
        this.patientHistories = []
      }
    },
    submit() {
      this.errorMessage = ''

      if (!this.form.patient_id) {
        this.errorMessage = 'Please select a patient.'
        return
      }

      if (!this.form.details || this.form.details.length === 0) {
        this.errorMessage = 'Please add at least one medicine.'
        return
      }

      for (let i = 0; i < this.form.details.length; i++) {
        const row = this.form.details[i]
        if (!row.medicine_id) {
          this.errorMessage = `Row #${i + 1}: Please select a medicine.`
          return
        }
        if (!row.qty || row.qty <= 0) {
          this.errorMessage = `Row #${i + 1}: Quantity must be greater than 0.`
          return
        }
        if (!row.ud || row.ud <= 0) {
          this.errorMessage = `Row #${i + 1}: U/D (number of days) must be greater than 0.`
          return
        }
        if (!row.morning && !row.afternoon && !row.evening && !row.night) {
          this.errorMessage = `Row #${i + 1}: Please select at least one usage time.`
          return
        }
      }

      if (this.isEditing) {
        this.form.put(route('prescriptions.update', this.prescription.id), {
          preserveScroll: true,
          onSuccess: () => {
            this.toast.success(this.tm('Prescription_updated_successfully') || 'Prescription updated successfully.')
            this.$emit('saved')
            this.close()
          },
          onError: (err) => {
            this.errorMessage = err.error || err.message || Object.values(err)[0] || 'Failed to update prescription.'
          },
        })
      } else {
        this.form.post(route('prescriptions.store'), {
          preserveScroll: true,
          onSuccess: () => {
            this.toast.success(this.tm('Prescription_created_successfully') || 'Prescription created successfully.')
            this.$emit('saved')
            this.close()
          },
          onError: (err) => {
            this.errorMessage = err.error || err.message || Object.values(err)[0] || 'Failed to create prescription.'
          },
        })
      }
    },
    close() {
      this.$emit('close')
    },
  },
})
</script>

<style scoped>
@import url('https://fonts.googleapis.com/css2?family=Battambang:wght@400;700&display=swap');

.font-khmer {
  font-family: 'Battambang', system-ui, sans-serif;
}
</style>
