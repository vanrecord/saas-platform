<template>
  <div class="cash-deposit-print-wrapper min-h-screen bg-slate-100 py-6 px-4 print:p-0 print:bg-white text-slate-800">
    <Head :title="`Deposit Receipt #${deposit.code_formatted}`" />

    <!-- Top Action Bar (hidden on print) -->
    <div class="max-w-4xl mx-auto flex items-center justify-between pb-4 mb-6 border-b border-slate-200 print:hidden">
      <Link
        :href="route('cash-deposits.index')"
        class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-semibold text-slate-600 bg-white hover:bg-slate-50 border border-slate-200 rounded-lg shadow-2xs transition"
      >
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
        </svg>
        Back to Cash Deposits
      </Link>

      <button
        type="button"
        @click="printNow"
        class="inline-flex items-center gap-1.5 px-4 py-2 text-xs font-bold text-white bg-[#0D63D6] hover:bg-blue-700 rounded-lg shadow-sm transition cursor-pointer"
      >
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4H7v4a2 2 0 002 2zM7 9V5a2 2 0 012-2h6a2 2 0 012 2v4" />
        </svg>
        Print Receipt
      </button>
    </div>

    <!-- Printable A4 Receipt Sheet (Matching 2.pdf layout) -->
    <div id="deposit-receipt-sheet" class="relative a4-sheet max-w-4xl mx-auto bg-white shadow-xl border border-slate-200 p-8 sm:p-14 print:p-0 print:shadow-none print:border-none print:max-w-none print:w-full overflow-hidden">
      
      <!-- Faint Background Watermark (Matching 2.pdf) -->
      <div class="pointer-events-none absolute inset-0 flex items-center justify-center opacity-[0.08] select-none z-0">
        <img
          :src="clinic.logo || '/images/dental/logo.png'"
          alt="Watermark"
          class="w-[420px] h-[420px] object-contain"
        />
      </div>

      <!-- Main Receipt Content -->
      <div class="relative z-10 flex flex-col justify-between min-h-[960px] print:min-h-0">
        <div>
          <!-- 1. CLINIC HEADER -->
          <div class="flex items-start justify-between pb-4 mb-4">
            <!-- Clinic Logo (Top Left) -->
            <div class="w-36 flex-shrink-0 flex items-start justify-start">
              <img
                :src="clinic.logo || '/images/dental/logo.png'"
                alt="Clinic Logo"
                class="h-24 w-auto object-contain max-w-[140px]"
              />
            </div>

            <!-- Clinic Names & Slogan (Centered) -->
            <div class="flex-1 text-center px-4 pt-1">
              <h1 class="font-moul text-xl sm:text-2xl font-bold text-slate-900 tracking-wide leading-relaxed">
                {{ clinic.name_kh || 'គ្លីនិកធ្មេញ ឌែនធីសយើង' }}
              </h1>
              <h2 class="font-sans font-bold text-sm sm:text-base text-slate-800 tracking-normal mt-0.5">
                {{ clinic.name_en || 'Dentist Yoeung Clinic' }}
              </h2>
              <p class="font-khmer text-xs font-semibold text-slate-700 mt-1">
                {{ clinic.slogan || 'គុណភាព សុវត្ថិភាព និងអនាម័យជាចម្បង' }}
              </p>
            </div>

            <!-- Symmetric balancing spacer -->
            <div class="w-36 flex-shrink-0 hidden sm:block"></div>
          </div>

          <!-- 2. RECEIPT METADATA (Date, No, Name of Client) -->
          <div class="pt-6 pb-4 text-xs font-sans text-slate-800">
            <div class="flex items-center justify-between">
              <div>
                <span class="font-medium inline-block w-28">Date</span>
                <span>: {{ deposit.date_formatted }}</span>
              </div>
              <div class="text-right">
                <span class="font-medium inline-block w-20">No.</span>
                <span class="font-mono">: {{ deposit.code_formatted }}</span>
              </div>
            </div>

            <div class="flex items-center justify-between mt-2">
              <div>
                <span class="font-medium inline-block w-28">Name of Client</span>
                <span class="font-bold">: {{ deposit.patient_fullname }}</span>
              </div>
            </div>
          </div>

          <!-- 3. MAIN TITLE BOX: DEPOSIT RECEIPT -->
          <div class="my-6 border-2 border-slate-900 rounded-xs py-3.5 px-6 text-center">
            <h3 class="text-xl sm:text-2xl font-black tracking-widest text-slate-900 uppercase">
              DEPOSIT RECEIPT
            </h3>
          </div>

          <!-- 4. DEPOSIT INFORMATION SECTION (Sum of amount & Cash / Bank Checkbox) -->
          <div class="my-10 flex items-center justify-between gap-6 py-4">
            <!-- Left: The sum of amount -->
            <div class="flex items-baseline gap-2 text-sm text-slate-900">
              <span class="font-semibold text-slate-800 whitespace-nowrap">The sum of amount:</span>
              <div class="border-b-2 border-dotted border-slate-400 px-6 pb-1">
                <span class="text-xl sm:text-2xl font-black font-mono text-[#0D63D6] tracking-tight">
                  {{ deposit.currency === 'KHR' ? '៛' : '$' }} {{ deposit.amount_formatted }}
                </span>
              </div>
            </div>

            <!-- Right: Cash & Bank Checkbox Indicators (Matching 2.pdf exactly) -->
            <div class="space-y-3.5 pl-4 flex-shrink-0">
              <!-- Cash Indicator -->
              <div class="flex items-center gap-3">
                <svg
                  v-if="isCash"
                  class="w-5 h-5 flex-shrink-0"
                  viewBox="0 0 20 20"
                  fill="none"
                  xmlns="http://www.w3.org/2000/svg"
                >
                  <rect x="0.5" y="0.5" width="19" height="19" rx="2" fill="#0D63D6" stroke="#0D63D6" />
                  <path d="M4.5 10.5L8.5 14.5L15.5 6.5" stroke="white" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" />
                </svg>
                <svg
                  v-else
                  class="w-5 h-5 flex-shrink-0"
                  viewBox="0 0 20 20"
                  fill="none"
                  xmlns="http://www.w3.org/2000/svg"
                >
                  <rect x="0.5" y="0.5" width="19" height="19" rx="2" fill="#94A3B8" stroke="#94A3B8" />
                </svg>
                <span class="text-xs font-semibold text-slate-800">Cash</span>
              </div>

              <!-- Bank Indicator -->
              <div class="flex items-center gap-3">
                <svg
                  v-if="!isCash"
                  class="w-5 h-5 flex-shrink-0"
                  viewBox="0 0 20 20"
                  fill="none"
                  xmlns="http://www.w3.org/2000/svg"
                >
                  <rect x="0.5" y="0.5" width="19" height="19" rx="2" fill="#0D63D6" stroke="#0D63D6" />
                  <path d="M4.5 10.5L8.5 14.5L15.5 6.5" stroke="white" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" />
                </svg>
                <svg
                  v-else
                  class="w-5 h-5 flex-shrink-0"
                  viewBox="0 0 20 20"
                  fill="none"
                  xmlns="http://www.w3.org/2000/svg"
                >
                  <rect x="0.5" y="0.5" width="19" height="19" rx="2" fill="#94A3B8" stroke="#94A3B8" />
                </svg>
                <span class="text-xs font-semibold text-slate-800">Bank</span>
              </div>
            </div>
          </div>
        </div>

        <!-- 5. SIGNATURE SECTION (Bottom of A4) -->
        <div class="pt-24 pb-8">
          <div class="flex items-end justify-between px-6 text-xs text-slate-800">
            <!-- Payee's Signature -->
            <div class="text-center w-64">
              <p class="font-bold mb-16 text-slate-900">Payee's Signature</p>
              <div class="border-b-2 border-slate-900 mx-auto w-56 mb-4"></div>
              <p class="text-slate-600 font-sans">Date: ............................................</p>
            </div>

            <!-- Receiver's Signature -->
            <div class="text-center w-64">
              <p class="font-bold mb-16 text-slate-900">Receiver's Signature</p>
              <div class="border-b-2 border-slate-900 mx-auto w-56 mb-4"></div>
              <p class="text-slate-600 font-sans">Date: ............................................</p>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import { Head, Link } from '@inertiajs/inertia-vue3'

export default {
  name: 'CashDepositPrint',
  components: {
    Head,
    Link,
  },
  props: {
    deposit: {
      type: Object,
      required: true,
    },
    clinic: {
      type: Object,
      default: () => ({}),
    },
  },
  computed: {
    isCash() {
      const val = this.deposit?.is_cash
      return val === true || val === 1 || val === '1' || val === 'true'
    },
  },
  mounted() {
    // If URL has ?auto_print=true, trigger window.print
    if (new URLSearchParams(window.location.search).get('auto_print') === 'true') {
      setTimeout(() => {
        window.print()
      }, 500)
    }
  },
  methods: {
    printNow() {
      window.print()
    },
  },
}
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

/* Print Specific Layout & Styles */
@media print {
  @page {
    size: A4 portrait;
    margin: 15mm;
  }

  *, *::before, *::after {
    -webkit-print-color-adjust: exact !important;
    print-color-adjust: exact !important;
    color-adjust: exact !important;
  }

  body, html {
    background-color: #ffffff !important;
    color: #000000 !important;
    margin: 0 !important;
    padding: 0 !important;
    -webkit-print-color-adjust: exact !important;
    print-color-adjust: exact !important;
  }

  .cash-deposit-print-wrapper {
    background: #ffffff !important;
    padding: 0 !important;
    margin: 0 !important;
    min-height: auto !important;
  }

  #deposit-receipt-sheet {
    box-shadow: none !important;
    border: none !important;
    padding: 0 !important;
    margin: 0 !important;
    max-width: 100% !important;
    width: 100% !important;
  }
}
</style>
