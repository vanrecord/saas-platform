<template>
  <TenantLayout>
    <Head :title="tm('Create_Service')" />
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
              <Link :href="route('services.index')" class="ml-1 text-sm font-medium text-gray-700 hover:text-gray-900 md:ml-2 dark:text-gray-400 dark:hover:text-white">
                {{ tm('Services') }}
              </Link>
            </div>
          </li>
          <li>
            <div class="flex items-center">
              <svg class="w-6 h-6 text-gray-400" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path>
              </svg>
              <span class="ml-1 text-sm font-medium text-gray-700 hover:text-gray-900 md:ml-2 dark:text-gray-400 dark:hover:text-white">
                {{ tm('Create') }}
              </span>
            </div>
          </li>
        </ol>
      </nav>

      <form @submit.prevent="submit">
        <div class="col-span-2 bg-white rounded-md dark:bg-darker shadow-sm">
          <div class="flex items-center justify-between p-4 border-b dark:border-primary">
            <h4 class="text-lg font-semibold text-gray-500 dark:text-light">
              {{ tm('Create_Service') }}
            </h4>
          </div>

          <div class="flex items-center justify-between p-4 border-b bg-gray-50 dark:bg-darker dark:border-primary">
            <h5 class="text-sm font-semibold text-gray-600 dark:text-light">
              {{ tm('Service_Information') }}
            </h5>
          </div>

          <div class="p-4">
            <div class="flex flex-wrap">
              <!-- Category (Select) -->
              <div class="pb-4 pr-6 w-full lg:w-1/2">
                <select-input
                  v-model="form.category_id"
                  :label="tm('Category')"
                  :options="categories"
                  value-attribute="id"
                  text-attribute="name"
                  :clearable="true"
                  :placeholder="tm('Select_Category')"
                  :error="form.errors.category_id"
                />
              </div>

              <!-- Name (Text, Required) -->
              <text-input
                v-model="form.name"
                :error="form.errors.name"
                required="required"
                class="pb-4 pr-6 w-full lg:w-1/2"
                :label="tm('Name')"
                placeholder="Enter service name"
              />

              <!-- Price (Number, Required) -->
              <text-input
                v-model="form.price"
                type="number"
                step="0.01"
                min="0"
                required="required"
                :error="form.errors.price"
                class="pb-4 pr-6 w-full lg:w-1/2"
                :label="tm('Price')"
                placeholder="0.00"
              />

              <!-- Costing (Number, Required, saved to cost) -->
              <text-input
                v-model="form.cost"
                type="number"
                step="0.01"
                min="0"
                required="required"
                :error="form.errors.cost"
                class="pb-4 pr-6 w-full lg:w-1/2"
                :label="tm('Costing')"
                placeholder="0.00"
              />

              <!-- Commission & Commission Type (Same row) -->
              <div class="flex flex-wrap w-full lg:w-1/2 pr-6 pb-4">
                <div class="w-2/3 pr-2">
                  <text-input
                    v-model="form.commission"
                    type="number"
                    step="0.01"
                    min="0"
                    :error="form.errors.commission"
                    :label="tm('Commission')"
                    placeholder="0.00"
                  />
                </div>
                <div class="w-1/3">
                  <select-input
                    v-model="form.commission_type"
                    :label="tm('Commission_Type')"
                    :options="commissionTypeOptions"
                    value-attribute="id"
                    text-attribute="name"
                    :clearable="false"
                    :error="form.errors.commission_type"
                  />
                </div>
              </div>

              <!-- Discount (Number, Optional) -->
              <text-input
                v-model="form.discount"
                type="number"
                step="0.01"
                min="0"
                :error="form.errors.discount"
                class="pb-4 pr-6 w-full lg:w-1/2"
                :label="tm('Discount')"
                placeholder="0.00"
              />

              <!-- Status (Select: Enable / Disable, defaults to Enable) -->
              <div class="pb-4 pr-6 w-full lg:w-1/2">
                <select-input
                  v-model="form.status"
                  :label="tm('Status')"
                  :options="statusOptions"
                  value-attribute="id"
                  text-attribute="name"
                  :clearable="false"
                  :error="form.errors.status"
                />
              </div>

              <!-- Description (Textarea, Optional) -->
              <textarea-input
                v-model="form.description"
                :error="form.errors.description"
                :rows="3"
                class="pb-4 pr-6 w-full"
                :label="tm('Description')"
                placeholder="Enter description (optional)"
              />
            </div>
          </div>

          <!-- Footer Actions -->
          <div class="flex items-center justify-end px-8 py-4 bg-gray-50 border-t border-gray-100 dark:bg-darker dark:border-primary space-x-2">
            <save-button :loading="form.processing">
              {{ tm('Save') }}
            </save-button>
            <back-button :url="route('services.index')">
              {{ tm('Cancel') }}
            </back-button>
          </div>
        </div>
      </form>
    </div>
  </TenantLayout>
</template>

<script>
import { defineComponent, computed } from 'vue'
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

    const commissionTypeOptions = [
      { id: '%', name: '%' },
      { id: '$', name: '$' },
    ]

    const statusOptions = computed(() => [
      { id: 1, name: tm('Enable') },
      { id: 0, name: tm('Disable') },
    ])

    return { tm, toast, commissionTypeOptions, statusOptions }
  },
  data() {
    return {
      form: this.$inertia.form({
        category_id: null,
        name: '',
        price: '',
        cost: '',
        commission: '',
        commission_type: '%',
        discount: '',
        status: 1,
        description: '',
      }),
    }
  },
  methods: {
    submit() {
      this.form.post(route('services.store'), {
        preserveScroll: true,
        onSuccess: () => {
          this.toast.success(this.tm('Service_created_successfully'))
        },
        onError: (errors) => {
          if (errors) {
            Object.values(errors).flat().forEach((msg) => {
              if (typeof msg === 'string') {
                this.toast.error(msg)
              }
            })
          }
        }
      })
    }
  }
})
</script>
