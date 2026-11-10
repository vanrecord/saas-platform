<template>
  <TenantLayout>
    <Head :title="tm('Doctors') || 'Doctors'" />
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
                {{ tm('Doctors') || 'Doctors' }}
              </span>
            </div>
          </li>
        </ol>
      </nav>

      <div class="col-span-2 bg-white rounded-md dark:bg-darker shadow-sm">
        <div class="flex items-center justify-between p-4 border-b dark:border-primary">
          <h4 class="text-lg font-semibold text-gray-500 dark:text-light">
            {{ tm('Doctors') || 'Doctors' }}
          </h4>
          <div class="flex items-center space-x-2">
            <!-- Create Doctor Button -->
            <Link
              v-if="can(['all', 'doctor:create', 'doctors.create'])"
              preserve-scroll
              :href="route('doctors.create')"
              class="inline-flex items-center px-4 py-2 bg-primary hover:bg-primary-dark border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest active:bg-gray-900 focus:outline-none focus:border-gray-900 focus:ring-gray-300 disabled:opacity-25 transition focus:ring-2 focus:ring-primary focus:ring-offset-1 dark:focus:ring-offset-darker"
            >
              + {{ tm('Create_Doctor') || 'Create Doctor' }}
            </Link>
          </div>
        </div>

        <div class="p-4">
          <Table :resource="doctors">
            <!-- 1. Sequence / ID -->
            <template #cell(id)="{index: key}">
              {{ ++key }}
            </template>

            <!-- 2. Avatar -->
            <template #cell(avatar)="{ item: doctor }">
              <div class="flex items-center justify-center">
                <img
                  v-if="doctor.avatar_url || doctor.user?.profile?.avatar_url"
                  class="h-9 w-9 rounded-full object-cover border border-slate-200 shadow-xs"
                  :src="doctor.avatar_url || doctor.user?.profile?.avatar_url"
                  :alt="doctor.full_name"
                >
                <div v-else class="h-9 w-9 rounded-full bg-slate-100 border border-slate-200 flex items-center justify-center text-slate-500 font-bold text-xs uppercase">
                  {{ (doctor.user?.profile?.firstname?.[0] || '') + (doctor.user?.profile?.lastname?.[0] || doctor.user?.name?.[0] || 'D') }}
                </div>
              </div>
            </template>

            <!-- 3. Fullname -->
            <template #cell(fullname)="{ item: doctor }">
              <Link :href="route('doctors.edit', doctor.id)" class="font-semibold text-primary hover:underline">
                {{ doctor.full_name || (doctor.user?.profile?.firstname ? doctor.user.profile.firstname + ' ' + doctor.user.profile.lastname : doctor.user?.name) || '—' }}
              </Link>
            </template>

            <!-- 4. Email -->
            <template #cell(email)="{ item: doctor }">
              <span class="text-xs text-slate-600">
                {{ doctor.email || doctor.user?.email || '-' }}
              </span>
            </template>

            <!-- 5. Gender -->
            <template #cell(gender)="{ item: doctor }">
              <span class="capitalize text-xs text-slate-700">
                {{ doctor.gender || doctor.user?.profile?.gender || '-' }}
              </span>
            </template>

            <!-- 6. Actions -->
            <template #cell(actions)="{ item: doctor }">
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
                        <!-- Edit -->
                        <div
                          v-if="can(['all', 'doctor:edit', 'doctors.edit'])"
                          class="hover:bg-indigo-100 hover:text-indigo-500 dark:hover:text-gray-200 dark:hover:bg-indigo-500 dark:hover:bg-opacity-75 cursor-pointer text-sm px-2 radius-3"
                        >
                          <jet-nav-link :href="route('doctors.edit', doctor.id)">
                            <div class="flex items-center h-6">
                              <PencilAltIcon class="h-4 w-4 text-indigo-500 text-right mr-1" />{{ tm('edit') || 'Edit' }}
                            </div>
                          </jet-nav-link>
                        </div>
                        <!-- Delete -->
                        <div
                          v-if="can(['all', 'doctor:delete', 'doctors.destroy'])"
                          class="hover:bg-red-100 hover:text-red-500 dark:hover:text-gray-200 dark:hover:bg-red-500 dark:hover:bg-opacity-75 cursor-pointer text-sm px-2 radius-3"
                        >
                          <div class="flex items-center h-6" @click="destroy(route('doctors.destroy', doctor.id))">
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
  </TenantLayout>
</template>

<script>
import { defineComponent } from 'vue'
import { Head, Link } from '@inertiajs/inertia-vue3'
import TenantLayout from '@/Layouts/TenantLayout.vue'
import JetBanner from '@/Jetstream/Banner.vue'
import JetDropdown from '@/Jetstream/Dropdown.vue'
import JetNavLink from '@/Jetstream/NavLink.vue'
import Destory from '@/Libs/Action/Delete.vue'
import { Table } from '@/Components/Datatables'
import { useToast } from 'vue-toastification'
import { useI18n } from 'vue-i18n'
import { lang } from '@/Lang'
import {
  DotsHorizontalIcon,
  PencilAltIcon,
  TrashIcon,
} from '@heroicons/vue/solid'

export default defineComponent({
  mixins: [Destory],
  props: {
    doctors: {
      type: Object,
      required: true,
    },
  },
  components: {
    TenantLayout,
    Head,
    Link,
    JetBanner,
    JetDropdown,
    JetNavLink,
    Table,
    DotsHorizontalIcon,
    PencilAltIcon,
    TrashIcon,
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
    }
  },
})
</script>
