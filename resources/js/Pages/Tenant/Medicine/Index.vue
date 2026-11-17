<template>
  <TenantLayout>
    <Head :title="tm('Medicines') || 'Medicines'" />
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
              <span class="ml-1 text-sm font-medium text-gray-700 hover:text-gray-900 md:ml-2 dark:text-gray-400 dark:hover:text-white">
                {{ tm('Settings') || 'Settings' }}
              </span>
            </div>
          </li>
          <li>
            <div class="flex items-center">
              <svg class="w-6 h-6 text-gray-400" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path>
              </svg>
              <span class="ml-1 text-sm font-medium text-gray-700 hover:text-gray-900 md:ml-2 dark:text-gray-400 dark:hover:text-white">
                {{ tm('Medicines') || 'Medicines' }}
              </span>
            </div>
          </li>
        </ol>
      </nav>

      <div class="col-span-2 bg-white rounded-md dark:bg-darker shadow-sm">
        <div class="flex items-center justify-between p-4 border-b dark:border-primary">
          <h4 class="text-lg font-semibold text-gray-500 dark:text-light">
            {{ tm('Medicines') || 'Medicines' }}
          </h4>
          <div class="flex items-center space-x-2">
            <!-- Create Medicine Pop-up Button -->
            <button
              v-if="can(['all', 'medicine:create', 'medicines:medicines:create', 'medicines', 'medicines.create'])"
              type="button"
              @click="openCreateModal"
              class="inline-flex items-center px-4 py-2 bg-primary hover:bg-primary-dark border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest active:bg-gray-900 focus:outline-none focus:border-gray-900 focus:ring-gray-300 disabled:opacity-25 transition focus:ring-2 focus:ring-primary focus:ring-offset-1 dark:focus:ring-offset-darker shadow-sm"
            >
              + {{ tm('Create_Medicine') || 'Create Medicine' }}
            </button>
          </div>
        </div>

        <div class="p-4">
          <Table :resource="medicines">
            <!-- Index / ID Column -->
            <template #cell(id)="{ item: medicine, index: key }">
              <span class="font-mono text-xs text-slate-700 dark:text-slate-300">
                #{{ medicine.id }}
              </span>
            </template>

            <!-- Name Column -->
            <template #cell(name)="{ item: medicine }">
              <span class="font-semibold text-slate-800 dark:text-slate-200">
                {{ medicine.name }}
              </span>
            </template>

            <!-- Description Column -->
            <template #cell(description)="{ item: medicine }">
              <span class="text-xs text-slate-600 dark:text-slate-300 line-clamp-2 max-w-md">
                {{ medicine.description || '-' }}
              </span>
            </template>

            <!-- Created Date Column -->
            <template #cell(created_at)="{ item: medicine }">
              <span class="text-xs text-slate-500 dark:text-slate-400">
                {{ medicine.created_at ? new Date(medicine.created_at).toLocaleDateString() : '-' }}
              </span>
            </template>

            <!-- Actions Dropdown -->
            <template #cell(actions)="{ item: medicine }">
              <jet-dropdown :width="w60" align="left">
                <template #trigger>
                  <button class="flex text-sm border-2 border-transparent rounded-full focus:outline-none focus:border-gray-300 transition">
                    <DotsHorizontalIcon class="h-5 w-5 text-blue-500 text-right" />
                  </button>
                </template>
                <template #content>
                  <transition name="fade">
                    <div class="dropdown-rich-trigger-content dropdown-content-right dropdown-content-fit">
                      <div class="flex justify-between items-center px-2 border dark:border-transparent dark:shadow-dark bg-white dark:bg-gray-700 text-gray-700 dark:text-gray-200 p-1 radius-3">
                        <!-- Edit (Pop-up Modal) -->
                        <div
                          v-if="can(['all', 'medicine:edit', 'medicines:medicines:edit', 'medicines.edit'])"
                          @click="openEditModal(medicine)"
                          class="hover:bg-indigo-100 hover:text-indigo-500 dark:hover:text-gray-200 dark:hover:bg-indigo-500 dark:hover:bg-opacity-75 cursor-pointer text-sm px-2 radius-3"
                        >
                          <div class="flex items-center h-6">
                            <PencilAltIcon class="h-4 w-4 text-indigo-500 text-right mr-1" />{{ tm('edit') || 'Edit' }}
                          </div>
                        </div>
                        <!-- Delete -->
                        <div
                          v-if="can(['all', 'medicine:delete', 'medicines:medicines:delete', 'medicines.destroy'])"
                          class="hover:bg-red-100 hover:text-red-500 dark:hover:text-gray-200 dark:hover:bg-red-500 dark:hover:bg-opacity-75 cursor-pointer text-sm px-2 radius-3"
                        >
                          <div class="flex items-center h-6" @click="destroy(route('medicines.destroy', medicine.id))">
                            <TrashIcon class="h-4 w-4 text-red-500 text-right mr-1" />{{ tm('delete') || 'Delete' }}
                          </div>
                        </div>
                      </div>
                    </div>
                  </transition>
                </template>
              </jet-dropdown>
            </template>
          </Table>
        </div>
      </div>
    </div>

    <!-- Medicine Create/Edit Pop-up Screen -->
    <MedicineModal
      :show="showModal"
      :medicine="selectedMedicine"
      @close="closeModal"
    />
  </TenantLayout>
</template>

<script>
import { defineComponent } from 'vue'
import TenantLayout from '@/Layouts/TenantLayout.vue'
import { Table } from '@/Components/Datatables'
import { useI18n } from 'vue-i18n'
import { lang } from '@/Lang'
import JetDropdown from '@/Jetstream/Dropdown.vue'
import JetBanner from '@/Jetstream/Banner.vue'
import Destory from '@/Libs/Action/Delete.vue'
import MedicineModal from '@/Components/Tenant/MedicineModal.vue'
import { Head, Link } from '@inertiajs/inertia-vue3'
import { useToast } from 'vue-toastification'
import {
  DotsHorizontalIcon,
  PencilAltIcon,
  TrashIcon,
} from '@heroicons/vue/solid'

export default defineComponent({
  mixins: [Destory],
  components: {
    TenantLayout,
    Table,
    JetDropdown,
    JetBanner,
    MedicineModal,
    Head,
    Link,
    DotsHorizontalIcon,
    PencilAltIcon,
    TrashIcon,
  },
  props: {
    medicines: Object,
  },
  setup() {
    const { tm } = useI18n({
      inheritLocale: true,
      messages: lang,
    })
    const toast = useToast()
    return { tm, toast }
  },
  data() {
    return {
      w60: 'w-60',
      showModal: false,
      selectedMedicine: null,
    }
  },
  mounted() {
    // Check if URL parameters request opening the modal
    if (typeof window !== 'undefined') {
      const params = new URLSearchParams(window.location.search)
      if (params.get('create')) {
        this.openCreateModal()
      }
    }
  },
  methods: {
    openCreateModal() {
      this.selectedMedicine = null
      this.showModal = true
    },
    openEditModal(medicine) {
      this.selectedMedicine = { ...medicine }
      this.showModal = true
    },
    closeModal() {
      this.showModal = false
      this.selectedMedicine = null
    },
  },
})
</script>
