<template>
  <TenantLayout title="Dashboard">
    <div class="max-w-7xl mx-auto space-y-6">

      <!-- Page Header -->
      <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
          <h1 class="text-2xl sm:text-3xl font-extrabold text-[#152B45] tracking-tight">
            Dashboard
          </h1>
          <p class="text-xs sm:text-sm text-slate-500 mt-1">
            Welcome to your Dental Clinic Management System.
          </p>
        </div>

        <div class="flex items-center gap-2.5">
          <!-- Register Patient Button -->
          <Link
            :href="route('tenant.visit.create')"
            class="inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-lg text-xs font-bold text-white bg-[#0D63D6] hover:bg-[#094bb5] transition-all shadow-xs"
          >
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4" />
            </svg>
            <span>+ Register Visit</span>
          </Link>

          <!-- Create Patient Modal Button -->
          <button
            type="button"
            @click="isModalOpen = true"
            class="inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-lg text-xs font-bold text-[#0D63D6] bg-white border border-[#0D63D6]/30 hover:bg-blue-50 transition-all shadow-xs"
          >
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z" />
            </svg>
            <span>+ Create Patient</span>
          </button>
        </div>
      </div>

      <!-- 4 Summary KPI Cards -->
      <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 sm:gap-6">
        
        <!-- Total Patients -->
        <div class="bg-white rounded-xl border border-[#DCE3EC] p-5 shadow-xs hover:shadow-md transition-shadow">
          <div class="flex items-center justify-between">
            <span class="text-xs font-bold text-slate-500 uppercase tracking-wider">Total Patients</span>
            <div class="w-9 h-9 rounded-lg bg-blue-50 text-[#0D63D6] flex items-center justify-center">
              <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
              </svg>
            </div>
          </div>
          <div class="mt-3">
            <span class="text-2xl sm:text-3xl font-extrabold text-[#152B45]">
              {{ stats.total_patients }}
            </span>
            <span class="block text-[11px] text-slate-400 mt-0.5">Active registered clinic records</span>
          </div>
        </div>

        <!-- Today's Visits -->
        <div class="bg-white rounded-xl border border-[#DCE3EC] p-5 shadow-xs hover:shadow-md transition-shadow">
          <div class="flex items-center justify-between">
            <span class="text-xs font-bold text-slate-500 uppercase tracking-wider">Today's Visits</span>
            <div class="w-9 h-9 rounded-lg bg-emerald-50 text-emerald-600 flex items-center justify-center">
              <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
              </svg>
            </div>
          </div>
          <div class="mt-3">
            <span class="text-2xl sm:text-3xl font-extrabold text-[#152B45]">
              {{ stats.today_visits }}
            </span>
            <span class="block text-[11px] text-slate-400 mt-0.5">Checked-in receptions today</span>
          </div>
        </div>

        <!-- Pending Invoices -->
        <div class="bg-white rounded-xl border border-[#DCE3EC] p-5 shadow-xs hover:shadow-md transition-shadow">
          <div class="flex items-center justify-between">
            <span class="text-xs font-bold text-slate-500 uppercase tracking-wider">Pending Invoices</span>
            <div class="w-9 h-9 rounded-lg bg-amber-50 text-amber-600 flex items-center justify-center">
              <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
              </svg>
            </div>
          </div>
          <div class="mt-3">
            <span class="text-2xl sm:text-3xl font-extrabold text-[#152B45]">
              {{ stats.pending_invoices }}
            </span>
            <span class="block text-[11px] text-slate-400 mt-0.5">Awaiting payment or settlement</span>
          </div>
        </div>

        <!-- Total Revenue -->
        <div class="bg-white rounded-xl border border-[#DCE3EC] p-5 shadow-xs hover:shadow-md transition-shadow">
          <div class="flex items-center justify-between">
            <span class="text-xs font-bold text-slate-500 uppercase tracking-wider">Total Revenue</span>
            <div class="w-9 h-9 rounded-lg bg-indigo-50 text-indigo-600 flex items-center justify-center">
              <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
              </svg>
            </div>
          </div>
          <div class="mt-3">
            <span class="text-2xl sm:text-3xl font-extrabold text-[#152B45]">
              ${{ stats.total_revenue }}
            </span>
            <span class="block text-[11px] text-slate-400 mt-0.5">Recorded patient visits balance</span>
          </div>
        </div>

      </div>

      <!-- Quick Action Shortcuts -->
      <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
        
        <Link
          :href="route('dental.index')"
          class="flex items-center gap-3.5 p-4 rounded-xl bg-white border border-[#DCE3EC] hover:border-[#0D63D6] hover:shadow-md transition-all group"
        >
          <div class="w-10 h-10 rounded-xl bg-blue-50 text-[#0D63D6] flex items-center justify-center group-hover:scale-105 transition-transform">
            <svg class="w-6 h-6 fill-current" viewBox="0 0 24 24">
              <path d="M12 2C8.5 2 6 4.5 6 7c0 1.9.8 3.5 1.5 5.2.7 1.8 1.5 3.8 1.5 5.8 0 1.7 1.3 3 3 3s3-1.3 3-3c0-2 .8-4 1.5-5.8C17.2 10.5 18 8.9 18 7c0-2.5-2.5-5-6-5zm-1 16c-.6 0-1-.4-1-1 0-1.8-.8-3.6-1.5-5.3C7.8 10 7 8.5 7 7c0-1.9 2-3.8 5-3.8s5 1.9 5 3.8c0 1.5-.8 3-1.5 4.7-.7 1.7-1.5 3.5-1.5 5.3 0 .6-.4 1-1 1h-2z"/>
            </svg>
          </div>
          <div>
            <span class="block text-xs font-extrabold text-[#152B45] group-hover:text-[#0D63D6] transition-colors">
              Interactive Odontogram
            </span>
            <span class="text-[11px] text-slate-500">Visual dental condition charting</span>
          </div>
        </Link>

        <Link
          :href="route('tenant.visit.create')"
          class="flex items-center gap-3.5 p-4 rounded-xl bg-white border border-[#DCE3EC] hover:border-[#0D63D6] hover:shadow-md transition-all group"
        >
          <div class="w-10 h-10 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center group-hover:scale-105 transition-transform">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z" />
            </svg>
          </div>
          <div>
            <span class="block text-xs font-extrabold text-[#152B45] group-hover:text-[#0D63D6] transition-colors">
              Patient Visit Registration
            </span>
            <span class="text-[11px] text-slate-500">Check-in, billing & invoices</span>
          </div>
        </Link>

        <Link
          :href="route('patients.index')"
          class="flex items-center gap-3.5 p-4 rounded-xl bg-white border border-[#DCE3EC] hover:border-[#0D63D6] hover:shadow-md transition-all group"
        >
          <div class="w-10 h-10 rounded-xl bg-purple-50 text-purple-600 flex items-center justify-center group-hover:scale-105 transition-transform">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
            </svg>
          </div>
          <div>
            <span class="block text-xs font-extrabold text-[#152B45] group-hover:text-[#0D63D6] transition-colors">
              Patients Directory
            </span>
            <span class="text-[11px] text-slate-500">Medical histories & profiles</span>
          </div>
        </Link>

      </div>

      <!-- Tables Grid: Recent Visits & Recent Patients -->
      <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

        <!-- Today's Visits / Appointments -->
        <div class="bg-white rounded-xl border border-[#DCE3EC] p-5 shadow-xs">
          <div class="flex items-center justify-between pb-3 border-b border-slate-100">
            <div>
              <h2 class="text-sm font-bold text-[#152B45]">Today's Consultations & Visits</h2>
              <p class="text-[11px] text-slate-500">Latest recorded reception activities</p>
            </div>
            <Link :href="route('tenant.visit.create')" class="text-xs font-bold text-[#0D63D6] hover:underline">
              + New Visit
            </Link>
          </div>

          <!-- List or Empty State -->
          <div v-if="today_appointments && today_appointments.length > 0" class="divide-y divide-slate-100 mt-2">
            <div
              v-for="visit in today_appointments"
              :key="visit.id"
              class="py-3 flex items-center justify-between gap-3 text-xs"
            >
              <div class="min-w-0">
                <div class="font-bold text-[#152B45] truncate">
                  {{ visit.patient?.full_name || 'Walk-in Patient' }}
                </div>
                <div class="text-[11px] text-slate-500 mt-0.5">
                  <span class="text-[#0D63D6] font-semibold">{{ visit.visit_type }}</span>
                  <span v-if="visit.doctor_name" class="ml-1.5">• {{ visit.doctor_name }}</span>
                </div>
              </div>

              <div class="text-right flex-shrink-0">
                <span class="inline-block px-2 py-0.5 rounded-md text-[10px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-200">
                  {{ visit.status || 'checked_in' }}
                </span>
                <span class="block text-[10px] text-slate-400 mt-0.5">
                  {{ visit.visit_number }}
                </span>
              </div>
            </div>
          </div>

          <!-- Empty State -->
          <div v-else class="text-center py-10 text-xs text-slate-400">
            <svg class="w-10 h-10 mx-auto text-slate-300 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
            </svg>
            No patient visits recorded today yet.
            <div class="mt-2">
              <Link :href="route('tenant.visit.create')" class="text-[#0D63D6] font-bold hover:underline">
                Register a patient visit →
              </Link>
            </div>
          </div>
        </div>

        <!-- Recent Patient Registrations -->
        <div class="bg-white rounded-xl border border-[#DCE3EC] p-5 shadow-xs">
          <div class="flex items-center justify-between pb-3 border-b border-slate-100">
            <div>
              <h2 class="text-sm font-bold text-[#152B45]">Recent Patient Registrations</h2>
              <p class="text-[11px] text-slate-500">Newly created clinic patient records</p>
            </div>
            <Link :href="route('patients.index')" class="text-xs font-bold text-[#0D63D6] hover:underline">
              View All
            </Link>
          </div>

          <!-- List or Empty State -->
          <div v-if="recent_patients && recent_patients.length > 0" class="divide-y divide-slate-100 mt-2">
            <div
              v-for="patient in recent_patients"
              :key="patient.id"
              class="py-3 flex items-center justify-between gap-3 text-xs"
            >
              <div class="flex items-center gap-3 min-w-0">
                <div class="w-8 h-8 rounded-full bg-blue-50 text-[#0D63D6] flex items-center justify-center font-bold text-xs flex-shrink-0">
                  {{ patient.full_name?.slice(0, 2).toUpperCase() }}
                </div>
                <div class="min-w-0">
                  <div class="font-bold text-[#152B45] truncate">
                    {{ patient.full_name }}
                  </div>
                  <div class="text-[11px] text-slate-500">
                    ID: <span class="font-semibold">{{ patient.patient_number }}</span>
                    <span v-if="patient.phone" class="ml-1.5">• 📞 {{ patient.phone }}</span>
                  </div>
                </div>
              </div>

              <div class="flex items-center gap-2 flex-shrink-0">
                <Link
                  :href="route('patients.show', patient.id)"
                  class="px-2.5 py-1 rounded-md border border-slate-200 text-slate-600 hover:text-[#0D63D6] hover:bg-slate-50 text-[11px] font-bold transition"
                >
                  Profile
                </Link>
              </div>
            </div>
          </div>

          <!-- Empty State -->
          <div v-else class="text-center py-10 text-xs text-slate-400">
            <svg class="w-10 h-10 mx-auto text-slate-300 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
            </svg>
            No patients registered yet.
            <div class="mt-2">
              <button
                type="button"
                @click="isModalOpen = true"
                class="text-[#0D63D6] font-bold hover:underline"
              >
                + Register First Patient →
              </button>
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
import { ref } from 'vue'
import { Link } from '@inertiajs/inertia-vue3'
import { Inertia } from '@inertiajs/inertia'
import TenantLayout from '@/Layouts/TenantLayout.vue'
import QuickCreatePatientModal from '@/Components/Tenant/QuickCreatePatientModal.vue'

const props = defineProps({
  stats: {
    type: Object,
    default: () => ({
      total_patients: 0,
      today_visits: 0,
      pending_invoices: 0,
      total_revenue: '0.00',
      total_charts: 0
    })
  },
  recent_patients: {
    type: Array,
    default: () => []
  },
  today_appointments: {
    type: Array,
    default: () => []
  },
  tenant: {
    type: Object,
    default: () => ({})
  }
})

const isModalOpen = ref(false)

function onQuickCreated(patient) {
  Inertia.reload({ only: ['stats', 'recent_patients'] })
}
</script>

