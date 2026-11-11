<template>
  <TenantLayout>
    <Head :title="tm('Invoices') || 'Invoices'" />
    <div class="px-4 py-4">
      <jet-banner />
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
                {{ tm('Invoices') || 'Invoices' }}
              </span>
            </div>
          </li>
        </ol>
      </nav>

      <!-- Main Card -->
      <div class="bg-white rounded-md dark:bg-darker shadow-sm">
        <div class="flex items-center justify-between p-4 border-b dark:border-primary">
          <h4 class="text-lg font-semibold text-gray-600 dark:text-light">
            {{ tm('Invoices') || 'Invoices' }}
          </h4>
          <div class="flex items-center space-x-2">
            <Link
              v-if="can(['all', 'invoice:create', 'invoice:save', 'invoices.create'])"
              :href="route('invoices.create')"
              class="inline-flex items-center px-4 py-2 bg-primary hover:bg-primary-dark border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest transition"
            >
              + {{ tm('Create_Invoice') || 'Create Invoice' }}
            </Link>
          </div>
        </div>

        <!-- Filter Bar Section -->
        <div class="p-4 bg-slate-50 border-b border-slate-200 dark:bg-slate-800/40 dark:border-slate-700">
          <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-3 items-end">
            <!-- Start Date -->
            <div>
              <DateInput
                v-model="filters.start_date"
                :label="tm('Start_Date') || 'Start Date'"
                placeholder="YYYY-MM-DD"
                class="w-full"
              />
            </div>

            <!-- End Date -->
            <div>
              <DateInput
                v-model="filters.end_date"
                :label="tm('End_Date') || 'End Date'"
                placeholder="YYYY-MM-DD"
                class="w-full"
              />
            </div>

            <!-- Patient Select -->
            <div>
              <label class="block text-xs font-semibold text-slate-600 dark:text-slate-300 mb-1">
                {{ tm('Patient') || 'Patient' }}
              </label>
              <select
                v-model="filters.patient_id"
                class="w-full text-xs rounded-md border-slate-300 shadow-2xs focus:border-primary focus:ring-primary dark:bg-slate-800 dark:border-slate-600 dark:text-white"
              >
                <option value="">{{ tm('All_Patients') || '-- All Patients --' }}</option>
                <option v-for="p in patients" :key="p.id" :value="p.id">
                  {{ p.full_name }} (#{{ p.id_number || p.id }})
                </option>
              </select>
            </div>

            <!-- Status Select -->
            <div>
              <label class="block text-xs font-semibold text-slate-600 dark:text-slate-300 mb-1">
                {{ tm('Status') || 'Status' }}
              </label>
              <select
                v-model="filters.status"
                class="w-full text-xs rounded-md border-slate-300 shadow-2xs focus:border-primary focus:ring-primary dark:bg-slate-800 dark:border-slate-600 dark:text-white"
              >
                <option value="">{{ tm('All_Statuses') || '-- All Statuses --' }}</option>
                <option value="unpaid">Unpaid</option>
                <option value="partial">Partial</option>
                <option value="paid">Paid</option>
                <option value="refunded">Refunded</option>
              </select>
            </div>

            <!-- Filter Buttons -->
            <div class="flex items-center space-x-2">
              <button
                type="button"
                @click="applyFilters"
                class="flex-1 inline-flex justify-center items-center px-3 py-2 bg-primary hover:bg-primary-dark text-white rounded-md text-xs font-semibold shadow-2xs transition"
              >
                <svg class="w-3.5 h-3.5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z" />
                </svg>
                Filter
              </button>
              <button
                type="button"
                @click="resetFilters"
                class="inline-flex justify-center items-center px-3 py-2 bg-slate-200 hover:bg-slate-300 text-slate-700 rounded-md text-xs font-semibold transition"
                title="Clear Filters"
              >
                Clear
              </button>
            </div>
          </div>
        </div>

        <!-- DataTable -->
        <div class="p-4">
          <Table :resource="invoices">
            <!-- 1. Action -->
            <template #cell(actions)="{ item: invoice }">
              <div class="flex items-center space-x-1.5 whitespace-nowrap">
                <!-- Pay -->
                <button
                  type="button"
                  @click="openPaymentModal(invoice)"
                  class="inline-flex items-center gap-1 px-2 py-1 text-xs font-semibold rounded bg-emerald-50 text-emerald-700 hover:bg-emerald-100 hover:text-emerald-800 border border-emerald-200 transition shadow-2xs"
                  title="Pay & Transaction History"
                >
                  <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z" />
                  </svg>
                  <span>Pay</span>
                </button>

                <!-- View -->
                <Link
                  :href="route('invoices.show', invoice.id)"
                  class="p-1 text-slate-500 hover:text-slate-800 rounded hover:bg-slate-100 transition"
                  title="View Invoice"
                >
                  <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                  </svg>
                </Link>

                <!-- Edit -->
                <Link
                  v-if="can(['all', 'invoice:edit', 'invoice:save'])"
                  :href="route('invoices.edit', invoice.id)"
                  class="p-1 text-blue-600 hover:text-blue-800 rounded hover:bg-blue-50 transition"
                  title="Edit Invoice"
                >
                  <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                  </svg>
                </Link>

                <!-- Delete -->
                <button
                  v-if="can(['all', 'invoice:delete'])"
                  type="button"
                  @click="deleteInvoice(invoice)"
                  class="p-1 text-red-600 hover:text-red-800 rounded hover:bg-red-50 transition"
                  title="Delete Invoice"
                >
                  <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                  </svg>
                </button>
              </div>
            </template>

            <!-- 2. Daily Date -->
            <template #cell(date)="{ item: invoice }">
              <span class="text-xs text-slate-700 whitespace-nowrap">
                {{ formatDate(invoice.date) }}
              </span>
            </template>

            <!-- 3. Invoice # -->
            <template #cell(invoice_number)="{ item: invoice }">
              <Link :href="route('invoices.show', invoice.id)" class="font-bold text-primary hover:underline font-mono text-xs">
                {{ invoice.invoice_number_formatted || invoice.invoice_number }}
              </Link>
            </template>

            <!-- 4. Quote # -->
            <template #cell(estimate_id)="{ item: invoice }">
              <span class="text-xs text-slate-500 font-mono">
                {{ invoice.estimate_id ? `#${invoice.estimate_id}` : '-' }}
              </span>
            </template>

            <!-- 5. ID Patient -->
            <template #cell(patient_id_number)="{ item: invoice }">
              <span class="text-xs font-mono font-bold text-slate-800">
                #{{ invoice.patient?.id_number || invoice.patient?.id || '-' }}
              </span>
            </template>

            <!-- 6. Patient -->
            <template #cell(patient_name)="{ item: invoice }">
              <span class="font-semibold text-slate-800 text-xs">
                {{ invoice.patient_name || (invoice.patient ? (invoice.patient.firstname + ' ' + invoice.patient.lastname) : '-') }}
              </span>
            </template>

            <!-- 7. Visit Type -->
            <template #cell(visit_type)="{ item: invoice }">
              <span class="capitalize text-xs text-slate-600">
                {{ invoice.visit_type || 'Initial' }}
              </span>
            </template>

            <!-- 8. Diagnosis -->
            <template #cell(diagnosis)="{ item: invoice }">
              <span class="text-xs text-slate-600 truncate max-w-xs block" :title="invoice.diagnosis">
                {{ invoice.diagnosis || '-' }}
              </span>
            </template>

            <!-- 9. Total ($) -->
            <template #cell(total)="{ item: invoice }">
              <span class="font-bold text-slate-800 text-xs text-right block font-mono">
                ${{ formatCurrency(invoice.total) }}
              </span>
            </template>

            <!-- 10. Refunded ($) -->
            <template #cell(refunded)="{ item: invoice }">
              <span class="text-xs text-purple-700 text-right block font-mono">
                ${{ formatCurrency(invoice.refunded) }}
              </span>
            </template>

            <!-- 11. Balance ($) -->
            <template #cell(balance)="{ item: invoice }">
              <span
                :class="[
                  'font-bold text-xs text-right block font-mono',
                  invoice.balance > 0.001 ? 'text-red-600 font-bold' : 'text-emerald-600'
                ]"
              >
                ${{ formatCurrency(invoice.balance) }}
              </span>
            </template>

            <!-- 12. Status -->
            <template #cell(status)="{ item: invoice }">
              <span
                :class="[
                  'inline-flex items-center px-2 py-0.5 rounded-full text-2xs font-bold uppercase tracking-wider',
                  invoice.status === 3 || invoice.status_label === 'Paid'
                    ? 'bg-emerald-100 text-emerald-800'
                    : invoice.status === 1 || invoice.status_label === 'Partial'
                    ? 'bg-blue-100 text-blue-800'
                    : invoice.status === 4 || invoice.status_label === 'Refunded'
                    ? 'bg-purple-100 text-purple-800'
                    : invoice.status === 5 || invoice.status_label === 'Partially Refunded'
                    ? 'bg-indigo-100 text-indigo-800'
                    : 'bg-amber-100 text-amber-800'
                ]"
              >
                {{ invoice.status_label || 'Unpaid' }}
              </span>
            </template>
          </Table>
        </div>
      </div>
    </div>

    <!-- Payment & Transaction History Modal -->
    <PaymentModal
      :show="showPaymentModal"
      :invoice-id="selectedInvoiceId"
      @close="closePaymentModal"
      @payment-saved="onPaymentSaved"
    />
  </TenantLayout>
</template>

<script>
import TenantLayout from "@/Layouts/TenantLayout.vue";
import { Head, Link } from "@inertiajs/inertia-vue3";
import JetBanner from "@/Jetstream/Banner.vue";
import { Table } from "@/Components/Datatables";
import DateInput from "@/Components/Forms/DateInput.vue";
import PaymentModal from "@/Components/Tenant/PaymentModal.vue";

export default {
  components: {
    TenantLayout,
    Head,
    Link,
    JetBanner,
    Table,
    DateInput,
    PaymentModal,
  },
  props: {
    invoices: Object,
    patients: Array,
  },
  data() {
    const urlParams = new URLSearchParams(window.location.search);
    return {
      showPaymentModal: false,
      selectedInvoiceId: null,
      filters: {
        start_date: urlParams.get("filter[start_date]") || "",
        end_date: urlParams.get("filter[end_date]") || "",
        patient_id: urlParams.get("filter[patient_id]") || "",
        status: urlParams.get("filter[status]") || "",
      },
    };
  },
  methods: {
    openPaymentModal(invoice) {
      this.selectedInvoiceId = invoice.id;
      this.showPaymentModal = true;
    },
    closePaymentModal() {
      this.showPaymentModal = false;
      this.selectedInvoiceId = null;
    },
    onPaymentSaved(updatedInvoice) {
      if (this.invoices?.data && Array.isArray(this.invoices.data)) {
        const index = this.invoices.data.findIndex((inv) => inv.id === updatedInvoice.id);
        if (index !== -1) {
          this.invoices.data[index].paid_usd = updatedInvoice.paid_usd;
          this.invoices.data[index].paid_kh = updatedInvoice.paid_kh;
          this.invoices.data[index].balance = updatedInvoice.balance;
          this.invoices.data[index].status = updatedInvoice.status;
          this.invoices.data[index].status_label = updatedInvoice.status_label;
        }
      }
      this.$toast?.success("Payment recorded successfully.");
    },
    formatDate(dateStr) {
      if (!dateStr) return "-";
      const d = new Date(dateStr);
      return d.toLocaleDateString();
    },
    formatCurrency(val) {
      const num = parseFloat(val) || 0;
      return num.toLocaleString(undefined, {
        minimumFractionDigits: 2,
        maximumFractionDigits: 2,
      });
    },
    applyFilters() {
      if (this.filters.start_date && this.filters.end_date && this.filters.end_date < this.filters.start_date) {
        alert("End Date cannot be earlier than Start Date.");
        return;
      }

      const query = {};
      if (this.filters.start_date) query["filter[start_date]"] = this.filters.start_date;
      if (this.filters.end_date) query["filter[end_date]"] = this.filters.end_date;
      if (this.filters.patient_id) query["filter[patient_id]"] = this.filters.patient_id;
      if (this.filters.status) query["filter[status]"] = this.filters.status;

      this.$inertia.get(route("invoices.index"), query, {
        preserveState: true,
        preserveScroll: true,
      });
    },
    resetFilters() {
      this.filters.start_date = "";
      this.filters.end_date = "";
      this.filters.patient_id = "";
      this.filters.status = "";

      this.$inertia.get(route("invoices.index"), {}, {
        preserveState: true,
        preserveScroll: true,
      });
    },
    deleteInvoice(invoice) {
      const num = invoice.invoice_number_formatted || invoice.invoice_number;
      if (confirm(`Are you sure you want to delete Invoice "${num}"?`)) {
        this.$inertia.delete(route("invoices.destroy", invoice.id), {
          onSuccess: () => {
            this.$toast?.success("Invoice deleted successfully.");
          },
        });
      }
    },
  },
};
</script>
