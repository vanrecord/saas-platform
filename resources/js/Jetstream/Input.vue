<template>
    <input class="w-full px-4 py-2 border border-gray-300 rounded-md dark:bg-darker dark:border-gray-300 focus:outline-none focus:ring focus:ring-primary-100 dark:focus:ring-primary-darker dark:border-primary-darker" :value="modelValue" @input="$emit('update:modelValue', $event.target.value)" ref="input">
</template>

<script>
    import { defineComponent } from 'vue'

    export default defineComponent({
        props: ['modelValue'],

        emits: ['update:modelValue'],

        methods: {
            focus() {
                this.$refs.input.focus()
            }
        }
    })
</script>
