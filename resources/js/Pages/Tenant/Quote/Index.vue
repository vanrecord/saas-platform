<template>
  <TenantLayout>
    <Head :title="tm('Quote') || 'Quotes'" />
    <div class="px-4 py-4">
      <jet-banner />

      <!-- Breadcrumb matching Invoice UI -->
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
              <span class="ml-1 text-sm font-medium text-gray-700 hover:text-gray-900 md:ml-2 dark:text-gray-400 dark:hover:text-white">
                {{ tm('Quote') || 'Quotes' }}
              </span>
            </div>
          </li>
        </ol>
      </nav>

      <!-- Main Card matching Invoice UI -->
      <div class="bg-white rounded-md dark:bg-darker shadow-sm">
        <div class="flex items-center justify-between p-4 border-b dark:border-primary">
          <h4 class="text-lg font-semibold text-gray-600 dark:text-light">
            {{ tm('Quote_Lists') || 'Quotes' }}
          </h4>
          <div class="flex items-center space-x-2">
            <Link
              v-if="can(['all', 'quotes:quotes:create', 'quote:create'])"
              :href="route('quotes.create')"
              class="inline-flex items-center px-4 py-2 bg-primary hover:bg-primary-dark border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest transition"
            >
              + {{ tm('Create_Quote') || 'Create Quote' }}
            </Link>
          </div>
        </div>

        <!-- Filter Bar Section (5 Columns Matching Specification) -->
        <div class="p-4 bg-slate-50 border-b border-slate-200 dark:bg-slate-800/40 dark:border-slate-700">
          <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-3 items-end">
            <!-- 1. Start Date -->
            <div>
              <DateInput
                v-model="filters.start_date"
                :label="tm('Start_Date') || 'Start Date'"
                placeholder="YYYY-MM-DD"
                class="w-full"
              />
            </div>

            <!-- 2. End Date -->
            <div>
              <DateInput
                v-model="filters.end_date"
                :label="tm('End_Date') || 'End Date'"
                placeholder="YYYY-MM-DD"
                class="w-full"
              />
            </div>

            <!-- 3. Patient Select matching Invoice UI -->
            <div>
              <label class="block text-xs font-semibold text-slate-600 dark:text-slate-300 mb-1">
                {{ tm('Patient') || 'Patient' }}
              </label>
              <select
                v-model="filters.patient_id"
                class="w-full text-xs rounded-md border-slate-300 shadow-2xs focus:border-primary focus:ring-primary dark:bg-slate-800 dark:border-slate-600 dark:text-white"
              >
                <option value="">{{ tm('All_Patients') || '-- All Patients --' }}</option>
                <option v-for="p in patients" :key="p.id" :value="p.id">
                  {{ p.full_name }} (#{{ p.id_number || p.id }})
                </option>
              </select>
            </div>

            <!-- 4. Quote No -->
            <div>
              <label class="block text-xs font-semibold text-slate-600 dark:text-slate-300 mb-1">
                {{ tm('Quote_No') || 'Quote #' }}
              </label>
              <input
                type="text"
                v-model="filters.quote_no"
                @keyup.enter="applyFilters"
                placeholder="e.g. QUT000073 or 73"
                class="w-full text-xs rounded-md border-slate-300 shadow-2xs focus:border-primary focus:ring-primary dark:bg-slate-800 dark:border-slate-600 dark:text-white py-2 font-mono"
              />
            </div>

            <!-- 5. Status Select matching Invoice UI -->
            <div>
              <label class="block text-xs font-semibold text-slate-600 dark:text-slate-300 mb-1">
                {{ tm('Status') || 'Status' }}
              </label>
              <select
                v-model="filters.status"
                class="w-full text-xs rounded-md border-slate-300 shadow-2xs focus:border-primary focus:ring-primary dark:bg-slate-800 dark:border-slate-600 dark:text-white py-2"
              >
                <option value="">{{ tm('All_Statuses') || '-- All Statuses --' }}</option>
                <option value="draft">Draft</option>
                <option value="approved">Approved</option>
                <option value="rejected">Rejected</option>
                <option value="completed">Completed</option>
                <option value="verified">Verified</option>
                <option value="partially_approved">Partially Approved</option>
                <option value="partially_rejected">Partially Rejected</option>
              </select>
            </div>

            <!-- Action Filter Buttons: span 5 columns with right alignment -->
            <div class="sm:col-span-2 lg:col-span-5 flex items-center justify-end space-x-2 pt-2 border-t border-slate-200/60 dark:border-slate-700">
              <button
                type="button"
                @click="applyFilters"
                class="inline-flex justify-center items-center px-4 py-2 bg-primary hover:bg-primary-dark text-white rounded-md text-xs font-semibold shadow-2xs transition"
              >
                <svg class="w-3.5 h-3.5 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z" />
                </svg>
                Filter
              </button>
              <button
                type="button"
                @click="resetFilters"
                class="inline-flex justify-center items-center px-4 py-2 bg-slate-200 hover:bg-slate-300 text-slate-700 rounded-md text-xs font-semibold transition"
                title="Clear Filters"
              >
                Clear
              </button>
            </div>
          </div>
        </div>

        <!-- DataTable Container matching Invoice UI -->
        <div class="p-4">
          <Table :resource="quotes">
            <!-- 1. Actions (matching Invoice Action icons: View, Edit, Print, Delete) -->
            <template #cell(actions)="{ item: quote }">
              <div class="flex items-center space-x-1.5 whitespace-nowrap">
                <!-- View Quote Detail (opens modal) -->
                <button
                  v-if="can(['all', 'quotes:quotes:view_detail', 'quote:view_detail', 'quote:view'])"
                  type="button"
                  @click="openQuoteDetail(quote)"
                  class="p-1 text-slate-500 hover:text-slate-800 rounded hover:bg-slate-100 transition"
                  title="View Quote Detail"
                >
                  <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                  </svg>
                </button>

                <!-- Edit Quote -->
                <Link
                  v-if="can(['all', 'quotes:quotes:edit', 'quote:edit'])"
                  :href="route('quotes.edit', quote.id)"
                  class="p-1 text-blue-600 hover:text-blue-800 rounded hover:bg-blue-50 transition"
                  title="Edit Quote"
                >
                  <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                  </svg>
                </Link>

                <!-- Print Quotation -->
                <a
                  v-if="can(['all', 'quotes:quotes:print', 'quote:print'])"
                  :href="route('quotes.print', quote.id)"
                  target="_blank"
                  class="p-1 text-slate-600 hover:text-slate-800 rounded hover:bg-slate-100 transition"
                  title="Print Quotation"
                >
                  <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4H7v4a2 2 0 002 2zM7 9V5a2 2 0 012-2h6a2 2 0 012 2v4" />
                  </svg>
                </a>

                <!-- Delete Quote -->
                <button
                  v-if="can(['all', 'quotes:quotes:delete', 'quote:delete'])"
                  type="button"
                  @click="destroy(route('quotes.destroy', quote.id))"
                  class="p-1 text-red-600 hover:text-red-800 rounded hover:bg-red-50 transition"
                  title="Delete Quote"
                >
                  <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                  </svg>
                </button>
              </div>
            </template>

            <!-- 2. Daily Date -->
            <template #cell(estimate_at)="{ item: quote }">
              <span class="text-xs text-slate-700 whitespace-nowrap">
                {{ formatDate(quote.estimate_at) }}
              </span>
            </template>

            <!-- 3. Quote # -->
            <template #cell(code)="{ item: quote }">
              <button
                type="button"
                @click="openQuoteDetail(quote)"
                class="font-bold text-primary hover:underline font-mono text-xs cursor-pointer"
              >
                {{ quote.quote_no || ('QUT' + String(quote.code || quote.id).padStart(6, '0')) }}
              </button>
            </template>

            <!-- 4. ID Patient -->
            <template #cell(patient_id)="{ item: quote }">
              <span class="text-xs font-mono font-bold text-slate-800">
                #{{ quote.patient_id_number || quote.patient?.id_number || quote.patient_id || '-' }}
              </span>
            </template>

            <!-- 5. Patient -->
            <template #cell(patient_name)="{ item: quote }">
              <span class="font-semibold text-slate-800 text-xs">
                {{ quote.patient_name || (quote.patient ? (quote.patient.firstname + ' ' + quote.patient.lastname) : '-') }}
              </span>
            </template>

            <!-- 6. Total ($) -->
            <template #cell(total)="{ item: quote }">
              <span class="font-bold text-slate-800 text-xs text-right block font-mono">
                ${{ formatCurrency(quote.total) }}
              </span>
            </template>

            <!-- 7. Discount ($) -->
            <template #cell(discount)="{ item: quote }">
              <span class="text-xs text-slate-600 text-right block font-mono">
                ${{ formatCurrency(quote.discount_amount || 0) }}
              </span>
            </template>

            <!-- 8. Status (pill badge matching Invoice UI) -->
            <template #cell(status)="{ item: quote }">
              <span
                :class="[
                  'inline-flex items-center px-2 py-0.5 rounded-full text-2xs font-bold uppercase tracking-wider',
                  statusClass(quote.status)
                ]"
              >
                {{ statusLabel(quote.status) }}
              </span>
            </template>
          </Table>
        </div>
      </div>
    </div>

    <!-- Quote Detail Modal (Matching Specification & Screenshot) -->
    <div
      v-if="showDetailModal"
      class="fixed inset-0 z-50 overflow-y-auto bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-3 sm:p-4"
    >
      <div
        class="bg-white dark:bg-darker rounded-xl shadow-2xl max-w-4xl w-full border border-slate-200 dark:border-slate-700 overflow-hidden transform transition-all flex flex-col max-h-[92vh]"
      >
        <!-- Modal Header -->
        <div class="px-6 py-4 border-b border-slate-200 dark:border-slate-700 flex items-center justify-between bg-slate-50/70 dark:bg-slate-800/50">
          <div class="flex items-center space-x-2">
            <h3 class="text-base font-bold text-slate-800 dark:text-white">
              Quote Detail
            </h3>
            <span
              v-if="activeQuote"
              class="font-mono text-xs px-2.5 py-0.5 rounded bg-blue-50 text-[#0D63D6] font-bold border border-blue-200"
            >
              {{ activeQuote.quote_no || ('QUT' + String(activeQuote.code || activeQuote.id).padStart(6, '0')) }}
            </span>
          </div>
          <button
            type="button"
            @click="closeDetailModal"
            class="text-slate-400 hover:text-slate-600 dark:hover:text-slate-200 text-lg leading-none cursor-pointer p-1"
          >
            &times;
          </button>
        </div>

        <!-- Modal Body -->
        <div class="p-6 overflow-y-auto space-y-4 flex-1">
          <!-- Top Row: Patient Info & Search -->
          <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
            <div class="text-xs text-slate-700 dark:text-slate-300">
              <span class="font-bold">Patient:</span>
              <span class="ml-1 text-slate-900 dark:text-white font-semibold">
                {{ activeQuote?.patient_name || activeQuote?.patient?.full_name || 'N/A' }}
              </span>
              <span class="ml-2 font-mono text-slate-500">
                (#{{ activeQuote?.patient_id_number || activeQuote?.patient?.id_number || '-' }})
              </span>
            </div>

            <!-- Search Field matching screenshot -->
            <div class="flex items-center space-x-2 ml-auto">
              <label class="text-xs text-slate-600 dark:text-slate-300 font-semibold">
                Search:
              </label>
              <input
                type="text"
                v-model="detailSearch"
                placeholder=""
                class="text-xs rounded border border-slate-300 dark:bg-slate-800 dark:border-slate-600 dark:text-white py-1 px-2.5 focus:border-primary focus:ring-primary w-48 font-normal"
              />
            </div>
          </div>



          <!-- Items Table matching Specification -->
          <div class="border border-slate-200 dark:border-slate-700 rounded-lg overflow-x-auto">
            <table class="w-full text-left text-xs text-slate-600 dark:text-slate-300">
              <thead class="bg-slate-100 dark:bg-slate-800/80 text-[11px] font-bold text-slate-700 dark:text-slate-200 border-b border-slate-200 dark:border-slate-700">
                <tr>
                  <th class="py-2.5 px-3">Operate Date <span class="text-slate-400 text-[10px]">⇅</span></th>
                  <th class="py-2.5 px-3">Service <span class="text-slate-400 text-[10px]">⇅</span></th>
                  <th class="py-2.5 px-3 text-center">Quantity <span class="text-slate-400 text-[10px]">⇅</span></th>
                  <th class="py-2.5 px-3 text-right">Discoun($) <span class="text-slate-400 text-[10px]">⇅</span></th>
                  <th class="py-2.5 px-3 text-right">Amount($) <span class="text-slate-400 text-[10px]">⇅</span></th>
                  <th class="py-2.5 px-3 text-center">Status <span class="text-slate-400 text-[10px]">⇅</span></th>
                  <th class="py-2.5 px-3 text-center">Action</th>
                </tr>
              </thead>
              <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                <tr
                  v-for="(item, idx) in filteredDetailItems"
                  :key="item.id || idx"
                  class="hover:bg-slate-50 dark:hover:bg-slate-800/50 transition"
                >
                  <!-- 1. Operate Date -->
                  <td class="py-2.5 px-3 font-mono text-slate-600 dark:text-slate-400 whitespace-nowrap">
                    {{ formatDate(item.date) }}
                  </td>

                  <!-- 2. Service -->
                  <td class="py-2.5 px-3 font-medium text-slate-800 dark:text-white">
                    <div>
                      <span>{{ item.item_name || item.service?.name || 'Item #' + (item.service_id || item.id) }}</span>
                      <span v-if="item.teeth_numbers && item.teeth_numbers.length" class="ml-1 text-[11px] text-blue-600 font-mono">
                        (Teeth: {{ item.teeth_numbers.join(', ') }})
                      </span>
                    </div>
                  </td>

                  <!-- 3. Quantity -->
                  <td class="py-2.5 px-3 text-center font-mono">
                    {{ item.quantity }}
                  </td>

                  <!-- 4. Discoun($) -->
                  <td class="py-2.5 px-3 text-right font-mono text-slate-600 dark:text-slate-400">
                    {{ (Number(item.discount) || 0).toFixed(0) }}
                  </td>

                  <!-- 5. Amount($) -->
                  <td class="py-2.5 px-3 text-right font-mono font-bold text-slate-800 dark:text-white">
                    {{ (Number(item.amount) || 0).toFixed(0) }}
                  </td>

                  <!-- 6. Status Badge matching Specification -->
                  <td class="py-2.5 px-3 text-center">
                    <span
                      v-if="item.status === 2"
                      class="inline-block px-2.5 py-1 text-[11px] font-bold rounded bg-[#00a65a] text-white"
                    >
                      Approved
                    </span>
                    <span
                      v-else-if="item.status === 3"
                      class="inline-block px-2.5 py-1 text-[11px] font-bold rounded bg-[#f56954] text-white"
                    >
                      Rejected
                    </span>
                    <span
                      v-else-if="item.status === 4"
                      class="inline-block px-2.5 py-1 text-[11px] font-bold rounded bg-blue-600 text-white"
                    >
                      Completed
                    </span>
                    <span
                      v-else-if="item.status === 5"
                      class="inline-block px-2.5 py-1 text-[11px] font-bold rounded bg-purple-600 text-white"
                    >
                      Verified
                    </span>
                    <span
                      v-else
                      class="inline-block px-2.5 py-1 text-[11px] font-bold rounded bg-slate-500 text-white"
                    >
                      Draft
                    </span>
                  </td>

                  <!-- 7. Action Buttons (Approve in Green, Reject in Yellow/Orange) -->
                  <td class="py-2.5 px-3 text-center">
                    <div class="flex items-center justify-center space-x-1.5">
                      <!-- Approve Button (Green) -->
                      <button
                        v-if="item.status !== 2 && can(['all', 'quotes:quotes:approve', 'quote:approve'])"
                        type="button"
                        @click="approveItem(item)"
                        :disabled="isProcessingAction === item.id"
                        class="px-2.5 py-1 bg-[#00a65a] hover:bg-[#008d4c] text-white text-[11px] font-bold rounded transition shadow-2xs disabled:opacity-50 cursor-pointer"
                      >
                        Approve
                      </button>

                      <!-- Reject Button (Yellow/Amber) -->
                      <button
                        v-if="item.status !== 3 && can(['all', 'quotes:quotes:reject', 'quote:reject'])"
                        type="button"
                        @click="promptRejectItem(item)"
                        :disabled="isProcessingAction === item.id"
                        class="px-2.5 py-1 bg-[#f39c12] hover:bg-[#e08e0b] text-white text-[11px] font-bold rounded transition shadow-2xs disabled:opacity-50 cursor-pointer"
                      >
                        Reject
                      </button>
                    </div>
                  </td>
                </tr>

                <tr v-if="!filteredDetailItems.length">
                  <td colspan="7" class="py-8 text-center text-slate-400">
                    No items found.
                  </td>
                </tr>
              </tbody>
            </table>
          </div>

          <div class="flex items-center justify-between text-xs text-slate-500 pt-1">
            <div>
              Showing {{ filteredDetailItems.length ? 1 : 0 }} to {{ filteredDetailItems.length }} of {{ detailItems.length }} entries
            </div>
            <div class="inline-flex rounded border border-slate-300 dark:border-slate-600 overflow-hidden text-xs">
              <button
                type="button"
                disabled
                class="px-2.5 py-1 bg-slate-50 dark:bg-slate-800 text-slate-400 border-r border-slate-300 dark:border-slate-600 cursor-not-allowed"
              >
                Previous
              </button>
              <button
                type="button"
                class="px-2.5 py-1 bg-[#152B45] text-white font-bold"
              >
                1
              </button>
              <button
                type="button"
                disabled
                class="px-2.5 py-1 bg-slate-50 dark:bg-slate-800 text-slate-400 border-l border-slate-300 dark:border-slate-600 cursor-not-allowed"
              >
                Next
              </button>
            </div>
          </div>
        </div>

        <!-- Modal Footer matching Screenshot ([Print], [Approve], [Reject], [Cancel]) -->
        <div class="px-6 py-3.5 bg-slate-50 dark:bg-slate-800/80 border-t border-slate-200 dark:border-slate-700 flex items-center justify-end space-x-2">
          <!-- Print Button (Cyan / Teal) -->
          <a
            v-if="activeQuote"
            :href="route('quotes.print', activeQuote.id)"
            target="_blank"
            class="inline-flex items-center gap-1.5 px-4 py-2 bg-[#00c0ef] hover:bg-[#00a7d0] text-white text-xs font-bold rounded transition shadow-2xs"
          >
            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4H7v4a2 2 0 002 2zM7 9V5a2 2 0 012-2h6a2 2 0 012 2v4" />
            </svg>
            Print
          </a>

          <!-- Approve All Drafts (Green) -->
          <button
            type="button"
            @click="approveAllDrafts"
            class="px-4 py-2 bg-[#00a65a] hover:bg-[#008d4c] text-white text-xs font-bold rounded transition shadow-2xs cursor-pointer"
          >
            Approve
          </button>

          <!-- Reject All Drafts (Yellow/Amber) -->
          <button
            type="button"
            @click="rejectAllDrafts"
            class="px-4 py-2 bg-[#f39c12] hover:bg-[#e08e0b] text-white text-xs font-bold rounded transition shadow-2xs cursor-pointer"
          >
            Reject
          </button>

          <!-- Cancel Button (Red) -->
          <button
            type="button"
            @click="closeDetailModal"
            class="inline-flex items-center gap-1 px-4 py-2 bg-[#dd4b39] hover:bg-[#d73925] text-white text-xs font-bold rounded transition shadow-2xs cursor-pointer"
          >
            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
            </svg>
            Cancel
          </button>
        </div>
      </div>
    </div>
  </TenantLayout>
</template>

<script>
import TenantLayout from "@/Layouts/TenantLayout.vue";
import { Head, Link } from "@inertiajs/inertia-vue3";
import JetBanner from "@/Jetstream/Banner.vue";
import { Table } from "@/Components/Datatables";
import DateInput from "@/Components/Forms/DateInput.vue";
import Destory from "@/Libs/Action/Delete.vue";
import { useI18n } from "vue-i18n";
import { lang } from "@/Lang";
import { useToast } from "vue-toastification";
import axios from "axios";
import dayjs from "dayjs";

export default {
  mixins: [Destory],
  components: {
    TenantLayout,
    Head,
    Link,
    JetBanner,
    Table,
    DateInput,
  },
  props: {
    quotes: Object,
    patients: Array,
    statusOptions: Object,
  },
  setup() {
    const { tm } = useI18n({
      inheritLocale: true,
      messages: lang,
    });
    const toast = useToast();
    return { tm, toast };
  },
  data() {
    const urlParams = new URLSearchParams(window.location.search);
    return {
      filters: {
        start_date: urlParams.get("filter[start_date]") || "",
        end_date: urlParams.get("filter[end_date]") || "",
        patient_id: urlParams.get("filter[patient_id]") || "",
        quote_no: urlParams.get("filter[quote_no]") || "",
        status: urlParams.get("filter[status]") || "",
      },
      showDetailModal: false,
      activeQuote: null,
      detailItems: [],
      detailSearch: "",
      isProcessingAction: null,
      isAddingItem: false,
      activeServices: [],
      activeProducts: [],
      newItem: {
        detailable_id: "",
        item_type: "service",
        quantity: 1,
        unit_price: 0,
        amount: 0,
      },
    };
  },
  computed: {
    filteredDetailItems() {
      if (!this.detailSearch) return this.detailItems;
      const q = this.detailSearch.toLowerCase();
      return this.detailItems.filter((item) => {
        return (
          (item.item_name && item.item_name.toLowerCase().includes(q)) ||
          (item.service?.name && item.service.name.toLowerCase().includes(q)) ||
          (item.status_label && item.status_label.toLowerCase().includes(q))
        );
      });
    },
  },
  methods: {
    formatDate(dateVal) {
      if (!dateVal) return "-";
      return dayjs(dateVal).format("YYYY-MM-DD");
    },
    formatCurrency(val) {
      const num = parseFloat(val) || 0;
      return num.toLocaleString(undefined, {
        minimumFractionDigits: 2,
        maximumFractionDigits: 2,
      });
    },
    statusLabel(statusCode) {
      const map = {
        1: "Draft",
        2: "Approved",
        3: "Rejected",
        4: "Completed",
        5: "Verified",
        6: "Partially Approved",
        7: "Partially Rejected",
      };
      return map[statusCode] || "Draft";
    },
    statusClass(statusCode) {
      const map = {
        1: "bg-amber-100 text-amber-800",
        2: "bg-emerald-100 text-emerald-800 font-bold",
        3: "bg-rose-100 text-rose-800 font-bold",
        4: "bg-blue-100 text-blue-800 font-bold",
        5: "bg-purple-100 text-purple-800 font-bold",
        6: "bg-sky-100 text-sky-800 font-bold",
        7: "bg-amber-100 text-amber-800 font-bold",
      };
      return map[statusCode] || map[1];
    },
    applyFilters() {
      if (this.filters.start_date && this.filters.end_date && this.filters.end_date < this.filters.start_date) {
        alert("End Date cannot be earlier than Start Date.");
        return;
      }

      const query = {};
      if (this.filters.start_date) query["filter[start_date]"] = this.filters.start_date;
      if (this.filters.end_date) query["filter[end_date]"] = this.filters.end_date;
      if (this.filters.patient_id) query["filter[patient_id]"] = this.filters.patient_id;
      if (this.filters.quote_no) query["filter[quote_no]"] = this.filters.quote_no;
      if (this.filters.status) query["filter[status]"] = this.filters.status;

      this.$inertia.get(route("quotes.index"), query, {
        preserveState: true,
        preserveScroll: true,
      });
    },
    resetFilters() {
      this.filters.start_date = "";
      this.filters.end_date = "";
      this.filters.patient_id = "";
      this.filters.quote_no = "";
      this.filters.status = "";

      this.$inertia.get(route("quotes.index"), {}, {
        preserveState: true,
        preserveScroll: true,
      });
    },
    async openQuoteDetail(quote) {
      this.activeQuote = quote;
      this.detailSearch = "";
      this.showDetailModal = true;
      try {
        const resp = await axios.get(route("quotes.show", quote.id));
        if (resp.data) {
          this.activeQuote = resp.data.quote;
          this.detailItems = resp.data.items || [];
          this.activeServices = resp.data.services || [];
          this.activeProducts = resp.data.products || [];
        }
      } catch (err) {
        console.error("Failed to load quote details:", err);
      }
    },
    closeDetailModal() {
      this.showDetailModal = false;
      this.activeQuote = null;
      this.detailItems = [];
      this.detailSearch = "";
    },
    async approveItem(item) {
      if (!this.activeQuote) return;
      this.isProcessingAction = item.id;
      try {
        const resp = await axios.post(
          route("quotes.items.approve", { quote: this.activeQuote.id, item: item.id })
        );
        if (resp.data.success) {
          item.status = 2;
          item.status_label = "Approved";
          if (resp.data.estimate) {
            this.activeQuote.status = resp.data.estimate.status;
          }
          if (this.toast && typeof this.toast.success === "function") {
            this.toast.success("Item approved successfully.");
          }
          this.$inertia.reload({ preserveScroll: true });
        }
      } catch (err) {
        alert(err.response?.data?.message || "Failed to approve item.");
      } finally {
        this.isProcessingAction = null;
      }
    },
    async promptRejectItem(item) {
      const reason = prompt("Please enter the reason for rejection:", "");
      if (reason === null) return;

      this.isProcessingAction = item.id;
      try {
        const resp = await axios.post(
          route("quotes.items.reject", { quote: this.activeQuote.id, item: item.id }),
          { reason: reason || "Rejected by user" }
        );
        if (resp.data.success) {
          item.status = 3;
          item.status_label = "Rejected";
          item.rejected_note = reason;
          if (resp.data.estimate) {
            this.activeQuote.status = resp.data.estimate.status;
          }
          if (this.toast && typeof this.toast.info === "function") {
            this.toast.info("Item marked as rejected.");
          }
          this.$inertia.reload({ preserveScroll: true });
        }
      } catch (err) {
        alert(err.response?.data?.message || "Failed to reject item.");
      } finally {
        this.isProcessingAction = null;
      }
    },
    async approveAllDrafts() {
      const drafts = this.detailItems.filter((i) => i.status === 1);
      if (!drafts.length) {
        alert("No draft items to approve.");
        return;
      }
      for (const d of drafts) {
        await this.approveItem(d);
      }
    },
    async rejectAllDrafts() {
      const drafts = this.detailItems.filter((i) => i.status === 1);
      if (!drafts.length) {
        alert("No draft items to reject.");
        return;
      }
      const reason = prompt("Reason for rejecting all draft items:", "Rejected in bulk");
      if (reason === null) return;
      for (const d of drafts) {
        await axios.post(
          route("quotes.items.reject", { quote: this.activeQuote.id, item: d.id }),
          { reason }
        );
        d.status = 3;
        d.status_label = "Rejected";
      }
      this.$inertia.reload({ preserveScroll: true });
    },
    onNewItemSelect() {
      const val = this.newItem.detailable_id;
      if (!val) return;
      if (val.startsWith("service_")) {
        const id = parseInt(val.replace("service_", ""));
        const s = this.activeServices.find((x) => x.id === id);
        this.newItem.item_type = "service";
        this.newItem.unit_price = s ? s.price : 0;
      } else if (val.startsWith("product_")) {
        const id = parseInt(val.replace("product_", ""));
        const p = this.activeProducts.find((x) => x.id === id);
        this.newItem.item_type = "product";
        this.newItem.unit_price = p ? p.price : 0;
      }
      this.recalculateNewItem();
    },
    recalculateNewItem() {
      const qty = Number(this.newItem.quantity) || 1;
      const price = Number(this.newItem.unit_price) || 0;
      this.newItem.amount = qty * price;
    },
    async addNewItemToQuote() {
      if (!this.activeQuote || !this.newItem.detailable_id) return;
      this.isAddingItem = true;
      try {
        const isProduct = this.newItem.detailable_id.startsWith("product_");
        const cleanId = parseInt(this.newItem.detailable_id.replace(/^(service_|product_)/, ""));

        const payload = {
          item_type: isProduct ? "product" : "service",
          detailable_id: cleanId,
          service_id: isProduct ? null : cleanId,
          quantity: this.newItem.quantity || 1,
          unit_price: this.newItem.unit_price || 0,
          amount: this.newItem.amount || 0,
          date: dayjs().format("YYYY-MM-DD"),
        };

        const resp = await axios.post(
          route("quotes.items.add", { quote: this.activeQuote.id }),
          payload
        );

        if (resp.data.success) {
          const it = resp.data.item;
          this.detailItems.push({
            id: it.id,
            date: it.date,
            item_name: it.item_name,
            quantity: it.quantity,
            discount: it.discount,
            amount: it.amount,
            status: it.status,
            status_label: it.status_label,
            teeth_numbers: it.teeth_numbers || [],
          });

          if (resp.data.estimate) {
            this.activeQuote.status = resp.data.estimate.status;
          }

          // Reset new item input
          this.newItem = {
            detailable_id: "",
            item_type: "service",
            quantity: 1,
            unit_price: 0,
            amount: 0,
          };

          if (this.toast && typeof this.toast.success === "function") {
            this.toast.success("New item added to quote.");
          }
          this.$inertia.reload({ preserveScroll: true });
        }
      } catch (err) {
        alert(err.response?.data?.message || "Failed to add item to quote.");
      } finally {
        this.isAddingItem = false;
      }
    },
  },
};
</script>
