<template>
  <div class="prescription-print-wrapper min-h-screen bg-slate-100 py-6 px-4 print:p-0 print:bg-white text-slate-800">
    <Head :title="`Prescription #${prescription.id}`" />

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
            Back to Prescriptions
          </button>
          <span class="text-xs text-slate-400 font-medium">|</span>
          <span class="text-xs text-slate-500 font-mono font-bold">#{{ prescription.id }}</span>
        </div>

        <div class="flex items-center gap-3">
          <span class="hidden sm:inline-block text-[11px] text-slate-500">
            Tip: In print dialog, choose <strong class="text-slate-700">Save as PDF</strong> to export
          </span>
          <button
            type="button"
            @click="printPrescription"
            class="inline-flex items-center gap-2 px-5 py-2 text-xs font-bold text-white bg-[#0D63D6] hover:bg-[#094bb5] rounded-lg shadow-sm transition"
          >
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4H7v4a2 2 0 002 2zM7 9V5a2 2 0 012-2h6a2 2 0 012 2v4" />
            </svg>
            Print Prescription
          </button>
        </div>
      </div>
    </div>

    <!-- Printable A4 Prescription Sheet (Matching Uploaded PDF Design) -->
    <div id="prescription-sheet" class="relative a4-sheet max-w-4xl mx-auto bg-white shadow-xl border border-slate-200 p-8 sm:p-12 print:p-0 print:shadow-none print:border-none print:max-w-none print:w-full overflow-hidden">
      
      <!-- Faint Background Watermark (Matching PDF) -->
      <div class="pointer-events-none absolute inset-0 flex items-center justify-center opacity-[0.06] select-none z-0">
        <img
          :src="clinic.logo || '/images/dental/logo.png'"
          alt="Watermark"
          class="w-[380px] h-[380px] object-contain"
        />
      </div>

      <!-- Content Container -->
      <div class="relative z-10 flex flex-col justify-between min-h-[920px] print:min-h-0">
        <div>
          <!-- 1. CLINIC HEADER -->
          <div class="flex items-center justify-between pb-4 mb-2 border-b border-slate-100">
            <!-- Clinic Logo (Left) -->
            <div class="w-32 flex-shrink-0 flex items-center justify-start">
              <img
                :src="clinic.logo || '/images/dental/logo.png'"
                alt="Clinic Logo"
                class="h-20 w-auto object-contain max-w-[120px]"
              />
            </div>

            <!-- Clinic Names & Slogan (Centered) -->
            <div class="flex-1 text-center px-4">
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

            <!-- Spacer for symmetric centering -->
            <div class="w-32 flex-shrink-0 hidden sm:block"></div>
          </div>

          <!-- 2. PRESCRIPTION TITLE (Matching PDF) -->
          <div class="text-center my-6">
            <h3 class="font-moul text-lg sm:text-xl font-bold text-slate-900 tracking-wide">
              រោគវិនិច្ឆ័យ (Prescription)
            </h3>
          </div>

          <!-- 3. PATIENT INFORMATION (Matching PDF Layout) -->
          <div class="my-6 text-xs sm:text-sm text-slate-900 font-khmer">
            <div class="grid grid-cols-12 gap-y-2">
              <!-- Row 1: Name (Left) | Sex (Right) -->
              <div class="col-span-7 flex items-baseline">
                <span class="font-bold w-28 flex-shrink-0">ឈ្មោះ / Name :</span>
                <span class="font-bold text-slate-900 ml-1">{{ prescription.patient.name || '-' }}</span>
              </div>
              <div class="col-span-5 flex items-baseline justify-start sm:justify-end">
                <span class="font-bold mr-2">ភេទ / Sex :</span>
                <span class="font-bold text-slate-900 uppercase">{{ prescription.patient.sex || 'M' }}</span>
              </div>

              <!-- Row 2: ID (Left) | Age (Right) -->
              <div class="col-span-7 flex items-baseline">
                <span class="font-bold w-28 flex-shrink-0">លេខរៀង / ID :</span>
                <span class="font-mono font-bold text-slate-900 ml-1">{{ prescription.patient.id || '-' }}</span>
              </div>
              <div class="col-span-5 flex items-baseline justify-start sm:justify-end">
                <span class="font-bold mr-2">អាយុ / Age :</span>
                <span class="font-bold text-slate-900">{{ prescription.patient.age || '-' }} ឆ្នាំ/Years</span>
              </div>
            </div>
          </div>

          <!-- 4. MEDICINE DETAILS TABLE (Matching PDF columns & styling) -->
          <div class="my-6">
            <table class="w-full border-collapse border border-slate-300 text-xs sm:text-sm font-khmer">
              <thead>
                <tr class="bg-sky-50/70 print:bg-sky-50 text-slate-900 font-bold text-center border-b border-slate-300">
                  <th class="py-2.5 px-2 border-r border-slate-300 w-12">ល.រ</th>
                  <th class="py-2.5 px-3 border-r border-slate-300 text-left">ឈ្មោះថ្នាំ</th>
                  <th class="py-2.5 px-3 border-r border-slate-300 w-24 text-center">ចំនួន</th>
                  <th class="py-2.5 px-3 border-r border-slate-300 text-center w-36">ការប្រើប្រាស់</th>
                  <th class="py-2.5 px-3 text-center w-24">ចំនួនថ្ងៃ</th>
                </tr>
              </thead>
              <tbody>
                <tr
                  v-for="item in prescription.items"
                  :key="item.n"
                  class="border-b border-slate-300 hover:bg-slate-50/50 print:hover:bg-transparent page-break-inside-avoid"
                >
                  <td class="py-2 px-2 text-center border-r border-slate-300 font-medium text-slate-800">
                    {{ item.n }}
                  </td>
                  <td class="py-2 px-3 border-r border-slate-300 text-left font-medium text-slate-900">
                    <div>{{ item.name }}</div>
                    <div v-if="item.note" class="text-[11px] text-slate-500 italic mt-0.5">
                      * {{ item.note }}
                    </div>
                  </td>
                  <td class="py-2 px-3 text-center border-r border-slate-300 text-slate-900 font-medium whitespace-nowrap">
                    {{ item.quantity }}
                  </td>
                  <td class="py-2 px-3 text-center border-r border-slate-300 text-slate-900 whitespace-nowrap">
                    {{ item.usage }}
                  </td>
                  <td class="py-2 px-3 text-center text-slate-900 whitespace-nowrap">
                    {{ item.days }}
                  </td>
                </tr>

                <!-- Empty fallback -->
                <tr v-if="!prescription.items || prescription.items.length === 0">
                  <td colspan="5" class="py-6 text-center text-slate-400 italic">
                    No medicines prescribed.
                  </td>
                </tr>
              </tbody>
            </table>
          </div>

          <!-- 5. DATE & DOCTOR SIGNATURE (Right-aligned, matching PDF) -->
          <div class="mt-8 flex justify-end font-khmer text-xs sm:text-sm">
            <div class="text-center space-y-1">
              <p class="text-slate-900">
                ធ្វើនៅថ្ងៃទី: <span class="font-mono font-medium">{{ prescription.date }}</span>
              </p>
              <p class="font-bold text-slate-900 pt-0.5">
                គ្រូពេទ្យព្យាបាល / Doctor
              </p>
              <div class="pt-16">
                <div class="border-b border-dotted border-slate-700 w-48 mx-auto mb-1.5"></div>
                <p class="font-bold text-slate-900 text-xs">
                  {{ prescription.doctor.name || 'fwta owner' }}
                </p>
              </div>
            </div>
          </div>
        </div>

        <!-- 6. FOOTER (Matching PDF Khmer instructions & Contact Bar) -->
        <div class="mt-12 pt-6 font-khmer text-xs">
          <!-- Khmer warning/instruction text -->
          <p class="text-slate-800 font-medium mb-4">
            *សូមយកវេជ្ជបញ្ជានេះមកវិញសម្រាប់ការពិនិត្យពេលក្រោយ
          </p>

          <!-- Clinic Contact Information (Bottom Border Bar) -->
          <div class="pt-3 border-t border-slate-300 text-center text-[11px] sm:text-xs text-slate-700 space-y-1 font-sans">
            <p>
              E-mail : <span class="font-medium">pethyoeungdental@gmail.com</span> Tel: <span class="font-medium font-mono">(+855) 093/077 685 668</span>
            </p>
            <p class="text-slate-800">
              #15-16D st: 03 Sangkat Toek LoakIII, Khan Toul Kork, Phnom Penh
            </p>
          </div>
        </div>
      </div>

    </div>
  </div>
</template>

<script>
import { Head } from "@inertiajs/inertia-vue3";

export default {
  components: {
    Head,
  },
  props: {
    prescription: {
      type: Object,
      required: true,
    },
    clinic: {
      type: Object,
      required: true,
    },
  },
  methods: {
    printPrescription() {
      window.print();
    },
    goBack() {
      if (window.history.length > 1) {
        window.history.back();
      } else {
        window.close();
      }
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

  .prescription-print-wrapper {
    background: #ffffff !important;
    padding: 0 !important;
    margin: 0 !important;
    min-height: auto !important;
  }

  #prescription-sheet {
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
