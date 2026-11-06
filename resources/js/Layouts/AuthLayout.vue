<template>
<div>

    <Head :title="title" />

    <jet-banner />
    <div :class="{ 'dark': isDark}">
        <div class="flex h-screen antialiased text-gray-900 bg-gray-100 dark:bg-dark dark:text-light">
            <!-- Loading screen -->
            <!-- <div x-ref="loading" class="fixed inset-0 z-50 flex items-center justify-center text-2xl font-semibold text-white bg-primary-darker">
                Loading.....
            </div> -->
            <side-bar />
            <div class="flex-1 h-full overflow-x-hidden overflow-y-auto">
                <nav-bar />

                <main>
                    <slot></slot>
                </main>
            </div>

        </div>
    </div>

</div>
</template>

<script>
import {
    defineComponent
} from 'vue'
import {
    isDark,
    toggleDarkMode,
} from '@/Composables'
import NavBar from '@/Layouts/Menu/NavBar.vue'
import SideBar from '@/Layouts/SideBar.vue'
import JetBanner from '@/Jetstream/Banner.vue'
import {
    Head,
    Link
} from '@inertiajs/inertia-vue3';

export default defineComponent({
    props: {
        title: String,
    },

    components: {
        Head,
        NavBar,
        SideBar,
        JetBanner
    },

    data() {
        return {
            showingNavigationDropdown: false,
            isDark: {
                isDark
            }.isDark
        }
    },
})
</script>
