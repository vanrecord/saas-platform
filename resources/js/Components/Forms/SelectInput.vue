<template>
    <div>
        <label v-if="label" class="block font-medium text-sm pb-2 text-gray-700 dark:text-light">
            {{ label }}
            <span v-if="required=='required'" class="text-danger pull-right">&nbsp;*</span>
        </label>
        <RichSelect
            v-model="value"
            :options="options"
            :feedback="feedback"
            :placeholder="placeholder"
            :errors="error"
            :valueAttribute="valueAttribute"
            :textAttribute="textAttribute"
            :multiple="multiple"
            :tags="tags"
            :clearable="clearable"
            :disabled="disabled"
        />
    </div>

</template>
<script>
import { RichSelect } from '@flavorly/vanilla-components'
export default {
    components: {
        RichSelect
    },
    props : {
        label: {
            type: String,
            default: null
        },
        required: {
            type: String,
            default: null
        },
        modelValue: {
            type: Array,
        },
        placeholder: {
            type: String,
            default: null
        },
        feedback: {
            type: String,
            default: null
        },
        options: {
            type: Array,
            default: []
        },
        multiple: {
            type: Boolean,
            default: false
        },
        tags: {
            type: Boolean,
            default: false
        },
        valueAttribute: {
            type: String,
            default: 'value',
        },
        textAttribute: {
            type: String,
            default: 'text',
        },
        clearable: {
            type: Boolean,
        },
        error: {
            type: String,
            default: null
        },
        disabled   : {
            type   : Boolean,
            default: false
        }
    },
    data() {
        return ({
            value: this.modelValue
        })
    },
    watch: {
        'value': function (value) {
            this.$emit('update:modelValue', value)
        },
        'modelValue': function (value) {
            this.value = value
        }
    },
    emits: ['update:modelValue'],
}
</script>
