<template>
  <TenantLayout>
    <Head :title="`Edit Condition - ${dentalCondition.name}`" />

    <div class="max-w-2xl mx-auto">
      <div class="flex items-center justify-between mb-6">
        <div>
          <h1 class="text-2xl font-black text-slate-900 tracking-tight">Edit Dental Condition</h1>
          <p class="text-sm text-slate-500 mt-1">{{ dentalCondition.name }} ({{ dentalCondition.code }})</p>
        </div>

        <Link
          :href="route('dental-conditions.index')"
          class="px-3.5 py-1.5 rounded-lg text-xs font-bold text-slate-600 border border-slate-300 hover:bg-slate-50 transition-colors"
        >
          &larr; Back
        </Link>
      </div>

      <div class="bg-white border border-slate-200 rounded-2xl p-6 sm:p-8 shadow-sm">
        <form @submit.prevent="submit">
          <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-5">
            <div>
              <label class="block text-xs font-extrabold text-slate-700 uppercase tracking-wider mb-2">
                Condition Name *
              </label>
              <input
                type="text"
                v-model="form.name"
                required
                class="w-full text-sm border-slate-300 rounded-xl focus:ring-blue-500 focus:border-blue-500 py-2.5"
              />
            </div>

            <div>
              <label class="block text-xs font-extrabold text-slate-700 uppercase tracking-wider mb-2">
                Unique Code *
              </label>
              <input
                type="text"
                v-model="form.code"
                required
                class="w-full text-sm border-slate-300 rounded-xl focus:ring-blue-500 focus:border-blue-500 py-2.5 font-mono"
              />
            </div>
          </div>

          <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-5">
            <div>
              <label class="block text-xs font-extrabold text-slate-700 uppercase tracking-wider mb-2">
                Default Marking Color *
              </label>
              <div class="flex items-center gap-3">
                <input
                  type="color"
                  v-model="form.default_color"
                  class="w-12 h-10 border border-slate-300 rounded-lg p-1 cursor-pointer bg-white"
                />
                <input
                  type="text"
                  v-model="form.default_color"
                  class="w-28 text-sm font-mono border-slate-300 rounded-lg py-2"
                />
              </div>
            </div>

            <div>
              <label class="block text-xs font-extrabold text-slate-700 uppercase tracking-wider mb-2">
                Category
              </label>
              <select
                v-model="form.category"
                class="w-full text-sm border-slate-300 rounded-xl focus:ring-blue-500 focus:border-blue-500 py-2.5"
              >
                <option value="finding">Finding / Pathology</option>
                <option value="treatment">Treatment / Restoration</option>
                <option value="planned">Planned</option>
                <option value="other">Other</option>
              </select>
            </div>
          </div>

          <div class="mb-5">
            <label class="block text-xs font-extrabold text-slate-700 uppercase tracking-wider mb-2">
              Clinical Description
            </label>
            <textarea
              v-model="form.description"
              rows="2"
              class="w-full text-sm border-slate-300 rounded-xl focus:ring-blue-500 focus:border-blue-500 py-2.5"
            ></textarea>
          </div>

          <div class="mb-6">
            <label class="inline-flex items-center gap-2 cursor-pointer">
              <input
                type="checkbox"
                v-model="form.is_active"
                class="rounded border-slate-300 text-blue-600 focus:ring-blue-500 w-4 h-4"
              />
              <span class="text-sm font-medium text-slate-700">Active (show in interactive chart modal &amp; color guide)</span>
            </label>
          </div>

          <div class="flex items-center justify-between pt-4 border-t border-slate-100">
            <button
              type="button"
              @click="destroyCondition"
              class="px-4 py-2 rounded-xl text-sm font-bold text-rose-600 bg-rose-50 hover:bg-rose-100 transition-colors"
            >
              Delete
            </button>

            <div class="flex items-center gap-2">
              <Link
                :href="route('dental-conditions.index')"
                class="px-4 py-2 rounded-xl text-sm font-bold text-slate-600 hover:bg-slate-100 transition-colors"
              >
                Cancel
              </Link>
              <button
                type="submit"
                :disabled="form.processing"
                class="px-6 py-2.5 rounded-xl text-sm font-bold text-white bg-[#2457c5] hover:bg-[#1d48a3] transition-colors shadow-sm disabled:opacity-50"
              >
                {{ form.processing ? 'Updating...' : 'Update Condition' }}
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
  dentalCondition: Object,
})

const form = useForm({
  name: props.dentalCondition.name,
  code: props.dentalCondition.code,
  description: props.dentalCondition.description || '',
  default_color: props.dentalCondition.default_color || '#d83c43',
  category: props.dentalCondition.category || 'finding',
  is_active: Boolean(props.dentalCondition.is_active),
})

function submit() {
  form.put(route('dental-conditions.update', props.dentalCondition.id))
}

function destroyCondition() {
  if (confirm(`Are you sure you want to delete the condition "${props.dentalCondition.name}"?`)) {
    router.delete(route('dental-conditions.destroy', props.dentalCondition.id))
  }
}
</script>

