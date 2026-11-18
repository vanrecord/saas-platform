<template>
  <TenantLayout title="General Settings">
    <template #header>
      <div class="flex items-center justify-between">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
          {{ tm('General') || 'General Settings' }}
        </h2>
      </div>
    </template>

    <div class="py-6 px-4 sm:px-6 lg:px-8 max-w-7xl mx-auto space-y-6">
      <!-- Breadcrumb -->
      <nav class="flex text-sm text-gray-500 dark:text-gray-400" aria-label="Breadcrumb">
        <ol class="inline-flex items-center space-x-1 md:space-x-2">
          <li>
            <Link :href="route('tenant.dashboard')" class="hover:text-primary">
              {{ tm('Dashboard') || 'Dashboard' }}
            </Link>
          </li>
          <li>
            <div class="flex items-center">
              <svg class="w-4 h-4 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path>
              </svg>
              <span class="ml-1 md:ml-2">{{ tm('Settings') || 'Settings' }}</span>
            </div>
          </li>
          <li aria-current="page">
            <div class="flex items-center">
              <svg class="w-4 h-4 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path>
              </svg>
              <span class="ml-1 md:ml-2 text-gray-700 dark:text-gray-200 font-semibold">{{ tm('General') || 'General' }}</span>
            </div>
          </li>
        </ol>
      </nav>

      <!-- Main Container Card -->
      <div class="bg-white dark:bg-darker rounded-xl shadow-xs border border-gray-200 dark:border-gray-700 overflow-hidden">
        <!-- Navigation Tabs -->
        <div class="border-b border-gray-200 dark:border-gray-700 bg-gray-50/70 dark:bg-dark/60 px-4 sm:px-6">
          <nav class="-mb-px flex space-x-6 sm:space-x-8" aria-label="Tabs">
            <button
              type="button"
              @click="activeTab = 'clinic'"
              :class="[
                activeTab === 'clinic'
                  ? 'border-primary text-primary font-bold'
                  : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 dark:text-gray-400 dark:hover:text-gray-200',
                'whitespace-nowrap py-4 px-1 border-b-2 text-sm transition-colors flex items-center gap-2 cursor-pointer'
              ]"
            >
              <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
              </svg>
              <span>{{ tm('Clinic_Info') || 'Clinic Info' }}</span>
            </button>

            <button
              type="button"
              @click="activeTab = 'income'"
              :class="[
                activeTab === 'income'
                  ? 'border-primary text-primary font-bold'
                  : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 dark:text-gray-400 dark:hover:text-gray-200',
                'whitespace-nowrap py-4 px-1 border-b-2 text-sm transition-colors flex items-center gap-2 cursor-pointer'
              ]"
            >
              <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
              </svg>
              <span>{{ tm('Except_Income_Doctor') || 'Except Income Doctor' }}</span>
              <span
                v-if="doctorExceptForm.doctor_except.length > 0"
                class="ml-1.5 px-2 py-0.5 text-xs font-semibold rounded-full bg-blue-100 text-primary dark:bg-blue-900/40 dark:text-blue-300"
              >
                {{ doctorExceptForm.doctor_except.length }}
              </span>
            </button>
          </nav>
        </div>

        <!-- ============================================== -->
        <!-- TAB 1: CLINIC INFO                             -->
        <!-- ============================================== -->
        <div v-show="activeTab === 'clinic'" class="p-6">
          <form @submit.prevent="submitClinicInfo">
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">
              <!-- Left Column: Logo (col-span-4 or col-span-3) -->
              <div class="lg:col-span-4 xl:col-span-3 flex flex-col items-center">
                <div class="w-full max-w-xs space-y-3">
                  <label class="block text-sm font-semibold text-gray-700 dark:text-gray-200">
                    {{ tm('Logo') || 'Logo' }}
                  </label>

                  <!-- Logo Box Preview -->
                  <div
                    class="relative w-full aspect-square bg-gray-50 dark:bg-gray-800/60 border-2 border-dashed border-gray-300 dark:border-gray-600 rounded-xl overflow-hidden flex flex-col items-center justify-center p-3 group transition hover:border-primary shadow-2xs"
                  >
                    <!-- Current / Local Preview Image -->
                    <img
                      v-if="logoPreviewUrl"
                      :src="logoPreviewUrl"
                      alt="Clinic Logo Preview"
                      class="w-full h-full object-contain transition-transform duration-200 group-hover:scale-105"
                    />

                    <!-- Placeholder if No Logo -->
                    <div v-else class="text-center p-4">
                      <svg class="mx-auto h-12 w-12 text-gray-400 dark:text-gray-500" stroke="currentColor" fill="none" viewBox="0 0 48 48">
                        <path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                      </svg>
                      <p class="mt-2 text-xs font-medium text-gray-500 dark:text-gray-400">
                        {{ tm('No_Logo_Uploaded') || 'No logo uploaded' }}
                      </p>
                    </div>

                    <!-- Overlay Action Badge -->
                    <div
                      v-if="logoPreviewUrl"
                      class="absolute top-2 right-2 flex items-center gap-1.5 opacity-90 group-hover:opacity-100 transition"
                    >
                      <button
                        type="button"
                        @click="removeLogo"
                        class="p-1.5 rounded-full bg-red-600 text-white hover:bg-red-700 shadow-sm transition"
                        title="Remove Logo"
                      >
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                        </svg>
                      </button>
                    </div>
                  </div>

                  <!-- Upload Trigger Controls -->
                  <div class="flex items-center gap-2">
                    <input
                      ref="fileInputRef"
                      type="file"
                      accept="image/*"
                      class="hidden"
                      @change="handleLogoChange"
                    />
                    <button
                      type="button"
                      @click="triggerFileInput"
                      class="flex-1 inline-flex justify-center items-center gap-2 px-3 py-2 border border-gray-300 dark:border-gray-600 shadow-2xs text-xs font-semibold rounded-lg text-gray-700 dark:text-gray-200 bg-white dark:bg-gray-800 hover:bg-gray-50 dark:hover:bg-gray-700 transition cursor-pointer"
                    >
                      <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12" />
                      </svg>
                      <span>{{ logoPreviewUrl ? (tm('Replace_Logo') || 'Replace Logo') : (tm('Upload_Logo') || 'Upload Logo') }}</span>
                    </button>

                    <button
                      v-if="logoPreviewUrl"
                      type="button"
                      @click="removeLogo"
                      class="px-3 py-2 border border-red-200 text-red-600 hover:bg-red-50 dark:border-red-900/50 dark:hover:bg-red-950/30 text-xs font-semibold rounded-lg transition"
                      title="Clear Logo"
                    >
                      ✕
                    </button>
                  </div>

                  <p class="text-[11px] text-gray-500 dark:text-gray-400 text-center leading-normal">
                    {{ tm('Logo_Hint') || 'Allowed format: PNG, JPG, WEBP, SVG. Max size: 2MB.' }}
                  </p>
                </div>
              </div>

              <!-- Right Column: Form Fields (col-span-8 or col-span-9) -->
              <div class="lg:col-span-8 xl:col-span-9 space-y-5">
                <!-- 1. Khmer Title & Color -->
                <div>
                  <label class="block text-xs font-semibold text-gray-700 dark:text-gray-300 mb-1">
                    {{ tm('Khmer_Title') || 'Khmer Title' }}
                  </label>
                  <div class="flex items-center gap-3">
                    <input
                      v-model="clinicForm.titlekh"
                      type="text"
                      class="flex-1 rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-100 shadow-2xs text-sm focus:ring-primary focus:border-primary"
                    />
                    <div class="flex items-center gap-2 shrink-0">
                      <span class="text-xs font-medium text-gray-500 dark:text-gray-400">{{ tm('Color') || 'Color' }}</span>
                      <div class="flex items-center rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 p-1 shadow-2xs">
                        <input
                          v-model="clinicForm.color_title_kh"
                          type="text"
                          class="w-20 border-0 p-0 text-xs font-mono text-gray-700 dark:text-gray-200 focus:ring-0"
                          maxlength="7"
                        />
                        <input
                          v-model="clinicForm.color_title_kh"
                          type="color"
                          class="w-6 h-6 rounded cursor-pointer border-0 p-0 bg-transparent"
                        />
                      </div>
                    </div>
                  </div>
                </div>

                <!-- 2. English Title & Color -->
                <div>
                  <label class="block text-xs font-semibold text-gray-700 dark:text-gray-300 mb-1">
                    {{ tm('English_Title') || 'English Title' }}
                  </label>
                  <div class="flex items-center gap-3">
                    <input
                      v-model="clinicForm.title"
                      type="text"
                      class="flex-1 rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-100 shadow-2xs text-sm focus:ring-primary focus:border-primary"
                    />
                    <div class="flex items-center gap-2 shrink-0">
                      <span class="text-xs font-medium text-gray-500 dark:text-gray-400">{{ tm('Color') || 'Color' }}</span>
                      <div class="flex items-center rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 p-1 shadow-2xs">
                        <input
                          v-model="clinicForm.color_title_en"
                          type="text"
                          class="w-20 border-0 p-0 text-xs font-mono text-gray-700 dark:text-gray-200 focus:ring-0"
                          maxlength="7"
                        />
                        <input
                          v-model="clinicForm.color_title_en"
                          type="color"
                          class="w-6 h-6 rounded cursor-pointer border-0 p-0 bg-transparent"
                        />
                      </div>
                    </div>
                  </div>
                </div>

                <!-- 3. Description & Color -->
                <div>
                  <label class="block text-xs font-semibold text-gray-700 dark:text-gray-300 mb-1">
                    {{ tm('Description') || 'Description' }}
                  </label>
                  <div class="flex items-center gap-3">
                    <input
                      v-model="clinicForm.description"
                      type="text"
                      class="flex-1 rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-100 shadow-2xs text-sm focus:ring-primary focus:border-primary"
                    />
                    <div class="flex items-center gap-2 shrink-0">
                      <span class="text-xs font-medium text-gray-500 dark:text-gray-400">{{ tm('Color') || 'Color' }}</span>
                      <div class="flex items-center rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 p-1 shadow-2xs">
                        <input
                          v-model="clinicForm.color_description"
                          type="text"
                          class="w-20 border-0 p-0 text-xs font-mono text-gray-700 dark:text-gray-200 focus:ring-0"
                          maxlength="7"
                        />
                        <input
                          v-model="clinicForm.color_description"
                          type="color"
                          class="w-6 h-6 rounded cursor-pointer border-0 p-0 bg-transparent"
                        />
                      </div>
                    </div>
                  </div>
                </div>

                <!-- 4. Facebook Account -->
                <div>
                  <label class="block text-xs font-semibold text-gray-700 dark:text-gray-300 mb-1">
                    {{ tm('Facebook_Account') || 'Facebook Account' }}
                  </label>
                  <input
                    v-model="clinicForm.fbuser"
                    type="text"
                    class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-100 shadow-2xs text-sm focus:ring-primary focus:border-primary"
                  />
                </div>

                <!-- 5. Facebook Page -->
                <div>
                  <label class="block text-xs font-semibold text-gray-700 dark:text-gray-300 mb-1">
                    {{ tm('Facebook_Page') || 'Facebook Page' }}
                  </label>
                  <input
                    v-model="clinicForm.fbpage"
                    type="text"
                    class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-100 shadow-2xs text-sm focus:ring-primary focus:border-primary"
                  />
                </div>

                <!-- 6. Email -->
                <div>
                  <label class="block text-xs font-semibold text-gray-700 dark:text-gray-300 mb-1">
                    {{ tm('Email') || 'Email' }}
                  </label>
                  <input
                    v-model="clinicForm.email"
                    type="email"
                    class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-100 shadow-2xs text-sm focus:ring-primary focus:border-primary"
                  />
                </div>

                <!-- 7. Phone (+855) -->
                <div>
                  <label class="block text-xs font-semibold text-gray-700 dark:text-gray-300 mb-1">
                    {{ tm('Phone') || 'Phone (+855)' }}
                  </label>
                  <input
                    v-model="clinicForm.phone"
                    type="text"
                    class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-100 shadow-2xs text-sm focus:ring-primary focus:border-primary"
                  />
                </div>

                <!-- 8. Address (Rich Text) -->
                <div>
                  <label class="block text-xs font-semibold text-gray-700 dark:text-gray-300 mb-1.5">
                    {{ tm('Address') || 'Address' }}
                  </label>
                  <RichTextEditor
                    v-model="clinicForm.address"
                  />
                </div>

                <!-- 9. Note (Rich Text) -->
                <div>
                  <label class="block text-xs font-semibold text-gray-700 dark:text-gray-300 mb-1.5">
                    {{ tm('Note') || 'Note' }}
                  </label>
                  <RichTextEditor
                    v-model="clinicForm.note"
                  />
                </div>

                <!-- 10. Term (Rich Text) -->
                <div>
                  <label class="block text-xs font-semibold text-gray-700 dark:text-gray-300 mb-1.5">
                    {{ tm('Term') || 'Term' }}
                  </label>
                  <RichTextEditor
                    v-model="clinicForm.term"
                  />
                </div>
              </div>
            </div>

            <!-- Footer Buttons -->
            <div class="mt-8 pt-5 border-t border-gray-200 dark:border-gray-700 flex items-center justify-end gap-3">
              <Link
                :href="route('tenant.dashboard')"
                class="px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg text-sm font-semibold text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-800 transition"
              >
                {{ tm('Cancel') || 'Cancel' }}
              </Link>

              <SaveButton :loading="clinicForm.processing">
                {{ tm('Save') || 'Save' }}
              </SaveButton>
            </div>
          </form>
        </div>

        <!-- ============================================== -->
        <!-- TAB 2: EXCEPT INCOME DOCTOR                     -->
        <!-- ============================================== -->
        <div v-show="activeTab === 'income'" class="p-6">
          <form @submit.prevent="submitDoctorExcept" class="space-y-6">
            <!-- Informational Banner -->
            <div class="p-4 rounded-xl bg-blue-50/80 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800/50 flex items-start gap-3">
              <svg class="w-5 h-5 text-blue-600 dark:text-blue-400 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
              </svg>
              <div class="text-xs text-blue-900 dark:text-blue-200 leading-relaxed">
                <p class="font-bold text-sm mb-0.5">
                  {{ tm('Except_Income_Doctor_Info') || 'Exclude Doctors from Income Reports' }}
                </p>
                <p>
                  {{ tm('Except_Income_Doctor_Desc') || 'Select the doctors who should be excluded from clinic revenue, commission, and income calculation reports. Checked doctors will not have their earnings aggregated in clinic income totals.' }}
                </p>
              </div>
            </div>

            <!-- Search and Multi-select Actions Bar -->
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 pb-3 border-b border-gray-100 dark:border-gray-700/60">
              <div class="relative w-full sm:w-72">
                <input
                  v-model="doctorSearch"
                  type="text"
                  class="w-full pl-9 pr-3 py-1.5 text-xs rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-100 focus:ring-primary focus:border-primary shadow-2xs"
                  placeholder="Search doctor name or specialty..."
                />
                <svg class="w-4 h-4 text-gray-400 absolute left-2.5 top-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                </svg>
              </div>

              <div class="flex items-center gap-3 text-xs">
                <span class="text-gray-500 dark:text-gray-400">
                  <strong class="text-gray-800 dark:text-gray-200">{{ doctorExceptForm.doctor_except.length }}</strong> of {{ doctors.length }} excluded
                </span>
                <span class="text-gray-300 dark:text-gray-600">|</span>
                <button
                  type="button"
                  @click="selectAllDoctors"
                  class="text-primary hover:underline font-semibold"
                >
                  {{ tm('Select_All') || 'Select All' }}
                </button>
                <span class="text-gray-300 dark:text-gray-600">|</span>
                <button
                  type="button"
                  @click="clearAllDoctors"
                  class="text-gray-500 hover:text-gray-700 dark:hover:text-gray-300 font-semibold"
                >
                  {{ tm('Clear_All') || 'Clear All' }}
                </button>
              </div>
            </div>

            <!-- Doctor List / Grid -->
            <div v-if="filteredDoctors.length > 0" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-3">
              <label
                v-for="doctor in filteredDoctors"
                :key="doctor.id"
                class="flex items-center gap-3 p-3 rounded-xl border cursor-pointer transition select-none"
                :class="isDoctorExcepted(doctor.id)
                  ? 'bg-blue-50/70 border-primary/50 text-primary dark:bg-blue-900/20 dark:border-blue-700 dark:text-blue-200 shadow-2xs'
                  : 'bg-white dark:bg-gray-800/80 border-gray-200 dark:border-gray-700 text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700/60 hover:border-gray-300'"
              >
                <input
                  type="checkbox"
                  class="w-4 h-4 rounded border-gray-300 text-primary focus:ring-primary dark:border-gray-600 dark:bg-gray-700 dark:ring-offset-gray-800 focus:ring-2 cursor-pointer"
                  :checked="isDoctorExcepted(doctor.id)"
                  @change="toggleDoctorExcept(doctor.id)"
                />

                <!-- Doctor Avatar / Initials -->
                <div class="w-9 h-9 rounded-full bg-blue-100 dark:bg-blue-900/50 text-blue-700 dark:text-blue-300 flex items-center justify-center font-bold text-xs shrink-0 overflow-hidden">
                  <img
                    v-if="doctor.avatar_url"
                    :src="doctor.avatar_url"
                    :alt="doctor.name"
                    class="w-full h-full object-cover"
                  />
                  <span v-else>{{ getInitials(doctor.name) }}</span>
                </div>

                <!-- Doctor Details -->
                <div class="flex-1 min-w-0">
                  <div class="text-xs font-semibold truncate text-gray-800 dark:text-gray-100">
                    {{ doctor.name }}
                  </div>
                  <div class="text-[11px] text-gray-500 dark:text-gray-400 truncate">
                    {{ doctor.specialty || 'General Practitioner' }}
                  </div>
                </div>

                <span
                  v-if="isDoctorExcepted(doctor.id)"
                  class="text-[10px] font-bold px-2 py-0.5 rounded-full bg-blue-200/80 text-blue-800 dark:bg-blue-800 dark:text-blue-100 uppercase tracking-wider shrink-0"
                >
                  Excluded
                </span>
              </label>
            </div>

            <!-- Empty State -->
            <div v-else class="text-center py-12 text-gray-500 dark:text-gray-400">
              <svg class="mx-auto h-12 w-12 text-gray-400 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
              </svg>
              <p class="text-sm font-medium">No doctors found matching your search.</p>
            </div>

            <!-- Footer Save Actions -->
            <div class="pt-5 border-t border-gray-200 dark:border-gray-700 flex items-center justify-end gap-3">
              <Link
                :href="route('tenant.dashboard')"
                class="px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg text-sm font-semibold text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-800 transition"
              >
                {{ tm('Cancel') || 'Cancel' }}
              </Link>

              <SaveButton :loading="doctorExceptForm.processing">
                {{ tm('Save') || 'Save' }}
              </SaveButton>
            </div>
          </form>
        </div>
      </div>
    </div>
  </TenantLayout>
</template>

<script>
import { defineComponent } from 'vue';
import { Head, Link } from '@inertiajs/inertia-vue3';
import TenantLayout from '@/Layouts/TenantLayout.vue';
import SaveButton from '@/Components/Button/Save.vue';
import RichTextEditor from '@/Components/Form/RichTextEditor.vue';
import { useI18n } from 'vue-i18n';
import { lang } from '@/Lang';
import { useToast } from 'vue-toastification';

export default defineComponent({
  name: 'GeneralSettingIndex',
  components: {
    TenantLayout,
    Head,
    Link,
    SaveButton,
    RichTextEditor,
  },
  props: {
    clinicInfo: {
      type: Object,
      required: true,
    },
    doctors: {
      type: Array,
      default: () => [],
    },
    doctorExcept: {
      type: Array,
      default: () => [],
    },
    initialTab: {
      type: String,
      default: 'clinic',
    },
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
    return {
      activeTab: this.initialTab || 'clinic',
      logoPreviewUrl: this.clinicInfo?.logo_url || null,
      logoFile: null,
      removeLogoFlag: false,
      doctorSearch: '',
      clinicForm: this.$inertia.form({
        titlekh: this.clinicInfo?.titlekh || '',
        title: this.clinicInfo?.title || '',
        description: this.clinicInfo?.description || '',
        color_title_kh: this.clinicInfo?.color_title_kh || '#000000',
        color_title_en: this.clinicInfo?.color_title_en || '#000000',
        color_description: this.clinicInfo?.color_description || '#000000',
        fbuser: this.clinicInfo?.fbuser || '',
        fbpage: this.clinicInfo?.fbpage || '',
        email: this.clinicInfo?.email || '',
        phone: this.clinicInfo?.phone || '',
        address: this.clinicInfo?.address || '',
        note: this.clinicInfo?.note || '',
        term: this.clinicInfo?.term || '',
        logo: null,
        remove_logo: false,
      }),
      doctorExceptForm: this.$inertia.form({
        doctor_except: [...(this.doctorExcept || [])],
      }),
    };
  },
  computed: {
    filteredDoctors() {
      if (!this.doctorSearch) {
        return this.doctors;
      }
      const q = this.doctorSearch.toLowerCase().trim();
      return this.doctors.filter((doc) => {
        return (
          (doc.name && doc.name.toLowerCase().includes(q)) ||
          (doc.specialty && doc.specialty.toLowerCase().includes(q))
        );
      });
    },
  },
  watch: {
    activeTab(newTab) {
      // Update URL query parameter without re-rendering or reloading
      const url = new URL(window.location.href);
      url.searchParams.set('tab', newTab);
      window.history.replaceState({}, '', url.toString());
    },
    'clinicInfo.logo_url'(newVal) {
      if (!this.logoFile) {
        this.logoPreviewUrl = newVal;
      }
    },
  },
  methods: {
    triggerFileInput() {
      this.$refs.fileInputRef?.click();
    },
    handleLogoChange(event) {
      const file = event.target.files?.[0];
      if (!file) return;

      // Validate size (2MB)
      if (file.size > 2 * 1024 * 1024) {
        this.toast.error('Logo image must be smaller than 2MB.');
        return;
      }

      this.logoFile = file;
      this.removeLogoFlag = false;
      this.clinicForm.logo = file;
      this.clinicForm.remove_logo = false;

      // Create preview
      this.logoPreviewUrl = URL.createObjectURL(file);
    },
    removeLogo() {
      this.logoFile = null;
      this.logoPreviewUrl = null;
      this.removeLogoFlag = true;
      this.clinicForm.logo = null;
      this.clinicForm.remove_logo = true;
      if (this.$refs.fileInputRef) {
        this.$refs.fileInputRef.value = '';
      }
    },
    submitClinicInfo() {
      this.clinicForm.post(route('settings.general.update-clinic'), {
        preserveScroll: true,
        forceFormData: true,
        onSuccess: () => {
          this.toast.success(this.tm('Saved_Successfully') || 'Clinic info saved successfully.');
          this.logoFile = null;
        },
        onError: (errors) => {
          const firstErr = Object.values(errors)[0];
          this.toast.error(firstErr || 'Failed to save clinic information.');
        },
      });
    },
    isDoctorExcepted(doctorId) {
      return this.doctorExceptForm.doctor_except.includes(Number(doctorId));
    },
    toggleDoctorExcept(doctorId) {
      const id = Number(doctorId);
      const index = this.doctorExceptForm.doctor_except.indexOf(id);
      if (index === -1) {
        this.doctorExceptForm.doctor_except.push(id);
      } else {
        this.doctorExceptForm.doctor_except.splice(index, 1);
      }
    },
    selectAllDoctors() {
      this.doctorExceptForm.doctor_except = this.doctors.map((d) => Number(d.id));
    },
    clearAllDoctors() {
      this.doctorExceptForm.doctor_except = [];
    },
    submitDoctorExcept() {
      this.doctorExceptForm.post(route('settings.general.update-doctor-except'), {
        preserveScroll: true,
        onSuccess: () => {
          this.toast.success(this.tm('Saved_Successfully') || 'Doctor exclusions saved successfully.');
        },
        onError: (errors) => {
          const firstErr = Object.values(errors)[0];
          this.toast.error(firstErr || 'Failed to save doctor exclusions.');
        },
      });
    },
    getInitials(name) {
      if (!name) return 'DR';
      const parts = name.trim().split(/\s+/);
      if (parts.length === 1) return parts[0].substring(0, 2).toUpperCase();
      return (parts[0][0] + parts[parts.length - 1][0]).toUpperCase();
    },
  },
});
</script>
