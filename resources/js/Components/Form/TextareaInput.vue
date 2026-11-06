//create reusable textarea input component
<template>
    <div :class="$attrs.class">
        <label v-if="label" class="block font-medium text-sm text-gray-700 dark:text-light" :for="id">{{ label }}
            <span v-if="required=='required'" class="text-danger pull-right">&nbsp;*</span>
        </label>
        <textarea :id="id" ref="input" v-bind="{ ...$attrs, class: null }" :rows="rows" class="form-textarea w-full rounded-md focus:ring focus:ring-primary-100 border border-gray-300" :class="{ error: error }" :type="type" :value="modelValue" @input="$emit('update:modelValue', $event.target.value)" ></textarea>
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
        modelValue: String,
        required: {
          type: String,
          default: 'text',
        },
        rows: {
          type: Number,
          default: 4,
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
