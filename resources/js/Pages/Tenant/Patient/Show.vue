<template>
  <TenantLayout>
    <Head :title="`Patient - ${patient.full_name}`" />

    <div class="max-w-6xl mx-auto space-y-4">
      <!-- Breadcrumb & Top Actions -->
      <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 pb-2">
        <div class="flex items-center gap-2 text-xs text-slate-500">
          <Link :href="route('patients.index')" class="hover:text-blue-600 transition-colors">Patients</Link>
          <span>/</span>
          <span class="text-slate-800 font-bold">{{ patient.full_name }}</span>
        </div>

        <div class="flex items-center gap-2 flex-wrap">
          <!-- e-Health Green Button -->
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

          <Link
            :href="route('dental-charts.create', { patient_id: patient.id })"
            class="inline-flex items-center gap-1.5 px-3.5 py-1.5 rounded text-xs font-bold text-white bg-[#2457c5] hover:bg-[#1d48a3] shadow-sm transition-colors"
          >
            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
            </svg>
            <span>+ Dental Chart</span>
          </Link>

          <Link
            :href="route('tenant.visit.create', { patient_id: patient.id })"
            class="px-3.5 py-1.5 rounded text-xs font-bold text-emerald-700 bg-emerald-50 hover:bg-emerald-100 border border-emerald-200 transition-colors"
          >
            + Register Visit
          </Link>

          <Link
            :href="route('patients.edit', patient.id)"
            class="px-3.5 py-1.5 rounded text-xs font-bold text-slate-700 border border-slate-300 hover:bg-slate-50 transition-colors"
          >
            {{ tm('edit') || 'Edit' }}
          </Link>

          <button
            type="button"
            @click="confirmDelete"
            class="px-3.5 py-1.5 rounded text-xs font-bold text-red-600 border border-red-200 hover:bg-red-50 transition-colors"
          >
            {{ tm('delete') || 'Delete' }}
          </button>

          <Link
            :href="route('patients.index')"
            class="px-3.5 py-1.5 rounded text-xs font-bold text-slate-600 hover:bg-slate-100 transition-colors"
          >
            &larr; {{ tm('Patients') || 'Patients' }}
          </Link>
        </div>
      </div>

      <!-- Main Profile Card matching reference screenshot layout -->
      <div class="bg-white border border-slate-200 rounded-lg p-6 sm:p-8 shadow-sm">
        <div class="grid grid-cols-1 md:grid-cols-12 gap-8 items-start">
          
          <!-- Left Column: Portrait Avatar Box -->
          <div class="md:col-span-3 flex flex-col items-start">
            <span class="text-xs font-semibold text-slate-600 mb-2">Avatar</span>

            <div class="w-32 h-52 border border-slate-300 rounded bg-white p-2 flex flex-col items-center justify-center shadow-inner">
              <div class="w-full h-full border border-dashed border-slate-300 rounded flex items-center justify-center overflow-hidden bg-slate-50/50">
                <img
                  v-if="patient.avatar_url"
                  :src="patient.avatar_url"
                  :alt="patient.full_name"
                  class="w-full h-full object-cover"
                />
                <div v-else class="text-center p-2 text-slate-300 flex flex-col items-center">
                  <svg class="w-8 h-8 stroke-slate-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                  </svg>
                </div>
              </div>
            </div>

            <div class="mt-3 w-32 text-center">
              <Link
                :href="route('patients.edit', patient.id)"
                class="inline-block text-[11px] font-semibold text-[#2457c5] hover:underline"
              >
                Change Avatar
              </Link>
            </div>
          </div>

          <!-- Right Column: Details List matching reference rows -->
          <div class="md:col-span-9 space-y-3">
            
            <!-- ID Patient -->
            <div class="flex flex-col sm:flex-row sm:items-center gap-2 sm:gap-4 py-1.5 border-b border-slate-100">
              <span class="w-full sm:w-44 text-left sm:text-right text-xs font-semibold text-slate-500">
                ID Patient
              </span>
              <span class="flex-1 text-sm font-mono font-bold text-slate-800">
                {{ patient.id_number || patient.id }}
              </span>
            </div>

            <!-- First name -->
            <div class="flex flex-col sm:flex-row sm:items-center gap-2 sm:gap-4 py-1.5 border-b border-slate-100">
              <span class="w-full sm:w-44 text-left sm:text-right text-xs font-semibold text-slate-500">
                First name
              </span>
              <span class="flex-1 text-sm font-semibold text-slate-800">
                {{ patient.firstname || '—' }}
              </span>
            </div>

            <!-- Last name -->
            <div class="flex flex-col sm:flex-row sm:items-center gap-2 sm:gap-4 py-1.5 border-b border-slate-100">
              <span class="w-full sm:w-44 text-left sm:text-right text-xs font-semibold text-slate-500">
                Last name
              </span>
              <span class="flex-1 text-sm font-semibold text-slate-800">
                {{ patient.lastname || '—' }}
              </span>
            </div>

            <!-- Gender -->
            <div class="flex flex-col sm:flex-row sm:items-center gap-2 sm:gap-4 py-1.5 border-b border-slate-100">
              <span class="w-full sm:w-44 text-left sm:text-right text-xs font-semibold text-slate-500">
                Gender
              </span>
              <span class="flex-1 text-sm capitalize text-slate-800">
                {{ patient.gender === 'M' || patient.gender === 'male' ? 'Male' : (patient.gender === 'F' || patient.gender === 'female' ? 'Female' : (patient.gender || '—')) }}
              </span>
            </div>

            <!-- Date of birth -->
            <div class="flex flex-col sm:flex-row sm:items-center gap-2 sm:gap-4 py-1.5 border-b border-slate-100">
              <span class="w-full sm:w-44 text-left sm:text-right text-xs font-semibold text-slate-500">
                Date of birth
              </span>
              <span class="flex-1 text-sm font-mono text-slate-800">
                {{ patient.dob || '—' }}
              </span>
            </div>

            <!-- Age -->
            <div class="flex flex-col sm:flex-row sm:items-center gap-2 sm:gap-4 py-1.5 border-b border-slate-100">
              <span class="w-full sm:w-44 text-left sm:text-right text-xs font-semibold text-slate-500">
                Age
              </span>
              <span class="flex-1 text-sm font-semibold text-slate-800">
                {{ patient.age !== null && patient.age !== undefined ? `${patient.age} years old` : '—' }}
              </span>
            </div>

            <!-- Phone -->
            <div class="flex flex-col sm:flex-row sm:items-center gap-2 sm:gap-4 py-1.5 border-b border-slate-100">
              <span class="w-full sm:w-44 text-left sm:text-right text-xs font-semibold text-slate-500">
                Phone
              </span>
              <span class="flex-1 text-sm font-mono text-slate-800">
                {{ patient.phone || '—' }}
              </span>
            </div>

            <!-- Email -->
            <div class="flex flex-col sm:flex-row sm:items-center gap-2 sm:gap-4 py-1.5 border-b border-slate-100">
              <span class="w-full sm:w-44 text-left sm:text-right text-xs font-semibold text-slate-500">
                Email
              </span>
              <span class="flex-1 text-sm text-slate-800">
                {{ patient.email || '—' }}
              </span>
            </div>

            <!-- Profession / Occupation -->
            <div class="flex flex-col sm:flex-row sm:items-center gap-2 sm:gap-4 py-1.5 border-b border-slate-100">
              <span class="w-full sm:w-44 text-left sm:text-right text-xs font-semibold text-slate-500">
                Profession / Occupation
              </span>
              <span class="flex-1 text-sm text-slate-800">
                {{ patient.profession || '—' }}
              </span>
            </div>

            <!-- Address -->
            <div class="flex flex-col sm:flex-row sm:items-start gap-2 sm:gap-4 py-1.5 border-b border-slate-100">
              <span class="w-full sm:w-44 text-left sm:text-right text-xs font-semibold text-slate-500">
                Address
              </span>
              <span class="flex-1 text-sm text-slate-700 whitespace-pre-line">
                {{ patient.address || '—' }}
              </span>
            </div>

            <!-- Financial & Card System Details -->
            <div class="flex flex-col sm:flex-row sm:items-center gap-2 sm:gap-4 py-1.5 border-b border-slate-100">
              <span class="w-full sm:w-44 text-left sm:text-right text-xs font-semibold text-slate-500">
                Balance
              </span>
              <span class="flex-1 text-sm font-mono font-bold text-slate-800">
                ${{ Number(patient.balance || 0).toFixed(2) }}
              </span>
            </div>

            <div class="flex flex-col sm:flex-row sm:items-center gap-2 sm:gap-4 py-1.5 border-b border-slate-100">
              <span class="w-full sm:w-44 text-left sm:text-right text-xs font-semibold text-slate-500">
                Card Status
              </span>
              <div class="flex-1 flex items-center gap-2">
                <span
                  :class="[
                    'px-2 py-0.5 rounded text-xs font-bold',
                    patient.card_activated
                      ? 'bg-emerald-50 text-emerald-700 border border-emerald-200'
                      : 'bg-slate-100 text-slate-500 border border-slate-200'
                  ]"
                >
                  {{ patient.card_activated ? 'Activated' : 'Inactive' }}
                </span>
                <span
                  v-if="patient.is_trail_card"
                  class="px-2 py-0.5 rounded text-xs font-bold bg-purple-50 text-purple-700 border border-purple-200"
                >
                  Trail Card
                </span>
              </div>
            </div>

            <div v-if="patient.card_code" class="flex flex-col sm:flex-row sm:items-center gap-2 sm:gap-4 py-1.5 border-b border-slate-100">
              <span class="w-full sm:w-44 text-left sm:text-right text-xs font-semibold text-slate-500">
                Card Code
              </span>
              <span class="flex-1 font-mono text-xs text-slate-700 bg-slate-50 px-2 py-1 rounded border border-slate-200">
                {{ patient.card_code }}
              </span>
            </div>

          </div>
        </div>
      </div>

      <!-- Dental Charts Section -->
      <div class="bg-white border border-slate-200 rounded-lg p-6 shadow-sm">
        <div class="flex items-center justify-between mb-4">
          <h3 class="text-base font-bold text-slate-900">Dental Examination Charts</h3>
          <Link
            :href="route('dental-charts.create', { patient_id: patient.id })"
            class="text-xs font-bold text-[#2457c5] hover:underline"
          >
            + New Chart Session
          </Link>
        </div>

        <div v-if="!patient.dental_charts || patient.dental_charts.length === 0" class="text-center py-8 text-slate-400 text-xs border border-dashed border-slate-200 rounded-lg">
          No dental examination charts recorded for this patient yet.
        </div>

        <div v-else class="space-y-2">
          <div
            v-for="c in patient.dental_charts"
            :key="c.id"
            class="flex items-center justify-between p-3 border border-slate-200 rounded-lg hover:bg-slate-50 transition-colors"
          >
            <div class="flex items-center gap-3">
              <span class="font-bold text-slate-800 text-xs">Chart #{{ c.id }}</span>
              <span class="text-xs text-slate-400">&middot; {{ c.chart_date }}</span>
              <span class="px-2 py-0.5 rounded text-[10px] font-semibold bg-slate-100 text-slate-600 uppercase">
                {{ c.dentition_type || 'mixed' }}
              </span>
            </div>

            <div class="flex items-center gap-2">
              <Link
                :href="route('dental.show', c.id)"
                class="px-3 py-1 rounded text-xs font-bold text-white bg-[#2457c5] hover:bg-[#1d48a3] transition-colors shadow-sm"
              >
                Open Chart
              </Link>
            </div>
          </div>
        </div>
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
            <h3 class="text-sm font-bold text-slate-900">e-Health Patient Card</h3>
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
          <div class="p-3 bg-emerald-50 rounded-lg border border-emerald-200 text-emerald-800 space-y-1">
            <div class="font-bold">Sync Status: Active</div>
            <div>Patient ID: <span class="font-mono font-bold">{{ patient.id_number || patient.id }}</span></div>
            <div>Full Name: <span class="font-bold">{{ patient.full_name }}</span></div>
            <div>Gender: <span class="capitalize">{{ patient.gender === 'M' || patient.gender === 'male' ? 'Male' : (patient.gender === 'F' || patient.gender === 'female' ? 'Female' : (patient.gender || '—')) }}</span></div>
            <div>Date of Birth: <span class="font-mono">{{ patient.dob || '—' }}</span></div>
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
import { Head, Link } from '@inertiajs/inertia-vue3'
import { Inertia } from '@inertiajs/inertia'
import Swal from 'sweetalert2'
import { useToast } from 'vue-toastification'
import { useI18n } from 'vue-i18n'
import { lang } from '@/Lang'
import TenantLayout from '@/Layouts/TenantLayout.vue'

const props = defineProps({
  patient: Object,
})

const { tm } = useI18n({
  inheritLocale: true,
  messages: lang,
})

const showEHealthModal = ref(false)

const confirmDelete = () => {
  Swal.fire({
    title: 'Are you sure?',
    text: "You won't be able to revert this!",
    icon: 'warning',
    showCancelButton: true,
    confirmButtonText: 'Yes, delete it!',
    customClass: {
      confirmButton: 'inline-flex items-center mx-2 px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 active:bg-gray-900 focus:outline-none focus:border-gray-900 focus:ring focus:ring-gray-300 disabled:opacity-25 transition bg-primary hover:bg-primary-dark focus:outline-none focus:ring-2 focus:ring-primary focus:ring-offset-1 dark:focus:ring-offset-darker',
      cancelButton: 'inline-flex items-center justify-center px-4 py-2 bg-red-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-red-500 focus:outline-none focus:border-red-700 focus:ring focus:ring-red-200 active:bg-red-600 disabled:opacity-25 transition',
    },
    buttonsStyling: false,
  }).then(result => {
    if (result.value) {
      Inertia.delete(route('patients.destroy', props.patient.id), {
        onSuccess: () => {
          useToast().success(tm('PatientDeletedSuccessfully') || 'Patient deleted successfully')
        },
      })
    }
  })
}
</script>
