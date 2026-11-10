<template>
  <TenantLayout>
    <Head :title="tm('Roles_and_Permissions')" />
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
                {{ tm('Roles_and_Permissions') }}
              </span>
            </div>
          </li>
        </ol>
      </nav>

      <div class="col-span-2 bg-white rounded-md dark:bg-darker shadow-sm">
        <div class="flex flex-col md:flex-row md:items-center justify-between p-4 border-b dark:border-primary gap-4">
          <div>
            <h4 class="text-lg font-semibold text-gray-800 dark:text-light">
              {{ tm('Roles_and_Permissions') }}
            </h4>
            <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">
              {{ tm('Manage_roles_and_control_access_to_application_menus') }}
            </p>
          </div>
          <div class="flex items-center space-x-2">
            <!-- Create Button -->
            <Link
              v-if="can(['all', 'role:create'])"
              preserve-scroll
              :href="route('roles.create')"
              class="inline-flex items-center px-4 py-2 bg-primary hover:bg-primary-dark border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest active:bg-gray-900 focus:outline-none focus:border-gray-900 focus:ring-gray-300 disabled:opacity-25 transition focus:ring-2 focus:ring-primary focus:ring-offset-1 dark:focus:ring-offset-darker"
            >
              + {{ tm('Create_Role') }}
            </Link>
          </div>
        </div>

        <div class="p-4">
          <Table :resource="roles">
            <!-- ID Column -->
            <template #cell(id)="{ item: role }">
              <span class="font-mono text-xs font-bold text-slate-700 dark:text-slate-200">
                #{{ role.id }}
              </span>
            </template>

            <!-- Role Name Column -->
            <template #cell(name)="{ item: role }">
              <span class="font-semibold text-slate-800 dark:text-slate-100">
                {{ role.name }}
              </span>
            </template>

            <!-- Actions Column -->
            <template #cell(actions)="{ item: role }">
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
                        <!-- Edit -->
                        <div
                          v-if="can(['all', 'role:edit'])"
                          class="hover:bg-indigo-100 hover:text-indigo-500 dark:hover:text-gray-200 dark:hover:bg-indigo-500 dark:hover:bg-opacity-75 cursor-pointer text-sm px-2 radius-3"
                        >
                          <jet-nav-link :href="route('roles.edit', role.id)">
                            <div class="flex items-center h-6">
                              <PencilAltIcon class="h-4 w-4 text-indigo-500 text-right mr-1" />{{ tm('edit') }}
                            </div>
                          </jet-nav-link>
                        </div>
                        <!-- Delete -->
                        <div
                          v-if="can(['all', 'role:delete'])"
                          class="hover:bg-red-100 hover:text-red-500 dark:hover:text-gray-200 dark:hover:bg-red-500 dark:hover:bg-opacity-75 cursor-pointer text-sm px-2 radius-3"
                        >
                          <div class="flex items-center h-6" @click="destroy(route('roles.destroy', role.id))">
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
  PencilAltIcon,
  TrashIcon
} from '@heroicons/vue/solid'

export default defineComponent({
  props: {
    roles: Object,
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
  methods: {
    destroy(url) {
      this.$swal({
        title: this.tm('Are_you_sure_you_want_to_delete_this_role'),
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
              this.toast.success(this.tm('Role_deleted_successfully'))
            },
            onError: (errors) => {
              this.toast.error(errors?.message || 'Failed to delete role.')
            }
          })
        }
      })
    }
  }
})
</script>
