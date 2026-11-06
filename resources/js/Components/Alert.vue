<template>
    <transition
        enter-active-class="ease-out duration-1000"
        enter-from-class="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
        enter-to-class="opacity-100 translate-y-0 sm:scale-100"
        leave-active-class="ease-in duration-1000"
        leave-from-class="opacity-100 translate-y-0 sm:scale-100"
        leave-to-class="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95">
        <div
            :class="{'p-4 text-green-700 border rounded border-green-900/10 bg-green-50 absolute top-4 right-4 fadein': style === 'success', 'p-4 text-red-700 border rounded border-red-900/10 bg-red-50 absolute top-4 right-4 fadein':style === 'error'}"
            role="alert"
            v-show="show&&message !== undefined"
            >
            <strong class="text-sm font-medium">
                {{ message }}
            </strong>
        </div>
    </transition>
</template>

<style>
    .fadein {
        animation: fadein 0.5s;
    }
    @keyframes fadein {
        from {
            opacity: 0;
        }
        to {
            opacity: 1;
        }
    }
</style>

<script>
export default {
    data() {
        return {
            show: true,
            timeout: null
        }
    },
    created() {
        this.timeout = setTimeout(() => {
            this.show = false
        }, 5000)
    },
    computed: {
        style() {
            return this.$page?.props?.jetstream?.flash?.style || 'success'
        },
        message() {
            return this.$page?.props?.jetstream?.flash?.message
        },
    }
}
</script>
