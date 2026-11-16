<template>
  <TenantLayout>
    <Head :title="`Invoice #${invoice.invoice_number_formatted || invoice.invoice_number}`" />
    <div class="px-4 py-4 max-w-5xl mx-auto">
      <jet-banner />

      <!-- Breadcrumb -->
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
              <Link :href="route('invoices.index')" class="ml-1 text-sm font-medium text-gray-700 hover:text-gray-900 md:ml-2">
                {{ tm('Invoices') || 'Invoices' }}
              </Link>
            </div>
          </li>
          <li>
            <div class="flex items-center">
              <svg class="w-6 h-6 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path>
              </svg>
              <span class="ml-1 text-sm font-medium text-gray-500 md:ml-2">
                #{{ invoice.invoice_number_formatted || invoice.invoice_number }}
              </span>
            </div>
          </li>
        </ol>
      </nav>

      <!-- Invoice Sheet -->
      <div class="bg-white dark:bg-darker rounded-xl shadow-xs border border-slate-200 dark:border-slate-700 p-8">
        <!-- Top bar with actions -->
        <div class="flex items-center justify-between pb-6 border-b border-slate-200 dark:border-slate-700">
          <div>
            <span class="text-xs font-bold uppercase tracking-wider text-slate-400">Dental Clinic Invoice</span>
            <h1 class="text-2xl font-black font-mono text-slate-800 dark:text-white mt-1">
              {{ invoice.invoice_number_formatted || invoice.invoice_number }}
            </h1>
            <div class="mt-2">
              <span
                :class="[
                  'inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold uppercase tracking-wider',
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
            </div>
          </div>

          <div class="flex items-center space-x-2">
            <a
              :href="route('invoices.print', invoice.id)"
              target="_blank"
              class="inline-flex items-center gap-1.5 px-3.5 py-2 border border-slate-300 text-slate-700 hover:bg-slate-50 text-xs font-bold rounded-lg transition"
            >
              <svg class="w-4 h-4 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4H7v4a2 2 0 002 2zM7 9V5a2 2 0 012-2h6a2 2 0 012 2v4" />
              </svg>
              <span>{{ tm('Print_Invoice') || 'Print Invoice' }}</span>
            </a>
            <Link
              v-if="can(['all', 'invoice:edit', 'invoice:save'])"
              :href="route('invoices.edit', invoice.id)"
              class="px-4 py-2 bg-primary hover:bg-primary-dark text-white text-xs font-bold rounded-lg transition"
            >
              Edit Invoice
            </Link>
          </div>
        </div>

        <!-- Meta Info Grid -->
        <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 py-6 border-b border-slate-100 dark:border-slate-700 text-xs">
          <div>
            <span class="block text-slate-400 font-semibold mb-0.5">Patient</span>
            <span class="font-bold text-slate-800 dark:text-white text-sm">
              {{ invoice.patient_name || (invoice.patient?.firstname + ' ' + invoice.patient?.lastname) }}
            </span>
            <span class="block text-slate-500 font-mono mt-0.5">
              ID: #{{ invoice.patient?.id_number || invoice.patient?.id }}
            </span>
          </div>

          <div>
            <span class="block text-slate-400 font-semibold mb-0.5">Attending Doctor</span>
            <span class="font-bold text-slate-800 dark:text-white">
              {{ invoice.doctor_name || 'N/A' }}
            </span>
          </div>

          <div>
            <span class="block text-slate-400 font-semibold mb-0.5">Daily Date</span>
            <span class="font-mono text-slate-800 dark:text-white">
              {{ formatDate(invoice.date) }}
            </span>
          </div>

          <div>
            <span class="block text-slate-400 font-semibold mb-0.5">Exchange Rate</span>
            <span class="font-mono text-slate-800 dark:text-white">
              1$ = {{ invoice.rate || 4050 }} ៛
            </span>
          </div>
        </div>

        <!-- Detail Table -->
        <div class="mt-6 overflow-x-auto">
          <table class="w-full text-left text-xs border-collapse">
            <thead>
              <tr class="border-b border-slate-200 dark:border-slate-700 text-slate-500 font-bold">
                <th class="py-2.5 px-3">#</th>
                <th class="py-2.5 px-3">Operate Date</th>
                <th class="py-2.5 px-3">Service / Product</th>
                <th class="py-2.5 px-3">Teeth</th>
                <th class="py-2.5 px-3 text-center">Qty</th>
                <th class="py-2.5 px-3 text-right">Fee ($)</th>
                <th class="py-2.5 px-3 text-center">Discount</th>
                <th class="py-2.5 px-3 text-right">Amount ($)</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-slate-100 dark:divide-slate-700">
              <tr v-for="(detail, i) in invoice.medical_details" :key="detail.id">
                <td class="py-2.5 px-3 font-mono text-slate-400">{{ i + 1 }}</td>
                <td class="py-2.5 px-3 font-mono">{{ formatDate(detail.date) }}</td>
                <td class="py-2.5 px-3 font-semibold text-slate-800 dark:text-white">
                  {{ detail.detailable?.name || detail.item_name || 'Item #' + detail.detailable_id }}
                </td>
                <td class="py-2.5 px-3">
                  <div class="flex flex-wrap gap-1">
                    <span
                      v-for="t in detail.treatment_teeth"
                      :key="t.id"
                      class="px-1.5 py-0.5 text-[10px] font-mono font-bold bg-blue-100 text-blue-800 rounded"
                    >
                      {{ t.teeth_number }}
                    </span>
                    <span v-if="!detail.treatment_teeth || !detail.treatment_teeth.length" class="text-slate-400 italic">-</span>
                  </div>
                </td>
                <td class="py-2.5 px-3 text-center font-mono">{{ detail.quantity }}</td>
                <td class="py-2.5 px-3 text-right font-mono">${{ (detail.unit_price || 0).toFixed(2) }}</td>
                <td class="py-2.5 px-3 text-center font-mono">
                  {{ detail.discount ? `${detail.discount}${detail.discount_as || '%'}` : '-' }}
                </td>
                <td class="py-2.5 px-3 text-right font-mono font-bold">${{ (detail.amount || 0).toFixed(2) }}</td>
              </tr>
            </tbody>
          </table>
        </div>

        <!-- Financial Summary -->
        <div class="flex justify-end mt-6 pt-4 border-t border-slate-200 dark:border-slate-700">
          <div class="w-80 text-xs border border-slate-200 dark:border-slate-700 rounded-lg overflow-hidden divide-y divide-slate-100 dark:divide-slate-700 shadow-2xs">
            <div v-if="totals.discount > 0" class="flex justify-between px-4 py-2 bg-white dark:bg-slate-800">
              <span class="text-slate-600 dark:text-slate-300">Sub-Total($)</span>
              <span class="font-mono font-bold text-slate-800 dark:text-white">
                ${{ totals.subtotal.toFixed(2) }}
              </span>
            </div>
            <div v-if="totals.discount > 0" class="flex justify-between px-4 py-2 bg-white dark:bg-slate-800">
              <span class="text-slate-600 dark:text-slate-300">Discount($)</span>
              <span class="font-mono font-bold text-rose-600">
                -${{ totals.discount.toFixed(2) }}
              </span>
            </div>
            <div class="flex justify-between px-4 py-2 bg-white dark:bg-slate-800">
              <span class="text-slate-600 dark:text-slate-300">Total($)</span>
              <span class="font-mono font-bold text-slate-800 dark:text-white">
                ${{ totals.total.toFixed(2) }}
              </span>
            </div>
            <div class="flex justify-between px-4 py-2 bg-white dark:bg-slate-800">
              <span class="text-slate-600 dark:text-slate-300">Paid to date($)</span>
              <span class="font-mono text-slate-800 dark:text-white">
                ${{ totals.paidUsd.toFixed(2) }}
              </span>
            </div>
            <div class="flex justify-between px-4 py-2 bg-white dark:bg-slate-800">
              <span class="text-slate-600 dark:text-slate-300">Paid to date(៛)</span>
              <span class="font-mono text-slate-800 dark:text-white">
                {{ totals.paidKh.toLocaleString() }} ៛
              </span>
            </div>
            <div class="flex justify-between px-4 py-2 bg-white dark:bg-slate-800">
              <span class="text-slate-600 dark:text-slate-300">Refunded($)</span>
              <span class="font-mono text-slate-800 dark:text-white">
                ${{ totals.refunded.toFixed(2) }}
              </span>
            </div>
            <div class="flex justify-between px-4 py-2 bg-[#8da5e0] text-white font-bold">
              <span>Balance($)</span>
              <span class="font-mono text-sm">
                ${{ totals.balance.toFixed(2) }}
              </span>
            </div>
            <div class="flex justify-between px-4 py-2 bg-white dark:bg-slate-800">
              <span class="text-slate-600 dark:text-slate-300">Balance(៛)</span>
              <span class="font-mono text-slate-800 dark:text-white">
                {{ totals.balanceKh.toLocaleString() }} ៛
              </span>
            </div>
          </div>
        </div>
      </div>
    </div>
  </TenantLayout>
</template>

<script>
import TenantLayout from "@/Layouts/TenantLayout.vue";
import { Head, Link } from "@inertiajs/inertia-vue3";
import JetBanner from "@/Jetstream/Banner.vue";

export default {
  components: {
    TenantLayout,
    Head,
    Link,
    JetBanner,
  },
  props: {
    invoice: {
      type: Object,
      required: true,
    },
  },
  computed: {
    totals() {
      // 1. Calculate subtotal from line items
      let subtotal = 0;
      (this.invoice.medical_details || []).forEach((d) => {
        const qty = Number(d.quantity) || 1;
        const unitPrice = Number(d.unit_price) || 0;
        const gross = qty * unitPrice;
        const disc = Number(d.discount) || 0;
        const discAs = d.discount_as || "%";
        let rowDisc = 0;
        if (discAs === "%") {
          rowDisc = gross * (disc / 100);
        } else {
          rowDisc = disc;
        }
        rowDisc = Math.min(gross, Math.max(0, rowDisc));
        const amount = d.amount !== undefined && d.amount !== null && Number(d.amount) > 0 
          ? Number(d.amount) 
          : Math.max(0, gross - rowDisc);
        subtotal += amount;
      });

      // 2. Overall discount
      const overallDiscVal = Number(this.invoice.discount_invoice) || 0;
      let overallDiscountAmount = 0;
      if (this.invoice.discount_inv_as === "%") {
        overallDiscountAmount = subtotal * (overallDiscVal / 100);
      } else {
        overallDiscountAmount = overallDiscVal;
      }
      overallDiscountAmount = Math.min(subtotal, Math.max(0, overallDiscountAmount));

      // Total: use invoice.total if > 0, else calculated total
      const total = Number(this.invoice.total) > 0 
        ? Number(this.invoice.total) 
        : Math.max(0, subtotal - overallDiscountAmount);

      const rate = Number(this.invoice.rate) || 4050;

      // 3. Payments & Refunds
      let paidUsd = Number(this.invoice.paid_usd) || 0;
      let refunded = Number(this.invoice.refunded) || 0;

      if (this.invoice.payments && this.invoice.payments.length > 0) {
        let pUsd = 0;
        let pRef = 0;
        this.invoice.payments.forEach((p) => {
          const amt = Number(p.amount) || 0;
          if (amt > 0) pUsd += amt;
          else if (amt < 0) pRef += Math.abs(amt);
        });
        paidUsd = pUsd;
        refunded = pRef;
      }

      let paidKh = Number(this.invoice.paid_kh) > 0 
        ? Number(this.invoice.paid_kh) 
        : Math.round(paidUsd * rate);

      // Balance
      const balance = Number(this.invoice.balance) > 0 && Math.abs(Number(this.invoice.balance) - (total - paidUsd + refunded)) < 0.01
        ? Number(this.invoice.balance)
        : Math.max(0, total - paidUsd + refunded);

      const balanceKh = Math.round(balance * rate);

      return {
        subtotal,
        discount: overallDiscountAmount,
        total,
        paidUsd,
        paidKh,
        refunded,
        balance,
        balanceKh,
      };
    },
  },
  methods: {
    formatDate(d) {
      if (!d) return "-";
      return new Date(d).toLocaleDateString();
    },
  },
};
</script>
