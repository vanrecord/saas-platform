<template>
<div>
    <div :class="{ 'dark': isDark}">
        <div class="flex h-screen antialiased text-gray-900 bg-gray-100 dark:bg-dark dark:text-light">
            <div class="flex-1 h-full overflow-x-hidden overflow-y-auto">
               <div class="px-8 pt-8">
                    <Link href="/admin" class="">
                <arrow-left-icon class="h-6 w-6 text-blue-500 dark:text-light" />
                </Link>
               </div>
              
                <main>
                    <Alert/>
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
import JetBanner from '@/Jetstream/Banner.vue'
import Alert from '@/Components/Alert.vue'
import { Link } from '@inertiajs/inertia-vue3'
import { ArrowLeftIcon } from '@heroicons/vue/solid'


export default defineComponent({
    props: {
        title: String,
    },

    components: {
        JetBanner,
        Alert,
        ArrowLeftIcon,
        Link
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
