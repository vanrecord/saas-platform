<template>
    <div class="flex flex-wrap">
        <div class="flex">
        <label v-if="label" class="block font-medium text-sm pb-2 text-gray-700 dark:text-light mr-2 w-12">
            {{ label }}
            <span v-if="required=='required'" class="text-danger pull-right">&nbsp;*</span>
        </label>
        <Input
            :type="type"
            :placeholder="placeholder"
            :feedback="feedback"
            :errors="error"
            :class="inputClass"
            v-model="value"
            @input="$emit('update:modelValue', $event.target.value)"
            :disabled="disabled"
            class="focus:ring focus:ring-primary-100 border border-gray-100 shadow-none"
        />
        </div>
    </div>
</template>
<script>
import { v4 as uuid } from 'uuid'
import { Input } from '@flavorly/vanilla-components'
export default {
    components : {
        Input
    },
    props : {
        modelValue : {
            type   : String,
            default: null
        },
        type       : {
            type   : String,
            default: 'text',
        },
        label      : {
            type   : String,
            default: null
        },
        required   : {
            type   : String,
            default: ''
        },
        placeholder: {
            type   : String,
            default: ''
        },
        feedback   : {
            type   : String,
            default: ''
        },
        error      : {
            type   : String,
            default: null,
        },
        inputClass : {
            type   : String,
            default: ''
        },
        disabled   : {
            type   : Boolean,
            default: false,
        }
    },
    data() {
        return {
            value: this.modelValue
        }
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
