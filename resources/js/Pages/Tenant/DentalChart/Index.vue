<template>
  <TenantLayout>
    <Head title="Dental Charts" />

    <div class="max-w-7xl mx-auto">
      <!-- Page Header -->
      <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
        <div>
          <h1 class="text-2xl font-black text-slate-900 tracking-tight">Dental Examination Charts</h1>
          <p class="text-sm text-slate-500 mt-1">List of patient examination records, odontograms, and findings</p>
        </div>

        <div class="flex items-center gap-2">
          <Link
            :href="route('dental.index')"
            class="inline-flex items-center gap-1.5 px-4 py-2 border border-slate-300 rounded-lg text-sm font-bold text-slate-700 bg-white hover:bg-slate-50 shadow-sm transition-colors"
          >
            <svg class="w-4 h-4 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" /></svg>
            <span>Interactive Chart</span>
          </Link>

          <Link
            :href="route('dental-charts.create')"
            class="inline-flex items-center gap-1.5 px-4 py-2 rounded-lg text-sm font-bold text-white bg-[#2457c5] hover:bg-[#1d48a3] shadow-sm transition-colors"
          >
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" /></svg>
            <span>+ New Chart</span>
          </Link>
        </div>
      </div>

      <!-- Filter Card -->
      <div class="bg-white border border-slate-200 rounded-xl p-4 shadow-sm mb-6">
        <div class="flex flex-wrap items-center gap-3">
          <div class="w-full sm:w-72">
            <select
              v-model="selectedPatientId"
              @change="filterByPatient"
              class="w-full text-sm border-slate-300 rounded-lg focus:ring-blue-500 focus:border-blue-500"
            >
              <option value="">All Patients</option>
              <option
                v-for="p in patients"
                :key="p.id"
                :value="p.id"
              >
                {{ p.full_name }} ({{ p.patient_number }})
              </option>
            </select>
          </div>

          <button
            v-if="selectedPatientId"
            type="button"
            @click="clearFilter"
            class="px-3 py-2 text-xs font-bold text-slate-600 bg-slate-100 hover:bg-slate-200 rounded-lg transition-colors"
          >
            Clear Filter
          </button>
        </div>
      </div>

      <!-- Table Card -->
      <div class="bg-white border border-slate-200 rounded-xl shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
          <table class="w-full text-left text-sm text-slate-600">
            <thead class="bg-slate-50 border-b border-slate-200 text-xs uppercase font-extrabold text-slate-500 tracking-wider">
              <tr>
                <th class="px-6 py-3.5">Chart Date</th>
                <th class="px-6 py-3.5">Patient</th>
                <th class="px-6 py-3.5">Dentition Type</th>
                <th class="px-6 py-3.5">Recorded Teeth</th>
                <th class="px-6 py-3.5">Notes</th>
                <th class="px-6 py-3.5 text-right">Actions</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
              <tr
                v-for="chart in charts.data"
                :key="chart.id"
                class="hover:bg-slate-50/70 transition-colors"
              >
                <td class="px-6 py-4 font-bold text-slate-900 whitespace-nowrap">
                  {{ chart.chart_date }}
                  <span class="block text-[11px] font-normal text-slate-400">Chart #{{ chart.id }}</span>
                </td>
                <td class="px-6 py-4">
                  <Link
                    v-if="chart.patient"
                    :href="route('patients.show', chart.patient.id)"
                    class="font-bold text-[#2457c5] hover:underline"
                  >
                    {{ chart.patient.full_name }}
                  </Link>
                  <span v-else class="text-slate-400 italic">Unknown</span>
                  <div class="text-xs text-slate-400">#{{ chart.patient?.patient_number }}</div>
                </td>
                <td class="px-6 py-4">
                  <span class="px-2.5 py-1 rounded-full text-xs font-semibold uppercase tracking-wider bg-slate-100 text-slate-700">
                    {{ chart.dentition_type || 'mixed' }}
                  </span>
                </td>
                <td class="px-6 py-4 text-slate-700 font-medium">
                  {{ chart.teeth_count || 0 }} teeth
                </td>
                <td class="px-6 py-4 max-w-xs truncate text-slate-500">
                  {{ chart.examination_notes || '—' }}
                </td>
                <td class="px-6 py-4 text-right whitespace-nowrap">
                  <div class="inline-flex items-center gap-2">
                    <Link
                      :href="route('dental.show', chart.id)"
                      class="px-3 py-1.5 rounded-lg text-xs font-bold text-white bg-[#2457c5] hover:bg-[#1d48a3] transition-colors shadow-sm"
                    >
                      Open Chart
                    </Link>
                    <Link
                      :href="route('dental-charts.edit', chart.id)"
                      class="px-3 py-1.5 rounded-lg text-xs font-bold text-slate-700 border border-slate-300 hover:bg-slate-50 transition-colors"
                    >
                      Edit Info
                    </Link>
                  </div>
                </td>
              </tr>

              <tr v-if="!charts.data || charts.data.length === 0">
                <td colspan="6" class="px-6 py-12 text-center text-slate-400">
                  No dental charts found. Click "+ New Chart" to create a new patient examination chart.
                </td>
              </tr>
            </tbody>
          </table>
        </div>

        <!-- Pagination -->
        <div
          v-if="charts.links && charts.links.length > 3"
          class="px-6 py-4 border-t border-slate-100 flex items-center justify-between"
        >
          <div class="text-xs text-slate-500">
            Showing {{ charts.from || 0 }} to {{ charts.to || 0 }} of {{ charts.total || 0 }} charts
          </div>

          <div class="flex items-center gap-1">
            <template v-for="(link, idx) in charts.links" :key="idx">
              <Link
                v-if="link.url"
                :href="link.url"
                :class="[
                  'px-3 py-1.5 rounded text-xs font-semibold transition-colors',
                  link.active
                    ? 'bg-[#2457c5] text-white'
                    : 'text-slate-600 hover:bg-slate-100'
                ]"
                v-html="link.label"
              />
              <span
                v-else
                class="px-3 py-1.5 text-xs text-slate-300"
                v-html="link.label"
              />
            </template>
          </div>
        </div>

      </div>

    </div>
  </TenantLayout>
</template>

<script setup>
import { ref } from 'vue'
import { Head, Link } from '@inertiajs/inertia-vue3'
import { Inertia as router } from '@inertiajs/inertia'
import TenantLayout from '@/Layouts/TenantLayout.vue'

const props = defineProps({
  charts: Object,
  patients: Array,
  filters: Object,
})

const selectedPatientId = ref(props.filters?.patient_id || '')

function filterByPatient() {
  router.get(route('dental-charts.index'), {
    patient_id: selectedPatientId.value || undefined,
  }, {
    preserveState: true,
  })
}

function clearFilter() {
  selectedPatientId.value = ''
  filterByPatient()
}
</script>

