<template>
  <TenantLayout>
    <Head :title="tm('Edit_Quote') || 'Edit Quote'" />
    <div class="px-4 py-4 max-w-7xl mx-auto">
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
              <Link :href="route('quotes.index')" class="ml-1 text-sm font-medium text-gray-700 hover:text-gray-900 md:ml-2">
                {{ tm('Quote') || 'Quotes' }}
              </Link>
            </div>
          </li>
          <li>
            <div class="flex items-center">
              <svg class="w-6 h-6 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path>
              </svg>
              <span class="ml-1 text-sm font-medium text-gray-500 md:ml-2">
                {{ tm('Edit_Quote') || 'Edit Quote' }}
              </span>
            </div>
          </li>
        </ol>
      </nav>

      <!-- Shared Form -->
      <QuoteForm
        :is-edit="true"
        :quote="quote"
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
import { Head, Link } from "@inertiajs/inertia-vue3";
import TenantLayout from "@/Layouts/TenantLayout.vue";
import JetBanner from "@/Jetstream/Banner.vue";
import QuoteForm from "@/Components/Tenant/QuoteForm.vue";
import { useI18n } from "vue-i18n";
import { lang } from "@/Lang";

export default {
  name: "QuoteEdit",
  components: {
    Head,
    Link,
    TenantLayout,
    JetBanner,
    QuoteForm,
  },
  props: {
    quote: {
      type: Object,
      required: true,
    },
    exchangeRate: {
      type: [Number, String],
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
  setup() {
    const { tm } = useI18n({
      inheritLocale: true,
      messages: lang,
    });
    return { tm };
  },
};
</script>
