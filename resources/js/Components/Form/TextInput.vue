<template>
  <div :class="$attrs.class">
    <label v-if="label" class="block font-medium text-sm pb-2 text-gray-700 dark:text-light" :for="id">{{ label }}
        <span v-if="required=='required'" class="text-danger pull-right">&nbsp;*</span>
    </label>
    <input :id="id" ref="input" v-bind="{ ...$attrs, class: null }" class="w-full rounded-md focus:ring focus:ring-primary-100 border border-gray-300" :class="{ error: error } + ' ' + inputClass" :type="type" :value="modelValue" @input="$emit('update:modelValue', $event.target.value)" :disabled="disabled"
    />
    <div class="flex" v-if="info">
        <InformationCircleIcon class="h-4 w-4 text-gray-400 text-right mr-1" /> 
        <span class="text-gray-400 text-sm"> {{ info }} </span>
    </div> 
    <div v-if="error" class="form-error">{{ error }}</div>
  </div>
</template>

<script>
import { v4 as uuid } from 'uuid'
import {
    InformationCircleIcon,
} from '@heroicons/vue/solid'

export default {
  inheritAttrs: false,
  props: {
    id: {
      type: String,
      default() {
        return `text-input-${uuid()}`
      },
    },
    type: {
      type: String,
      default: 'text',
    },
    error: String,
    label: String,
    modelValue: null,
    required: {
      type: String,
      default: '',
    },
    disabled: {
      type: Boolean,
      default: false,
    },
    inputClass: {
      type: String,
      default: '',
    },
    info:{
      type: String,
      default: ''
    }
  },
  components: {
        InformationCircleIcon
    },
  emits: ['update:modelValue'],
  methods: {
    focus() {
      this.$refs.input.focus()
    },
    select() {
      this.$refs.input.select()
    },
    setSelectionRange(start, end) {
      this.$refs.input.setSelectionRange(start, end)
    },
  },
}
</script>