<template>
  <div class="print-header-component">
    <!-- Scenario A: Custom Header Image Exists -->
    <div v-if="clinic.header_image" class="invoice-custom-header mb-6 text-center">
      <img
        :src="clinic.header_image"
        alt="Clinic Header"
        class="w-full object-contain max-h-40 mx-auto"
      />
    </div>

    <!-- Scenario B: Default Header (No custom header image) -->
    <div v-else class="invoice-default-header flex items-center justify-between pb-6 mb-4 border-b border-slate-100">
      <!-- Clinic Logo (Left) -->
      <div class="w-32 flex-shrink-0 flex items-center justify-start">
        <img
          v-if="clinic.logo"
          :src="clinic.logo"
          alt="Clinic Logo"
          class="h-20 w-auto object-contain max-w-[120px]"
        />
      </div>

      <!-- Clinic Titles & Slogan (Centered) -->
      <div class="flex-1 text-center px-4">
        <h1 class="font-moul text-xl sm:text-2xl font-bold text-slate-900 tracking-wide leading-relaxed">
          {{ clinic.name_kh }}
        </h1>
        <h2 class="font-sans font-bold text-sm sm:text-base text-slate-700 tracking-normal mt-0.5">
          {{ clinic.name_en }}
        </h2>
        <p v-if="clinic.slogan" class="font-khmer text-xs text-slate-600 mt-1">
          {{ clinic.slogan }}
        </p>
      </div>

      <!-- Balance spacer for symmetric centering -->
      <div class="w-32 flex-shrink-0 hidden sm:block"></div>
    </div>

    <!-- Optional Slot for document title or extra header info -->
    <slot />
  </div>
</template>

<script>
export default {
  name: "PrintHeader",
  props: {
    clinic: {
      type: Object,
      required: true,
      default: () => ({}),
    },
  },
};
</script>

<style scoped>
@import url('https://fonts.googleapis.com/css2?family=Battambang:wght@400;700&family=Moul&display=swap');

.font-moul {
  font-family: 'Moul', cursive, serif;
}
.font-khmer {
  font-family: 'Battambang', system-ui, sans-serif;
}
</style>
