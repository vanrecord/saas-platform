<template>
  <TenantLayout>
    <Head :title="tm('Cash_Deposit') || 'Cash Deposit'" />
    <div class="px-4 py-4">
      <jet-banner />

      <!-- Breadcrumb Navigation -->
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
              <span class="ml-1 text-sm font-medium text-gray-700 hover:text-gray-900 md:ml-2 dark:text-gray-400 dark:hover:text-white">
                {{ tm('Cash_Deposit') || 'Cash Deposit' }}
              </span>
            </div>
          </li>
        </ol>
      </nav>

      <!-- Main Card -->
      <div class="bg-white rounded-xl dark:bg-darker shadow-sm border border-slate-100 dark:border-slate-800">
        <!-- Top Toolbar Header -->
        <div class="flex flex-col sm:flex-row sm:items-center justify-between p-4 border-b border-slate-100 dark:border-slate-800 gap-3">
          <div class="flex items-center gap-3">
            <span class="w-9 h-9 rounded-xl bg-blue-100 dark:bg-blue-900/40 text-[#0D63D6] flex items-center justify-center font-bold">
              <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z" />
              </svg>
            </span>
            <div>
              <h4 class="text-base font-bold text-slate-800 dark:text-light">
                {{ tm('Cash_Deposit') || 'Cash Deposit' }}
              </h4>
              <p class="text-xs text-slate-500 dark:text-slate-400">
                Patient deposit ledger grouped by patient
              </p>
            </div>
          </div>

          <div class="flex items-center gap-2">
            <!-- Create Cash Deposit Button -->
            <button
              v-if="can(['all', 'accounting:cashDeposit:create', 'cash-deposit:create', 'cash-deposit:save'])"
              type="button"
              @click="openCreateModal"
              class="inline-flex items-center gap-1.5 px-4 py-2 bg-[#0D63D6] hover:bg-blue-700 border border-transparent rounded-lg font-bold text-xs text-white uppercase tracking-wider shadow-sm transition cursor-pointer"
            >
              <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
              </svg>
              <span>+ {{ tm('Create_Cash_Deposit') || 'Create Cash Deposit' }}</span>
            </button>
          </div>
        </div>

        <!-- Filter Bar (Patient Filter Autocomplete) -->
        <div class="p-4 bg-slate-50/60 dark:bg-slate-900/40 border-b border-slate-100 dark:border-slate-800 flex flex-wrap items-center gap-3">
          <div class="text-xs font-bold text-slate-600 dark:text-slate-300 uppercase tracking-wider">
            Filter:
          </div>

          <!-- Patient Search Input with Autocomplete -->
          <div class="relative min-w-[260px] max-w-sm">
            <!-- When a patient filter is currently applied -->
            <div
              v-if="selectedFilterPatient"
              class="flex items-center justify-between px-3 py-1.5 rounded-lg border border-blue-200 bg-blue-50 dark:bg-blue-950/40 dark:border-blue-900/50 text-xs"
            >
              <span class="font-bold text-[#0D63D6] truncate mr-2">
                {{ selectedFilterPatient.full_name }}
              </span>
              <button
                type="button"
                @click="clearPatientFilter"
                class="text-slate-400 hover:text-red-500 font-bold p-0.5 rounded cursor-pointer"
                title="Clear patient filter"
              >
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
              </button>
            </div>

            <!-- Autocomplete input when no filter active -->
            <div v-else class="relative">
              <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-slate-400">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                </svg>
              </div>
              <input
                type="text"
                v-model="patientFilterQuery"
                @input="onPatientFilterInput"
                :placeholder="tm('Search_patient_placeholder') || 'Filter by patient (min 2 chars)...'"
                class="w-full text-xs rounded-lg border border-slate-300 dark:border-slate-600 dark:bg-slate-800 dark:text-white pl-9 pr-8 py-1.5 focus:border-[#0D63D6] focus:ring-1 focus:ring-blue-100 focus:outline-none transition"
              />
              <div v-if="isSearchingPatient" class="absolute inset-y-0 right-0 pr-2.5 flex items-center">
                <svg class="w-3.5 h-3.5 text-blue-600 animate-spin" fill="none" viewBox="0 0 24 24">
                  <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                  <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
              </div>

              <!-- Autocomplete dropdown -->
              <div
                v-if="patientFilterResults.length > 0"
                class="absolute left-0 right-0 mt-1 bg-white dark:bg-slate-800 rounded-lg shadow-xl border border-slate-200 dark:border-slate-700 z-30 max-h-56 overflow-y-auto divide-y divide-slate-100 dark:divide-slate-700"
              >
                <button
                  v-for="p in patientFilterResults"
                  :key="p.id"
                  type="button"
                  @click="applyPatientFilter(p)"
                  class="w-full text-left px-3 py-2 hover:bg-[#EBF3FE]/70 dark:hover:bg-slate-700 flex items-center justify-between text-xs transition cursor-pointer"
                >
                  <div class="font-bold text-slate-800 dark:text-white">
                    {{ p.full_name || `${p.firstname || ''} ${p.lastname || ''}`.trim() }}
                    <span v-if="p.id_number || p.id" class="text-[10px] text-slate-400 font-mono ml-1">
                      #{{ p.id_number || p.id }}
                    </span>
                  </div>
                  <span class="text-[11px] font-mono text-emerald-600 font-bold">
                    ${{ Number(p.balance || 0).toFixed(2) }}
                  </span>
                </button>
              </div>
            </div>
          </div>
        </div>

        <!-- DataTable Container -->
        <div class="p-4">
          <Table :resource="deposits">
            <!-- 1. Action Column (View Detail Button) -->
            <template #cell(actions)="{ item: row }">
              <div class="flex items-center gap-1.5">
                <button
                  type="button"
                  @click="openDetailModal(row)"
                  class="inline-flex items-center gap-1 px-2.5 py-1 text-xs font-bold text-[#0D63D6] hover:text-white bg-blue-50 hover:bg-[#0D63D6] dark:bg-blue-950/40 dark:text-blue-300 dark:hover:bg-blue-700 rounded-lg shadow-2xs transition cursor-pointer"
                  title="View Detail"
                >
                  <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16" />
                  </svg>
                  <span>Detail</span>
                </button>
              </div>
            </template>

            <!-- 2. Row Index -->
            <template #cell(index)="{ item: row }">
              <span class="text-xs font-mono text-slate-500">
                {{ row.index || String(deposits.data.indexOf(row) + 1 + (deposits.from ? deposits.from - 1 : 0)).padStart(2, '0') }}
              </span>
            </template>

            <!-- 3. Patient Full Name -->
            <template #cell(fullname)="{ item: row }">
              <div class="flex items-center gap-2">
                <div class="w-7 h-7 rounded-full bg-blue-100 dark:bg-blue-900/40 text-[#0D63D6] dark:text-blue-300 flex items-center justify-center font-bold text-xs flex-shrink-0">
                  {{ patientInitials(row.fullname) }}
                </div>
                <div>
                  <div class="font-bold text-xs text-slate-900 dark:text-white">
                    {{ row.fullname }}
                  </div>
                  <div v-if="row.id_number || row.patient_id" class="text-[10px] text-slate-400 font-mono">
                    ID: {{ row.id_number || row.patient_id }}
                  </div>
                </div>
              </div>
            </template>

            <!-- 4. Currency -->
            <template #cell(currency)="{ item: row }">
              <span class="inline-flex items-center px-2 py-0.5 rounded text-[11px] font-bold bg-slate-100 text-slate-700 dark:bg-slate-800 dark:text-slate-300 font-mono">
                {{ row.currency || 'USD' }}
              </span>
            </template>

            <!-- 5. Amount (Total verified deposit amount) -->
            <template #cell(amount)="{ item: row }">
              <span class="font-mono text-xs font-bold text-slate-900 dark:text-white">
                {{ row.amount_formatted || Number(row.amount || 0).toFixed(2) }}
              </span>
            </template>

            <!-- 6. Remain (Patient balance) -->
            <template #cell(remain)="{ item: row }">
              <span class="font-mono text-xs font-bold text-emerald-600 dark:text-emerald-400">
                {{ row.remain_formatted || Number(row.remain || 0).toFixed(2) }}
              </span>
            </template>

            <!-- 7. Total Paid on Deposit -->
            <template #cell(paid_deposit)="{ item: row }">
              <span class="font-mono text-xs font-semibold text-purple-700 dark:text-purple-300">
                {{ row.paid_deposit || '0.00' }}
              </span>
            </template>
          </Table>
        </div>
      </div>
    </div>

    <!-- Patient Deposit Detail Modal -->
    <CashDepositDetailModal
      ref="detailModal"
      :show="showDetailModal"
      :patient-id="selectedDetailPatientId"
      @close="closeDetailModal"
      @updated="onDetailUpdated"
      @open-create="openCreateFromDetail"
      @open-edit="openEditFromDetail"
    />

    <!-- Create / Edit Cash Deposit Modal (Higher z-index layer) -->
    <CashDepositFormModal
      :show="showCreateModal"
      :deposit="selectedDeposit"
      :patient="selectedPatientForCreate"
      :next-code="nextCode"
      @close="closeCreateModal"
      @saved="onDepositSaved"
    />
  </TenantLayout>
</template>

<script>
import { defineComponent } from 'vue'
import TenantLayout from '@/Layouts/TenantLayout.vue'
import { Table } from '@/Components/Datatables'
import JetBanner from '@/Jetstream/Banner.vue'
import CashDepositFormModal from '@/Components/Tenant/CashDepositFormModal.vue'
import CashDepositDetailModal from '@/Components/Tenant/CashDepositDetailModal.vue'
import { Head, Link } from '@inertiajs/inertia-vue3'
import { Inertia } from '@inertiajs/inertia'
import axios from 'axios'

export default defineComponent({
  name: 'CashDepositIndex',
  components: {
    TenantLayout,
    Table,
    JetBanner,
    CashDepositFormModal,
    CashDepositDetailModal,
    Head,
    Link,
  },
  props: {
    deposits: {
      type: Object,
      required: true,
    },
    nextCode: {
      type: [Number, String],
      default: 1,
    },
  },
  data() {
    return {
      showCreateModal: false,
      showDetailModal: false,
      selectedDeposit: null,
      selectedPatientForCreate: null,
      selectedDetailPatientId: null,

      // Filter by patient state
      patientFilterQuery: '',
      patientFilterResults: [],
      isSearchingPatient: false,
      patientFilterTimer: null,
      selectedFilterPatient: null,
    }
  },
  methods: {
    patientInitials(name) {
      if (!name) return '?'
      const parts = String(name).trim().split(/\s+/)
      if (parts.length >= 2) return `${parts[0][0]}${parts[1][0]}`.toUpperCase()
      return parts[0][0].toUpperCase()
    },
    openCreateModal() {
      this.selectedDeposit = null
      this.selectedPatientForCreate = null
      this.showCreateModal = true
    },
    closeCreateModal() {
      this.showCreateModal = false
      this.selectedDeposit = null
      this.selectedPatientForCreate = null
    },
    openDetailModal(row) {
      this.selectedDetailPatientId = row.patient_id
      this.showDetailModal = true
    },
    closeDetailModal() {
      this.showDetailModal = false
      this.selectedDetailPatientId = null
    },
    openCreateFromDetail(patient) {
      this.selectedDeposit = null
      this.selectedPatientForCreate = patient
      this.showCreateModal = true
    },
    openEditFromDetail(deposit, patient) {
      this.selectedDeposit = { ...deposit, patient: patient || deposit.patient }
      this.selectedPatientForCreate = patient
      this.showCreateModal = true
    },
    onDepositSaved() {
      Inertia.reload({ preserveScroll: true, preserveState: true })
      if (this.showDetailModal && this.$refs.detailModal) {
        this.$refs.detailModal.fetchDetails()
      }
    },
    onDetailUpdated() {
      Inertia.reload({ preserveScroll: true, preserveState: true })
    },
    onPatientFilterInput() {
      clearTimeout(this.patientFilterTimer)
      const q = (this.patientFilterQuery || '').trim()

      if (q.length < 2) {
        this.patientFilterResults = []
        this.isSearchingPatient = false
        return
      }

      this.isSearchingPatient = true
      this.patientFilterTimer = setTimeout(async () => {
        try {
          const res = await axios.get(route('cash-deposits.patient-search'), { params: { q } })
          this.patientFilterResults = res.data || []
        } catch (e) {
          this.patientFilterResults = []
        } finally {
          this.isSearchingPatient = false
        }
      }, 250)
    },
    applyPatientFilter(p) {
      this.selectedFilterPatient = {
        id: p.id,
        full_name: p.full_name || `${p.firstname || ''} ${p.lastname || ''}`.trim(),
      }
      this.patientFilterQuery = ''
      this.patientFilterResults = []

      Inertia.get(
        route('cash-deposits.index'),
        { 'filter[patient_id]': p.id },
        { preserveState: true, preserveScroll: true, replace: true }
      )
    },
    clearPatientFilter() {
      this.selectedFilterPatient = null
      this.patientFilterQuery = ''
      this.patientFilterResults = []

      Inertia.get(
        route('cash-deposits.index'),
        {},
        { preserveState: true, preserveScroll: true, replace: true }
      )
    },
  },
})
</script>
