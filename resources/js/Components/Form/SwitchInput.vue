//create reusable switch input component with value true or false

<template>
    <div :class="$attrs.class">
        <label v-if="label" class="form-label" :for="id">{{ label }}
            <span v-if="required=='required'" class="text-danger pull-right">&nbsp;*</span>
        </label>
        <Toggle 
                v-onclick="passValue(value)"
                @change="passValue"
                v-model="value"
                :onLabel="active" :offLabel="customizeSwitch ? active : inactive" 
                :classes="{toggle: 'flex w-20 h-5 rounded-full relative cursor-pointer transition items-center box-content border-2 text-xs leading-none', 
                toggleOn: 'bg-primary border-primary justify-start text-white ',
                toggleOff: 'bg-red-800 border-red-800 justify-end text-white',
                label: 'text-left w-8 border-box whitespace-nowrap select-none mr-4 ml-2',}"
                :disabled="disabled"/>
        <div v-if="error" class="form-error">{{ error }}</div>
    </div>
</template>

<script>
import { v4 as uuid } from 'uuid'
import { Switch } from '@headlessui/vue'
import Toggle from '@vueform/toggle'

export default {
    components: {
        Switch,
        Toggle,
    },
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
        modelValue: Boolean,
        required: {
          type: String,
          default: 'text',
        },
        disabled: {
          type: Boolean,
          default: false,
        },
        customizeSwitch:{
            type: Boolean,
            default: false,
        },
        active:{
            type:String,
            default:'Active'
        },
        inactive:{
            type:String,
            default:'Inactive'
        }
    },
    data(){
        return{
            value:this.modelValue
        }
    },
    emits: ['update:modelValue'],
    watch: {
        value(val) {
            this.$emit('update:modelValue', val)
        },
        modelValue(val) {
            this.value = val
        }
    },
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
        passValue(value) {
            this.$emit('update:modelValue', value)
        },
    },
}
</script>
