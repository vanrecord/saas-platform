<template>
  <TenantLayout>
    <Head :title="tm('Services')" />
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
              {{ tm('Home') }}
            </Link>
          </li>
          <li>
            <div class="flex items-center">
              <svg class="w-6 h-6 text-gray-400" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path>
              </svg>
              <span class="ml-1 text-sm font-medium text-gray-700 hover:text-gray-900 md:ml-2 dark:text-gray-400 dark:hover:text-white">
                {{ tm('Hospital') }}
              </span>
            </div>
          </li>
          <li>
            <div class="flex items-center">
              <svg class="w-6 h-6 text-gray-400" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path>
              </svg>
              <span class="ml-1 text-sm font-medium text-gray-700 hover:text-gray-900 md:ml-2 dark:text-gray-400 dark:hover:text-white">
                {{ tm('Services') }}
              </span>
            </div>
          </li>
        </ol>
      </nav>

      <div class="col-span-2 bg-white rounded-md dark:bg-darker shadow-sm">
        <div class="flex items-center justify-between p-4 border-b dark:border-primary">
          <h4 class="text-lg font-semibold text-gray-500 dark:text-light">
            {{ tm('Services') }}
          </h4>
          <div class="flex items-center space-x-2">
            <!-- Create Button -->
            <Link
              v-if="can(['all', 'service:create', 'services', 'services.create', 'services:services:create', 'services:create'])"
              preserve-scroll
              :href="route('services.create')"
              class="inline-flex items-center px-4 py-2 bg-primary hover:bg-primary-dark border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest active:bg-gray-900 focus:outline-none focus:border-gray-900 focus:ring-gray-300 disabled:opacity-25 transition focus:ring-2 focus:ring-primary focus:ring-offset-1 dark:focus:ring-offset-darker"
            >
              + {{ tm('Create_Service') }}
            </Link>
          </div>
        </div>

        <div class="p-4">
          <Table :resource="services">
            <!-- No. (Service code) -->
            <template #cell(code)="{ item: service }">
              <span class="font-mono text-xs font-bold text-slate-700 dark:text-slate-200">
                #{{ service.code || service.id }}
              </span>
            </template>

            <!-- Name -->
            <template #cell(name)="{ item: service }">
              <Link :href="route('services.show', service.id)" class="font-semibold text-primary hover:underline">
                {{ service.name }}
              </Link>
            </template>

            <!-- Category -->
            <template #cell(category_name)="{ item: service }">
              <span v-if="service.category" class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-blue-50 text-blue-700 dark:bg-blue-900/30 dark:text-blue-300">
                {{ service.category.name }}
              </span>
              <span v-else class="text-xs text-slate-400">
                —
              </span>
            </template>

            <!-- Price -->
            <template #cell(price)="{ item: service }">
              <span class="font-mono text-sm font-semibold text-slate-800 dark:text-slate-100">
                ${{ Number(service.price || 0).toFixed(2) }}
              </span>
            </template>

            <!-- Discount -->
            <template #cell(discount)="{ item: service }">
              <span v-if="service.discount && Number(service.discount) > 0" class="font-mono text-xs text-emerald-600 dark:text-emerald-400">
                ${{ Number(service.discount).toFixed(2) }}
              </span>
              <span v-else class="text-xs text-slate-400">
                —
              </span>
            </template>

            <!-- Cost -->
            <template #cell(cost)="{ item: service }">
              <span class="font-mono text-xs text-slate-600 dark:text-slate-300">
                ${{ Number(service.cost || 0).toFixed(2) }}
              </span>
            </template>

            <!-- Actions -->
            <template #cell(actions)="{ item: service }">
              <jet-dropdown align="right" width="48">
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
                        <div v-if="can(['all', 'service:view', 'services', 'services.show'])" class="hover:bg-blue-100 hover:text-blue-500 dark:hover:text-gray-200 dark:hover:bg-blue-500 dark:hover:bg-opacity-75 cursor-pointer text-sm px-2 radius-3">
                          <jet-nav-link :href="route('services.show', service.id)">
                            <div class="flex items-center h-6">
                              <EyeIcon class="h-4 w-4 text-blue-500 text-right mr-1" />{{ tm('View') }}
                            </div>
                          </jet-nav-link>
                        </div>
                        <!-- Edit -->
                        <div
                          v-if="can(['all', 'service:edit', 'services', 'services.edit', 'services:services:edit', 'services:edit'])"
                          class="hover:bg-indigo-100 hover:text-indigo-500 dark:hover:text-gray-200 dark:hover:bg-indigo-500 dark:hover:bg-opacity-75 cursor-pointer text-sm px-2 radius-3"
                        >
                          <jet-nav-link :href="route('services.edit', service.id)">
                            <div class="flex items-center h-6">
                              <PencilAltIcon class="h-4 w-4 text-indigo-500 text-right mr-1" />{{ tm('edit') }}
                            </div>
                          </jet-nav-link>
                        </div>
                        <!-- Delete -->
                        <div
                          v-if="can(['all', 'service:delete', 'services', 'services.destroy', 'services:services:delete', 'services:delete'])"
                          class="hover:bg-red-100 hover:text-red-500 dark:hover:text-gray-200 dark:hover:bg-red-500 dark:hover:bg-opacity-75 cursor-pointer text-sm px-2 radius-3"
                        >
                          <div class="flex items-center h-6" @click="destroy(route('services.destroy', service.id))">
                            <TrashIcon class="h-4 w-4 text-red-500 text-right mr-1" />{{ tm('delete') }}
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
import { Table } from '@/Components/Datatables'
import { useToast } from 'vue-toastification'
import { useI18n } from 'vue-i18n'
import { lang } from '@/Lang'
import {
  DotsHorizontalIcon,
  EyeIcon,
  PencilAltIcon,
  TrashIcon
} from '@heroicons/vue/solid'

export default defineComponent({
  props: {
    services: Object,
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
    EyeIcon,
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
  methods: {
    destroy(url) {
      this.$swal({
        title: this.tm('Are_you_sure_to_delete_this_service'),
        text: this.tm('You_wont_be_able_to_revert_this'),
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: this.tm('Yes_delete_it'),
        cancelButtonText: this.tm('Cancel'),
      }).then((result) => {
        if (result.isConfirmed) {
          this.$inertia.delete(url, {
            preserveScroll: true,
            onSuccess: () => {
              this.toast.success(this.tm('Service_deleted_successfully'))
            },
            onError: (errors) => {
              this.toast.error(errors?.message || 'Failed to delete service.')
            }
          })
        }
      })
    }
  }
})
</script>
