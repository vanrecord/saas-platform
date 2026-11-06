<template>
  <TenantLayout>
    <Head :title="`Edit Dental Chart #${dentalChart.id}`" />

    <div class="max-w-2xl mx-auto">
      <div class="flex items-center justify-between mb-6">
        <div>
          <h1 class="text-2xl font-black text-slate-900 tracking-tight">Edit Dental Chart #{{ dentalChart.id }}</h1>
          <p class="text-sm text-slate-500 mt-1">Patient: {{ dentalChart.patient?.full_name }} (#{{ dentalChart.patient?.patient_number }})</p>
        </div>

        <div class="flex items-center gap-2">
          <Link
            :href="route('dental.show', dentalChart.id)"
            class="px-3.5 py-1.5 rounded-lg text-xs font-bold text-white bg-[#2457c5] hover:bg-[#1d48a3] transition-colors shadow-sm"
          >
            Open Interactive Chart
          </Link>
          <Link
            :href="route('dental-charts.index')"
            class="px-3.5 py-1.5 rounded-lg text-xs font-bold text-slate-600 border border-slate-300 hover:bg-slate-50 transition-colors"
          >
            &larr; Back
          </Link>
        </div>
      </div>

      <div class="bg-white border border-slate-200 rounded-2xl p-6 sm:p-8 shadow-sm">
        <form @submit.prevent="submit">
          <!-- Patient Select -->
          <div class="mb-5">
            <label class="block text-xs font-extrabold text-slate-700 uppercase tracking-wider mb-2">
              Patient *
            </label>
            <select
              v-model="form.patient_id"
              required
              class="w-full text-sm border-slate-300 rounded-xl focus:ring-blue-500 focus:border-blue-500 py-2.5"
            >
              <option
                v-for="p in patients"
                :key="p.id"
                :value="p.id"
              >
                {{ p.full_name }} ({{ p.patient_number }})
              </option>
            </select>
          </div>

          <!-- Date & Dentition Type -->
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
            </div>

            <div>
              <label class="block text-xs font-extrabold text-slate-700 uppercase tracking-wider mb-2">
                Dentition Type
              </label>
              <select
                v-model="form.dentition_type"
                class="w-full text-sm border-slate-300 rounded-xl focus:ring-blue-500 focus:border-blue-500 py-2.5"
              >
                <option value="mixed">Mixed</option>
                <option value="permanent">Permanent</option>
                <option value="primary">Primary / Deciduous</option>
              </select>
            </div>
          </div>

          <!-- Notes -->
          <div class="mb-6">
            <label class="block text-xs font-extrabold text-slate-700 uppercase tracking-wider mb-2">
              Examination / General Notes
            </label>
            <textarea
              v-model="form.examination_notes"
              rows="3"
              class="w-full text-sm border-slate-300 rounded-xl focus:ring-blue-500 focus:border-blue-500 py-2.5"
            ></textarea>
          </div>

          <!-- Actions -->
          <div class="flex items-center justify-between pt-4 border-t border-slate-100">
            <button
              type="button"
              @click="destroyChart"
              class="px-4 py-2 rounded-xl text-sm font-bold text-rose-600 bg-rose-50 hover:bg-rose-100 transition-colors"
            >
              Delete Chart
            </button>

            <div class="flex items-center gap-2">
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
                {{ form.processing ? 'Updating...' : 'Update Chart' }}
              </button>
            </div>
          </div>
        </form>
      </div>
    </div>
  </TenantLayout>
</template>

<script setup>
import { Head, Link, useForm } from '@inertiajs/inertia-vue3'
import { Inertia as router } from '@inertiajs/inertia'
import TenantLayout from '@/Layouts/TenantLayout.vue'

const props = defineProps({
  dentalChart: Object,
  patients: Array,
})

const form = useForm({
  patient_id: props.dentalChart.patient_id,
  chart_date: props.dentalChart.chart_date ? props.dentalChart.chart_date.slice(0, 10) : '',
  dentition_type: props.dentalChart.dentition_type || 'mixed',
  examination_notes: props.dentalChart.examination_notes || '',
})

function submit() {
  form.put(route('dental-charts.update', props.dentalChart.id))
}

function destroyChart() {
  if (confirm('Are you sure you want to delete this dental chart and all its recorded teeth/findings?')) {
    router.delete(route('dental-charts.destroy', props.dentalChart.id))
  }
}
</script>

