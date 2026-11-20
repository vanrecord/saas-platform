<template>
  <TenantLayout>
    <Head :title="tm('Create_Role')" />
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
                {{ tm('Create') }}
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
                  {{ tm('Create_Role') }}
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

              <!-- Subsection 1: View-Only Modules (Dashboard, Register & Payment) -->
              <div class="mb-6">
                <div class="text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400 mb-3">
                  {{ tm('Overview_and_Registration') }}
                </div>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
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
                    <label
                      class="inline-flex items-center gap-2 px-3 py-1.5 rounded-lg border cursor-pointer transition select-none"
                      :class="hasPermission('dashboard:view')
                        ? 'bg-blue-50/60 dark:bg-blue-900/20 border-primary/40 text-primary dark:text-blue-300 font-semibold shadow-2xs'
                        : 'bg-white dark:bg-gray-800 border-gray-200 dark:border-gray-700 text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700'"
                    >
                      <input
                        type="checkbox"
                        class="w-4 h-4 rounded border-gray-300 text-primary focus:ring-primary dark:border-gray-600 dark:bg-gray-700 dark:ring-offset-gray-800 focus:ring-2 cursor-pointer"
                        :checked="hasPermission('dashboard:view')"
                        @change="togglePermission('dashboard:view')"
                      />
                      <span class="text-xs">{{ tm('View') }}</span>
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
                    <label
                      class="inline-flex items-center gap-2 px-3 py-1.5 rounded-lg border cursor-pointer transition select-none"
                      :class="hasPermission('register:view')
                        ? 'bg-blue-50/60 dark:bg-blue-900/20 border-primary/40 text-primary dark:text-blue-300 font-semibold shadow-2xs'
                        : 'bg-white dark:bg-gray-800 border-gray-200 dark:border-gray-700 text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700'"
                    >
                      <input
                        type="checkbox"
                        class="w-4 h-4 rounded border-gray-300 text-primary focus:ring-primary dark:border-gray-600 dark:bg-gray-700 dark:ring-offset-gray-800 focus:ring-2 cursor-pointer"
                        :checked="hasPermission('register:view')"
                        @change="togglePermission('register:view')"
                      />
                      <span class="text-xs">{{ tm('View') }}</span>
                    </label>
                  </div>

                  <!-- Payment Card (View Only) -->
                  <div class="flex items-center justify-between p-4 rounded-lg border border-gray-200 dark:border-gray-700 bg-gray-50/50 dark:bg-dark">
                    <div class="flex items-center gap-3">
                      <div class="w-8 h-8 rounded-lg bg-indigo-50 text-indigo-600 dark:bg-indigo-900/30 dark:text-indigo-400 flex items-center justify-center font-bold text-xs">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                      </div>
                      <div>
                        <div class="text-sm font-semibold text-gray-800 dark:text-gray-200">
                          {{ tm('Payment') || 'Payment' }}
                        </div>
                        <div class="text-xs text-gray-500 dark:text-gray-400">
                          {{ tm('View_only_access') }}
                        </div>
                      </div>
                    </div>
                    <label
                      class="inline-flex items-center gap-2 px-3 py-1.5 rounded-lg border cursor-pointer transition select-none"
                      :class="hasPermission('payment:view')
                        ? 'bg-blue-50/60 dark:bg-blue-900/20 border-primary/40 text-primary dark:text-blue-300 font-semibold shadow-2xs'
                        : 'bg-white dark:bg-gray-800 border-gray-200 dark:border-gray-700 text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700'"
                    >
                      <input
                        type="checkbox"
                        class="w-4 h-4 rounded border-gray-300 text-primary focus:ring-primary dark:border-gray-600 dark:bg-gray-700 dark:ring-offset-gray-800 focus:ring-2 cursor-pointer"
                        :checked="hasPermission('payment:view')"
                        @change="togglePermission('payment:view')"
                      />
                      <span class="text-xs">{{ tm('View') }}</span>
                    </label>
                  </div>
                </div>
              </div>

              <!-- Subsection 2: Conditions (Diagnostic Legend) -->
              <div class="mb-6">
                <div class="text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400 mb-3">
                  {{ tm('Conditions') || 'Conditions' }}
                </div>
                <div class="space-y-3">
                  <div
                    class="p-4 rounded-lg border border-gray-200 dark:border-gray-700 bg-white dark:bg-dark hover:border-gray-300 dark:hover:border-gray-600 transition"
                  >
                    <div class="flex flex-col sm:flex-row sm:items-center justify-between pb-3 border-b border-gray-100 dark:border-gray-700/60 gap-2 mb-3">
                      <div class="flex items-center gap-2">
                        <span class="text-sm font-semibold text-gray-800 dark:text-gray-100">
                          {{ conditionMenu.title }}
                        </span>
                        <span class="text-xs px-2 py-0.5 rounded-full bg-gray-100 text-gray-600 dark:bg-gray-700 dark:text-gray-300 font-mono">
                          {{ getSelectedCount(conditionMenu.actions) }}/{{ conditionMenu.actions.length }}
                        </span>
                      </div>
                      <div class="flex items-center gap-2 text-xs">
                        <button
                          type="button"
                          @click="selectMenuAll(conditionMenu.actions)"
                          class="text-primary hover:underline font-medium"
                        >
                          {{ tm('Select_All') }}
                        </button>
                        <span class="text-gray-300 dark:text-gray-600">|</span>
                        <button
                          type="button"
                          @click="clearMenuAll(conditionMenu.actions)"
                          class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 font-medium"
                        >
                          {{ tm('Clear_All') }}
                        </button>
                      </div>
                    </div>

                    <!-- 4-col Action Checkboxes -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-3">
                      <label
                        v-for="action in conditionMenu.actions"
                        :key="action.permission"
                        class="flex items-center gap-2.5 px-3 py-2.5 rounded-lg border cursor-pointer transition select-none"
                        :class="hasPermission(action.permission)
                          ? 'bg-blue-50/60 dark:bg-blue-900/20 border-primary/40 text-primary dark:text-blue-300 font-semibold shadow-2xs'
                          : 'bg-gray-50/80 dark:bg-gray-800/60 border-gray-200/80 dark:border-gray-700/60 text-gray-700 dark:text-gray-300 hover:bg-gray-100/80 dark:hover:bg-gray-800 hover:border-gray-300 dark:hover:border-gray-600'"
                      >
                        <input
                          type="checkbox"
                          class="w-4 h-4 rounded border-gray-300 text-primary focus:ring-primary dark:border-gray-600 dark:bg-gray-700 dark:ring-offset-gray-800 focus:ring-2 cursor-pointer"
                          :checked="hasPermission(action.permission)"
                          @change="togglePermission(action.permission)"
                        />
                        <span class="text-xs">
                          {{ action.label }}
                        </span>
                      </label>
                    </div>
                  </div>
                </div>
              </div>

              <!-- Subsection 3: Hospital Menus (Patient, Service, Product, Service Category, Product Category, Suppliers) -->
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

                    <!-- 4-col Action Checkboxes -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-3">
                      <label
                        v-for="action in menu.actions"
                        :key="action.permission"
                        class="flex items-center gap-2.5 px-3 py-2.5 rounded-lg border cursor-pointer transition select-none"
                        :class="hasPermission(action.permission)
                          ? 'bg-blue-50/60 dark:bg-blue-900/20 border-primary/40 text-primary dark:text-blue-300 font-semibold shadow-2xs'
                          : 'bg-gray-50/80 dark:bg-gray-800/60 border-gray-200/80 dark:border-gray-700/60 text-gray-700 dark:text-gray-300 hover:bg-gray-100/80 dark:hover:bg-gray-800 hover:border-gray-300 dark:hover:border-gray-600'"
                      >
                        <input
                          type="checkbox"
                          class="w-4 h-4 rounded border-gray-300 text-primary focus:ring-primary dark:border-gray-600 dark:bg-gray-700 dark:ring-offset-gray-800 focus:ring-2 cursor-pointer"
                          :checked="hasPermission(action.permission)"
                          @change="togglePermission(action.permission)"
                        />
                        <span class="text-xs">
                          {{ action.label }}
                        </span>
                      </label>
                    </div>
                  </div>
                </div>
              </div>

              <!-- Subsection 3.5: Quote -->
              <div class="mb-6">
                <div class="text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400 mb-3">
                  {{ tm('Quote') || 'Quote' }}
                </div>
                <div class="space-y-3">
                  <div class="p-4 rounded-lg border border-gray-200 dark:border-gray-700 bg-white dark:bg-dark hover:border-gray-300 dark:hover:border-gray-600 transition">
                    <div class="flex flex-col sm:flex-row sm:items-center justify-between pb-3 border-b border-gray-100 dark:border-gray-700/60 gap-2 mb-3">
                      <div class="flex items-center gap-2">
                        <span class="text-sm font-semibold text-gray-800 dark:text-gray-100">
                          {{ quoteMenu.title }}
                        </span>
                        <span class="text-xs px-2 py-0.5 rounded-full bg-gray-100 text-gray-600 dark:bg-gray-700 dark:text-gray-300 font-mono">
                          {{ getSelectedCount(quoteMenu.actions) }}/{{ quoteMenu.actions.length }}
                        </span>
                      </div>
                      <div class="flex items-center gap-2 text-xs">
                        <button
                          type="button"
                          @click="selectMenuAll(quoteMenu.actions)"
                          class="text-primary hover:underline font-medium"
                        >
                          {{ tm('Select_All') }}
                        </button>
                        <span class="text-gray-300 dark:text-gray-600">|</span>
                        <button
                          type="button"
                          @click="clearMenuAll(quoteMenu.actions)"
                          class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 font-medium"
                        >
                          {{ tm('Clear_All') }}
                        </button>
                      </div>
                    </div>

                    <!-- 4-col Action Checkboxes -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-3">
                      <label
                        v-for="action in quoteMenu.actions"
                        :key="action.permission"
                        class="flex items-center gap-2.5 px-3 py-2.5 rounded-lg border cursor-pointer transition select-none"
                        :class="hasPermission(action.permission)
                          ? 'bg-blue-50/60 dark:bg-blue-900/20 border-primary/40 text-primary dark:text-blue-300 font-semibold shadow-2xs'
                          : 'bg-gray-50/80 dark:bg-gray-800/60 border-gray-200/80 dark:border-gray-700/60 text-gray-700 dark:text-gray-300 hover:bg-gray-100/80 dark:hover:bg-gray-800 hover:border-gray-300 dark:hover:border-gray-600'"
                      >
                        <input
                          type="checkbox"
                          class="w-4 h-4 rounded border-gray-300 text-primary focus:ring-primary dark:border-gray-600 dark:bg-gray-700 dark:ring-offset-gray-800 focus:ring-2 cursor-pointer"
                          :checked="hasPermission(action.permission)"
                          @change="togglePermission(action.permission)"
                        />
                        <span class="text-xs">
                          {{ action.label }}
                        </span>
                      </label>
                    </div>
                  </div>
                </div>
              </div>

              <!-- Subsection 3.6: Target (Clinic Target & Doctor Target) -->
              <div class="mb-6">
                <div class="text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400 mb-3">
                  {{ tm('Target') || 'Target' }}
                </div>
                <div class="space-y-3">
                  <div
                    v-for="menu in targetMenus"
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

                    <!-- 5-col Action Checkboxes -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-5 gap-3">
                      <label
                        v-for="action in menu.actions"
                        :key="action.permission"
                        class="flex items-center gap-2.5 px-3 py-2.5 rounded-lg border cursor-pointer transition select-none"
                        :class="hasPermission(action.permission)
                          ? 'bg-blue-50/60 dark:bg-blue-900/20 border-primary/40 text-primary dark:text-blue-300 font-semibold shadow-2xs'
                          : 'bg-gray-50/80 dark:bg-gray-800/60 border-gray-200/80 dark:border-gray-700/60 text-gray-700 dark:text-gray-300 hover:bg-gray-100/80 dark:hover:bg-gray-800 hover:border-gray-300 dark:hover:border-gray-600'"
                      >
                        <input
                          type="checkbox"
                          class="w-4 h-4 rounded border-gray-300 text-primary focus:ring-primary dark:border-gray-600 dark:bg-gray-700 dark:ring-offset-gray-800 focus:ring-2 cursor-pointer"
                          :checked="hasPermission(action.permission)"
                          @change="togglePermission(action.permission)"
                        />
                        <span class="text-xs">
                          {{ action.label }}
                        </span>
                      </label>
                    </div>
                  </div>
                </div>
              </div>

              <!-- Subsection 4: Invoice & Billing -->
              <div class="mb-6">
                <div class="text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400 mb-3">
                  {{ tm('Invoice') || 'Invoice' }}
                </div>
                <div class="space-y-3">
                  <div class="p-4 rounded-lg border border-gray-200 dark:border-gray-700 bg-white dark:bg-dark hover:border-gray-300 dark:hover:border-gray-600 transition">
                    <div class="flex flex-col sm:flex-row sm:items-center justify-between pb-3 border-b border-gray-100 dark:border-gray-700/60 gap-2 mb-3">
                      <div class="flex items-center gap-2">
                        <span class="text-sm font-semibold text-gray-800 dark:text-gray-100">
                          {{ invoiceMenu.title }}
                        </span>
                        <span class="text-xs px-2 py-0.5 rounded-full bg-gray-100 text-gray-600 dark:bg-gray-700 dark:text-gray-300 font-mono">
                          {{ getSelectedCount(invoiceMenu.actions) }}/{{ invoiceMenu.actions.length }}
                        </span>
                      </div>
                      <div class="flex items-center gap-2 text-xs">
                        <button
                          type="button"
                          @click="selectMenuAll(invoiceMenu.actions)"
                          class="text-primary hover:underline font-medium"
                        >
                          {{ tm('Select_All') }}
                        </button>
                        <span class="text-gray-300 dark:text-gray-600">|</span>
                        <button
                          type="button"
                          @click="clearMenuAll(invoiceMenu.actions)"
                          class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 font-medium"
                        >
                          {{ tm('Clear_All') }}
                        </button>
                      </div>
                    </div>

                    <!-- 4-col Action Checkboxes -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-3">
                      <label
                        v-for="action in invoiceMenu.actions"
                        :key="action.permission"
                        class="flex items-center gap-2.5 px-3 py-2.5 rounded-lg border cursor-pointer transition select-none"
                        :class="hasPermission(action.permission)
                          ? 'bg-blue-50/60 dark:bg-blue-900/20 border-primary/40 text-primary dark:text-blue-300 font-semibold shadow-2xs'
                          : 'bg-gray-50/80 dark:bg-gray-800/60 border-gray-200/80 dark:border-gray-700/60 text-gray-700 dark:text-gray-300 hover:bg-gray-100/80 dark:hover:bg-gray-800 hover:border-gray-300 dark:hover:border-gray-600'"
                      >
                        <input
                          type="checkbox"
                          class="w-4 h-4 rounded border-gray-300 text-primary focus:ring-primary dark:border-gray-600 dark:bg-gray-700 dark:ring-offset-gray-800 focus:ring-2 cursor-pointer"
                          :checked="hasPermission(action.permission)"
                          @change="togglePermission(action.permission)"
                        />
                        <span class="text-xs">
                          {{ action.label }}
                        </span>
                      </label>
                    </div>
                  </div>
                </div>
              </div>

              <!-- Subsection 4.5: Cash Deposit (Accounting) -->
              <div class="mb-6">
                <div class="text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400 mb-3">
                  {{ tm('Cash_Deposit') || 'Cash Deposit' }}
                </div>
                <div class="space-y-3">
                  <div class="p-4 rounded-lg border border-gray-200 dark:border-gray-700 bg-white dark:bg-dark hover:border-gray-300 dark:hover:border-gray-600 transition">
                    <div class="flex flex-col sm:flex-row sm:items-center justify-between pb-3 border-b border-gray-100 dark:border-gray-700/60 gap-2 mb-3">
                      <div class="flex items-center gap-2">
                        <span class="text-sm font-semibold text-gray-800 dark:text-gray-100">
                          {{ cashDepositMenu.title }}
                        </span>
                        <span class="text-xs px-2 py-0.5 rounded-full bg-gray-100 text-gray-600 dark:bg-gray-700 dark:text-gray-300 font-mono">
                          {{ getSelectedCount(cashDepositMenu.actions) }}/{{ cashDepositMenu.actions.length }}
                        </span>
                      </div>
                      <div class="flex items-center gap-2 text-xs">
                        <button
                          type="button"
                          @click="selectMenuAll(cashDepositMenu.actions)"
                          class="text-primary hover:underline font-medium"
                        >
                          {{ tm('Select_All') }}
                        </button>
                        <span class="text-gray-300 dark:text-gray-600">|</span>
                        <button
                          type="button"
                          @click="clearMenuAll(cashDepositMenu.actions)"
                          class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 font-medium"
                        >
                          {{ tm('Clear_All') }}
                        </button>
                      </div>
                    </div>

                    <!-- Action Checkboxes -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-5 gap-3">
                      <label
                        v-for="action in cashDepositMenu.actions"
                        :key="action.permission"
                        class="flex items-center gap-2.5 px-3 py-2.5 rounded-lg border cursor-pointer transition select-none"
                        :class="hasPermission(action.permission)
                          ? 'bg-blue-50/60 dark:bg-blue-900/20 border-primary/40 text-primary dark:text-blue-300 font-semibold shadow-2xs'
                          : 'bg-gray-50/80 dark:bg-gray-800/60 border-gray-200/80 dark:border-gray-700/60 text-gray-700 dark:text-gray-300 hover:bg-gray-100/80 dark:hover:bg-gray-800 hover:border-gray-300 dark:hover:border-gray-600'"
                      >
                        <input
                          type="checkbox"
                          class="w-4 h-4 rounded border-gray-300 text-primary focus:ring-primary dark:border-gray-600 dark:bg-gray-700 dark:ring-offset-gray-800 focus:ring-2 cursor-pointer"
                          :checked="hasPermission(action.permission)"
                          @change="togglePermission(action.permission)"
                        />
                        <span class="text-xs">
                          {{ action.label }}
                        </span>
                      </label>
                    </div>
                  </div>
                </div>
              </div>

              <!-- Subsection 5: Administration (Roles, Doctor & Users) -->
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

                    <!-- 4-col Action Checkboxes -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-3">
                      <label
                        v-for="action in roleMenu.actions"
                        :key="action.permission"
                        class="flex items-center gap-2.5 px-3 py-2.5 rounded-lg border cursor-pointer transition select-none"
                        :class="hasPermission(action.permission)
                          ? 'bg-blue-50/60 dark:bg-blue-900/20 border-primary/40 text-primary dark:text-blue-300 font-semibold shadow-2xs'
                          : 'bg-gray-50/80 dark:bg-gray-800/60 border-gray-200/80 dark:border-gray-700/60 text-gray-700 dark:text-gray-300 hover:bg-gray-100/80 dark:hover:bg-gray-800 hover:border-gray-300 dark:hover:border-gray-600'"
                      >
                        <input
                          type="checkbox"
                          class="w-4 h-4 rounded border-gray-300 text-primary focus:ring-primary dark:border-gray-600 dark:bg-gray-700 dark:ring-offset-gray-800 focus:ring-2 cursor-pointer"
                          :checked="hasPermission(action.permission)"
                          @change="togglePermission(action.permission)"
                        />
                        <span class="text-xs">
                          {{ action.label }}
                        </span>
                      </label>
                    </div>
                  </div>

                  <!-- Doctor Card -->
                  <div class="p-4 rounded-lg border border-gray-200 dark:border-gray-700 bg-white dark:bg-dark hover:border-gray-300 dark:hover:border-gray-600 transition">
                    <div class="flex flex-col sm:flex-row sm:items-center justify-between pb-3 border-b border-gray-100 dark:border-gray-700/60 gap-2 mb-3">
                      <div class="flex items-center gap-2">
                        <span class="text-sm font-semibold text-gray-800 dark:text-gray-100">
                          {{ doctorMenu.title }}
                        </span>
                        <span class="text-xs px-2 py-0.5 rounded-full bg-gray-100 text-gray-600 dark:bg-gray-700 dark:text-gray-300 font-mono">
                          {{ getSelectedCount(doctorMenu.actions) }}/{{ doctorMenu.actions.length }}
                        </span>
                      </div>
                      <div class="flex items-center gap-2 text-xs">
                        <button
                          type="button"
                          @click="selectMenuAll(doctorMenu.actions)"
                          class="text-primary hover:underline font-medium"
                        >
                          {{ tm('Select_All') }}
                        </button>
                        <span class="text-gray-300 dark:text-gray-600">|</span>
                        <button
                          type="button"
                          @click="clearMenuAll(doctorMenu.actions)"
                          class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 font-medium"
                        >
                          {{ tm('Clear_All') }}
                        </button>
                      </div>
                    </div>

                    <!-- 4-col Action Checkboxes -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-3">
                      <label
                        v-for="action in doctorMenu.actions"
                        :key="action.permission"
                        class="flex items-center gap-2.5 px-3 py-2.5 rounded-lg border cursor-pointer transition select-none"
                        :class="hasPermission(action.permission)
                          ? 'bg-blue-50/60 dark:bg-blue-900/20 border-primary/40 text-primary dark:text-blue-300 font-semibold shadow-2xs'
                          : 'bg-gray-50/80 dark:bg-gray-800/60 border-gray-200/80 dark:border-gray-700/60 text-gray-700 dark:text-gray-300 hover:bg-gray-100/80 dark:hover:bg-gray-800 hover:border-gray-300 dark:hover:border-gray-600'"
                      >
                        <input
                          type="checkbox"
                          class="w-4 h-4 rounded border-gray-300 text-primary focus:ring-primary dark:border-gray-600 dark:bg-gray-700 dark:ring-offset-gray-800 focus:ring-2 cursor-pointer"
                          :checked="hasPermission(action.permission)"
                          @change="togglePermission(action.permission)"
                        />
                        <span class="text-xs">
                          {{ action.label }}
                        </span>
                      </label>
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

                    <!-- 4-col Action Checkboxes -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-3">
                      <label
                        v-for="action in userMenu.actions"
                        :key="action.permission"
                        class="flex items-center gap-2.5 px-3 py-2.5 rounded-lg border cursor-pointer transition select-none"
                        :class="hasPermission(action.permission)
                          ? 'bg-blue-50/60 dark:bg-blue-900/20 border-primary/40 text-primary dark:text-blue-300 font-semibold shadow-2xs'
                          : 'bg-gray-50/80 dark:bg-gray-800/60 border-gray-200/80 dark:border-gray-700/60 text-gray-700 dark:text-gray-300 hover:bg-gray-100/80 dark:hover:bg-gray-800 hover:border-gray-300 dark:hover:border-gray-600'"
                      >
                        <input
                          type="checkbox"
                          class="w-4 h-4 rounded border-gray-300 text-primary focus:ring-primary dark:border-gray-600 dark:bg-gray-700 dark:ring-offset-gray-800 focus:ring-2 cursor-pointer"
                          :checked="hasPermission(action.permission)"
                          @change="togglePermission(action.permission)"
                        />
                        <span class="text-xs">
                          {{ action.label }}
                        </span>
                      </label>
                    </div>
                  </div>
                </div>
              </div>

              <!-- Subsection 6: Settings (General, Medicines, Prescriptions & Payment Methods) -->
              <div class="mt-6">
                <div class="text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400 mb-3">
                  {{ tm('Settings') || 'Settings' }}
                </div>
                <div class="space-y-3">
                  <!-- General Settings Card -->
                  <div class="p-4 rounded-lg border border-gray-200 dark:border-gray-700 bg-white dark:bg-dark hover:border-gray-300 dark:hover:border-gray-600 transition">
                    <div class="flex flex-col sm:flex-row sm:items-center justify-between pb-3 border-b border-gray-100 dark:border-gray-700/60 gap-2 mb-3">
                      <div class="flex items-center gap-2">
                        <span class="text-sm font-semibold text-gray-800 dark:text-gray-100">
                          {{ generalMenu.title }}
                        </span>
                        <span class="text-xs px-2 py-0.5 rounded-full bg-gray-100 text-gray-600 dark:bg-gray-700 dark:text-gray-300 font-mono">
                          {{ getSelectedCount(generalMenu.actions) }}/{{ generalMenu.actions.length }}
                        </span>
                      </div>
                      <div class="flex items-center gap-2 text-xs">
                        <button
                          type="button"
                          @click="selectMenuAll(generalMenu.actions)"
                          class="text-primary hover:underline font-medium"
                        >
                          {{ tm('Select_All') }}
                        </button>
                        <span class="text-gray-300 dark:text-gray-600">|</span>
                        <button
                          type="button"
                          @click="clearMenuAll(generalMenu.actions)"
                          class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 font-medium"
                        >
                          {{ tm('Clear_All') }}
                        </button>
                      </div>
                    </div>

                    <!-- 4-col Action Checkboxes -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-3">
                      <label
                        v-for="action in generalMenu.actions"
                        :key="action.permission"
                        class="flex items-center gap-2.5 px-3 py-2.5 rounded-lg border cursor-pointer transition select-none"
                        :class="hasPermission(action.permission)
                          ? 'bg-blue-50/60 dark:bg-blue-900/20 border-primary/40 text-primary dark:text-blue-300 font-semibold shadow-2xs'
                          : 'bg-gray-50/80 dark:bg-gray-800/60 border-gray-200/80 dark:border-gray-700/60 text-gray-700 dark:text-gray-300 hover:bg-gray-100/80 dark:hover:bg-gray-800 hover:border-gray-300 dark:hover:border-gray-600'"
                      >
                        <input
                          type="checkbox"
                          class="w-4 h-4 rounded border-gray-300 text-primary focus:ring-primary dark:border-gray-600 dark:bg-gray-700 dark:ring-offset-gray-800 focus:ring-2 cursor-pointer"
                          :checked="hasPermission(action.permission)"
                          @change="togglePermission(action.permission)"
                        />
                        <span class="text-xs">
                          {{ action.label }}
                        </span>
                      </label>
                    </div>
                  </div>

                  <!-- Medicines Card -->
                  <div class="p-4 rounded-lg border border-gray-200 dark:border-gray-700 bg-white dark:bg-dark hover:border-gray-300 dark:hover:border-gray-600 transition">
                    <div class="flex flex-col sm:flex-row sm:items-center justify-between pb-3 border-b border-gray-100 dark:border-gray-700/60 gap-2 mb-3">
                      <div class="flex items-center gap-2">
                        <span class="text-sm font-semibold text-gray-800 dark:text-gray-100">
                          {{ tm('Medicines') || 'Medicines' }}
                        </span>
                        <span class="text-xs px-2 py-0.5 rounded-full bg-gray-100 text-gray-600 dark:bg-gray-700 dark:text-gray-300 font-mono">
                          {{ getSelectedCount(medicineMenu.actions) }}/{{ medicineMenu.actions.length }}
                        </span>
                      </div>
                      <div class="flex items-center gap-2 text-xs">
                        <button
                          type="button"
                          @click="selectMenuAll(medicineMenu.actions)"
                          class="text-primary hover:underline font-medium"
                        >
                          {{ tm('Select_All') }}
                        </button>
                        <span class="text-gray-300 dark:text-gray-600">|</span>
                        <button
                          type="button"
                          @click="clearMenuAll(medicineMenu.actions)"
                          class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 font-medium"
                        >
                          {{ tm('Clear_All') }}
                        </button>
                      </div>
                    </div>

                    <!-- 4-col Action Checkboxes -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-3">
                      <label
                        v-for="action in medicineMenu.actions"
                        :key="action.permission"
                        class="flex items-center gap-2.5 px-3 py-2.5 rounded-lg border cursor-pointer transition select-none"
                        :class="hasPermission(action.permission)
                          ? 'bg-blue-50/60 dark:bg-blue-900/20 border-primary/40 text-primary dark:text-blue-300 font-semibold shadow-2xs'
                          : 'bg-gray-50/80 dark:bg-gray-800/60 border-gray-200/80 dark:border-gray-700/60 text-gray-700 dark:text-gray-300 hover:bg-gray-100/80 dark:hover:bg-gray-800 hover:border-gray-300 dark:hover:border-gray-600'"
                      >
                        <input
                          type="checkbox"
                          class="w-4 h-4 rounded border-gray-300 text-primary focus:ring-primary dark:border-gray-600 dark:bg-gray-700 dark:ring-offset-gray-800 focus:ring-2 cursor-pointer"
                          :checked="hasPermission(action.permission)"
                          @change="togglePermission(action.permission)"
                        />
                        <span class="text-xs">
                          {{ action.label }}
                        </span>
                      </label>
                    </div>
                  </div>

                  <!-- Prescriptions Card -->
                  <div class="p-4 rounded-lg border border-gray-200 dark:border-gray-700 bg-white dark:bg-dark hover:border-gray-300 dark:hover:border-gray-600 transition">
                    <div class="flex flex-col sm:flex-row sm:items-center justify-between pb-3 border-b border-gray-100 dark:border-gray-700/60 gap-2 mb-3">
                      <div class="flex items-center gap-2">
                        <span class="text-sm font-semibold text-gray-800 dark:text-gray-100">
                          {{ tm('Prescriptions') || 'Prescriptions' }}
                        </span>
                        <span class="text-xs px-2 py-0.5 rounded-full bg-gray-100 text-gray-600 dark:bg-gray-700 dark:text-gray-300 font-mono">
                          {{ getSelectedCount(prescriptionMenu.actions) }}/{{ prescriptionMenu.actions.length }}
                        </span>
                      </div>
                      <div class="flex items-center gap-2 text-xs">
                        <button
                          type="button"
                          @click="selectMenuAll(prescriptionMenu.actions)"
                          class="text-primary hover:underline font-medium"
                        >
                          {{ tm('Select_All') }}
                        </button>
                        <span class="text-gray-300 dark:text-gray-600">|</span>
                        <button
                          type="button"
                          @click="clearMenuAll(prescriptionMenu.actions)"
                          class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 font-medium"
                        >
                          {{ tm('Clear_All') }}
                        </button>
                      </div>
                    </div>

                    <!-- 4-col Action Checkboxes -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-3">
                      <label
                        v-for="action in prescriptionMenu.actions"
                        :key="action.permission"
                        class="flex items-center gap-2.5 px-3 py-2.5 rounded-lg border cursor-pointer transition select-none"
                        :class="hasPermission(action.permission)
                          ? 'bg-blue-50/60 dark:bg-blue-900/20 border-primary/40 text-primary dark:text-blue-300 font-semibold shadow-2xs'
                          : 'bg-gray-50/80 dark:bg-gray-800/60 border-gray-200/80 dark:border-gray-700/60 text-gray-700 dark:text-gray-300 hover:bg-gray-100/80 dark:hover:bg-gray-800 hover:border-gray-300 dark:hover:border-gray-600'"
                      >
                        <input
                          type="checkbox"
                          class="w-4 h-4 rounded border-gray-300 text-primary focus:ring-primary dark:border-gray-600 dark:bg-gray-700 dark:ring-offset-gray-800 focus:ring-2 cursor-pointer"
                          :checked="hasPermission(action.permission)"
                          @change="togglePermission(action.permission)"
                        />
                        <span class="text-xs">
                          {{ action.label }}
                        </span>
                      </label>
                    </div>
                  </div>

                  <!-- Payment Methods Card -->
                  <div class="p-4 rounded-lg border border-gray-200 dark:border-gray-700 bg-white dark:bg-dark hover:border-gray-300 dark:hover:border-gray-600 transition">
                    <div class="flex flex-col sm:flex-row sm:items-center justify-between pb-3 border-b border-gray-100 dark:border-gray-700/60 gap-2 mb-3">
                      <div class="flex items-center gap-2">
                        <span class="text-sm font-semibold text-gray-800 dark:text-gray-100">
                          {{ paymentMethodMenu.title }}
                        </span>
                        <span class="text-xs px-2 py-0.5 rounded-full bg-gray-100 text-gray-600 dark:bg-gray-700 dark:text-gray-300 font-mono">
                          {{ getSelectedCount(paymentMethodMenu.actions) }}/{{ paymentMethodMenu.actions.length }}
                        </span>
                      </div>
                      <div class="flex items-center gap-2 text-xs">
                        <button
                          type="button"
                          @click="selectMenuAll(paymentMethodMenu.actions)"
                          class="text-primary hover:underline font-medium"
                        >
                          {{ tm('Select_All') }}
                        </button>
                        <span class="text-gray-300 dark:text-gray-600">|</span>
                        <button
                          type="button"
                          @click="clearMenuAll(paymentMethodMenu.actions)"
                          class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 font-medium"
                        >
                          {{ tm('Clear_All') }}
                        </button>
                      </div>
                    </div>

                    <!-- 4-col Action Checkboxes -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-3">
                      <label
                        v-for="action in paymentMethodMenu.actions"
                        :key="action.permission"
                        class="flex items-center gap-2.5 px-3 py-2.5 rounded-lg border cursor-pointer transition select-none"
                        :class="hasPermission(action.permission)
                          ? 'bg-blue-50/60 dark:bg-blue-900/20 border-primary/40 text-primary dark:text-blue-300 font-semibold shadow-2xs'
                          : 'bg-gray-50/80 dark:bg-gray-800/60 border-gray-200/80 dark:border-gray-700/60 text-gray-700 dark:text-gray-300 hover:bg-gray-100/80 dark:hover:bg-gray-800 hover:border-gray-300 dark:hover:border-gray-600'"
                      >
                        <input
                          type="checkbox"
                          class="w-4 h-4 rounded border-gray-300 text-primary focus:ring-primary dark:border-gray-600 dark:bg-gray-700 dark:ring-offset-gray-800 focus:ring-2 cursor-pointer"
                          :checked="hasPermission(action.permission)"
                          @change="togglePermission(action.permission)"
                        />
                        <span class="text-xs">
                          {{ action.label }}
                        </span>
                      </label>
                    </div>
                  </div>
                </div>
              </div>
            </div>

            <!-- Footer Actions -->
            <div class="flex items-center justify-end px-8 py-4 bg-gray-50 border-t border-gray-100 dark:bg-darker dark:border-primary space-x-2">
              <save-button :loading="form.processing">
                {{ tm('Save') }}
              </save-button>
              <back-button :url="route('roles.index')">
                {{ tm('Cancel') }}
              </back-button>
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
import SaveButton from '@/Components/Button/Save.vue'
import BackButton from '@/Components/Button/Back.vue'
import { useToast } from 'vue-toastification'
import { useI18n } from 'vue-i18n'
import { lang } from '@/Lang'

export default defineComponent({
  components: {
    TenantLayout,
    Head,
    Link,
    SaveButton,
    BackButton,
  },
  props: {
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
        name: '',
        permissions: [],
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
            { permission: 'patient:delete', label: this.tm('Delete') },
            { permission: 'patient:save', label: this.tm('Save') },
          ],
        },
        {
          id: 'service',
          title: this.tm('Service'),
          actions: [
            { permission: 'service:view', label: this.tm('View') },
            { permission: 'service:create', label: this.tm('Create') },
            { permission: 'service:edit', label: this.tm('Edit') },
            { permission: 'service:delete', label: this.tm('Delete') },
            { permission: 'service:save', label: this.tm('Save') },
          ],
        },
        {
          id: 'product',
          title: this.tm('Product'),
          actions: [
            { permission: 'product:view', label: this.tm('View') },
            { permission: 'product:create', label: this.tm('Create') },
            { permission: 'product:edit', label: this.tm('Edit') },
            { permission: 'product:delete', label: this.tm('Delete') },
            { permission: 'product:save', label: this.tm('Save') },
          ],
        },
        {
          id: 'service_category',
          title: this.tm('Service_Category'),
          actions: [
            { permission: 'service-category:view', label: this.tm('View') },
            { permission: 'service-category:create', label: this.tm('Create') },
            { permission: 'service-category:edit', label: this.tm('Edit') },
            { permission: 'service-category:delete', label: this.tm('Delete') },
            { permission: 'service-category:save', label: this.tm('Save') },
          ],
        },
        {
          id: 'product_category',
          title: this.tm('Product_Category'),
          actions: [
            { permission: 'product-category:view', label: this.tm('View') },
            { permission: 'product-category:create', label: this.tm('Create') },
            { permission: 'product-category:edit', label: this.tm('Edit') },
            { permission: 'product-category:delete', label: this.tm('Delete') },
            { permission: 'product-category:save', label: this.tm('Save') },
          ],
        },
        {
          id: 'supplier',
          title: this.tm('Suppliers') || 'Suppliers',
          actions: [
            { permission: 'supplier:view', label: this.tm('View') },
            { permission: 'supplier:create', label: this.tm('Create') },
            { permission: 'supplier:edit', label: this.tm('Edit') },
            { permission: 'supplier:delete', label: this.tm('Delete') },
            { permission: 'supplier:save', label: this.tm('Save') },
          ],
        },
      ]
    },
    conditionMenu() {
      return {
        id: 'dental-condition',
        title: this.tm('Diagnostic_Legend') || 'Diagnostic Legend',
        actions: [
          { permission: 'dental-condition:view', label: this.tm('View') },
          { permission: 'dental-condition:create', label: this.tm('Create') },
          { permission: 'dental-condition:edit', label: this.tm('Edit') },
          { permission: 'dental-condition:delete', label: this.tm('Delete') },
          { permission: 'dental-condition:save', label: this.tm('Save') },
        ],
      }
    },
    quoteMenu() {
      return {
        id: 'quote',
        title: this.tm('Quote') || 'Quote',
        actions: [
          { permission: 'quotes:quotes:view', label: this.tm('View') },
          { permission: 'quotes:quotes:create', label: this.tm('Create') },
          { permission: 'quotes:quotes:edit', label: this.tm('Edit') },
          { permission: 'quotes:quotes:delete', label: this.tm('Delete') },
          { permission: 'quotes:quotes:view_detail', label: this.tm('View_Detail') || 'View Detail' },
          { permission: 'quotes:quotes:approve', label: this.tm('Approve') || 'Approve' },
          { permission: 'quotes:quotes:reject', label: this.tm('Reject') || 'Reject' },
          { permission: 'quotes:quotes:print', label: this.tm('Print') || 'Print' },
        ],
      }
    },
    targetMenus() {
      return [
        {
          id: 'target-clinic',
          title: this.tm('Clinic_Target') || 'Clinic Target',
          actions: [
            { permission: 'target-clinic:view', label: this.tm('View') },
            { permission: 'target-clinic:create', label: this.tm('Create') },
            { permission: 'target-clinic:edit', label: this.tm('Edit') },
            { permission: 'target-clinic:save', label: this.tm('Save') },
            { permission: 'target-clinic:delete', label: this.tm('Delete') },
          ],
        },
        {
          id: 'target-doctor',
          title: this.tm('Doctor_Target') || 'Doctor Target',
          actions: [
            { permission: 'target-doctor:view', label: this.tm('View') },
            { permission: 'target-doctor:create', label: this.tm('Create') },
            { permission: 'target-doctor:edit', label: this.tm('Edit') },
            { permission: 'target-doctor:save', label: this.tm('Save') },
            { permission: 'target-doctor:delete', label: this.tm('Delete') },
          ],
        },
      ]
    },
    invoiceMenu() {
      return {
        id: 'invoice',
        title: this.tm('Invoice') || 'Invoice',
        actions: [
          { permission: 'invoice:view', label: this.tm('View') },
          { permission: 'invoice:create', label: this.tm('Create') },
          { permission: 'invoice:edit', label: this.tm('Edit') },
          { permission: 'invoice:delete', label: this.tm('Delete') },
          { permission: 'invoice:save', label: this.tm('Save') },
          { permission: 'invoice:view-detail', label: this.tm('View_Detail') || 'View Detail' },
          { permission: 'invoice:print', label: this.tm('Print') || 'Print' },
          { permission: 'invoice:pay', label: this.tm('Pay') || 'Pay' },
        ],
      }
    },
    cashDepositMenu() {
      return {
        id: 'cash-deposit',
        title: this.tm('Cash_Deposit') || 'Cash Deposit',
        actions: [
          { permission: 'accounting:cashDeposit:view', label: this.tm('View') || 'View' },
          { permission: 'accounting:cashDeposit:create', label: this.tm('Create') || 'Create' },
          { permission: 'accounting:cashDeposit:edit', label: this.tm('Edit') || 'Edit' },
          { permission: 'accounting:cashDeposit:delete', label: this.tm('Delete') || 'Delete' },
          { permission: 'accounting:cashDeposit:print', label: this.tm('Print') || 'Print' },
        ],
      }
    },
    roleMenu() {
      return {
        id: 'role',
        title: this.tm('Roles_and_Permissions'),
        actions: [
          { permission: 'role:view', label: this.tm('View') },
          { permission: 'role:create', label: this.tm('Create') },
          { permission: 'role:edit', label: this.tm('Edit') },
          { permission: 'role:delete', label: this.tm('Delete') },
          { permission: 'role:save', label: this.tm('Save') },
        ],
      }
    },
    doctorMenu() {
      return {
        id: 'doctor',
        title: this.tm('Doctor') || 'Doctor',
        actions: [
          { permission: 'doctor:view', label: this.tm('View') },
          { permission: 'doctor:create', label: this.tm('Create') },
          { permission: 'doctor:edit', label: this.tm('Edit') },
          { permission: 'doctor:delete', label: this.tm('Delete') },
          { permission: 'doctor:save', label: this.tm('Save') },
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
          { permission: 'user:delete', label: this.tm('Delete') },
          { permission: 'user:save', label: this.tm('Save') },
        ],
      }
    },
    medicineMenu() {
      return {
        id: 'medicine',
        title: this.tm('Medicines') || 'Medicines',
        actions: [
          { permission: 'medicine:view', label: this.tm('View') },
          { permission: 'medicine:create', label: this.tm('Create') },
          { permission: 'medicine:edit', label: this.tm('Edit') },
          { permission: 'medicine:delete', label: this.tm('Delete') },
          { permission: 'medicine:save', label: this.tm('Save') },
        ],
      }
    },
    prescriptionMenu() {
      return {
        id: 'prescription',
        title: this.tm('Prescriptions') || 'Prescriptions',
        actions: [
          { permission: 'prescription:view', label: this.tm('View') },
          { permission: 'prescription:create', label: this.tm('Create') },
          { permission: 'prescription:edit', label: this.tm('Edit') },
          { permission: 'prescription:delete', label: this.tm('Delete') },
          { permission: 'prescription:save', label: this.tm('Save') },
        ],
      }
    },
    paymentMethodMenu() {
      return {
        id: 'payment-method',
        title: this.tm('Payment_Methods') || 'Payment Methods',
        actions: [
          { permission: 'payment-method:view', label: this.tm('View') },
          { permission: 'payment-method:create', label: this.tm('Create') },
          { permission: 'payment-method:edit', label: this.tm('Edit') },
          { permission: 'payment-method:delete', label: this.tm('Delete') },
          { permission: 'payment-method:save', label: this.tm('Save') },
        ],
      }
    },
    generalMenu() {
      return {
        id: 'general',
        title: this.tm('General_Settings') || 'General Settings',
        actions: [
          { permission: 'settings:general:view', label: this.tm('View') },
          { permission: 'settings:general:edit', label: this.tm('Edit') },
        ],
      }
    },
    allPermissionNames() {
      const list = ['dashboard:view', 'register:view', 'payment:view']
      this.conditionMenu.actions.forEach((a) => list.push(a.permission))
      this.hospitalMenus.forEach((m) => {
        m.actions.forEach((a) => list.push(a.permission))
      })
      this.quoteMenu.actions.forEach((a) => {
        list.push(a.permission)
        if (a.permission === 'quotes:quotes:view') list.push('quote:view')
        if (a.permission === 'quotes:quotes:create') list.push('quote:create')
        if (a.permission === 'quotes:quotes:edit') list.push('quote:edit')
        if (a.permission === 'quotes:quotes:delete') list.push('quote:delete')
        if (a.permission === 'quotes:quotes:view_detail') list.push('quote:view-detail', 'quote:view_detail')
        if (a.permission === 'quotes:quotes:approve') list.push('quote:approve')
        if (a.permission === 'quotes:quotes:reject') list.push('quote:reject')
        if (a.permission === 'quotes:quotes:print') list.push('quote:print')
      })
      this.targetMenus.forEach((m) => {
        m.actions.forEach((a) => {
          list.push(a.permission)
          if (a.permission.startsWith('target-')) {
            list.push(a.permission.replace('target-', 'targets:'))
          }
        })
      })
      this.invoiceMenu.actions.forEach((a) => {
        list.push(a.permission)
        if (a.permission === 'invoice:view-detail') list.push('invoice:view_detail')
      })
      this.cashDepositMenu.actions.forEach((a) => {
        list.push(a.permission)
        if (a.permission === 'accounting:cashDeposit:view') list.push('cash-deposit:view')
        if (a.permission === 'accounting:cashDeposit:create') list.push('cash-deposit:create', 'cash-deposit:save')
        if (a.permission === 'accounting:cashDeposit:edit') list.push('cash-deposit:edit')
        if (a.permission === 'accounting:cashDeposit:delete') list.push('cash-deposit:delete')
        if (a.permission === 'accounting:cashDeposit:print') list.push('cash-deposit:print')
      })
      this.roleMenu.actions.forEach((a) => list.push(a.permission))
      this.doctorMenu.actions.forEach((a) => list.push(a.permission))
      this.userMenu.actions.forEach((a) => list.push(a.permission))
      this.generalMenu.actions.forEach((a) => {
        list.push(a.permission)
        if (a.permission === 'settings:general:view') list.push('general:view')
        if (a.permission === 'settings:general:edit') list.push('general:edit')
      })
      this.medicineMenu.actions.forEach((a) => {
        list.push(a.permission)
        if (a.permission === 'medicine:view') list.push('medicines:medicines:view')
        if (a.permission === 'medicine:create') list.push('medicines:medicines:create')
        if (a.permission === 'medicine:edit') list.push('medicines:medicines:edit')
        if (a.permission === 'medicine:delete') list.push('medicines:medicines:delete')
      })
      this.prescriptionMenu.actions.forEach((a) => {
        list.push(a.permission)
        if (a.permission === 'prescription:view') list.push('prescriptions:prescriptions:view')
        if (a.permission === 'prescription:create') list.push('prescriptions:prescriptions:create')
        if (a.permission === 'prescription:edit') list.push('prescriptions:prescriptions:edit')
        if (a.permission === 'prescription:delete') list.push('prescriptions:prescriptions:delete')
      })
      this.paymentMethodMenu.actions.forEach((a) => list.push(a.permission))
      return list
    },
    permissionAliases() {
      return {
        'settings:general:view': 'general:view',
        'settings:general:edit': 'general:edit',
        'medicine:view': 'medicines:medicines:view',
        'medicine:create': 'medicines:medicines:create',
        'medicine:edit': 'medicines:medicines:edit',
        'medicine:delete': 'medicines:medicines:delete',
        'prescription:view': 'prescriptions:prescriptions:view',
        'prescription:create': 'prescriptions:prescriptions:create',
        'prescription:edit': 'prescriptions:prescriptions:edit',
        'prescription:delete': 'prescriptions:prescriptions:delete',
        'invoice:view-detail': 'invoice:view_detail',
        'accounting:cashDeposit:view': 'cash-deposit:view',
        'cash-deposit:view': 'accounting:cashDeposit:view',
        'accounting:cashDeposit:create': 'cash-deposit:create',
        'cash-deposit:create': 'accounting:cashDeposit:create',
        'accounting:cashDeposit:edit': 'cash-deposit:edit',
        'cash-deposit:edit': 'accounting:cashDeposit:edit',
        'accounting:cashDeposit:delete': 'cash-deposit:delete',
        'cash-deposit:delete': 'accounting:cashDeposit:delete',
        'accounting:cashDeposit:print': 'cash-deposit:print',
        'cash-deposit:print': 'accounting:cashDeposit:print',
        'quotes:quotes:view': 'quote:view',
        'quote:view': 'quotes:quotes:view',
        'quotes:quotes:create': 'quote:create',
        'quote:create': 'quotes:quotes:create',
        'quotes:quotes:edit': 'quote:edit',
        'quote:edit': 'quotes:quotes:edit',
        'quotes:quotes:delete': 'quote:delete',
        'quote:delete': 'quotes:quotes:delete',
        'quotes:quotes:view_detail': 'quote:view_detail',
        'quote:view-detail': 'quotes:quotes:view_detail',
        'quote:view_detail': 'quotes:quotes:view_detail',
        'quotes:quotes:approve': 'quote:approve',
        'quote:approve': 'quotes:quotes:approve',
        'quotes:quotes:reject': 'quote:reject',
        'quote:reject': 'quotes:quotes:reject',
        'quotes:quotes:print': 'quote:print',
        'quote:print': 'quotes:quotes:print',
        'target-clinic:view': 'targets:clinic:view',
        'targets:clinic:view': 'target-clinic:view',
        'target-clinic:create': 'targets:clinic:create',
        'targets:clinic:create': 'target-clinic:create',
        'target-clinic:edit': 'targets:clinic:edit',
        'targets:clinic:edit': 'target-clinic:edit',
        'target-clinic:save': 'targets:clinic:save',
        'targets:clinic:save': 'target-clinic:save',
        'target-clinic:delete': 'targets:clinic:delete',
        'targets:clinic:delete': 'target-clinic:delete',
        'target-doctor:view': 'targets:doctor:view',
        'targets:doctor:view': 'target-doctor:view',
        'target-doctor:create': 'targets:doctor:create',
        'targets:doctor:create': 'target-doctor:create',
        'target-doctor:edit': 'targets:doctor:edit',
        'targets:doctor:edit': 'target-doctor:edit',
        'target-doctor:save': 'targets:doctor:save',
        'targets:doctor:save': 'target-doctor:save',
        'target-doctor:delete': 'targets:doctor:delete',
        'targets:doctor:delete': 'target-doctor:delete',
      }
    },
  },
  methods: {
    hasPermission(name) {
      const aliases = this.permissionAliases
      return this.form.permissions.includes(name) || (aliases[name] && this.form.permissions.includes(aliases[name]))
    },
    togglePermission(name) {
      const aliases = this.permissionAliases
      const alias = aliases[name]
      const index = this.form.permissions.indexOf(name)
      if (index > -1) {
        this.form.permissions.splice(index, 1)
        if (alias) {
          const aIdx = this.form.permissions.indexOf(alias)
          if (aIdx > -1) this.form.permissions.splice(aIdx, 1)
        }
      } else {
        this.form.permissions.push(name)
        if (alias && !this.form.permissions.includes(alias)) {
          this.form.permissions.push(alias)
        }
      }
    },
    selectMenuAll(actions) {
      const aliases = this.permissionAliases
      actions.forEach((a) => {
        if (!this.form.permissions.includes(a.permission)) {
          this.form.permissions.push(a.permission)
        }
        const alias = aliases[a.permission]
        if (alias && !this.form.permissions.includes(alias)) {
          this.form.permissions.push(alias)
        }
      })
    },
    clearMenuAll(actions) {
      const aliases = this.permissionAliases
      const permsToClear = []
      actions.forEach((a) => {
        permsToClear.push(a.permission)
        if (aliases[a.permission]) permsToClear.push(aliases[a.permission])
      })
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
      this.form.post(route('roles.store'), {
        preserveScroll: true,
        onSuccess: () => {
          this.toast.success(this.tm('Role_created_successfully'))
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
