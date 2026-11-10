<template>
  <TenantLayout>
    <Head :title="tm('Edit_Role')" />
    <div class="px-4 py-4">
      <!-- Breadcrumb Navigation -->
      <nav class="flex pt-4 mb-6" aria-label="Breadcrumb">
        <ol class="inline-flex items-center space-x-1 md:space-x-3">
          <li class="inline-flex items-center">
            <Link :href="route('tenant.dashboard')" class="inline-flex items-center text-sm font-medium text-gray-700 hover:text-gray-900 dark:text-gray-400 dark:hover:text-white">
              <svg class="mr-2 w-4 h-4" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                <path d="M10.707 2.293a1 1 0 00-1.414 0l-7 7a1 1 0 001.414 1.414L4 10.414V17a1 1 0 001 1h2a1 1 0 001-1v-2a1 1 0 011-1h2a1 1 0 011 1v2a1 1 0 001 1h2a1 1 0 001-1v-6.586l.293.293a1 1 0 001.414-1.414l-7-7z"></path>
              </svg>
              {{ tm('Home') }}
            </Link>
          </li>
          <li>
            <div class="flex items-center">
              <svg class="w-6 h-6 text-gray-400" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path>
              </svg>
              <Link :href="route('roles.index')" class="ml-1 text-sm font-medium text-gray-700 hover:text-gray-900 md:ml-2 dark:text-gray-400 dark:hover:text-white">
                {{ tm('Roles_and_Permissions') }}
              </Link>
            </div>
          </li>
          <li>
            <div class="flex items-center">
              <svg class="w-6 h-6 text-gray-400" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path>
              </svg>
              <span class="ml-1 text-sm font-medium text-gray-700 hover:text-gray-900 md:ml-2 dark:text-gray-400 dark:hover:text-white">
                {{ tm('Edit') }}
              </span>
            </div>
          </li>
        </ol>
      </nav>

      <form @submit.prevent="submit">
        <div class="space-y-6">
          <!-- Main Card Container -->
          <div class="bg-white rounded-lg shadow-sm border border-gray-100 dark:bg-darker dark:border-primary">
            <!-- Header -->
            <div class="flex items-center justify-between p-4 border-b dark:border-primary">
              <div>
                <h4 class="text-lg font-semibold text-gray-800 dark:text-light">
                  {{ tm('Edit_Role') }}
                </h4>
                <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">
                  {{ tm('Manage_roles_and_control_access_to_application_menus') }}
                </p>
              </div>
            </div>

            <!-- SECTION A: Role Information -->
            <div class="p-6 border-b dark:border-primary">
              <div class="mb-4">
                <h5 class="text-sm font-bold uppercase tracking-wider text-gray-700 dark:text-gray-200 flex items-center gap-2">
                  <span class="w-2 h-2 rounded-full bg-primary"></span>
                  {{ tm('Role_Information') }}
                </h5>
                <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                  {{ tm('Enter_the_basic_information_for_this_role') }}
                </p>
              </div>

              <div class="max-w-xl">
                <label class="block font-medium text-sm pb-2 text-gray-700 dark:text-light" for="role-name">
                  {{ tm('Role_Name') }}
                  <span class="text-red-500 font-bold">*</span>
                </label>
                <input
                  id="role-name"
                  v-model="form.name"
                  type="text"
                  required
                  :placeholder="tm('Enter_role_name')"
                  class="w-full rounded-md border-gray-300 shadow-sm focus:border-primary focus:ring focus:ring-primary focus:ring-opacity-50 dark:bg-dark dark:border-gray-600 dark:text-white text-sm"
                  :class="{ 'border-red-500 focus:border-red-500 focus:ring-red-200': form.errors.name }"
                />
                <div v-if="form.errors.name" class="mt-1 text-xs text-red-600 dark:text-red-400">
                  {{ form.errors.name }}
                </div>
              </div>
            </div>

            <!-- SECTION B: General Permission -->
            <div class="p-6">
              <div class="mb-6">
                <div class="flex items-center justify-between">
                  <div>
                    <h5 class="text-sm font-bold uppercase tracking-wider text-gray-700 dark:text-gray-200 flex items-center gap-2">
                      <span class="w-2 h-2 rounded-full bg-primary"></span>
                      {{ tm('General_Permission') }}
                    </h5>
                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                      {{ tm('Select_which_menus_and_actions_this_role_can_access') }}
                    </p>
                  </div>
                  <!-- Quick Bulk Actions -->
                  <div class="flex items-center gap-2">
                    <button
                      type="button"
                      @click="selectAllGeneral"
                      class="px-2.5 py-1 text-xs font-medium text-primary bg-primary/10 hover:bg-primary/20 rounded transition"
                    >
                      {{ tm('Select_All') }}
                    </button>
                    <button
                      type="button"
                      @click="clearAllGeneral"
                      class="px-2.5 py-1 text-xs font-medium text-gray-500 hover:text-gray-700 bg-gray-100 hover:bg-gray-200 dark:bg-gray-700 dark:text-gray-300 rounded transition"
                    >
                      {{ tm('Clear_All') }}
                    </button>
                  </div>
                </div>
                <div v-if="form.errors.permissions" class="mt-2 text-xs text-red-600 dark:text-red-400">
                  {{ form.errors.permissions }}
                </div>
              </div>

              <!-- Subsection 1: View-Only Modules (Dashboard & Register) -->
              <div class="mb-6">
                <div class="text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400 mb-3">
                  {{ tm('Overview_and_Registration') }}
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                  <!-- Dashboard Card (View Only) -->
                  <div class="flex items-center justify-between p-4 rounded-lg border border-gray-200 dark:border-gray-700 bg-gray-50/50 dark:bg-dark">
                    <div class="flex items-center gap-3">
                      <div class="w-8 h-8 rounded-lg bg-blue-50 text-blue-600 dark:bg-blue-900/30 dark:text-blue-400 flex items-center justify-center font-bold text-xs">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path>
                        </svg>
                      </div>
                      <div>
                        <div class="text-sm font-semibold text-gray-800 dark:text-gray-200">
                          {{ tm('Dashboard') }}
                        </div>
                        <div class="text-xs text-gray-500 dark:text-gray-400">
                          {{ tm('View_only_access') }}
                        </div>
                      </div>
                    </div>
                    <label class="relative inline-flex items-center cursor-pointer">
                      <input
                        type="checkbox"
                        class="sr-only peer"
                        :checked="hasPermission('dashboard:view')"
                        @change="togglePermission('dashboard:view')"
                      />
                      <div class="w-9 h-5 bg-gray-200 peer-focus:outline-none rounded-full peer dark:bg-gray-700 peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-4 after:w-4 after:transition-all dark:border-gray-600 peer-checked:bg-primary"></div>
                      <span class="ml-2 text-xs font-medium text-gray-700 dark:text-gray-300">{{ tm('View') }}</span>
                    </label>
                  </div>

                  <!-- Register Card (View Only) -->
                  <div class="flex items-center justify-between p-4 rounded-lg border border-gray-200 dark:border-gray-700 bg-gray-50/50 dark:bg-dark">
                    <div class="flex items-center gap-3">
                      <div class="w-8 h-8 rounded-lg bg-emerald-50 text-emerald-600 dark:bg-emerald-900/30 dark:text-emerald-400 flex items-center justify-center font-bold text-xs">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                        </svg>
                      </div>
                      <div>
                        <div class="text-sm font-semibold text-gray-800 dark:text-gray-200">
                          {{ tm('Register') }}
                        </div>
                        <div class="text-xs text-gray-500 dark:text-gray-400">
                          {{ tm('View_only_access') }}
                        </div>
                      </div>
                    </div>
                    <label class="relative inline-flex items-center cursor-pointer">
                      <input
                        type="checkbox"
                        class="sr-only peer"
                        :checked="hasPermission('register:view')"
                        @change="togglePermission('register:view')"
                      />
                      <div class="w-9 h-5 bg-gray-200 peer-focus:outline-none rounded-full peer dark:bg-gray-700 peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-4 after:w-4 after:transition-all dark:border-gray-600 peer-checked:bg-primary"></div>
                      <span class="ml-2 text-xs font-medium text-gray-700 dark:text-gray-300">{{ tm('View') }}</span>
                    </label>
                  </div>
                </div>
              </div>

              <!-- Subsection 2: Hospital Menus (Patient, Service, Product, Service Category, Product Category) -->
              <div class="mb-6">
                <div class="text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400 mb-3">
                  {{ tm('Hospital') }}
                </div>
                <div class="space-y-3">
                  <div
                    v-for="menu in hospitalMenus"
                    :key="menu.id"
                    class="p-4 rounded-lg border border-gray-200 dark:border-gray-700 bg-white dark:bg-dark hover:border-gray-300 dark:hover:border-gray-600 transition"
                  >
                    <div class="flex flex-col sm:flex-row sm:items-center justify-between pb-3 border-b border-gray-100 dark:border-gray-700/60 gap-2 mb-3">
                      <div class="flex items-center gap-2">
                        <span class="text-sm font-semibold text-gray-800 dark:text-gray-100">
                          {{ menu.title }}
                        </span>
                        <span class="text-xs px-2 py-0.5 rounded-full bg-gray-100 text-gray-600 dark:bg-gray-700 dark:text-gray-300 font-mono">
                          {{ getSelectedCount(menu.actions) }}/{{ menu.actions.length }}
                        </span>
                      </div>
                      <div class="flex items-center gap-2 text-xs">
                        <button
                          type="button"
                          @click="selectMenuAll(menu.actions)"
                          class="text-primary hover:underline font-medium"
                        >
                          {{ tm('Select_All') }}
                        </button>
                        <span class="text-gray-300 dark:text-gray-600">|</span>
                        <button
                          type="button"
                          @click="clearMenuAll(menu.actions)"
                          class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 font-medium"
                        >
                          {{ tm('Clear_All') }}
                        </button>
                      </div>
                    </div>

                    <!-- 5 Action Switches -->
                    <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-5 gap-3">
                      <div
                        v-for="action in menu.actions"
                        :key="action.permission"
                        class="flex items-center justify-between p-2 rounded-md bg-gray-50 dark:bg-gray-800/60 border border-gray-100 dark:border-gray-700/50"
                      >
                        <span class="text-xs font-medium text-gray-700 dark:text-gray-300">
                          {{ action.label }}
                        </span>
                        <label class="relative inline-flex items-center cursor-pointer">
                          <input
                            type="checkbox"
                            class="sr-only peer"
                            :checked="hasPermission(action.permission)"
                            @change="togglePermission(action.permission)"
                          />
                          <div class="w-8 h-4 bg-gray-200 peer-focus:outline-none rounded-full peer dark:bg-gray-700 peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[1px] after:left-[1px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-3.5 after:w-3.5 after:transition-all dark:border-gray-600 peer-checked:bg-primary"></div>
                        </label>
                      </div>
                    </div>
                  </div>
                </div>
              </div>

              <!-- Subsection 3: Administration (Roles & Users) -->
              <div>
                <div class="text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400 mb-3">
                  {{ tm('Administration') }}
                </div>
                <div class="space-y-3">
                  <!-- Roles Card -->
                  <div class="p-4 rounded-lg border border-gray-200 dark:border-gray-700 bg-white dark:bg-dark hover:border-gray-300 dark:hover:border-gray-600 transition">
                    <div class="flex flex-col sm:flex-row sm:items-center justify-between pb-3 border-b border-gray-100 dark:border-gray-700/60 gap-2 mb-3">
                      <div class="flex items-center gap-2">
                        <span class="text-sm font-semibold text-gray-800 dark:text-gray-100">
                          {{ tm('Roles_and_Permissions') }}
                        </span>
                        <span class="text-xs px-2 py-0.5 rounded-full bg-gray-100 text-gray-600 dark:bg-gray-700 dark:text-gray-300 font-mono">
                          {{ getSelectedCount(roleMenu.actions) }}/{{ roleMenu.actions.length }}
                        </span>
                      </div>
                      <div class="flex items-center gap-2 text-xs">
                        <button
                          type="button"
                          @click="selectMenuAll(roleMenu.actions)"
                          class="text-primary hover:underline font-medium"
                        >
                          {{ tm('Select_All') }}
                        </button>
                        <span class="text-gray-300 dark:text-gray-600">|</span>
                        <button
                          type="button"
                          @click="clearMenuAll(roleMenu.actions)"
                          class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 font-medium"
                        >
                          {{ tm('Clear_All') }}
                        </button>
                      </div>
                    </div>

                    <!-- 5 Action Switches -->
                    <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-5 gap-3">
                      <div
                        v-for="action in roleMenu.actions"
                        :key="action.permission"
                        class="flex items-center justify-between p-2 rounded-md bg-gray-50 dark:bg-gray-800/60 border border-gray-100 dark:border-gray-700/50"
                      >
                        <span class="text-xs font-medium text-gray-700 dark:text-gray-300">
                          {{ action.label }}
                        </span>
                        <label class="relative inline-flex items-center cursor-pointer">
                          <input
                            type="checkbox"
                            class="sr-only peer"
                            :checked="hasPermission(action.permission)"
                            @change="togglePermission(action.permission)"
                          />
                          <div class="w-8 h-4 bg-gray-200 peer-focus:outline-none rounded-full peer dark:bg-gray-700 peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[1px] after:left-[1px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-3.5 after:w-3.5 after:transition-all dark:border-gray-600 peer-checked:bg-primary"></div>
                        </label>
                      </div>
                    </div>
                  </div>

                  <!-- Users Card -->
                  <div class="p-4 rounded-lg border border-gray-200 dark:border-gray-700 bg-white dark:bg-dark hover:border-gray-300 dark:hover:border-gray-600 transition">
                    <div class="flex flex-col sm:flex-row sm:items-center justify-between pb-3 border-b border-gray-100 dark:border-gray-700/60 gap-2 mb-3">
                      <div class="flex items-center gap-2">
                        <span class="text-sm font-semibold text-gray-800 dark:text-gray-100">
                          {{ tm('Users') }}
                        </span>
                        <span class="text-xs px-2 py-0.5 rounded-full bg-gray-100 text-gray-600 dark:bg-gray-700 dark:text-gray-300 font-mono">
                          {{ getSelectedCount(userMenu.actions) }}/{{ userMenu.actions.length }}
                        </span>
                      </div>
                      <div class="flex items-center gap-2 text-xs">
                        <button
                          type="button"
                          @click="selectMenuAll(userMenu.actions)"
                          class="text-primary hover:underline font-medium"
                        >
                          {{ tm('Select_All') }}
                        </button>
                        <span class="text-gray-300 dark:text-gray-600">|</span>
                        <button
                          type="button"
                          @click="clearMenuAll(userMenu.actions)"
                          class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 font-medium"
                        >
                          {{ tm('Clear_All') }}
                        </button>
                      </div>
                    </div>

                    <!-- 5 Action Switches -->
                    <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-5 gap-3">
                      <div
                        v-for="action in userMenu.actions"
                        :key="action.permission"
                        class="flex items-center justify-between p-2 rounded-md bg-gray-50 dark:bg-gray-800/60 border border-gray-100 dark:border-gray-700/50"
                      >
                        <span class="text-xs font-medium text-gray-700 dark:text-gray-300">
                          {{ action.label }}
                        </span>
                        <label class="relative inline-flex items-center cursor-pointer">
                          <input
                            type="checkbox"
                            class="sr-only peer"
                            :checked="hasPermission(action.permission)"
                            @change="togglePermission(action.permission)"
                          />
                          <div class="w-8 h-4 bg-gray-200 peer-focus:outline-none rounded-full peer dark:bg-gray-700 peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[1px] after:left-[1px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-3.5 after:w-3.5 after:transition-all dark:border-gray-600 peer-checked:bg-primary"></div>
                        </label>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>

            <!-- Form Actions Footer -->
            <div class="flex items-center justify-end gap-3 p-4 border-t bg-gray-50/50 dark:bg-dark dark:border-primary">
              <Link
                :href="route('roles.index')"
                class="inline-flex items-center px-4 py-2 bg-gray-100 hover:bg-gray-200 dark:bg-gray-700 dark:hover:bg-gray-600 text-gray-700 dark:text-gray-200 border border-transparent rounded-md font-semibold text-xs uppercase tracking-widest transition"
              >
                {{ tm('Cancel') }}
              </Link>
              <button
                type="submit"
                :disabled="form.processing"
                class="inline-flex items-center px-4 py-2 bg-primary hover:bg-primary-dark border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest disabled:opacity-50 transition shadow-sm"
              >
                {{ form.processing ? tm('Saving') : tm('Save_Role') }}
              </button>
            </div>
          </div>
        </div>
      </form>
    </div>
  </TenantLayout>
</template>

<script>
import { defineComponent } from 'vue'
import { Head, Link } from '@inertiajs/inertia-vue3'
import TenantLayout from '@/Layouts/TenantLayout.vue'
import { useToast } from 'vue-toastification'
import { useI18n } from 'vue-i18n'
import { lang } from '@/Lang'

export default defineComponent({
  components: {
    TenantLayout,
    Head,
    Link,
  },
  props: {
    role: Object,
    assignedPermissions: Array,
    availablePermissions: Object,
  },
  setup() {
    const { tm } = useI18n({
      inheritLocale: true,
      messages: lang,
    })
    const toast = useToast()
    return { tm, toast }
  },
  data() {
    return {
      form: this.$inertia.form({
        name: this.role?.name || '',
        permissions: [...(this.assignedPermissions || [])],
      }),
    }
  },
  computed: {
    hospitalMenus() {
      return [
        {
          id: 'patient',
          title: this.tm('Patient'),
          actions: [
            { permission: 'patient:view', label: this.tm('View') },
            { permission: 'patient:create', label: this.tm('Create') },
            { permission: 'patient:edit', label: this.tm('Edit') },
            { permission: 'patient:save', label: this.tm('Save') },
            { permission: 'patient:delete', label: this.tm('Delete') },
          ],
        },
        {
          id: 'service',
          title: this.tm('Service'),
          actions: [
            { permission: 'service:view', label: this.tm('View') },
            { permission: 'service:create', label: this.tm('Create') },
            { permission: 'service:edit', label: this.tm('Edit') },
            { permission: 'service:save', label: this.tm('Save') },
            { permission: 'service:delete', label: this.tm('Delete') },
          ],
        },
        {
          id: 'product',
          title: this.tm('Product'),
          actions: [
            { permission: 'product:view', label: this.tm('View') },
            { permission: 'product:create', label: this.tm('Create') },
            { permission: 'product:edit', label: this.tm('Edit') },
            { permission: 'product:save', label: this.tm('Save') },
            { permission: 'product:delete', label: this.tm('Delete') },
          ],
        },
        {
          id: 'service_category',
          title: this.tm('Service_Category'),
          actions: [
            { permission: 'service-category:view', label: this.tm('View') },
            { permission: 'service-category:create', label: this.tm('Create') },
            { permission: 'service-category:edit', label: this.tm('Edit') },
            { permission: 'service-category:save', label: this.tm('Save') },
            { permission: 'service-category:delete', label: this.tm('Delete') },
          ],
        },
        {
          id: 'product_category',
          title: this.tm('Product_Category'),
          actions: [
            { permission: 'product-category:view', label: this.tm('View') },
            { permission: 'product-category:create', label: this.tm('Create') },
            { permission: 'product-category:edit', label: this.tm('Edit') },
            { permission: 'product-category:save', label: this.tm('Save') },
            { permission: 'product-category:delete', label: this.tm('Delete') },
          ],
        },
      ]
    },
    roleMenu() {
      return {
        id: 'role',
        title: this.tm('Roles_and_Permissions'),
        actions: [
          { permission: 'role:view', label: this.tm('View') },
          { permission: 'role:create', label: this.tm('Create') },
          { permission: 'role:edit', label: this.tm('Edit') },
          { permission: 'role:save', label: this.tm('Save') },
          { permission: 'role:delete', label: this.tm('Delete') },
        ],
      }
    },
    userMenu() {
      return {
        id: 'user',
        title: this.tm('Users'),
        actions: [
          { permission: 'user:view', label: this.tm('View') },
          { permission: 'user:create', label: this.tm('Create') },
          { permission: 'user:edit', label: this.tm('Edit') },
          { permission: 'user:save', label: this.tm('Save') },
          { permission: 'user:delete', label: this.tm('Delete') },
        ],
      }
    },
    allPermissionNames() {
      const list = ['dashboard:view', 'register:view']
      this.hospitalMenus.forEach((m) => {
        m.actions.forEach((a) => list.push(a.permission))
      })
      this.roleMenu.actions.forEach((a) => list.push(a.permission))
      this.userMenu.actions.forEach((a) => list.push(a.permission))
      return list
    },
  },
  methods: {
    hasPermission(name) {
      return this.form.permissions.includes(name)
    },
    togglePermission(name) {
      const index = this.form.permissions.indexOf(name)
      if (index > -1) {
        this.form.permissions.splice(index, 1)
      } else {
        this.form.permissions.push(name)
      }
    },
    selectMenuAll(actions) {
      actions.forEach((a) => {
        if (!this.form.permissions.includes(a.permission)) {
          this.form.permissions.push(a.permission)
        }
      })
    },
    clearMenuAll(actions) {
      const permsToClear = actions.map((a) => a.permission)
      this.form.permissions = this.form.permissions.filter((p) => !permsToClear.includes(p))
    },
    getSelectedCount(actions) {
      return actions.filter((a) => this.form.permissions.includes(a.permission)).length
    },
    selectAllGeneral() {
      this.form.permissions = [...this.allPermissionNames]
    },
    clearAllGeneral() {
      this.form.permissions = []
    },
    submit() {
      this.form.put(route('roles.update', this.role.id), {
        preserveScroll: true,
        onSuccess: () => {
          this.toast.success(this.tm('Role_updated_successfully'))
        },
        onError: (errors) => {
          if (errors) {
            Object.values(errors).flat().forEach((msg) => {
              if (typeof msg === 'string') {
                this.toast.error(msg)
              }
            })
          }
        },
      })
    },
  },
})
</script>
