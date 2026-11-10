<template>
  <TenantLayout>
    <Head :title="`Edit Supplier - ${supplier.name}`" />
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
              <Link :href="route('suppliers.index')" class="ml-1 text-sm font-medium text-gray-700 hover:text-gray-900 md:ml-2 dark:text-gray-400 dark:hover:text-white">
                {{ tm('Suppliers') }}
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
              {{ tm('Edit_Supplier') }}: {{ supplier.name }}
            </h4>
          </div>

          <!-- Section 1: Basic Information -->
          <div class="flex items-center justify-between p-4 border-b bg-gray-50 dark:bg-darker dark:border-primary">
            <h5 class="text-sm font-semibold text-gray-600 dark:text-light">
              {{ tm('Supplier_Information') }}
            </h5>
          </div>
          <div class="p-4">
            <div class="flex flex-wrap">
              <text-input
                v-model="form.name"
                :error="form.errors.name"
                required="required"
                class="pb-2 pr-6 w-full lg:w-1/2"
                :label="tm('Name')"
                placeholder="Enter supplier name"
              />

              <text-input
                v-model="form.phone"
                :error="form.errors.phone"
                class="pb-2 pr-6 w-full lg:w-1/2"
                :label="tm('phone')"
                placeholder="Enter phone number"
              />

              <text-input
                v-model="form.email"
                type="email"
                :error="form.errors.email"
                class="pb-2 pr-6 w-full lg:w-1/2"
                :label="tm('Email')"
                placeholder="Enter email address"
              />

              <text-input
                v-model="form.contact_person"
                :error="form.errors.contact_person"
                class="pb-2 pr-6 w-full lg:w-1/2"
                :label="tm('contact_person')"
                placeholder="Enter contact person name"
              />
            </div>
          </div>

          <!-- Legacy Plain-Text Address Notice (Scenario B) -->
          <div v-if="hasLegacyAddress && !hasLinkedAddress" class="p-4 bg-amber-50 border-b border-amber-200">
            <div class="flex items-start gap-3">
              <svg class="w-5 h-5 text-amber-600 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
              </svg>
              <div class="flex-1 text-xs">
                <p class="font-bold text-amber-900">{{ tm('Legacy_Address_Notice') }}</p>
                <p class="text-amber-800 mt-1">
                  <strong>{{ tm('Current_Legacy_Address') }}:</strong> "{{ supplier.address }}"
                </p>
                <p class="text-amber-700 mt-0.5">
                  {{ tm('Legacy_Address_Help') }}
                </p>
              </div>
            </div>
          </div>

          <!-- Section 2: Address Information (Cascading Selectors) -->
          <div class="flex items-center justify-between p-4 border-b bg-gray-50 dark:bg-darker dark:border-primary">
            <h5 class="text-sm font-semibold text-gray-600 dark:text-light flex items-center gap-2">
              <span>{{ tm('Address') }}</span>
              <span class="text-xs font-normal text-slate-400">
                (Province → District → Commune → Village)
              </span>
            </h5>
          </div>
          <div class="p-4">
            <div class="flex flex-wrap">
              <!-- 1. Province -->
              <div class="pb-2 pr-6 w-full lg:w-1/2">
                <select-input
                  v-model="form.province_id"
                  :label="tm('province')"
                  :options="provinceOptions"
                  :clearable="true"
                  value-attribute="id"
                  text-attribute="name_en"
                  :placeholder="tm('Please_select_a_province')"
                  :error="form.errors.province_id"
                />
              </div>

              <!-- 2. District -->
              <div class="pb-2 pr-6 w-full lg:w-1/2">
                <div class="relative">
                  <select-input
                    v-model="form.district_id"
                    :label="tm('district')"
                    :options="districtOptions"
                    :clearable="true"
                    value-attribute="id"
                    text-attribute="name_en"
                    :placeholder="loadingDistricts ? 'Loading districts...' : tm('Please_select_a_district')"
                    :disabled="!form.province_id || loadingDistricts"
                    :error="form.errors.district_id"
                  />
                  <span v-if="loadingDistricts" class="text-xs text-blue-500 italic mt-1 block">
                    Loading districts...
                  </span>
                </div>
              </div>

              <!-- 3. Commune -->
              <div class="pb-2 pr-6 w-full lg:w-1/2">
                <div class="relative">
                  <select-input
                    v-model="form.commune_id"
                    :label="tm('commune')"
                    :options="communeOptions"
                    :clearable="true"
                    value-attribute="id"
                    text-attribute="name_en"
                    :placeholder="loadingCommunes ? 'Loading communes...' : tm('Please_select_a_commune')"
                    :disabled="!form.district_id || loadingCommunes"
                    :error="form.errors.commune_id"
                  />
                  <span v-if="loadingCommunes" class="text-xs text-blue-500 italic mt-1 block">
                    Loading communes...
                  </span>
                </div>
              </div>

              <!-- 4. Village -->
              <div class="pb-2 pr-6 w-full lg:w-1/2">
                <div class="relative">
                  <select-input
                    v-model="form.village_id"
                    :label="tm('village')"
                    :options="villageOptions"
                    :clearable="true"
                    value-attribute="id"
                    text-attribute="name_en"
                    :placeholder="loadingVillages ? 'Loading villages...' : tm('Please_select_a_village')"
                    :disabled="!form.commune_id || loadingVillages"
                    :error="form.errors.village_id"
                  />
                  <span v-if="loadingVillages" class="text-xs text-blue-500 italic mt-1 block">
                    Loading villages...
                  </span>
                </div>
              </div>
            </div>
          </div>

          <!-- Footer Actions -->
          <div class="flex items-center justify-end px-8 py-4 bg-gray-50 border-t border-gray-100 dark:bg-darker dark:border-primary space-x-2">
            <save-button :loading="form.processing">
              {{ tm('Save') }}
            </save-button>
            <back-button :url="route('suppliers.index')">
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
import SelectInput from '@/Components/Forms/SelectInput.vue'
import SaveButton from '@/Components/Button/Save.vue'
import BackButton from '@/Components/Button/Back.vue'
import { useToast } from 'vue-toastification'
import { useI18n } from 'vue-i18n'
import { lang } from '@/Lang'
import axios from 'axios'

export default defineComponent({
  props: {
    supplier: {
      type: Object,
      required: true
    },
    provinces: {
      type: Array,
      default: () => []
    }
  },
  components: {
    TenantLayout,
    Head,
    Link,
    TextInput,
    SelectInput,
    SaveButton,
    BackButton
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
    const addr = this.supplier.address_record || {}
    return {
      provinceOptions: this.provinces || [],
      districtOptions: [],
      communeOptions: [],
      villageOptions: [],

      loadingDistricts: false,
      loadingCommunes: false,
      loadingVillages: false,
      isInitializing: true,

      hasLinkedAddress: Boolean(this.supplier.address_record),
      hasLegacyAddress: Boolean(this.supplier.address && !this.supplier.address_record),

      form: this.$inertia.form({
        name: this.supplier.name || '',
        phone: this.supplier.phone || '',
        email: this.supplier.email || '',
        contact_person: this.supplier.contact_person || '',
        address: this.supplier.address || '',
        province_id: addr.province_id || null,
        district_id: addr.district_id || null,
        commune_id: addr.commune_id || null,
        village_id: addr.village_id || null,
      }),
    }
  },
  watch: {
    'form.province_id': function (newVal, oldVal) {
      if (this.isInitializing) return
      if (newVal !== oldVal) {
        this.form.district_id = null
        this.form.commune_id = null
        this.form.village_id = null
        this.districtOptions = []
        this.communeOptions = []
        this.villageOptions = []

        if (newVal) {
          this.fetchDistricts(newVal)
        }
      }
    },
    'form.district_id': function (newVal, oldVal) {
      if (this.isInitializing) return
      if (newVal !== oldVal) {
        this.form.commune_id = null
        this.form.village_id = null
        this.communeOptions = []
        this.villageOptions = []

        if (newVal) {
          this.fetchCommunes(newVal)
        }
      }
    },
    'form.commune_id': function (newVal, oldVal) {
      if (this.isInitializing) return
      if (newVal !== oldVal) {
        this.form.village_id = null
        this.villageOptions = []

        if (newVal) {
          this.fetchVillages(newVal)
        }
      }
    },
  },
  mounted() {
    this.preloadExistingAddress()
  },
  methods: {
    async preloadExistingAddress() {
      const addr = this.supplier.address_record
      if (!addr) {
        this.isInitializing = false
        return
      }

      try {
        if (addr.province_id) {
          await this.fetchDistricts(addr.province_id)
        }
        if (addr.district_id) {
          await this.fetchCommunes(addr.district_id)
        }
        if (addr.commune_id) {
          await this.fetchVillages(addr.commune_id)
        }
      } finally {
        this.$nextTick(() => {
          this.isInitializing = false
        })
      }
    },
    fetchDistricts(provinceId) {
      this.loadingDistricts = true
      return axios.get(route('api.address.districts', provinceId))
        .then((response) => {
          this.districtOptions = response.data || []
        })
        .catch(() => {
          this.toast.error('Failed to load districts.')
        })
        .finally(() => {
          this.loadingDistricts = false
        })
    },
    fetchCommunes(districtId) {
      this.loadingCommunes = true
      return axios.get(route('api.address.communes', districtId))
        .then((response) => {
          this.communeOptions = response.data || []
        })
        .catch(() => {
          this.toast.error('Failed to load communes.')
        })
        .finally(() => {
          this.loadingCommunes = false
        })
    },
    fetchVillages(communeId) {
      this.loadingVillages = true
      return axios.get(route('api.address.villages', communeId))
        .then((response) => {
          this.villageOptions = response.data || []
        })
        .catch(() => {
          this.toast.error('Failed to load villages.')
        })
        .finally(() => {
          this.loadingVillages = false
        })
    },
    submit() {
      this.form.put(route('suppliers.update', this.supplier.id), {
        preserveScroll: true,
        onSuccess: () => {
          this.toast.success(this.tm('Updated_successfully'))
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
