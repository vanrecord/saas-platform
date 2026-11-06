<template>
<div class="relative flex min-h-screen flex-col items-center justify-center overflow-hidden py-6 sm:py-12 bg-white">
    <div class="max-w-xl px-5 text-center">
        <h2 class="mb-2 text-[42px] font-bold text-zinc-800">403 | forbidden</h2>
        <p class="mb-2 text-lg text-zinc-500">Please contact your administrator to check permission.</p>
        <back-button></back-button>
    </div>
</div>
</template>

<script>
import BackButton from "@/Components/Button/Back.vue";
import {
    defineComponent
} from 'vue'

export default defineComponent({
    components: {
        BackButton
    },
    props: {
        status: Number,
    },
    computed: {
        title() {
            return {
                503: '503: Service Unavailable',
                500: '500: Server Error',
                404: '404: Page Not Found',
                403: '403: Forbidden',
            } [this.status]
        },
        description() {
            return {
                503: 'Sorry, we are doing some maintenance. Please check back soon.',
                500: 'Whoops, something went wrong on our servers.',
                404: 'Sorry, the page you are looking for could not be found.',
                403: 'Sorry, you are forbidden from accessing this page.',
            } [this.status]
        },
    },
})
</script>