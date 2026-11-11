<template>
  <TenantLayout>
    <Head :title="tm('Edit_Payment_Method') || 'Edit Payment Method'" />
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
              <Link
                :href="route('settings.payment-methods.index')"
                class="ml-1 text-sm font-medium text-gray-700 hover:text-gray-900 md:ml-2 dark:text-gray-400 dark:hover:text-white"
              >
                {{ tm('Payment_Methods') || 'Payment Methods' }}
              </Link>
            </div>
          </li>
          <li>
            <div class="flex items-center">
              <svg class="w-6 h-6 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path>
              </svg>
              <span class="ml-1 text-sm font-medium text-gray-500 md:ml-2 dark:text-gray-400">
                {{ tm('Edit_Payment_Method') || 'Edit Payment Method' }}
              </span>
            </div>
          </li>
        </ol>
      </nav>

      <!-- Form Card -->
      <form @submit.prevent="update">
        <div class="bg-white rounded-md dark:bg-darker shadow-sm space-y-6">
          <div class="flex items-center justify-between p-4 border-b dark:border-primary">
            <div class="flex items-center gap-3">
              <h4 class="text-lg font-semibold text-gray-500 dark:text-light">
                {{ tm('Edit_Payment_Method') || 'Edit Payment Method' }}: {{ paymentMethod.name }}
              </h4>
              <span v-if="paymentMethod.provider" class="px-2 py-0.5 rounded text-xs font-mono font-bold bg-blue-50 text-[#0D63D6] border border-blue-200">
                {{ paymentMethod.provider }}
              </span>
            </div>
          </div>

          <!-- Section 1: General Information -->
          <div>
            <div class="p-4 border-b bg-gray-50 dark:bg-darker dark:border-primary flex items-center justify-between">
              <h5 class="text-sm font-semibold text-gray-600 dark:text-light">
                {{ tm('General_Information') || 'General Information' }}
              </h5>
            </div>

            <div class="p-4">
              <div class="flex flex-wrap -mx-2">
                <!-- Name -->
                <text-input
                  v-model="form.name"
                  required="required"
                  :error="form.errors.name"
                  class="pb-4 px-2 w-full lg:w-1/2"
                  :label="tm('Name') || 'Name'"
                  placeholder="e.g. Cash, ABA KHQR, Wing"
                />

                <!-- Order -->
                <text-input
                  v-model.number="form.order"
                  type="number"
                  min="0"
                  :error="form.errors.order"
                  class="pb-4 px-2 w-full lg:w-1/2"
                  :label="tm('Order') || 'Order'"
                  placeholder="Display order sequence (e.g. 1, 2, 3)"
                />

                <!-- Description -->
                <textarea-input
                  v-model="form.description"
                  :error="form.errors.description"
                  class="pb-4 px-2 w-full"
                  :label="tm('Description') || 'Description'"
                  placeholder="Optional notes or instructions for this payment method..."
                  rows="3"
                />
              </div>

              <!-- Switches Grid -->
              <div class="grid grid-cols-1 md:grid-cols-3 gap-4 p-4 mt-2 bg-slate-50 dark:bg-slate-800/40 rounded-lg border border-slate-200 dark:border-slate-700">
                <!-- Default Print -->
                <div class="p-3 bg-white dark:bg-slate-700 rounded-md border border-slate-100 dark:border-slate-600">
                  <switch-input
                    v-model="form.print"
                    :label="tm('Default_Print') || 'Default Print'"
                    :active="tm('Yes') || 'Yes'"
                    :inactive="tm('No') || 'No'"
                  />
                  <p class="text-3xs text-slate-400 mt-2 text-center">Selected by default when printing receipts.</p>
                </div>

                <!-- Disable Switch -->
                <div class="p-3 bg-white dark:bg-slate-700 rounded-md border border-slate-100 dark:border-slate-600">
                  <switch-input
                    v-model="form.is_disable"
                    :label="tm('Disable') || 'Disable'"
                    :active="tm('Inactive') || 'Inactive'"
                    :inactive="tm('Active') || 'Active'"
                  />
                  <p class="text-3xs text-slate-400 mt-2 text-center">When enabled, hidden from new payment creation.</p>
                </div>

                <!-- Is Sponsor -->
                <div class="p-3 bg-white dark:bg-slate-700 rounded-md border border-slate-100 dark:border-slate-600">
                  <switch-input
                    v-model="form.is_sponsor"
                    :label="tm('Is_Sponsor') || 'Is Sponsor'"
                    :active="tm('Yes') || 'Yes'"
                    :inactive="tm('No') || 'No'"
                  />
                  <p class="text-3xs text-slate-400 mt-2 text-center">Marks method as sponsored / insurance.</p>
                </div>
              </div>

              <!-- QR Code Upload Section -->
              <div class="mt-6 p-4 bg-white dark:bg-slate-700 rounded-lg border border-slate-200 dark:border-slate-600">
                <label class="block text-xs font-bold text-slate-700 dark:text-slate-200 uppercase tracking-wider mb-2">
                  {{ tm('QR_Code') || 'QR Code' }}
                </label>
                <div class="flex flex-wrap items-center gap-6">
                  <!-- Preview Box -->
                  <div class="w-32 h-32 rounded-lg border-2 border-dashed border-gray-300 dark:border-gray-500 overflow-hidden flex items-center justify-center bg-gray-50 dark:bg-slate-800">
                    <img
                      v-if="qrCodePreview"
                      :src="qrCodePreview"
                      alt="QR Code preview"
                      class="w-full h-full object-contain p-1"
                    />
                    <div v-else class="text-center p-2 text-slate-400">
                      <svg class="w-10 h-10 mx-auto text-gray-300 dark:text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <rect x="3" y="3" width="7" height="7" stroke-width="2" />
                        <rect x="14" y="3" width="7" height="7" stroke-width="2" />
                        <rect x="3" y="14" width="7" height="7" stroke-width="2" />
                        <rect x="14" y="14" width="7" height="7" stroke-width="2" />
                      </svg>
                      <span class="text-3xs block mt-1">No QR Code</span>
                    </div>
                  </div>

                  <div class="space-y-2">
                    <div class="flex items-center gap-3">
                      <button
                        type="button"
                        @click="triggerFileInput"
                        class="px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm text-xs font-semibold text-gray-700 dark:text-gray-200 bg-white dark:bg-slate-800 hover:bg-gray-50 dark:hover:bg-slate-700 transition"
                      >
                        {{ qrCodePreview ? (tm('Change_QR_Code') || 'Change QR Code') : (tm('Upload_QR_Code') || 'Upload QR Code') }}
                      </button>
                      <button
                        v-if="qrCodePreview"
                        type="button"
                        @click="removeQrCode"
                        class="px-4 py-2 border border-red-300 rounded-md shadow-sm text-xs font-semibold text-red-600 bg-white hover:bg-red-50 transition"
                      >
                        {{ tm('Remove') || 'Remove' }}
                      </button>
                      <input
                        ref="fileInput"
                        type="file"
                        accept="image/jpeg,image/png,image/webp,image/svg+xml"
                        @change="handleQrCodeChange"
                        class="hidden"
                      />
                    </div>
                    <p class="text-xs text-slate-400">
                      Upload or replace payment QR code. Recommended format: PNG, JPG, or SVG. Maximum file size: 4MB.
                    </p>
                    <div v-if="form.errors.qr_code" class="text-xs text-red-500 font-semibold">
                      {{ form.errors.qr_code }}
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <!-- Section 2: Online Payment / Bank Payment Configuration -->
          <div id="open_bank_payment" class="border-t dark:border-primary pt-2">
            <div class="p-4 border-b bg-blue-50/60 dark:bg-slate-800 dark:border-primary flex items-center justify-between">
              <div class="flex items-center gap-2">
                <svg class="w-5 h-5 text-[#0D63D6]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z" />
                </svg>
                <h5 class="text-sm font-bold text-slate-800 dark:text-light">
                  {{ tm('Online Payment / Bank Payment Configuration') || 'Online Payment / Bank Payment Configuration' }}
                </h5>
              </div>
              <span class="text-xs text-slate-500 font-mono">Reference: PayWay / Bank API</span>
            </div>

            <div class="p-5 space-y-5">
              <div class="grid grid-cols-1 md:grid-cols-2 gap-6 items-start">
                <!-- 4.1 Bank Provider Selection -->
                <div class="space-y-1">
                  <label class="block font-medium text-sm text-gray-700 dark:text-light">
                    {{ tm('Bank_Provider') || 'Bank Provider' }}
                  </label>
                  <select
                    v-model="form.provider"
                    class="w-full rounded-md border border-gray-300 py-2 px-3 text-sm text-gray-700 focus:outline-none focus:ring focus:ring-primary-100 dark:bg-gray-800 dark:text-gray-200 dark:border-gray-600"
                  >
                    <option value="">-- None / Manual Payment --</option>
                    <option value="ABA">ABA Bank (PayWay)</option>
                  </select>
                  <p class="text-xs text-slate-400">
                    Select a supported financial provider to configure direct online integration.
                  </p>
                  <div v-if="form.errors.provider" class="text-xs text-red-500 font-semibold">
                    {{ form.errors.provider }}
                  </div>
                </div>

                <!-- 4.2 Enable Online Payment Switch -->
                <div class="p-4 bg-slate-50 dark:bg-slate-800/40 rounded-lg border border-slate-200 dark:border-slate-700">
                  <switch-input
                    v-model="form.is_activate"
                    :label="tm('Enable_Online_Payment') || 'Enable Online Payment'"
                    :active="tm('Enabled') || 'Enabled'"
                    :inactive="tm('Disabled') || 'Disabled'"
                  />
                  <p class="text-3xs text-slate-500 dark:text-slate-400 mt-2">
                    When enabled, patients and cashier can initiate online payments via this method. Provider configuration must be complete before activating.
                  </p>
                  <div v-if="form.errors.is_activate" class="text-xs text-red-500 font-semibold mt-1">
                    {{ form.errors.is_activate }}
                  </div>
                </div>
              </div>

              <!-- ABA Bank Specific Configuration (Visible when ABA is selected) -->
              <transition name="fade">
                <div
                  v-if="form.provider === 'ABA'"
                  class="mt-4 p-5 rounded-xl border border-blue-200 bg-blue-50/40 dark:bg-slate-800/80 dark:border-blue-900/40 space-y-4"
                >
                  <div class="flex items-center justify-between border-b border-blue-100 dark:border-slate-700 pb-2">
                    <div class="flex items-center gap-2">
                      <span class="w-2.5 h-2.5 rounded-full bg-blue-500"></span>
                      <h6 class="text-xs font-bold text-slate-800 dark:text-slate-200 uppercase tracking-wider">
                        ABA PayWay Gateway Parameters
                      </h6>
                    </div>
                    <span v-if="paymentMethod.datas?.has_api_key" class="inline-flex items-center gap-1 px-2 py-0.5 rounded text-3xs font-semibold bg-emerald-100 text-emerald-800">
                      ✓ API Key Configured
                    </span>
                  </div>

                  <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <!-- Merchant ID -->
                    <div>
                      <label class="block font-medium text-xs text-gray-700 dark:text-light mb-1">
                        {{ tm('Merchant_ID') || 'Merchant ID' }} <span class="text-red-500">*</span>
                      </label>
                      <input
                        type="text"
                        v-model="form.datas.merchant_id"
                        placeholder="e.g. ec435210"
                        class="w-full rounded-md border border-gray-300 py-2 px-3 text-sm text-gray-700 focus:outline-none focus:ring focus:ring-primary-100 dark:bg-gray-800 dark:text-gray-200 dark:border-gray-600 font-mono"
                      />
                      <div v-if="form.errors['datas.merchant_id']" class="text-xs text-red-500 mt-1">
                        {{ form.errors['datas.merchant_id'] }}
                      </div>
                    </div>

                    <!-- Currency Selection -->
                    <div>
                      <label class="block font-medium text-xs text-gray-700 dark:text-light mb-1">
                        Settlement Currency
                      </label>
                      <select
                        v-model="form.currency"
                        class="w-full rounded-md border border-gray-300 py-2 px-3 text-sm text-gray-700 focus:outline-none focus:ring focus:ring-primary-100 dark:bg-gray-800 dark:text-gray-200 dark:border-gray-600"
                      >
                        <option value="USD">USD ($)</option>
                        <option value="KHR">KHR (៛)</option>
                      </select>
                    </div>

                    <!-- API Key -->
                    <div class="col-span-1 md:col-span-2">
                      <div class="flex items-center justify-between mb-1">
                        <label class="block font-medium text-xs text-gray-700 dark:text-light">
                          {{ tm('API_Key') || 'API Key / Secret Key' }} <span class="text-red-500">*</span>
                        </label>
                        <span v-if="paymentMethod.datas?.has_api_key" class="text-3xs text-slate-500">
                          Leave blank or untouched to keep the existing configured secret key.
                        </span>
                      </div>
                      <div class="relative">
                        <input
                          :type="showApiKey ? 'text' : 'password'"
                          v-model="form.datas.api_key"
                          :placeholder="paymentMethod.datas?.has_api_key ? '•••••••• (Secret key configured)' : 'Paste ABA PayWay API secret key here...'"
                          class="w-full rounded-md border border-gray-300 py-2 pl-3 pr-10 text-sm text-gray-700 focus:outline-none focus:ring focus:ring-primary-100 dark:bg-gray-800 dark:text-gray-200 dark:border-gray-600 font-mono"
                        />
                        <button
                          type="button"
                          @click="showApiKey = !showApiKey"
                          class="absolute inset-y-0 right-0 pr-3 flex items-center text-slate-400 hover:text-slate-600"
                        >
                          <svg v-if="!showApiKey" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                          </svg>
                          <svg v-else class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l18 18" />
                          </svg>
                        </button>
                      </div>
                      <div v-if="form.errors['datas.api_key']" class="text-xs text-red-500 mt-1">
                        {{ form.errors['datas.api_key'] }}
                      </div>
                    </div>
                  </div>
                </div>
              </transition>
            </div>
          </div>

          <!-- Section 3: Default Payment -->
          <div class="border-t dark:border-primary pt-2">
            <div class="p-4 border-b bg-gray-50 dark:bg-darker dark:border-primary">
              <h5 class="text-sm font-semibold text-gray-600 dark:text-light">
                {{ tm('Default_Payment') || 'Default Payment' }}
              </h5>
            </div>
            <div class="p-5">
              <div class="max-w-md p-4 bg-slate-50 dark:bg-slate-800/40 rounded-lg border border-slate-200 dark:border-slate-700">
                <switch-input
                  v-model="form.is_default_payment"
                  :label="tm('Default_Payment') || 'Default Payment'"
                  :active="tm('Yes') || 'Yes'"
                  :inactive="tm('No') || 'No'"
                />
                <p class="text-xs text-slate-500 dark:text-slate-400 mt-2">
                  When enabled, this Payment Method is automatically selected as the default method when creating new payments. Only one Payment Method per Tenant can be set as the Default Payment. Setting this will automatically unset any previously configured default method.
                </p>
              </div>
            </div>
          </div>

          <!-- Bottom Actions -->
          <div class="p-4 border-t dark:border-primary flex items-center justify-end space-x-2">
            <save-button :disabled="form.processing"></save-button>
            <back-button :url="route('settings.payment-methods.index')"></back-button>
          </div>
        </div>
      </form>
    </div>
  </TenantLayout>
</template>

<script>
import { defineComponent } from 'vue'
import TenantLayout from '@/Layouts/TenantLayout.vue'
import TextInput from '@/Components/Form/TextInput.vue'
import TextareaInput from '@/Components/Form/TextareaInput.vue'
import SwitchInput from '@/Components/Forms/SwitchInput.vue'
import BackButton from '@/Components/Button/Back.vue'
import SaveButton from '@/Components/Button/Save.vue'
import JetBanner from '@/Jetstream/Banner.vue'
import { Head, Link } from '@inertiajs/inertia-vue3'
import { useI18n } from 'vue-i18n'
import { lang } from '@/Lang'
import { useToast } from 'vue-toastification'

export default defineComponent({
  components: {
    TenantLayout,
    Head,
    Link,
    TextInput,
    TextareaInput,
    SwitchInput,
    BackButton,
    SaveButton,
    JetBanner,
  },
  props: {
    paymentMethod: {
      type: Object,
      required: true,
    },
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
      showApiKey: false,
      qrCodePreview: this.paymentMethod.qr_code_url || null,
      form: this.$inertia.form({
        _method: 'PUT',
        name: this.paymentMethod.name || '',
        order: this.paymentMethod.order ?? 0,
        description: this.paymentMethod.description || '',
        print: Boolean(this.paymentMethod.print),
        is_disable: Boolean(this.paymentMethod.is_disable),
        is_sponsor: Boolean(this.paymentMethod.is_sponsor),
        is_default_payment: Boolean(this.paymentMethod.is_default_payment),
        provider: this.paymentMethod.provider || '',
        is_activate: Boolean(this.paymentMethod.is_activate),
        currency: this.paymentMethod.currency || 'USD',
        datas: {
          merchant_id: this.paymentMethod.datas?.merchant_id || '',
          api_key: this.paymentMethod.datas?.api_key || '',
        },
        qr_code: null,
        remove_qr_code: false,
      }),
    }
  },
  methods: {
    triggerFileInput() {
      this.$refs.fileInput.click()
    },
    handleQrCodeChange(e) {
      const file = e.target.files?.[0]
      if (file) {
        if (file.size > 4 * 1024 * 1024) {
          this.toast.error('File size must not exceed 4MB')
          return
        }
        this.form.qr_code = file
        this.form.remove_qr_code = false
        this.qrCodePreview = URL.createObjectURL(file)
      }
    },
    removeQrCode() {
      this.form.qr_code = null
      this.form.remove_qr_code = true
      this.qrCodePreview = null
      if (this.$refs.fileInput) {
        this.$refs.fileInput.value = ''
      }
    },
    update() {
      this.form.post(route('settings.payment-methods.update', this.paymentMethod.id), {
        preserveScroll: true,
        onSuccess: () => {
          this.toast.success(
            this.tm('Payment_Method_updated_successfully') || 'Payment Method updated successfully'
          )
        },
      })
    },
  },
})
</script>
