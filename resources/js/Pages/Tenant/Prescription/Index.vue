<template>
  <TenantLayout>
    <Head :title="tm('Prescriptions') || 'Prescriptions'" />
    <div class="px-4 py-4">
      <jet-banner />

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
              <Link :href="route('tenant.payments.index')" class="ml-1 text-sm font-medium text-gray-700 hover:text-gray-900 md:ml-2 dark:text-gray-400 dark:hover:text-white">
                {{ tm('Payment') || 'Payment' }}
              </Link>
            </div>
          </li>
          <li>
            <div class="flex items-center">
              <svg class="w-6 h-6 text-gray-400" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path>
              </svg>
              <span class="ml-1 text-sm font-medium text-gray-700 hover:text-gray-900 md:ml-2 dark:text-gray-400 dark:hover:text-white">
                {{ tm('Prescriptions') || 'Prescriptions' }}
              </span>
            </div>
          </li>
        </ol>
      </nav>

      <!-- Main Card -->
      <div class="bg-white rounded-md dark:bg-darker shadow-sm">
        <div class="flex items-center justify-between p-4 border-b dark:border-primary">
          <h4 class="text-lg font-semibold text-gray-600 dark:text-light">
            {{ tm('Prescriptions') || 'Prescriptions' }}
          </h4>
          <div class="flex items-center space-x-2">
            <!-- Create Prescription Button -->
            <button
              v-if="can(['all', 'prescription:create', 'prescriptions:prescriptions:create', 'prescriptions.create'])"
              type="button"
              @click="openCreateModal"
              class="inline-flex items-center px-4 py-2 bg-primary hover:bg-primary-dark border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest active:bg-gray-900 focus:outline-none focus:border-gray-900 focus:ring focus:ring-primary focus:ring-offset-1 dark:focus:ring-offset-darker shadow-sm transition"
            >
              + {{ tm('Create_Prescription') || 'Create Prescription' }}
            </button>
          </div>
        </div>

        <div class="p-4">
          <Table :resource="prescriptions">
            <!-- 1. Action Column (Print, Edit, Delete) -->
            <template #cell(actions)="{ item: prescription }">
              <div class="flex items-center gap-1.5">
                <!-- Print Action -->
                <Link
                  :href="route('prescriptions.print', prescription.id)"
                  class="inline-flex items-center gap-1 px-2 py-1 text-xs font-semibold text-slate-700 hover:text-blue-600 bg-slate-100 hover:bg-blue-50 dark:bg-slate-800 dark:text-slate-300 dark:hover:text-blue-400 rounded transition"
                  title="Print Prescription"
                >
                  <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4H7v4a2 2 0 002 2zM7 9V5a2 2 0 012-2h6a2 2 0 012 2v4" />
                  </svg>
                  <span>{{ tm('Print') || 'Print' }}</span>
                </Link>

                <!-- Edit Action -->
                <button
                  v-if="can(['all', 'prescription:edit', 'prescriptions:prescriptions:edit', 'prescriptions.edit'])"
                  type="button"
                  @click="openEditModal(prescription)"
                  class="inline-flex items-center gap-1 px-2 py-1 text-xs font-semibold text-indigo-700 hover:text-indigo-900 bg-indigo-50 hover:bg-indigo-100 dark:bg-indigo-950/40 dark:text-indigo-300 rounded transition"
                  title="Edit Prescription"
                >
                  <PencilAltIcon class="w-3.5 h-3.5 text-indigo-600 dark:text-indigo-400" />
                  <span>{{ tm('Edit') || 'Edit' }}</span>
                </button>

                <!-- Delete Action -->
                <button
                  v-if="can(['all', 'prescription:delete', 'prescriptions:prescriptions:delete', 'prescriptions.destroy'])"
                  type="button"
                  @click="destroy(route('prescriptions.destroy', prescription.id))"
                  class="inline-flex items-center gap-1 px-2 py-1 text-xs font-semibold text-red-700 hover:text-red-900 bg-red-50 hover:bg-red-100 dark:bg-red-950/40 dark:text-red-300 rounded transition"
                  title="Delete Prescription"
                >
                  <TrashIcon class="w-3.5 h-3.5 text-red-600 dark:text-red-400" />
                  <span>{{ tm('Delete') || 'Delete' }}</span>
                </button>
              </div>
            </template>

            <!-- 2. ID Column -->
            <template #cell(id)="{ item: prescription }">
              <span class="font-mono text-xs font-bold text-slate-800 dark:text-slate-200">
                #{{ prescription.id }}
              </span>
            </template>

            <!-- 3. Patient Column -->
            <template #cell(patient)="{ item: prescription }">
              <div class="flex flex-col">
                <span class="font-semibold text-slate-900 dark:text-slate-100 text-xs">
                  {{ prescription.patient ? `${prescription.patient.firstname || ''} ${prescription.patient.lastname || ''}`.trim() : '-' }}
                </span>
                <span v-if="prescription.patient && prescription.patient.id_number" class="text-[11px] font-mono text-slate-500 dark:text-slate-400">
                  ID: {{ prescription.patient.id_number }}
                </span>
              </div>
            </template>

            <!-- 4. Prescription Column (Information / Medicine Summary) -->
            <template #cell(prescription)="{ item: prescription }">
              <span class="text-xs text-slate-700 dark:text-slate-300 line-clamp-2 max-w-md font-khmer">
                {{ prescription.summary || prescription.prescription || '-' }}
              </span>
            </template>

            <!-- 5. Date Column -->
            <template #cell(created_at)="{ item: prescription }">
              <span class="text-xs text-slate-600 dark:text-slate-400 whitespace-nowrap">
                {{ prescription.created_at ? new Date(prescription.created_at).toLocaleDateString() : '-' }}
              </span>
            </template>
          </Table>
        </div>
      </div>
    </div>

    <!-- Prescription Create/Edit Modal -->
    <PrescriptionModal
      :show="showModal"
      :prescription="selectedPrescription"
      :patients="patients"
      :medicines="medicines"
      @close="closeModal"
      @saved="onSaved"
    />
  </TenantLayout>
</template>

<script>
import { defineComponent } from 'vue'
import TenantLayout from '@/Layouts/TenantLayout.vue'
import { Table } from '@/Components/Datatables'
import { useI18n } from 'vue-i18n'
import { lang } from '@/Lang'
import JetBanner from '@/Jetstream/Banner.vue'
import Destory from '@/Libs/Action/Delete.vue'
import PrescriptionModal from '@/Components/Tenant/PrescriptionModal.vue'
import { Head, Link } from '@inertiajs/inertia-vue3'
import {
  PencilAltIcon,
  TrashIcon,
} from '@heroicons/vue/solid'

export default defineComponent({
  mixins: [Destory],
  components: {
    TenantLayout,
    Table,
    JetBanner,
    PrescriptionModal,
    Head,
    Link,
    PencilAltIcon,
    TrashIcon,
  },
  props: {
    prescriptions: Object,
    patients: Array,
    medicines: Array,
  },
  setup() {
    const { tm } = useI18n({
      inheritLocale: true,
      messages: lang,
    })
    return { tm }
  },
  data() {
    return {
      showModal: false,
      selectedPrescription: null,
    }
  },
  mounted() {
    if (typeof window !== 'undefined') {
      const params = new URLSearchParams(window.location.search)
      if (params.get('create')) {
        this.openCreateModal()
      }
    }
  },
  methods: {
    openCreateModal() {
      this.selectedPrescription = null
      this.showModal = true
    },
    openEditModal(prescription) {
      this.selectedPrescription = { ...prescription }
      this.showModal = true
    },
    closeModal() {
      this.showModal = false
      this.selectedPrescription = null
    },
    onSaved() {
      this.closeModal()
      this.$inertia.reload({ preserveScroll: true })
    },
  },
})
</script>

<style scoped>
@import url('https://fonts.googleapis.com/css2?family=Battambang:wght@400;700&display=swap');

.font-khmer {
  font-family: 'Battambang', system-ui, sans-serif;
}
</style>
