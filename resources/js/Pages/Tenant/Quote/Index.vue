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
                  <th class="py-2.5 px-3 text-center w-10">
                    <input
                      type="checkbox"
                      :checked="isAllSelected"
                      :indeterminate.prop="isIndeterminate"
                      :disabled="!eligibleDraftItems.length"
                      @change="toggleSelectAll"
                      class="rounded border-slate-300 text-[#0D63D6] focus:ring-blue-500 h-4 w-4 disabled:opacity-30 disabled:cursor-not-allowed cursor-pointer"
                      title="Select all eligible draft items"
                    />
                  </th>
                  <th class="py-2.5 px-3">Operate Date <span class="text-slate-400 text-[10px]">⇅</span></th>
                  <th class="py-2.5 px-3">Service <span class="text-slate-400 text-[10px]">⇅</span></th>
                  <th class="py-2.5 px-3 text-center">Quantity <span class="text-slate-400 text-[10px]">⇅</span></th>
                  <th class="py-2.5 px-3 text-right">Discount($) <span class="text-slate-400 text-[10px]">⇅</span></th>
                  <th class="py-2.5 px-3 text-right">Amount($) <span class="text-slate-400 text-[10px]">⇅</span></th>
                  <th class="py-2.5 px-3 text-center">Status <span class="text-slate-400 text-[10px]">⇅</span></th>
                </tr>
              </thead>
              <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                <tr
                  v-for="(item, idx) in filteredDetailItems"
                  :key="item.id || idx"
                  class="hover:bg-slate-50 dark:hover:bg-slate-800/50 transition"
                  :class="{ 'bg-blue-50/40 dark:bg-blue-900/10': selectedItemIds.includes(item.id) }"
                >
                  <!-- 1. Checkbox Column -->
                  <td class="py-2.5 px-3 text-center">
                    <input
                      type="checkbox"
                      :value="item.id"
                      v-model="selectedItemIds"
                      :disabled="Number(item.status) !== 1"
                      class="rounded border-slate-300 text-[#0D63D6] focus:ring-blue-500 h-4 w-4 disabled:opacity-30 disabled:cursor-not-allowed cursor-pointer"
                    />
                  </td>

                  <!-- 2. Operate Date -->
                  <td class="py-2.5 px-3 font-mono text-slate-600 dark:text-slate-400 whitespace-nowrap">
                    {{ formatDate(item.date) }}
                  </td>

                  <!-- 3. Service -->
                  <td class="py-2.5 px-3 font-medium text-slate-800 dark:text-white">
                    <div>
                      <span>{{ item.item_name || item.service?.name || 'Item #' + (item.service_id || item.id) }}</span>
                      <span v-if="item.teeth_numbers && item.teeth_numbers.length" class="ml-1 text-[11px] text-blue-600 font-mono">
                        (Teeth: {{ item.teeth_numbers.join(', ') }})
                      </span>
                      <span
                        v-if="item.is_invoiced || item.medical_detail_id"
                        class="ml-1.5 inline-block px-1.5 py-0.5 text-[10px] font-semibold rounded bg-sky-100 text-sky-800 dark:bg-sky-900/40 dark:text-sky-300 border border-sky-200 dark:border-sky-800"
                        title="Included in Invoice"
                      >
                        Invoiced
                      </span>
                    </div>
                  </td>

                  <!-- 4. Quantity -->
                  <td class="py-2.5 px-3 text-center font-mono">
                    {{ item.quantity }}
                  </td>

                  <!-- 5. Discount($) -->
                  <td class="py-2.5 px-3 text-right font-mono text-slate-600 dark:text-slate-400">
                    {{ (Number(item.discount) || 0).toFixed(0) }}
                  </td>

                  <!-- 6. Amount($) -->
                  <td class="py-2.5 px-3 text-right font-mono font-bold text-slate-800 dark:text-white">
                    {{ (Number(item.amount) || 0).toFixed(0) }}
                  </td>

                  <!-- 7. Status Badge matching Specification -->
                  <td class="py-2.5 px-3 text-center">
                    <span
                      v-if="Number(item.status) === 2"
                      class="inline-block px-2.5 py-1 text-[11px] font-bold rounded bg-[#00a65a] text-white"
                    >
                      Approved
                    </span>
                    <span
                      v-else-if="Number(item.status) === 3"
                      class="inline-block px-2.5 py-1 text-[11px] font-bold rounded bg-[#f56954] text-white"
                    >
                      Rejected
                    </span>
                    <span
                      v-else-if="Number(item.status) === 4"
                      class="inline-block px-2.5 py-1 text-[11px] font-bold rounded bg-blue-600 text-white"
                    >
                      Completed
                    </span>
                    <span
                      v-else-if="Number(item.status) === 5"
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
        <div class="px-6 py-3.5 bg-slate-50 dark:bg-slate-800/80 border-t border-slate-200 dark:border-slate-700 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
          <!-- Selection info badge on the left side of footer -->
          <div class="text-xs text-slate-600 dark:text-slate-400">
            <span v-if="selectedItemIds.length > 0" class="font-medium text-[#0D63D6] dark:text-blue-400">
              Selected <strong>{{ selectedItemIds.length }}</strong> item(s) (Total: ${{ selectedItemsAmount.toFixed(2) }})
            </span>
            <span v-else class="text-slate-400">
              Select draft items using checkboxes to approve or reject.
            </span>
          </div>

          <div class="flex items-center space-x-2 self-end sm:self-auto shrink-0">
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

            <!-- ONE Approve Button (Green) -->
            <button
              v-if="can(['all', 'quotes:quotes:approve', 'quote:approve'])"
              type="button"
              @click="approveSelected"
              :disabled="isProcessingAction"
              class="px-4 py-2 bg-[#00a65a] hover:bg-[#008d4c] text-white text-xs font-bold rounded transition shadow-2xs cursor-pointer disabled:opacity-50"
            >
              <span v-if="isProcessingAction && currentAction === 'approve'">Approving...</span>
              <span v-else>Approve</span>
            </button>

            <!-- ONE Reject Button (Yellow/Amber) -->
            <button
              v-if="can(['all', 'quotes:quotes:reject', 'quote:reject'])"
              type="button"
              @click="rejectSelected"
              :disabled="isProcessingAction"
              class="px-4 py-2 bg-[#f39c12] hover:bg-[#e08e0b] text-white text-xs font-bold rounded transition shadow-2xs cursor-pointer disabled:opacity-50"
            >
              <span v-if="isProcessingAction && currentAction === 'reject'">Rejecting...</span>
              <span v-else>Reject</span>
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
    </div>
  </TenantLayout>
</template>

<script>
import TenantLayout from "@/Layouts/TenantLayout.vue";
import { Head, Link } from "@inertiajs/inertia-vue3";
import { Inertia } from "@inertiajs/inertia";
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
      selectedItemIds: [],
      detailSearch: "",
      isProcessingAction: false,
      currentAction: null,
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
    eligibleDraftItems() {
      return this.detailItems.filter((i) => Number(i.status) === 1);
    },
    isAllSelected() {
      return (
        this.eligibleDraftItems.length > 0 &&
        this.eligibleDraftItems.every((i) => this.selectedItemIds.includes(i.id))
      );
    },
    isIndeterminate() {
      const selectedDraftCount = this.eligibleDraftItems.filter((i) =>
        this.selectedItemIds.includes(i.id)
      ).length;
      return selectedDraftCount > 0 && selectedDraftCount < this.eligibleDraftItems.length;
    },
    selectedItemsAmount() {
      return this.detailItems
        .filter((i) => this.selectedItemIds.includes(i.id))
        .reduce((sum, i) => sum + (Number(i.amount) || 0), 0);
    },
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
    toggleSelectAll(event) {
      if (event.target.checked) {
        this.selectedItemIds = this.eligibleDraftItems.map((i) => i.id);
      } else {
        this.selectedItemIds = [];
      }
    },
    async openQuoteDetail(quote) {
      this.activeQuote = quote;
      this.detailSearch = "";
      this.selectedItemIds = [];
      this.showDetailModal = true;
      await this.refreshQuoteDetail();
    },
    async refreshQuoteDetail() {
      if (!this.activeQuote) return;
      try {
        const resp = await axios.get(route("quotes.show", this.activeQuote.id));
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
      this.selectedItemIds = [];
      this.detailSearch = "";
      this.isProcessingAction = false;
      this.currentAction = null;
    },
    async approveSelected() {
      if (!this.activeQuote) return;

      // Step 1: Validate selected items
      if (!this.selectedItemIds.length) {
        if (this.$swal) {
          this.$swal({
            icon: "warning",
            title: "No Items Selected",
            text: "Please select at least one quote item before approving.",
          });
        } else {
          alert("Please select at least one quote item before approving.");
        }
        return;
      }

      // Step 2: Confirm operation
      const count = this.selectedItemIds.length;
      const totalAmount = this.selectedItemsAmount.toFixed(2);
      let confirmed = false;

      if (this.$swal) {
        const result = await this.$swal({
          title: "Confirm Approval",
          html: `Are you sure you want to approve the <strong>${count}</strong> selected quote item(s) ($${totalAmount}) and create an invoice using these items?`,
          icon: "question",
          showCancelButton: true,
          confirmButtonText: "Yes, Approve & Invoice",
          cancelButtonText: "Cancel",
          customClass: {
            confirmButton: "inline-flex items-center mx-2 px-4 py-2 bg-[#00a65a] hover:bg-[#008d4c] text-white rounded-md font-semibold text-xs uppercase tracking-widest cursor-pointer",
            cancelButton: "inline-flex items-center justify-center px-4 py-2 bg-slate-200 hover:bg-slate-300 text-slate-800 rounded-md font-semibold text-xs uppercase tracking-widest cursor-pointer",
          },
          buttonsStyling: false,
        });
        confirmed = result.isConfirmed || result.value;
      } else {
        confirmed = confirm(`Are you sure you want to approve the ${count} selected quote items ($${totalAmount}) and create an invoice using these items?`);
      }

      if (!confirmed) return;

      // Step 3 & 4: Call backend
      this.isProcessingAction = true;
      this.currentAction = "approve";

      try {
        const resp = await axios.post(
          route("quotes.items.approve-selected", { quote: this.activeQuote.id }),
          { item_ids: this.selectedItemIds }
        );

        if (resp.data.success) {
          if (this.toast && typeof this.toast.success === "function") {
            this.toast.success(resp.data.message || "Items approved and invoice created successfully.");
          }

          const invoiceId = resp.data.invoice_id || resp.data.quote?.invoice?.id;
          const redirectUrl = resp.data.redirect_url || (invoiceId ? route("invoices.edit", invoiceId) : null);

          this.selectedItemIds = [];
          this.closeDetailModal();

          if (redirectUrl) {
            Inertia.visit(redirectUrl);
          } else {
            this.$inertia.reload({ preserveScroll: true });
          }
        }
      } catch (err) {
        const msg = err.response?.data?.message || err.message || "Failed to approve items.";
        if (this.$swal) {
          this.$swal({ icon: "error", title: "Error", text: msg });
        } else {
          alert(msg);
        }
      } finally {
        this.isProcessingAction = false;
        this.currentAction = null;
      }
    },
    async rejectSelected() {
      if (!this.activeQuote) return;

      // Step 1: Validate selected items
      if (!this.selectedItemIds.length) {
        if (this.$swal) {
          this.$swal({
            icon: "warning",
            title: "No Items Selected",
            text: "Please select at least one quote item before rejecting.",
          });
        } else {
          alert("Please select at least one quote item before rejecting.");
        }
        return;
      }

      // Step 2: Confirm operation & optional reason
      const count = this.selectedItemIds.length;
      let reason = "";
      let confirmed = false;

      if (this.$swal) {
        const result = await this.$swal({
          title: "Confirm Rejection",
          text: `Are you sure you want to reject the ${count} selected quote item(s)?`,
          input: "text",
          inputPlaceholder: "Reason for rejection (optional)",
          icon: "warning",
          showCancelButton: true,
          confirmButtonText: "Yes, Reject",
          cancelButtonText: "Cancel",
          customClass: {
            confirmButton: "inline-flex items-center mx-2 px-4 py-2 bg-[#f39c12] hover:bg-[#e08e0b] text-white rounded-md font-semibold text-xs uppercase tracking-widest cursor-pointer",
            cancelButton: "inline-flex items-center justify-center px-4 py-2 bg-slate-200 hover:bg-slate-300 text-slate-800 rounded-md font-semibold text-xs uppercase tracking-widest cursor-pointer",
          },
          buttonsStyling: false,
        });
        if (result.isConfirmed || result.value !== undefined) {
          confirmed = true;
          reason = result.value || "Rejected by user";
        }
      } else {
        reason = prompt(`Are you sure you want to reject the ${count} selected quote item(s)? Reason:`, "Rejected by user");
        if (reason !== null) {
          confirmed = true;
        }
      }

      if (!confirmed) return;

      // Step 3: Call backend
      this.isProcessingAction = true;
      this.currentAction = "reject";

      try {
        const resp = await axios.post(
          route("quotes.items.reject-selected", { quote: this.activeQuote.id }),
          { item_ids: this.selectedItemIds, reason }
        );

        if (resp.data.success) {
          // 1. Refresh datatable rows with updated items from server
          if (resp.data.items) {
            this.detailItems = resp.data.items;
          } else {
            await this.refreshQuoteDetail();
          }

          if (resp.data.quote) {
            this.activeQuote = resp.data.quote;
          } else if (resp.data.estimate) {
            this.activeQuote.status = resp.data.estimate.status;
          }

          // 2. Clear selected checkboxes
          this.selectedItemIds = [];

          // 3. Preserve the existing popup (do NOT close modal)
          this.$inertia.reload({ preserveScroll: true });

          if (this.toast && typeof this.toast.info === "function") {
            this.toast.info(resp.data.message || "Selected items rejected.");
          }
        }
      } catch (err) {
        const msg = err.response?.data?.message || err.message || "Failed to reject items.";
        if (this.$swal) {
          this.$swal({ icon: "error", title: "Error", text: msg });
        } else {
          alert(msg);
        }
      } finally {
        this.isProcessingAction = false;
        this.currentAction = null;
      }
    },
  },
};
</script>
