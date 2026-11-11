<template>
  <TenantLayout>
    <Head :title="tm('Payments') || 'Payments'" />
    <div class="px-4 py-4">
      <jet-banner />

      <!-- Breadcrumbs -->
      <nav class="flex pt-4 mb-6" aria-label="Breadcrumb">
        <ol class="inline-flex items-center space-x-1 md:space-x-3">
          <li class="inline-flex items-center">
            <Link :href="route('tenant.dashboard')" class="inline-flex items-center text-sm font-medium text-gray-700 hover:text-gray-900 dark:text-gray-400 dark:hover:text-white">
              <svg class="mr-2 w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                <path d="M10.707 2.293a1 1 0 00-1.414 0l-7 7a1 1 0 001.414 1.414L4 10.414V17a1 1 0 001 1h2a1 1 0 001-1v-2a1 1 0 011-1h2a1 1 0 011 1v2a1 1 0 001 1h2a1 1 0 001-1v-6.586l.293.293a1 1 0 001.414-1.414l-7-7z"></path>
              </svg>
              {{ tm('Home') || 'Home' }}
            </Link>
          </li>
          <li>
            <div class="flex items-center">
              <svg class="w-6 h-6 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path>
              </svg>
              <span class="ml-1 text-sm font-medium text-gray-700 hover:text-gray-900 md:ml-2 dark:text-gray-400 dark:hover:text-white">
                {{ tm('Payments') || 'Payments' }}
              </span>
            </div>
          </li>
        </ol>
      </nav>

      <!-- Main Card -->
      <div class="bg-white rounded-md dark:bg-darker shadow-sm">
        <div class="flex items-center justify-between p-4 border-b dark:border-primary">
          <h4 class="text-lg font-semibold text-gray-600 dark:text-light">
            {{ tm('Payments') || 'Payments' }}
          </h4>
        </div>

        <!-- Filter Bar Section -->
        <div class="p-4 bg-slate-50 border-b border-slate-200 dark:bg-slate-800/40 dark:border-slate-700">
          <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-3 items-end">
            <!-- 1. Start Date -->
            <div>
              <DateInput
                v-model="filters.start_date"
                :label="tm('Start_Date') || 'Start Date'"
                placeholder="YYYY-MM-DD"
                class="w-full"
              />
            </div>

            <!-- 2. End Date -->
            <div>
              <DateInput
                v-model="filters.end_date"
                :label="tm('End_Date') || 'End Date'"
                placeholder="YYYY-MM-DD"
                class="w-full"
              />
            </div>

            <!-- 3. Invoice # Search Input -->
            <div>
              <label class="block text-xs font-semibold text-slate-600 dark:text-slate-300 mb-1">
                {{ tm('Invoice_No') || 'Invoice #' }}
              </label>
              <input
                type="text"
                v-model="filters.invoice_number"
                placeholder="Search invoice #..."
                @keyup.enter="applyFilters"
                class="w-full text-xs rounded-md border-slate-300 shadow-2xs focus:border-primary focus:ring-primary dark:bg-slate-800 dark:border-slate-600 dark:text-white"
              />
            </div>

            <!-- 4. Patient (AJAX Searchable Select - Min 3 Chars) -->
            <div class="relative" ref="patientSearchDropdown">
              <label class="block text-xs font-semibold text-slate-600 dark:text-slate-300 mb-1">
                {{ tm('Patient') || 'Patient' }}
              </label>

              <!-- Selected Patient Display Badge -->
              <div
                v-if="selectedPatient"
                class="flex items-center justify-between w-full text-xs py-1.5 px-2.5 rounded-md border border-primary/40 bg-blue-50/70 text-primary font-medium"
              >
                <span class="truncate">
                  {{ selectedPatient.full_name }} (#{{ selectedPatient.id_number || selectedPatient.id }})
                </span>
                <button
                  type="button"
                  @click="clearSelectedPatient"
                  class="ml-1 text-slate-400 hover:text-red-500 font-bold"
                  title="Remove Patient Filter"
                >
                  &times;
                </button>
              </div>

              <!-- Search Input when not selected -->
              <div v-else class="relative">
                <input
                  type="text"
                  v-model="patientSearchQuery"
                  @input="handlePatientInput"
                  @focus="patientDropdownOpen = true"
                  placeholder="Type name or ID (min 3 chars)..."
                  class="w-full text-xs rounded-md border-slate-300 shadow-2xs focus:border-primary focus:ring-primary dark:bg-slate-800 dark:border-slate-600 dark:text-white pr-7"
                />
                <span v-if="patientSearching" class="absolute right-2 top-2 text-slate-400">
                  <svg class="animate-spin h-3.5 w-3.5" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" fill="none"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                  </svg>
                </span>
              </div>

              <!-- AJAX Dropdown Menu -->
              <div
                v-if="patientDropdownOpen && !selectedPatient"
                class="absolute z-50 left-0 right-0 mt-1 bg-white dark:bg-slate-800 rounded-lg shadow-lg border border-slate-200 dark:border-slate-700 max-h-56 overflow-y-auto text-xs"
              >
                <!-- Hint if < 3 chars -->
                <div v-if="patientSearchQuery.trim().length < 3" class="p-3 text-slate-400 text-center italic">
                  {{ tm('Please_enter_at_least_3_characters') || 'Please enter at least 3 characters' }}
                </div>

                <!-- Searching state -->
                <div v-else-if="patientSearching" class="p-3 text-slate-500 text-center">
                  {{ tm('Loading') || 'Searching patients...' }}
                </div>

                <!-- No results -->
                <div v-else-if="patientSearchResults.length === 0" class="p-3 text-slate-400 text-center italic">
                  {{ tm('No_patients_found') || 'No Patients found' }}
                </div>

                <!-- Results list -->
                <ul v-else class="divide-y divide-slate-100 dark:divide-slate-700">
                  <li
                    v-for="p in patientSearchResults"
                    :key="p.id"
                    @click="selectPatient(p)"
                    class="p-2 hover:bg-slate-100 dark:hover:bg-slate-700 cursor-pointer flex items-center justify-between transition"
                  >
                    <div>
                      <div class="font-bold text-slate-800 dark:text-slate-100">{{ p.full_name }}</div>
                      <div class="text-[11px] text-slate-500 font-mono">#{{ p.id_number || p.id }} &middot; {{ p.phone || 'No phone' }}</div>
                    </div>
                    <span class="text-xs text-primary font-bold">Select</span>
                  </li>
                </ul>
              </div>
            </div>

            <!-- 5. Payment Method Select -->
            <div>
              <label class="block text-xs font-semibold text-slate-600 dark:text-slate-300 mb-1">
                {{ tm('Payment_Method') || 'Payment Method' }}
              </label>
              <select
                v-model="filters.payment_method_id"
                class="w-full text-xs rounded-md border-slate-300 shadow-2xs focus:border-primary focus:ring-primary dark:bg-slate-800 dark:border-slate-600 dark:text-white"
              >
                <option value="">-- All Payment Methods --</option>
                <option v-for="pm in paymentMethods" :key="pm.id" :value="pm.id">
                  {{ pm.name }}
                </option>
              </select>
            </div>

            <!-- 6. Action Filter Buttons -->
            <div class="sm:col-span-2 lg:col-span-5 flex items-center justify-end space-x-2 pt-2 border-t border-slate-200/60 dark:border-slate-700">
              <button
                type="button"
                @click="applyFilters"
                class="inline-flex justify-center items-center px-4 py-2 bg-primary hover:bg-primary-dark text-white rounded-md text-xs font-semibold shadow-2xs transition"
              >
                <svg class="w-3.5 h-3.5 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z" />
                </svg>
                Filter
              </button>
              <button
                type="button"
                @click="clearFilters"
                class="inline-flex justify-center items-center px-4 py-2 bg-slate-200 hover:bg-slate-300 text-slate-700 rounded-md text-xs font-semibold transition"
                title="Clear Filters"
              >
                {{ tm('Clear_Filters') || 'Clear Filters' }}
              </button>
            </div>
          </div>
        </div>

        <!-- DataTable Container -->
        <div class="p-4">
          <Table :resource="payments">
            <!-- 1. Action Column (Print & Delete) -->
            <template #cell(actions)="{ item: payment }">
              <div class="flex items-center space-x-1.5 whitespace-nowrap">
                <!-- Print Button -->
                <button
                  v-if="can(['all', 'payment:print'])"
                  type="button"
                  @click="printReceipt(payment)"
                  class="p-1.5 text-slate-600 hover:text-primary rounded-md hover:bg-slate-100 dark:hover:bg-slate-700 transition"
                  :title="tm('Print') || 'Print Receipt'"
                >
                  <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4H7v4a2 2 0 002 2zM7 9V5a2 2 0 012-2h6a2 2 0 012 2v4" />
                  </svg>
                </button>

                <!-- Delete Button -->
                <button
                  v-if="can(['all', 'payment:delete'])"
                  type="button"
                  @click="deletePayment(payment)"
                  class="p-1.5 text-red-600 hover:text-red-800 rounded-md hover:bg-red-50 dark:hover:bg-red-900/20 transition"
                  :title="tm('Delete') || 'Delete Payment'"
                >
                  <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                  </svg>
                </button>
              </div>
            </template>

            <!-- 2. Date Column -->
            <template #cell(date)="{ item: payment }">
              <span class="text-xs text-slate-600 dark:text-slate-300 font-mono">
                {{ formatDate(payment.date) }}
              </span>
            </template>

            <!-- 3. Patient Column -->
            <template #cell(patient_name)="{ item: payment }">
              <Link
                v-if="payment.patient_id"
                :href="route('patients.show', payment.patient_id)"
                class="font-semibold text-primary hover:underline text-xs"
              >
                {{ payment.patient_name }}
              </Link>
              <span v-else class="text-xs text-slate-700">
                {{ payment.patient_name }}
              </span>
            </template>

            <!-- 4. Invoice # Column -->
            <template #cell(invoice_number)="{ item: payment }">
              <Link
                v-if="payment.medical_history_id"
                :href="route('invoices.show', payment.medical_history_id)"
                class="font-mono font-bold text-xs text-slate-800 hover:text-primary dark:text-slate-200"
              >
                {{ payment.invoice_number }}
              </Link>
              <span v-else class="font-mono font-bold text-xs text-slate-800 dark:text-slate-200">
                {{ payment.invoice_number }}
              </span>
            </template>

            <!-- 5. Payment Method Column -->
            <template #cell(payment_method_name)="{ item: payment }">
              <span class="text-xs font-semibold text-slate-700 dark:text-slate-300">
                {{ payment.payment_method_name }}
              </span>
            </template>

            <!-- 6. txnref Column -->
            <template #cell(txnref)="{ item: payment }">
              <span
                class="font-mono text-xs text-slate-600 dark:text-slate-400 truncate max-w-[150px] inline-block"
                :title="payment.txnref || '-'"
              >
                {{ payment.txnref || '-' }}
              </span>
            </template>

            <!-- 7. Amount ($) Column -->
            <template #cell(amount)="{ item: payment }">
              <span class="font-mono font-bold text-xs text-slate-900 dark:text-slate-100">
                ${{ (parseFloat(payment.amount) || 0).toFixed(2) }}
              </span>
            </template>

            <!-- 8. Amount (៛) Column -->
            <template #cell(amount_kh)="{ item: payment }">
              <span class="font-mono text-xs text-slate-800 dark:text-slate-200">
                {{ Math.round(payment.amount_kh || 0).toLocaleString() }} ៛
              </span>
            </template>

            <!-- 9. Paid Currency Column -->
            <template #cell(currency)="{ item: payment }">
              <span class="px-2 py-0.5 rounded text-[10px] font-extrabold uppercase bg-slate-100 text-slate-700 dark:bg-slate-700 dark:text-slate-300 border border-slate-200 dark:border-slate-600">
                {{ payment.currency || 'USD' }}
              </span>
            </template>

            <!-- 10. Verified By Column -->
            <template #cell(verified_by_name)="{ item: payment }">
              <span class="text-xs text-slate-700 dark:text-slate-300">
                {{ payment.verified_by_name }}
              </span>
            </template>

            <!-- 11. Status Column -->
            <template #cell(status)="{ item: payment }">
              <span :class="['px-2.5 py-0.5 rounded-full text-[11px] font-bold capitalize', getStatusBadgeClass(payment.status)]">
                {{ payment.status || 'Completed' }}
              </span>
            </template>
          </Table>
        </div>
      </div>
    </div>
  </TenantLayout>
</template>

<script>
import TenantLayout from '@/Layouts/TenantLayout.vue'
import { Table } from '@/Components/Datatables'
import { Head, Link } from '@inertiajs/inertia-vue3'
import JetBanner from '@/Jetstream/Banner.vue'
import Destory from '@/Libs/Action/Delete.vue'
import DateInput from '@/Components/Forms/DateInput.vue'
import axios from 'axios'
import { useToast } from 'vue-toastification'

export default {
  mixins: [Destory],
  components: {
    TenantLayout,
    Table,
    Head,
    Link,
    JetBanner,
    DateInput,
  },
  props: {
    payments: {
      type: Object,
      required: true,
    },
    paymentMethods: {
      type: Array,
      default: () => [],
    },
  },
  data() {
    const urlParams = new URLSearchParams(window.location.search);
    return {
      filters: {
        start_date: urlParams.get('filter[start_date]') || '',
        end_date: urlParams.get('filter[end_date]') || '',
        invoice_number: urlParams.get('filter[invoice_number]') || '',
        patient_id: urlParams.get('filter[patient_id]') || '',
        payment_method_id: urlParams.get('filter[payment_method_id]') || '',
      },
      patientSearchQuery: '',
      patientSearching: false,
      patientSearchResults: [],
      patientDropdownOpen: false,
      selectedPatient: null,
      patientDebounceTimer: null,
    };
  },
  mounted() {
    document.addEventListener('click', this.handleOutsideClick);
  },
  beforeUnmount() {
    document.removeEventListener('click', this.handleOutsideClick);
  },
  methods: {
    formatDate(dateStr) {
      if (!dateStr) return '-';
      const d = new Date(dateStr);
      return d.toLocaleDateString('en-US', {
        year: 'numeric',
        month: 'short',
        day: 'numeric',
      });
    },
    getStatusBadgeClass(status) {
      const s = String(status || 'completed').toLowerCase();
      if (s === 'completed' || s === 'paid') {
        return 'bg-emerald-100 text-emerald-800 dark:bg-emerald-900/40 dark:text-emerald-300';
      }
      if (s === 'verified') {
        return 'bg-blue-100 text-blue-800 dark:bg-blue-900/40 dark:text-blue-300';
      }
      if (s === 'pending') {
        return 'bg-amber-100 text-amber-800 dark:bg-amber-900/40 dark:text-amber-300';
      }
      if (s === 'refunded') {
        return 'bg-purple-100 text-purple-800 dark:bg-purple-900/40 dark:text-purple-300';
      }
      return 'bg-slate-100 text-slate-700 dark:bg-slate-700 dark:text-slate-300';
    },

    // Patient AJAX Search (Minimum 3 Characters required)
    handlePatientInput() {
      clearTimeout(this.patientDebounceTimer);
      const query = this.patientSearchQuery.trim();

      // Enforce: Do not send request if less than 3 characters
      if (query.length < 3) {
        this.patientSearching = false;
        this.patientSearchResults = [];
        this.patientDropdownOpen = true;
        return;
      }

      this.patientSearching = true;
      this.patientDropdownOpen = true;

      this.patientDebounceTimer = setTimeout(() => {
        axios
          .get(route('tenant.payments.patients-search'), {
            params: { q: query },
          })
          .then((res) => {
            this.patientSearchResults = res.data || [];
          })
          .catch((err) => {
            console.error('Failed to search patients:', err);
            this.patientSearchResults = [];
          })
          .finally(() => {
            this.patientSearching = false;
          });
      }, 300);
    },
    selectPatient(patient) {
      this.selectedPatient = patient;
      this.filters.patient_id = patient.id;
      this.patientDropdownOpen = false;
      this.patientSearchQuery = '';
      this.patientSearchResults = [];
    },
    clearSelectedPatient() {
      this.selectedPatient = null;
      this.filters.patient_id = '';
      this.patientSearchQuery = '';
      this.patientSearchResults = [];
      this.patientDropdownOpen = false;
    },
    handleOutsideClick(event) {
      if (this.$refs.patientSearchDropdown && !this.$refs.patientSearchDropdown.contains(event.target)) {
        this.patientDropdownOpen = false;
      }
    },

    // Apply Combined Filters
    applyFilters() {
      // Validate: End date not earlier than Start date
      if (this.filters.start_date && this.filters.end_date) {
        if (new Date(this.filters.end_date) < new Date(this.filters.start_date)) {
          alert('End Date cannot be earlier than Start Date.');
          return;
        }
      }

      const params = {};
      if (this.filters.start_date) params['filter[start_date]'] = this.filters.start_date;
      if (this.filters.end_date) params['filter[end_date]'] = this.filters.end_date;
      if (this.filters.invoice_number) params['filter[invoice_number]'] = this.filters.invoice_number.trim();
      if (this.filters.patient_id) params['filter[patient_id]'] = this.filters.patient_id;
      if (this.filters.payment_method_id) params['filter[payment_method_id]'] = this.filters.payment_method_id;

      this.$inertia.get(route('tenant.payments.index'), params, {
        preserveState: true,
        preserveScroll: true,
      });
    },

    // Clear Filters
    clearFilters() {
      this.filters = {
        start_date: '',
        end_date: '',
        invoice_number: '',
        patient_id: '',
        payment_method_id: '',
      };
      this.clearSelectedPatient();
      this.$inertia.get(route('tenant.payments.index'), {}, {
        preserveState: true,
        preserveScroll: true,
      });
    },

    // Print Receipt Action
    printReceipt(payment) {
      const url = route('tenant.payments.print', payment.id) + '?auto_print=1';
      window.open(url, '_blank', 'width=800,height=900,scrollbars=yes');
    },

    // Delete Payment Action with Business Rule Confirmation
    deletePayment(payment) {
      this.$swal({
        title: this.tm('Confirm_Delete') || 'Are you sure?',
        text: `Delete payment #${payment.id} of $${parseFloat(payment.amount || 0).toFixed(2)}? Related invoice balance will be recalculated.`,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Yes, delete it!',
        cancelButtonText: 'Cancel',
        customClass: {
          confirmButton: 'inline-flex items-center mx-2 px-4 py-2 bg-red-600 hover:bg-red-700 text-white text-xs font-bold rounded-md uppercase tracking-wider',
          cancelButton: 'inline-flex items-center px-4 py-2 bg-slate-200 hover:bg-slate-300 text-slate-700 text-xs font-bold rounded-md uppercase tracking-wider',
        },
        buttonsStyling: false,
      }).then((result) => {
        if (result.value) {
          this.$inertia.delete(route('tenant.payments.destroy', payment.id), {
            preserveScroll: true,
            onSuccess: () => {
              useToast().success(this.tm('Payment_deleted_successfully') || 'Payment deleted successfully.');
            },
            onError: (err) => {
              useToast().error(err.error || this.tm('Unable_to_delete_this_payment') || 'Unable to delete this payment.');
            },
          });
        }
      });
    },
  },
}
</script>
