<template>
  <div :class="$attrs.class || 'w-full'">
    <label v-if="label" class="block font-medium text-sm pb-2 text-gray-700 dark:text-light">
      {{ label }}
      <span v-if="required == 'required'" class="text-danger pull-right">&nbsp;*</span>
    </label>
    <DatePicker
      v-model.string="value"
      :masks="masks"
      :min-date="minDate"
      :max-date="maxDate"
      mode="date"
      :popover="{ visibility: 'focus' }"
    >
      <template #default="{ inputValue, inputEvents }">
        <div class="relative w-full">
          <input
            :value="inputValue"
            v-on="inputEvents"
            :placeholder="placeholder || 'YYYY-MM-DD'"
            class="w-full rounded-md border border-gray-300 py-2 pl-3 pr-10 text-sm text-gray-700 focus:outline-none focus:ring focus:ring-primary-100 dark:bg-gray-800 dark:text-gray-200 dark:border-gray-600"
            :class="[{ 'border-red-500 ring-1 ring-red-500': error }, inputClass]"
          />
          <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center pr-3">
            <CalendarIcon class="h-5 w-5 text-gray-400" />
          </div>
        </div>
      </template>
    </DatePicker>
    <div v-if="error" class="form-error">{{ error }}</div>
  </div>
</template>

<script>
import { DatePicker } from 'v-calendar';
import 'v-calendar/dist/style.css';
import { CalendarIcon } from '@heroicons/vue/solid';

export default {
  inheritAttrs: false,
  components: {
    DatePicker,
    CalendarIcon,
  },
  props: {
    label: {
      type: String,
      default: null,
    },
    required: {
      type: String,
      default: null,
    },
    modelValue: {
      default: null,
    },
    placeholder: {
      type: String,
      default: null,
    },
    error: {
      type: String,
      default: null,
    },
    minDate: {
      type: [String, Date, Object],
      default: null,
    },
    maxDate: {
      type: [String, Date, Object],
      default: null,
    },
    inputClass: {
      type: String,
      default: '',
    },
  },
  emits: ['update:modelValue'],
  data() {
    return {
      value: this.modelValue,
      masks: {
        input: 'YYYY-MM-DD',
        modelValue: 'YYYY-MM-DD',
      },
    };
  },
  watch: {
    value(val) {
      if (val !== this.modelValue) {
        this.$emit('update:modelValue', val);
      }
    },
    modelValue(val) {
      if (val !== this.value) {
        this.value = val;
      }
    },
  },
};
</script>
