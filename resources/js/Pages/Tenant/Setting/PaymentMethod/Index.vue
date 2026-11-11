<template>
  <TenantLayout>
    <Head :title="tm('Payment_Methods') || 'Payment Methods'" />
    <div class="px-4 py-4">
      <jet-banner />

      <!-- Breadcrumbs -->
      <nav class="flex pt-4 mb-6" aria-label="Breadcrumb">
        <ol class="inline-flex items-center space-x-1 md:space-x-3">
          <li class="inline-flex items-center">
            <Link
              :href="route('tenant.dashboard')"
              class="inline-flex items-center text-sm font-medium text-gray-700 hover:text-gray-900 dark:text-gray-400 dark:hover:text-white"
            >
              <svg class="mr-2 w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                <path d="M10.707 2.293a1 1 0 00-1.414 0l-7 7a1 1 0 001.414 1.414L4 10.414V17a1 1 0 001 1h2a1 1 0 001-1v-2a1 1 0 011-1h2a1 1 0 011 1v2a1 1 0 001 1h2a1 1 0 001-1v-6.586l.293.293a1 1 0 001.414-1.414l-7-7z"></path>
              </svg>
              {{ tm('Home') || 'Home' }}
            </Link>
          </li>
          <li>
            <div class="flex items-center">
              <svg class="w-6 h-6 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path>
              </svg>
              <span class="ml-1 text-sm font-medium text-gray-500 md:ml-2 dark:text-gray-400">
                {{ tm('Settings') || 'Settings' }}
              </span>
            </div>
          </li>
          <li>
            <div class="flex items-center">
              <svg class="w-6 h-6 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path>
              </svg>
              <span class="ml-1 text-sm font-medium text-gray-700 hover:text-gray-900 md:ml-2 dark:text-gray-400 dark:hover:text-white">
                {{ tm('Payment_Methods') || 'Payment Methods' }}
              </span>
            </div>
          </li>
        </ol>
      </nav>

      <!-- Main Card -->
      <div class="bg-white rounded-md dark:bg-darker shadow-sm">
        <div class="flex items-center justify-between p-4 border-b dark:border-primary">
          <h4 class="text-lg font-semibold text-gray-500 dark:text-light">
            {{ tm('Payment_Methods') || 'Payment Methods' }}
          </h4>
          <div class="flex items-center space-x-2">
            <!-- Create Button -->
            <Link
              v-if="can(['all', 'payment-method:create', 'settings.payment-methods.create'])"
              preserve-scroll
              :href="route('settings.payment-methods.create')"
              class="inline-flex items-center px-4 py-2 bg-primary hover:bg-primary-dark border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest active:bg-gray-900 focus:outline-none focus:ring-2 focus:ring-primary focus:ring-offset-1 transition"
            >
              + {{ tm('Create_Payment_Method') || 'Create Payment Method' }}
            </Link>
          </div>
        </div>

        <div class="p-4">
          <Table :resource="paymentMethods">
            <!-- Action Column (on the left) -->
            <template #cell(actions)="{ item: method }">
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
                          v-if="can(['all', 'payment-method:edit', 'settings.payment-methods.edit'])"
                          class="hover:bg-indigo-100 hover:text-indigo-500 dark:hover:text-gray-200 dark:hover:bg-indigo-500 dark:hover:bg-opacity-75 cursor-pointer text-sm px-2 radius-3"
                        >
                          <jet-nav-link :href="route('settings.payment-methods.edit', method.id)">
                            <div class="flex items-center h-6">
                              <PencilAltIcon class="h-4 w-4 text-indigo-500 text-right mr-1" />
                              {{ tm('Edit') || 'Edit' }}
                            </div>
                          </jet-nav-link>
                        </div>
                        <!-- Delete -->
                        <div
                          v-if="can(['all', 'payment-method:delete', 'settings.payment-methods.destroy'])"
                          class="hover:bg-red-100 hover:text-red-500 dark:hover:text-gray-200 dark:hover:bg-red-500 dark:hover:bg-opacity-75 cursor-pointer text-sm px-2 radius-3"
                        >
                          <div class="flex items-center h-6" @click="destroy(route('settings.payment-methods.destroy', method.id))">
                            <TrashIcon class="h-4 w-4 text-red-500 text-right mr-1" />
                            {{ tm('Delete') || 'Delete' }}
                          </div>
                        </div>
                      </div>
                    </div>
                  </transition>
                </template>
              </jet-dropdown>
            </template>

            <!-- 1. Name Column (with QR thumbnail preview if available) -->
            <template #cell(name)="{ item: method }">
              <div class="flex items-center space-x-3">
                <div v-if="method.qr_code_url" class="flex-shrink-0 w-8 h-8 rounded border border-slate-200 overflow-hidden bg-slate-50">
                  <img :src="method.qr_code_url" :alt="method.name" class="w-full h-full object-cover" />
                </div>
                <div v-else class="flex-shrink-0 w-8 h-8 rounded border border-dashed border-slate-200 flex items-center justify-center bg-slate-50 text-slate-400 text-2xs">
                  <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <rect x="3" y="3" width="7" height="7" stroke-width="2" />
                    <rect x="14" y="3" width="7" height="7" stroke-width="2" />
                    <rect x="3" y="14" width="7" height="7" stroke-width="2" />
                    <rect x="14" y="14" width="7" height="7" stroke-width="2" />
                  </svg>
                </div>
                <div>
                  <Link
                    v-if="can(['all', 'payment-method:edit', 'settings.payment-methods.edit'])"
                    :href="route('settings.payment-methods.edit', method.id)"
                    class="font-semibold text-primary hover:underline text-xs"
                  >
                    {{ method.name }}
                  </Link>
                  <span v-else class="font-semibold text-slate-800 dark:text-slate-200 text-xs">
                    {{ method.name }}
                  </span>
                  <span v-if="method.provider" class="ml-1.5 px-1.5 py-0.5 rounded text-3xs font-mono font-bold bg-blue-50 text-[#0D63D6] border border-blue-200">
                    {{ method.provider }}
                  </span>
                </div>
              </div>
            </template>

            <!-- 2. Default Print Column -->
            <template #cell(print)="{ item: method }">
              <span
                :class="[
                  'inline-flex items-center px-2 py-0.5 rounded-full text-xs font-semibold',
                  method.print
                    ? 'bg-emerald-100 text-emerald-800 dark:bg-emerald-900/40 dark:text-emerald-300'
                    : 'bg-slate-100 text-slate-600 dark:bg-slate-700 dark:text-slate-300'
                ]"
              >
                {{ method.print ? (tm('Yes') || 'Yes') : (tm('No') || 'No') }}
              </span>
            </template>

            <!-- 3. Default Payment Column -->
            <template #cell(is_default_payment)="{ item: method }">
              <span
                :class="[
                  'inline-flex items-center px-2 py-0.5 rounded-full text-xs font-semibold',
                  method.is_default_payment
                    ? 'bg-blue-100 text-[#0D63D6] font-bold dark:bg-blue-900/40 dark:text-blue-300'
                    : 'bg-slate-100 text-slate-600 dark:bg-slate-700 dark:text-slate-300'
                ]"
              >
                {{ method.is_default_payment ? (tm('Yes') || 'Yes') : (tm('No') || 'No') }}
              </span>
            </template>

            <!-- 4. Sponsored Column -->
            <template #cell(is_sponsor)="{ item: method }">
              <span
                :class="[
                  'inline-flex items-center px-2 py-0.5 rounded-full text-xs font-semibold',
                  method.is_sponsor
                    ? 'bg-amber-100 text-amber-800 dark:bg-amber-900/40 dark:text-amber-300'
                    : 'bg-slate-100 text-slate-600 dark:bg-slate-700 dark:text-slate-300'
                ]"
              >
                {{ method.is_sponsor ? (tm('Yes') || 'Yes') : (tm('No') || 'No') }}
              </span>
            </template>

            <!-- 5. Online Payment Column -->
            <template #cell(is_activate)="{ item: method }">
              <span
                :class="[
                  'inline-flex items-center px-2 py-0.5 rounded-full text-xs font-semibold',
                  method.is_activate
                    ? 'bg-purple-100 text-purple-800 dark:bg-purple-900/40 dark:text-purple-300'
                    : 'bg-slate-100 text-slate-600 dark:bg-slate-700 dark:text-slate-300'
                ]"
              >
                {{ method.is_activate ? (tm('Enabled') || 'Enabled') : (tm('Disabled') || 'Disabled') }}
              </span>
            </template>

            <!-- 6. Active Column (Disable = false -> Active, Disable = true -> Inactive) -->
            <template #cell(is_disable)="{ item: method }">
              <span
                :class="[
                  'inline-flex items-center px-2 py-0.5 rounded-full text-xs font-semibold',
                  !method.is_disable
                    ? 'bg-emerald-100 text-emerald-800 dark:bg-emerald-900/40 dark:text-emerald-300'
                    : 'bg-red-100 text-red-800 dark:bg-red-900/40 dark:text-red-300'
                ]"
              >
                {{ !method.is_disable ? (tm('Active') || 'Active') : (tm('Inactive') || 'Inactive') }}
              </span>
            </template>

            <!-- 7. Description Column -->
            <template #cell(description)="{ item: method }">
              <span class="text-xs text-slate-600 dark:text-slate-400 line-clamp-1 max-w-xs" :title="method.description || '-'">
                {{ method.description || '-' }}
              </span>
            </template>

            <!-- 8. Order Column -->
            <template #cell(order)="{ item: method }">
              <span class="font-mono text-xs font-bold text-slate-700 dark:text-slate-300">
                {{ method.order ?? 0 }}
              </span>
            </template>
          </Table>
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
    Head,
    Link,
    JetDropdown,
    JetBanner,
    JetNavLink,
    DotsHorizontalIcon,
    PencilAltIcon,
    TrashIcon,
  },
  props: {
    paymentMethods: Object,
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
