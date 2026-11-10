<template>
  <TenantLayout>
    <Head title="Suppliers" />
    <div class="px-4 py-4">
      <jet-banner />
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
                {{ tm('Suppliers') }}
              </span>
            </div>
          </li>
        </ol>
      </nav>

      <div class="col-span-2 bg-white rounded-md dark:bg-darker shadow-sm">
        <div class="flex items-center justify-between p-4 border-b dark:border-primary">
          <h4 class="text-lg font-semibold text-gray-500 dark:text-light">
            {{ tm('Suppliers') }}
          </h4>
          <div class="flex items-center space-x-2">
            <!-- Create Supplier Button -->
            <Link
              v-if="can(['all', 'suppliers', 'suppliers.create', 'suppliers:suppliers:create', 'suppliers:create'])"
              preserve-scroll
              :href="route('suppliers.create')"
              class="inline-flex items-center px-4 py-2 bg-primary hover:bg-primary-dark border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest active:bg-gray-900 focus:outline-none focus:border-gray-900 focus:ring-gray-300 disabled:opacity-25 transition focus:ring-2 focus:ring-primary focus:ring-offset-1 dark:focus:ring-offset-darker"
            >
              + {{ tm('Create_Supplier') }}
            </Link>
          </div>
        </div>

        <div class="p-4">
          <Table :resource="suppliers">
            <template #cell(id)="{index: key}">
              {{ ++key }}
            </template>
            <template #cell(name)="{ item: supplier }">
              <Link :href="route('suppliers.show', supplier.id)" class="font-semibold text-primary hover:underline">
                {{ supplier.name }}
              </Link>
            </template>
            <template #cell(phone)="{ item: supplier }">
              <span class="text-sm text-slate-700 font-mono">
                {{ supplier.phone || '-' }}
              </span>
            </template>
            <template #cell(email)="{ item: supplier }">
              <span class="text-sm text-slate-700">
                {{ supplier.email || '-' }}
              </span>
            </template>
            <template #cell(contact_person)="{ item: supplier }">
              <span class="text-sm text-slate-700">
                {{ supplier.contact_person || '-' }}
              </span>
            </template>
            <template #cell(created_at)="{ item: supplier }">
              <span class="text-xs text-slate-500">
                {{ supplier.created_at ? supplier.created_at.substring(0, 10) : '-' }}
              </span>
            </template>
            <template #cell(actions)="{ item: supplier }">
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
                        <div class="hover:bg-blue-100 hover:text-blue-500 dark:hover:text-gray-200 dark:hover:bg-blue-500 dark:hover:bg-opacity-75 cursor-pointer text-sm px-2 radius-3">
                          <jet-nav-link :href="route('suppliers.show', supplier.id)">
                            <div class="flex items-center h-6">
                              <EyeIcon class="h-4 w-4 text-blue-500 text-right mr-1" />{{ tm('View') }}
                            </div>
                          </jet-nav-link>
                        </div>
                        <!-- Edit -->
                        <div
                          v-if="can(['all', 'suppliers', 'suppliers.edit', 'suppliers:suppliers:edit', 'suppliers:edit'])"
                          class="hover:bg-indigo-100 hover:text-indigo-500 dark:hover:text-gray-200 dark:hover:bg-indigo-500 dark:hover:bg-opacity-75 cursor-pointer text-sm px-2 radius-3"
                        >
                          <jet-nav-link :href="route('suppliers.edit', supplier.id)">
                            <div class="flex items-center h-6">
                              <PencilAltIcon class="h-4 w-4 text-indigo-500 text-right mr-1" />{{ tm('edit') }}
                            </div>
                          </jet-nav-link>
                        </div>
                        <!-- Delete -->
                        <div
                          v-if="can(['all', 'suppliers', 'suppliers.destroy', 'suppliers:suppliers:delete', 'suppliers:delete'])"
                          class="hover:bg-red-100 hover:text-red-500 dark:hover:text-gray-200 dark:hover:bg-red-500 dark:hover:bg-opacity-75 cursor-pointer text-sm px-2 radius-3"
                        >
                          <div class="flex items-center h-6" @click="destroy(route('suppliers.destroy', supplier.id))">
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
    suppliers: Object,
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
    TrashIcon
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
        title: this.tm('Are_you_sure_to_delete'),
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
              this.toast.success(this.tm('Deleted_successfully'))
            },
            onError: (errors) => {
              this.toast.error(errors?.message || 'Failed to delete supplier.')
            }
          })
        }
      })
    }
  }
})
</script>
