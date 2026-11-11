<template>
  <TenantLayout>
    <Head :title="tm('Create_Invoice') || 'Create Invoice'" />
    <div class="px-4 py-4 max-w-7xl mx-auto">
      <jet-banner />

      <!-- Breadcrumb -->
      <nav class="flex pt-4 mb-6" aria-label="Breadcrumb">
        <ol class="inline-flex items-center space-x-1 md:space-x-3">
          <li class="inline-flex items-center">
            <Link :href="route('tenant.dashboard')" class="inline-flex items-center text-sm font-medium text-gray-700 hover:text-gray-900 dark:text-gray-400 dark:hover:text-white">
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
              <Link :href="route('invoices.index')" class="ml-1 text-sm font-medium text-gray-700 hover:text-gray-900 md:ml-2">
                {{ tm('Invoices') || 'Invoices' }}
              </Link>
            </div>
          </li>
          <li>
            <div class="flex items-center">
              <svg class="w-6 h-6 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path>
              </svg>
              <span class="ml-1 text-sm font-medium text-gray-500 md:ml-2">
                {{ tm('Create_Invoice') || 'Create Invoice' }}
              </span>
            </div>
          </li>
        </ol>
      </nav>

      <!-- Shared Form -->
      <InvoiceForm
        :suggested-invoice-number="suggestedInvoiceNumber"
        :default-prefix="defaultPrefix"
        :exchange-rate="exchangeRate"
        :patients="patients"
        :doctors="doctors"
        :assistants="assistants"
        :services="services"
        :products="products"
        :conditions="conditions"
      />
    </div>
  </TenantLayout>
</template>

<script>
import TenantLayout from "@/Layouts/TenantLayout.vue";
import { Head, Link } from "@inertiajs/inertia-vue3";
import JetBanner from "@/Jetstream/Banner.vue";
import InvoiceForm from "@/Components/Tenant/InvoiceForm.vue";

export default {
  components: {
    TenantLayout,
    Head,
    Link,
    JetBanner,
    InvoiceForm,
  },
  props: {
    suggestedInvoiceNumber: String,
    defaultPrefix: Object,
    exchangeRate: Number,
    patients: Array,
    doctors: Array,
    assistants: Array,
    services: Array,
    products: Array,
    conditions: Array,
  },
};
</script>
