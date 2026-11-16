<template>
  <div class="invoice-print-wrapper min-h-screen bg-slate-100 py-6 px-4 print:p-0 print:bg-white text-slate-800">
    <Head :title="`Invoice - ${invoice.number}`" />

    <!-- Top Action Toolbar (Hidden during browser print) -->
    <div class="max-w-4xl mx-auto mb-4 print:hidden">
      <div class="bg-white rounded-xl shadow-xs border border-slate-200 px-5 py-3.5 flex items-center justify-between flex-wrap gap-3">
        <div class="flex items-center gap-2">
          <button
            type="button"
            @click="goBack"
            class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-semibold text-slate-600 bg-slate-100 hover:bg-slate-200 rounded-lg transition"
          >
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
            </svg>
            Back to Edit
          </button>
          <span class="text-xs text-slate-400 font-medium">|</span>
          <span class="text-xs text-slate-500 font-mono font-bold">#{{ invoice.number }}</span>
        </div>

        <div class="flex items-center gap-3">
          <span class="hidden sm:inline-block text-[11px] text-slate-500">
            Tip: In print dialog, choose <strong class="text-slate-700">Save as PDF</strong> to export
          </span>
          <button
            type="button"
            @click="printInvoice"
            class="inline-flex items-center gap-2 px-5 py-2 text-xs font-bold text-white bg-[#0D63D6] hover:bg-[#094bb5] rounded-lg shadow-sm transition"
          >
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4H7v4a2 2 0 002 2zM7 9V5a2 2 0 012-2h6a2 2 0 012 2v4" />
            </svg>
            Print Invoice
          </button>
        </div>
      </div>
    </div>

    <!-- Printable A4 Invoice Sheet -->
    <div id="invoice-sheet" class="a4-sheet max-w-4xl mx-auto bg-white shadow-xl border border-slate-200 p-8 sm:p-12 print:p-0 print:shadow-none print:border-none print:max-w-none print:w-full">
      
      <!-- 1. CLINIC HEADER COMPONENT (Scenario A / Scenario B) -->
      <PrintHeader :clinic="clinic" />

      <!-- 2. INVOICE TITLE & NUMBER AREA -->
      <div class="text-center my-4 relative">
        <!-- Khmer Title in Cyan / Blue -->
        <div class="font-moul text-lg sm:text-xl font-bold text-[#0284c7] tracking-wider mb-1">
          វិក្កយបត្រ
        </div>

        <!-- English Title & Invoice Reference -->
        <div class="flex items-center justify-between mt-1 text-xs">
          <!-- Left spacer -->
          <div class="w-1/4"></div>

          <!-- Centered INVOICE -->
          <div class="w-1/2 text-center">
            <span class="font-black text-sm uppercase tracking-widest border-b-2 border-slate-900 pb-0.5 inline-block">
              INVOICE
            </span>
          </div>

          <!-- Right: Invoice Number & Quotation Number -->
          <div class="w-1/4 text-right space-x-3 font-sans text-xs">
            <span class="font-bold text-slate-900">
              N°. <span class="font-mono font-extrabold">{{ invoice.number }}</span>
            </span>
            <span v-if="invoice.quotation_number" class="font-bold text-slate-900">
              N°. <span class="font-mono font-extrabold">{{ invoice.quotation_number }}</span>
            </span>
          </div>
        </div>
      </div>

      <!-- 3. PATIENT & DOCTOR INFORMATION GRID -->
      <div class="my-5 text-xs text-slate-800 leading-relaxed font-sans">
        <div class="grid grid-cols-12 gap-y-1.5">
          <!-- Row 1: Doctor's Name (Left) | Phone Number (Right) -->
          <div class="col-span-7 flex items-baseline">
            <span class="font-bold w-32 flex-shrink-0">Doctor's Name :</span>
            <span class="font-semibold text-slate-900">{{ invoice.doctor.name || '-' }}</span>
          </div>
          <div class="col-span-5 flex items-baseline justify-end">
            <span class="font-bold mr-1.5">Phone Number :</span>
            <span class="font-mono font-semibold text-slate-900">{{ invoice.doctor.phone || '-' }}</span>
          </div>

          <!-- Row 2: Patient's Name (Left) | Sex, Age, ID (Right) -->
          <div class="col-span-7 flex items-baseline">
            <span class="font-bold w-32 flex-shrink-0">Patient's Name :</span>
            <span class="font-bold text-slate-900">{{ invoice.patient.name || '-' }}</span>
          </div>
          <div class="col-span-5 flex items-baseline justify-end space-x-4">
            <div>
              <span class="font-bold mr-1">Sex :</span>
              <span class="font-semibold text-slate-900 uppercase">{{ invoice.patient.sex || '-' }}</span>
            </div>
            <div>
              <span class="font-bold mr-1">Age :</span>
              <span class="font-semibold text-slate-900">{{ invoice.patient.age || '-' }}</span>
            </div>
            <div>
              <span class="font-bold mr-1">ID:</span>
              <span class="font-mono font-bold text-slate-900">{{ invoice.patient.id || '-' }}</span>
            </div>
          </div>

          <!-- Row 3: Phone Number (Patient) -->
          <div class="col-span-12 flex items-baseline">
            <span class="font-bold w-32 flex-shrink-0">Phone Number :</span>
            <span class="font-mono text-slate-900">{{ invoice.patient.phone || '(+855)' }}</span>
          </div>

          <!-- Row 4: Address (Patient) -->
          <div class="col-span-12 flex items-baseline">
            <span class="font-bold w-32 flex-shrink-0">Address :</span>
            <span class="text-slate-900">{{ invoice.patient.address || 'Phnom Penh' }}</span>
          </div>
        </div>
      </div>

      <!-- 4. INVOICE SERVICE DETAILS TABLE -->
      <div class="my-6">
        <table class="w-full border-collapse border border-slate-300 text-xs">
          <thead>
            <tr class="bg-slate-50/80 font-bold text-slate-900 text-center border-b border-slate-300">
              <th class="py-2.5 px-2 border-r border-slate-300 w-10">N</th>
              <th class="py-2.5 px-3 border-r border-slate-300 text-left">Type of Treatment</th>
              <th class="py-2.5 px-2 border-r border-slate-300 w-14">QTY</th>
              <th class="py-2.5 px-2.5 border-r border-slate-300 text-right w-20">Price ($)</th>
              <th class="py-2.5 px-2 border-r border-slate-300 text-right w-20">Disc(%)</th>
              <th class="py-2.5 px-2 border-r border-slate-300 text-right w-20">Disc($)</th>
              <th class="py-2.5 px-3 text-right w-24">Amount ($)</th>
            </tr>
          </thead>
          <tbody>
            <!-- Line Items -->
            <tr
              v-for="item in invoice.items"
              :key="item.n"
              class="border-b border-slate-300 hover:bg-slate-50/50 print:hover:bg-transparent page-break-inside-avoid"
            >
              <td class="py-2 px-2 text-center border-r border-slate-300 font-medium text-slate-700">
                {{ item.n }}
              </td>
              <td class="py-2 px-3 border-r border-slate-300 text-left font-medium font-khmer text-slate-900">
                {{ item.name }}
              </td>
              <td class="py-2 px-2 text-center border-r border-slate-300 font-mono text-slate-800">
                {{ item.qty }}
              </td>
              <td class="py-2 px-2.5 text-right border-r border-slate-300 font-mono text-slate-800">
                {{ formatNumber(item.price) }}
              </td>
              <td class="py-2 px-2 text-right border-r border-slate-300 font-mono text-slate-800">
                {{ formatDecimals(item.disc_percent) }}
              </td>
              <td class="py-2 px-2 text-right border-r border-slate-300 font-mono text-slate-800">
                {{ formatDiscountDollars(item.disc_amount) }}
              </td>
              <td class="py-2 px-3 text-right font-mono font-medium text-slate-900">
                {{ formatNumber(item.amount) }}
              </td>
            </tr>

            <!-- Fallback if no items -->
            <tr v-if="!invoice.items || invoice.items.length === 0">
              <td colspan="7" class="py-4 text-center text-slate-400 italic">
                No services or treatments recorded on this invoice.
              </td>
            </tr>

            <!-- 5. FINANCIAL SUMMARY ROWS (Matching Reference PDF) -->
            <!-- Sub-Total ($) -->
            <tr class="border-b border-slate-300 page-break-inside-avoid">
              <td colspan="6" class="py-1.5 px-3 text-right font-bold text-slate-800 border-r border-slate-300">
                Sub-Total ($)
              </td>
              <td class="py-1.5 px-3 text-right font-mono font-semibold text-slate-900">
                {{ formatCurrency(invoice.subtotal) }}
              </td>
            </tr>

            <!-- Discount ($) -->
            <tr class="border-b border-slate-300 page-break-inside-avoid">
              <td colspan="6" class="py-1.5 px-3 text-right font-bold text-slate-800 border-r border-slate-300">
                Discount ($)
              </td>
              <td class="py-1.5 px-3 text-right font-mono font-semibold text-slate-900">
                {{ formatCurrency(invoice.discount) }}
              </td>
            </tr>

            <!-- Total ($) -->
            <tr class="border-b border-slate-300 page-break-inside-avoid bg-slate-50/40">
              <td colspan="6" class="py-2 px-3 text-right font-extrabold text-slate-900 border-r border-slate-300">
                Total ($)
              </td>
              <td class="py-2 px-3 text-right font-mono font-extrabold text-slate-900">
                {{ formatCurrency(invoice.total) }}
              </td>
            </tr>

            <!-- Paid to date ($) -->
            <tr class="border-b border-slate-300 page-break-inside-avoid">
              <td colspan="6" class="py-1.5 px-3 text-right font-bold text-slate-800 border-r border-slate-300">
                Paid to date ($)
              </td>
              <td class="py-1.5 px-3 text-right font-mono font-semibold text-slate-900">
                {{ formatCurrency(invoice.paid_usd) }}
              </td>
            </tr>

            <!-- Paid to date (៛) -->
            <tr class="border-b border-slate-300 page-break-inside-avoid">
              <td colspan="6" class="py-1.5 px-3 text-right font-bold text-slate-800 border-r border-slate-300 font-khmer">
                Paid to date (៛)
              </td>
              <td class="py-1.5 px-3 text-right font-mono font-semibold text-slate-900">
                {{ formatRiel(invoice.paid_kh) }}
              </td>
            </tr>

            <!-- Balance ($) -->
            <tr class="border-b border-slate-300 page-break-inside-avoid bg-slate-50/40">
              <td colspan="6" class="py-2 px-3 text-right font-extrabold text-slate-900 border-r border-slate-300">
                Balance ($)
              </td>
              <td class="py-2 px-3 text-right font-mono font-extrabold text-slate-900">
                {{ formatCurrency(invoice.balance_usd) }}
              </td>
            </tr>

            <!-- Balance (៛) -->
            <tr class="border-b border-slate-300 page-break-inside-avoid">
              <td colspan="6" class="py-1.5 px-3 text-right font-bold text-slate-800 border-r border-slate-300 font-khmer">
                Balance (៛)
              </td>
              <td class="py-1.5 px-3 text-right font-mono font-bold text-slate-900">
                {{ formatRiel(invoice.balance_kh) }}
              </td>
            </tr>
          </tbody>
        </table>
      </div>

      <!-- 6. FOOTER COMPONENT (Policy Notes, Cashier Signature & Clinic Contact) -->
      <PrintFooter
        :clinic="clinic"
        :printed-at="invoice.printed_at"
        signer-title="អ្នកទទួលប្រាក់ / Cashier"
        :signer-name="invoice.cashier_name || 'fwta owner'"
      />

    </div>
  </div>
</template>

<script>
import { Head } from "@inertiajs/inertia-vue3";
import PrintHeader from "@/Components/Tenant/PrintHeader.vue";
import PrintFooter from "@/Components/Tenant/PrintFooter.vue";

export default {
  components: {
    Head,
    PrintHeader,
    PrintFooter,
  },
  props: {
    invoice: {
      type: Object,
      required: true,
    },
    clinic: {
      type: Object,
      required: true,
    },
  },
  methods: {
    printInvoice() {
      window.print();
    },
    goBack() {
      if (window.history.length > 1) {
        window.history.back();
      } else {
        window.close();
      }
    },
    formatCurrency(val) {
      return (Number(val) || 0).toLocaleString("en-US", {
        minimumFractionDigits: 2,
        maximumFractionDigits: 2,
      });
    },
    formatDecimals(val) {
      return (Number(val) || 0).toLocaleString("en-US", {
        minimumFractionDigits: 2,
        maximumFractionDigits: 2,
      });
    },
    formatDiscountDollars(val) {
      const num = Number(val) || 0;
      return num === 0 ? "0" : num.toLocaleString("en-US", { minimumFractionDigits: 0, maximumFractionDigits: 2 });
    },
    formatNumber(val) {
      const num = Number(val) || 0;
      return num % 1 === 0 ? String(num) : num.toLocaleString("en-US", { minimumFractionDigits: 2, maximumFractionDigits: 2 });
    },
    formatRiel(val) {
      return Math.round(Number(val) || 0).toLocaleString("en-US");
    },
  },
};
</script>

<style scoped>
@import url('https://fonts.googleapis.com/css2?family=Moul&display=swap');
@import url('https://fonts.googleapis.com/css2?family=Battambang:wght@400;700;900&family=Content:wght@400;700&display=swap');

.font-moul {
  font-family: 'Moul', cursive, serif;
}

.font-khmer {
  font-family: 'Battambang', 'Content', sans-serif;
}

.page-break-inside-avoid {
  break-inside: avoid;
  page-break-inside: avoid;
}

/* Print Specific Layout & Styles */
@media print {
  @page {
    size: A4 portrait;
    margin: 12mm 15mm 15mm 15mm;
  }

  body, html {
    background-color: #ffffff !important;
    color: #000000 !important;
    margin: 0 !important;
    padding: 0 !important;
    -webkit-print-color-adjust: exact !important;
    print-color-adjust: exact !important;
  }

  .invoice-print-wrapper {
    background: #ffffff !important;
    padding: 0 !important;
    margin: 0 !important;
    min-height: auto !important;
  }

  #invoice-sheet {
    box-shadow: none !important;
    border: none !important;
    padding: 0 !important;
    margin: 0 !important;
    max-width: 100% !important;
    width: 100% !important;
  }

  thead {
    display: table-header-group;
  }

  tr {
    break-inside: avoid;
    page-break-inside: avoid;
  }
}
</style>
