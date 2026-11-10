<template>
  <TenantLayout>
    <Head title="Patients" />
    <div class="px-4 py-4">
      <jet-banner />
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
                {{ tm('Patients') || 'Patients' }}
              </span>
            </div>
          </li>
        </ol>
      </nav>

      <div class="col-span-2 bg-white rounded-md dark:bg-darker shadow-sm">
        <div class="flex items-center justify-between p-4 border-b dark:border-primary">
          <h4 class="text-lg font-semibold text-gray-500 dark:text-light">{{ tm('Patients') || 'Patients' }}</h4>
          <div class="flex items-center space-x-2">
            <!-- e-Health Modal Button -->
            <button
              type="button"
              @click="showEHealthModal = true"
              class="inline-flex items-center gap-1.5 px-3 py-2 rounded-md bg-[#00a65a] hover:bg-[#008d4c] text-white text-xs font-semibold shadow-sm transition-colors"
            >
              <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <rect x="3" y="3" width="7" height="7"></rect>
                <rect x="14" y="3" width="7" height="7"></rect>
                <rect x="3" y="14" width="7" height="7"></rect>
                <rect x="14" y="14" width="7" height="7"></rect>
                <path d="M7 7h.01M17 7h.01M7 17h.01M17 17h.01"></path>
              </svg>
              <span>e-Health</span>
            </button>

            <!-- Create Patient Button -->
            <Link
              v-if="can(['all', 'patient:create', 'patients.create'])"
              preserve-scroll
              :href="route('patients.create')"
              class="inline-flex items-center px-4 py-2 bg-primary hover:bg-primary-dark border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest active:bg-gray-900 focus:outline-none focus:border-gray-900 focus:ring-gray-300 disabled:opacity-25 transition focus:ring-2 focus:ring-primary focus:ring-offset-1 dark:focus:ring-offset-darker"
            >
              + {{ tm('Create_Patient') || 'Create Patient' }}
            </Link>
          </div>
        </div>

        <div class="p-4">
          <Table :resource="patients">
            <template #cell(id)="{index: key}">
              {{ ++key }}
            </template>
            <template #cell(avatar)="{ item: patient }">
              <div class="flex items-center justify-center">
                <img v-if="patient.avatar_url" class="h-9 w-9 rounded-full object-cover border border-slate-200 shadow-xs" :src="patient.avatar_url" :alt="patient.full_name">
                <div v-else class="h-9 w-9 rounded-full bg-slate-100 border border-slate-200 flex items-center justify-center text-slate-500 font-bold text-xs uppercase">
                  {{ (patient.firstname?.[0] || '') + (patient.lastname?.[0] || 'P') }}
                </div>
              </div>
            </template>
            <template #cell(id_number)="{ item: patient }">
              <span class="font-mono font-bold text-slate-800">
                #{{ patient.id_number || patient.id }}
              </span>
            </template>
            <template #cell(firstname)="{ item: patient }">
              <Link :href="route('patients.show', patient.id)" class="font-semibold text-primary hover:underline">
                {{ patient.firstname }}
              </Link>
            </template>
            <template #cell(lastname)="{ item: patient }">
              <Link :href="route('patients.show', patient.id)" class="font-semibold text-primary hover:underline">
                {{ patient.lastname }}
              </Link>
            </template>
            <template #cell(gender)="{ item: patient }">
              <span class="capitalize text-xs text-slate-700">
                {{ patient.gender === 'M' || patient.gender === 'male' ? 'Male' : (patient.gender === 'F' || patient.gender === 'female' ? 'Female' : (patient.gender || '-')) }}
              </span>
            </template>
            <template #cell(age)="{ item: patient }">
              <span class="text-xs text-slate-700">
                {{ patient.age !== null ? `${patient.age} yrs` : '-' }}
              </span>
            </template>
            <template #cell(dob)="{ item: patient }">
              <span class="text-xs text-slate-500 font-mono">
                {{ patient.dob || '-' }}
              </span>
            </template>
            <template #cell(phone)="{ item: patient }">
              <span class="text-xs text-slate-700 font-mono">
                {{ patient.phone || '-' }}
              </span>
            </template>
            <template #cell(email)="{ item: patient }">
              <span class="text-xs text-slate-600">
                {{ patient.email || '-' }}
              </span>
            </template>
            <template #cell(created_at)="{ item: patient }">
              <span class="text-xs text-slate-500">
                {{ patient.created_at ? new Date(patient.created_at).toLocaleDateString() : '-' }}
              </span>
            </template>
            <template #cell(actions)="{ item: patient }">
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
                        <!-- View -->
                        <div v-if="can(['all', 'patient:view', 'patients.show'])" class="hover:bg-blue-100 hover:text-blue-500 dark:hover:text-gray-200 dark:hover:bg-blue-500 dark:hover:bg-opacity-75 cursor-pointer text-sm px-2 radius-3">
                          <jet-nav-link :href="route('patients.show', patient.id)">
                            <div class="flex items-center h-6">
                              <EyeIcon class="h-4 w-4 text-blue-500 text-right mr-1" />{{ tm('View') || 'View' }}
                            </div>
                          </jet-nav-link>
                        </div>
                        <!-- Edit -->
                        <div v-if="can(['all', 'patient:edit', 'patients.edit'])" class="hover:bg-indigo-100 hover:text-indigo-500 dark:hover:text-gray-200 dark:hover:bg-indigo-500 dark:hover:bg-opacity-75 cursor-pointer text-sm px-2 radius-3">
                          <jet-nav-link :href="route('patients.edit', patient.id)">
                            <div class="flex items-center h-6">
                              <PencilAltIcon class="h-4 w-4 text-indigo-500 text-right mr-1" />{{ tm('edit') || 'Edit' }}
                            </div>
                          </jet-nav-link>
                        </div>
                        <!-- Delete -->
                        <div v-if="can(['all', 'patient:delete', 'patients.destroy'])" class="hover:bg-red-100 hover:text-red-500 dark:hover:text-gray-200 dark:hover:bg-red-500 dark:hover:bg-opacity-75 cursor-pointer text-sm px-2 radius-3">
                          <div class="flex items-center h-6" @click="destroy(route('patients.destroy', patient.id))">
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

    <!-- e-Health Modal -->
    <div v-if="showEHealthModal" class="fixed inset-0 z-50 overflow-y-auto flex items-center justify-center p-4">
      <div class="fixed inset-0 bg-slate-900/50 backdrop-blur-sm" @click="showEHealthModal = false"></div>
      <div class="relative bg-white rounded-xl max-w-md w-full p-6 shadow-2xl border border-slate-200 space-y-4">
        <div class="flex items-center justify-between border-b border-slate-100 pb-3">
          <div class="flex items-center gap-2 text-[#00a65a]">
            <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
              <rect x="3" y="3" width="7" height="7"></rect>
              <rect x="14" y="3" width="7" height="7"></rect>
              <rect x="3" y="14" width="7" height="7"></rect>
              <rect x="14" y="14" width="7" height="7"></rect>
            </svg>
            <h3 class="text-sm font-bold text-slate-900">e-Health Card Reader</h3>
          </div>
          <button type="button" @click="showEHealthModal = false" class="text-slate-400 hover:text-slate-600">✕</button>
        </div>
        <p class="text-xs text-slate-500">Scan an e-Health QR code or patient membership card to instantly pull identification records.</p>
        <div class="flex justify-end pt-2">
          <button type="button" @click="showEHealthModal = false" class="px-4 py-2 rounded text-xs font-bold text-white bg-[#00a65a] hover:bg-[#008d4c] transition-colors">
            Close
          </button>
        </div>
      </div>
    </div>
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
import JetNavLink from '@/Jetstream/NavLink.vue'
import Destory from '@/Libs/Action/Delete.vue'
import { Link } from '@inertiajs/inertia-vue3'
import { useToast } from 'vue-toastification'
import {
  DotsHorizontalIcon,
  PencilAltIcon,
  TrashIcon,
  EyeIcon,
} from '@heroicons/vue/solid'

export default defineComponent({
  mixins: [Destory],
  components: {
    TenantLayout,
    Table,
    JetDropdown,
    JetBanner,
    JetNavLink,
    Link,
    DotsHorizontalIcon,
    PencilAltIcon,
    TrashIcon,
    EyeIcon,
  },
  props: {
    patients: Object,
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
      showEHealthModal: false,
    }
  },
})
</script>
