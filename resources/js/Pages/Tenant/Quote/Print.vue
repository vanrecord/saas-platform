<template>
  <div class="quote-print-wrapper min-h-screen bg-slate-100 py-6 px-4 print:p-0 print:bg-white text-slate-800">
    <Head :title="`Quotation - ${quote.number}`" />

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
            Back
          </button>
          <span class="text-xs text-slate-400 font-medium">|</span>
          <span class="text-xs text-slate-500 font-mono font-bold">{{ quote.number }}</span>
        </div>

        <div class="flex items-center gap-3">
          <span class="hidden sm:inline-block text-[11px] text-slate-500">
            Tip: In print dialog, choose <strong class="text-slate-700">Save as PDF</strong> to export
          </span>
          <button
            type="button"
            @click="printQuote"
            class="inline-flex items-center gap-2 px-5 py-2 text-xs font-bold text-white bg-[#0D63D6] hover:bg-[#094bb5] rounded-lg shadow-sm transition cursor-pointer"
          >
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4H7v4a2 2 0 002 2zM7 9V5a2 2 0 012-2h6a2 2 0 012 2v4" />
            </svg>
            Print Quotation
          </button>
        </div>
      </div>
    </div>

    <!-- Printable A4 Quotation Sheet (Matches PDF Reference Layout & Spacing) -->
    <div id="quote-sheet" class="a4-sheet max-w-4xl mx-auto bg-white shadow-xl border border-slate-200 p-8 sm:p-12 print:p-0 print:shadow-none print:border-none print:max-w-none print:w-full">
      
      <!-- 1. CLINIC HEADER COMPONENT -->
      <PrintHeader :clinic="clinic" />

      <!-- 2. QUOTATION TITLE & NUMBER AREA -->
      <div class="text-center my-6 relative">
        <h2 class="font-bold text-base uppercase tracking-widest border-b-2 border-slate-900 pb-0.5 inline-block">
          QUOTATION
        </h2>

        <!-- Right: Quote Number (e.g. N°. QUT000063) -->
        <div class="text-right text-xs mt-1">
          <span class="font-bold text-slate-900">
            N°. <span class="font-mono font-extrabold text-slate-900">{{ quote.number }}</span>
          </span>
        </div>
      </div>

      <!-- 3. PATIENT & DOCTOR INFORMATION GRID (Matching PDF exactly) -->
      <div class="my-5 text-xs text-slate-800 leading-relaxed font-sans">
        <div class="grid grid-cols-12 gap-y-1.5">
          <!-- Row 1: Doctor's Name (Left) | Phone Number (Right) -->
          <div class="col-span-7 flex items-baseline">
            <span class="font-bold w-32 flex-shrink-0">Doctor's Name :</span>
            <span class="font-semibold text-slate-900">{{ quote.doctor.name || '-' }}</span>
          </div>
          <div class="col-span-5 flex items-baseline justify-end">
            <span class="font-bold mr-1.5">Phone Number :</span>
            <span class="font-mono font-semibold text-slate-900">{{ quote.doctor.phone || '-' }}</span>
          </div>

          <!-- Row 2: Patient's Name (Left) | Sex, Age, ID (Right) -->
          <div class="col-span-7 flex items-baseline">
            <span class="font-bold w-32 flex-shrink-0">Patient's Name :</span>
            <span class="font-bold text-slate-900">{{ quote.patient.name || '-' }}</span>
          </div>
          <div class="col-span-5 flex items-baseline justify-end space-x-3">
            <div>
              <span class="font-bold mr-1">Sex :</span>
              <span class="font-semibold text-slate-900">{{ quote.patient.sex || 'M' }}</span>
            </div>
            <div>
              <span class="font-bold mr-1">Age :</span>
              <span class="font-semibold text-slate-900">{{ quote.patient.age || '-' }}</span>
            </div>
            <div>
              <span class="font-bold mr-1">ID:</span>
              <span class="font-mono font-bold text-slate-900">{{ quote.patient.id || '-' }}</span>
            </div>
          </div>

          <!-- Row 3: Phone Number (Left) | Profession (Right) -->
          <div class="col-span-7 flex items-baseline">
            <span class="font-bold w-32 flex-shrink-0">Phone Number :</span>
            <span class="font-mono font-semibold text-slate-900">{{ quote.patient.phone || '-' }}</span>
          </div>
          <div class="col-span-5 flex items-baseline justify-end">
            <span class="font-bold mr-1.5">Profession :</span>
            <span class="font-semibold text-slate-900">{{ quote.patient.profession || '-' }}</span>
          </div>

          <!-- Row 4: Address -->
          <div class="col-span-12 flex items-baseline">
            <span class="font-bold w-32 flex-shrink-0">Address :</span>
            <span class="font-normal text-slate-700">{{ quote.patient.address || '-' }}</span>
          </div>
        </div>
      </div>

      <!-- 4. TREATMENT ITEMS TABLE (No "Other" column as explicitly required) -->
      <div class="my-6">
        <table class="w-full text-xs border border-slate-300 font-sans border-collapse">
          <thead>
            <tr class="bg-slate-50 border-b border-slate-300 font-bold text-slate-800">
              <th class="py-2 px-2 text-center w-12 border-r border-slate-300">N°</th>
              <th class="py-2 px-3 text-left border-r border-slate-300">Type of Treatment</th>
              <th class="py-2 px-2 text-center w-16 border-r border-slate-300">QTY</th>
              <th class="py-2 px-3 text-right w-24 border-r border-slate-300">Price ($)</th>
              <th class="py-2 px-2 text-center w-20 border-r border-slate-300">Disc(%)</th>
              <th class="py-2 px-2 text-center w-20 border-r border-slate-300">Disc($)</th>
              <th class="py-2 px-3 text-right w-28">Amount ($)</th>
            </tr>
          </thead>
          <tbody>
            <tr
              v-for="item in quote.items"
              :key="item.n"
              class="border-b border-slate-200"
            >
              <td class="py-2 px-2 text-center font-mono border-r border-slate-300">{{ item.n }}</td>
              <td class="py-2 px-3 font-semibold text-slate-900 border-r border-slate-300 font-khmer">
                {{ item.name }}
              </td>
              <td class="py-2 px-2 text-center font-mono border-r border-slate-300">{{ item.qty }}</td>
              <td class="py-2 px-3 text-right font-mono border-r border-slate-300">
                {{ formatNumber(item.price) }}
              </td>
              <td class="py-2 px-2 text-center font-mono border-r border-slate-300">
                {{ formatDecimals(item.disc_percent) }}
              </td>
              <td class="py-2 px-2 text-center font-mono border-r border-slate-300">
                {{ formatDiscountDollars(item.disc_amount) }}
              </td>
              <td class="py-2 px-3 text-right font-mono font-bold text-slate-900">
                {{ formatNumber(item.amount) }}
              </td>
            </tr>

            <!-- Sub-Total ($) -->
            <tr class="border-b border-slate-300">
              <td colspan="6" class="py-2 px-3 text-right font-bold text-slate-800 border-r border-slate-300">
                Sub-Total ($)
              </td>
              <td class="py-2 px-3 text-right font-mono font-bold text-slate-900">
                {{ formatCurrency(quote.subtotal) }}
              </td>
            </tr>

            <!-- Discount ($) -->
            <tr class="border-b border-slate-300">
              <td colspan="6" class="py-2 px-3 text-right font-bold text-slate-800 border-r border-slate-300">
                Discount ($)
              </td>
              <td class="py-2 px-3 text-right font-mono font-bold text-slate-900">
                {{ formatCurrency(quote.discount) }}
              </td>
            </tr>

            <!-- Total ($) - Soft Blue Highlight Matching Reference PDF -->
            <tr class="border-b border-slate-300 bg-[#9fb5e6]/50">
              <td colspan="6" class="py-2 px-3 text-right font-extrabold text-slate-900 border-r border-slate-300">
                Total ($)
              </td>
              <td class="py-2 px-3 text-right font-mono font-extrabold text-slate-900">
                {{ formatCurrency(quote.total) }}
              </td>
            </tr>
          </tbody>
        </table>
      </div>

      <!-- 5. FOOTER & CASHIER SIGNATURE AREA (Matching PDF layout) -->
      <div class="mt-8 pt-4">
        <div class="flex items-start justify-between flex-wrap gap-4 text-xs font-sans">
          <!-- Policy Note on Left -->
          <div class="space-y-1">
            <div class="font-bold text-slate-900 font-khmer">
              {{ clinic.note_kh || 'បញ្ជាក់៖ ប្រាក់ដែលបានបង់រួចមិនអាចដកវិញបានទេ' }}
            </div>
            <div class="text-slate-600 italic">
              {{ clinic.note_en || 'Note: All payment are not returnable' }}
            </div>
          </div>

          <!-- Cashier Signature on Right -->
          <div class="text-right space-y-2 min-w-[200px]">
            <div class="text-slate-600 font-khmer">
              ធ្វើនៅថ្ងៃទី: <span class="font-mono text-slate-900">{{ quote.printed_at }}</span>
            </div>
            <div class="font-bold text-slate-900 font-khmer pt-1">
              អ្នកទទួលប្រាក់ / Cashier
            </div>
            <div class="pt-8 text-slate-400">
              ..................................
            </div>
            <div class="font-bold text-slate-800">
              {{ quote.cashier_name || 'fwta owner' }}
            </div>
          </div>
        </div>

        <!-- Clinic Address & Telegram at Bottom Center -->
        <div class="text-center text-xs text-slate-600 space-y-1 mt-12 pt-6 border-t border-slate-200">
          <div>
            Address: {{ clinic.address || 'Sangkat Tuek Laok I, Khan Toulkork, Phnom Penh.' }}
          </div>
          <div class="font-khmer">
            ទូរស័ព្ទលេខ: {{ clinic.phone || '086761216 (Call & Telegram)' }}
          </div>
        </div>
      </div>

    </div>
  </div>
</template>

<script>
import { Head } from "@inertiajs/inertia-vue3";
import PrintHeader from "@/Components/Tenant/PrintHeader.vue";

export default {
  name: "QuotePrint",
  components: {
    Head,
    PrintHeader,
  },
  props: {
    quote: {
      type: Object,
      required: true,
    },
    clinic: {
      type: Object,
      required: true,
    },
  },
  methods: {
    printQuote() {
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
  },
};
</script>

<style scoped>
@import url('https://fonts.googleapis.com/css2?family=Battambang:wght@400;700;900&family=Content:wght@400;700&display=swap');

.font-khmer {
  font-family: 'Battambang', 'Content', sans-serif;
}

@media print {
  @page {
    size: A4 portrait;
    margin: 10mm 12mm 12mm 12mm;
  }

  body, html {
    background-color: #ffffff !important;
    color: #000000 !important;
    margin: 0 !important;
    padding: 0 !important;
    -webkit-print-color-adjust: exact !important;
    print-color-adjust: exact !important;
  }

  .quote-print-wrapper {
    background: #ffffff !important;
    padding: 0 !important;
    margin: 0 !important;
    min-height: auto !important;
  }

  #quote-sheet {
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
