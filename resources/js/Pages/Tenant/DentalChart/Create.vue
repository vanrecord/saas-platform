<template>
  <TenantLayout>
    <Head title="Create Dental Chart" />

    <div class="max-w-2xl mx-auto">
      <div class="flex items-center justify-between mb-6">
        <div>
          <h1 class="text-2xl font-black text-slate-900 tracking-tight">Create Dental Chart</h1>
          <p class="text-sm text-slate-500 mt-1">Initialize a new dental examination chart session for a patient</p>
        </div>

        <Link
          :href="route('dental-charts.index')"
          class="px-3.5 py-1.5 rounded-lg text-xs font-bold text-slate-600 border border-slate-300 hover:bg-slate-50 transition-colors"
        >
          &larr; Back to Charts
        </Link>
      </div>

      <div class="bg-white border border-slate-200 rounded-2xl p-6 sm:p-8 shadow-sm">
        <form @submit.prevent="submit">
          <!-- Patient Field -->
          <div class="mb-5">
            <label class="block text-xs font-extrabold text-slate-700 uppercase tracking-wider mb-2">
              Patient *
            </label>
            <select
              v-model="form.patient_id"
              required
              class="w-full text-sm border-slate-300 rounded-xl focus:ring-blue-500 focus:border-blue-500 py-2.5"
            >
              <option value="" disabled>-- Select Patient --</option>
              <option
                v-for="p in patients"
                :key="p.id"
                :value="p.id"
              >
                {{ p.full_name }} ({{ p.patient_number }})
              </option>
            </select>
            <div v-if="form.errors.patient_id" class="text-xs text-rose-500 mt-1 font-semibold">
              {{ form.errors.patient_id }}
            </div>
          </div>

          <!-- Date and Dentition Type Grid -->
          <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-5">
            <div>
              <label class="block text-xs font-extrabold text-slate-700 uppercase tracking-wider mb-2">
                Chart Date *
              </label>
              <input
                type="date"
                v-model="form.chart_date"
                required
                class="w-full text-sm border-slate-300 rounded-xl focus:ring-blue-500 focus:border-blue-500 py-2.5"
              />
              <div v-if="form.errors.chart_date" class="text-xs text-rose-500 mt-1 font-semibold">
                {{ form.errors.chart_date }}
              </div>
            </div>

            <div>
              <label class="block text-xs font-extrabold text-slate-700 uppercase tracking-wider mb-2">
                Dentition Type
              </label>
              <select
                v-model="form.dentition_type"
                class="w-full text-sm border-slate-300 rounded-xl focus:ring-blue-500 focus:border-blue-500 py-2.5"
              >
                <option value="mixed">Mixed (Permanent &amp; Primary)</option>
                <option value="permanent">Permanent Only</option>
                <option value="primary">Primary / Deciduous Only</option>
              </select>
            </div>
          </div>

          <!-- Examination Notes -->
          <div class="mb-6">
            <label class="block text-xs font-extrabold text-slate-700 uppercase tracking-wider mb-2">
              Initial Examination / Clinical Notes
            </label>
            <textarea
              v-model="form.examination_notes"
              rows="3"
              placeholder="Record any general clinical observations, chief complaint, or initial examination findings..."
              class="w-full text-sm border-slate-300 rounded-xl focus:ring-blue-500 focus:border-blue-500 py-2.5"
            ></textarea>
            <div v-if="form.errors.examination_notes" class="text-xs text-rose-500 mt-1 font-semibold">
              {{ form.errors.examination_notes }}
            </div>
          </div>

          <!-- Action Buttons -->
          <div class="flex items-center justify-end gap-3 pt-4 border-t border-slate-100">
            <Link
              :href="route('dental-charts.index')"
              class="px-4 py-2 rounded-xl text-sm font-bold text-slate-600 hover:bg-slate-100 transition-colors"
            >
              Cancel
            </Link>

            <button
              type="submit"
              :disabled="form.processing"
              class="px-6 py-2.5 rounded-xl text-sm font-bold text-white bg-[#2457c5] hover:bg-[#1d48a3] transition-colors shadow-sm disabled:opacity-50"
            >
              {{ form.processing ? 'Creating Chart...' : 'Create & Open Interactive Chart' }}
            </button>
          </div>
        </form>
      </div>
    </div>
  </TenantLayout>
</template>

<script setup>
import { Head, Link, useForm } from '@inertiajs/inertia-vue3'
import TenantLayout from '@/Layouts/TenantLayout.vue'

const props = defineProps({
  patients: Array,
  selectedPatientId: [Number, String],
})

const form = useForm({
  patient_id: props.selectedPatientId || '',
  chart_date: new Date().toISOString().slice(0, 10),
  dentition_type: 'mixed',
  examination_notes: '',
})

function submit() {
  form.post(route('dental-charts.store'))
}
</script>

