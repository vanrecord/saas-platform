<template>
  <TenantLayout>
    <Head :title="tm('Create_Payment_Method') || 'Create Payment Method'" />
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
                {{ tm('Create_Payment_Method') || 'Create Payment Method' }}
              </span>
            </div>
          </li>
        </ol>
      </nav>

      <!-- Form Card -->
      <form @submit.prevent="submit">
        <div class="col-span-2 bg-white rounded-md dark:bg-darker shadow-sm">
          <div class="flex items-center justify-between p-4 border-b dark:border-primary">
            <h4 class="text-lg font-semibold text-gray-500 dark:text-light">
              {{ tm('Create_Payment_Method') || 'Create Payment Method' }}
            </h4>
          </div>

          <!-- Section 1: General Information -->
          <div class="p-4 border-b bg-gray-50 dark:bg-darker dark:border-primary">
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
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 p-4 mt-2 bg-slate-50 dark:bg-slate-800/40 rounded-lg border border-slate-200 dark:border-slate-700">
              <!-- Default Print -->
              <div class="p-2 bg-white dark:bg-slate-700 rounded-md border border-slate-100 dark:border-slate-600">
                <switch-input
                  v-model="form.print"
                  :label="tm('Default_Print') || 'Default Print'"
                  :active="tm('Yes') || 'Yes'"
                  :inactive="tm('No') || 'No'"
                />
                <p class="text-3xs text-slate-400 mt-2 text-center">Selected by default when printing receipts.</p>
              </div>

              <!-- Default Payment -->
              <div class="p-2 bg-white dark:bg-slate-700 rounded-md border border-slate-100 dark:border-slate-600">
                <switch-input
                  v-model="form.is_default_payment"
                  :label="tm('Default_Payment') || 'Default Payment'"
                  :active="tm('Yes') || 'Yes'"
                  :inactive="tm('No') || 'No'"
                />
                <p class="text-3xs text-slate-400 mt-2 text-center">Pre-selected by default for new payments.</p>
              </div>

              <!-- Disable Switch -->
              <div class="p-2 bg-white dark:bg-slate-700 rounded-md border border-slate-100 dark:border-slate-600">
                <switch-input
                  v-model="form.is_disable"
                  :label="tm('Disable') || 'Disable'"
                  :active="tm('Inactive') || 'Inactive'"
                  :inactive="tm('Active') || 'Active'"
                />
                <p class="text-3xs text-slate-400 mt-2 text-center">When enabled, hidden from new payment creation.</p>
              </div>

              <!-- Is Sponsor -->
              <div class="p-2 bg-white dark:bg-slate-700 rounded-md border border-slate-100 dark:border-slate-600">
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
                    Recommended format: PNG, JPG, or SVG. Maximum file size: 4MB.
                  </p>
                  <div v-if="form.errors.qr_code" class="text-xs text-red-500 font-semibold">
                    {{ form.errors.qr_code }}
                  </div>
                </div>
              </div>
            </div>
          </div>

          <!-- Actions -->
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
    nextOrder: {
      type: Number,
      default: 1,
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
      qrCodePreview: null,
      form: this.$inertia.form({
        name: '',
        order: this.nextOrder,
        description: '',
        print: false,
        is_disable: false,
        is_sponsor: false,
        is_default_payment: false,
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
    submit() {
      this.form.post(route('settings.payment-methods.store'), {
        preserveScroll: true,
        onSuccess: () => {
          this.toast.success(
            this.tm('Payment_Method_created_successfully') || 'Payment Method created successfully'
          )
        },
      })
    },
  },
})
</script>
