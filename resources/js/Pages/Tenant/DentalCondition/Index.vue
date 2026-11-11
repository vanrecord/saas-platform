<template>
  <TenantLayout>
    <Head title="Dental Conditions" />

    <div class="max-w-7xl mx-auto">
      <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
        <div>
          <h1 class="text-2xl font-black text-slate-900 tracking-tight">Dental Conditions &amp; Legend Guide</h1>
          <p class="text-sm text-slate-500 mt-1">Manage diagnostic conditions, clinical treatments, and colors shown in the chart editor</p>
        </div>

        <div class="flex items-center gap-2">
          <Link
            :href="route('dental-conditions.create')"
            class="inline-flex items-center gap-1.5 px-4 py-2 rounded-lg text-sm font-bold text-white bg-[#2457c5] hover:bg-[#1d48a3] shadow-sm transition-colors"
          >
            + New Condition
          </Link>
        </div>
      </div>

      <div class="bg-white border border-slate-200 rounded-xl shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
          <table class="w-full text-left text-sm text-slate-600">
            <thead class="bg-slate-50 border-b border-slate-200 text-xs uppercase font-extrabold text-slate-500 tracking-wider">
              <tr>
                <th class="px-6 py-3.5">Color Swatch</th>
                <th class="px-6 py-3.5">Condition Name</th>
                <th class="px-6 py-3.5">Unique Code</th>
                <th class="px-6 py-3.5">Category</th>
                <th class="px-6 py-3.5">Status</th>
                <th class="px-6 py-3.5">Findings Count</th>
                <th class="px-6 py-3.5 text-right">Actions</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
              <tr
                v-for="cond in conditions.data"
                :key="cond.id"
                class="hover:bg-slate-50/70 transition-colors"
              >
                <td class="px-6 py-4 whitespace-nowrap">
                  <div class="flex items-center gap-2">
                    <span
                      class="w-5 h-5 rounded-md border border-black/20 shadow-sm flex-shrink-0"
                      :style="{ background: cond.default_color }"
                    ></span>
                    <code class="text-xs text-slate-600 font-mono">{{ cond.default_color }}</code>
                  </div>
                </td>
                <td class="px-6 py-4 font-bold text-slate-900">
                  {{ cond.name }}
                  <span v-if="cond.description" class="block text-xs font-normal text-slate-400 mt-0.5">
                    {{ cond.description }}
                  </span>
                </td>
                <td class="px-6 py-4">
                  <span class="px-2 py-0.5 rounded bg-slate-100 text-slate-600 font-mono text-xs">
                    {{ cond.code }}
                  </span>
                </td>
                <td class="px-6 py-4">
                  <span class="px-2.5 py-1 rounded-full text-xs font-semibold uppercase tracking-wider bg-slate-100 text-slate-700">
                    {{ cond.category || 'finding' }}
                  </span>
                </td>
                <td class="px-6 py-4">
                  <span
                    :class="[
                      'px-2.5 py-1 rounded-full text-xs font-semibold uppercase tracking-wider',
                      cond.is_active
                        ? 'bg-emerald-50 text-emerald-700 border border-emerald-200'
                        : 'bg-slate-100 text-slate-400'
                    ]"
                  >
                    {{ cond.is_active ? 'Active' : 'Inactive' }}
                  </span>
                </td>
                <td class="px-6 py-4 text-slate-700 font-medium">
                  {{ cond.findings_count || 0 }} findings
                </td>
                <td class="px-6 py-4 text-right whitespace-nowrap">
                  <Link
                    :href="route('dental-conditions.edit', cond.id)"
                    class="px-3 py-1.5 rounded-lg text-xs font-bold text-slate-700 border border-slate-300 hover:bg-slate-50 transition-colors"
                  >
                    Edit
                  </Link>
                </td>
              </tr>

              <tr v-if="!conditions.data || conditions.data.length === 0">
                <td colspan="7" class="px-6 py-12 text-center text-slate-400">
                  No conditions found. Click "+ New Condition" to create one.
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </TenantLayout>
</template>

<script setup>
import { Head, Link } from '@inertiajs/inertia-vue3'
import TenantLayout from '@/Layouts/TenantLayout.vue'

defineProps({
  conditions: Object,
})
</script>

