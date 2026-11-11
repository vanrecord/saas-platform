<template>
  <TenantLayout :title="tm('Patient_Visit_Registration')">
    <div class="max-w-7xl mx-auto space-y-6">

      <!-- Page Header -->
      <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
          <h1 class="text-2xl sm:text-3xl font-extrabold text-[#152B45] tracking-tight">
            {{ tm('Patient_Visit_Registration') }}
          </h1>
          <p class="text-xs sm:text-sm text-slate-500 mt-1">
            {{ tm('Register_a_patient_for_a_clinical_visit') }}
          </p>
        </div>

        <Link
          :href="route('patients.create', { return_to: 'register' })"
          class="inline-flex items-center justify-center gap-2 px-5 py-2.5 rounded-lg text-xs font-bold text-white bg-[#0D63D6] hover:bg-[#094bb5] transition-all shadow-sm flex-shrink-0"
        >
          <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4" />
          </svg>
          <span>+ {{ tm('Create_Patient') }}</span>
        </Link>
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
                  {{ tm('Find_or_Select_Patient') }}
                </h2>
              </div>
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
                        {{ selectedPatient.patient_number || ('P' + String(selectedPatient.id).padStart(6, '0')) }}
                      </span>
                    </div>
                    <div class="text-xs text-slate-500 mt-0.5 flex items-center gap-3 flex-wrap">
                      <span v-if="selectedPatient.phone">📞 {{ selectedPatient.phone }}</span>
                      <span v-if="selectedPatient.gender || selectedPatient.sex" class="capitalize">👤 {{ selectedPatient.gender || selectedPatient.sex }}</span>
                      <span v-if="selectedPatient.age">🎂 {{ selectedPatient.age }} yrs</span>
                    </div>
                  </div>
                </div>

                <button
                  type="button"
                  @click="clearSelectedPatient"
                  class="px-3 py-1.5 rounded-lg border border-slate-200 text-slate-600 hover:bg-white text-xs font-bold transition flex-shrink-0"
                >
                  {{ tm('Change') }}
                </button>
              </div>

              <!-- Search Input When No Patient Selected -->
              <div v-else class="relative">
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
                    :placeholder="tm('Search_patient_placeholder')"
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
                          ID: <span class="font-semibold text-slate-700">{{ patient.patient_number || ('P' + String(patient.id).padStart(6, '0')) }}</span>
                          <span v-if="patient.phone" class="ml-2">• 📞 {{ patient.phone }}</span>
                        </div>
                      </div>
                    </div>
                    <span class="text-xs font-bold text-[#0D63D6] group-hover:translate-x-0.5 transition-transform">
                      {{ tm('Select') }} →
                    </span>
                  </button>
                </div>

                <!-- No Results Notice -->
                <div
                  v-else-if="searchQuery.trim().length >= 2 && !isSearching && hasSearched"
                  class="absolute left-0 right-0 mt-1.5 bg-white rounded-xl shadow-lg border border-slate-200 p-4 text-center z-20 text-xs text-slate-500"
                >
                  {{ tm('No_existing_patient_found') }} "<span class="font-bold text-slate-700">{{ searchQuery }}</span>".
                </div>
              </div>
            </div>
          </div>

          <!-- STEP 2: Registration & Billing Workflow -->
          <div class="bg-white rounded-xl border border-[#DCE3EC] p-5 sm:p-6 shadow-xs relative">
            <div class="flex items-center gap-2.5 pb-4 border-b border-slate-100">
              <span class="w-6 h-6 rounded-full bg-[#0D63D6] text-white flex items-center justify-center text-xs font-bold flex-shrink-0 shadow-xs">
                2
              </span>
              <h2 class="text-base font-bold text-[#152B45]">
                {{ tm('Registration_Billing_Workflow') }}
              </h2>
            </div>

            <!-- When NO Patient Selected: Informative Disabled Prompt -->
            <div v-if="!selectedPatient" class="mt-5 p-6 rounded-xl border border-dashed border-slate-200 bg-slate-50/70 text-center">
              <div class="w-10 h-10 mx-auto rounded-full bg-slate-200/70 flex items-center justify-center text-slate-400 mb-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                </svg>
              </div>
              <p class="text-xs text-slate-500 max-w-md mx-auto">
                {{ tm('Please_select_a_patient_first_to_proceed') }}
              </p>
            </div>

            <!-- When Patient is Selected: Active Workflow Options & Form -->
            <div v-else class="space-y-6 mt-5">

              <!-- Selectable Workflow Cards (Option A vs Option B) -->
              <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                
                <!-- Option A: Create New Invoice -->
                <div
                  @click="selectWorkflowOption('new_invoice')"
                  :class="[
                    'relative rounded-xl p-4 cursor-pointer transition-all border-2 flex items-start gap-3.5 select-none',
                    form.workflow_option === 'new_invoice'
                      ? 'border-[#0D63D6] bg-[#F0FDF9] shadow-xs'
                      : 'border-[#DCE3EC] bg-white hover:border-slate-300'
                  ]"
                >
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
                      {{ tm('Option_A_New_Invoice') }}
                    </h3>
                    <p class="text-xs text-slate-500 mt-1 leading-relaxed">
                      {{ tm('Option_A_Description') }}
                    </p>
                  </div>
                </div>

                <!-- Option B: Select Existing Invoice (Follow-up Visit) -->
                <div
                  @click="selectWorkflowOption('follow_up')"
                  :class="[
                    'relative rounded-xl p-4 cursor-pointer transition-all border-2 flex items-start gap-3.5 select-none',
                    form.workflow_option === 'follow_up'
                      ? 'border-[#0D63D6] bg-[#F0FDF9] shadow-xs'
                      : 'border-[#DCE3EC] bg-white hover:border-slate-300'
                  ]"
                >
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
                      {{ tm('Option_B_Existing_Invoice') }}
                    </h3>
                    <p class="text-xs text-slate-500 mt-1 leading-relaxed">
                      {{ tm('Option_B_Description') }}
                    </p>
                  </div>
                </div>

              </div>

              <!-- Option B Dependent: Existing Invoice Dropdown / Empty State -->
              <div v-if="form.workflow_option === 'follow_up'" class="p-4 rounded-xl border border-blue-200 bg-blue-50/40 space-y-3">
                <div class="flex items-center justify-between">
                  <label class="block text-xs font-bold text-[#152B45]">
                    {{ tm('Select_Existing_Invoice') }} <span class="text-red-500">*</span>
                  </label>
                  <span v-if="isLoadingInvoices" class="text-xs text-blue-600 flex items-center gap-1.5 font-medium">
                    <svg class="w-3.5 h-3.5 animate-spin" fill="none" viewBox="0 0 24 24">
                      <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                      <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                    {{ tm('Loading_invoices') }}
                  </span>
                </div>

                <!-- Empty State: Patient has no existing invoices -->
                <div
                  v-if="!isLoadingInvoices && patientInvoices.length === 0"
                  class="p-4 rounded-lg bg-amber-50 border border-amber-200 text-amber-800 text-xs flex items-start gap-2.5"
                >
                  <span class="text-base select-none">⚠️</span>
                  <div>
                    <p class="font-bold">
                      {{ tm('No_existing_invoices_found') }}
                    </p>
                    <button
                      type="button"
                      @click="selectWorkflowOption('new_invoice')"
                      class="mt-2 text-xs font-bold text-[#0D63D6] underline hover:text-[#094bb5]"
                    >
                      Switch to Option A: Create New Invoice →
                    </button>
                  </div>
                </div>

                <!-- Dropdown when invoices exist -->
                <div v-else>
                  <select
                    v-model="form.invoice_id"
                    :disabled="isLoadingInvoices"
                    class="w-full rounded-lg border border-slate-300 px-3.5 py-2.5 text-xs sm:text-sm text-[#152B45] focus:border-[#0D63D6] focus:ring-2 focus:ring-blue-100 focus:outline-none transition bg-white"
                  >
                    <option value="">{{ tm('Choose_an_invoice') }}</option>
                    <option
                      v-for="inv in patientInvoices"
                      :key="inv.id"
                      :value="inv.id"
                    >
                      {{ inv.display_label }}
                    </option>
                  </select>
                  <p class="text-[11px] text-slate-500 mt-1.5">
                    Sorted by newest invoice date descending (DESC). Displays: Invoice Number - Date - Status - Balance.
                  </p>
                </div>
              </div>

              <!-- Form Inputs: Visit Type, Doctor, Complaints, Notes -->
              <div class="space-y-4 pt-2">
                <!-- Row: Visit Type & Doctor -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                  
                  <!-- Visit Type -->
                  <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1.5">
                      {{ tm('Visit_Type') }} <span class="text-red-500">*</span>
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
                      {{ tm('Doctor') }}
                    </label>
                    <select
                      v-model="form.doctor_id"
                      @change="onDoctorChange"
                      class="w-full rounded-lg border border-slate-300 px-3.5 py-2.5 text-xs sm:text-sm text-[#152B45] focus:border-[#0D63D6] focus:ring-2 focus:ring-blue-100 focus:outline-none transition bg-white"
                    >
                      <option value="">{{ tm('Select_Doctor_Optional') }}</option>
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
                    {{ tm('Chief_Complaint_Reason_for_Visit') }}
                  </label>
                  <input
                    v-model="form.chief_complaint"
                    type="text"
                    :placeholder="tm('Chief_Complaint_Placeholder')"
                    class="w-full rounded-lg border border-slate-300 px-3.5 py-2.5 text-xs sm:text-sm text-[#152B45] placeholder-slate-400 focus:border-[#0D63D6] focus:ring-2 focus:ring-blue-100 focus:outline-none transition bg-white"
                  />
                </div>

                <!-- Additional Clinical Notes -->
                <div>
                  <label class="block text-xs font-bold text-slate-700 mb-1.5">
                    {{ tm('Additional_Clinical_Billing_Notes') }}
                  </label>
                  <textarea
                    v-model="form.clinical_notes"
                    rows="3"
                    :placeholder="tm('Additional_Notes_Placeholder')"
                    class="w-full rounded-lg border border-slate-300 px-3.5 py-2.5 text-xs sm:text-sm text-[#152B45] placeholder-slate-400 focus:border-[#0D63D6] focus:ring-2 focus:ring-blue-100 focus:outline-none transition bg-white"
                  ></textarea>
                </div>
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
                {{ tm('Registration_Summary') }}
              </h2>
            </div>

            <!-- Summary Details List -->
            <div class="divide-y divide-slate-100 text-xs py-2">
              
              <!-- Patient -->
              <div class="py-2.5">
                <span class="block text-[11px] text-slate-400 uppercase font-semibold">Patient</span>
                <span class="block text-xs sm:text-sm font-bold text-[#152B45] mt-0.5 truncate">
                  {{ selectedPatient ? `${selectedPatient.full_name} (${selectedPatient.patient_number || ('P' + String(selectedPatient.id).padStart(6, '0'))})` : 'None selected' }}
                </span>
              </div>

              <!-- Workflow Option -->
              <div class="py-2.5">
                <span class="block text-[11px] text-slate-400 uppercase font-semibold">Workflow Option</span>
                <span class="block text-xs sm:text-sm font-bold text-[#0D63D6] mt-0.5">
                  {{ form.workflow_option === 'new_invoice' ? tm('Option_A_New_Invoice') : tm('Option_B_Existing_Invoice') }}
                </span>
              </div>

              <!-- Doctor -->
              <div class="py-2.5">
                <span class="block text-[11px] text-slate-400 uppercase font-semibold">{{ tm('Doctor') }}</span>
                <span class="block text-xs sm:text-sm font-bold text-[#152B45] mt-0.5">
                  {{ selectedDoctorName || 'None specified' }}
                </span>
              </div>

              <!-- Invoice Reference -->
              <div class="py-2.5">
                <span class="block text-[11px] text-slate-400 uppercase font-semibold">{{ tm('Invoice_Reference') }}</span>
                <span class="block text-xs sm:text-sm font-medium text-slate-600 mt-0.5">
                  <span v-if="form.workflow_option === 'new_invoice'" class="italic text-slate-500">
                    {{ tm('Auto_generated_upon_save') }}
                  </span>
                  <span v-else-if="selectedInvoiceObject" class="font-bold text-[#152B45]">
                    {{ selectedInvoiceObject.invoice_number }}
                  </span>
                  <span v-else class="text-amber-600 italic">
                    {{ tm('Choose_an_invoice') }}
                  </span>
                </span>
              </div>

              <!-- Outstanding Balance -->
              <div class="py-2.5">
                <span class="block text-[11px] text-slate-400 uppercase font-semibold">{{ tm('Outstanding_Balance') }}</span>
                <span class="block text-xs sm:text-sm font-extrabold text-[#152B45] mt-0.5">
                  {{ form.workflow_option === 'follow_up' && selectedInvoiceObject ? selectedInvoiceObject.balance_formatted : '$0.00' }}
                </span>
              </div>

              <!-- Reception Type -->
              <div class="py-2.5">
                <span class="block text-[11px] text-slate-400 uppercase font-semibold">{{ tm('Reception_Type') }}</span>
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
                :disabled="isSubmitDisabled"
                class="w-full py-2.5 px-4 rounded-lg bg-[#0D63D6] hover:bg-[#094bb5] disabled:opacity-50 disabled:cursor-not-allowed text-white font-bold text-xs sm:text-sm transition-all shadow-xs flex items-center justify-center gap-1.5"
              >
                <svg v-if="isSubmitting" class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24">
                  <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                  <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
                <span>{{ tm('Confirm_Register_Visit') }}</span>
              </button>

              <button
                type="button"
                @click="resetForm"
                class="w-full py-2 px-4 rounded-lg border border-[#DCE3EC] bg-white hover:bg-slate-50 text-slate-700 font-semibold text-xs transition"
              >
                {{ tm('Cancel_Reset') }}
              </button>
            </div>

          </div>

          <!-- Integrity Notice Card -->
          <div class="bg-[#FFFBEB]/70 rounded-xl border border-[#FEF3C7] p-4 text-[11px] leading-relaxed text-slate-600 shadow-xs flex items-start gap-2.5">
            <span class="text-base select-none">💡</span>
            <div>
              <span class="font-bold text-[#152B45]">{{ tm('Integrity_Notice') }}:</span>
              {{ tm('Integrity_Notice_Description') }}
            </div>
          </div>

        </div>

      </div>

    </div>
  </TenantLayout>
</template>

<script setup>
import { ref, reactive, computed, watch, onMounted } from 'vue'
import { Inertia } from '@inertiajs/inertia'
import { Link } from '@inertiajs/inertia-vue3'
import TenantLayout from '@/Layouts/TenantLayout.vue'
import { useI18n } from 'vue-i18n'
import { lang } from '@/Lang'
import axios from 'axios'

const { tm } = useI18n({
  inheritLocale: true,
  messages: lang,
})

const props = defineProps({
  selectedPatient: {
    type: Object,
    default: null
  },
  existingInvoices: {
    type: Array,
    default: () => []
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
const isSubmitting = ref(false)

const patientInvoices = ref(props.existingInvoices || [])
const isLoadingInvoices = ref(false)

const form = reactive({
  patient_id: props.selectedPatient?.id || null,
  workflow_option: 'new_invoice',
  invoice_id: '',
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

const selectedInvoiceObject = computed(() => {
  if (!form.invoice_id) return null
  return patientInvoices.value.find(i => String(i.id) === String(form.invoice_id)) || null
})

const isSubmitDisabled = computed(() => {
  if (isSubmitting.value) return true
  if (!selectedPatient.value) return true
  if (form.workflow_option === 'follow_up' && !form.invoice_id) return true
  return false
})

function onDoctorChange() {
  form.doctor_name = selectedDoctorName.value
}

function selectWorkflowOption(option) {
  form.workflow_option = option
  if (option === 'new_invoice') {
    form.invoice_id = ''
  } else if (option === 'follow_up') {
    if (selectedPatient.value && patientInvoices.value.length === 0 && !isLoadingInvoices.value) {
      fetchPatientInvoices(selectedPatient.value.id)
    }
  }
}

async function fetchPatientInvoices(patientId) {
  if (!patientId) {
    patientInvoices.value = []
    return
  }

  isLoadingInvoices.value = true
  try {
    const response = await axios.get(`/api/patients/${patientId}/invoices`)
    patientInvoices.value = response.data || []
  } catch (error) {
    console.error('Failed to load patient invoices:', error)
    patientInvoices.value = []
  } finally {
    isLoadingInvoices.value = false
  }
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
  form.invoice_id = ''
  searchQuery.value = ''
  searchResults.value = []
  hasSearched.value = false

  // Fetch invoices for the newly selected patient
  fetchPatientInvoices(patient.id)
}

function clearSelectedPatient() {
  selectedPatient.value = null
  form.patient_id = null
  form.invoice_id = ''
  patientInvoices.value = []
  searchQuery.value = ''
  searchResults.value = []
  hasSearched.value = false
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
  form.invoice_id = ''
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

  if (form.workflow_option === 'follow_up' && !form.invoice_id) {
    alert('Please select an existing invoice for follow-up visit.')
    return
  }

  isSubmitting.value = true
  form.reception_type = form.workflow_option === 'new_invoice'
    ? 'Initial Visit (Checked In)'
    : 'Follow-up Consultation'

  form.outstanding_balance = selectedInvoiceObject.value?.balance ?? 0.00

  Inertia.post(route('tenant.visit.store'), form, {
    onFinish: () => {
      isSubmitting.value = false
    }
  })
}

onMounted(() => {
  if (selectedPatient.value && patientInvoices.value.length === 0) {
    fetchPatientInvoices(selectedPatient.value.id)
  }
})
</script>
