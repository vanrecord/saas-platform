<template>
  <div v-if="show" class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="detail-modal-title" role="dialog" aria-modal="true">
    <!-- Backdrop (static, does not close on backdrop click) -->
    <div class="fixed inset-0 bg-slate-900/50 backdrop-blur-xs transition-opacity"></div>

    <div class="flex min-h-screen items-center justify-center p-3 text-center sm:p-4">
      <div
        class="relative transform overflow-hidden rounded-2xl bg-white dark:bg-darker text-left shadow-2xl transition-all w-full max-w-4xl border border-slate-200 dark:border-slate-700 animate-in fade-in zoom-in duration-150"
      >
        <!-- Modal Header -->
        <div class="flex items-center justify-between border-b border-slate-100 dark:border-slate-700 px-6 py-4 bg-slate-50/70 dark:bg-dark">
          <div class="flex items-center gap-3">
            <span class="w-9 h-9 rounded-xl bg-blue-100 dark:bg-blue-900/40 text-[#0D63D6] flex items-center justify-center font-bold">
              <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
              </svg>
            </span>
            <div>
              <h3 class="text-base font-bold text-[#152B45] dark:text-light" id="detail-modal-title">
                {{ tm('Patient_Deposit_Detail') || 'Patient Deposit Details' }}
              </h3>
              <p class="text-xs text-slate-500 dark:text-slate-400">
                {{ patient ? (patient.full_name || 'Patient') : 'Loading patient records...' }}
              </p>
            </div>
          </div>

          <div class="flex items-center gap-2">
            <!-- Add New Deposit Button -->
            <button
              v-if="can(['all', 'accounting:cashDeposit:create', 'cash-deposit:create', 'cash-deposit:save'])"
              type="button"
              @click="openAddDeposit"
              class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-bold text-white bg-[#0D63D6] hover:bg-blue-700 rounded-lg shadow-sm transition cursor-pointer"
            >
              <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
              </svg>
              <span>{{ tm('Create_Cash_Deposit') || 'Add Deposit' }}</span>
            </button>

            <!-- Close Button -->
            <button
              type="button"
              @click="close"
              class="rounded-lg p-1.5 text-slate-400 hover:bg-slate-100 dark:hover:bg-gray-700 hover:text-slate-600 transition"
            >
              <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
              </svg>
            </button>
          </div>
        </div>

        <!-- Patient Summary Card -->
        <div v-if="patient" class="px-6 py-3.5 bg-blue-50/50 dark:bg-slate-800/60 border-b border-blue-100 dark:border-slate-700 flex flex-wrap items-center justify-between gap-3 text-xs">
          <div class="flex items-center gap-3">
            <div class="w-9 h-9 rounded-full bg-[#0D63D6] text-white flex items-center justify-center font-bold text-sm shadow-2xs">
              {{ patientInitials(patient) }}
            </div>
            <div>
              <div class="flex items-center gap-2">
                <span class="font-bold text-slate-900 dark:text-white text-sm">
                  {{ patient.full_name }}
                </span>
                <span v-if="patient.id_number || patient.id" class="px-2 py-0.5 rounded bg-blue-100 text-[#0D63D6] dark:bg-blue-950 dark:text-blue-300 text-[11px] font-mono font-bold">
                  ID: {{ patient.id_number || patient.id }}
                </span>
              </div>
              <div class="text-slate-500 text-[11px] mt-0.5">
                <span v-if="patient.phone">📞 {{ patient.phone }}</span>
              </div>
            </div>
          </div>

          <!-- Balance Stats -->
          <div class="flex items-center gap-4">
            <div class="text-right">
              <span class="text-[11px] text-slate-500 dark:text-slate-400 block uppercase tracking-wider font-semibold">
                {{ tm('Remain') || 'Current Balance' }}
              </span>
              <span class="text-sm font-bold font-mono text-emerald-600 dark:text-emerald-400">
                ${{ Number(patient.balance || 0).toFixed(2) }}
              </span>
            </div>
            <div class="h-8 w-px bg-slate-200 dark:bg-slate-700"></div>
            <div class="text-right">
              <span class="text-[11px] text-slate-500 dark:text-slate-400 block uppercase tracking-wider font-semibold">
                Total Verified
              </span>
              <span class="text-sm font-bold font-mono text-[#0D63D6] dark:text-blue-400">
                ${{ Number(totalVerifiedAmount).toFixed(2) }}
              </span>
            </div>
          </div>
        </div>

        <!-- Detail Table Body -->
        <div class="p-6">
          <!-- Loading State -->
          <div v-if="isLoading" class="py-12 flex flex-col items-center justify-center text-slate-500">
            <svg class="w-8 h-8 text-blue-600 animate-spin mb-3" fill="none" viewBox="0 0 24 24">
              <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
              <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
            <p class="text-xs">Loading deposits...</p>
          </div>

          <!-- Empty State -->
          <div v-else-if="deposits.length === 0" class="py-12 text-center text-slate-500 dark:text-slate-400">
            <svg class="w-12 h-12 mx-auto text-slate-300 dark:text-slate-600 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
            </svg>
            <p class="text-sm font-semibold">No cash deposit records found for this patient.</p>
            <p class="text-xs text-slate-400 mt-1">Click "Add Deposit" above to create the first deposit record.</p>
          </div>

          <!-- Deposits List Table -->
          <div v-else class="overflow-x-auto rounded-xl border border-slate-200 dark:border-slate-700">
            <table class="min-w-full divide-y divide-slate-200 dark:divide-slate-700 text-xs">
              <thead class="bg-slate-50 dark:bg-dark text-slate-600 dark:text-slate-300 font-bold uppercase tracking-wider">
                <tr>
                  <th scope="col" class="px-3.5 py-3 text-left">Action</th>
                  <th scope="col" class="px-3 py-3 text-left">No.</th>
                  <th scope="col" class="px-3 py-3 text-left">Code</th>
                  <th scope="col" class="px-3.5 py-3 text-left">Date</th>
                  <th scope="col" class="px-3 py-3 text-left">Currency</th>
                  <th scope="col" class="px-3.5 py-3 text-right">Amount</th>
                  <th scope="col" class="px-3 py-3 text-center">Deposit Type</th>
                  <th scope="col" class="px-3 py-3 text-center">Status</th>
                </tr>
              </thead>
              <tbody class="divide-y divide-slate-100 dark:divide-slate-700 bg-white dark:bg-darker">
                <tr
                  v-for="(row, idx) in deposits"
                  :key="row.id"
                  class="hover:bg-slate-50/80 dark:hover:bg-slate-800/60 transition"
                >
                  <!-- Actions Column -->
                  <td class="px-3.5 py-2.5 whitespace-nowrap">
                    <div class="flex items-center gap-1.5">
                      <!-- Verify Button (Only for Draft / Unverified) -->
                      <button
                        v-if="!row.is_verify"
                        type="button"
                        @click="verifyDeposit(row)"
                        title="Verify Deposit"
                        class="inline-flex items-center px-2 py-1 bg-emerald-600 hover:bg-emerald-700 text-white rounded text-[11px] font-bold shadow-2xs transition cursor-pointer"
                      >
                        <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7" />
                        </svg>
                        Verify
                      </button>

                      <!-- Edit Button (Only for Draft / Unverified) -->
                      <button
                        v-if="!row.is_verify && can(['all', 'accounting:cashDeposit:edit', 'cash-deposit:edit', 'cash-deposit:save'])"
                        type="button"
                        @click="editDeposit(row)"
                        title="Edit Deposit"
                        class="p-1 rounded bg-blue-50 text-blue-600 hover:bg-blue-100 dark:bg-blue-900/30 dark:text-blue-300 transition cursor-pointer"
                      >
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                        </svg>
                      </button>

                      <!-- Delete Button (Only for Draft / Unverified) -->
                      <button
                        v-if="!row.is_verify && can(['all', 'accounting:cashDeposit:delete', 'cash-deposit:delete'])"
                        type="button"
                        @click="deleteDeposit(row)"
                        title="Delete Deposit"
                        class="p-1 rounded bg-red-50 text-red-600 hover:bg-red-100 dark:bg-red-900/30 dark:text-red-300 transition cursor-pointer"
                      >
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                        </svg>
                      </button>

                      <!-- Print Button (Only for Verified records) -->
                      <button
                        v-if="row.is_verify && can(['all', 'accounting:cashDeposit:print', 'cash-deposit:print', 'accounting:cashDeposit:view'])"
                        type="button"
                        @click="printDeposit(row)"
                        title="Print Receipt"
                        class="inline-flex items-center px-2 py-1 bg-[#0D63D6] hover:bg-blue-700 text-white rounded text-[11px] font-bold shadow-2xs transition cursor-pointer"
                      >
                        <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4H7v4a2 2 0 002 2zM7 9V5a2 2 0 012-2h6a2 2 0 012 2v4" />
                        </svg>
                        Print
                      </button>
                    </div>
                  </td>

                  <!-- Row Number -->
                  <td class="px-3 py-2.5 whitespace-nowrap text-slate-500 font-mono">
                    {{ String(idx + 1).padStart(2, '0') }}
                  </td>

                  <!-- Code -->
                  <td class="px-3 py-2.5 whitespace-nowrap font-mono font-bold text-slate-800 dark:text-white">
                    {{ row.code }}
                  </td>

                  <!-- Date -->
                  <td class="px-3.5 py-2.5 whitespace-nowrap text-slate-600 dark:text-slate-300">
                    {{ row.date_formatted || row.date }}
                  </td>

                  <!-- Currency -->
                  <td class="px-3 py-2.5 whitespace-nowrap font-bold text-slate-700 dark:text-slate-200">
                    {{ row.currency }}
                  </td>

                  <!-- Amount -->
                  <td class="px-3.5 py-2.5 whitespace-nowrap text-right font-mono font-bold text-slate-900 dark:text-white">
                    {{ row.amount_formatted }}
                  </td>

                  <!-- Deposit Type -->
                  <td class="px-3 py-2.5 whitespace-nowrap text-center">
                    <span
                      class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold"
                      :class="row.is_cash
                        ? 'bg-blue-100 text-[#0D63D6] dark:bg-blue-950 dark:text-blue-300'
                        : 'bg-purple-100 text-purple-700 dark:bg-purple-950 dark:text-purple-300'"
                    >
                      {{ row.deposit_type }}
                    </span>
                  </td>

                  <!-- Status -->
                  <td class="px-3 py-2.5 whitespace-nowrap text-center">
                    <span
                      class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold"
                      :class="row.is_verify
                        ? 'bg-emerald-100 text-emerald-800 dark:bg-emerald-950 dark:text-emerald-300'
                        : 'bg-amber-100 text-amber-800 dark:bg-amber-950 dark:text-amber-300'"
                    >
                      <span class="w-1.5 h-1.5 rounded-full mr-1" :class="row.is_verify ? 'bg-emerald-500' : 'bg-amber-500'"></span>
                      {{ row.status_label }}
                    </span>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>

        <!-- Modal Footer -->
        <div class="flex items-center justify-end px-6 py-3.5 bg-slate-50/70 dark:bg-dark border-t border-slate-100 dark:border-slate-700">
          <button
            type="button"
            @click="close"
            class="px-4 py-2 text-xs font-semibold text-slate-600 dark:text-slate-300 hover:bg-slate-200/60 dark:hover:bg-slate-700 rounded-lg transition"
          >
            {{ tm('Close') || 'Close' }}
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import axios from 'axios'
import { useToast } from 'vue-toastification'

export default {
  name: 'CashDepositDetailModal',
  props: {
    show: {
      type: Boolean,
      default: false,
    },
    patientId: {
      type: [Number, String],
      default: null,
    },
  },
  emits: ['close', 'updated', 'open-create', 'open-edit'],
  data() {
    return {
      patient: null,
      deposits: [],
      isLoading: false,
    }
  },
  computed: {
    totalVerifiedAmount() {
      return this.deposits
        .filter((d) => d.is_verify)
        .reduce((sum, d) => sum + Number(d.amount || 0), 0)
    },
  },
  watch: {
    show(val) {
      if (val && this.patientId) {
        this.fetchDetails()
      }
    },
    patientId(newId) {
      if (this.show && newId) {
        this.fetchDetails()
      }
    },
  },
  methods: {
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
    async fetchDetails() {
      if (!this.patientId) return
      this.isLoading = true
      try {
        const res = await axios.get(route('cash-deposits.patient-details', this.patientId))
        this.patient = res.data?.patient || null
        this.deposits = res.data?.deposits || []
      } catch (err) {
        useToast().error('Failed to load patient deposit details.')
      } finally {
        this.isLoading = false
      }
    },
    openAddDeposit() {
      this.$emit('open-create', this.patient)
    },
    editDeposit(deposit) {
      this.$emit('open-edit', deposit, this.patient)
    },
    verifyDeposit(deposit) {
      this.$swal({
        title: this.tm('Are_you_sure_to_verify_this_deposit') || 'Are you sure you want to verify this cash deposit?',
        text: 'This will credit the patient balance and finalize this deposit record.',
        icon: 'question',
        showCancelButton: true,
        confirmButtonText: 'Yes, Verify It!',
        cancelButtonText: 'Cancel',
        customClass: {
          confirmButton: 'inline-flex items-center mx-2 px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white rounded-md font-semibold text-xs uppercase tracking-widest cursor-pointer',
          cancelButton: 'inline-flex items-center justify-center px-4 py-2 bg-slate-200 hover:bg-slate-300 text-slate-800 rounded-md font-semibold text-xs uppercase tracking-widest cursor-pointer',
        },
        buttonsStyling: false,
      }).then(async (result) => {
        if (result.value) {
          try {
            const res = await axios.post(route('cash-deposits.verify', deposit.id))
            useToast().success(
              res.data?.successful || (this.tm('Cash_deposit_verified_successfully') || 'Cash deposit verified successfully.')
            )
            await this.fetchDetails()
            this.$emit('updated')
          } catch (err) {
            const msg = err.response?.data?.error || 'Failed to verify deposit.'
            useToast().error(msg)
          }
        }
      })
    },
    deleteDeposit(deposit) {
      this.$swal({
        title: this.tm('Are_you_sure_to_delete_this_deposit') || 'Are you sure you want to delete this cash deposit?',
        text: 'This unverified draft deposit will be permanently deleted.',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Yes, Delete',
        cancelButtonText: 'Cancel',
        customClass: {
          confirmButton: 'inline-flex items-center mx-2 px-4 py-2 bg-red-600 hover:bg-red-700 text-white rounded-md font-semibold text-xs uppercase tracking-widest cursor-pointer',
          cancelButton: 'inline-flex items-center justify-center px-4 py-2 bg-slate-200 hover:bg-slate-300 text-slate-800 rounded-md font-semibold text-xs uppercase tracking-widest cursor-pointer',
        },
        buttonsStyling: false,
      }).then(async (result) => {
        if (result.value) {
          try {
            const res = await axios.delete(route('cash-deposits.destroy', deposit.id))
            useToast().success(
              res.data?.successful || (this.tm('Cash_deposit_deleted_successfully') || 'Cash deposit deleted successfully.')
            )
            await this.fetchDetails()
            this.$emit('updated')
          } catch (err) {
            const msg = err.response?.data?.error || 'Failed to delete deposit.'
            useToast().error(msg)
          }
        }
      })
    },
    printDeposit(deposit) {
      window.open(route('cash-deposits.print', deposit.id), '_blank')
    },
    close() {
      this.$emit('close')
    },
  },
}
</script>
