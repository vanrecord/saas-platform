<template>
    <div :class="$attrs.class">
        <label v-if="label" class="form-label" :for="id">{{ label }}
            <span v-if="required=='required'" class="text-danger pb-2 pull-right">&nbsp;*</span>
        </label>
        <select :id="id" ref="input" v-bind="{ ...$attrs, class: null }" class="form-select w-full rounded-md focus:ring focus:ring-primary border border-gray-300" :class="{ error: error } + ' ' + selectClass" :value="modelValue" @input="$emit('update:modelValue', $event.target.value)" >
            <option v-for="(option,index) in options" :value="optionValue?option[optionValue]:index" selected v-show="option" :key="index">{{ optionLabel?option[optionLabel]:option }}</option>
        </select>
        <div v-if="error" class="form-error">{{ error }}</div>
    </div>
</template>

<script>
import { v4 as uuid } from 'uuid'

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
        options: Object,
        required: {
          type: String,
          default: 'text',
        },
        selectClass: {
            type: String,
            default: '',
        },
        optionLabel:{
            type: String,
            default: ''
        },
        optionValue:{
            default: '',
        }
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

