<template>
  <form @submit.prevent="submit" class="space-y-6">
    <!-- Top Action Header / Breadcrumb -->
    <div class="flex items-center justify-between">
      <h2 class="text-xl font-bold text-slate-800 dark:text-light">
        {{ isEdit ? (tm('Edit_Invoice') || 'Edit Invoice') : (tm('Create_Invoice') || 'Create Invoice') }}
        <span class="text-sm font-mono text-primary font-bold ml-2">
          #{{ form.invoice_number || suggestedInvoiceNumber || '-' }}
        </span>
      </h2>
      <div class="flex items-center space-x-2">
        <BackButton :url="route('invoices.index')">
          {{ tm('Cancel') || 'Cancel' }}
        </BackButton>
        <SaveButton :loading="form.processing" @submit="submit">
          {{ form.processing ? (tm('Saving') || 'Saving...') : (isEdit ? (tm('Update_Invoice') || 'Update Invoice') : (tm('Save_Invoice') || 'Save Invoice')) }}
        </SaveButton>
      </div>
    </div>

    <!-- Error Alert Box -->
    <div v-if="hasErrors" class="p-4 bg-red-50 border border-red-200 rounded-xl text-red-700 text-xs shadow-2xs">
      <div class="font-bold mb-1 flex items-center gap-1.5">
        <svg class="w-4 h-4 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
        </svg>
        Please check and correct the following errors:
      </div>
      <ul class="list-disc list-inside space-y-0.5 ml-1">
        <li v-for="(err, field) in form.errors" :key="field">{{ err }}</li>
      </ul>
    </div>

    <!-- Section 1: Invoice Information -->
    <div class="bg-white dark:bg-darker rounded-xl shadow-xs border border-slate-200 dark:border-slate-700 p-5">
      <h3 class="text-sm font-bold text-slate-700 dark:text-slate-200 uppercase tracking-wider mb-4 pb-2 border-b border-slate-100 dark:border-slate-700 flex items-center">
        <svg class="w-4 h-4 mr-2 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
        </svg>
        1. {{ tm('Invoice_Information') || 'Invoice Information' }}
      </h3>

      <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
        <!-- Patient Name -->
        <div>
          <label class="block text-xs font-bold text-slate-600 dark:text-slate-300 uppercase mb-1">
            {{ tm('Patient_Name') || 'Patient Name' }} <span class="text-red-500">*</span>
          </label>
          <select
            v-model="form.patient_id"
            class="w-full text-xs rounded-lg border-slate-300 shadow-2xs focus:border-primary focus:ring-primary dark:bg-slate-800 dark:border-slate-600 dark:text-white"
            required
          >
            <option value="">{{ tm('Select_Patient') || '-- Choose Patient --' }}</option>
            <option v-for="p in patients" :key="p.id" :value="p.id">
              {{ p.full_name }} (#{{ p.id_number || p.id }}) - {{ p.phone || 'No phone' }}
            </option>
          </select>
          <div v-if="form.errors.patient_id" class="text-xs text-red-500 mt-1">
            {{ form.errors.patient_id }}
          </div>
        </div>

        <!-- Doctor -->
        <div>
          <label class="block text-xs font-bold text-slate-600 dark:text-slate-300 uppercase mb-1">
            {{ tm('Doctor') || 'Doctor' }}
          </label>
          <select
            v-model="form.doctor_id"
            class="w-full text-xs rounded-lg border-slate-300 shadow-2xs focus:border-primary focus:ring-primary dark:bg-slate-800 dark:border-slate-600 dark:text-white"
          >
            <option value="">{{ tm('Select_Doctor') || '-- Choose Doctor --' }}</option>
            <option v-for="d in doctors" :key="d.id" :value="d.id">
              {{ d.name }}
            </option>
          </select>
          <div v-if="form.errors.doctor_id" class="text-xs text-red-500 mt-1">
            {{ form.errors.doctor_id }}
          </div>
        </div>

        <!-- Assistant -->
        <div>
          <label class="block text-xs font-bold text-slate-600 dark:text-slate-300 uppercase mb-1">
            {{ tm('Assistant') || 'Assistant' }}
          </label>
          <select
            v-model="form.assistant_id"
            class="w-full text-xs rounded-lg border-slate-300 shadow-2xs focus:border-primary focus:ring-primary dark:bg-slate-800 dark:border-slate-600 dark:text-white"
          >
            <option value="">{{ tm('Select_Assistant') || '-- Choose Assistant --' }}</option>
            <option v-for="a in assistants" :key="a.id" :value="a.id">
              {{ a.name }}
            </option>
          </select>
          <div v-if="form.errors.assistant_id" class="text-xs text-red-500 mt-1">
            {{ form.errors.assistant_id }}
          </div>
        </div>

        <!-- Invoice # (Read-only) -->
        <div>
          <label class="block text-xs font-bold text-slate-600 dark:text-slate-300 uppercase mb-1">
            {{ tm('Invoice_number') || 'Invoice #' }}
          </label>
          <input
            type="text"
            :value="form.invoice_number || suggestedInvoiceNumber || 'Generating...'"
            readonly
            class="w-full text-xs font-mono font-bold bg-slate-100 text-slate-700 rounded-lg border-slate-300 shadow-2xs cursor-not-allowed dark:bg-slate-700 dark:border-slate-600 dark:text-slate-300"
          />
        </div>

        <!-- Daily Date -->
        <div>
          <DateInput
            v-model="form.date"
            required="required"
            :label="tm('Daily_Date') || 'Daily Date'"
            :error="form.errors.date"
            placeholder="YYYY-MM-DD"
            class="w-full"
          />
        </div>

        <!-- Visit Type -->
        <div>
          <label class="block text-xs font-bold text-slate-600 dark:text-slate-300 uppercase mb-1">
            {{ tm('Visit_Type') || 'Visit Type' }}
          </label>
          <select
            v-model="form.visit_type"
            class="w-full text-xs rounded-lg border-slate-300 shadow-2xs focus:border-primary focus:ring-primary dark:bg-slate-800 dark:border-slate-600 dark:text-white"
          >
            <option value="initial">Initial Visit</option>
            <option value="follow_up">Follow Up</option>
            <option value="consultation">Consultation</option>
            <option value="emergency">Emergency</option>
            <option value="routine">Routine Checkup</option>
          </select>
        </div>

        <!-- Quote # (Estimate ID) -->
        <div>
          <label class="block text-xs font-bold text-slate-600 dark:text-slate-300 uppercase mb-1">
            {{ tm('Quote_No') || 'Quote #' }}
          </label>
          <input
            type="number"
            v-model="form.estimate_id"
            placeholder="e.g. 101"
            class="w-full text-xs rounded-lg border-slate-300 shadow-2xs focus:border-primary focus:ring-primary dark:bg-slate-800 dark:border-slate-600 dark:text-white"
          />
        </div>

        <!-- Diagnosis -->
        <div>
          <label class="block text-xs font-bold text-slate-600 dark:text-slate-300 uppercase mb-1">
            {{ tm('Diagnosis') || 'Diagnosis' }}
          </label>
          <input
            type="text"
            v-model="form.diagnosis"
            placeholder="e.g. Dental caries, pulpitis"
            class="w-full text-xs rounded-lg border-slate-300 shadow-2xs focus:border-primary focus:ring-primary dark:bg-slate-800 dark:border-slate-600 dark:text-white"
          />
        </div>
      </div>
    </div>

    <!-- Section 2: Service & Product Details (Matches Reference Screenshot) -->
    <div class="bg-white dark:bg-darker rounded-xl shadow-xs border border-slate-200 dark:border-slate-700 p-5">
      <!-- 6.1 Top Control Bar -->
      <div class="flex flex-wrap items-center justify-between gap-3 pb-4 border-b border-slate-200 dark:border-slate-700">
        <!-- Left Buttons -->
        <div class="flex items-center space-x-2">
          <!-- + Service Button (Green) -->
          <button
            type="button"
            @click="addServiceRow"
            class="inline-flex items-center px-3.5 py-1.5 bg-[#00a65a] hover:bg-[#008d4c] text-white text-xs font-bold rounded shadow-xs transition"
          >
            + Service
          </button>

          <!-- + Product Button (Blue) -->
          <button
            type="button"
            @click="addProductRow"
            class="inline-flex items-center px-3.5 py-1.5 bg-[#3c8dbc] hover:bg-[#357ca5] text-white text-xs font-bold rounded shadow-xs transition"
          >
            + Product
          </button>
        </div>

        <!-- Right Inputs: Daily Date, Exchange Rate, Discount -->
        <div class="flex flex-wrap items-center gap-4">
          <!-- Daily Date in bar -->
          <div class="flex items-center space-x-2 bg-slate-50 dark:bg-slate-800 px-2.5 py-1 rounded border border-slate-200 dark:border-slate-700">
            <span class="text-xs text-slate-500 font-semibold whitespace-nowrap">Daily Date</span>
            <DateInput
              v-model="form.date"
              placeholder="YYYY-MM-DD"
              class="w-36"
              input-class="py-1 px-2 text-xs"
            />
          </div>

          <!-- Exchange Rate -->
          <div class="flex items-center space-x-1.5 bg-slate-50 dark:bg-slate-800 px-2.5 py-1 rounded border border-slate-200 dark:border-slate-700">
            <span class="text-xs text-slate-500 font-semibold whitespace-nowrap">1$ =</span>
            <input
              type="number"
              step="any"
              v-model="form.rate"
              class="text-xs py-1 px-2 w-20 border border-slate-300 rounded bg-white dark:bg-slate-700 dark:border-slate-600 dark:text-white text-right font-mono"
            />
            <span class="text-xs text-slate-600 font-bold">៛</span>
          </div>

          <!-- Overall Discount -->
          <div class="flex items-center space-x-1.5 bg-slate-50 dark:bg-slate-800 px-2.5 py-1 rounded border border-slate-200 dark:border-slate-700">
            <span class="text-xs text-slate-500 font-semibold whitespace-nowrap">Discount</span>
            <input
              type="number"
              step="any"
              min="0"
              v-model="form.discount_invoice"
              class="text-xs py-1 px-2 w-16 border border-slate-300 rounded bg-white dark:bg-slate-700 dark:border-slate-600 dark:text-white text-right font-mono"
            />
            <select
              v-model="form.discount_inv_as"
              class="text-xs py-1 px-2 border border-slate-300 rounded bg-white dark:bg-slate-700 dark:border-slate-600 dark:text-white"
            >
              <option value="%">%</option>
              <option value="$">$</option>
            </select>
          </div>
        </div>
      </div>

      <!-- 6.2 Detail Table -->
      <div class="overflow-x-auto mt-4">
        <table class="w-full text-left text-xs border-collapse">
          <thead>
            <tr class="border-b border-slate-200 dark:border-slate-700 text-slate-600 dark:text-slate-300 font-bold">
              <th class="py-2.5 px-3 whitespace-nowrap w-32">Operate Date</th>
              <th class="py-2.5 px-3 min-w-[200px]">Service</th>
              <th class="py-2.5 px-3 min-w-[160px]">Treatment teeth</th>
              <th class="py-2.5 px-3 w-20 text-center">Quantity</th>
              <th class="py-2.5 px-3 w-24 text-right">Fee ($)</th>
              <!-- Highlighted Cost header in orange matching screenshot -->
              <th class="py-2.5 px-3 w-24 text-right bg-[#FF8C5A] text-white">Cost</th>
              <th class="py-2.5 px-3 w-32 text-center">Discount</th>
              <th class="py-2.5 px-3 w-28 text-right">Amount ($)</th>
              <th class="py-2.5 px-3 w-16 text-center">Action</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-slate-100 dark:divide-slate-700">
            <tr
              v-for="(row, idx) in form.items"
              :key="row._clientId"
              :class="[
                'transition-colors',
                activeRowIndex === idx
                  ? 'bg-blue-50/70 dark:bg-blue-900/20 ring-1 ring-primary/40'
                  : 'hover:bg-slate-50/70 dark:hover:bg-slate-800/40'
              ]"
              @click="activeRowIndex = idx"
            >
              <!-- 1. Operate Date -->
              <td class="py-2 px-3 whitespace-nowrap">
                <DateInput
                  v-model="row.date"
                  placeholder="YYYY-MM-DD"
                  class="w-36"
                  input-class="py-1 px-2 text-xs"
                />
              </td>

              <!-- 2. Service / Product Selector -->
              <td class="py-2 px-3">
                <div class="flex items-center space-x-1.5">
                  <span
                    :class="[
                      'text-[10px] uppercase font-bold px-1.5 py-0.5 rounded text-white flex-shrink-0',
                      row.item_type === 'product' ? 'bg-[#3c8dbc]' : 'bg-[#00a65a]'
                    ]"
                  >
                    {{ row.item_type === 'product' ? 'PRD' : 'SRV' }}
                  </span>
                  <select
                    v-if="row.item_type === 'service'"
                    v-model="row.detailable_id"
                    @change="onServiceSelected(row)"
                    class="w-full text-xs py-1 px-2 border border-slate-300 rounded bg-white dark:bg-slate-800 dark:border-slate-600 dark:text-white"
                    required
                  >
                    <option value="">-- Choose Service --</option>
                    <option v-for="s in services" :key="s.id" :value="s.id">
                      {{ s.name }} (${{ s.price || 0 }})
                    </option>
                  </select>
                  <select
                    v-else
                    v-model="row.detailable_id"
                    @change="onProductSelected(row)"
                    class="w-full text-xs py-1 px-2 border border-slate-300 rounded bg-white dark:bg-slate-800 dark:border-slate-600 dark:text-white"
                    required
                  >
                    <option value="">-- Choose Product --</option>
                    <option v-for="p in products" :key="p.id" :value="p.id">
                      {{ p.name }} (${{ p.price || 0 }})
                    </option>
                  </select>
                </div>
              </td>

              <!-- 3. Treatment Teeth -->
              <td class="py-2 px-3">
                <div class="flex items-center flex-wrap gap-1">
                  <!-- Badges for selected teeth -->
                  <template v-if="row.teeth && row.teeth.length">
                    <span
                      v-for="t in row.teeth"
                      :key="t.teeth_number || t"
                      class="inline-flex items-center px-1.5 py-0.5 rounded text-[11px] font-mono font-bold bg-blue-100 text-blue-800 dark:bg-blue-900/40 dark:text-blue-300"
                    >
                      {{ t.teeth_number || t }}
                    </span>
                  </template>
                  <span v-else class="text-[11px] text-slate-400 italic">None</span>

                  <!-- Odontogram link button -->
                  <button
                    type="button"
                    @click.stop="activateOdontogramForRow(idx)"
                    :class="[
                      'ml-auto text-[11px] font-semibold px-2 py-0.5 rounded border transition',
                      activeRowIndex === idx
                        ? 'bg-primary text-white border-primary'
                        : 'bg-white hover:bg-slate-100 text-slate-700 border-slate-300'
                    ]"
                    title="Click to assign teeth on Odontogram chart below"
                  >
                    {{ activeRowIndex === idx ? 'Editing Teeth' : '+ Teeth' }}
                  </button>
                </div>
              </td>

              <!-- 4. Quantity -->
              <td class="py-2 px-3">
                <input
                  type="number"
                  min="1"
                  v-model.number="row.quantity"
                  class="w-full text-xs py-1 px-1.5 border border-slate-300 rounded text-center bg-white dark:bg-slate-800 dark:border-slate-600 dark:text-white"
                  required
                />
              </td>

              <!-- 5. Fee ($) -->
              <td class="py-2 px-3">
                <input
                  type="number"
                  step="any"
                  min="0"
                  v-model.number="row.unit_price"
                  class="w-full text-xs py-1 px-1.5 border border-slate-300 rounded text-right font-mono bg-white dark:bg-slate-800 dark:border-slate-600 dark:text-white"
                />
              </td>

              <!-- 6. Cost (orange tint) -->
              <td class="py-2 px-3 bg-amber-50/40 dark:bg-amber-900/10">
                <input
                  type="number"
                  step="any"
                  min="0"
                  v-model.number="row.cost"
                  class="w-full text-xs py-1 px-1.5 border border-amber-300 rounded text-right font-mono bg-white dark:bg-slate-800 dark:border-slate-600 dark:text-white"
                />
              </td>

              <!-- 7. Discount -->
              <td class="py-2 px-3">
                <div class="flex items-center space-x-1">
                  <input
                    type="number"
                    step="any"
                    min="0"
                    v-model.number="row.discount"
                    class="w-16 text-xs py-1 px-1 border border-slate-300 rounded text-right font-mono bg-white dark:bg-slate-800 dark:border-slate-600 dark:text-white"
                  />
                  <select
                    v-model="row.discount_as"
                    class="text-xs py-1 px-1 border border-slate-300 rounded bg-white dark:bg-slate-800 dark:border-slate-600 dark:text-white"
                  >
                    <option value="%">%</option>
                    <option value="$">$</option>
                  </select>
                </div>
              </td>

              <!-- 8. Amount ($) -->
              <td class="py-2 px-3 text-right font-mono font-bold text-slate-800 dark:text-slate-200">
                ${{ calculateRowAmount(row).toFixed(2) }}
              </td>

              <!-- 9. Action -->
              <td class="py-2 px-3 text-center">
                <button
                  type="button"
                  @click.stop="removeRow(idx)"
                  class="text-red-500 hover:text-red-700 p-1 rounded hover:bg-red-50 transition"
                  title="Remove row"
                >
                  <svg class="w-4 h-4 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                  </svg>
                </button>
              </td>
            </tr>

            <!-- Empty Row Hint -->
            <tr v-if="!form.items || !form.items.length">
              <td colspan="9" class="py-8 text-center text-slate-400">
                No items added yet. Click <span class="text-emerald-600 font-bold">+ Service</span> or <span class="text-blue-600 font-bold">+ Product</span> above to add rows.
              </td>
            </tr>
          </tbody>
        </table>
      </div>

      <!-- 7. Financial Summary Table (Right Aligned, Follows Uploaded Screenshot) -->
      <div class="flex justify-end mt-4 border-t border-slate-200 dark:border-slate-700 pt-4">
        <div class="w-full sm:w-80 md:w-96 text-xs">
          <div class="border border-slate-200 dark:border-slate-700 rounded-lg overflow-hidden shadow-2xs divide-y divide-slate-100 dark:divide-slate-700">
            <!-- 1. Total ($) -->
            <div class="flex items-center justify-between px-4 py-2 bg-white dark:bg-slate-800 font-medium">
              <span class="text-slate-600 dark:text-slate-300">Total($)</span>
              <span class="font-mono font-bold text-slate-800 dark:text-white">
                {{ totals.totalUsd.toFixed(2) }}
              </span>
            </div>

            <!-- 2. Paid to date ($) -->
            <div class="flex items-center justify-between px-4 py-2 bg-white dark:bg-slate-800 font-medium">
              <span class="text-slate-600 dark:text-slate-300">Paid to date($)</span>
              <span class="font-mono text-slate-800 dark:text-white">
                {{ totals.paidUsd.toFixed(2) }}
              </span>
            </div>

            <!-- 3. Paid to date (៛) -->
            <div class="flex items-center justify-between px-4 py-2 bg-white dark:bg-slate-800 font-medium">
              <span class="text-slate-600 dark:text-slate-300">Paid to date(៛)</span>
              <span class="font-mono text-slate-800 dark:text-white">
                {{ Math.round(totals.paidKh).toLocaleString() }}
              </span>
            </div>

            <!-- 4. Refunded ($) -->
            <div class="flex items-center justify-between px-4 py-2 bg-white dark:bg-slate-800 font-medium">
              <span class="text-slate-600 dark:text-slate-300">Refunded($)</span>
              <span class="font-mono text-slate-800 dark:text-white">
                {{ totals.refundedUsd.toFixed(2) }}
              </span>
            </div>

            <!-- 5. Balance ($) - Highlighted in Blue as in Screenshot -->
            <div class="flex items-center justify-between px-4 py-2 bg-[#8da5e0] text-white font-bold">
              <span>Balance($)</span>
              <span class="font-mono text-sm">
                {{ totals.balanceUsd.toFixed(2) }}
              </span>
            </div>

            <!-- 6. Balance (៛) -->
            <div class="flex items-center justify-between px-4 py-2 bg-white dark:bg-slate-800 font-medium">
              <span class="text-slate-600 dark:text-slate-300">Balance(៛)</span>
              <span class="font-mono text-slate-800 dark:text-white">
                {{ Math.round(totals.balanceKh).toLocaleString() }}
              </span>
            </div>
          </div>
        </div>
      </div>
    </div>

    <div ref="odontogramSection" class="odontogram-section bg-white dark:bg-darker rounded-xl shadow-xs border border-slate-200 dark:border-slate-700 p-5">
      <div class="flex flex-wrap items-center justify-between gap-3 pb-3 border-b border-slate-200 dark:border-slate-700">
        <div>
          <h3 class="text-sm font-bold text-slate-800 dark:text-slate-200 uppercase tracking-wider flex items-center">
            <svg class="w-4 h-4 mr-2 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.828 14.828a4 4 0 01-5.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
            3. {{ tm('Interactive_Odontogram_Chart') || 'Interactive Odontogram Chart' }}
          </h3>
          <p class="text-xs text-slate-500 mt-0.5">
            <span v-if="activeRow">
              Currently assigning teeth to row #{{ activeRowIndex + 1 }}:
              <strong class="text-primary">{{ getActiveRowItemName() }}</strong>
            </span>
            <span v-else class="text-slate-500">
              Click on any tooth surface (top, bottom, left, right, center) or tooth number (whole tooth) to record findings.
            </span>
          </p>
        </div>

        <!-- Dentition Filter Buttons & Clear -->
        <div class="flex items-center space-x-2">
          <div class="filters">
            <button
              type="button"
              :class="['filter', activeFilter === 'all' ? 'active' : '']"
              @click="activeFilter = 'all'"
            >
              All teeth (32 perm + 20 pri)
            </button>
            <button
              type="button"
              :class="['filter', activeFilter === 'permanent' ? 'active' : '']"
              @click="activeFilter = 'permanent'"
            >
              Permanent only (11–48)
            </button>
            <button
              type="button"
              :class="['filter', activeFilter === 'primary' ? 'active' : '']"
              @click="activeFilter = 'primary'"
            >
              Primary only (51–85)
            </button>
          </div>

          <button
            type="button"
            @click="clearAllFindings"
            class="btn-chart btn-chart-danger"
            title="Clear all tooth markings"
          >
            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
            </svg>
            <span>Clear</span>
          </button>
        </div>
      </div>

      <!-- Paper Odontogram Sheet (Mirrors Dental.vue .paper-shell exactly) -->
      <div class="paper-shell mt-4">
        <div class="paper">
          <div class="paper-heading">
            ODONTOGRAM / DENTAL CHART EXAMINATION SHEET
            <span v-if="selectedPatientName"> &middot; Patient: {{ selectedPatientName }}</span>
          </div>

          <div class="chart-scroll">
            <div class="chart-inner">

              <!-- 1. Upper Permanent Arch (18-11 | 21-28) -->
              <div class="arch-caption">MAXILLARY (UPPER) PERMANENT DENTITION</div>
              <div class="arch-row upper-perm">
                <!-- Upper Right Permanent: 18 down to 11 -->
                <div class="arch-side">
                  <div
                    v-for="num in permanent.upperRight"
                    :key="num"
                    :class="['tooth-slot', getToothClass(num, 'permanent')]"
                  >
                    <div class="tooth" :data-type="'permanent'">
                      <button
                        type="button"
                        class="tooth-number"
                        :style="getWholeToothStyle(num)"
                        @click="openFindingModal(num, 'whole')"
                        :title="`Tooth ${num} - click for whole tooth`"
                      >
                        {{ num }}
                      </button>
                      <svg class="tooth-face" viewBox="0 0 100 100">
                        <polygon
                          v-for="s in surfaces"
                          :key="s"
                          :points="polygons[s]"
                          :style="{ fill: getSurfaceFill(num, s) }"
                          @click="openFindingModal(num, s)"
                          :title="`Tooth ${num}, ${s}`"
                        />
                      </svg>
                    </div>
                  </div>
                </div>

                <!-- Midline -->
                <div class="midline" aria-hidden="true"></div>

                <!-- Upper Left Permanent: 21 up to 28 -->
                <div class="arch-side">
                  <div
                    v-for="num in permanent.upperLeft"
                    :key="num"
                    :class="['tooth-slot', getToothClass(num, 'permanent')]"
                  >
                    <div class="tooth" :data-type="'permanent'">
                      <button
                        type="button"
                        class="tooth-number"
                        :style="getWholeToothStyle(num)"
                        @click="openFindingModal(num, 'whole')"
                        :title="`Tooth ${num} - click for whole tooth`"
                      >
                        {{ num }}
                      </button>
                      <svg class="tooth-face" viewBox="0 0 100 100">
                        <polygon
                          v-for="s in surfaces"
                          :key="s"
                          :points="polygons[s]"
                          :style="{ fill: getSurfaceFill(num, s) }"
                          @click="openFindingModal(num, s)"
                          :title="`Tooth ${num}, ${s}`"
                        />
                      </svg>
                    </div>
                  </div>
                </div>
              </div>

              <!-- 2. Upper Primary / Deciduous Arch (55-51 | 61-65) -->
              <div class="arch-caption" style="margin-top: 10px;">DECIDUOUS ARCH</div>
              <div class="arch-row upper-primary">
                <!-- Upper Right Primary: 3 padding slots + 55..51 -->
                <div class="arch-side">
                  <div v-for="n in 3" :key="`ur-empty-${n}`" class="tooth-slot empty"></div>
                  <div
                    v-for="num in primary.upperRight"
                    :key="num"
                    :class="['tooth-slot primary-tooth', getToothClass(num, 'primary')]"
                  >
                    <div class="tooth" :data-type="'primary'">
                      <svg class="tooth-face" viewBox="0 0 100 100">
                        <polygon
                          v-for="s in surfaces"
                          :key="s"
                          :points="polygons[s]"
                          :style="{ fill: getSurfaceFill(num, s) }"
                          @click="openFindingModal(num, s)"
                          :title="`Tooth ${num}, ${s}`"
                        />
                      </svg>
                      <button
                        type="button"
                        class="tooth-number"
                        :style="getWholeToothStyle(num)"
                        @click="openFindingModal(num, 'whole')"
                        :title="`Tooth ${num} - click for whole tooth`"
                      >
                        {{ num }}
                      </button>
                    </div>
                  </div>
                </div>

                <!-- Midline -->
                <div class="midline" aria-hidden="true"></div>

                <!-- Upper Left Primary: 61..65 + 3 padding slots -->
                <div class="arch-side">
                  <div
                    v-for="num in primary.upperLeft"
                    :key="num"
                    :class="['tooth-slot primary-tooth', getToothClass(num, 'primary')]"
                  >
                    <div class="tooth" :data-type="'primary'">
                      <svg class="tooth-face" viewBox="0 0 100 100">
                        <polygon
                          v-for="s in surfaces"
                          :key="s"
                          :points="polygons[s]"
                          :style="{ fill: getSurfaceFill(num, s) }"
                          @click="openFindingModal(num, s)"
                          :title="`Tooth ${num}, ${s}`"
                        />
                      </svg>
                      <button
                        type="button"
                        class="tooth-number"
                        :style="getWholeToothStyle(num)"
                        @click="openFindingModal(num, 'whole')"
                        :title="`Tooth ${num} - click for whole tooth`"
                      >
                        {{ num }}
                      </button>
                    </div>
                  </div>
                  <div v-for="n in 3" :key="`ul-empty-${n}`" class="tooth-slot empty"></div>
                </div>
              </div>

              <!-- 3. Horizontal Midline Divider with 'R' and 'L' labels -->
              <div class="divider-line">
                <span class="rl-label">R</span>
                <div class="horizontal-rule"></div>
                <div class="divider-midline"></div>
                <div class="horizontal-rule"></div>
                <span class="rl-label">L</span>
              </div>

              <!-- 4. Lower Primary / Deciduous Arch (85-81 | 71-75) -->
              <div class="arch-caption">DECIDUOUS ARCH</div>
              <div class="arch-row lower-primary">
                <!-- Lower Right Primary: 3 padding slots + 85..81 -->
                <div class="arch-side">
                  <div v-for="n in 3" :key="`lr-empty-${n}`" class="tooth-slot empty"></div>
                  <div
                    v-for="num in primary.lowerRight"
                    :key="num"
                    :class="['tooth-slot primary-tooth', getToothClass(num, 'primary')]"
                  >
                    <div class="tooth" :data-type="'primary'">
                      <svg class="tooth-face" viewBox="0 0 100 100">
                        <polygon
                          v-for="s in surfaces"
                          :key="s"
                          :points="polygons[s]"
                          :style="{ fill: getSurfaceFill(num, s) }"
                          @click="openFindingModal(num, s)"
                          :title="`Tooth ${num}, ${s}`"
                        />
                      </svg>
                      <button
                        type="button"
                        class="tooth-number"
                        :style="getWholeToothStyle(num)"
                        @click="openFindingModal(num, 'whole')"
                        :title="`Tooth ${num} - click for whole tooth`"
                      >
                        {{ num }}
                      </button>
                    </div>
                  </div>
                </div>

                <!-- Midline -->
                <div class="midline" aria-hidden="true"></div>

                <!-- Lower Left Primary: 71..75 + 3 padding slots -->
                <div class="arch-side">
                  <div
                    v-for="num in primary.lowerLeft"
                    :key="num"
                    :class="['tooth-slot primary-tooth', getToothClass(num, 'primary')]"
                  >
                    <div class="tooth" :data-type="'primary'">
                      <svg class="tooth-face" viewBox="0 0 100 100">
                        <polygon
                          v-for="s in surfaces"
                          :key="s"
                          :points="polygons[s]"
                          :style="{ fill: getSurfaceFill(num, s) }"
                          @click="openFindingModal(num, s)"
                          :title="`Tooth ${num}, ${s}`"
                        />
                      </svg>
                      <button
                        type="button"
                        class="tooth-number"
                        :style="getWholeToothStyle(num)"
                        @click="openFindingModal(num, 'whole')"
                        :title="`Tooth ${num} - click for whole tooth`"
                      >
                        {{ num }}
                      </button>
                    </div>
                  </div>
                  <div v-for="n in 3" :key="`ll-empty-${n}`" class="tooth-slot empty"></div>
                </div>
              </div>

              <!-- 5. Lower Permanent Arch (48-41 | 31-38) -->
              <div class="arch-caption" style="margin-top: 10px;">MANDIBULAR (LOWER) PERMANENT DENTITION</div>
              <div class="arch-row lower-perm">
                <!-- Lower Right Permanent: 48 down to 41 -->
                <div class="arch-side">
                  <div
                    v-for="num in permanent.lowerRight"
                    :key="num"
                    :class="['tooth-slot', getToothClass(num, 'permanent')]"
                  >
                    <div class="tooth" :data-type="'permanent'">
                      <svg class="tooth-face" viewBox="0 0 100 100">
                        <polygon
                          v-for="s in surfaces"
                          :key="s"
                          :points="polygons[s]"
                          :style="{ fill: getSurfaceFill(num, s) }"
                          @click="openFindingModal(num, s)"
                          :title="`Tooth ${num}, ${s}`"
                        />
                      </svg>
                      <button
                        type="button"
                        class="tooth-number"
                        :style="getWholeToothStyle(num)"
                        @click="openFindingModal(num, 'whole')"
                        :title="`Tooth ${num} - click for whole tooth`"
                      >
                        {{ num }}
                      </button>
                    </div>
                  </div>
                </div>

                <!-- Midline -->
                <div class="midline" aria-hidden="true"></div>

                <!-- Lower Left Permanent: 31 up to 38 -->
                <div class="arch-side">
                  <div
                    v-for="num in permanent.lowerLeft"
                    :key="num"
                    :class="['tooth-slot', getToothClass(num, 'permanent')]"
                  >
                    <div class="tooth" :data-type="'permanent'">
                      <svg class="tooth-face" viewBox="0 0 100 100">
                        <polygon
                          v-for="s in surfaces"
                          :key="s"
                          :points="polygons[s]"
                          :style="{ fill: getSurfaceFill(num, s) }"
                          @click="openFindingModal(num, s)"
                          :title="`Tooth ${num}, ${s}`"
                        />
                      </svg>
                      <button
                        type="button"
                        class="tooth-number"
                        :style="getWholeToothStyle(num)"
                        @click="openFindingModal(num, 'whole')"
                        :title="`Tooth ${num} - click for whole tooth`"
                      >
                        {{ num }}
                      </button>
                    </div>
                  </div>
                </div>
              </div>

            </div>
          </div>

          <!-- Color Guide / Legend -->
          <div class="legend">
            <span class="legend-title">Color guide</span>
            <span
              v-for="c in conditionList"
              :key="c.id || c.value"
              class="legend-item"
            >
              <span class="swatch" :style="{ background: c.color }"></span>
              <span>{{ c.label }}</span>
            </span>
          </div>

          <!-- Other Information / Examination Notes -->
          <div class="other-info">
            <label for="otherInfoInput">OTHER INFORMATION / CLINICAL OBSERVATIONS</label>
            <textarea
              id="otherInfoInput"
              v-model="form.examination_notes"
              rows="2"
              placeholder="Record additional clinical findings, soft tissue observations, occlusal notes, or treatment plan details..."
            ></textarea>
          </div>

          <!-- Paper Footer -->
          <div class="paper-foot">
            <span>FDI Two-Digit World Dental Federation Numbering System</span>
            <span>OdontoCare Dental System &middot; {{ new Date().getFullYear() }}</span>
          </div>
        </div>
      </div>

      <!-- Quick Hint -->
      <div class="hint mt-3 text-xs text-slate-500 flex items-center gap-1.5">
        <span class="w-2 h-2 rounded-full bg-blue-600 inline-block"></span>
        <span>Click any trapezoid for surface marking (top, bottom, left, right, center), or click the tooth number for whole tooth condition.</span>
      </div>

      <!-- Below Grid: Findings Summary and List -->
      <div class="below-grid mt-4 grid grid-cols-1 lg:grid-cols-3 gap-4">
        <!-- Recorded Findings List (2 cols) -->
        <div class="info-card lg:col-span-2">
          <div class="flex items-center justify-between mb-2">
            <h4 class="font-bold text-slate-700 dark:text-slate-200 text-sm">
              Recorded Tooth Findings ({{ Object.keys(findings).length }})
            </h4>
            <span v-if="Object.keys(findings).length > 0" class="text-xs text-slate-400">
              Click Edit to modify or link to service
            </span>
          </div>

          <div v-if="Object.keys(findings).length === 0" class="empty-note py-6 text-center text-slate-400 text-xs">
            No tooth findings recorded yet. Click on any tooth or surface in the chart above to mark conditions.
          </div>

          <div v-else class="findings-list divide-y divide-slate-100 dark:divide-slate-700 max-h-64 overflow-y-auto">
            <div
              v-for="(f, key) in findings"
              :key="key"
              class="finding-item py-2 flex items-center justify-between text-xs"
            >
              <div class="flex items-center gap-2 flex-wrap">
                <span class="w-3.5 h-3.5 rounded-full flex-shrink-0" :style="{ background: f.color || '#d83c43' }"></span>
                <span class="font-bold text-slate-800 dark:text-slate-100">Tooth {{ f.tooth }}</span>
                <span class="text-slate-500 font-semibold text-[11px]">({{ f.surface }})</span>
                <span class="text-slate-600 dark:text-slate-300 font-medium">&middot; {{ getConditionLabel(f.condition_id, f.condition_code) }}</span>
                <span v-if="f.status" class="px-1.5 py-0.5 rounded text-[10px] font-bold uppercase bg-slate-100 dark:bg-slate-700 text-slate-600 dark:text-slate-300">
                  {{ f.status }}
                </span>
                <span v-if="f.assignedItemName" class="px-1.5 py-0.5 rounded text-[10px] font-bold bg-blue-50 text-blue-700 border border-blue-200">
                  Linked: {{ f.assignedItemName }}
                </span>
                <span v-if="f.note" class="text-slate-400 text-xs italic truncate max-w-[200px]">"{{ f.note }}"</span>
              </div>
              <div class="flex items-center space-x-2 flex-shrink-0 ml-2">
                <button
                  type="button"
                  @click="openFindingModal(f.tooth, f.surface)"
                  class="text-blue-600 hover:text-blue-800 font-bold text-xs"
                >
                  Edit
                </button>
                <button
                  type="button"
                  @click="removeFindingByKey(key)"
                  class="text-red-500 hover:text-red-700 text-xs"
                >
                  &times;
                </button>
              </div>
            </div>
          </div>
        </div>

        <!-- Quick Stats Card (1 col) -->
        <div class="info-card flex flex-col justify-between">
          <div>
            <h4 class="font-bold text-slate-700 dark:text-slate-200 text-sm">Odontogram Summary</h4>
            <div class="stat-big mt-2 text-3xl font-extrabold text-primary">{{ Object.keys(findings).length }}</div>
            <div class="stat-small text-xs text-slate-500">Active surface markings</div>
          </div>

          <div class="pt-4 border-t border-slate-100 dark:border-slate-700 mt-4">
            <div class="text-xs font-semibold text-slate-500">Affected Teeth:</div>
            <div class="text-base font-bold text-slate-800 dark:text-slate-100 mt-1">
              {{ uniqueTeethCount }} teeth
            </div>
            <div class="text-[11px] text-slate-400 mt-1">
              Linked to Invoice: #{{ form.invoice_number || suggestedInvoiceNumber || 'Draft' }}
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Finding Editor Modal Dialog (Matches Dental.vue modal exactly) -->
    <div
      v-if="modalOpen"
      class="modal-backdrop open"
      @click.self="closeFindingModal"
    >
      <div class="modal">
        <div class="modal-head">
          <div>
            <div class="modal-kicker">Dental finding editor</div>
            <h2 class="modal-title">
              Tooth {{ modalForm.tooth }} &middot; {{ modalForm.surface === 'whole' ? 'Whole tooth' : (modalForm.surface.charAt(0).toUpperCase() + modalForm.surface.slice(1)) }}
            </h2>
          </div>
          <button type="button" @click="closeFindingModal" class="close" aria-label="Close">&times;</button>
        </div>

        <div class="modal-body space-y-4">
          <div class="field-grid">
            <div class="field">
              <label>Tooth number</label>
              <input type="text" :value="modalForm.tooth" readonly class="bg-slate-100 cursor-not-allowed">
            </div>

            <div class="field">
              <label>Selected area</label>
              <select v-model="modalForm.surface" @change="onSurfaceChange">
                <option value="top">Top</option>
                <option value="bottom">Bottom / under</option>
                <option value="left">Left</option>
                <option value="right">Right</option>
                <option value="center">Center</option>
                <option value="whole">Whole tooth</option>
              </select>
            </div>
          </div>

          <div class="field">
            <label>Condition / Diagnosis</label>
            <select v-model="modalForm.condition_id" @change="onConditionChange">
              <option :value="null">No condition / note only</option>
              <option
                v-for="c in conditionList"
                :key="c.id || c.value"
                :value="c.id"
              >
                {{ c.label }}
              </option>
            </select>
          </div>

          <!-- Color preview and custom hex -->
          <div class="field">
            <label>Marking color</label>
            <div class="flex items-center gap-2">
              <input type="color" v-model="modalForm.color" class="w-10 h-9 p-0.5 border border-slate-300 rounded cursor-pointer">
              <input type="text" v-model="modalForm.color" class="flex-1 text-xs font-mono uppercase">
            </div>
          </div>

          <!-- Treatment Status -->
          <div class="field">
            <label>Treatment Status</label>
            <select v-model="modalForm.status">
              <option value="observed">Observed / existing</option>
              <option value="planned">Planned treatment</option>
              <option value="in_progress">In progress</option>
              <option value="completed">Completed</option>
              <option value="referred">Referred</option>
            </select>
          </div>

          <!-- Link to Invoice Item (Service or Product row) -->
          <div class="field">
            <label>Assign to Invoice Item</label>
            <select v-model="modalForm.assigned_item_idx">
              <option :value="null">-- General finding (Not linked to specific row) --</option>
              <option
                v-for="(item, idx) in form.items"
                :key="item._clientId"
                :value="idx"
              >
                Row #{{ idx + 1 }}: {{ getItemDisplayName(item) }}
              </option>
            </select>
          </div>

          <!-- Clinical Note -->
          <div class="field">
            <label>Clinical Note</label>
            <textarea
              v-model="modalForm.note"
              rows="3"
              placeholder="Type observations, procedure notes, or details for this surface..."
            ></textarea>
          </div>

          <!-- Modal Actions -->
          <div class="modal-actions">
            <button
              type="button"
              @click="removeModalFinding"
              class="btn-chart btn-chart-danger"
            >
              Remove marking
            </button>

            <div class="modal-actions-right">
              <button type="button" @click="closeFindingModal" class="btn-chart">
                Cancel
              </button>
              <button type="button" @click="saveModalFinding" class="btn-chart btn-chart-primary">
                Save finding
              </button>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Bottom Actions -->
    <div class="flex items-center justify-end space-x-3 pt-4">
      <BackButton :url="route('invoices.index')">
        {{ tm('Cancel') || 'Cancel' }}
      </BackButton>
      <SaveButton :loading="form.processing" @submit="submit">
        {{ form.processing ? (tm('Saving') || 'Saving...') : (isEdit ? (tm('Update_Invoice') || 'Update Invoice') : (tm('Save_Invoice') || 'Save Invoice')) }}
      </SaveButton>
    </div>
  </form>
</template>

<script>
import { useForm } from "@inertiajs/inertia-vue3";
import SaveButton from "@/Components/Button/Save.vue";
import BackButton from "@/Components/Button/Back.vue";
import DateInput from "@/Components/Forms/DateInput.vue";

export default {
  components: {
    SaveButton,
    BackButton,
    DateInput,
  },
  props: {
    invoice: {
      type: Object,
      default: null,
    },
    suggestedInvoiceNumber: {
      type: String,
      default: "",
    },
    defaultPrefix: {
      type: Object,
      default: null,
    },
    exchangeRate: {
      type: Number,
      default: 4050,
    },
    patients: {
      type: Array,
      default: () => [],
    },
    doctors: {
      type: Array,
      default: () => [],
    },
    assistants: {
      type: Array,
      default: () => [],
    },
    services: {
      type: Array,
      default: () => [],
    },
    products: {
      type: Array,
      default: () => [],
    },
    conditions: {
      type: Array,
      default: () => [],
    },
  },
  data() {
    const isEdit = Boolean(this.invoice && this.invoice.id);

    // Initial items from invoice if edit mode
    let initialItems = [];
    if (isEdit && this.invoice.medical_details && this.invoice.medical_details.length) {
      initialItems = this.invoice.medical_details.map((d) => {
        const itemType = d.item_type || (d.detailable_type && d.detailable_type.includes("Product") ? "product" : "service");
        const teeth = (d.treatment_teeth || []).map((t) => ({
          teeth_number: String(t.teeth_number),
          surfaces: {
            top: Boolean(t.class_one),
            bottom: Boolean(t.class_two),
            left: Boolean(t.class_three),
            right: Boolean(t.class_four),
            center: Boolean(t.class_five),
            whole: Boolean(t.class_six),
          },
          findings: (t.tooth_findings || t.toothFindings || []).map((tf) => ({
            surface: tf.surface,
            dental_condition_id: tf.dental_condition_id,
            condition_id: tf.dental_condition_id,
            custom_color: tf.custom_color,
            color: tf.custom_color,
            treatment_status: tf.treatment_status || "observed",
            status: tf.treatment_status || "observed",
            note: tf.note || "",
          })),
        }));

        return {
          _clientId: "item_" + d.id,
          id: d.id,
          item_type: itemType,
          detailable_id: d.detailable_id || d.service_id,
          quantity: d.quantity || 1,
          unit_price: d.unit_price || 0,
          cost: d.cost || 0,
          discount: d.discount || 0,
          discount_as: d.discount_as || "%",
          date: d.date ? d.date.split("T")[0] : new Date().toISOString().split("T")[0],
          teeth: teeth,
        };
      });
    }

    return {
      isEdit,
      activeRowIndex: initialItems.length ? 0 : null,
      activeFilter: "all",
      findings: {},
      permanent: {
        upperRight: [18, 17, 16, 15, 14, 13, 12, 11],
        upperLeft: [21, 22, 23, 24, 25, 26, 27, 28],
        lowerRight: [48, 47, 46, 45, 44, 43, 42, 41],
        lowerLeft: [31, 32, 33, 34, 35, 36, 37, 38],
      },
      primary: {
        upperRight: [55, 54, 53, 52, 51],
        upperLeft: [61, 62, 63, 64, 65],
        lowerRight: [85, 84, 83, 82, 81],
        lowerLeft: [71, 72, 73, 74, 75],
      },
      surfaces: ["top", "left", "center", "right", "bottom"],
      polygons: {
        top: "9,7 91,7 70,29 30,29",
        left: "7,9 29,30 29,70 7,91",
        center: "34,34 66,34 66,66 34,66",
        right: "93,9 71,30 71,70 93,91",
        bottom: "9,93 30,71 70,71 91,93",
      },
      modalOpen: false,
      modalForm: {
        tooth: "",
        surface: "top",
        condition_id: null,
        condition_code: "",
        color: "#d83c43",
        status: "observed",
        assigned_item_idx: null,
        note: "",
      },
      form: useForm({
        patient_id: this.invoice ? this.invoice.patient_id : "",
        doctor_id: this.invoice ? this.invoice.doctor_id : "",
        assistant_id: this.invoice ? this.invoice.user_id : "",
        invoice_number: this.invoice ? (this.invoice.invoice_number_formatted || this.invoice.invoice_number) : this.suggestedInvoiceNumber,
        date: this.invoice && this.invoice.date ? this.invoice.date.split("T")[0] : new Date().toISOString().split("T")[0],
        rate: this.invoice && this.invoice.rate ? this.invoice.rate : this.exchangeRate,
        discount_invoice: this.invoice ? this.invoice.discount_invoice : 0,
        discount_inv_as: this.invoice && this.invoice.discount_inv_as ? this.invoice.discount_inv_as : "%",
        visit_type: this.invoice ? this.invoice.visit_type : "initial",
        diagnosis: this.invoice ? this.invoice.diagnosis : "",
        estimate_id: this.invoice ? this.invoice.estimate_id : "",
        examination_notes: this.invoice ? (this.invoice.dental_chart?.examination_notes || this.invoice.description || "") : "",
        paid_usd: this.invoice ? (this.invoice.paid_usd || 0) : 0,
        paid_kh: this.invoice ? (this.invoice.paid_kh || 0) : 0,
        refunded: this.invoice ? (this.invoice.refunded || 0) : 0,
        items: initialItems,
        odontogram_findings: [],
      }),
    };
  },
  computed: {
    hasErrors() {
      return Boolean(this.form.errors && Object.keys(this.form.errors).length > 0);
    },
    activeRow() {
      if (this.activeRowIndex !== null && this.form.items[this.activeRowIndex]) {
        return this.form.items[this.activeRowIndex];
      }
      return null;
    },
    selectedPatientName() {
      if (!this.form.patient_id) return "";
      const p = this.patients.find((item) => item.id === this.form.patient_id);
      return p ? p.full_name : "";
    },
    conditionList() {
      if (this.conditions && this.conditions.length > 0) {
        return this.conditions.map((c) => ({
          id: c.id,
          value: c.code,
          label: c.name,
          color: c.default_color || "#d83c43",
        }));
      }
      return [
        { id: 1, value: "caries", label: "Caries / decay", color: "#d83c43" },
        { id: 2, value: "restoration", label: "Existing restoration", color: "#2f66cf" },
        { id: 3, value: "planned", label: "Treatment planned", color: "#e59b26" },
        { id: 4, value: "completed", label: "Completed treatment", color: "#319b62" },
        { id: 5, value: "missing", label: "Missing tooth", color: "#707782" },
        { id: 6, value: "crown", label: "Crown", color: "#8b5bc7" },
        { id: 7, value: "fracture", label: "Fracture", color: "#8a5a38" },
        { id: 8, value: "sealant", label: "Sealant", color: "#169d9a" },
      ];
    },
    uniqueTeethCount() {
      const teethSet = new Set();
      Object.values(this.findings).forEach((f) => {
        if (f.tooth) teethSet.add(String(f.tooth));
      });
      return teethSet.size;
    },
    totals() {
      let subtotal = 0;
      (this.form.items || []).forEach((row) => {
        subtotal += this.calculateRowAmount(row);
      });

      const overallDiscVal = parseFloat(this.form.discount_invoice) || 0;
      let overallDiscountAmount = 0;
      if (this.form.discount_inv_as === "%") {
        overallDiscountAmount = subtotal * (overallDiscVal / 100);
      } else {
        overallDiscountAmount = overallDiscVal;
      }
      overallDiscountAmount = Math.min(subtotal, Math.max(0, overallDiscountAmount));
      const totalUsd = Math.max(0, subtotal - overallDiscountAmount);

      const paidUsd = parseFloat(this.form.paid_usd) || 0;
      const rate = parseFloat(this.form.rate) || 4050;
      const paidKh = parseFloat(this.form.paid_kh) || Math.round(paidUsd * rate);
      const refundedUsd = parseFloat(this.form.refunded) || 0;
      const balanceUsd = Math.max(0, totalUsd - paidUsd + refundedUsd);
      const balanceKh = Math.round(balanceUsd * rate);

      return {
        subtotal,
        totalUsd,
        paidUsd,
        paidKh,
        refundedUsd,
        balanceUsd,
        balanceKh,
      };
    },
  },
  mounted() {
    this.restoreExistingFindings();
  },
  methods: {
    restoreExistingFindings() {
      // Restore from treatment teeth attached to medical details
      if (this.invoice && this.invoice.medical_details) {
        this.invoice.medical_details.forEach((d, itemIdx) => {
          const itemName = d.detailable ? d.detailable.name : (d.service_id ? `Service #${d.service_id}` : `Item #${itemIdx + 1}`);
          if (d.treatment_teeth && Array.isArray(d.treatment_teeth)) {
            d.treatment_teeth.forEach((t) => {
              const toothNum = String(t.teeth_number);
              // If findings exist on treatment tooth
              const findingsList = t.tooth_findings || t.toothFindings || [];
              if (findingsList.length) {
                findingsList.forEach((tf) => {
                  const cond = tf.condition;
                  const k = this.keyFor(toothNum, tf.surface);
                  this.findings[k] = {
                    tooth: toothNum,
                    surface: tf.surface,
                    condition_id: tf.dental_condition_id,
                    condition_code: cond ? cond.code : "",
                    color: tf.custom_color || (cond ? cond.default_color : "#d83c43"),
                    status: tf.treatment_status || "observed",
                    note: tf.note || "",
                    assigned_item_idx: itemIdx,
                    assignedItemName: itemName,
                  };
                });
              } else {
                // Synthesize from boolean surface columns if no child findings records
                const surfaceMap = {
                  top: t.class_one,
                  bottom: t.class_two,
                  left: t.class_three,
                  right: t.class_four,
                  center: t.class_five,
                  whole: t.class_six,
                };
                Object.entries(surfaceMap).forEach(([surf, isMarked]) => {
                  if (isMarked) {
                    const k = this.keyFor(toothNum, surf);
                    if (!this.findings[k]) {
                      this.findings[k] = {
                        tooth: toothNum,
                        surface: surf,
                        condition_id: null,
                        condition_code: "planned",
                        color: "#2457c5",
                        status: "observed",
                        note: "",
                        assigned_item_idx: itemIdx,
                        assignedItemName: itemName,
                      };
                    }
                  }
                });
              }
            });
          }
        });
      }
    },
    keyFor(tooth, surface) {
      return `${tooth}-${surface}`;
    },
    addServiceRow() {
      const today = this.form.date || new Date().toISOString().split("T")[0];
      const newRow = {
        _clientId: "row_" + Date.now() + "_" + Math.floor(Math.random() * 1000),
        item_type: "service",
        detailable_id: "",
        quantity: 1,
        unit_price: 0,
        cost: 0,
        discount: 0,
        discount_as: "%",
        date: today,
        teeth: [],
      };
      this.form.items.push(newRow);
      this.activeRowIndex = this.form.items.length - 1;
    },
    addProductRow() {
      const today = this.form.date || new Date().toISOString().split("T")[0];
      const newRow = {
        _clientId: "row_" + Date.now() + "_" + Math.floor(Math.random() * 1000),
        item_type: "product",
        detailable_id: "",
        quantity: 1,
        unit_price: 0,
        cost: 0,
        discount: 0,
        discount_as: "%",
        date: today,
        teeth: [],
      };
      this.form.items.push(newRow);
      this.activeRowIndex = this.form.items.length - 1;
    },
    removeRow(idx) {
      this.form.items.splice(idx, 1);
      // Clean up any findings assigned to this row or update indices
      Object.values(this.findings).forEach((f) => {
        if (f.assigned_item_idx === idx) {
          f.assigned_item_idx = null;
          f.assignedItemName = "";
        } else if (f.assigned_item_idx > idx) {
          f.assigned_item_idx--;
        }
      });

      if (this.activeRowIndex === idx) {
        this.activeRowIndex = this.form.items.length ? 0 : null;
      } else if (this.activeRowIndex > idx) {
        this.activeRowIndex--;
      }
    },
    onServiceSelected(row) {
      const s = this.services.find((item) => item.id === row.detailable_id);
      if (s) {
        row.unit_price = parseFloat(s.price) || 0;
        row.cost = parseFloat(s.cost) || 0;
      }
    },
    onProductSelected(row) {
      const p = this.products.find((item) => item.id === row.detailable_id);
      if (p) {
        row.unit_price = parseFloat(p.price) || 0;
        row.cost = parseFloat(p.cost) || 0;
      }
    },
    calculateRowAmount(row) {
      const qty = Math.max(1, parseInt(row.quantity) || 1);
      const price = Math.max(0, parseFloat(row.unit_price) || 0);
      const disc = Math.max(0, parseFloat(row.discount) || 0);
      const gross = qty * price;

      let rowDiscount = 0;
      if (row.discount_as === "%") {
        rowDiscount = gross * (disc / 100);
      } else {
        rowDiscount = disc;
      }
      rowDiscount = Math.min(gross, Math.max(0, rowDiscount));
      return Math.max(0, gross - rowDiscount);
    },
    activateOdontogramForRow(idx) {
      this.activeRowIndex = idx;
      if (this.$refs.odontogramSection) {
        this.$refs.odontogramSection.scrollIntoView({ behavior: "smooth" });
      }
    },
    getActiveRowItemName() {
      if (!this.activeRow) return "";
      return this.getItemDisplayName(this.activeRow);
    },
    getItemDisplayName(item) {
      if (!item) return "";
      if (item.item_type === "service") {
        const s = this.services.find((sItem) => sItem.id === item.detailable_id);
        return s ? s.name : "Service (Unselected)";
      } else {
        const p = this.products.find((pItem) => pItem.id === item.detailable_id);
        return p ? p.name : "Product (Unselected)";
      }
    },
    getToothClass(num, type) {
      if (this.activeFilter === "all") return "";
      return this.activeFilter === type ? "" : "opacity-20 pointer-events-none";
    },
    getSurfaceFill(tooth, surface) {
      const k = this.keyFor(tooth, surface);
      if (this.findings[k] && this.findings[k].color) {
        return this.findings[k].color;
      }
      const wholeK = this.keyFor(tooth, "whole");
      if (this.findings[wholeK] && this.findings[wholeK].color) {
        return this.findings[wholeK].color;
      }
      return "#ffffff";
    },
    getWholeToothStyle(tooth) {
      const wholeK = this.keyFor(tooth, "whole");
      if (this.findings[wholeK] && this.findings[wholeK].color) {
        return {
          background: this.findings[wholeK].color,
          color: "#ffffff",
          fontWeight: "700",
          borderRadius: "4px",
        };
      }
      // Check if tooth is in active item row
      if (this.activeRow && this.activeRow.teeth) {
        const hasTooth = this.activeRow.teeth.some((t) => String(t.teeth_number || t) === String(tooth));
        if (hasTooth) {
          return {
            background: "#eff6ff",
            color: "#1d4ed8",
            fontWeight: "700",
            border: "1px solid #93c5fd",
          };
        }
      }
      return {};
    },
    getConditionLabel(condId, condCode) {
      if (condId) {
        const found = this.conditionList.find((c) => c.id === condId);
        if (found) return found.label;
      }
      if (condCode) {
        const found = this.conditionList.find((c) => c.value === condCode);
        if (found) return found.label;
      }
      return "Marking";
    },
    openFindingModal(tooth, surface = "top") {
      const toothStr = String(tooth);
      const k = this.keyFor(toothStr, surface);
      const existing = this.findings[k] || this.findings[this.keyFor(toothStr, "whole")];

      this.modalForm.tooth = toothStr;
      this.modalForm.surface = surface;

      if (existing) {
        this.modalForm.condition_id = existing.condition_id;
        this.modalForm.condition_code = existing.condition_code || "";
        this.modalForm.color = existing.color || "#d83c43";
        this.modalForm.status = existing.status || "observed";
        this.modalForm.note = existing.note || "";
        this.modalForm.assigned_item_idx = existing.assigned_item_idx !== undefined ? existing.assigned_item_idx : this.activeRowIndex;
      } else {
        // Default to first condition or active row
        const firstCond = this.conditionList[0];
        this.modalForm.condition_id = firstCond ? firstCond.id : null;
        this.modalForm.condition_code = firstCond ? firstCond.value : "caries";
        this.modalForm.color = firstCond ? firstCond.color : "#d83c43";
        this.modalForm.status = "observed";
        this.modalForm.note = "";
        this.modalForm.assigned_item_idx = this.activeRowIndex !== null ? this.activeRowIndex : (this.form.items.length ? 0 : null);
      }

      this.modalOpen = true;
    },
    closeFindingModal() {
      this.modalOpen = false;
    },
    onSurfaceChange() {
      const k = this.keyFor(this.modalForm.tooth, this.modalForm.surface);
      if (this.findings[k]) {
        const existing = this.findings[k];
        this.modalForm.condition_id = existing.condition_id;
        this.modalForm.condition_code = existing.condition_code;
        this.modalForm.color = existing.color;
        this.modalForm.status = existing.status;
        this.modalForm.note = existing.note;
        this.modalForm.assigned_item_idx = existing.assigned_item_idx;
      }
    },
    onConditionChange() {
      if (this.modalForm.condition_id) {
        const cond = this.conditionList.find((c) => c.id === this.modalForm.condition_id);
        if (cond) {
          this.modalForm.condition_code = cond.value;
          this.modalForm.color = cond.color;
        }
      }
    },
    saveModalFinding() {
      const tooth = String(this.modalForm.tooth);
      const surface = this.modalForm.surface;
      const k = this.keyFor(tooth, surface);

      let assignedName = "";
      if (this.modalForm.assigned_item_idx !== null && this.form.items[this.modalForm.assigned_item_idx]) {
        assignedName = this.getItemDisplayName(this.form.items[this.modalForm.assigned_item_idx]);
      }

      this.findings[k] = {
        tooth: tooth,
        surface: surface,
        condition_id: this.modalForm.condition_id,
        condition_code: this.modalForm.condition_code,
        color: this.modalForm.color || "#d83c43",
        status: this.modalForm.status || "observed",
        note: this.modalForm.note || "",
        assigned_item_idx: this.modalForm.assigned_item_idx,
        assignedItemName: assignedName,
      };

      // Synchronize with assigned row teeth badges
      this.syncAssignedRowTeeth(tooth, surface, this.modalForm.assigned_item_idx);

      this.closeFindingModal();
    },
    removeModalFinding() {
      const tooth = String(this.modalForm.tooth);
      const surface = this.modalForm.surface;
      const k = this.keyFor(tooth, surface);

      delete this.findings[k];

      // Remove from item row teeth array if no more surfaces for that tooth
      this.syncAssignedRowTeeth(tooth, surface, null);

      this.closeFindingModal();
    },
    removeFindingByKey(key) {
      const f = this.findings[key];
      if (f) {
        delete this.findings[key];
        this.syncAssignedRowTeeth(f.tooth, f.surface, null);
      }
    },
    clearAllFindings() {
      if (!confirm("Are you sure you want to clear all tooth markings?")) return;
      this.findings = {};
      this.form.items.forEach((item) => {
        item.teeth = [];
      });
    },
    syncAssignedRowTeeth(tooth, surface, targetIdx) {
      const toothStr = String(tooth);

      this.form.items.forEach((item, idx) => {
        if (!item.teeth) item.teeth = [];

        // Check if any finding is linked to this item
        const itemToothSurfaces = Object.values(this.findings).filter(
          (f) => String(f.tooth) === toothStr && f.assigned_item_idx === idx
        );

        const existingToothIdx = item.teeth.findIndex((t) => String(t.teeth_number || t) === toothStr);

        if (itemToothSurfaces.length > 0) {
          const surfaces = {
            top: itemToothSurfaces.some((f) => f.surface === "top"),
            bottom: itemToothSurfaces.some((f) => f.surface === "bottom"),
            left: itemToothSurfaces.some((f) => f.surface === "left"),
            right: itemToothSurfaces.some((f) => f.surface === "right"),
            center: itemToothSurfaces.some((f) => f.surface === "center"),
            whole: itemToothSurfaces.some((f) => f.surface === "whole"),
          };
          const findings = itemToothSurfaces.map((f) => ({
            surface: f.surface,
            dental_condition_id: f.condition_id || null,
            condition_id: f.condition_id || null,
            custom_color: f.color || "#d83c43",
            color: f.color || "#d83c43",
            treatment_status: f.status || "observed",
            status: f.status || "observed",
            note: f.note || null,
          }));
          const toothPayload = {
            teeth_number: toothStr,
            surfaces: surfaces,
            findings: findings,
          };

          if (existingToothIdx >= 0) {
            item.teeth[existingToothIdx] = toothPayload;
          } else {
            item.teeth.push(toothPayload);
          }
        } else {
          // If no findings for this tooth on this item and we explicitly assigned to another or removed
          if (existingToothIdx >= 0 && (targetIdx === null || targetIdx !== idx)) {
            item.teeth.splice(existingToothIdx, 1);
          }
        }
      });
    },
    submit() {
      if (!this.form.patient_id) {
        alert("Please select a Patient.");
        return;
      }
      if (!this.form.items || !this.form.items.length) {
        alert("Please add at least one Service or Product.");
        return;
      }

      // Ensure every item has detailable_id selected
      const hasEmptyItem = this.form.items.some((i) => !i.detailable_id);
      if (hasEmptyItem) {
        alert("Please select a valid Service or Product for every row.");
        return;
      }

      // Compile odontogram findings payload
      const odontogramPayload = Object.values(this.findings).map((f) => ({
        tooth: String(f.tooth),
        surface: f.surface,
        condition_id: f.condition_id || null,
        dental_condition_id: f.condition_id || null,
        condition_code: f.condition_code || null,
        color: f.color || "#d83c43",
        custom_color: f.color || "#d83c43",
        status: f.status || "observed",
        treatment_status: f.status || "observed",
        note: f.note || null,
        service_id: f.assigned_item_idx !== null && this.form.items[f.assigned_item_idx] ? this.form.items[f.assigned_item_idx].detailable_id : null,
      }));

      // Synchronize findings into each item's teeth array before submission
      this.form.items.forEach((item, idx) => {
        if (!item.teeth) item.teeth = [];
        item.teeth.forEach((t) => {
          const toothStr = String(t.teeth_number || t);
          const itemToothSurfaces = Object.values(this.findings).filter(
            (f) => String(f.tooth) === toothStr && (f.assigned_item_idx === idx || (f.assigned_item_idx === null && idx === 0))
          );
          if (itemToothSurfaces.length > 0) {
            t.surfaces = {
              top: itemToothSurfaces.some((f) => f.surface === "top"),
              bottom: itemToothSurfaces.some((f) => f.surface === "bottom"),
              left: itemToothSurfaces.some((f) => f.surface === "left"),
              right: itemToothSurfaces.some((f) => f.surface === "right"),
              center: itemToothSurfaces.some((f) => f.surface === "center"),
              whole: itemToothSurfaces.some((f) => f.surface === "whole"),
            };
            t.findings = itemToothSurfaces.map((f) => ({
              surface: f.surface,
              dental_condition_id: f.condition_id || null,
              condition_id: f.condition_id || null,
              custom_color: f.color || "#d83c43",
              color: f.color || "#d83c43",
              treatment_status: f.status || "observed",
              status: f.status || "observed",
              note: f.note || null,
            }));
          }
        });
      });

      // Also ensure any marked teeth in this.findings are attached to the corresponding item.teeth
      Object.values(this.findings).forEach((f) => {
        const itemIdx = (f.assigned_item_idx !== null && f.assigned_item_idx !== undefined && this.form.items[f.assigned_item_idx])
          ? f.assigned_item_idx
          : (this.form.items.length ? 0 : null);

        if (itemIdx !== null && this.form.items[itemIdx]) {
          const item = this.form.items[itemIdx];
          if (!item.teeth) item.teeth = [];
          const toothStr = String(f.tooth);
          let toothItem = item.teeth.find((t) => String(t.teeth_number || t) === toothStr);
          if (!toothItem) {
            toothItem = {
              teeth_number: toothStr,
              surfaces: { top: false, bottom: false, left: false, right: false, center: false, whole: false },
              findings: [],
            };
            item.teeth.push(toothItem);
          }
          if (typeof toothItem === 'object') {
            if (!toothItem.surfaces) toothItem.surfaces = {};
            if (f.surface) toothItem.surfaces[f.surface] = true;
            if (!toothItem.findings) toothItem.findings = [];
            const exists = toothItem.findings.some((tf) => tf.surface === f.surface);
            if (!exists) {
              toothItem.findings.push({
                surface: f.surface,
                dental_condition_id: f.condition_id || null,
                condition_id: f.condition_id || null,
                custom_color: f.color || "#d83c43",
                color: f.color || "#d83c43",
                treatment_status: f.status || "observed",
                status: f.status || "observed",
                note: f.note || null,
              });
            }
          }
        }
      });

      this.form.odontogram_findings = odontogramPayload;

      if (this.isEdit) {
        this.form.put(route("invoices.update", this.invoice.id), {
          onSuccess: () => {
            this.$toast?.success("Invoice updated successfully.");
          },
        });
      } else {
        this.form.post(route("invoices.store"), {
          onSuccess: () => {
            this.$toast?.success("Invoice created successfully.");
          },
        });
      }
    },
  },
};
</script>

<style scoped>
/* Paper Odontogram Sheet Styling (Matches Dental.vue exactly) */
.paper-shell {
  background: #fff;
  border: 1px solid #e2e8f0;
  border-radius: 16px;
  padding: 14px;
  box-shadow: 0 4px 20px rgba(0, 0, 0, 0.04);
}

.paper {
  background: #fffefa;
  border: 1px solid #d2d0c8;
  padding: 24px 26px 22px;
  box-shadow: inset 0 0 0 1px #ffffffb0;
}

.paper-heading {
  font-family: Arial, Helvetica, sans-serif;
  font-size: 18px;
  font-weight: 800;
  font-style: italic;
  letter-spacing: -0.02em;
  color: #2a2a2a;
  margin: 0 0 16px;
}

.paper-heading span {
  font-weight: 500;
  font-size: 15px;
  letter-spacing: 0;
}

.chart-scroll {
  overflow-x: auto;
  padding: 0 0 6px;
}

.chart-inner {
  min-width: 1090px;
  padding: 8px 0 0;
}

.arch-row {
  display: flex;
  align-items: stretch;
  justify-content: center;
  gap: 12px;
  min-width: max-content;
}

.arch-side {
  display: grid;
  grid-template-columns: repeat(8, 54px);
  gap: 9px;
  align-items: stretch;
}

.midline {
  width: 5px;
  min-width: 5px;
  background: #171717;
  align-self: stretch;
  margin: 0;
}

.tooth-slot {
  width: 54px;
  min-height: 80px;
  display: flex;
  align-items: center;
  justify-content: flex-start;
  transition: opacity 0.15s;
}

.tooth-slot.empty {
  pointer-events: none;
}

.tooth {
  width: 54px;
  display: flex;
  flex-direction: column;
  align-items: center;
  position: relative;
}

.tooth-number {
  border: 0;
  background: transparent;
  color: #262626;
  border-radius: 3px;
  font-size: 14px;
  line-height: 20px;
  height: 22px;
  width: 100%;
  padding: 0;
  font-weight: 600;
  text-align: center;
  cursor: pointer;
  transition: all 0.15s;
}

.tooth-number:hover,
.tooth-number:focus-visible {
  background: #e9eefc;
  outline: 1px solid #a8b9ed;
}

.tooth-face {
  width: 43px;
  height: 51px;
  display: block;
  overflow: visible;
}

.tooth-face polygon {
  stroke: #252525;
  stroke-width: 2.6;
  stroke-linejoin: miter;
  cursor: pointer;
  transition: fill 0.12s, filter 0.12s;
}

.tooth-face polygon:hover,
.tooth-face polygon:focus {
  filter: brightness(0.89);
  stroke: #2457c5;
  stroke-width: 3.5;
  outline: none;
}

.primary-tooth .tooth-face {
  width: 35px;
  height: 31px;
}

.primary-tooth .tooth-number {
  font-size: 12px;
  height: 20px;
  line-height: 18px;
}

.upper-perm .tooth-number { order: 0; }
.upper-perm .tooth-face { order: 1; }
.lower-perm .tooth-face { order: 0; }
.lower-perm .tooth-number { order: 1; }
.primary-tooth .tooth-face { order: 0; }
.primary-tooth .tooth-number { order: 1; }
.upper-primary { margin-top: 7px; }
.lower-primary { margin-top: 8px; }
.upper-primary .tooth-slot,
.lower-primary .tooth-slot { min-height: 54px; }

.divider-line {
  display: flex;
  align-items: center;
  gap: 16px;
  margin: 12px 0 9px;
  min-width: 1090px;
  height: 62px;
  color: #151515;
}

.rl-label {
  font-family: Arial, Helvetica, sans-serif;
  font-size: 28px;
  font-weight: 800;
  line-height: 1;
}

.horizontal-rule {
  height: 5px;
  background: #222;
  flex: 1;
}

.divider-midline {
  height: 62px;
  width: 5px;
  min-width: 5px;
  background: #171717;
  margin: 0 4px;
}

.arch-caption {
  font-size: 11px;
  letter-spacing: 0.17em;
  text-transform: uppercase;
  font-weight: 800;
  color: #8c8981;
  text-align: center;
  margin: 6px 0 6px;
}

.legend {
  margin-top: 20px;
  border-top: 1px solid #dcdad3;
  padding-top: 12px;
  display: flex;
  gap: 14px;
  flex-wrap: wrap;
  align-items: center;
}

.legend-title {
  font-size: 11px;
  text-transform: uppercase;
  letter-spacing: 0.14em;
  color: #64748b;
  font-weight: 800;
}

.legend-item {
  display: inline-flex;
  align-items: center;
  gap: 6px;
  font-size: 12px;
  color: #334155;
  font-weight: 600;
}

.swatch {
  width: 12px;
  height: 12px;
  border-radius: 3px;
  border: 1px solid rgba(0, 0, 0, 0.2);
  display: inline-block;
}

.other-info {
  margin-top: 18px;
  border-top: 1px solid #dcdad3;
  padding-top: 12px;
  display: flex;
  flex-direction: column;
  gap: 6px;
}

.other-info label {
  font-family: Arial, Helvetica, sans-serif;
  font-size: 16px;
  font-weight: 800;
  font-style: italic;
  letter-spacing: -0.02em;
  color: #2a2a2a;
}

.other-info textarea {
  width: 100%;
  border: 1px dashed #c0beba;
  border-radius: 4px;
  resize: vertical;
  min-height: 48px;
  padding: 8px 10px;
  background: transparent;
  outline: none;
  color: #202020;
  font-size: 13px;
}

.other-info textarea:focus {
  border-color: #2457c5;
}

.paper-foot {
  display: flex;
  justify-content: space-between;
  gap: 14px;
  margin-top: 14px;
  color: #9b9b96;
  font-size: 10px;
  border-top: 1px solid #eae8e1;
  padding-top: 6px;
}

/* Dentition filter buttons */
.filters {
  display: flex;
  gap: 6px;
  flex-wrap: wrap;
}

.filter {
  padding: 5px 10px;
  border: 1px solid #cbd5e1;
  background: #fff;
  color: #475569;
  border-radius: 999px;
  font-size: 11px;
  font-weight: 700;
  cursor: pointer;
  transition: all 0.15s;
}

.filter.active {
  background: #eff6ff;
  color: #1d4ed8;
  border-color: #93c5fd;
}

.btn-chart {
  border: 1px solid #cbd5e1;
  background: #fff;
  border-radius: 7px;
  padding: 6px 10px;
  color: #334155;
  font-weight: 700;
  font-size: 12px;
  box-shadow: 0 1px 2px rgba(0, 0, 0, 0.04);
  cursor: pointer;
  display: inline-flex;
  align-items: center;
  gap: 5px;
  transition: all 0.15s;
}

.btn-chart:hover {
  background: #f8fafc;
  border-color: #94a3b8;
}

.btn-chart-primary {
  background: #2457c5;
  color: white;
  border-color: #2457c5;
}

.btn-chart-primary:hover {
  background: #1d48a3;
  border-color: #1d48a3;
}

.btn-chart-danger {
  color: #b42318;
  border-color: #fecaca;
  background: #fff5f5;
}

.btn-chart-danger:hover {
  background: #fee2e2;
}

/* Below Grid / Info Cards */
.info-card {
  background: #fff;
  border: 1px solid #e2e8f0;
  border-radius: 12px;
  padding: 14px;
}

/* Modal Backdrop & Dialog */
.modal-backdrop {
  position: fixed;
  inset: 0;
  background: rgba(15, 23, 42, 0.45);
  backdrop-filter: blur(2px);
  display: flex;
  align-items: center;
  justify-content: center;
  z-index: 9999;
  padding: 16px;
}

.modal {
  background: #fff;
  border-radius: 14px;
  max-width: 520px;
  width: 100%;
  box-shadow: 0 20px 40px rgba(0, 0, 0, 0.2);
  border: 1px solid #e2e8f0;
  overflow: hidden;
  animation: modalFadeIn 0.15s ease-out;
}

@keyframes modalFadeIn {
  from { opacity: 0; transform: scale(0.97); }
  to { opacity: 1; transform: scale(1); }
}

.modal-head {
  padding: 14px 18px;
  background: #f8fafc;
  border-bottom: 1px solid #e2e8f0;
  display: flex;
  align-items: center;
  justify-content: space-between;
}

.modal-kicker {
  font-size: 11px;
  text-transform: uppercase;
  letter-spacing: 0.14em;
  font-weight: 800;
  color: #64748b;
}

.modal-title {
  margin: 2px 0 0;
  font-size: 16px;
  font-weight: 800;
  color: #0f172a;
}

.close {
  background: transparent;
  border: 0;
  font-size: 22px;
  line-height: 1;
  color: #94a3b8;
  cursor: pointer;
  padding: 4px 8px;
}

.close:hover {
  color: #334155;
}

.modal-body {
  padding: 16px 18px;
}

.field-grid {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 12px;
}

.field {
  display: flex;
  flex-direction: column;
  gap: 4px;
}

.field label {
  font-size: 11px;
  font-weight: 700;
  text-transform: uppercase;
  color: #475569;
}

.field input,
.field select,
.field textarea {
  border: 1px solid #cbd5e1;
  border-radius: 7px;
  padding: 7px 10px;
  font-size: 13px;
  color: #1e293b;
  outline: none;
}

.field input:focus,
.field select:focus,
.field textarea:focus {
  border-color: #2457c5;
}

.modal-actions {
  display: flex;
  align-items: center;
  justify-content: space-between;
  gap: 10px;
  margin-top: 16px;
  padding-top: 14px;
  border-top: 1px solid #e2e8f0;
}

.modal-actions-right {
  display: flex;
  gap: 8px;
}
</style>
