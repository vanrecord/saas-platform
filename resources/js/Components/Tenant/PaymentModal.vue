<template>
  <div
    v-if="show"
    class="fixed inset-0 z-50 overflow-y-auto bg-slate-900/60 backdrop-blur-sm flex items-center justify-center p-3 sm:p-4 transition-all duration-300"
    @click.self="closeModal"
  >
    <div
      class="bg-white rounded-2xl shadow-2xl border border-slate-200/80 w-full max-w-3xl overflow-hidden transform transition-all duration-200 my-6"
    >
      <!-- Modal Header -->
      <div class="px-6 pt-5 pb-4 flex items-start justify-between border-b border-slate-100">
        <div class="flex items-start gap-3.5">
          <div class="w-10 h-10 rounded-xl bg-blue-50 border border-blue-100 flex items-center justify-center text-blue-600 shadow-xs shrink-0 mt-0.5">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 14l6-6m-5.5.5h.01m4.99 5h.01M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16l3.5-2 3.5 2 3.5-2 3.5 2zM10 8.5a.5.5 0 11-1 0 .5.5 0 011 0zm5 5a.5.5 0 11-1 0 .5.5 0 011 0z" />
            </svg>
          </div>
          <div>
            <h2 class="text-lg font-bold text-slate-900 leading-snug">Payment & Transaction History</h2>
            <p class="text-xs text-slate-500 mt-0.5">Manage patient transactions, payment methods, and account balances.</p>
          </div>
        </div>

        <button
          type="button"
          @click="closeModal"
          class="text-slate-400 hover:text-slate-600 hover:bg-slate-100 p-1.5 rounded-lg transition"
          title="Close modal"
        >
          <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
          </svg>
        </button>
      </div>

      <!-- Loading State -->
      <div v-if="loading" class="py-20 text-center text-slate-500">
        <svg class="w-8 h-8 mx-auto animate-spin text-blue-600 mb-2" fill="none" viewBox="0 0 24 24">
          <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
          <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
        </svg>
        <span class="text-xs font-semibold">Loading payment details...</span>
      </div>

      <!-- Modal Body -->
      <div v-else class="p-6 space-y-6 max-h-[calc(85vh-130px)] overflow-y-auto">
        
        <!-- Flash Alert Message if any -->
        <div v-if="alertMessage" :class="alertSuccess ? 'bg-emerald-50 border-emerald-200 text-emerald-800' : 'bg-red-50 border-red-200 text-red-800'" class="p-3 rounded-xl border text-xs flex items-center justify-between">
          <span>{{ alertMessage }}</span>
          <button type="button" @click="alertMessage = ''" class="ml-2 font-bold opacity-60 hover:opacity-100">&times;</button>
        </div>

        <!-- 1. NEW PAYMENT ENTRY CARD -->
        <div class="rounded-2xl border border-slate-200/90 bg-white p-5 space-y-4 shadow-xs">
          <!-- Card Header -->
          <div class="flex items-center justify-between">
            <span class="text-[11px] font-bold uppercase tracking-wider text-slate-700">NEW PAYMENT ENTRY</span>
            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[11px] font-semibold bg-sky-50 text-sky-600 border border-sky-100">
              Secure Gateway
            </span>
          </div>

          <!-- Payment Methods Selection Grid -->
          <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
            <div
              v-for="method in displayPaymentMethods"
              :key="method.id"
              @click="selectPaymentMethod(method)"
              :class="selectedMethodId === method.id
                ? 'border-2 border-blue-600 bg-white shadow-xs ring-1 ring-blue-600/20'
                : 'border border-slate-200/90 hover:border-slate-300 bg-white'"
              class="rounded-xl p-3.5 flex items-center justify-between cursor-pointer transition select-none"
            >
              <div class="flex items-center gap-3">
                <!-- Method Logo / Icon -->
                <template v-if="isAbaMethod(method)">
                  <div class="w-10 h-10 rounded-lg bg-[#005a9c] flex items-center justify-center text-white font-black text-xs tracking-tight shadow-xs shrink-0">
                    ABA
                  </div>
                </template>
                <template v-else>
                  <div class="w-10 h-10 rounded-lg bg-emerald-50 border border-emerald-100/60 flex items-center justify-center text-emerald-600 shadow-xs shrink-0">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z" />
                    </svg>
                  </div>
                </template>

                <div>
                  <div class="text-sm font-bold text-slate-900 leading-snug">{{ method.name }}</div>
                  <div class="text-[11px] text-slate-500 mt-0.5">
                    {{ isAbaMethod(method) ? 'Instant mobile scanning' : 'Manual verification' }}
                  </div>
                </div>
              </div>

              <!-- Right Indicator -->
              <div>
                <div
                  v-if="selectedMethodId === method.id"
                  class="w-5 h-5 rounded-full bg-blue-600 text-white flex items-center justify-center shadow-xs"
                >
                  <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7" />
                  </svg>
                </div>
                <div
                  v-else
                  class="w-3.5 h-3.5 rounded-full"
                  :class="isAbaMethod(method) ? 'bg-slate-200' : 'bg-emerald-500'"
                />
              </div>
            </div>
          </div>

          <!-- Available Credit Balance Bar -->
          <div class="rounded-xl border border-slate-200/80 bg-white p-3.5 flex items-center justify-between shadow-xs">
            <div class="flex items-center gap-3">
              <div class="w-9 h-9 rounded-xl bg-amber-50 border border-amber-100 flex items-center justify-center text-amber-500 shrink-0">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
              </div>
              <div>
                <div class="text-[11px] font-semibold text-slate-500 uppercase tracking-wider">Available Credit Balance</div>
                <div class="text-base font-black text-slate-900 leading-tight">${{ formatCurrency(availableCredit) }}</div>
              </div>
            </div>

            <div class="flex items-center gap-3">
              <span class="text-xs font-semibold text-slate-700">Apply Credit Balance</span>
              <!-- Toggle switch -->
              <button
                type="button"
                @click="toggleApplyCredit"
                :class="applyCredit ? 'bg-blue-600' : 'bg-slate-200'"
                class="relative inline-flex h-6 w-11 flex-shrink-0 cursor-pointer rounded-full border-2 border-transparent transition-colors duration-200 ease-in-out focus:outline-none"
              >
                <span
                  :class="applyCredit ? 'translate-x-5' : 'translate-x-0'"
                  class="pointer-events-none inline-block h-5 w-5 transform rounded-full bg-white shadow ring-0 transition duration-200 ease-in-out"
                />
              </button>
            </div>
          </div>

          <!-- Form Fields Grid -->
          <div class="grid grid-cols-1 sm:grid-cols-3 gap-3.5 pt-1">
            <!-- Payment Account -->
            <div>
              <label class="block text-[11px] font-bold text-slate-600 uppercase tracking-wider mb-1.5">
                PAYMENT ACCOUNT
              </label>
              <select
                v-model="form.payment_account"
                class="w-full h-10 px-3 text-sm rounded-lg border border-slate-300 bg-white text-slate-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition shadow-xs"
              >
                <option v-for="acc in paymentAccounts" :key="acc" :value="acc">{{ acc }}</option>
              </select>
            </div>

            <!-- Amount -->
            <div>
              <label class="block text-[11px] font-bold text-slate-600 uppercase tracking-wider mb-1.5">
                AMOUNT
              </label>
              <div class="relative">
                <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-sm font-semibold text-slate-400">$</span>
                <input
                  type="number"
                  step="0.01"
                  min="0"
                  :max="maxAllowedAmount"
                  v-model.number="form.amount"
                  placeholder="0.00"
                  class="w-full h-10 pl-7 pr-3 text-sm font-semibold text-slate-900 rounded-lg border border-slate-300 bg-white focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition shadow-xs"
                />
              </div>
            </div>

            <!-- Date -->
            <div>
              <label class="block text-[11px] font-bold text-slate-600 uppercase tracking-wider mb-1.5">
                DATE
              </label>
              <DateInput
                v-model="form.date"
                :masks="{ input: 'DD/MM/YYYY', modelValue: 'YYYY-MM-DD' }"
                input-class="h-10 text-sm font-medium"
              />
            </div>
          </div>

          <!-- Add Payment Button -->
          <div class="flex justify-end pt-1">
            <button
              type="button"
              @click="submitPayment"
              :disabled="submitting || (Number(form.amount) <= 0 && !applyCredit)"
              class="inline-flex items-center gap-1.5 px-5 py-2.5 text-xs font-bold text-white bg-blue-600 hover:bg-blue-700 active:bg-blue-800 disabled:opacity-50 disabled:cursor-not-allowed rounded-lg shadow-sm transition"
            >
              <svg v-if="!submitting" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4" />
              </svg>
              <svg v-else class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
              </svg>
              <span>+ Add Payment</span>
            </button>
          </div>
        </div>

        <!-- 2. TRANSACTION RECORDS TABLE -->
        <div class="space-y-3">
          <div class="flex items-center justify-between">
            <span class="text-[11px] font-bold uppercase tracking-wider text-slate-700">TRANSACTION RECORDS</span>
            <span class="text-xs text-slate-500">
              Showing {{ transactions.length }} {{ transactions.length === 1 ? 'entry' : 'entries' }}
            </span>
          </div>

          <div class="rounded-xl border border-slate-200 overflow-hidden bg-white shadow-xs">
            <table class="w-full text-left text-xs">
              <thead>
                <tr class="bg-slate-50/80 border-b border-slate-200 text-slate-500 font-semibold uppercase text-[11px] tracking-wider">
                  <th class="py-3 px-4">DATE</th>
                  <th class="py-3 px-4">PATIENT</th>
                  <th class="py-3 px-4">INVOICE</th>
                  <th class="py-3 px-4">PAID CURRENCY</th>
                  <th class="py-3 px-4 text-right">AMOUNT ($)</th>
                  <th class="py-3 px-4 text-right">AMOUNT (៛ KHR)</th>
                </tr>
              </thead>
              <tbody class="divide-y divide-slate-100">
                <tr v-if="transactions.length === 0">
                  <td colspan="6" class="py-8 text-center text-slate-400 italic">
                    No payment transactions recorded for this invoice yet.
                  </td>
                </tr>
                <tr v-for="t in transactions" :key="t.id" class="hover:bg-slate-50/50 transition">
                  <td class="py-3 px-4 text-slate-700 whitespace-nowrap">{{ formatDate(t.date) }}</td>
                  <td class="py-3 px-4 font-medium text-blue-600 hover:underline cursor-pointer">{{ t.patient_name }}</td>
                  <td class="py-3 px-4">
                    <span class="inline-block px-2 py-0.5 rounded bg-slate-100 text-slate-700 font-mono text-[11px] font-medium">
                      {{ t.invoice_number }}
                    </span>
                  </td>
                  <td class="py-3 px-4">
                    <span class="inline-block px-2 py-0.5 rounded text-[11px] font-semibold bg-emerald-50 text-emerald-700 border border-emerald-100">
                      {{ t.currency || 'USD' }}
                    </span>
                  </td>
                  <td class="py-3 px-4 text-right font-bold text-slate-900">${{ formatCurrency(t.amount) }}</td>
                  <td class="py-3 px-4 text-right text-slate-700">{{ formatRiel(t.amount_kh) }}</td>
                </tr>
              </tbody>
              <!-- Summary Rows -->
              <tfoot class="border-t-2 border-slate-200 bg-white font-bold">
                <tr>
                  <td colspan="4" class="py-3 px-4 text-right text-[11px] uppercase tracking-wider text-slate-500">
                    TOTAL PAID:
                  </td>
                  <td class="py-3 px-4 text-right text-sm text-blue-600">
                    ${{ formatCurrency(summary.total_paid_usd) }}
                  </td>
                  <td class="py-3 px-4 text-right text-sm text-blue-600">
                    {{ formatRiel(summary.total_paid_kh) }} ៛
                  </td>
                </tr>
                <tr class="border-t border-slate-100">
                  <td colspan="4" class="py-3 px-4 text-right text-[11px] uppercase tracking-wider text-slate-700">
                    OUTSTANDING BALANCE:
                  </td>
                  <td colspan="2" class="py-3 px-4 text-right text-base font-black text-red-600">
                    ${{ formatCurrency(summary.outstanding_balance) }}
                  </td>
                </tr>
              </tfoot>
            </table>
          </div>
        </div>

      </div>

      <!-- Modal Footer -->
      <div class="px-6 py-4 bg-slate-50/50 border-t border-slate-100 flex items-center justify-between">
        <div class="flex items-center gap-1.5 text-xs text-slate-400">
          <svg class="w-4 h-4 text-slate-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
          </svg>
          <span>Secure 256-bit SSL Encrypted Transaction</span>
        </div>

        <div class="flex items-center gap-3">
          <button
            type="button"
            @click="closeModal"
            class="px-4 py-2 text-xs font-semibold text-slate-600 hover:text-slate-900 rounded-lg hover:bg-slate-100 transition"
          >
            Close
          </button>
          <button
            type="button"
            @click="printOrExportStatement"
            class="inline-flex items-center gap-2 px-4 py-2 text-xs font-bold text-white bg-slate-900 hover:bg-slate-800 active:bg-slate-950 rounded-lg shadow-sm transition"
          >
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4H7v4a2 2 0 002 2zM7 9V5a2 2 0 012-2h6a2 2 0 012 2v4" />
            </svg>
            <span>Print / Export</span>
          </button>
        </div>
      </div>

    </div>
  </div>
</template>

<script>
import axios from 'axios';
import dayjs from 'dayjs';
import DateInput from '@/Components/Forms/DateInput.vue';

export default {
  name: 'PaymentModal',
  components: {
    DateInput,
  },
  props: {
    show: {
      type: Boolean,
      default: false,
    },
    invoiceId: {
      type: [Number, String],
      default: null,
    },
  },
  emits: ['close', 'payment-saved'],
  data() {
    return {
      loading: false,
      submitting: false,
      alertMessage: '',
      alertSuccess: true,
      invoice: null,
      patient: null,
      availableCredit: 0.0,
      paymentMethods: [],
      paymentAccounts: ['00047_USD', '00047_KHR', 'CASH_USD'],
      selectedMethodId: null,
      applyCredit: false,
      form: {
        payment_account: '00047_USD',
        amount: 0.0,
        date: dayjs().format('YYYY-MM-DD'),
      },
      transactions: [],
      summary: {
        total_paid_usd: 0.0,
        total_paid_kh: 0,
        refunded_usd: 0.0,
        outstanding_balance: 0.0,
        available_credit: 0.0,
      },
    };
  },
  computed: {
    displayPaymentMethods() {
      if (this.paymentMethods.length > 0) {
        return this.paymentMethods;
      }
      // Fallback defaults matching UI screenshot
      return [
        { id: 1, name: 'ABA KHQR', provider: 'aba' },
        { id: 2, name: 'Cash / Bank Transfer', provider: null },
      ];
    },
    maxAllowedAmount() {
      const balance = Number(this.summary.outstanding_balance) || 0;
      if (this.applyCredit) {
        const credit = Math.min(Number(this.availableCredit) || 0, balance);
        return Math.max(0, balance - credit);
      }
      return balance;
    },
  },
  watch: {
    show(val) {
      if (val && this.invoiceId) {
        this.fetchPaymentData();
      } else {
        this.resetState();
      }
    },
    invoiceId(newVal) {
      if (this.show && newVal) {
        this.fetchPaymentData();
      }
    },
  },
  methods: {
    resetState() {
      this.alertMessage = '';
      this.applyCredit = false;
      this.form.date = dayjs().format('YYYY-MM-DD');
    },
    isAbaMethod(method) {
      if (!method) return false;
      const name = (method.name || '').toLowerCase();
      const prov = (method.provider || '').toLowerCase();
      return name.includes('aba') || prov === 'aba';
    },
    selectPaymentMethod(method) {
      this.selectedMethodId = method.id;
      if (method.register_ref) {
        const acc = `${method.register_ref}_${method.currency || 'USD'}`;
        if (this.paymentAccounts.includes(acc)) {
          this.form.payment_account = acc;
        }
      }
    },
    toggleApplyCredit() {
      const credit = Number(this.availableCredit) || 0;
      if (!this.applyCredit) {
        if (credit <= 0) {
          this.alertMessage = 'Patient has no available credit balance.';
          this.alertSuccess = false;
          return;
        }
        this.applyCredit = true;
        const currentBalance = Number(this.summary.outstanding_balance) || 0;
        const creditToApply = Math.min(credit, currentBalance);
        const remainingToPay = Math.max(0, currentBalance - creditToApply);
        this.form.amount = Number(remainingToPay.toFixed(2));
      } else {
        this.applyCredit = false;
        const currentBalance = Number(this.summary.outstanding_balance) || 0;
        this.form.amount = Number(currentBalance.toFixed(2));
      }
    },
    async fetchPaymentData() {
      this.loading = true;
      this.alertMessage = '';
      try {
        const response = await axios.get(route('tenant.invoices.payment-history', this.invoiceId));
        const data = response.data;
        this.invoice = data.invoice;
        this.patient = data.patient;
        this.availableCredit = Number(data.patient?.available_credit) || 0.0;
        this.paymentMethods = data.payment_methods || [];
        this.paymentAccounts = data.payment_accounts?.length ? data.payment_accounts : ['00047_USD', '00047_KHR', 'CASH_USD'];
        this.transactions = data.transactions || [];
        this.summary = data.summary || {
          total_paid_usd: 0,
          total_paid_kh: 0,
          outstanding_balance: 0,
          available_credit: this.availableCredit,
        };

        // Select default payment method
        if (!this.selectedMethodId && this.paymentMethods.length > 0) {
          const aba = this.paymentMethods.find((m) => this.isAbaMethod(m));
          this.selectedMethodId = aba ? aba.id : this.paymentMethods[0].id;
        }

        // Set default amount to remaining balance
        const outstanding = Number(this.summary.outstanding_balance) || 0.0;
        this.form.amount = Number(outstanding.toFixed(2));
        this.applyCredit = false;
      } catch (err) {
        console.error('Failed to load payment history:', err);
        this.alertMessage = 'Failed to load invoice payment information.';
        this.alertSuccess = false;
      } finally {
        this.loading = false;
      }
    },
    async submitPayment() {
      const amount = Number(this.form.amount) || 0;
      if (amount <= 0 && !this.applyCredit) {
        this.alertMessage = 'Please enter an amount to pay or apply credit balance.';
        this.alertSuccess = false;
        return;
      }

      this.submitting = true;
      this.alertMessage = '';

      try {
        const payload = {
          payment_method_id: this.selectedMethodId,
          amount: amount,
          date: this.form.date,
          payment_account: this.form.payment_account,
          apply_credit: this.applyCredit,
        };

        const response = await axios.post(route('tenant.invoices.payments.store', this.invoiceId), payload);

        this.alertMessage = response.data.message || 'Payment recorded successfully!';
        this.alertSuccess = true;

        // Emit updated invoice so parent DataTable updates balance and status badges
        this.$emit('payment-saved', response.data.invoice);

        // Refresh modal data in real-time
        await this.fetchPaymentData();
      } catch (err) {
        console.error('Payment failed:', err);
        const errorMsg =
          err.response?.data?.message ||
          (err.response?.data?.errors ? Object.values(err.response.data.errors).flat().join(' ') : 'Failed to record payment.');
        this.alertMessage = errorMsg;
        this.alertSuccess = false;
      } finally {
        this.submitting = false;
      }
    },
    printOrExportStatement() {
      if (!this.invoiceId) return;
      const url = route('tenant.invoices.statement', this.invoiceId);
      window.open(url, '_blank');
    },
    closeModal() {
      this.$emit('close');
    },
    formatDate(date) {
      if (!date) return '-';
      return dayjs(date).format('DD/MM/YYYY');
    },
    formatCurrency(val) {
      return (Number(val) || 0).toLocaleString('en-US', {
        minimumFractionDigits: 2,
        maximumFractionDigits: 2,
      });
    },
    formatRiel(val) {
      return (Number(val) || 0).toLocaleString('en-US');
    },
  },
};
</script>
