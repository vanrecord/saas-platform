<template>
  <TenantLayout>
    <Head :title="tm('Clinic_Target') || 'Clinic Target'" />
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
                {{ tm('Target') || 'Target' }}
              </span>
            </div>
          </li>
          <li>
            <div class="flex items-center">
              <svg class="w-6 h-6 text-gray-400" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path>
              </svg>
              <span class="ml-1 text-sm font-medium text-gray-700 hover:text-gray-900 md:ml-2 dark:text-gray-400 dark:hover:text-white">
                {{ tm('Clinic') || 'Clinic' }}
              </span>
            </div>
          </li>
        </ol>
      </nav>

      <!-- Main Card -->
      <div class="bg-white rounded-md dark:bg-darker shadow-sm">
        <div class="flex items-center justify-between p-4 border-b dark:border-primary">
          <h4 class="text-lg font-semibold text-gray-600 dark:text-light">
            {{ tm('Clinic_Target') || 'Clinic Target' }}
          </h4>
          <div class="flex items-center space-x-2">
            <!-- Create Clinic Target Button -->
            <button
              v-if="can(['all', 'target-clinic:create', 'target-clinic:save', 'target:clinic:create', 'targets:clinic:create'])"
              type="button"
              @click="openCreateModal"
              class="inline-flex items-center px-4 py-2 bg-primary hover:bg-primary-dark border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest active:bg-gray-900 focus:outline-none focus:border-gray-900 focus:ring focus:ring-primary focus:ring-offset-1 dark:focus:ring-offset-darker shadow-sm transition cursor-pointer"
            >
              + {{ tm('Create_Clinic_Target') || 'Create Clinic Target' }}
            </button>
          </div>
        </div>

        <div class="p-4">
          <Table :resource="targets">
            <!-- 1. Action Column (Edit, Delete) - Hidden if is_verified -->
            <template #cell(actions)="{ item: target }">
              <div v-if="!target.is_verified" class="flex items-center gap-1.5">
                <!-- Edit Action -->
                <button
                  v-if="can(['all', 'target-clinic:edit', 'target-clinic:save', 'target:clinic:edit', 'targets:clinic:edit'])"
                  type="button"
                  @click="openEditModal(target)"
                  class="inline-flex items-center gap-1 px-2 py-1 text-xs font-semibold text-indigo-700 hover:text-indigo-900 bg-indigo-50 hover:bg-indigo-100 dark:bg-indigo-950/40 dark:text-indigo-300 rounded transition cursor-pointer"
                  title="Edit Clinic Target"
                >
                  <PencilAltIcon class="w-3.5 h-3.5 text-indigo-600 dark:text-indigo-400" />
                  <span>{{ tm('Edit') || 'Edit' }}</span>
                </button>

                <!-- Delete Action -->
                <button
                  v-if="can(['all', 'target-clinic:delete', 'target:clinic:delete', 'targets:clinic:delete'])"
                  type="button"
                  @click="destroy(route('targets.clinic.destroy', target.id))"
                  class="inline-flex items-center gap-1 px-2 py-1 text-xs font-semibold text-red-700 hover:text-red-900 bg-red-50 hover:bg-red-100 dark:bg-red-950/40 dark:text-red-300 rounded transition cursor-pointer"
                  title="Delete Clinic Target"
                >
                  <TrashIcon class="w-3.5 h-3.5 text-red-600 dark:text-red-400" />
                  <span>{{ tm('Delete') || 'Delete' }}</span>
                </button>
              </div>
              <span v-else class="text-xs text-slate-400 italic">
                {{ tm('Verified') || 'Verified' }}
              </span>
            </template>

            <!-- 2. Start Date Column -->
            <template #cell(start_date)="{ item: target }">
              <span class="text-xs font-medium text-slate-800 dark:text-slate-200">
                {{ target.start_date_formatted || target.start_date || '-' }}
              </span>
            </template>

            <!-- 3. End Date Column -->
            <template #cell(end_date)="{ item: target }">
              <span class="text-xs font-medium text-slate-800 dark:text-slate-200">
                {{ target.end_date_formatted || target.end_date || '-' }}
              </span>
            </template>

            <!-- 4. Target ($) Column -->
            <template #cell(amount)="{ item: target }">
              <span class="font-mono text-xs font-bold text-slate-900 dark:text-slate-100">
                {{ target.amount_formatted || '$' + Number(target.amount || 0).toFixed(2) }}
              </span>
            </template>

            <!-- 5. Income ($) Column -->
            <template #cell(income)="{ item: target }">
              <span class="font-mono text-xs font-semibold text-emerald-600 dark:text-emerald-400">
                {{ target.income_formatted || '$' + Number(target.income || 0).toFixed(2) }}
              </span>
            </template>

            <!-- 6. Result as % of Target Column -->
            <template #cell(result)="{ item: target }">
              <span
                :class="[
                  'inline-flex items-center px-2 py-0.5 rounded-full text-xs font-mono font-bold',
                  (target.result_percentage >= 100)
                    ? 'bg-emerald-100 text-emerald-800 dark:bg-emerald-950/50 dark:text-emerald-300'
                    : (target.result_percentage >= 50)
                      ? 'bg-blue-100 text-blue-800 dark:bg-blue-950/50 dark:text-blue-300'
                      : 'bg-amber-100 text-amber-800 dark:bg-amber-950/50 dark:text-amber-300'
                ]"
              >
                {{ target.result || '0.00%' }}
              </span>
            </template>

            <!-- 7. Description Column -->
            <template #cell(description)="{ item: target }">
              <span class="text-xs text-slate-600 dark:text-slate-400 max-w-sm truncate block" :title="target.description || ''">
                {{ target.description || '-' }}
              </span>
            </template>
          </Table>
        </div>
      </div>
    </div>

    <!-- Clinic Target Create/Edit Modal -->
    <TargetModal
      :show="showModal"
      :target="selectedTarget"
      :by-doctor="false"
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
import JetBanner from '@/Jetstream/Banner.vue'
import Destory from '@/Libs/Action/Delete.vue'
import TargetModal from '@/Components/Tenant/TargetModal.vue'
import { Head, Link } from '@inertiajs/inertia-vue3'
import {
  PencilAltIcon,
  TrashIcon,
} from '@heroicons/vue/solid'

export default defineComponent({
  name: 'ClinicTargetIndex',
  mixins: [Destory],
  components: {
    TenantLayout,
    Table,
    JetBanner,
    TargetModal,
    Head,
    Link,
    PencilAltIcon,
    TrashIcon,
  },
  props: {
    targets: Object,
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
      selectedTarget: null,
    }
  },
  methods: {
    openCreateModal() {
      this.selectedTarget = null
      this.showModal = true
    },
    openEditModal(target) {
      this.selectedTarget = target
      this.showModal = true
    },
    closeModal() {
      this.showModal = false
      this.selectedTarget = null
    },
  },
})
</script>
