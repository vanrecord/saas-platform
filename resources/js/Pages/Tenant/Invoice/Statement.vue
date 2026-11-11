<template>
  <div class="min-h-screen bg-slate-100 py-8 px-4 print:p-0 print:bg-white text-slate-800">
    <Head :title="`Statement - ${invoice.invoice_number_formatted || 'INV #' + invoice.id}`" />

    <!-- Statement Container -->
    <div class="max-w-3xl mx-auto bg-white rounded-xl shadow-lg border border-slate-200 p-8 print:p-4 print:shadow-none print:border-none print:max-w-none">
      
      <!-- Top Action Bar (hidden on print) -->
      <div class="flex items-center justify-between pb-6 mb-6 border-b border-slate-200 print:hidden">
        <button
          type="button"
          @click="goBack"
          class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-semibold text-slate-600 bg-slate-100 hover:bg-slate-200 rounded-lg transition"
        >
          <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
          </svg>
          Back
        </button>

        <button
          type="button"
          @click="printNow"
          class="inline-flex items-center gap-1.5 px-4 py-2 text-xs font-bold text-white bg-slate-900 hover:bg-slate-800 rounded-lg shadow-sm transition"
        >
          <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4H7v4a2 2 0 002 2zM7 9V5a2 2 0 012-2h6a2 2 0 012 2v4" />
          </svg>
          Print / Export Statement
        </button>
      </div>

      <!-- Printable Area -->
      <div id="statement-print-area">
        <!-- Header -->
        <div class="text-center pb-6 border-b border-slate-200">
          <h1 class="text-2xl font-black tracking-tight text-slate-900 uppercase">
            {{ hospital?.name || 'Dental Clinic Management' }}
          </h1>
          <p class="text-xs text-slate-500 mt-1">
            {{ hospital?.address || 'Phnom Penh, Cambodia' }} &middot; Phone: {{ hospital?.phone || '(+855) 23 888 999' }}
          </p>
          <div class="inline-block mt-3 px-3 py-1 bg-slate-100 text-slate-800 rounded-full text-xs font-bold uppercase tracking-wider">
            Statement of Account & Transactions
          </div>
        </div>

        <!-- Meta Grid -->
        <div class="grid grid-cols-2 gap-4 py-5 text-xs border-b border-slate-100">
          <div>
            <div class="text-slate-500 font-semibold uppercase text-[10px] tracking-wider">Patient Details</div>
            <div class="font-bold text-slate-900 text-sm mt-0.5">{{ invoice.patient_name || '-' }}</div>
            <div class="text-slate-600 font-mono">Patient ID: #{{ invoice.patient?.id_number || invoice.patient_id }}</div>
            <div v-if="invoice.patient?.phone" class="text-slate-600">Phone: {{ invoice.patient.phone }}</div>
            <div class="text-slate-600 mt-1">Available Credit: <strong class="text-emerald-600">${{ formatCurrency(invoice.patient?.balance || 0) }}</strong></div>
          </div>

          <div class="text-right">
            <div class="text-slate-500 font-semibold uppercase text-[10px] tracking-wider">Invoice Details</div>
            <div class="font-mono font-bold text-slate-900 text-sm mt-0.5">{{ invoice.invoice_number_formatted }}</div>
            <div class="text-slate-600">Date: {{ formatDate(invoice.date) }}</div>
            <div class="text-slate-600">Doctor: {{ invoice.doctor_name || 'N/A' }}</div>
            <div class="mt-1">
              Status:
              <span
                class="inline-block px-2 py-0.5 rounded text-[10px] font-bold uppercase"
                :class="statusBadgeClass(invoice.status)"
              >
                {{ invoice.status_label }}
              </span>
            </div>
          </div>
        </div>

        <!-- Financial Overview Summary -->
        <div class="grid grid-cols-3 gap-3 py-4 border-b border-slate-100 text-center">
          <div class="p-3 bg-slate-50 rounded-lg">
            <div class="text-[10px] font-bold uppercase text-slate-500">Invoice Total</div>
            <div class="text-base font-bold text-slate-900 mt-0.5">${{ formatCurrency(invoice.total) }}</div>
          </div>
          <div class="p-3 bg-blue-50/50 rounded-lg">
            <div class="text-[10px] font-bold uppercase text-blue-600">Total Paid</div>
            <div class="text-base font-bold text-blue-700 mt-0.5">${{ formatCurrency(invoice.paid_usd) }}</div>
          </div>
          <div class="p-3 bg-red-50/50 rounded-lg">
            <div class="text-[10px] font-bold uppercase text-red-600">Outstanding Balance</div>
            <div class="text-base font-black text-red-600 mt-0.5">${{ formatCurrency(invoice.balance) }}</div>
          </div>
        </div>

        <!-- Transaction Records Table -->
        <div class="py-5">
          <h2 class="text-xs font-bold uppercase text-slate-700 tracking-wider mb-3">Transaction Records</h2>
          <table class="w-full text-xs">
            <thead>
              <tr class="text-slate-500 font-semibold border-b border-slate-200 text-left bg-slate-50/60">
                <th class="py-2.5 px-3">Date</th>
                <th class="py-2.5 px-3">Payment Method</th>
                <th class="py-2.5 px-3">Account / Ref</th>
                <th class="py-2.5 px-3">Currency</th>
                <th class="py-2.5 px-3 text-right">Amount ($)</th>
                <th class="py-2.5 px-3 text-right">Amount (៛ KHR)</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
              <tr v-if="!invoice.payments || invoice.payments.length === 0">
                <td colspan="6" class="py-6 text-center text-slate-400 italic">
                  No payment transactions recorded yet.
                </td>
              </tr>
              <tr v-for="p in invoice.payments" :key="p.id" class="font-medium">
                <td class="py-2.5 px-3 text-slate-700">{{ formatDate(p.date) }}</td>
                <td class="py-2.5 px-3 font-semibold text-slate-800">{{ p.payment_method?.name || (p.amount < 0 ? 'Refund' : 'Cash') }}</td>
                <td class="py-2.5 px-3 font-mono text-slate-600">{{ p.txnref || '-' }}</td>
                <td class="py-2.5 px-3 font-semibold text-emerald-700">{{ p.currency || 'USD' }}</td>
                <td class="py-2.5 px-3 text-right font-bold text-slate-900">${{ formatCurrency(p.amount) }}</td>
                <td class="py-2.5 px-3 text-right text-slate-700">{{ formatRiel(p.amount_kh) }}</td>
              </tr>
            </tbody>
            <tfoot class="border-t-2 border-slate-200 font-bold">
              <tr>
                <td colspan="4" class="py-3 px-3 text-right uppercase text-[11px] text-slate-500">TOTAL PAID:</td>
                <td class="py-3 px-3 text-right text-sm text-blue-600">${{ formatCurrency(invoice.paid_usd) }}</td>
                <td class="py-3 px-3 text-right text-sm text-blue-600">{{ formatRiel(invoice.paid_kh) }} ៛</td>
              </tr>
              <tr class="border-t border-slate-100">
                <td colspan="4" class="py-3 px-3 text-right uppercase text-[11px] text-slate-700">OUTSTANDING BALANCE:</td>
                <td colspan="2" class="py-3 px-3 text-right text-base font-black text-red-600">${{ formatCurrency(invoice.balance) }}</td>
              </tr>
            </tfoot>
          </table>
        </div>

        <!-- Signatures & Footer -->
        <div class="mt-8 pt-8 border-t border-slate-200 text-xs">
          <div class="grid grid-cols-2 gap-12 text-center">
            <div>
              <div class="h-16"></div>
              <div class="border-t border-slate-300 pt-2 font-medium text-slate-700">Patient's Signature</div>
            </div>
            <div>
              <div class="h-16"></div>
              <div class="border-t border-slate-300 pt-2 font-medium text-slate-700">Cashier / Authorized Signature</div>
            </div>
          </div>
          <p class="text-center text-[10px] text-slate-400 mt-8">
            Thank you for choosing our dental services. For inquiries, please contact us.
          </p>
        </div>

      </div>

    </div>
  </div>
</template>

<script>
import { Head } from '@inertiajs/vue3';
import dayjs from 'dayjs';

export default {
  components: {
    Head,
  },
  props: {
    invoice: {
      type: Object,
      required: true,
    },
    hospital: {
      type: Object,
      default: null,
    },
  },
  methods: {
    formatDate(date) {
      if (!date) return '-';
      return dayjs(date).format('DD/MM/YYYY');
    },
    formatCurrency(val) {
      return (Number(val) || 0).toLocaleString('en-US', {
        minimumFractionDigits: 2,
        maximumFractionDigits: 2,
      });
    },
    formatRiel(val) {
      return (Number(val) || 0).toLocaleString('en-US');
    },
    statusBadgeClass(status) {
      if (status === 3) return 'bg-emerald-100 text-emerald-800'; // Paid
      if (status === 1) return 'bg-blue-100 text-blue-800'; // Partial
      if (status === 2) return 'bg-amber-100 text-amber-800'; // Unpaid
      if (status === 4) return 'bg-purple-100 text-purple-800'; // Refunded
      if (status === 5) return 'bg-indigo-100 text-indigo-800'; // Partially Refunded
      return 'bg-slate-200 text-slate-700';
    },
    goBack() {
      if (window.history.length > 1) {
        window.history.back();
      } else {
        window.close();
      }
    },
    printNow() {
      window.print();
    },
  },
};
</script>

<style scoped>
@media print {
  body {
    background: white !important;
  }
}
</style>
