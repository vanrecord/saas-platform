<template>
    <div class="flex items-left justify-left mx-auto text-center">
        <div >
            <label v-if="label" class="block font-medium text-sm pb-4 text-gray-700 dark:text-light">
                {{ label }}
            </label>
            <Toggle
                v-model="value"
                :value="value"
                :checked-value="true"
                :unchecked-value="false"
                feedback="Im useful helper out here, choose wisely"
                align="center-center"
                :class="value==true?'bg-primary border-primary text-white':'bg-red-800 border-red-800 text-white'"
            />
        </div>
        <div class="flex items-center justify-center text-center pl-2 pt-4">
            <pre class="text-xs">{{ value ? active : inactive }}</pre>
        </div> 
    </div>
</template>
<script>
import { Toggle } from '@flavorly/vanilla-components'
export default {
    components: {
        Toggle
    },
    props: {
        modelValue: {
            type: Boolean,
            default: true,
        },
        label: {
            type: String,
            default: null
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
    data() {
        return ({
            value: this.modelValue
        })
    },
    emits: ['update:modelValue'],
    watch: {
        'value': function (value) {
            this.$emit('update:modelValue', value)
        }
    },
}
</script>
