<template>
  <TenantLayout>
    <Head :title="tm('Edit_Medicine') || 'Edit Medicine'" />
    <div class="px-4 py-4">
      <!-- Breadcrumb Navigation -->
      <nav class="flex pt-4 mb-6" aria-label="Breadcrumb">
        <ol class="inline-flex items-center space-x-1 md:space-x-3">
          <li class="inline-flex items-center">
            <Link :href="route('tenant.dashboard')" class="inline-flex items-center text-sm font-medium text-gray-700 hover:text-gray-900 dark:text-gray-400 dark:hover:text-white">
              <svg class="mr-2 w-4 h-4" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                <path d="M10.707 2.293a1 1 0 00-1.414 0l-7 7a1 1 0 001.414 1.414L4 10.414V17a1 1 0 001 1h2a1 1 0 001-1v-2a1 1 0 011-1h2a1 1 0 011 1v2a1 1 0 001 1h2a1 1 0 001-1v-6.586l.293.293a1 1 0 001.414-1.414l-7-7z"></path>
              </svg>
              {{ tm('Home') || 'Home' }}
            </Link>
          </li>
          <li>
            <div class="flex items-center">
              <svg class="w-6 h-6 text-gray-400" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path>
              </svg>
              <Link :href="route('medicines.index')" class="ml-1 text-sm font-medium text-gray-700 hover:text-gray-900 md:ml-2 dark:text-gray-400 dark:hover:text-white">
                {{ tm('Medicines') || 'Medicines' }}
              </Link>
            </div>
          </li>
          <li>
            <div class="flex items-center">
              <svg class="w-6 h-6 text-gray-400" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path>
              </svg>
              <span class="ml-1 text-sm font-medium text-gray-700 hover:text-gray-900 md:ml-2 dark:text-gray-400 dark:hover:text-white">
                {{ tm('Edit') || 'Edit' }}
              </span>
            </div>
          </li>
        </ol>
      </nav>

      <div class="p-8 text-center text-slate-400">
        <p class="text-sm">Opening Edit Medicine Pop-up Screen...</p>
      </div>

      <!-- Pop-up Screen Modal -->
      <MedicineModal
        :show="true"
        :medicine="medicine"
        @close="onClose"
        @saved="onClose"
      />
    </div>
  </TenantLayout>
</template>

<script>
import { defineComponent } from 'vue'
import TenantLayout from '@/Layouts/TenantLayout.vue'
import MedicineModal from '@/Components/Tenant/MedicineModal.vue'
import { Head, Link } from '@inertiajs/inertia-vue3'
import { Inertia } from '@inertiajs/inertia'
import { useI18n } from 'vue-i18n'
import { lang } from '@/Lang'

export default defineComponent({
  components: {
    TenantLayout,
    MedicineModal,
    Head,
    Link,
  },
  props: {
    medicine: Object,
  },
  setup() {
    const { tm } = useI18n({
      inheritLocale: true,
      messages: lang,
    })
    return { tm }
  },
  methods: {
    onClose() {
      Inertia.visit(route('medicines.index'))
    },
  },
})
</script>
