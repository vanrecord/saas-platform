<template>
  <TenantLayout>
    <Head :title="tm('Edit_Product')" />
    <div class="px-4 py-4">
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
          <li>
            <div class="flex items-center">
              <svg class="w-6 h-6 text-gray-400" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path>
              </svg>
              <span class="ml-1 text-sm font-medium text-gray-700 hover:text-gray-900 md:ml-2 dark:text-gray-400 dark:hover:text-white">
                {{ tm('edit') }}
              </span>
            </div>
          </li>
        </ol>
      </nav>

      <form @submit.prevent="submit">
        <div class="col-span-2 bg-white rounded-md dark:bg-darker shadow-sm">
          <div class="flex items-center justify-between p-4 border-b dark:border-primary">
            <h4 class="text-lg font-semibold text-gray-500 dark:text-light">
              {{ tm('Edit_Product') }}
            </h4>
          </div>

          <div class="flex items-center justify-between p-4 border-b bg-gray-50 dark:bg-darker dark:border-primary">
            <h5 class="text-sm font-semibold text-gray-600 dark:text-light">
              {{ tm('Product_Information') }}
            </h5>
          </div>

          <div class="p-4">
            <div class="flex flex-wrap">
              <!-- 1. Supplier (Select, Optional) -->
              <div class="pb-4 pr-6 w-full lg:w-1/2">
                <select-input
                  v-model="form.supplier_id"
                  :label="tm('Supplier')"
                  :options="suppliers"
                  value-attribute="id"
                  text-attribute="name"
                  :clearable="true"
                  :placeholder="tm('Please_select_a_supplier')"
                  :error="form.errors.supplier_id"
                />
              </div>

              <!-- 2. Category (Select, Optional, Product Categories only) -->
              <div class="pb-4 pr-6 w-full lg:w-1/2">
                <select-input
                  v-model="form.category_id"
                  :label="tm('Category')"
                  :options="categories"
                  value-attribute="id"
                  text-attribute="name"
                  :clearable="true"
                  :placeholder="tm('Please_select_a_category')"
                  :error="form.errors.category_id"
                />
              </div>

              <!-- 3. Name (Text, Required) -->
              <text-input
                v-model="form.name"
                :error="form.errors.name"
                required="required"
                class="pb-4 pr-6 w-full lg:w-1/2"
                :label="tm('Name')"
              />

              <!-- 4. Sale Price (Number, Required, min 0, maps to price) -->
              <text-input
                v-model="form.price"
                type="number"
                step="0.01"
                min="0"
                required="required"
                :error="form.errors.price"
                class="pb-4 pr-6 w-full lg:w-1/2"
                :label="tm('Sale_Price')"
              />

              <!-- 5. Unit Price (Number, Required, min 0, maps to cost) -->
              <text-input
                v-model="form.cost"
                type="number"
                step="0.01"
                min="0"
                required="required"
                :error="form.errors.cost"
                class="pb-4 pr-6 w-full lg:w-1/2"
                :label="tm('Unit_Price')"
              />

              <!-- 6. Status (Select: Enable / Disable, preserves disabled status) -->
              <div class="pb-4 pr-6 w-full lg:w-1/2">
                <select-input
                  v-model="form.status"
                  :label="tm('Status')"
                  :options="statusOptions"
                  value-attribute="value"
                  text-attribute="text"
                  :clearable="false"
                  :error="form.errors.status"
                />
              </div>

              <!-- 7. Description (Textarea, Optional) -->
              <textarea-input
                v-model="form.description"
                :error="form.errors.description"
                class="pb-4 pr-6 w-full"
                :label="tm('Description')"
              />
            </div>
          </div>

          <!-- Form Actions -->
          <div class="flex items-center justify-end px-8 py-4 bg-gray-50 border-t border-gray-100 dark:bg-darker dark:border-primary space-x-2">
            <save-button :loading="form.processing">
              {{ tm('Save') }}
            </save-button>
            <back-button :url="route('products.index')">
              {{ tm('Cancel') }}
            </back-button>
          </div>
        </div>
      </form>
    </div>
  </TenantLayout>
</template>

<script>
import { defineComponent } from 'vue'
import { Head, Link } from '@inertiajs/inertia-vue3'
import TenantLayout from '@/Layouts/TenantLayout.vue'
import TextInput from '@/Components/Form/TextInput.vue'
import TextareaInput from '@/Components/Form/TextareaInput.vue'
import SelectInput from '@/Components/Forms/SelectInput.vue'
import SaveButton from '@/Components/Button/Save.vue'
import BackButton from '@/Components/Button/Back.vue'
import { useToast } from 'vue-toastification'
import { useI18n } from 'vue-i18n'
import { lang } from '@/Lang'

export default defineComponent({
  props: {
    product: {
      type: Object,
      required: true,
    },
    suppliers: {
      type: Array,
      default: () => [],
    },
    categories: {
      type: Array,
      default: () => [],
    },
  },
  components: {
    TenantLayout,
    Head,
    Link,
    TextInput,
    TextareaInput,
    SelectInput,
    SaveButton,
    BackButton,
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
      form: this.$inertia.form({
        supplier_id: this.product.supplier_id || null,
        category_id: this.product.category_id || null,
        name: this.product.name || '',
        price: this.product.price !== null && this.product.price !== undefined ? this.product.price : '',
        cost: this.product.cost !== null && this.product.cost !== undefined ? this.product.cost : '',
        qty: this.product.qty ?? 0,
        status: this.product.status ? 1 : 0,
        description: this.product.description || '',
      }),
      statusOptions: [
        { value: 1, text: this.tm('Enable') },
        { value: 0, text: this.tm('Disable') },
      ],
    }
  },
  methods: {
    submit() {
      this.form.put(route('products.update', this.product.id), {
        preserveScroll: true,
        onSuccess: () => {
          this.toast.success(this.tm('Product_updated_successfully'))
        },
        onError: () => {
          this.toast.error('Please check the form for errors.')
        },
      })
    },
  },
})
</script>
