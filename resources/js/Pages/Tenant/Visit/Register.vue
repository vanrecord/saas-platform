<template>
  <TenantLayout title="Patient Visit Registration">
    <div class="max-w-7xl mx-auto space-y-6">

      <!-- Page Header -->
      <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
          <h1 class="text-2xl sm:text-3xl font-extrabold text-[#152B45] tracking-tight">
            Patient Visit Registration
          </h1>
          <p class="text-xs sm:text-sm text-slate-500 mt-1">
            Register a patient for a clinical visit, create new billing invoices, or link follow-up consultations
          </p>
        </div>

        <button
          type="button"
          @click="isModalOpen = true"
          class="inline-flex items-center justify-center gap-2 px-5 py-2.5 rounded-lg text-xs font-bold text-white bg-[#0D63D6] hover:bg-[#094bb5] transition-all shadow-sm flex-shrink-0"
        >
          <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4" />
          </svg>
          <span>+ Create Patient</span>
        </button>
      </div>

      <!-- Main Layout: 2 Columns Grid -->
      <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-start">

        <!-- Left Column: Step 1 & Step 2 -->
        <div class="lg:col-span-8 space-y-6">

          <!-- STEP 1: Find or Select Patient -->
          <div class="bg-white rounded-xl border border-[#DCE3EC] p-5 sm:p-6 shadow-xs">
            <div class="flex items-center justify-between pb-4 border-b border-slate-100">
              <div class="flex items-center gap-2.5">
                <span class="w-6 h-6 rounded-full bg-[#0D63D6] text-white flex items-center justify-center text-xs font-bold flex-shrink-0 shadow-xs">
                  1
                </span>
                <h2 class="text-base font-bold text-[#152B45]">
                  Find or Select Patient
                </h2>
              </div>

              <button
                type="button"
                @click="isModalOpen = true"
                class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg border border-slate-200 text-slate-700 hover:bg-slate-50 text-xs font-bold transition shadow-xs"
              >
                <svg class="w-3.5 h-3.5 text-[#0D63D6]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4" />
                </svg>
                <span>+ Create Patient</span>
              </button>
            </div>

            <!-- Search Area / Selected Patient Card -->
            <div class="mt-4">
              <!-- When Patient is Selected -->
              <div
                v-if="selectedPatient"
                class="flex items-center justify-between p-4 rounded-xl border border-blue-200 bg-[#EBF3FE]/50"
              >
                <div class="flex items-center gap-3 min-w-0">
                  <div class="w-11 h-11 rounded-full bg-[#0D63D6] text-white flex items-center justify-center font-bold text-sm flex-shrink-0 shadow-xs">
                    {{ patientInitials(selectedPatient) }}
                  </div>
                  <div class="min-w-0">
                    <div class="flex items-center gap-2">
                      <span class="font-extrabold text-[#152B45] text-sm truncate">
                        {{ selectedPatient.full_name }}
                      </span>
                      <span class="px-2 py-0.5 rounded-md bg-blue-100 text-[#0D63D6] text-[10px] font-black tracking-wider uppercase">
                        {{ selectedPatient.patient_number }}
                      </span>
                    </div>
                    <div class="text-xs text-slate-500 mt-0.5 flex items-center gap-3 flex-wrap">
                      <span v-if="selectedPatient.phone">📞 {{ selectedPatient.phone }}</span>
                      <span v-if="selectedPatient.sex" class="capitalize">👤 {{ selectedPatient.sex }}</span>
                      <span v-if="selectedPatient.age">🎂 {{ selectedPatient.age }} yrs</span>
                    </div>
                  </div>
                </div>

                <button
                  type="button"
                  @click="clearSelectedPatient"
                  class="px-3 py-1.5 rounded-lg border border-slate-200 text-slate-600 hover:bg-white text-xs font-bold transition flex-shrink-0"
                >
                  Change
                </button>
              </div>

              <!-- Search Input When No Patient Selected -->
              <div v-else class="relative">
                <label class="block text-xs font-bold text-slate-700 mb-1.5">
                  Search Existing Patient
                  <span class="text-[11px] font-normal text-slate-400 ml-1">(enter at least 2 characters)</span>
                </label>

                <div class="relative">
                  <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                    </svg>
                  </div>
                  <input
                    v-model="searchQuery"
                    type="text"
                    @input="onSearchInput"
                    placeholder="Search by patient ID (e.g. P000125), full name, or phone number (min. 2 characters)..."
                    class="w-full rounded-lg border border-slate-300 pl-10 pr-10 py-2.5 text-xs sm:text-sm text-[#152B45] placeholder-slate-400 focus:border-[#0D63D6] focus:ring-2 focus:ring-blue-100 focus:outline-none transition bg-white"
                  />
                  <!-- Search Loading Spinner -->
                  <div v-if="isSearching" class="absolute inset-y-0 right-0 pr-3.5 flex items-center">
                    <svg class="w-4 h-4 text-[#0D63D6] animate-spin" fill="none" viewBox="0 0 24 24">
                      <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                      <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                  </div>
                </div>

                <!-- Auto-suggest Dropdown -->
                <div
                  v-if="searchResults.length > 0"
                  class="absolute left-0 right-0 mt-1.5 bg-white rounded-xl shadow-xl border border-slate-200 z-20 max-h-64 overflow-y-auto divide-y divide-slate-100"
                >
                  <button
                    v-for="patient in searchResults"
                    :key="patient.id"
                    type="button"
                    @click="selectPatient(patient)"
                    class="w-full text-left px-4 py-3 hover:bg-[#EBF3FE]/70 flex items-center justify-between transition-colors group"
                  >
                    <div class="flex items-center gap-3">
                      <div class="w-8 h-8 rounded-full bg-slate-100 text-[#0D63D6] group-hover:bg-[#0D63D6] group-hover:text-white transition flex items-center justify-center font-bold text-xs">
                        {{ patientInitials(patient) }}
                      </div>
                      <div>
                        <div class="text-xs sm:text-sm font-bold text-[#152B45]">
                          {{ patient.full_name }}
                        </div>
                        <div class="text-[11px] text-slate-500">
                          ID: <span class="font-semibold text-slate-700">{{ patient.patient_number }}</span>
                          <span v-if="patient.phone" class="ml-2">• 📞 {{ patient.phone }}</span>
                        </div>
                      </div>
                    </div>
                    <span class="text-xs font-bold text-[#0D63D6] group-hover:translate-x-0.5 transition-transform">
                      Select →
                    </span>
                  </button>
                </div>

                <!-- No Results Notice -->
                <div
                  v-else-if="searchQuery.trim().length >= 2 && !isSearching && hasSearched"
                  class="absolute left-0 right-0 mt-1.5 bg-white rounded-xl shadow-lg border border-slate-200 p-4 text-center z-20 text-xs text-slate-500"
                >
                  No existing patient found matching "<span class="font-bold text-slate-700">{{ searchQuery }}</span>".
                  <button
                    type="button"
                    @click="isModalOpen = true"
                    class="text-[#0D63D6] font-bold hover:underline ml-1"
                  >
                    + Register New Patient
                  </button>
                </div>
              </div>
            </div>
          </div>

          <!-- STEP 2: Registration & Billing Workflow -->
          <div class="bg-white rounded-xl border border-[#DCE3EC] p-5 sm:p-6 shadow-xs">
            <div class="flex items-center gap-2.5 pb-4 border-b border-slate-100">
              <span class="w-6 h-6 rounded-full bg-[#0D63D6] text-white flex items-center justify-center text-xs font-bold flex-shrink-0 shadow-xs">
                2
              </span>
              <h2 class="text-base font-bold text-[#152B45]">
                Registration & Billing Workflow
              </h2>
            </div>

            <!-- Selectable Workflow Cards -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mt-5">
              
              <!-- Option A: New Invoice -->
              <div
                @click="form.workflow_option = 'new_invoice'"
                :class="[
                  'relative rounded-xl p-4 cursor-pointer transition-all border-2 flex items-start gap-3.5 select-none',
                  form.workflow_option === 'new_invoice'
                    ? 'border-[#0D63D6] bg-[#F0FDF9] shadow-xs'
                    : 'border-[#DCE3EC] bg-white hover:border-slate-300'
                ]"
              >
                <!-- Radio Dot -->
                <div class="pt-0.5">
                  <div
                    :class="[
                      'w-4 h-4 rounded-full flex items-center justify-center transition-all',
                      form.workflow_option === 'new_invoice'
                        ? 'border-2 border-[#0D63D6] bg-white'
                        : 'border border-slate-300 bg-white'
                    ]"
                  >
                    <div
                      v-if="form.workflow_option === 'new_invoice'"
                      class="w-2 h-2 rounded-full bg-[#0D63D6]"
                    ></div>
                  </div>
                </div>

                <div>
                  <h3 class="text-xs sm:text-sm font-bold text-[#152B45]">
                    Option A: New Invoice
                  </h3>
                  <p class="text-xs text-slate-500 mt-1 leading-relaxed">
                    Create a new invoice and initial reception record for this visit.
                  </p>
                </div>
              </div>

              <!-- Option B: Follow-up Visit -->
              <div
                @click="form.workflow_option = 'follow_up'"
                :class="[
                  'relative rounded-xl p-4 cursor-pointer transition-all border-2 flex items-start gap-3.5 select-none',
                  form.workflow_option === 'follow_up'
                    ? 'border-[#0D63D6] bg-[#F0FDF9] shadow-xs'
                    : 'border-[#DCE3EC] bg-white hover:border-slate-300'
                ]"
              >
                <!-- Radio Dot -->
                <div class="pt-0.5">
                  <div
                    :class="[
                      'w-4 h-4 rounded-full flex items-center justify-center transition-all',
                      form.workflow_option === 'follow_up'
                        ? 'border-2 border-[#0D63D6] bg-white'
                        : 'border border-slate-300 bg-white'
                    ]"
                  >
                    <div
                      v-if="form.workflow_option === 'follow_up'"
                      class="w-2 h-2 rounded-full bg-[#0D63D6]"
                    ></div>
                  </div>
                </div>

                <div>
                  <h3 class="text-xs sm:text-sm font-bold text-[#152B45]">
                    Option B: Follow-up Visit
                  </h3>
                  <p class="text-xs text-slate-500 mt-1 leading-relaxed">
                    Link a new reception to an existing eligible invoice without creating another invoice.
                  </p>
                </div>
              </div>

            </div>

            <!-- Form Inputs -->
            <div class="space-y-4 mt-6">
              <!-- Row: Visit Type & Doctor -->
              <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                
                <!-- Visit Type -->
                <div>
                  <label class="block text-xs font-bold text-slate-700 mb-1.5">
                    Visit Type
                  </label>
                  <select
                    v-model="form.visit_type"
                    class="w-full rounded-lg border border-slate-300 px-3.5 py-2.5 text-xs sm:text-sm text-[#152B45] focus:border-[#0D63D6] focus:ring-2 focus:ring-blue-100 focus:outline-none transition bg-white"
                  >
                    <option value="Initial Consultation">Initial Consultation</option>
                    <option value="Follow-up Visit">Follow-up Visit</option>
                    <option value="Emergency Dental Care">Emergency Dental Care</option>
                    <option value="Routine Dental Cleaning">Routine Dental Cleaning</option>
                    <option value="Orthodontic Adjustment">Orthodontic Adjustment</option>
                    <option value="Surgical & Extraction">Surgical & Extraction</option>
                    <option value="Restorative & Filling">Restorative & Filling</option>
                    <option value="Endodontic / Root Canal">Endodontic / Root Canal</option>
                  </select>
                </div>

                <!-- Doctor -->
                <div>
                  <label class="block text-xs font-bold text-slate-700 mb-1.5">
                    Doctor
                  </label>
                  <select
                    v-model="form.doctor_id"
                    @change="onDoctorChange"
                    class="w-full rounded-lg border border-slate-300 px-3.5 py-2.5 text-xs sm:text-sm text-[#152B45] focus:border-[#0D63D6] focus:ring-2 focus:ring-blue-100 focus:outline-none transition bg-white"
                  >
                    <option value="">Select Doctor (Optional)...</option>
                    <option
                      v-for="doc in doctors"
                      :key="doc.id"
                      :value="doc.id"
                    >
                      {{ doc.name }}
                    </option>
                  </select>
                </div>

              </div>

              <!-- Chief Complaint -->
              <div>
                <label class="block text-xs font-bold text-slate-700 mb-1.5">
                  Chief Complaint / Reason for Visit
                </label>
                <input
                  v-model="form.chief_complaint"
                  type="text"
                  placeholder="e.g. Tooth sensitivity, regular cleaning checkup, follow-up on restoration..."
                  class="w-full rounded-lg border border-slate-300 px-3.5 py-2.5 text-xs sm:text-sm text-[#152B45] placeholder-slate-400 focus:border-[#0D63D6] focus:ring-2 focus:ring-blue-100 focus:outline-none transition bg-white"
                />
              </div>

              <!-- Additional Clinical Notes -->
              <div>
                <label class="block text-xs font-bold text-slate-700 mb-1.5">
                  Additional Clinical / Billing Notes
                </label>
                <textarea
                  v-model="form.clinical_notes"
                  rows="3"
                  placeholder="Optional notes for this visit or invoice..."
                  class="w-full rounded-lg border border-slate-300 px-3.5 py-2.5 text-xs sm:text-sm text-[#152B45] placeholder-slate-400 focus:border-[#0D63D6] focus:ring-2 focus:ring-blue-100 focus:outline-none transition bg-white"
                ></textarea>
              </div>
            </div>

          </div>

        </div>

        <!-- Right Column: Step 3 Registration Summary & Integrity Notice -->
        <div class="lg:col-span-4 space-y-4">

          <!-- STEP 3: Registration Summary Card -->
          <div class="bg-white rounded-xl border border-[#DCE3EC] p-5 sm:p-6 shadow-xs">
            <div class="flex items-center gap-2.5 pb-4 border-b border-slate-100">
              <span class="w-6 h-6 rounded-full bg-[#0D63D6] text-white flex items-center justify-center text-xs font-bold flex-shrink-0 shadow-xs">
                3
              </span>
              <h2 class="text-base font-bold text-[#152B45]">
                Registration Summary
              </h2>
            </div>

            <!-- Summary Details List -->
            <div class="divide-y divide-slate-100 text-xs py-2">
              
              <!-- Patient -->
              <div class="py-2.5">
                <span class="block text-[11px] text-slate-400 uppercase font-semibold">Patient</span>
                <span class="block text-xs sm:text-sm font-bold text-[#152B45] mt-0.5 truncate">
                  {{ selectedPatient ? `${selectedPatient.full_name} (${selectedPatient.patient_number})` : 'None selected' }}
                </span>
              </div>

              <!-- Workflow Option -->
              <div class="py-2.5">
                <span class="block text-[11px] text-slate-400 uppercase font-semibold">Workflow Option</span>
                <span class="block text-xs sm:text-sm font-bold text-[#0D63D6] mt-0.5">
                  {{ form.workflow_option === 'new_invoice' ? 'Option A: New Invoice' : 'Option B: Follow-up Visit' }}
                </span>
              </div>

              <!-- Doctor -->
              <div class="py-2.5">
                <span class="block text-[11px] text-slate-400 uppercase font-semibold">Doctor</span>
                <span class="block text-xs sm:text-sm font-bold text-[#152B45] mt-0.5">
                  {{ selectedDoctorName || 'None specified' }}
                </span>
              </div>

              <!-- Invoice Reference -->
              <div class="py-2.5">
                <span class="block text-[11px] text-slate-400 uppercase font-semibold">Invoice Reference</span>
                <span class="block text-xs sm:text-sm font-medium text-slate-600 mt-0.5">
                  {{ form.workflow_option === 'new_invoice' ? 'Auto-generated upon save' : (suggestedInvoiceReference || 'Linked Invoice') }}
                </span>
              </div>

              <!-- Outstanding Balance -->
              <div class="py-2.5">
                <span class="block text-[11px] text-slate-400 uppercase font-semibold">Outstanding Balance</span>
                <span class="block text-xs sm:text-sm font-extrabold text-[#152B45] mt-0.5">
                  $0.00
                </span>
              </div>

              <!-- Reception Type -->
              <div class="py-2.5">
                <span class="block text-[11px] text-slate-400 uppercase font-semibold">Reception Type</span>
                <span class="block text-xs sm:text-sm font-medium text-slate-700 mt-0.5">
                  {{ form.workflow_option === 'new_invoice' ? 'Initial Visit (Checked In)' : 'Follow-up Consultation' }}
                </span>
              </div>

            </div>

            <!-- Action Buttons -->
            <div class="pt-4 space-y-2.5">
              <button
                type="button"
                @click="submitVisit"
                :disabled="!selectedPatient || isSubmitting"
                class="w-full py-2.5 px-4 rounded-lg bg-[#0D63D6] hover:bg-[#094bb5] disabled:opacity-50 disabled:cursor-not-allowed text-white font-bold text-xs sm:text-sm transition-all shadow-xs flex items-center justify-center gap-1.5"
              >
                <svg v-if="isSubmitting" class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24">
                  <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                  <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
                <span>Confirm & Register Visit</span>
              </button>

              <button
                type="button"
                @click="resetForm"
                class="w-full py-2 px-4 rounded-lg border border-[#DCE3EC] bg-white hover:bg-slate-50 text-slate-700 font-semibold text-xs transition"
              >
                Cancel / Reset
              </button>
            </div>

          </div>

          <!-- Integrity Notice Card (Matching Reference Screenshot) -->
          <div class="bg-[#FFFBEB]/70 rounded-xl border border-[#FEF3C7] p-4 text-[11px] leading-relaxed text-slate-600 shadow-xs flex items-start gap-2.5">
            <span class="text-base select-none">💡</span>
            <div>
              <span class="font-bold text-[#152B45]">Integrity Notice:</span>
              All visit registrations and billing modifications are executed within secure database transactions. Reusing an existing invoice links a new reception without duplicating invoices.
            </div>
          </div>

        </div>

      </div>

    </div>

    <!-- Quick Create Patient Modal -->
    <QuickCreatePatientModal
      :show="isModalOpen"
      @close="isModalOpen = false"
      @created="onQuickCreated"
    />
  </TenantLayout>
</template>

<script setup>
import { ref, reactive, computed } from 'vue'
import { Inertia } from '@inertiajs/inertia'
import TenantLayout from '@/Layouts/TenantLayout.vue'
import QuickCreatePatientModal from '@/Components/Tenant/QuickCreatePatientModal.vue'
import axios from 'axios'

const props = defineProps({
  selectedPatient: {
    type: Object,
    default: null
  },
  doctors: {
    type: Array,
    default: () => []
  },
  suggestedInvoiceReference: {
    type: String,
    default: 'INV-0001'
  }
})

const selectedPatient = ref(props.selectedPatient)
const searchQuery = ref('')
const searchResults = ref([])
const isSearching = ref(false)
const hasSearched = ref(false)
const isModalOpen = ref(false)
const isSubmitting = ref(false)

const form = reactive({
  patient_id: props.selectedPatient?.id || null,
  workflow_option: 'new_invoice',
  visit_type: 'Initial Consultation',
  doctor_id: '',
  doctor_name: '',
  chief_complaint: '',
  clinical_notes: '',
  reception_type: 'Initial Visit (Checked In)',
  outstanding_balance: 0.00
})

const selectedDoctorName = computed(() => {
  if (!form.doctor_id) return ''
  const doc = props.doctors.find(d => String(d.id) === String(form.doctor_id))
  return doc ? doc.name : ''
})

function onDoctorChange() {
  form.doctor_name = selectedDoctorName.value
}

let searchTimer = null
function onSearchInput() {
  clearTimeout(searchTimer)
  const q = searchQuery.value.trim()

  if (q.length < 2) {
    searchResults.value = []
    hasSearched.value = false
    return
  }

  isSearching.value = true
  searchTimer = setTimeout(async () => {
    try {
      const response = await axios.get('/api/patients/search', { params: { q } })
      searchResults.value = response.data
      hasSearched.value = true
    } catch (e) {
      searchResults.value = []
    } finally {
      isSearching.value = false
    }
  }, 250)
}

function selectPatient(patient) {
  selectedPatient.value = patient
  form.patient_id = patient.id
  searchQuery.value = ''
  searchResults.value = []
  hasSearched.value = false
}

function clearSelectedPatient() {
  selectedPatient.value = null
  form.patient_id = null
  searchQuery.value = ''
  searchResults.value = []
  hasSearched.value = false
}

function onQuickCreated(newPatient) {
  selectPatient(newPatient)
}

function patientInitials(patient) {
  if (!patient?.full_name) return 'P'
  const parts = patient.full_name.trim().split(' ')
  if (parts.length >= 2) {
    return (parts[0][0] + parts[1][0]).toUpperCase()
  }
  return patient.full_name.slice(0, 2).toUpperCase()
}

function resetForm() {
  clearSelectedPatient()
  form.workflow_option = 'new_invoice'
  form.visit_type = 'Initial Consultation'
  form.doctor_id = ''
  form.doctor_name = ''
  form.chief_complaint = ''
  form.clinical_notes = ''
}

function submitVisit() {
  if (!selectedPatient.value) {
    alert('Please select or create a patient first.')
    return
  }

  isSubmitting.value = true
  form.reception_type = form.workflow_option === 'new_invoice'
    ? 'Initial Visit (Checked In)'
    : 'Follow-up Consultation'

  Inertia.post(route('tenant.visit.store'), form, {
    onFinish: () => {
      isSubmitting.value = false
    },
    onSuccess: () => {
      resetForm()
    }
  })
}
</script>

