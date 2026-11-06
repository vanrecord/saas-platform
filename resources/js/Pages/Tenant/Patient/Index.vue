<template>
  <TenantLayout>
    <Head title="Patients Directory" />

    <div class="max-w-7xl mx-auto space-y-4">
      <!-- Breadcrumb & Top Bar -->
      <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 pb-2">
        <div>
          <h1 class="text-2xl font-black text-slate-900 tracking-tight">Patients Directory</h1>
          <p class="text-xs text-slate-500 mt-0.5">Manage patient records, identification, profiles, and clinical records</p>
        </div>

        <div class="flex items-center gap-2.5">
          <!-- e-Health Green Button -->
          <button
            type="button"
            @click="showEHealthModal = true"
            class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded bg-[#00a65a] hover:bg-[#008d4c] text-white text-xs font-semibold shadow-sm transition-colors"
          >
            <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
              <rect x="3" y="3" width="7" height="7"></rect>
              <rect x="14" y="3" width="7" height="7"></rect>
              <rect x="3" y="14" width="7" height="7"></rect>
              <rect x="14" y="14" width="7" height="7"></rect>
              <path d="M7 7h.01M17 7h.01M7 17h.01M17 17h.01"></path>
            </svg>
            <span>e-Health</span>
          </button>

          <!-- Create Patient Button -->
          <Link
            :href="route('patients.create')"
            class="inline-flex items-center gap-1.5 px-4 py-2 rounded text-xs font-bold text-white bg-[#2457c5] hover:bg-[#1d48a3] shadow-sm transition-colors"
          >
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
            </svg>
            <span>+ Create Patient</span>
          </Link>
        </div>
      </div>

      <!-- Search & Controls Bar -->
      <div class="bg-white border border-slate-200 rounded-lg p-3.5 shadow-sm flex flex-col sm:flex-row sm:items-center justify-between gap-3">
        <form @submit.prevent="applySearch" class="flex items-center gap-2 flex-1 max-w-md">
          <div class="relative flex-1">
            <input
              type="text"
              v-model="searchQuery"
              placeholder="Search by ID, name, phone, email, id_number..."
              class="w-full text-xs border-slate-300 rounded pl-8 pr-3 py-1.5 text-slate-800 placeholder-slate-400 focus:border-blue-500 focus:ring-1 focus:ring-blue-500"
            />
            <svg class="w-3.5 h-3.5 text-slate-400 absolute left-2.5 top-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
            </svg>
          </div>

          <button
            type="submit"
            class="px-3 py-1.5 text-xs font-bold text-slate-700 bg-slate-100 hover:bg-slate-200 rounded transition-colors"
          >
            Search
          </button>

          <button
            v-if="searchQuery"
            type="button"
            @click="clearSearch"
            class="px-2.5 py-1.5 text-xs font-bold text-slate-500 hover:text-slate-800 transition-colors"
          >
            Clear
          </button>
        </form>

        <div class="text-xs text-slate-500">
          Showing <span class="font-bold text-slate-800">{{ patients.total || 0 }}</span> patients
        </div>
      </div>

      <!-- Patients DataTable matching reference styling -->
      <div class="bg-white border border-slate-200 rounded-lg shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
          <table class="w-full text-left text-xs text-slate-600">
            <thead class="bg-slate-50 border-b border-slate-200 text-[11px] uppercase font-bold text-slate-600 tracking-wider">
              <tr>
                <th
                  @click="toggleSort('id_number')"
                  class="px-4 py-3 cursor-pointer hover:bg-slate-100/70 select-none transition-colors"
                >
                  <div class="flex items-center gap-1">
                    <span>ID Patient</span>
                    <span class="text-slate-400 text-[9px]">{{ sortIndicator('id_number') }}</span>
                  </div>
                </th>
                <th class="px-3 py-3 text-center">Avatar</th>
                <th
                  @click="toggleSort('firstname')"
                  class="px-4 py-3 cursor-pointer hover:bg-slate-100/70 select-none transition-colors"
                >
                  <div class="flex items-center gap-1">
                    <span>First Name</span>
                    <span class="text-slate-400 text-[9px]">{{ sortIndicator('firstname') }}</span>
                  </div>
                </th>
                <th
                  @click="toggleSort('lastname')"
                  class="px-4 py-3 cursor-pointer hover:bg-slate-100/70 select-none transition-colors"
                >
                  <div class="flex items-center gap-1">
                    <span>Last Name</span>
                    <span class="text-slate-400 text-[9px]">{{ sortIndicator('lastname') }}</span>
                  </div>
                </th>
                <th
                  @click="toggleSort('gender')"
                  class="px-3 py-3 cursor-pointer hover:bg-slate-100/70 select-none transition-colors"
                >
                  <div class="flex items-center gap-1">
                    <span>Gender</span>
                    <span class="text-slate-400 text-[9px]">{{ sortIndicator('gender') }}</span>
                  </div>
                </th>
                <th
                  @click="toggleSort('dob')"
                  class="px-3 py-3 cursor-pointer hover:bg-slate-100/70 select-none transition-colors"
                >
                  <div class="flex items-center gap-1">
                    <span>Date of Birth</span>
                    <span class="text-slate-400 text-[9px]">{{ sortIndicator('dob') }}</span>
                  </div>
                </th>
                <th
                  @click="toggleSort('age')"
                  class="px-3 py-3 cursor-pointer hover:bg-slate-100/70 select-none transition-colors text-center"
                >
                  <div class="flex items-center justify-center gap-1">
                    <span>Age</span>
                    <span class="text-slate-400 text-[9px]">{{ sortIndicator('age') }}</span>
                  </div>
                </th>
                <th class="px-3 py-3">Phone</th>
                <th class="px-3 py-3">Email</th>
                <th class="px-4 py-3">Profession / Occupation</th>
                <th class="px-4 py-3">Address</th>
                <th
                  @click="toggleSort('balance')"
                  class="px-3 py-3 cursor-pointer hover:bg-slate-100/70 select-none transition-colors"
                >
                  <div class="flex items-center gap-1">
                    <span>Balance</span>
                    <span class="text-slate-400 text-[9px]">{{ sortIndicator('balance') }}</span>
                  </div>
                </th>
                <th class="px-3 py-3">Card Status</th>
                <th class="px-4 py-3 text-right">Actions</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
              <tr
                v-for="p in patients.data"
                :key="p.id"
                class="hover:bg-slate-50/70 transition-colors"
              >
                <!-- ID Patient -->
                <td class="px-4 py-3 font-mono font-bold text-slate-800 whitespace-nowrap">
                  {{ p.id_number !== null && p.id_number !== undefined ? p.id_number : p.id }}
                </td>

                <!-- Avatar Thumbnail -->
                <td class="px-3 py-2 text-center">
                  <div class="inline-flex w-7 h-9 border border-slate-200 rounded overflow-hidden bg-slate-50 items-center justify-center">
                    <img
                      v-if="p.avatar_url"
                      :src="p.avatar_url"
                      :alt="p.full_name"
                      class="w-full h-full object-cover"
                    />
                    <svg v-else class="w-4 h-4 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                    </svg>
                  </div>
                </td>

                <!-- First Name -->
                <td class="px-4 py-3 font-medium text-slate-900 whitespace-nowrap">
                  <Link :href="route('patients.show', p.id)" class="text-[#2457c5] hover:underline font-semibold">
                    {{ p.firstname || '—' }}
                  </Link>
                </td>

                <!-- Last Name -->
                <td class="px-4 py-3 font-medium text-slate-900 whitespace-nowrap">
                  {{ p.lastname || '—' }}
                </td>

                <!-- Gender -->
                <td class="px-3 py-3 capitalize whitespace-nowrap">
                  <span
                    v-if="p.gender"
                    :class="[
                      'px-2 py-0.5 rounded text-[10px] font-semibold',
                      p.gender === 'male' ? 'bg-sky-50 text-sky-700' :
                      p.gender === 'female' ? 'bg-pink-50 text-pink-700' :
                      'bg-slate-100 text-slate-600'
                    ]"
                  >
                    {{ p.gender }}
                  </span>
                  <span v-else class="text-slate-300">—</span>
                </td>

                <!-- Date of Birth -->
                <td class="px-3 py-3 font-mono whitespace-nowrap text-slate-700">
                  {{ p.dob || '—' }}
                </td>

                <!-- Age -->
                <td class="px-3 py-3 text-center font-semibold text-slate-700">
                  {{ p.age !== null && p.age !== undefined ? p.age : '—' }}
                </td>

                <!-- Phone -->
                <td class="px-3 py-3 font-mono whitespace-nowrap text-slate-700">
                  {{ p.phone || '—' }}
                </td>

                <!-- Email -->
                <td class="px-3 py-3 max-w-[140px] truncate text-slate-600" :title="p.email">
                  {{ p.email || '—' }}
                </td>

                <!-- Profession / Occupation -->
                <td class="px-4 py-3 max-w-[140px] truncate text-slate-600" :title="p.profession">
                  {{ p.profession || '—' }}
                </td>

                <!-- Address -->
                <td class="px-4 py-3 max-w-[160px] truncate text-slate-600" :title="p.address">
                  {{ p.address || '—' }}
                </td>

                <!-- Balance -->
                <td class="px-3 py-3 font-mono font-bold whitespace-nowrap">
                  <span :class="(p.balance || 0) > 0 ? 'text-amber-600' : 'text-slate-700'">
                    ${{ Number(p.balance || 0).toFixed(2) }}
                  </span>
                </td>

                <!-- Card Status -->
                <td class="px-3 py-3 whitespace-nowrap">
                  <div class="flex items-center gap-1">
                    <span
                      v-if="p.card_activated"
                      class="px-2 py-0.5 rounded text-[10px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-200"
                    >
                      Active
                    </span>
                    <span
                      v-else
                      class="px-2 py-0.5 rounded text-[10px] font-bold bg-slate-100 text-slate-500 border border-slate-200"
                    >
                      Inactive
                    </span>
                    <span
                      v-if="p.is_trail_card"
                      class="px-1.5 py-0.5 rounded text-[9px] font-bold bg-purple-50 text-purple-700 border border-purple-200"
                    >
                      Trail
                    </span>
                  </div>
                </td>

                <!-- Actions -->
                <td class="px-4 py-3 text-right whitespace-nowrap">
                  <div class="inline-flex items-center gap-1.5">
                    <Link
                      :href="route('patients.show', p.id)"
                      class="px-2 py-1 rounded text-xs font-semibold text-[#2457c5] bg-blue-50 hover:bg-blue-100 transition-colors"
                      title="View Profile"
                    >
                      View
                    </Link>
                    <Link
                      :href="route('patients.edit', p.id)"
                      class="px-2 py-1 rounded text-xs font-semibold text-slate-700 border border-slate-300 hover:bg-slate-50 transition-colors"
                      title="Edit Patient"
                    >
                      Edit
                    </Link>
                    <button
                      type="button"
                      @click="confirmDelete(p)"
                      class="px-2 py-1 rounded text-xs font-semibold text-rose-600 bg-rose-50 hover:bg-rose-100 transition-colors"
                      title="Delete Patient"
                    >
                      Delete
                    </button>
                  </div>
                </td>
              </tr>

              <!-- Empty State -->
              <tr v-if="!patients.data || patients.data.length === 0">
                <td colspan="14" class="px-6 py-12 text-center text-slate-400">
                  <div class="max-w-sm mx-auto space-y-2">
                    <p class="font-medium text-slate-600">No patients found</p>
                    <p class="text-xs text-slate-400">
                      {{ searchQuery ? 'Try adjusting your search criteria.' : 'Click "+ Create Patient" to add your first patient.' }}
                    </p>
                  </div>
                </td>
              </tr>
            </tbody>
          </table>
        </div>

        <!-- Pagination -->
        <div
          v-if="patients.links && patients.links.length > 3"
          class="px-4 py-3 border-t border-slate-100 flex flex-col sm:flex-row sm:items-center justify-between gap-2"
        >
          <div class="text-xs text-slate-500">
            Showing {{ patients.from || 0 }} to {{ patients.to || 0 }} of {{ patients.total || 0 }} records
          </div>

          <div class="flex items-center gap-1 flex-wrap">
            <template v-for="(link, idx) in patients.links" :key="idx">
              <Link
                v-if="link.url"
                :href="link.url"
                :class="[
                  'px-2.5 py-1 rounded text-xs font-semibold transition-colors',
                  link.active
                    ? 'bg-[#2457c5] text-white shadow-sm'
                    : 'text-slate-600 hover:bg-slate-100'
                ]"
                v-html="link.label"
              />
              <span
                v-else
                class="px-2.5 py-1 text-xs text-slate-300 select-none"
                v-html="link.label"
              />
            </template>
          </div>
        </div>
      </div>
    </div>

    <!-- e-Health Modal -->
    <div
      v-if="showEHealthModal"
      class="fixed inset-0 z-50 overflow-y-auto flex items-center justify-center p-4"
    >
      <div class="fixed inset-0 bg-slate-900/50 backdrop-blur-sm" @click="showEHealthModal = false"></div>

      <div class="relative bg-white rounded-xl max-w-md w-full p-6 shadow-2xl border border-slate-200 space-y-4">
        <div class="flex items-center justify-between border-b border-slate-100 pb-3">
          <div class="flex items-center gap-2 text-[#00a65a]">
            <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
              <rect x="3" y="3" width="7" height="7"></rect>
              <rect x="14" y="3" width="7" height="7"></rect>
              <rect x="3" y="14" width="7" height="7"></rect>
              <rect x="14" y="14" width="7" height="7"></rect>
            </svg>
            <h3 class="text-sm font-bold text-slate-900">e-Health System Registry</h3>
          </div>
          <button
            type="button"
            @click="showEHealthModal = false"
            class="text-slate-400 hover:text-slate-600"
          >
            ✕
          </button>
        </div>

        <div class="text-xs text-slate-600 space-y-3">
          <p>
            Connected to tenant dental clinic e-Health sync gateway. Patient records are synchronized with electronic health service registries.
          </p>
          <div class="p-3 bg-emerald-50 rounded-lg border border-emerald-200 text-emerald-800 space-y-1">
            <div class="font-bold flex items-center gap-2">
              <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
              <span>Gateway Status: Active &amp; Ready</span>
            </div>
            <div>Total Registered: <span class="font-bold">{{ patients.total || 0 }} patients</span></div>
          </div>
        </div>

        <div class="flex justify-end pt-2">
          <button
            type="button"
            @click="showEHealthModal = false"
            class="px-4 py-2 rounded text-xs font-bold text-white bg-[#00a65a] hover:bg-[#008d4c] transition-colors"
          >
            Close
          </button>
        </div>
      </div>
    </div>

    <!-- Delete Confirmation Modal -->
    <div
      v-if="patientToDelete"
      class="fixed inset-0 z-50 overflow-y-auto flex items-center justify-center p-4"
    >
      <div class="fixed inset-0 bg-slate-900/50 backdrop-blur-sm" @click="patientToDelete = null"></div>

      <div class="relative bg-white rounded-xl max-w-md w-full p-6 shadow-2xl border border-slate-200 space-y-4">
        <div class="flex items-center gap-3 text-rose-600">
          <div class="w-10 h-10 rounded-lg bg-rose-100 flex items-center justify-center flex-shrink-0">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
            </svg>
          </div>
          <div>
            <h3 class="text-sm font-bold text-slate-900">Delete Patient Record</h3>
            <p class="text-xs text-slate-500">Soft delete can be reversed by an administrator</p>
          </div>
        </div>

        <p class="text-xs text-slate-600">
          Are you sure you want to delete patient <strong class="text-slate-900">{{ patientToDelete.full_name }}</strong> (ID: {{ patientToDelete.id_number || patientToDelete.id }})?
        </p>

        <div class="flex items-center justify-end gap-2 pt-2">
          <button
            type="button"
            @click="patientToDelete = null"
            class="px-3.5 py-1.5 rounded text-xs font-bold text-slate-600 hover:bg-slate-100 transition-colors"
          >
            Cancel
          </button>
          <button
            type="button"
            @click="deletePatientConfirmed"
            class="px-3.5 py-1.5 rounded text-xs font-bold text-white bg-rose-600 hover:bg-rose-700 transition-colors shadow-sm"
          >
            Yes, Delete
          </button>
        </div>
      </div>
    </div>
  </TenantLayout>
</template>

<script setup>
import { ref } from 'vue'
import { Head, Link } from '@inertiajs/inertia-vue3'
import { Inertia as router } from '@inertiajs/inertia'
import TenantLayout from '@/Layouts/TenantLayout.vue'

const props = defineProps({
  patients: Object,
  filters: Object,
})

const searchQuery = ref(props.filters?.search || '')
const currentSortBy = ref(props.filters?.sort_by || 'id')
const currentSortDir = ref(props.filters?.sort_dir || 'desc')
const patientToDelete = ref(null)
const showEHealthModal = ref(false)

function applySearch() {
  router.get(route('patients.index'), {
    search: searchQuery.value || undefined,
    sort_by: currentSortBy.value,
    sort_dir: currentSortDir.value,
  }, {
    preserveState: true,
    replace: true,
  })
}

function clearSearch() {
  searchQuery.value = ''
  applySearch()
}

function toggleSort(field) {
  if (currentSortBy.value === field) {
    currentSortDir.value = currentSortDir.value === 'asc' ? 'desc' : 'asc'
  } else {
    currentSortBy.value = field
    currentSortDir.value = 'asc'
  }
  applySearch()
}

function sortIndicator(field) {
  if (currentSortBy.value !== field) return '↕'
  return currentSortDir.value === 'asc' ? '▲' : '▼'
}

function confirmDelete(patient) {
  patientToDelete.value = patient
}

function deletePatientConfirmed() {
  if (!patientToDelete.value) return
  router.delete(route('patients.destroy', patientToDelete.value.id), {
    onSuccess: () => {
      patientToDelete.value = null
    }
  })
}
</script>
