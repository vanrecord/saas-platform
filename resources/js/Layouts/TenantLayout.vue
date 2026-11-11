<template>
  <div class="min-h-screen bg-[#F7F9FC] text-[#152B45] flex font-sans antialiased">
    <Head :title="title ? `${title} - Dental Clinic` : 'Dental Clinic • OdontoCare'" />

    <!-- Mobile Sidebar Backdrop -->
    <div
      v-if="isMobileSidebarOpen"
      class="fixed inset-0 z-40 bg-slate-900/50 backdrop-blur-sm lg:hidden transition-opacity"
      @click="isMobileSidebarOpen = false"
    ></div>

    <!-- Left Sidebar Navigation -->
    <aside
      :class="[
        'fixed top-0 bottom-0 left-0 z-50 flex flex-col bg-[#F0F2F5] border-r border-[#DCE3EC] transition-all duration-300 ease-in-out',
        // Desktop responsive width
        isSidebarCollapsed ? 'w-20' : 'w-[260px]',
        // Mobile responsive drawer positioning
        isMobileSidebarOpen ? 'translate-x-0' : '-translate-x-full lg:translate-x-0'
      ]"
    >
      <!-- Clinic Brand Header at Top -->
      <div class="h-20 flex items-center px-4 border-b border-[#DCE3EC]/70 bg-[#F0F2F5] flex-shrink-0">
        <Link :href="route('tenant.dashboard')" class="flex items-center gap-3 w-full group">
          <!-- Stylized Tooth Icon in Circle -->
          <div class="w-10 h-10 rounded-full bg-white border border-[#0D63D6]/20 shadow-sm flex items-center justify-center flex-shrink-0 group-hover:scale-105 transition-transform">
            <svg class="w-5 h-5 text-[#0D63D6]" viewBox="0 0 24 24" fill="currentColor">
              <path d="M12 2C8.5 2 6 4.5 6 7c0 1.9.8 3.5 1.5 5.2.7 1.8 1.5 3.8 1.5 5.8 0 1.7 1.3 3 3 3s3-1.3 3-3c0-2 .8-4 1.5-5.8C17.2 10.5 18 8.9 18 7c0-2.5-2.5-5-6-5zm-1 16c-.6 0-1-.4-1-1 0-1.8-.8-3.6-1.5-5.3C7.8 10 7 8.5 7 7c0-1.9 2-3.8 5-3.8s5 1.9 5 3.8c0 1.5-.8 3-1.5 4.7-.7 1.7-1.5 3.5-1.5 5.3 0 .6-.4 1-1 1h-2z"/>
            </svg>
          </div>

          <!-- Brand Text & Khmer Subtitle -->
          <div v-show="!isSidebarCollapsed" class="flex flex-col min-w-0 leading-tight overflow-hidden">
            <span class="text-sm font-extrabold text-[#152B45] tracking-tight truncate">
              {{ $page.props.tenant?.company || 'Dental Clinic • Odont' }}
            </span>
            <span class="text-[11px] font-normal text-slate-500 truncate mt-0.5 font-khmer">
              គ្លីនិកធ្មេញ អូដុនតូខែរ
            </span>
          </div>
        </Link>
      </div>

      <!-- Scrollable Navigation Area -->
      <nav class="flex-1 overflow-y-auto px-3 py-4 space-y-1 text-sm">
        
        <div>
          <div class="space-y-1">
            <!-- Dashboard -->
            <Link
              v-if="can(['all', 'dashboard:view'])"
              :href="route('tenant.dashboard')"
              :class="[
                'flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-semibold transition-all',
                route().current('tenant.dashboard')
                  ? 'bg-[#EBF3FE] text-[#0D63D6] border-l-4 border-[#0D63D6] font-bold shadow-xs'
                  : 'text-slate-600 hover:text-[#0D63D6] hover:bg-slate-200/50'
              ]"
              :title="isSidebarCollapsed ? 'Dashboard' : ''"
            >
              <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z" />
              </svg>
              <span v-show="!isSidebarCollapsed">Dashboard</span>
            </Link>

            <!-- Register (Active in Reference Screenshot) -->
            <Link
              v-if="can(['all', 'register:view'])"
              :href="route('tenant.visit.create')"
              :class="[
                'flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-semibold transition-all',
                route().current('tenant.visit.*')
                  ? 'bg-[#EBF3FE] text-[#0D63D6] border-l-4 border-[#0D63D6] font-bold shadow-xs'
                  : 'text-slate-600 hover:text-[#0D63D6] hover:bg-slate-200/50'
              ]"
              :title="isSidebarCollapsed ? 'Register' : ''"
            >
              <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z" />
              </svg>
              <span v-show="!isSidebarCollapsed">Register</span>
            </Link>

            <!-- Patients -->
            <Link
              v-if="can(['all', 'patient:view', 'patients', 'patients.index'])"
              :href="route('patients.index')"
              :class="[
                'flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-semibold transition-all',
                route().current('patients.*')
                  ? 'bg-[#EBF3FE] text-[#0D63D6] border-l-4 border-[#0D63D6] font-bold shadow-xs'
                  : 'text-slate-600 hover:text-[#0D63D6] hover:bg-slate-200/50'
              ]"
              :title="isSidebarCollapsed ? 'Patients' : ''"
            >
              <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
              </svg>
              <span v-show="!isSidebarCollapsed">Patients</span>
            </Link>
          </div>
        </div>

        <div>
          <div class="space-y-1">
            <!-- Invoice -->
            <Link
              :href="route('invoices.index')"
              :class="[
                'flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-semibold transition-all',
                route().current('invoices.*') || route().current('tenant.invoices.*')
                  ? 'bg-[#EBF3FE] text-[#0D63D6] border-l-4 border-[#0D63D6] font-bold shadow-xs'
                  : 'text-slate-600 hover:text-[#0D63D6] hover:bg-slate-200/50'
              ]"
              :title="isSidebarCollapsed ? 'Invoice' : ''"
            >
              <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
              </svg>
              <span v-show="!isSidebarCollapsed">Invoice</span>
            </Link>

            <!-- Payment -->
            <Link
              :href="route('tenant.payments.index')"
              :class="[
                'flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-semibold transition-all',
                route().current('tenant.payments.*')
                  ? 'bg-[#EBF3FE] text-[#0D63D6] border-l-4 border-[#0D63D6] font-bold shadow-xs'
                  : 'text-slate-600 hover:text-[#0D63D6] hover:bg-slate-200/50'
              ]"
              :title="isSidebarCollapsed ? 'Payment' : ''"
            >
              <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
              </svg>
              <span v-show="!isSidebarCollapsed">Payment</span>
            </Link>
          </div>
        </div>

        <div>
          <div class="space-y-1">
            <!-- Conditions with Submenu -->
            <div>
              <button
                type="button"
                @click="clinicalOpen.conditions = !clinicalOpen.conditions"
                class="w-full flex items-center justify-between px-3 py-2.5 rounded-lg text-sm font-semibold text-slate-600 hover:text-[#0D63D6] hover:bg-slate-200/50 transition-all"
              >
                <div class="flex items-center gap-3">
                  <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21a4 4 0 01-4-4V5a2 2 0 012-2h4a2 2 0 012 2v12a4 4 0 01-4 4zm0 0h12a2 2 0 002-2v-4a2 2 0 00-2-2h-2.343M11 7.343l1.657-1.657a2 2 0 012.828 0l2.829 2.829a2 2 0 010 2.828l-8.486 8.485M7 17h.01" />
                  </svg>
                  <span v-show="!isSidebarCollapsed">Conditions</span>
                </div>
                <svg
                  v-show="!isSidebarCollapsed"
                  :class="['w-3.5 h-3.5 text-slate-400 transition-transform duration-200', clinicalOpen.conditions ? 'rotate-180' : '']"
                  fill="none"
                  stroke="currentColor"
                  viewBox="0 0 24 24"
                >
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                </svg>
              </button>

              <!-- Submenu -->
              <div v-show="!isSidebarCollapsed && clinicalOpen.conditions" class="pl-9 pr-2 py-1 space-y-1">
                <Link
                  :href="route('dental-conditions.index')"
                  :class="[
                    'block px-2.5 py-1.5 rounded-md text-sm transition-colors',
                    route().current('dental-conditions.*') ? 'text-[#0D63D6] font-bold bg-blue-50/70' : 'text-slate-500 hover:text-slate-800'
                  ]"
                >
                  Diagnostic Legend
                </Link>
              </div>
            </div>

            <!-- Hospital -->
            <div
              v-if="can([
                'all',
                'service:view', 'services', 'services.index', 'services:services:view', 'services:view',
                'service-category:view', 'service-categories', 'service_categories', 'service-categories.index', 'categories:service_categories:view', 'service_categories:view',
                'product-category:view', 'product-categories', 'product_categories', 'product-categories.index', 'categories:product_categories:view', 'product_categories:view',
                'supplier:view', 'suppliers', 'suppliers.index', 'suppliers:suppliers:view', 'suppliers:view',
                'product:view', 'products', 'products.index', 'products:products:view', 'products:view'
              ])"
            >
              <button
                type="button"
                @click="clinicalOpen.hospital = !clinicalOpen.hospital"
                class="w-full flex items-center justify-between px-3 py-2.5 rounded-lg text-sm font-semibold text-slate-600 hover:text-[#0D63D6] hover:bg-slate-200/50 transition-all"
              >
                <div class="flex items-center gap-3">
                  <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                  </svg>
                  <span v-show="!isSidebarCollapsed">Hospital</span>
                </div>
                <svg
                  v-show="!isSidebarCollapsed"
                  :class="['w-3.5 h-3.5 text-slate-400 transition-transform duration-200', clinicalOpen.hospital ? 'rotate-180' : '']"
                  fill="none"
                  stroke="currentColor"
                  viewBox="0 0 24 24"
                >
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                </svg>
              </button>

              <!-- Submenu -->
              <div v-show="!isSidebarCollapsed && clinicalOpen.hospital" class="pl-9 pr-2 py-1 space-y-1">
                <Link
                  v-if="can(['all', 'service:view', 'services', 'services.index', 'services:services:view', 'services:view'])"
                  :href="route('services.index')"
                  :class="[
                    'block px-2.5 py-1.5 rounded-md text-sm transition-colors',
                    route().current('services.*') ? 'text-[#0D63D6] font-bold bg-blue-50/70' : 'text-slate-500 hover:text-slate-800'
                  ]"
                >
                  Service
                </Link>

                <Link
                  v-if="can(['all', 'service-category:view', 'service-categories', 'service_categories', 'service-categories.index', 'categories:service_categories:view', 'service_categories:view'])"
                  :href="route('service-categories.index')"
                  :class="[
                    'block px-2.5 py-1.5 rounded-md text-sm transition-colors',
                    route().current('service-categories.*') ? 'text-[#0D63D6] font-bold bg-blue-50/70' : 'text-slate-500 hover:text-slate-800'
                  ]"
                >
                  Service Category
                </Link>

                <Link
                  v-if="can(['all', 'product-category:view', 'product-categories', 'product_categories', 'product-categories.index', 'categories:product_categories:view', 'product_categories:view'])"
                  :href="route('product-categories.index')"
                  :class="[
                    'block px-2.5 py-1.5 rounded-md text-sm transition-colors',
                    route().current('product-categories.*') ? 'text-[#0D63D6] font-bold bg-blue-50/70' : 'text-slate-500 hover:text-slate-800'
                  ]"
                >
                  Product Category
                </Link>

                <Link
                  v-if="can(['all', 'supplier:view', 'suppliers', 'suppliers.index', 'suppliers:suppliers:view', 'suppliers:view'])"
                  :href="route('suppliers.index')"
                  :class="[
                    'block px-2.5 py-1.5 rounded-md text-sm transition-colors',
                    route().current('suppliers.*') ? 'text-[#0D63D6] font-bold bg-blue-50/70' : 'text-slate-500 hover:text-slate-800'
                  ]"
                >
                  Supplier
                </Link>

                <Link
                  v-if="can(['all', 'product:view', 'products', 'products.index', 'products:products:view', 'products:view'])"
                  :href="route('products.index')"
                  :class="[
                    'block px-2.5 py-1.5 rounded-md text-sm transition-colors',
                    route().current('products.*') ? 'text-[#0D63D6] font-bold bg-blue-50/70' : 'text-slate-500 hover:text-slate-800'
                  ]"
                >
                  Product
                </Link>
              </div>
            </div>
          </div>
        </div>

        <div>
          <div class="space-y-1">
            <Link
              v-if="can(['all', 'doctor:view', 'doctors', 'doctors.index'])"
              :href="route('doctors.index')"
              :class="[
                'flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-semibold transition-all',
                route().current('doctors.*')
                  ? 'bg-[#EBF3FE] text-[#0D63D6] border-l-4 border-[#0D63D6] font-bold shadow-xs'
                  : 'text-slate-600 hover:text-[#0D63D6] hover:bg-slate-200/50'
              ]"
            >
              <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11v6m3-3h-6" />
              </svg>
              <span v-show="!isSidebarCollapsed">Doctor</span>
            </Link>

            <Link
              v-if="can(['all', 'user:view', 'users', 'users.index'])"
              :href="route('users.index')"
              :class="[
                'flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-semibold transition-all',
                route().current('users.*')
                  ? 'bg-[#EBF3FE] text-[#0D63D6] border-l-4 border-[#0D63D6] font-bold shadow-xs'
                  : 'text-slate-600 hover:text-[#0D63D6] hover:bg-slate-200/50'
              ]"
            >
              <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
              </svg>
              <span v-show="!isSidebarCollapsed">Users</span>
            </Link>

            <Link
              v-if="can(['all', 'role:view', 'roles', 'roles.index', 'role:view'])"
              :href="route('roles.index')"
              :class="[
                'flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-semibold transition-all',
                route().current('roles.*')
                  ? 'bg-[#EBF3FE] text-[#0D63D6] border-l-4 border-[#0D63D6] font-bold shadow-xs'
                  : 'text-slate-600 hover:text-[#0D63D6] hover:bg-slate-200/50'
              ]"
            >
              <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
              </svg>
              <span v-show="!isSidebarCollapsed">Roles & Permissions</span>
            </Link>
          </div>
        </div>

        <div>
          <div class="space-y-1">
            <!-- Settings with Submenu -->
            <div>
              <button
                type="button"
                @click="clinicalOpen.settings = !clinicalOpen.settings"
                class="w-full flex items-center justify-between px-3 py-2.5 rounded-lg text-sm font-semibold text-slate-600 hover:text-[#0D63D6] hover:bg-slate-200/50 transition-all"
              >
                <div class="flex items-center gap-3">
                  <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                  </svg>
                  <span v-show="!isSidebarCollapsed">Settings</span>
                </div>
                <svg
                  v-show="!isSidebarCollapsed"
                  :class="['w-3.5 h-3.5 text-slate-400 transition-transform duration-200', clinicalOpen.settings ? 'rotate-180' : '']"
                  fill="none"
                  stroke="currentColor"
                  viewBox="0 0 24 24"
                >
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                </svg>
              </button>

              <!-- Submenu -->
              <div v-show="!isSidebarCollapsed && clinicalOpen.settings" class="pl-9 pr-2 py-1 space-y-1">
                <Link
                  :href="route('settings.invoice-prefixes.index')"
                  :class="[
                    'block px-2.5 py-1.5 rounded-md text-sm transition-colors',
                    route().current('settings.invoice-prefixes.*') ? 'text-[#0D63D6] font-bold bg-blue-50/70' : 'text-slate-500 hover:text-slate-800'
                  ]"
                >
                  New Prefix Invoice
                </Link>
                <Link
                  v-if="can(['all', 'payment-method:view', 'payment-method:create', 'payment-method:edit'])"
                  :href="route('settings.payment-methods.index')"
                  :class="[
                    'block px-2.5 py-1.5 rounded-md text-sm transition-colors',
                    route().current('settings.payment-methods.*') ? 'text-[#0D63D6] font-bold bg-blue-50/70' : 'text-slate-500 hover:text-slate-800'
                  ]"
                >
                  {{ tm('Payment_Method') || 'Payment Method' }}
                </Link>
              </div>
            </div>

            <a
              href="#"
              class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-semibold text-slate-600 hover:text-[#0D63D6] hover:bg-slate-200/50 transition-all"
            >
              <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
              </svg>
              <span v-show="!isSidebarCollapsed">API Health</span>
            </a>
          </div>
        </div>

      </nav>

      <!-- Bottom Profile Badge & Sign Out Button -->
      <div class="p-3 border-t border-[#DCE3EC] bg-white flex-shrink-0">
        <div class="flex items-center gap-3 px-1 py-1">
          <!-- Initial Avatar -->
          <div class="w-9 h-9 rounded-full bg-[#E2E8F0] text-[#152B45] font-black text-xs flex items-center justify-center flex-shrink-0 shadow-xs">
            {{ userInitials }}
          </div>
          <div v-show="!isSidebarCollapsed" class="flex flex-col min-w-0 flex-1 leading-tight overflow-hidden">
            <span class="text-xs font-bold text-[#152B45] truncate">
              {{ $page.props.auth?.user?.name || 'Super Administrator' }}
            </span>
            <span class="text-[11px] text-slate-500 truncate mt-0.5">
              Dental Surgeon
            </span>
          </div>
        </div>

        <!-- Sign Out Button -->
        <button
          v-show="!isSidebarCollapsed"
          type="button"
          @click="logout"
          class="w-full mt-2.5 flex items-center justify-center gap-1.5 py-1.5 px-3 rounded-lg border border-red-200 text-red-600 hover:bg-red-50 text-xs font-bold transition-colors shadow-xs"
        >
          <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
          </svg>
          <span>Sign Out</span>
        </button>
      </div>
    </aside>

    <!-- Main Content Area -->
    <div
      :class="[
        'flex-1 flex flex-col min-w-0 transition-all duration-300 ease-in-out',
        isSidebarCollapsed ? 'lg:pl-20' : 'lg:pl-[260px]'
      ]"
    >
      <!-- Top Header / Navbar -->
      <header class="sticky top-0 z-30 h-16 bg-white border-b border-[#DCE3EC] flex items-center justify-between px-4 sm:px-6 lg:px-8 shadow-xs">
        
        <!-- Left: Hamburger Toggle & Breadcrumb -->
        <div class="flex items-center gap-4">
          <button
            type="button"
            @click="toggleSidebar"
            class="p-2 rounded-lg border border-slate-200 text-slate-600 hover:bg-slate-50 focus:outline-none transition shadow-xs"
            aria-label="Toggle navigation"
          >
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
            </svg>
          </button>

          <!-- Breadcrumb -->
          <nav class="flex items-center text-xs font-medium text-slate-500" aria-label="Breadcrumb">
            <Link :href="route('tenant.dashboard')" class="hover:text-[#0D63D6] transition-colors">
              OdontoCare
            </Link>
            <span class="mx-2 text-slate-400">/</span>
            <span class="text-[#152B45] font-bold truncate">
              {{ title || 'Patient Visit Registration' }}
            </span>
          </nav>
        </div>

        <!-- Right Side: Action Buttons & User Menu -->
        <div class="flex items-center gap-2.5 sm:gap-3">
          <!-- Vertical Divider Line -->
          <div class="h-6 w-px bg-slate-200 mx-1"></div>

          <!-- User Status Dropdown -->
          <div class="relative">
            <button
              type="button"
              @click="isUserMenuOpen = !isUserMenuOpen"
              class="flex items-center gap-2 pl-1 pr-2 py-1 rounded-lg hover:bg-slate-50 transition focus:outline-none"
            >
              <!-- Avatar Circle -->
              <div class="w-7 h-7 rounded-full bg-[#E2E8F0] text-[#152B45] font-black text-xs flex items-center justify-center flex-shrink-0">
                {{ userInitials }}
              </div>

              <!-- Name & Online Status Indicator -->
              <span class="text-xs font-bold text-[#152B45] hidden sm:inline-block">
                {{ $page.props.auth?.user?.name || 'Super Administrator' }}
              </span>

              <!-- Green Online Dot -->
              <span class="w-2 h-2 rounded-full bg-emerald-500 ring-2 ring-emerald-100 flex-shrink-0"></span>

              <!-- Dropdown Chevron -->
              <svg class="w-3.5 h-3.5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
              </svg>
            </button>

            <!-- Dropdown Menu -->
            <transition
              enter-active-class="transition duration-100 ease-out"
              enter-from-class="transform scale-95 opacity-0"
              enter-to-class="transform scale-100 opacity-100"
              leave-active-class="transition duration-75 ease-in"
              leave-from-class="transform scale-100 opacity-100"
              leave-to-class="transform scale-95 opacity-0"
            >
              <div
                v-if="isUserMenuOpen"
                class="absolute right-0 mt-2 w-48 rounded-xl bg-white shadow-xl border border-slate-100 py-1.5 z-50 text-xs"
                @click="isUserMenuOpen = false"
              >
                <div class="px-4 py-2 border-b border-slate-100 font-bold text-slate-800">
                  {{ $page.props.auth?.user?.name }}
                  <span class="block text-[10px] text-slate-400 font-normal">Online (Tenant User)</span>
                </div>
                <Link :href="route('tenant.dashboard')" class="block px-4 py-2 text-slate-700 hover:bg-slate-50">
                  Dashboard
                </Link>
                <Link :href="route('dental.index')" class="block px-4 py-2 text-slate-700 hover:bg-slate-50">
                  Odontogram
                </Link>
                <div class="border-t border-slate-100 my-1"></div>
                <button
                  type="button"
                  @click="logout"
                  class="w-full text-left px-4 py-2 text-red-600 hover:bg-red-50 font-semibold"
                >
                  Log Out
                </button>
              </div>
            </transition>
          </div>

        </div>

      </header>

      <!-- Flash Notification Alerts -->
      <div v-if="$page.props.flash?.success" class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mt-4 w-full">
        <div class="bg-emerald-50 border border-emerald-200 text-emerald-800 px-4 py-3 rounded-xl text-xs sm:text-sm flex items-center justify-between shadow-xs">
          <div class="flex items-center gap-2">
            <svg class="w-4 h-4 text-emerald-600 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
              <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
            </svg>
            <span class="font-medium">{{ $page.props.flash.success }}</span>
          </div>
        </div>
      </div>

      <!-- Page Content Body -->
      <main class="flex-1 p-4 sm:p-6 lg:p-8">
        <slot></slot>
      </main>

    </div>

    <!-- Quick Create Patient Modal -->
    <QuickCreatePatientModal
      :show="isQuickCreateModalOpen"
      @close="isQuickCreateModalOpen = false"
      @created="onPatientCreated"
    />
  </div>
</template>

<script setup>
import { ref, computed, reactive } from 'vue'
import { Head, Link } from '@inertiajs/inertia-vue3'
import { Inertia } from '@inertiajs/inertia'
import { useI18n } from 'vue-i18n'
import { lang } from '@/Lang'
import QuickCreatePatientModal from '@/Components/Tenant/QuickCreatePatientModal.vue'

const { tm } = useI18n({
  inheritLocale: true,
  messages: lang,
})

const props = defineProps({
  title: {
    type: String,
    default: ''
  }
})

const emit = defineEmits(['patient-created'])

const isSidebarCollapsed = ref(false)
const isMobileSidebarOpen = ref(false)
const isUserMenuOpen = ref(false)
const isQuickCreateModalOpen = ref(false)

const clinicalOpen = reactive({
  conditions: Boolean(route().current('dental-conditions.*')),
  hospital: Boolean(route().current('services.*') || route().current('suppliers.*') || route().current('service-categories.*') || route().current('product-categories.*') || route().current('products.*')),
  settings: Boolean(route().current('settings.*'))
})

Inertia.on('navigate', () => {
  if (route().current('dental-conditions.*')) {
    clinicalOpen.conditions = true
  }
  if (route().current('services.*') || route().current('suppliers.*') || route().current('service-categories.*') || route().current('product-categories.*') || route().current('products.*')) {
    clinicalOpen.hospital = true
  }
  if (route().current('settings.*')) {
    clinicalOpen.settings = true
  }
})

const userInitials = computed(() => {
  const name = Inertia.page?.props?.auth?.user?.name || 'Super Administrator'
  const parts = name.trim().split(' ')
  if (parts.length >= 2) {
    return (parts[0][0] + parts[1][0]).toUpperCase()
  }
  return name.slice(0, 2).toUpperCase()
})

function toggleSidebar() {
  if (window.innerWidth < 1024) {
    isMobileSidebarOpen.value = !isMobileSidebarOpen.value
  } else {
    isSidebarCollapsed.value = !isSidebarCollapsed.value
  }
}

function onPatientCreated(patient) {
  emit('patient-created', patient)
}

function logout() {
  Inertia.post(route('logout'))
}
</script>

<style scoped>
@import url('https://fonts.googleapis.com/css2?family=Kantumruy+Pro:wght@400;500;600;700&display=swap');

.font-khmer {
  font-family: 'Kantumruy Pro', sans-serif;
}
</style>
