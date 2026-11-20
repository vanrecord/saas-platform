<template>
  <div v-if="show" class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
    <!-- Backdrop (static, does not close on backdrop click) -->
    <div class="fixed inset-0 bg-slate-900/50 backdrop-blur-xs transition-opacity"></div>

    <div class="flex min-h-screen items-center justify-center p-4 text-center sm:p-0">
      <div
        class="relative transform overflow-hidden rounded-2xl bg-white dark:bg-darker text-left shadow-2xl transition-all sm:my-8 sm:w-full sm:max-w-lg border border-slate-200 dark:border-slate-700 animate-in fade-in zoom-in duration-150"
      >
        <!-- Modal Header -->
        <div class="flex items-center justify-between border-b border-slate-100 dark:border-slate-700 px-6 py-4 bg-slate-50/50 dark:bg-dark">
          <div class="flex items-center gap-2.5">
            <span class="w-8 h-8 rounded-lg bg-blue-100 dark:bg-blue-900/30 text-[#0D63D6] flex items-center justify-center font-bold text-sm">
              <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <circle cx="12" cy="12" r="9" stroke-width="2" />
                <circle cx="12" cy="12" r="5" stroke-width="2" />
                <circle cx="12" cy="12" r="1.5" fill="currentColor" />
              </svg>
            </span>
            <div>
              <h3 class="text-base font-bold text-[#152B45] dark:text-light" id="modal-title">
                {{ modalTitle }}
              </h3>
              <p class="text-xs text-slate-500 dark:text-slate-400">
                {{ isEditing ? (tm('Edit_Target_Details') || 'Update target details') : (tm('Add_New_Target_Record') || 'Add a new target goal') }}
              </p>
            </div>
          </div>
          <button
            type="button"
            @click="close"
            class="rounded-lg p-1 text-slate-400 hover:bg-slate-100 dark:hover:bg-gray-700 hover:text-slate-600 transition"
          >
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
            </svg>
          </button>
        </div>

        <!-- Form Body -->
        <form @submit.prevent="submit" class="p-6 space-y-4">
          <!-- Doctor Selection (Only if byDoctor is true) -->
          <div v-if="byDoctor">
            <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-1" for="target-doctor-input">
              {{ tm('Doctor') || 'Doctor' }} <span class="text-red-500">*</span>
            </label>
            <select
              id="target-doctor-input"
              v-model="form.doctor_id"
              required
              class="w-full rounded-lg border border-slate-300 px-3.5 py-2 text-sm text-[#152B45] focus:border-[#0D63D6] focus:ring-2 focus:ring-blue-100 focus:outline-none dark:bg-slate-800 dark:border-slate-600 dark:text-white transition"
              :class="{ 'border-red-500 focus:border-red-500 focus:ring-red-100': form.errors.doctor_id }"
            >
              <option value="">-- {{ tm('Select_Doctor') || 'Select Doctor' }} --</option>
              <option v-for="d in doctors" :key="d.id" :value="d.id">
                {{ d.name }}
              </option>
            </select>
            <p v-if="form.errors.doctor_id" class="mt-1 text-xs text-red-500">
              {{ form.errors.doctor_id }}
            </p>
          </div>

          <!-- Date Range Row: Start Date & End Date -->
          <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <!-- Start Date -->
            <div>
              <DateInput
                v-model="form.start_date"
                required="required"
                :label="tm('Start_Date') || 'Start Date'"
                :error="form.errors.start_date"
                placeholder="YYYY-MM-DD"
                class="w-full"
              />
            </div>

            <!-- End Date -->
            <div>
              <DateInput
                v-model="form.end_date"
                required="required"
                :min-date="form.start_date"
                :label="tm('End_Date') || 'End Date'"
                :error="form.errors.end_date"
                placeholder="YYYY-MM-DD"
                class="w-full"
              />
            </div>
          </div>

          <!-- Target Amount ($) -->
          <div>
            <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-1" for="target-amount-input">
              {{ tm('Target_dollar') || 'Target ($)' }} <span class="text-red-500">*</span>
            </label>
            <div class="relative rounded-lg shadow-2xs">
              <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3.5">
                <span class="text-slate-500 sm:text-sm font-semibold">$</span>
              </div>
              <input
                id="target-amount-input"
                v-model.number="form.amount"
                type="number"
                step="any"
                min="0.01"
                required
                :placeholder="tm('Enter_target_amount') || '0.00'"
                class="w-full rounded-lg border border-slate-300 pl-8 pr-3.5 py-2 text-sm text-[#152B45] placeholder-slate-400 focus:border-[#0D63D6] focus:ring-2 focus:ring-blue-100 focus:outline-none dark:bg-slate-800 dark:border-slate-600 dark:text-white transition font-mono"
                :class="{ 'border-red-500 focus:border-red-500 focus:ring-red-100': form.errors.amount }"
              />
            </div>
            <p v-if="form.errors.amount" class="mt-1 text-xs text-red-500">
              {{ form.errors.amount }}
            </p>
          </div>

          <!-- Description (Textarea) -->
          <div>
            <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-1" for="target-description-input">
              {{ tm('Description') || 'Description' }}
            </label>
            <textarea
              id="target-description-input"
              v-model="form.description"
              rows="3"
              maxlength="255"
              :placeholder="tm('Enter_description') || 'Enter target goal notes or description...'"
              class="w-full rounded-lg border border-slate-300 px-3.5 py-2 text-sm text-[#152B45] placeholder-slate-400 focus:border-[#0D63D6] focus:ring-2 focus:ring-blue-100 focus:outline-none dark:bg-slate-800 dark:border-slate-600 dark:text-white transition"
              :class="{ 'border-red-500 focus:border-red-500 focus:ring-red-100': form.errors.description }"
            ></textarea>
            <p v-if="form.errors.description" class="mt-1 text-xs text-red-500">
              {{ form.errors.description }}
            </p>
          </div>

          <!-- Modal Footer Actions -->
          <div class="flex items-center justify-end space-x-3 pt-4 border-t border-slate-100 dark:border-slate-700">
            <button
              type="button"
              @click="close"
              class="px-4 py-2 text-xs font-semibold text-slate-600 hover:text-slate-800 border border-slate-300 rounded-lg hover:bg-slate-50 dark:text-gray-300 dark:border-gray-600 dark:hover:bg-gray-700 transition cursor-pointer"
            >
              {{ tm('Cancel') || 'Cancel' }}
            </button>
            <button
              type="submit"
              :disabled="form.processing"
              class="inline-flex items-center px-4 py-2 text-xs font-semibold text-white bg-primary hover:bg-primary-dark rounded-lg transition shadow-xs disabled:opacity-50 cursor-pointer"
            >
              <svg
                v-if="form.processing"
                class="animate-spin -ml-1 mr-2 h-4 w-4 text-white"
                fill="none"
                viewBox="0 0 24 24"
              >
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
              </svg>
              <span>{{ form.processing ? (tm('Saving') || 'Saving...') : (isEditing ? (tm('Save') || 'Update') : (tm('Save') || 'Save')) }}</span>
            </button>
          </div>
        </form>
      </div>
    </div>
  </div>
</template>

<script>
import { defineComponent } from 'vue'
import { useForm } from '@inertiajs/inertia-vue3'
import { useI18n } from 'vue-i18n'
import { lang } from '@/Lang'
import { useToast } from 'vue-toastification'
import DateInput from '@/Components/Forms/DateInput.vue'

export default defineComponent({
  name: 'TargetModal',
  components: {
    DateInput,
  },
  props: {
    show: {
      type: Boolean,
      default: false,
    },
    target: {
      type: Object,
      default: null,
    },
    byDoctor: {
      type: Boolean,
      default: false,
    },
    doctors: {
      type: Array,
      default: () => [],
    },
  },
  emits: ['close', 'saved'],
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
      form: useForm({
        id: null,
        start_date: '',
        end_date: '',
        doctor_id: '',
        amount: '',
        description: '',
      }),
    }
  },
  computed: {
    isEditing() {
      return Boolean(this.target && (this.target.id || this.form.id))
    },
    modalTitle() {
      if (this.byDoctor) {
        return this.isEditing
          ? (this.tm('Edit_Doctor_Target') || 'Edit Doctor Target')
          : (this.tm('Create_Doctor_Target') || 'Create Doctor Target')
      }
      return this.isEditing
        ? (this.tm('Edit_Clinic_Target') || 'Edit Clinic Target')
        : (this.tm('Create_Clinic_Target') || 'Create Clinic Target')
    },
  },
  watch: {
    show(val) {
      if (val) {
        this.populateForm()
      } else {
        this.form.reset()
        this.form.clearErrors()
      }
    },
    target() {
      if (this.show) {
        this.populateForm()
      }
    },
  },
  methods: {
    populateForm() {
      this.form.clearErrors()
      if (this.target) {
        this.form.id = this.target.id || null
        // Ensure format is YYYY-MM-DD for date inputs
        this.form.start_date = this.formatDateForInput(this.target.start_date || this.target.start_date_formatted)
        this.form.end_date = this.formatDateForInput(this.target.end_date || this.target.end_date_formatted)
        this.form.doctor_id = this.target.doctor_id || ''
        this.form.amount = this.target.amount !== undefined ? Number(this.target.amount) : ''
        this.form.description = this.target.description || ''
      } else {
        this.form.reset()
        this.form.id = null
        this.form.doctor_id = ''
        const today = new Date().toISOString().split('T')[0]
        this.form.start_date = today
        this.form.end_date = today
      }
    },
    formatDateForInput(val) {
      if (!val) return ''
      if (val.includes('/')) {
        const parts = val.split('/')
        if (parts.length === 3) {
          // d/m/Y -> Y-m-d
          return `${parts[2]}-${parts[1].padStart(2, '0')}-${parts[0].padStart(2, '0')}`
        }
      }
      if (val.includes('T')) {
        return val.split('T')[0]
      }
      return val
    },
    close() {
      this.$emit('close')
    },
    submit() {
      const routeBase = this.byDoctor ? 'targets.doctor' : 'targets.clinic'

      if (this.isEditing) {
        this.form.put(route(`${routeBase}.update`, this.form.id), {
          preserveScroll: true,
          onSuccess: () => {
            this.toast.success(this.tm('Target_updated_successfully') || 'Target updated successfully.')
            this.$emit('saved')
            this.close()
          },
          onError: (errors) => {
            const firstErr = Object.values(errors)[0]
            if (firstErr) {
              this.toast.error(firstErr)
            }
          },
        })
      } else {
        this.form.post(route(`${routeBase}.store`), {
          preserveScroll: true,
          onSuccess: () => {
            this.toast.success(this.tm('Target_created_successfully') || 'Target created successfully.')
            this.$emit('saved')
            this.close()
          },
          onError: (errors) => {
            const firstErr = Object.values(errors)[0]
            if (firstErr) {
              this.toast.error(firstErr)
            }
          },
        })
      }
    },
  },
})
</script>
