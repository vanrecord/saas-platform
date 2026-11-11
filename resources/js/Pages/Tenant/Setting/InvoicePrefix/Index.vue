<template>
  <TenantLayout>
    <Head :title="tm('Invoice_Prefix') || 'Invoice Prefix'" />
    <div class="px-4 py-4">
      <jet-banner />
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
              <span class="ml-1 text-sm font-medium text-gray-500 md:ml-2">Settings</span>
            </div>
          </li>
          <li>
            <div class="flex items-center">
              <svg class="w-6 h-6 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path>
              </svg>
              <span class="ml-1 text-sm font-medium text-gray-700 hover:text-gray-900 md:ml-2 dark:text-gray-400 dark:hover:text-white">
                {{ tm('New_Prefix_Invoice') || 'New Prefix Invoice' }}
              </span>
            </div>
          </li>
        </ol>
      </nav>

      <div class="bg-white rounded-md dark:bg-darker shadow-sm">
        <div class="flex items-center justify-between p-4 border-b dark:border-primary">
          <h4 class="text-lg font-semibold text-gray-600 dark:text-light">
            {{ tm('Invoice_Prefix') || 'Invoice Prefix Settings' }}
          </h4>
          <div>
            <button
              v-if="can(['all', 'invoice-prefix:create', 'invoice-prefix:save'])"
              type="button"
              @click="openCreateModal"
              class="inline-flex items-center px-4 py-2 bg-primary hover:bg-primary-dark border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest transition"
            >
              + {{ tm('Create_Prefix') || 'Create Prefix' }}
            </button>
          </div>
        </div>

        <div class="p-4">
          <Table :resource="prefixes">
            <template #cell(id)="{index: key}">
              {{ ++key }}
            </template>

            <template #cell(prefix_name)="{ item: prefix }">
              <span class="font-bold text-slate-800 font-mono text-sm px-2 py-0.5 bg-slate-100 rounded">
                {{ prefix.prefix_name }}
              </span>
            </template>

            <template #cell(prefix_format)="{ item: prefix }">
              <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                {{ prefix.prefix_format }}
              </span>
            </template>

            <template #cell(default_prefix)="{ item: prefix }">
              <span v-if="prefix.default_prefix" class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-emerald-100 text-emerald-800">
                <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                  <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                </svg>
                Default
              </span>
              <span v-else class="text-xs text-slate-400">
                No
              </span>
            </template>

            <template #cell(created_at)="{ item: prefix }">
              <span class="text-xs text-slate-500">
                {{ formatDate(prefix.created_at) }}
              </span>
            </template>

            <template #cell(actions)="{ item: prefix }">
              <div class="flex items-center space-x-2">
                <button
                  v-if="can(['all', 'invoice-prefix:edit', 'invoice-prefix:save'])"
                  type="button"
                  @click="openEditModal(prefix)"
                  class="p-1 text-blue-600 hover:text-blue-800 rounded hover:bg-blue-50 transition"
                  title="Edit"
                >
                  <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                  </svg>
                </button>
                <button
                  v-if="can(['all', 'invoice-prefix:delete']) && !prefix.default_prefix"
                  type="button"
                  @click="confirmDelete(prefix)"
                  class="p-1 text-red-600 hover:text-red-800 rounded hover:bg-red-50 transition"
                  title="Delete"
                >
                  <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                  </svg>
                </button>
              </div>
            </template>
          </Table>
        </div>
      </div>
    </div>

    <!-- Create/Edit Modal -->
    <div v-if="showModal" class="fixed inset-0 z-50 overflow-y-auto flex items-center justify-center p-4 bg-slate-900/50 backdrop-blur-xs">
      <div class="bg-white dark:bg-darker rounded-xl shadow-2xl max-w-md w-full overflow-hidden border border-slate-200 dark:border-slate-700 animate-in fade-in zoom-in duration-150">
        <div class="flex items-center justify-between px-6 py-4 border-b border-slate-100 dark:border-slate-700">
          <h3 class="text-base font-bold text-slate-800 dark:text-light">
            {{ isEditing ? (tm('Edit_Invoice_Prefix') || 'Edit Invoice Prefix') : (tm('Create_Invoice_Prefix') || 'Create Invoice Prefix') }}
          </h3>
          <button @click="closeModal" type="button" class="text-slate-400 hover:text-slate-600 transition">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
            </svg>
          </button>
        </div>

        <form @submit.prevent="submitForm" class="p-6 space-y-4">
          <!-- Prefix Name -->
          <div>
            <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-1">
              {{ tm('Name') || 'Prefix Name' }} <span class="text-red-500">*</span>
            </label>
            <input
              type="text"
              v-model="form.prefix_name"
              placeholder="e.g. INV"
              class="w-full px-3 py-2 text-sm border border-slate-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-primary dark:bg-slate-800 dark:border-slate-600 dark:text-white"
              required
            />
            <div v-if="form.errors.prefix_name" class="text-xs text-red-500 mt-1">
              {{ form.errors.prefix_name }}
            </div>
            <p class="text-[11px] text-slate-400 mt-1">
              Letters or code prefixed to the generated invoice number (e.g. INV).
            </p>
          </div>

          <!-- Format -->
          <div>
            <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-1">
              {{ tm('Format') || 'Format' }} <span class="text-red-500">*</span>
            </label>
            <select
              v-model="form.prefix_format"
              class="w-full px-3 py-2 text-sm border border-slate-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-primary dark:bg-slate-800 dark:border-slate-600 dark:text-white"
              required
            >
              <option value="Series">Series (e.g. INV-000001)</option>
              <option value="Year-Series">Year-Series (e.g. INV-2026-000001)</option>
            </select>
            <div v-if="form.errors.prefix_format" class="text-xs text-red-500 mt-1">
              {{ form.errors.prefix_format }}
            </div>
          </div>

          <!-- Default Prefix Checkbox -->
          <div class="pt-2">
            <label class="inline-flex items-center cursor-pointer">
              <input
                type="checkbox"
                v-model="form.default_prefix"
                class="rounded border-slate-300 text-primary shadow-xs focus:ring-primary h-4 w-4"
              />
              <span class="ml-2 text-sm font-medium text-slate-700 dark:text-slate-300">
                {{ tm('Default_Prefix') || 'Set as Default Prefix' }}
              </span>
            </label>
            <p class="text-[11px] text-slate-400 ml-6 mt-0.5">
              Only one prefix can be active as default. Setting this will unset previous defaults.
            </p>
          </div>

          <!-- Modal Actions -->
          <div class="flex items-center justify-end space-x-3 pt-4 border-t border-slate-100 dark:border-slate-700">
            <button
              type="button"
              @click="closeModal"
              class="px-4 py-2 text-xs font-semibold text-slate-600 hover:text-slate-800 border border-slate-300 rounded-lg transition"
            >
              {{ tm('Cancel') || 'Cancel' }}
            </button>
            <button
              type="submit"
              :disabled="form.processing"
              class="px-4 py-2 text-xs font-semibold text-white bg-primary hover:bg-primary-dark rounded-lg transition shadow-xs disabled:opacity-50"
            >
              {{ form.processing ? 'Saving...' : (isEditing ? 'Update Prefix' : 'Create Prefix') }}
            </button>
          </div>
        </form>
      </div>
    </div>
  </TenantLayout>
</template>

<script>
import TenantLayout from "@/Layouts/TenantLayout.vue";
import { Head, Link, useForm } from "@inertiajs/inertia-vue3";
import JetBanner from "@/Jetstream/Banner.vue";
import { Table } from "@/Components/Datatables";

export default {
  components: {
    TenantLayout,
    Head,
    Link,
    JetBanner,
    Table,
  },
  props: {
    prefixes: Object,
  },
  data() {
    return {
      showModal: false,
      isEditing: false,
      editingId: null,
      form: useForm({
        prefix_name: "INV",
        prefix_format: "Year-Series",
        default_prefix: false,
      }),
    };
  },
  methods: {
    formatDate(dateStr) {
      if (!dateStr) return "-";
      const d = new Date(dateStr);
      return d.toLocaleDateString();
    },
    openCreateModal() {
      this.isEditing = false;
      this.editingId = null;
      this.form.reset();
      this.form.prefix_name = "INV";
      this.form.prefix_format = "Year-Series";
      this.form.default_prefix = false;
      this.form.clearErrors();
      this.showModal = true;
    },
    openEditModal(prefix) {
      this.isEditing = true;
      this.editingId = prefix.id;
      this.form.clearErrors();
      this.form.prefix_name = prefix.prefix_name;
      this.form.prefix_format = prefix.prefix_format;
      this.form.default_prefix = Boolean(prefix.default_prefix);
      this.showModal = true;
    },
    closeModal() {
      this.showModal = false;
      this.form.reset();
    },
    submitForm() {
      if (this.isEditing && this.editingId) {
        this.form.put(route("settings.invoice-prefixes.update", this.editingId), {
          onSuccess: () => {
            this.closeModal();
            this.$toast?.success("Invoice Prefix updated successfully.");
          },
        });
      } else {
        this.form.post(route("settings.invoice-prefixes.store"), {
          onSuccess: () => {
            this.closeModal();
            this.$toast?.success("Invoice Prefix created successfully.");
          },
        });
      }
    },
    confirmDelete(prefix) {
      if (confirm(`Are you sure you want to delete prefix "${prefix.prefix_name}"?`)) {
        this.$inertia.delete(route("settings.invoice-prefixes.destroy", prefix.id), {
          onSuccess: () => {
            this.$toast?.success("Invoice Prefix deleted successfully.");
          },
        });
      }
    },
  },
};
</script>
