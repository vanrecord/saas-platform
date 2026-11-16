<template>
  <div class="print-footer-component mt-8 pt-4 page-break-inside-avoid font-sans text-xs">
    <div class="flex items-start justify-between">
      <!-- Left: Policy Notes -->
      <div class="w-1/2 space-y-1">
        <slot name="notes">
          <p v-if="showNotes && effectiveNoteKh" class="font-khmer font-bold text-slate-900 leading-relaxed">
            {{ effectiveNoteKh }}
          </p>
          <p v-if="showNotes && effectiveNoteEn" class="font-bold text-slate-800">
            {{ effectiveNoteEn }}
          </p>
        </slot>
      </div>

      <!-- Right: Printed Date & Cashier / Signer Signature Area -->
      <div v-if="showSigner" class="w-1/2 text-right space-y-1">
        <slot name="signer">
          <p v-if="printedAt" class="font-khmer text-slate-800">
            ធ្វើនៅថ្ងៃទី: <span class="font-mono font-medium">{{ printedAt }}</span>
          </p>
          <p class="font-khmer font-bold text-slate-900 pt-1">
            {{ signerTitle }}
          </p>

          <!-- Signature space & dotted line -->
          <div class="pt-14 inline-block text-center">
            <div class="border-b border-dotted border-slate-600 w-48 mb-1.5"></div>
            <p class="font-bold text-slate-900 text-xs">
              {{ signerName || 'fwta owner' }}
            </p>
          </div>
        </slot>
      </div>
    </div>

    <!-- Clinic Address & Contact Bottom Bar (Matching PDF) -->
    <slot name="bottom">
      <div
        v-if="showClinicContact && (clinic.address || clinic.phone)"
        class="mt-14 pt-4 border-t border-slate-200 text-center text-xs text-slate-700 space-y-1 font-khmer"
      >
        <p v-if="clinic.address">{{ clinic.address }}</p>
        <p v-if="clinic.phone">
          ទូរស័ព្ទលេខ: <span class="font-mono font-semibold">{{ clinic.phone }}</span>
        </p>
      </div>
    </slot>
  </div>
</template>

<script>
export default {
  name: "PrintFooter",
  props: {
    clinic: {
      type: Object,
      required: true,
      default: () => ({}),
    },
    printedAt: {
      type: String,
      default: "",
    },
    signerTitle: {
      type: String,
      default: "អ្នកទទួលប្រាក់ / Cashier",
    },
    signerName: {
      type: String,
      default: "",
    },
    noteKh: {
      type: String,
      default: "",
    },
    noteEn: {
      type: String,
      default: "",
    },
    showNotes: {
      type: Boolean,
      default: true,
    },
    showSigner: {
      type: Boolean,
      default: true,
    },
    showClinicContact: {
      type: Boolean,
      default: true,
    },
  },
  computed: {
    effectiveNoteKh() {
      return this.noteKh || this.clinic.note_kh || "បញ្ជាក់៖ ប្រាក់ដែលបានបង់រួចមិនអាចដកវិញបានទេ";
    },
    effectiveNoteEn() {
      return this.noteEn || this.clinic.note_en || "Note: All payment are not returnable";
    },
  },
};
</script>

<style scoped>
@import url('https://fonts.googleapis.com/css2?family=Battambang:wght@400;700&display=swap');

.font-khmer {
  font-family: 'Battambang', system-ui, sans-serif;
}
.page-break-inside-avoid {
  break-inside: avoid;
  page-break-inside: avoid;
}
</style>
