<template>
    <Link :href="href" :class="classes" preserve-scroll>
        <slot />
    </Link>
</template>

<script>
    import { defineComponent } from 'vue'
    import { Link } from '@inertiajs/inertia-vue3';

    export default defineComponent({
        components: {
            Link,
        },
        props: ['href', 'active'],

        computed: {
            classes() {
                return this.active
                    ? 'flex items-center p-2 text-gray-500 transition-colors rounded-md dark:text-light hover:bg-primary-100 dark:hover:bg-primary bg-primary-100 dark:bg-primary'
                    : 'flex items-center p-2 text-gray-500 transition-colors rounded-md dark:text-light dark:hover:bg-primary'
            }
        }
    })
</script>
