<template>
    <div v-if="isSuperAdmin()" class="flex-1 h-full bg-gray-100 overflow-x-hidden overflow-y-auto">
        <div class="px-6 lg:px-8 pt-8">
            <Link :href="route('dashboard.index')" class="">
                <arrow-left-icon class="h-6 w-6 text-blue-500 dark:text-light" />
            </Link>
        </div>
        <div class="min-h-screen">
            <Head title="API Tokens" />
            <div>
                <div class="max-w-7xl mx-auto py-10 sm:px-6 lg:px-8">
                    <api-token-manager  :tokens="tokens"
                                        :available-permissions="availablePermissions"
                                        :default-permissions="defaultPermissions" />
                </div>
            </div>
        </div>
    </div>
    <div v-else class="relative flex min-h-screen flex-col items-center justify-center overflow-hidden py-6 sm:py-12 bg-white">
        <div class="max-w-xl px-5 text-center">
            <h2 class="mb-2 text-[42px] font-bold text-zinc-800">403 | forbidden</h2>
            <p class="mb-2 text-lg text-zinc-500">Please contact your administrator to check permission.</p>
            <back-button></back-button>
        </div>
    </div>
</template>

<script>
    import { defineComponent } from 'vue'
    import ApiTokenManager from '@/Pages/API/Partials/ApiTokenManager.vue'
    import AppLayout from '@/Layouts/AppLayout.vue'
    import { ArrowLeftIcon } from '@heroicons/vue/solid'
    import BackButton from "@/Components/Button/Back.vue"
    import { Head, Link } from '@inertiajs/inertia-vue3';

    export default defineComponent({
        props: [
            'tokens',
            'availablePermissions',
            'defaultPermissions',
        ],

        components: {
            ApiTokenManager,
            AppLayout,
            Head,
            Link,
            BackButton,
            ArrowLeftIcon,
        },
    })
</script>
