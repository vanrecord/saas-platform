<template>
  <TenantLayout>
    <Head :title="`${tm('Product')} - ${product.name}`" />

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
              <Link :href="route('products.index')" class="ml-1 text-sm font-medium text-gray-700 hover:text-gray-900 md:ml-2 dark:text-gray-400 dark:hover:text-white">
                {{ tm('Products') }}
              </Link>
            </div>
          </li>
          <li aria-current="page">
            <div class="flex items-center">
              <svg class="w-6 h-6 text-gray-400" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path>
              </svg>
              <span class="ml-1 text-sm font-medium text-gray-500 md:ml-2 dark:text-gray-400">
                {{ product.name }}
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
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
              </svg>
            </div>
            <div>
              <h2 class="text-lg font-bold text-gray-800 dark:text-light">
                {{ product.name }}
              </h2>
              <p class="text-xs text-gray-500 dark:text-gray-400">
                {{ tm('Product_Details') }}
              </p>
            </div>
          </div>

          <div class="flex items-center space-x-2 flex-wrap">
            <Link
              v-if="can(['all', 'products', 'products.edit', 'products:products:edit', 'products:edit'])"
              :href="route('products.edit', product.id)"
              class="inline-flex items-center px-3.5 py-1.5 bg-primary hover:bg-primary-dark text-white text-xs font-semibold rounded-md shadow-sm transition"
            >
              <svg class="w-3.5 h-3.5 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
              </svg>
              {{ tm('edit') }}
            </Link>

            <button
              v-if="can(['all', 'products', 'products.destroy', 'products:products:delete', 'products:delete'])"
              type="button"
              @click="confirmDelete"
              class="inline-flex items-center px-3.5 py-1.5 bg-red-600 hover:bg-red-700 text-white text-xs font-semibold rounded-md shadow-sm transition"
            >
              <svg class="w-3.5 h-3.5 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
              </svg>
              {{ tm('delete') }}
            </button>

            <Link
              :href="route('products.index')"
              class="inline-flex items-center px-3.5 py-1.5 border border-gray-300 dark:border-gray-600 hover:bg-gray-50 dark:hover:bg-gray-700 text-gray-700 dark:text-gray-200 text-xs font-semibold rounded-md transition"
            >
              &larr; {{ tm('Back_to_List') }}
            </Link>
          </div>
        </div>

        <!-- Body Grid -->
        <div class="p-6 grid grid-cols-1 md:grid-cols-2 gap-6">
          <!-- Left Column: Product Info & Categorization -->
          <div class="space-y-4">
            <h3 class="text-sm font-bold uppercase tracking-wider text-primary border-b pb-2 dark:border-gray-700">
              {{ tm('Product_Information') }}
            </h3>

            <div class="bg-gray-50 dark:bg-dark p-4 rounded-lg space-y-3">
              <div class="flex justify-between items-center py-1 border-b border-gray-200 dark:border-gray-700">
                <span class="text-xs font-semibold text-gray-500 dark:text-gray-400">{{ tm('No_dot') }}</span>
                <span class="text-sm font-mono font-bold text-gray-800 dark:text-gray-200">#{{ product.code || product.id }}</span>
              </div>

              <div class="flex justify-between items-center py-1 border-b border-gray-200 dark:border-gray-700">
                <span class="text-xs font-semibold text-gray-500 dark:text-gray-400">{{ tm('Name') }}</span>
                <span class="text-sm font-semibold text-gray-800 dark:text-gray-200">{{ product.name }}</span>
              </div>

              <div class="flex justify-between items-center py-1 border-b border-gray-200 dark:border-gray-700">
                <span class="text-xs font-semibold text-gray-500 dark:text-gray-400">{{ tm('Supplier') }}</span>
                <span class="text-sm text-gray-800 dark:text-gray-200">
                  <span v-if="product.supplier" class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-emerald-50 text-emerald-700 dark:bg-emerald-900/30 dark:text-emerald-300">
                    {{ product.supplier.name }}
                  </span>
                  <span v-else>—</span>
                </span>
              </div>

              <div class="flex justify-between items-center py-1 border-b border-gray-200 dark:border-gray-700">
                <span class="text-xs font-semibold text-gray-500 dark:text-gray-400">{{ tm('Category') }}</span>
                <span class="text-sm text-gray-800 dark:text-gray-200">
                  <span v-if="product.category" class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-blue-50 text-blue-700 dark:bg-blue-900/30 dark:text-blue-300">
                    {{ product.category.name }}
                  </span>
                  <span v-else>—</span>
                </span>
              </div>

              <div class="flex justify-between items-center py-1 border-b border-gray-200 dark:border-gray-700">
                <span class="text-xs font-semibold text-gray-500 dark:text-gray-400">{{ tm('Status') }}</span>
                <span>
                  <span
                    v-if="product.status"
                    class="inline-flex items-center px-2 py-0.5 rounded text-xs font-semibold bg-emerald-100 text-emerald-800 dark:bg-emerald-900/30 dark:text-emerald-300"
                  >
                    {{ tm('Enable') }}
                  </span>
                  <span
                    v-else
                    class="inline-flex items-center px-2 py-0.5 rounded text-xs font-semibold bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-300"
                  >
                    {{ tm('Disable') }}
                  </span>
                </span>
              </div>

              <div class="flex justify-between items-center py-1 border-b border-gray-200 dark:border-gray-700">
                <span class="text-xs font-semibold text-gray-500 dark:text-gray-400">{{ tm('Created_At') }}</span>
                <span class="text-xs text-gray-600 dark:text-gray-300">{{ product.created_at || '—' }}</span>
              </div>

              <div class="flex justify-between items-center py-1">
                <span class="text-xs font-semibold text-gray-500 dark:text-gray-400">{{ tm('Updated_At') }}</span>
                <span class="text-xs text-gray-600 dark:text-gray-300">{{ product.updated_at || '—' }}</span>
              </div>
            </div>
          </div>

          <!-- Right Column: Pricing & Inventory Details -->
          <div class="space-y-4">
            <h3 class="text-sm font-bold uppercase tracking-wider text-primary border-b pb-2 dark:border-gray-700">
              {{ tm('Price') }} &amp; {{ tm('Cost') }}
            </h3>

            <div class="bg-gray-50 dark:bg-dark p-4 rounded-lg space-y-3">
              <div class="flex justify-between items-center py-1 border-b border-gray-200 dark:border-gray-700">
                <span class="text-xs font-semibold text-gray-500 dark:text-gray-400">{{ tm('Sale_Price') }}</span>
                <span class="text-base font-mono font-bold text-primary">
                  ${{ Number(product.price || 0).toFixed(2) }}
                </span>
              </div>

              <div class="flex justify-between items-center py-1 border-b border-gray-200 dark:border-gray-700">
                <span class="text-xs font-semibold text-gray-500 dark:text-gray-400">{{ tm('Unit_Price') }}</span>
                <span class="text-sm font-mono text-gray-800 dark:text-gray-200">
                  ${{ Number(product.cost || 0).toFixed(2) }}
                </span>
              </div>

              <div class="flex justify-between items-center py-1 border-b border-gray-200 dark:border-gray-700">
                <span class="text-xs font-semibold text-gray-500 dark:text-gray-400">{{ tm('Quantity') }}</span>
                <span class="text-sm font-mono font-bold text-slate-700 dark:text-slate-200">
                  {{ product.qty ?? 0 }}
                </span>
              </div>

              <!-- Description -->
              <div class="pt-2">
                <span class="text-xs font-semibold text-gray-500 dark:text-gray-400 block mb-1">
                  {{ tm('Description') }}
                </span>
                <p class="text-xs text-gray-700 dark:text-gray-300 bg-white dark:bg-darker p-3 rounded border border-gray-200 dark:border-gray-700 whitespace-pre-wrap">
                  {{ product.description || '—' }}
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
import { Head, Link } from '@inertiajs/inertia-vue3'
import TenantLayout from '@/Layouts/TenantLayout.vue'
import JetBanner from '@/Jetstream/Banner.vue'
import { useToast } from 'vue-toastification'
import { useI18n } from 'vue-i18n'
import { lang } from '@/Lang'

export default defineComponent({
  props: {
    product: {
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
        title: this.tm('Are_you_sure_to_delete_this_product'),
        text: this.tm('You_wont_be_able_to_revert_this'),
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: this.tm('Yes_delete_it'),
        cancelButtonText: this.tm('Cancel'),
      }).then((result) => {
        if (result.isConfirmed) {
          this.$inertia.delete(route('products.destroy', this.product.id), {
            preserveScroll: true,
            onSuccess: () => {
              this.toast.success(this.tm('Product_deleted_successfully'))
            },
            onError: (errors) => {
              this.toast.error(errors?.message || 'Failed to delete product.')
            },
          })
        }
      })
    },
  },
})
</script>
