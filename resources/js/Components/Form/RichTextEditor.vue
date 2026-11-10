<template>
  <div :class="['border border-gray-300 rounded overflow-hidden flex flex-col', isFullscreen ? 'fixed inset-4 z-50 bg-white shadow-2xl' : 'w-full']">
    <!-- Toolbar with blue background matching screenshot -->
    <div class="bg-[#5b86e5] text-gray-800 p-1.5 flex flex-col gap-1 select-none text-xs">
      <!-- Row 1 -->
      <div class="flex items-center flex-wrap gap-1">
        <button
          type="button"
          @click="exec('bold')"
          class="w-6 h-6 bg-white hover:bg-gray-100 rounded text-center font-bold text-gray-800 shadow-xs flex items-center justify-center"
          title="Bold"
        >
          B
        </button>
        <button
          type="button"
          @click="exec('italic')"
          class="w-6 h-6 bg-white hover:bg-gray-100 rounded text-center italic font-serif text-gray-800 shadow-xs flex items-center justify-center"
          title="Italic"
        >
          I
        </button>
        <button
          type="button"
          @click="exec('underline')"
          class="w-6 h-6 bg-white hover:bg-gray-100 rounded text-center underline font-semibold text-gray-800 shadow-xs flex items-center justify-center"
          title="Underline"
        >
          U
        </button>
        <button
          type="button"
          @click="exec('strikeThrough')"
          class="w-6 h-6 bg-white hover:bg-gray-100 rounded text-center line-through font-semibold text-gray-800 shadow-xs flex items-center justify-center"
          title="Strikethrough"
        >
          S
        </button>

        <!-- Font Family -->
        <select
          @change="exec('fontName', $event.target.value)"
          class="h-6 px-1.5 py-0 text-xs bg-white text-gray-800 rounded border-0 focus:ring-1 focus:ring-blue-300 cursor-pointer shadow-xs"
        >
          <option value="sans-serif">sans-serif</option>
          <option value="serif">serif</option>
          <option value="monospace">monospace</option>
          <option value="Arial">Arial</option>
          <option value="Georgia">Georgia</option>
        </select>

        <!-- Font Color Highlight button -->
        <div class="relative flex items-center bg-white rounded shadow-xs h-6 px-1">
          <label class="cursor-pointer font-bold text-gray-800 px-1 border-b-2 border-yellow-400 leading-none">
            A
            <input
              type="color"
              @change="exec('foreColor', $event.target.value)"
              class="sr-only"
            />
          </label>
        </div>

        <!-- Font Size -->
        <select
          @change="exec('fontSize', $event.target.value)"
          class="h-6 px-1.5 py-0 text-xs bg-white text-gray-800 rounded border-0 focus:ring-1 focus:ring-blue-300 cursor-pointer shadow-xs"
        >
          <option value="3">14</option>
          <option value="1">10</option>
          <option value="2">12</option>
          <option value="4">16</option>
          <option value="5">18</option>
          <option value="6">24</option>
        </select>

        <!-- Format Block -->
        <select
          @change="exec('formatBlock', $event.target.value)"
          class="h-6 px-1.5 py-0 text-xs bg-white text-gray-800 rounded border-0 focus:ring-1 focus:ring-blue-300 cursor-pointer shadow-xs"
        >
          <option value="p">¶ Normal</option>
          <option value="h1">H1</option>
          <option value="h2">H2</option>
          <option value="h3">H3</option>
          <option value="blockquote">Quote</option>
        </select>

        <!-- Clear Format -->
        <button
          type="button"
          @click="exec('removeFormat')"
          class="h-6 px-1.5 bg-white hover:bg-gray-100 rounded text-gray-800 shadow-xs flex items-center justify-center"
          title="Clear Format"
        >
          <svg class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M19 11l-7-7-7 7m14 6l-7-7-7 7" />
          </svg>
        </button>

        <!-- Bullet List -->
        <button
          type="button"
          @click="exec('insertUnorderedList')"
          class="w-6 h-6 bg-white hover:bg-gray-100 rounded text-gray-800 shadow-xs flex items-center justify-center"
          title="Bullet List"
        >
          <svg class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <line x1="8" y1="6" x2="21" y2="6"></line>
            <line x1="8" y1="12" x2="21" y2="12"></line>
            <line x1="8" y1="18" x2="21" y2="18"></line>
            <circle cx="4" cy="6" r="1.5" fill="currentColor"></circle>
            <circle cx="4" cy="12" r="1.5" fill="currentColor"></circle>
            <circle cx="4" cy="18" r="1.5" fill="currentColor"></circle>
          </svg>
        </button>

        <!-- Numbered List -->
        <button
          type="button"
          @click="exec('insertOrderedList')"
          class="w-6 h-6 bg-white hover:bg-gray-100 rounded text-gray-800 shadow-xs flex items-center justify-center"
          title="Numbered List"
        >
          <svg class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <line x1="10" y1="6" x2="21" y2="6"></line>
            <line x1="10" y1="12" x2="21" y2="12"></line>
            <line x1="10" y1="18" x2="21" y2="18"></line>
            <path d="M4 6h2v4"></path>
            <path d="M4 14h2a1 1 0 0 1 1 1v1a1 1 0 0 1-1 1H4h3"></path>
          </svg>
        </button>

        <!-- Align dropdown -->
        <select
          @change="exec($event.target.value)"
          class="h-6 px-1.5 py-0 text-xs bg-white text-gray-800 rounded border-0 focus:ring-1 focus:ring-blue-300 cursor-pointer shadow-xs"
        >
          <option value="justifyLeft">Align Left</option>
          <option value="justifyCenter">Center</option>
          <option value="justifyRight">Align Right</option>
          <option value="justifyFull">Justify</option>
        </select>
      </div>

      <!-- Row 2 -->
      <div class="flex items-center flex-wrap gap-1">
        <!-- Fullscreen -->
        <button
          type="button"
          @click="toggleFullscreen"
          class="w-6 h-6 bg-white hover:bg-gray-100 rounded text-gray-800 shadow-xs flex items-center justify-center"
          :title="isFullscreen ? 'Exit Fullscreen' : 'Fullscreen'"
        >
          <svg class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <polyline points="15 3 21 3 21 9"></polyline>
            <polyline points="9 21 3 21 3 15"></polyline>
            <line x1="21" y1="3" x2="14" y2="10"></line>
            <line x1="3" y1="21" x2="10" y2="14"></line>
          </svg>
        </button>

        <!-- Undo -->
        <button
          type="button"
          @click="exec('undo')"
          class="w-6 h-6 bg-white hover:bg-gray-100 rounded text-gray-800 shadow-xs flex items-center justify-center"
          title="Undo"
        >
          <svg class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <polyline points="1 4 1 10 7 10"></polyline>
            <path d="M3.51 15a9 9 0 1 0 2.13-9.36L1 10"></path>
          </svg>
        </button>

        <!-- Redo -->
        <button
          type="button"
          @click="exec('redo')"
          class="w-6 h-6 bg-white hover:bg-gray-100 rounded text-gray-800 shadow-xs flex items-center justify-center"
          title="Redo"
        >
          <svg class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <polyline points="23 4 23 10 17 10"></polyline>
            <path d="M20.49 15a9 9 0 1 1-2.12-9.36L23 10"></path>
          </svg>
        </button>

        <!-- Help / Info -->
        <button
          type="button"
          class="w-6 h-6 bg-white hover:bg-gray-100 rounded text-gray-800 shadow-xs flex items-center justify-center font-bold"
          title="Help"
        >
          ?
        </button>

        <!-- Horizontal rule -->
        <button
          type="button"
          @click="exec('insertHorizontalRule')"
          class="w-6 h-6 bg-white hover:bg-gray-100 rounded text-gray-800 shadow-xs flex items-center justify-center font-bold"
          title="Horizontal Line"
        >
          —
        </button>
      </div>
    </div>

    <!-- Editable content area -->
    <div
      ref="editor"
      contenteditable="true"
      @input="onInput"
      @blur="onBlur"
      :class="['p-3 bg-white text-gray-800 focus:outline-none overflow-y-auto leading-relaxed', isFullscreen ? 'flex-1' : 'min-h-[140px] max-h-[300px]']"
    ></div>
  </div>
</template>

<script setup>
import { ref, onMounted, watch } from 'vue';

const props = defineProps({
  modelValue: {
    type: String,
    default: '',
  },
  placeholder: {
    type: String,
    default: '',
  },
});

const emit = defineEmits(['update:modelValue']);

const editor = ref(null);
const isFullscreen = ref(false);

const toggleFullscreen = () => {
  isFullscreen.value = !isFullscreen.value;
};

const exec = (command, value = null) => {
  if (editor.value) {
    editor.value.focus();
    document.execCommand(command, false, value);
    emit('update:modelValue', editor.value.innerHTML);
  }
};

const onInput = () => {
  if (editor.value) {
    emit('update:modelValue', editor.value.innerHTML);
  }
};

const onBlur = () => {
  if (editor.value) {
    emit('update:modelValue', editor.value.innerHTML);
  }
};

onMounted(() => {
  if (editor.value && props.modelValue) {
    editor.value.innerHTML = props.modelValue;
  }
});

watch(
  () => props.modelValue,
  (newVal) => {
    if (editor.value && editor.value.innerHTML !== newVal) {
      editor.value.innerHTML = newVal || '';
    }
  }
);
</script>
