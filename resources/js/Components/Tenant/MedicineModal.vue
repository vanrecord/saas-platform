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
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z" />
              </svg>
            </span>
            <div>
              <h3 class="text-base font-bold text-[#152B45] dark:text-light" id="modal-title">
                {{ isEditing ? (tm('Edit_Medicine') || 'Edit Medicine') : (tm('Create_Medicine') || 'Create Medicine') }}
              </h3>
              <p class="text-xs text-slate-500 dark:text-slate-400">
                {{ isEditing ? (tm('Edit_Medicine_Details') || 'Update medicine details') : (tm('Add_New_Medicine_Record') || 'Add a new medicine record') }}
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

        <!-- Modal Form Body -->
        <form @submit.prevent="submit" class="p-6 space-y-4">
          <!-- Medicine Name (Required) -->
          <div>
            <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-1" for="medicine-name-input">
              {{ tm('Medicine_Name') || 'Medicine Name' }} <span class="text-red-500">*</span>
            </label>
            <input
              id="medicine-name-input"
              v-model="form.name"
              type="text"
              required
              maxlength="255"
              :placeholder="tm('Enter_medicine_name') || 'Enter medicine name'"
              class="w-full rounded-lg border border-slate-300 px-3.5 py-2 text-sm text-[#152B45] placeholder-slate-400 focus:border-[#0D63D6] focus:ring-2 focus:ring-blue-100 focus:outline-none dark:bg-slate-800 dark:border-slate-600 dark:text-white transition"
              :class="{ 'border-red-500 focus:border-red-500 focus:ring-red-100': form.errors.name }"
            />
            <p v-if="form.errors.name" class="mt-1 text-xs text-red-500">
              {{ form.errors.name }}
            </p>
          </div>

          <!-- Description (Optional) -->
          <div>
            <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-1" for="medicine-description-input">
              {{ tm('Description') || 'Description' }}
            </label>
            <textarea
              id="medicine-description-input"
              v-model="form.description"
              rows="4"
              :placeholder="tm('Enter_description') || 'Enter description'"
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
              class="px-4 py-2 text-xs font-semibold text-slate-600 hover:text-slate-800 border border-slate-300 rounded-lg hover:bg-slate-50 dark:text-gray-300 dark:border-gray-600 dark:hover:bg-gray-700 transition"
            >
              {{ tm('Cancel') || 'Cancel' }}
            </button>
            <button
              type="submit"
              :disabled="form.processing"
              class="inline-flex items-center px-4 py-2 text-xs font-semibold text-white bg-primary hover:bg-primary-dark rounded-lg transition shadow-xs disabled:opacity-50"
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
              <span>{{ form.processing ? (tm('Saving') || 'Saving...') : (isEditing ? (tm('Save') || 'Update Medicine') : (tm('Save') || 'Create Medicine')) }}</span>
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

export default defineComponent({
  name: 'MedicineModal',
  props: {
    show: {
      type: Boolean,
      default: false,
    },
    medicine: {
      type: Object,
      default: null,
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
        name: '',
        description: '',
      }),
    }
  },
  computed: {
    isEditing() {
      return Boolean(this.medicine && this.medicine.id)
    },
  },
  watch: {
    show(val) {
      if (val) {
        this.populateForm()
      }
    },
    medicine: {
      deep: true,
      handler() {
        if (this.show) {
          this.populateForm()
        }
      },
    },
  },
  methods: {
    populateForm() {
      this.form.clearErrors()
      if (this.medicine && this.medicine.id) {
        this.form.name = this.medicine.name || ''
        this.form.description = this.medicine.description || ''
      } else {
        this.form.reset()
        this.form.name = ''
        this.form.description = ''
      }
    },
    close() {
      this.$emit('close')
      this.form.clearErrors()
    },
    submit() {
      if (this.isEditing) {
        this.form.put(route('medicines.update', this.medicine.id), {
          preserveScroll: true,
          onSuccess: (page) => {
            this.toast.success(this.tm('Medicine_updated_successfully') || 'Medicine updated successfully.')
            this.$emit('saved', { ...this.medicine, name: this.form.name, description: this.form.description })
            this.close()
          },
          onError: (errors) => {
            if (errors) {
              Object.values(errors).flat().forEach((msg) => {
                if (typeof msg === 'string') {
                  this.toast.error(msg)
                }
              })
            }
          },
        })
      } else {
        this.form.post(route('medicines.store'), {
          preserveScroll: true,
          onSuccess: (page) => {
            this.toast.success(this.tm('Medicine_created_successfully') || 'Medicine created successfully.')
            this.$emit('saved')
            this.close()
          },
          onError: (errors) => {
            if (errors) {
              Object.values(errors).flat().forEach((msg) => {
                if (typeof msg === 'string') {
                  this.toast.error(msg)
                }
              })
            }
          },
        })
      }
    },
  },
})
</script>
