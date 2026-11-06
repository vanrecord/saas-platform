<template>
    <auth-layout title="Dashboard">
        

        
    </auth-layout>
</template>

<script>
    import { defineComponent } from 'vue'
    import AuthLayout from '@/Layouts/AuthLayout.vue'
    import Welcome from '@/Jetstream/Welcome.vue'

    export default defineComponent({
        components: {
            AuthLayout,
            Welcome,
        },
    })
</script>
