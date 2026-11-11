<template>
  <div class="min-h-screen bg-slate-100 py-8 px-4 print:p-0 print:bg-white text-slate-800">
    <Head :title="`Receipt - ${payment.invoice_number || 'Payment #' + payment.id}`" />

    <!-- Receipt Container -->
    <div class="max-w-2xl mx-auto bg-white rounded-xl shadow-lg border border-slate-200 p-8 print:p-4 print:shadow-none print:border-none print:max-w-none">
      
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
          Back to Payments
        </button>

        <button
          type="button"
          @click="printNow"
          class="inline-flex items-center gap-1.5 px-4 py-2 text-xs font-bold text-white bg-primary hover:bg-primary-dark rounded-lg shadow-sm transition"
        >
          <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4H7v4a2 2 0 002 2zM7 9V5a2 2 0 012-2h6a2 2 0 012 2v4" />
          </svg>
          Print Receipt
        </button>
      </div>

      <!-- Printable Area -->
      <div id="receipt-print-area">
        <!-- Header -->
        <div class="text-center pb-6 border-b border-slate-200">
          <h1 class="text-2xl font-black tracking-tight text-slate-900 uppercase">
            {{ hospital?.name || 'OdontoCare Dental Clinic' }}
          </h1>
          <p class="text-xs text-slate-500 mt-1">
            {{ hospital?.address || 'Phnom Penh, Cambodia' }} &middot; Phone: {{ hospital?.phone || '(+855) 23 888 999' }}
          </p>
          <div class="inline-block mt-3 px-3 py-1 bg-slate-100 text-slate-800 rounded-full text-xs font-bold uppercase tracking-wider">
            Official Payment Receipt
          </div>
        </div>

        <!-- Receipt Meta Grid -->
        <div class="grid grid-cols-2 gap-4 py-5 text-xs border-b border-slate-100">
          <div>
            <div class="text-slate-500 font-semibold uppercase text-[10px] tracking-wider">Patient Details</div>
            <div class="font-bold text-slate-900 text-sm mt-0.5">{{ payment.patient_name }}</div>
            <div class="text-slate-600 font-mono">Patient ID: #{{ payment.patient?.id_number || payment.patient_id }}</div>
            <div v-if="payment.patient?.phone" class="text-slate-600">Phone: {{ payment.patient.phone }}</div>
          </div>

          <div class="text-right">
            <div class="text-slate-500 font-semibold uppercase text-[10px] tracking-wider">Receipt Details</div>
            <div class="font-mono font-bold text-slate-900 text-sm mt-0.5">REC-{{ String(payment.id).padStart(6, '0') }}</div>
            <div class="text-slate-600">Date: {{ formatDate(payment.date) }}</div>
            <div class="font-mono font-bold text-primary">Invoice: {{ payment.invoice_number }}</div>
          </div>
        </div>

        <!-- Payment Info Table -->
        <div class="py-5 border-b border-slate-200">
          <table class="w-full text-xs">
            <thead>
              <tr class="text-slate-500 font-semibold border-b border-slate-200 text-left">
                <th class="pb-2">Description</th>
                <th class="pb-2">Payment Method</th>
                <th class="pb-2">txnref</th>
                <th class="pb-2 text-right">Amount ($)</th>
                <th class="pb-2 text-right">Amount (៛)</th>
              </tr>
            </thead>
            <tbody>
              <tr class="border-b border-slate-100 font-medium">
                <td class="py-3">Payment for Invoice {{ payment.invoice_number }}</td>
                <td class="py-3 font-semibold">{{ payment.payment_method_name }}</td>
                <td class="py-3 font-mono text-slate-600">{{ payment.txnref || '-' }}</td>
                <td class="py-3 text-right font-mono font-bold">${{ parseFloat(payment.amount || 0).toFixed(2) }}</td>
                <td class="py-3 text-right font-mono font-bold">{{ Math.round(payment.amount_kh || 0).toLocaleString() }} ៛</td>
              </tr>
            </tbody>
          </table>
        </div>

        <!-- Total Summary Box -->
        <div class="flex justify-end py-5 border-b border-slate-200">
          <div class="w-64 space-y-1.5 text-xs">
            <div class="flex justify-between text-slate-600">
              <span>Paid Currency:</span>
              <span class="font-bold text-slate-900 uppercase">{{ payment.currency || 'USD' }}</span>
            </div>
            <div class="flex justify-between text-slate-600">
              <span>Status:</span>
              <span class="font-bold text-emerald-600 capitalize">{{ payment.status || 'Completed' }}</span>
            </div>
            <div class="flex justify-between text-sm font-black pt-2 border-t border-slate-200 text-slate-900">
              <span>Total Paid:</span>
              <span class="font-mono">${{ parseFloat(payment.amount || 0).toFixed(2) }}</span>
            </div>
            <div class="flex justify-between text-xs text-slate-500 font-mono">
              <span>Equivalent (KHR):</span>
              <span>{{ Math.round(payment.amount_kh || 0).toLocaleString() }} ៛</span>
            </div>
            <div v-if="payment.medical_history" class="flex justify-between text-xs pt-1 border-t border-slate-100 text-blue-700 font-bold">
              <span>Remaining Balance:</span>
              <span class="font-mono">${{ parseFloat(payment.medical_history.balance || 0).toFixed(2) }}</span>
            </div>
          </div>
        </div>

        <!-- Footer / Signatures -->
        <div class="pt-8">
          <div class="flex justify-between text-xs text-center text-slate-600">
            <div class="w-44">
              <div class="h-14 border-b border-slate-300"></div>
              <div class="mt-1 font-semibold">Patient / Payer</div>
            </div>
            <div class="w-44">
              <div class="h-14 border-b border-slate-300"></div>
              <div class="mt-1 font-semibold">
                Cashier / Verified By:
                <div class="font-bold text-slate-900">{{ payment.verified_by_name }}</div>
              </div>
            </div>
          </div>

          <div class="text-center text-[10px] text-slate-400 mt-8">
            Thank you for trusting our dental care services &middot; Keep this receipt for your records.
          </div>
        </div>
      </div>

    </div>
  </div>
</template>

<script>
import { Head } from '@inertiajs/inertia-vue3'

export default {
  components: {
    Head,
  },
  props: {
    payment: {
      type: Object,
      required: true,
    },
    hospital: {
      type: Object,
      default: null,
    },
  },
  mounted() {
    // If URL has ?auto_print=1, automatically trigger print
    const urlParams = new URLSearchParams(window.location.search);
    if (urlParams.get('auto_print') === '1') {
      setTimeout(() => {
        window.print();
      }, 400);
    }
  },
  methods: {
    formatDate(dateStr) {
      if (!dateStr) return '-';
      const d = new Date(dateStr);
      return d.toLocaleDateString('en-US', {
        year: 'numeric',
        month: 'short',
        day: 'numeric',
        hour: '2-digit',
        minute: '2-digit',
      });
    },
    printNow() {
      window.print();
    },
    goBack() {
      if (window.history.length > 1) {
        window.history.back();
      } else {
        this.$inertia.visit(route('tenant.payments.index'));
      }
    },
  },
}
</script>

<style>
@media print {
  body {
    background: #ffffff !important;
  }
  @page {
    margin: 1cm;
  }
}
</style>
