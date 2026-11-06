<template>
  <TenantLayout>
    <Head :title="activeChart ? `Chart - ${activeChart.patient?.full_name}` : 'Interactive Dental Chart'" />

    <div class="odontogram-page">
      <!-- Topbar Header -->
      <header class="topbar">
        <div>
          <div class="eyebrow">
            <span v-if="activeChart">
              Patient #{{ activeChart.patient?.patient_number }} &middot; Chart #{{ activeChart.id }}
            </span>
            <span v-else>Dental chart workspace</span>
          </div>
          <h1 class="title">
            <span v-if="activeChart">{{ activeChart.patient?.full_name }}'s Dental Chart</span>
            <span v-else>Interactive Odontogram Chart</span>
          </h1>
          <p class="sub">
            Click on any tooth surface (top, bottom, left, right, center) or tooth number (whole tooth) to record findings.
          </p>
        </div>

        <div class="actions no-print">
          <!-- Patient Selector -->
          <div class="patient-selector">
            <label for="chartSelect" class="text-xs font-bold text-slate-500">Switch Chart:</label>
            <select
              id="chartSelect"
              :value="activeChart?.id || ''"
              @change="onSwitchChart($event.target.value)"
            >
              <option value="">-- Choose Patient / Chart --</option>
              <template v-for="p in patients" :key="p.id">
                <option
                  v-for="c in p.dental_charts"
                  :key="c.id"
                  :value="c.id"
                >
                  {{ p.full_name }} (Chart #{{ c.id }} - {{ c.chart_date }})
                </option>
              </template>
            </select>
          </div>

          <Link :href="route('dental-charts.index')" class="btn-chart">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16" /></svg>
            <span>All Charts</span>
          </Link>

          <Link :href="route('dental-charts.create')" class="btn-chart">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" /></svg>
            <span>New Chart</span>
          </Link>

          <button type="button" @click="exportJson" class="btn-chart" title="Export chart data as JSON">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" /></svg>
            <span>Export JSON</span>
          </button>

          <button type="button" @click="printChart" class="btn-chart">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4H7v4a2 2 0 002 2zM7 9V5a2 2 0 012-2h6a2 2 0 012 2v4" /></svg>
            <span>Print</span>
          </button>

          <button type="button" @click="resetChart" class="btn-chart btn-chart-danger">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
            <span>Clear</span>
          </button>

          <button
            type="button"
            @click="saveChart"
            :disabled="isSaving"
            class="btn-chart btn-chart-primary"
          >
            <svg v-if="!isSaving" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" /></svg>
            <svg v-else class="animate-spin w-4 h-4" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path></svg>
            <span>{{ isSaving ? 'Saving...' : 'Save Chart' }}</span>
          </button>
        </div>
      </header>

      <!-- Toolrow: Dentition Filter and Save status -->
      <div class="toolrow no-print">
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

        <div class="status">
          <span>{{ saveStatus }}</span>
        </div>
      </div>

      <!-- Paper Odontogram Sheet -->
      <div class="paper-shell">
        <div class="paper">
          <div class="paper-heading">
            ODONTOGRAM / DENTAL CHART EXAMINATION SHEET
            <span v-if="activeChart"> &middot; Patient: {{ activeChart.patient?.full_name }} (#{{ activeChart.patient?.patient_number }})</span>
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
                        @click="openModal(num, 'whole')"
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
                          @click="openModal(num, s)"
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
                        @click="openModal(num, 'whole')"
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
                          @click="openModal(num, s)"
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
                          @click="openModal(num, s)"
                          :title="`Tooth ${num}, ${s}`"
                        />
                      </svg>
                      <button
                        type="button"
                        class="tooth-number"
                        :style="getWholeToothStyle(num)"
                        @click="openModal(num, 'whole')"
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
                          @click="openModal(num, s)"
                          :title="`Tooth ${num}, ${s}`"
                        />
                      </svg>
                      <button
                        type="button"
                        class="tooth-number"
                        :style="getWholeToothStyle(num)"
                        @click="openModal(num, 'whole')"
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
                          @click="openModal(num, s)"
                          :title="`Tooth ${num}, ${s}`"
                        />
                      </svg>
                      <button
                        type="button"
                        class="tooth-number"
                        :style="getWholeToothStyle(num)"
                        @click="openModal(num, 'whole')"
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
                          @click="openModal(num, s)"
                          :title="`Tooth ${num}, ${s}`"
                        />
                      </svg>
                      <button
                        type="button"
                        class="tooth-number"
                        :style="getWholeToothStyle(num)"
                        @click="openModal(num, 'whole')"
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
                          @click="openModal(num, s)"
                          :title="`Tooth ${num}, ${s}`"
                        />
                      </svg>
                      <button
                        type="button"
                        class="tooth-number"
                        :style="getWholeToothStyle(num)"
                        @click="openModal(num, 'whole')"
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
                          @click="openModal(num, s)"
                          :title="`Tooth ${num}, ${s}`"
                        />
                      </svg>
                      <button
                        type="button"
                        class="tooth-number"
                        :style="getWholeToothStyle(num)"
                        @click="openModal(num, 'whole')"
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
              v-model="examinationNotes"
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
      <div class="hint no-print">
        <span class="hint-dot"></span>
        <span>Click any trapezoid for surface marking (top, bottom, left, right, center), or click the tooth number for whole tooth condition.</span>
      </div>

      <!-- Below Grid: Findings Summary and List -->
      <div class="below-grid no-print">
        <!-- Recorded Findings List -->
        <div class="info-card">
          <div class="flex items-center justify-between mb-2">
            <h3 class="font-bold text-slate-700 text-sm">Recorded Tooth Findings ({{ Object.keys(findings).length }})</h3>
            <span v-if="Object.keys(findings).length > 0" class="text-xs text-slate-400">Click Edit to modify</span>
          </div>

          <div v-if="Object.keys(findings).length === 0" class="empty-note py-4 text-center">
            No tooth findings marked yet. Click on any tooth surface above to begin.
          </div>

          <div v-else class="findings-list">
            <div
              v-for="(f, key) in findings"
              :key="key"
              class="finding-item"
            >
              <div class="flex items-center gap-2">
                <span class="w-3.5 h-3.5 rounded-full flex-shrink-0" :style="{ background: f.color || '#d83c43' }"></span>
                <span class="font-bold text-slate-800">Tooth {{ f.tooth }}</span>
                <span class="text-slate-500 font-semibold text-xs">({{ f.surface }})</span>
                <span class="text-slate-600 font-medium text-xs">&middot; {{ getConditionLabel(f.condition_id, f.condition) }}</span>
                <span v-if="f.status" class="px-1.5 py-0.5 rounded text-[10px] font-bold uppercase bg-slate-100 text-slate-600 ml-1">
                  {{ f.status }}
                </span>
                <span v-if="f.note" class="text-slate-400 text-xs italic ml-1 truncate max-w-[200px]">"{{ f.note }}"</span>
              </div>
              <button
                type="button"
                @click="openModal(f.tooth, f.surface)"
                class="text-blue-600 hover:text-blue-800 font-bold text-xs"
              >
                Edit
              </button>
            </div>
          </div>
        </div>

        <!-- Quick Stats Card -->
        <div class="info-card flex flex-col justify-between">
          <div>
            <h3 class="font-bold text-slate-700 text-sm">Chart Summary</h3>
            <div class="stat-big mt-2">{{ Object.keys(findings).length }}</div>
            <div class="stat-small">Active surface markings</div>
          </div>

          <div class="pt-4 border-t border-slate-100 mt-4">
            <div class="text-xs font-semibold text-slate-500">Affected Teeth:</div>
            <div class="text-base font-bold text-slate-800 mt-1">
              {{ uniqueTeethCount }} teeth
            </div>
            <div class="text-[11px] text-slate-400 mt-1">
              Status: {{ activeChart ? `Linked to Chart #${activeChart.id}` : 'Draft' }}
            </div>
          </div>
        </div>
      </div>

      <!-- Finding Editor Modal Dialog -->
      <div
        v-if="modalOpen"
        class="modal-backdrop open"
        @click.self="closeModal"
      >
        <div class="modal">
          <div class="modal-head">
            <div>
              <div class="modal-kicker">Dental finding editor</div>
              <h2 class="modal-title">
                Tooth {{ modalForm.tooth }} &middot; {{ modalForm.surface === 'whole' ? 'Whole tooth' : (modalForm.surface.charAt(0).toUpperCase() + modalForm.surface.slice(1)) }}
              </h2>
            </div>
            <button type="button" @click="closeModal" class="close" aria-label="Close">&times;</button>
          </div>

          <form @submit.prevent="saveModalFinding" class="modal-body">
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

            <div class="field">
              <label>Marking color</label>
              <div class="color-row">
                <input type="color" v-model="modalForm.color">
                <span class="color-help">Select the visual fill color for this surface on the chart.</span>
              </div>
            </div>

            <div class="field">
              <label>Treatment status</label>
              <select v-model="modalForm.status">
                <option value="observed">Observed</option>
                <option value="planned">Treatment planned</option>
                <option value="in_progress">In progress</option>
                <option value="completed">Completed</option>
                <option value="referred">Referred</option>
              </select>
            </div>

            <div class="field">
              <label>Clinical Note</label>
              <textarea
                v-model="modalForm.note"
                rows="3"
                placeholder="Type observations, procedure notes, or details for this surface..."
              ></textarea>
            </div>

            <div class="modal-actions">
              <button
                type="button"
                @click="removeModalFinding"
                class="btn-chart btn-chart-danger"
              >
                Remove marking
              </button>

              <div class="modal-actions-right">
                <button type="button" @click="closeModal" class="btn-chart">
                  Cancel
                </button>
                <button type="submit" class="btn-chart btn-chart-primary">
                  Save finding
                </button>
              </div>
            </div>
          </form>
        </div>
      </div>

      <!-- Toast Notification Alert -->
      <div :class="['toast', toastShow ? 'show' : '']" role="status" aria-live="polite">
        {{ toastMessage }}
      </div>

    </div>
  </TenantLayout>
</template>

<script setup>
import { ref, reactive, computed, onMounted } from 'vue'
import { Head, Link } from '@inertiajs/inertia-vue3'
import { Inertia as router } from '@inertiajs/inertia'
import TenantLayout from '@/Layouts/TenantLayout.vue'
import axios from 'axios'

const props = defineProps({
  activeChart: Object,
  conditions: Array,
  patients: Array,
  invoiceId: [String, Number],
  invoiceItemId: [String, Number],
})

// Dentition FDI constants
const permanent = {
  upperRight: [18, 17, 16, 15, 14, 13, 12, 11],
  upperLeft: [21, 22, 23, 24, 25, 26, 27, 28],
  lowerRight: [48, 47, 46, 45, 44, 43, 42, 41],
  lowerLeft: [31, 32, 33, 34, 35, 36, 37, 38],
}

const primary = {
  upperRight: [55, 54, 53, 52, 51],
  upperLeft: [61, 62, 63, 64, 65],
  lowerRight: [85, 84, 83, 82, 81],
  lowerLeft: [71, 72, 73, 74, 75],
}

const surfaces = ['top', 'left', 'center', 'right', 'bottom']

const polygons = {
  top: '9,7 91,7 70,29 30,29',
  left: '7,9 29,30 29,70 7,91',
  center: '34,34 66,34 66,66 34,66',
  right: '93,9 71,30 71,70 93,91',
  bottom: '9,93 30,71 70,71 91,93',
}

// Local state
const activeFilter = ref('all')
const examinationNotes = ref(props.activeChart?.examination_notes || '')
const saveStatus = ref('Ready')
const isSaving = ref(false)
const findings = reactive({})
const toastShow = ref(false)
const toastMessage = ref('')
let toastTimer = null

// Modal state
const modalOpen = ref(false)
const modalForm = reactive({
  tooth: '',
  surface: 'top',
  condition_id: null,
  condition_code: '',
  color: '#d83c43',
  status: 'observed',
  note: '',
})

// Normalised Condition list
const conditionList = computed(() => {
  if (props.conditions && props.conditions.length > 0) {
    return props.conditions.map(c => ({
      id: c.id,
      value: c.code,
      label: c.name,
      color: c.default_color,
    }))
  }
  return [
    { value: 'caries', label: 'Caries / decay', color: '#d83c43' },
    { value: 'restoration', label: 'Existing restoration', color: '#2f66cf' },
    { value: 'planned', label: 'Treatment planned', color: '#e59b26' },
    { value: 'completed', label: 'Completed treatment', color: '#319b62' },
    { value: 'missing', label: 'Missing tooth', color: '#707782' },
    { value: 'crown', label: 'Crown', color: '#8b5bc7' },
    { value: 'fracture', label: 'Fracture', color: '#8a5a38' },
    { value: 'sealant', label: 'Sealant', color: '#169d9a' },
  ]
})

const keyFor = (tooth, surface) => `${tooth}-${surface}`

const uniqueTeethCount = computed(() => {
  const teeth = new Set()
  Object.values(findings).forEach(f => teeth.add(f.tooth))
  return teeth.size
})

function getConditionLabel(condId, condCode) {
  if (condId) {
    const found = conditionList.value.find(c => c.id === condId)
    if (found) return found.label
  }
  if (condCode) {
    const found = conditionList.value.find(c => c.value === condCode)
    if (found) return found.label
  }
  return 'Marking'
}

function getToothClass(num, type) {
  if (activeFilter.value === 'all') return ''
  return activeFilter.value === type ? '' : 'opacity-20 pointer-events-none'
}

function getSurfaceFill(tooth, surface) {
  const k = keyFor(tooth, surface)
  if (findings[k] && findings[k].color) {
    return findings[k].color
  }
  // Check if whole tooth is marked
  const wholeK = keyFor(tooth, 'whole')
  if (findings[wholeK] && findings[wholeK].color) {
    return findings[wholeK].color
  }
  return '#ffffff'
}

function getWholeToothStyle(tooth) {
  const wholeK = keyFor(tooth, 'whole')
  if (findings[wholeK] && findings[wholeK].color) {
    return {
      background: findings[wholeK].color,
      color: '#ffffff',
      fontWeight: 'bold',
      borderRadius: '4px',
    }
  }
  return {}
}

function showToast(msg) {
  toastMessage.value = msg
  toastShow.value = true
  clearTimeout(toastTimer)
  toastTimer = setTimeout(() => {
    toastShow.value = false
  }, 2500)
}

function openModal(tooth, surface) {
  const toothStr = String(tooth)
  const existing = findings[keyFor(toothStr, surface)] || (surface !== 'whole' ? findings[keyFor(toothStr, 'whole')] : null) || {}

  modalForm.tooth = toothStr
  modalForm.surface = surface
  modalForm.condition_id = existing.condition_id || null
  modalForm.condition_code = existing.condition || ''
  modalForm.color = existing.color || '#d83c43'
  modalForm.status = existing.status || 'observed'
  modalForm.note = existing.note || ''

  modalOpen.value = true
}

function closeModal() {
  modalOpen.value = false
}

function onConditionChange() {
  if (modalForm.condition_id) {
    const matched = conditionList.value.find(c => c.id === modalForm.condition_id)
    if (matched && matched.color) {
      modalForm.color = matched.color
      modalForm.condition_code = matched.value
    }
  }
}

function onSurfaceChange() {
  openModal(modalForm.tooth, modalForm.surface)
}

function saveModalFinding() {
  const tooth = modalForm.tooth
  const surface = modalForm.surface
  const k = keyFor(tooth, surface)

  if (!modalForm.condition_id && !modalForm.note && !modalForm.color) {
    delete findings[k]
  } else {
    findings[k] = {
      tooth: String(tooth),
      surface: surface,
      condition_id: modalForm.condition_id,
      condition: modalForm.condition_code,
      color: modalForm.color,
      status: modalForm.status,
      note: modalForm.note ? modalForm.note.trim() : '',
    }
  }

  closeModal()
  showToast('Finding recorded')
  saveChart()
}

function removeModalFinding() {
  const k = keyFor(modalForm.tooth, modalForm.surface)
  delete findings[k]
  closeModal()
  showToast('Marking removed')
  saveChart()
}

function onSwitchChart(chartId) {
  if (!chartId) {
    router.visit(route('dental.index'))
  } else {
    router.visit(route('dental.show', chartId))
  }
}

async function saveChart() {
  const chart = props.activeChart
  const payloadFindings = Object.values(findings).map(f => {
    const condObj = conditionList.value.find(c => c.id === f.condition_id || c.value === f.condition)
    return {
      tooth: Number(f.tooth),
      surface: f.surface,
      condition_id: condObj?.id || f.condition_id || null,
      condition_code: condObj?.value || f.condition || null,
      color: f.color || null,
      status: f.status || 'observed',
      note: f.note || null,
    }
  })

  const payload = {
    dentition_type: 'mixed',
    examination_notes: examinationNotes.value,
    findings: payloadFindings,
    invoice_id: props.invoiceId || null,
    invoice_item_id: props.invoiceItemId || null,
  }

  // Local storage caching
  const storageKey = chart?.id ? `dental-chart-db-${chart.id}` : 'dental-chart-demo'
  try {
    localStorage.setItem(storageKey, JSON.stringify({
      savedAt: new Date().toISOString(),
      examinationNotes: examinationNotes.value,
      findings,
    }))
  } catch (e) {}

  if (chart?.id) {
    isSaving.value = true
    saveStatus.value = 'Saving to database...'
    try {
      const res = await axios.post(`/api/dental-charts/${chart.id}/sync-findings`, payload)
      saveStatus.value = `Saved to database · ${new Date().toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' })}`
      showToast('Chart saved to database successfully')
      return true
    } catch (err) {
      console.error(err)
      saveStatus.value = `Save failed: ${err.response?.data?.message || err.message}`
      showToast('Database save failed (saved locally)')
      return false
    } finally {
      isSaving.value = false
    }
  } else {
    saveStatus.value = `Saved in browser · ${new Date().toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' })}`
    return true
  }
}

function resetChart() {
  if (!confirm('Clear all chart markings and reset?')) return
  Object.keys(findings).forEach(k => delete findings[k])
  examinationNotes.value = ''
  saveChart()
  showToast('Chart reset')
}

function exportJson() {
  const data = {
    version: 2,
    exportedAt: new Date().toISOString(),
    chartId: props.activeChart?.id ?? null,
    patient: props.activeChart?.patient?.full_name ?? 'DEMO',
    examinationNotes: examinationNotes.value,
    findings: Object.values(findings),
  }
  const blob = new Blob([JSON.stringify(data, null, 2)], { type: 'application/json' })
  const url = URL.createObjectURL(blob)
  const a = document.createElement('a')
  a.href = url
  a.download = `dental-chart-${props.activeChart ? 'patient-' + props.activeChart.patient_id : 'demo'}-${new Date().toISOString().slice(0, 10)}.json`
  document.body.appendChild(a)
  a.click()
  a.remove()
  URL.revokeObjectURL(url)
  showToast('JSON backup downloaded')
}

function printChart() {
  window.print()
}

// Initialize findings from database or local storage
onMounted(() => {
  if (props.activeChart) {
    const chart = props.activeChart
    examinationNotes.value = chart.examination_notes || ''

    if (chart.teeth && Array.isArray(chart.teeth)) {
      chart.teeth.forEach(tooth => {
        if (tooth.findings && Array.isArray(tooth.findings)) {
          tooth.findings.forEach(f => {
            const cond = f.condition
            findings[keyFor(tooth.tooth_number, f.surface)] = {
              tooth: String(tooth.tooth_number),
              surface: f.surface,
              condition_id: f.dental_condition_id,
              condition: cond ? cond.code : '',
              color: f.custom_color || (cond ? cond.default_color : '#d83c43'),
              status: f.treatment_status || 'observed',
              note: f.note || '',
            }
          })
        }
      })
    }
    saveStatus.value = `Loaded chart #${chart.id} for ${chart.patient?.full_name || 'patient'}`
  } else {
    // Example demo finding for workspace overview
    findings[keyFor('23', 'top')] = {
      tooth: '23',
      surface: 'top',
      condition_id: null,
      condition: 'caries',
      color: '#d83c43',
      status: 'observed',
      note: 'Example marking.',
    }
    saveStatus.value = 'Demo chart loaded · select or create a chart to record clinical records'
  }
})
</script>

<style scoped>
:root {
  --ink: #171717;
  --paper: #fffefa;
  --muted: #686868;
  --line: #202020;
  --blue: #2457c5;
  --bg: #f8fafc;
  --border: #dadde2;
}

.odontogram-page {
  width: 100%;
  max-width: 1400px;
  margin: 0 auto;
}

.topbar {
  display: flex;
  align-items: center;
  justify-content: space-between;
  gap: 18px;
  flex-wrap: wrap;
  margin-bottom: 18px;
  width: 100%;
}

.eyebrow {
  font-size: 12px;
  text-transform: uppercase;
  letter-spacing: 0.16em;
  color: #64748b;
  font-weight: 800;
}

.title {
  margin: 5px 0 7px;
  font-size: clamp(22px, 2.5vw, 30px);
  letter-spacing: -0.035em;
  color: #0f172a;
  font-weight: 850;
}

.sub {
  margin: 0;
  color: #64748b;
  font-size: 13.5px;
}

.actions {
  display: flex;
  gap: 8px;
  flex-wrap: wrap;
  align-items: center;
  margin-left: auto;
  justify-content: flex-end;
}

.btn-chart {
  border: 1px solid #cbd5e1;
  background: #fff;
  border-radius: 9px;
  padding: 8px 12px;
  color: #334155;
  font-weight: 700;
  font-size: 13px;
  box-shadow: 0 1px 2px rgba(0, 0, 0, 0.04);
  cursor: pointer;
  display: inline-flex;
  align-items: center;
  gap: 6px;
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

.patient-selector {
  display: flex;
  align-items: center;
  gap: 8px;
  background: #fff;
  border: 1px solid #cbd5e1;
  padding: 4px 9px;
  border-radius: 9px;
}

.patient-selector select {
  border: 0;
  background: transparent;
  outline: none;
  font-size: 13px;
  font-weight: 600;
  color: #1e293b;
  cursor: pointer;
}

.toolrow {
  display: flex;
  justify-content: space-between;
  align-items: center;
  gap: 12px;
  flex-wrap: wrap;
  margin-bottom: 14px;
}

.filters {
  display: flex;
  gap: 6px;
  flex-wrap: wrap;
}

.filter {
  padding: 7px 12px;
  border: 1px solid #cbd5e1;
  background: #fff;
  color: #475569;
  border-radius: 999px;
  font-size: 12px;
  font-weight: 800;
  cursor: pointer;
  transition: all 0.15s;
}

.filter.active {
  background: #eff6ff;
  color: #1d4ed8;
  border-color: #93c5fd;
}

.status {
  font-size: 12px;
  color: #64748b;
  font-weight: 500;
}

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
  font-size: 19px;
  font-weight: 800;
  font-style: italic;
  letter-spacing: -0.02em;
  color: #2a2a2a;
  margin: 0 0 18px;
}

.paper-heading span {
  font-weight: 500;
  font-size: 16px;
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
  margin: 0 0px;
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
  margin-top: 22px;
  border-top: 1px solid #dcdad3;
  padding-top: 14px;
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
  margin-top: 20px;
  border-top: 1px solid #dcdad3;
  padding-top: 14px;
  display: flex;
  flex-direction: column;
  gap: 7px;
}

.other-info label {
  font-family: Arial, Helvetica, sans-serif;
  font-size: 19px;
  font-weight: 800;
  font-style: italic;
  letter-spacing: -0.02em;
  color: #2a2a2a;
}

.other-info textarea {
  width: 100%;
  border: 1px dashed #c0beba;
  border-radius: 0;
  resize: vertical;
  min-height: 48px;
  padding: 8px 10px;
  background: transparent;
  outline: none;
  color: #202020;
  font-size: 14px;
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
}

.hint {
  margin: 13px 2px 0;
  color: #64748b;
  font-size: 12px;
  display: flex;
  align-items: center;
  gap: 8px;
}

.hint-dot {
  width: 7px;
  height: 7px;
  background: #2457c5;
  border-radius: 50%;
  display: inline-block;
}

.below-grid {
  display: grid;
  grid-template-columns: minmax(0, 1fr) 280px;
  gap: 14px;
  margin-top: 15px;
}

.info-card {
  background: white;
  border: 1px solid #e2e8f0;
  border-radius: 12px;
  padding: 16px;
}

.stat-big {
  font-size: 26px;
  font-weight: 850;
  color: #0f172a;
}

.stat-small {
  font-size: 12px;
  color: #64748b;
  margin-top: 2px;
}

.findings-list {
  display: flex;
  flex-direction: column;
  gap: 7px;
  max-height: 220px;
  overflow: auto;
}

.finding-item {
  display: flex;
  justify-content: space-between;
  align-items: center;
  gap: 10px;
  border: 1px solid #e2e8f0;
  border-radius: 8px;
  padding: 8px 10px;
  font-size: 12px;
  background: #f8fafc;
}

.empty-note {
  font-size: 12px;
  color: #94a3b8;
}

.modal-backdrop {
  position: fixed;
  inset: 0;
  z-index: 300;
  background: rgba(15, 23, 42, 0.55);
  display: none;
  align-items: center;
  justify-content: center;
  padding: 18px;
}

.modal-backdrop.open {
  display: flex;
}

.modal {
  background: #fff;
  border-radius: 17px;
  width: min(100%, 530px);
  max-height: calc(100vh - 36px);
  overflow: auto;
  box-shadow: 0 25px 80px rgba(0, 0, 0, 0.25);
}

.modal-head {
  display: flex;
  justify-content: space-between;
  align-items: flex-start;
  padding: 22px 23px 14px;
  border-bottom: 1px solid #e2e8f0;
}

.modal-kicker {
  font-size: 11px;
  text-transform: uppercase;
  letter-spacing: 0.15em;
  color: #64748b;
  font-weight: 800;
}

.modal-title {
  margin: 5px 0 0;
  font-size: 21px;
  font-weight: 850;
  letter-spacing: -0.03em;
  color: #0f172a;
}

.close {
  border: 0;
  background: #f1f5f9;
  width: 31px;
  height: 31px;
  border-radius: 8px;
  font-size: 22px;
  line-height: 1;
  color: #64748b;
  cursor: pointer;
}

.close:hover {
  background: #e2e8f0;
  color: #0f172a;
}

.modal-body {
  padding: 18px 23px 22px;
}

.field {
  margin-bottom: 14px;
}

.field label {
  display: block;
  font-size: 12px;
  font-weight: 800;
  color: #334155;
  margin-bottom: 6px;
}

.field input:not([type=color]),
.field select,
.field textarea {
  width: 100%;
  border: 1px solid #cbd5e1;
  border-radius: 9px;
  padding: 9px 11px;
  font-size: 13px;
  background: #fff;
  color: #0f172a;
  outline: none;
}

.field input:focus,
.field select:focus,
.field textarea:focus {
  border-color: #3b82f6;
  box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.15);
}

.field textarea {
  resize: vertical;
  min-height: 70px;
}

.field-grid {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 12px;
}

.color-row {
  display: flex;
  align-items: center;
  gap: 10px;
}

.color-row input[type=color] {
  width: 45px;
  height: 38px;
  border: 1px solid #cbd5e1;
  border-radius: 8px;
  background: #fff;
  padding: 3px;
  cursor: pointer;
}

.color-help {
  font-size: 12px;
  color: #64748b;
}

.modal-actions {
  display: flex;
  align-items: center;
  justify-content: space-between;
  gap: 10px;
  margin-top: 19px;
}

.modal-actions-right {
  display: flex;
  gap: 8px;
}

.toast {
  position: fixed;
  z-index: 400;
  bottom: 22px;
  left: 50%;
  transform: translateX(-50%);
  background: #0f172a;
  color: white;
  border-radius: 10px;
  padding: 11px 16px;
  box-shadow: 0 8px 30px rgba(0, 0, 0, 0.2);
  font-size: 13px;
  opacity: 0;
  pointer-events: none;
  transition: opacity 0.2s;
}

.toast.show {
  opacity: 1;
}

@media (max-width: 740px) {
  .paper-shell { padding: 6px; border-radius: 11px; }
  .paper { padding: 16px 12px; }
  .below-grid { grid-template-columns: 1fr; }
}

@media print {
  body { background: #fff !important; }
  .odontogram-page { max-width: none; padding: 0; }
  .topbar, .toolrow, .below-grid, .hint, .no-print { display: none !important; }
  .paper-shell { border: 0; border-radius: 0; padding: 0; box-shadow: none; }
  .paper { border: 0; box-shadow: none; padding: 8mm 5mm; }
  .chart-scroll { overflow: visible; }
  .chart-inner, .divider-line { min-width: 0; width: 100%; }
}
</style>

