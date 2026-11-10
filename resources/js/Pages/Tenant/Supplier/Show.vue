<template>
  <TenantLayout>
    <Head :title="`Supplier - ${supplier.name}`" />

    <div class="px-4 py-4">
      <jet-banner />

      <!-- Breadcrumb -->
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
              <Link :href="route('suppliers.index')" class="ml-1 text-sm font-medium text-gray-700 hover:text-gray-900 md:ml-2 dark:text-gray-400 dark:hover:text-white">
                {{ tm('Suppliers') }}
              </Link>
            </div>
          </li>
          <li aria-current="page">
            <div class="flex items-center">
              <svg class="w-6 h-6 text-gray-400" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path>
              </svg>
              <span class="ml-1 text-sm font-medium text-gray-500 md:ml-2 dark:text-gray-400">
                {{ supplier.name }}
              </span>
            </div>
          </li>
        </ol>
      </nav>

      <!-- Main Container -->
      <div class="bg-white rounded-md dark:bg-darker shadow-sm overflow-hidden">
        <!-- Header & Actions -->
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between p-4 border-b dark:border-primary gap-3">
          <div class="flex items-center space-x-3">
            <div class="p-2 bg-primary/10 rounded-lg text-primary">
              <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
              </svg>
            </div>
            <div>
              <h2 class="text-lg font-bold text-gray-800 dark:text-light">
                {{ supplier.name }}
              </h2>
              <p class="text-xs text-gray-500 dark:text-gray-400">
                {{ tm('Supplier_Details') }}
              </p>
            </div>
          </div>

          <div class="flex items-center space-x-2 flex-wrap">
            <Link
              v-if="can(['all', 'suppliers', 'suppliers.edit', 'suppliers:suppliers:edit', 'suppliers:edit'])"
              :href="route('suppliers.edit', supplier.id)"
              class="inline-flex items-center px-3.5 py-1.5 bg-primary hover:bg-primary-dark text-white text-xs font-semibold rounded-md shadow-sm transition"
            >
              <svg class="w-3.5 h-3.5 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
              </svg>
              {{ tm('Edit') }}
            </Link>

            <button
              v-if="can(['all', 'suppliers', 'suppliers.delete', 'suppliers:suppliers:delete', 'suppliers:destroy'])"
              type="button"
              @click="confirmDelete"
              class="inline-flex items-center px-3.5 py-1.5 bg-red-600 hover:bg-red-700 text-white text-xs font-semibold rounded-md shadow-sm transition"
            >
              <svg class="w-3.5 h-3.5 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
              </svg>
              {{ tm('Delete') }}
            </button>

            <Link
              :href="route('suppliers.index')"
              class="inline-flex items-center px-3.5 py-1.5 border border-gray-300 dark:border-gray-600 hover:bg-gray-50 dark:hover:bg-gray-700 text-gray-700 dark:text-gray-200 text-xs font-semibold rounded-md transition"
            >
              &larr; {{ tm('Back_to_List') }}
            </Link>
          </div>
        </div>

        <!-- Body Grid -->
        <div class="p-6 grid grid-cols-1 md:grid-cols-2 gap-6">
          <!-- Left Column: Supplier Information -->
          <div class="space-y-4">
            <h3 class="text-sm font-bold uppercase tracking-wider text-primary border-b pb-2 dark:border-gray-700">
              {{ tm('Basic_Information') }}
            </h3>

            <div class="bg-gray-50 dark:bg-dark p-4 rounded-lg space-y-3">
              <div class="flex justify-between items-center py-1 border-b border-gray-200 dark:border-gray-700">
                <span class="text-xs font-semibold text-gray-500 dark:text-gray-400">ID</span>
                <span class="text-sm font-mono font-bold text-gray-800 dark:text-gray-200">#{{ supplier.id }}</span>
              </div>

              <div class="flex justify-between items-center py-1 border-b border-gray-200 dark:border-gray-700">
                <span class="text-xs font-semibold text-gray-500 dark:text-gray-400">{{ tm('Name') }}</span>
                <span class="text-sm font-semibold text-gray-800 dark:text-gray-200">{{ supplier.name }}</span>
              </div>

              <div class="flex justify-between items-center py-1 border-b border-gray-200 dark:border-gray-700">
                <span class="text-xs font-semibold text-gray-500 dark:text-gray-400">{{ tm('contact_person') }}</span>
                <span class="text-sm text-gray-800 dark:text-gray-200">{{ supplier.contact_person || '—' }}</span>
              </div>

              <div class="flex justify-between items-center py-1 border-b border-gray-200 dark:border-gray-700">
                <span class="text-xs font-semibold text-gray-500 dark:text-gray-400">{{ tm('phone') }}</span>
                <span class="text-sm font-mono text-gray-800 dark:text-gray-200">
                  <a v-if="supplier.phone" :href="`tel:${supplier.phone}`" class="text-primary hover:underline">{{ supplier.phone }}</a>
                  <span v-else>—</span>
                </span>
              </div>

              <div class="flex justify-between items-center py-1 border-b border-gray-200 dark:border-gray-700">
                <span class="text-xs font-semibold text-gray-500 dark:text-gray-400">{{ tm('Email') }}</span>
                <span class="text-sm text-gray-800 dark:text-gray-200">
                  <a v-if="supplier.email" :href="`mailto:${supplier.email}`" class="text-primary hover:underline">{{ supplier.email }}</a>
                  <span v-else>—</span>
                </span>
              </div>

              <div class="flex justify-between items-center py-1 border-b border-gray-200 dark:border-gray-700">
                <span class="text-xs font-semibold text-gray-500 dark:text-gray-400">{{ tm('Created_At') }}</span>
                <span class="text-xs text-gray-600 dark:text-gray-300">{{ supplier.created_at || '—' }}</span>
              </div>

              <div class="flex justify-between items-center py-1">
                <span class="text-xs font-semibold text-gray-500 dark:text-gray-400">{{ tm('Updated_At') }}</span>
                <span class="text-xs text-gray-600 dark:text-gray-300">{{ supplier.updated_at || '—' }}</span>
              </div>
            </div>
          </div>

          <!-- Right Column: Address Details -->
          <div class="space-y-4">
            <h3 class="text-sm font-bold uppercase tracking-wider text-primary border-b pb-2 dark:border-gray-700">
              {{ tm('Address_Information') }}
            </h3>

            <div class="bg-gray-50 dark:bg-dark p-4 rounded-lg space-y-3">
              <!-- Full Formatted Address Callout -->
              <div class="p-3 bg-white dark:bg-darker rounded border border-gray-200 dark:border-gray-700">
                <span class="text-xs font-semibold text-gray-500 dark:text-gray-400 block mb-1">
                  {{ tm('Formatted_Address') }}
                </span>
                <p class="text-sm font-medium text-gray-800 dark:text-gray-200">
                  {{ supplier.formatted_address || 'No address specified' }}
                </p>
              </div>

              <!-- Cascading Breakdown (if addressRecord exists) -->
              <div v-if="supplier.address_record" class="space-y-2 pt-2">
                <h4 class="text-xs font-bold text-gray-600 dark:text-gray-300 uppercase tracking-wide">
                  {{ tm('Structured_Hierarchy') }}
                </h4>

                <div class="flex justify-between items-center py-1 border-b border-gray-200 dark:border-gray-700">
                  <span class="text-xs font-semibold text-gray-500 dark:text-gray-400">{{ tm('province') }}</span>
                  <span class="text-sm font-semibold text-gray-800 dark:text-gray-200">
                    {{ supplier.address_record.province ? `${supplier.address_record.province.name_en} (${supplier.address_record.province.name_kh})` : '—' }}
                  </span>
                </div>

                <div class="flex justify-between items-center py-1 border-b border-gray-200 dark:border-gray-700">
                  <span class="text-xs font-semibold text-gray-500 dark:text-gray-400">{{ tm('district') }}</span>
                  <span class="text-sm text-gray-800 dark:text-gray-200">
                    {{ supplier.address_record.district ? `${supplier.address_record.district.name_en} (${supplier.address_record.district.name_kh})` : '—' }}
                  </span>
                </div>

                <div class="flex justify-between items-center py-1 border-b border-gray-200 dark:border-gray-700">
                  <span class="text-xs font-semibold text-gray-500 dark:text-gray-400">{{ tm('commune') }}</span>
                  <span class="text-sm text-gray-800 dark:text-gray-200">
                    {{ supplier.address_record.commune ? `${supplier.address_record.commune.name_en} (${supplier.address_record.commune.name_kh})` : '—' }}
                  </span>
                </div>

                <div class="flex justify-between items-center py-1">
                  <span class="text-xs font-semibold text-gray-500 dark:text-gray-400">{{ tm('village') }}</span>
                  <span class="text-sm text-gray-800 dark:text-gray-200">
                    {{ supplier.address_record.village ? `${supplier.address_record.village.name_en} (${supplier.address_record.village.name_kh})` : '—' }}
                  </span>
                </div>
              </div>

              <!-- Legacy Address Text (if raw text exists) -->
              <div v-if="supplier.address && supplier.address !== supplier.formatted_address" class="pt-2">
                <span class="text-xs font-semibold text-gray-500 dark:text-gray-400 block mb-1">
                  {{ tm('Legacy_Address_Note') }}
                </span>
                <p class="text-xs font-mono bg-amber-50 dark:bg-amber-900/20 text-amber-800 dark:text-amber-200 p-2 rounded border border-amber-200 dark:border-amber-800">
                  {{ supplier.address }}
                </p>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </TenantLayout>
</template>

<script>
import { defineComponent } from 'vue'
import TenantLayout from '@/Layouts/TenantLayout.vue'
import { Head, Link } from '@inertiajs/inertia-vue3'
import JetBanner from '@/Jetstream/Banner.vue'
import { useI18n } from 'vue-i18n'
import { lang } from '@/Lang'
import { useToast } from 'vue-toastification'

export default defineComponent({
  props: {
    supplier: {
      type: Object,
      required: true,
    },
  },
  components: {
    TenantLayout,
    Head,
    Link,
    JetBanner,
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
    confirmDelete() {
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
          this.$inertia.delete(route('suppliers.destroy', this.supplier.id), {
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
