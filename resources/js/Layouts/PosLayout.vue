<template>
    <div class="pos-nav-bar-h-screen border-b border-gray-100 bg-blue-300">
        <div class="pos-nav-bar-h-screen flex items-center justify-between sm:px-6 lg:px-8">
            <a v-show="$page.props.company_logo" :href="route('pos.main.screen')">
                <img :src="$page.props.company_logo??'https://thumbs.dreamstime.com/b/upload-icon-vector-logo-template-illustration-design-upload-icon-vector-logo-template-illustration-design-193453107.jpg'" class="h-10 w-10 object-cover">
            </a>
            <div class="flex items-center justify-end flex-1">
                <nav class="hidden lg:text-gray-500 lg:tracking-wide lg:font-bold lg:text-xs lg:space-x-4 lg:flex">
                    <slot name="menu"></slot>
                    <Link v-if="route().current() == 'pos.home.screen'" :href="route('wholesale.home.screen')" class="lg:uppercase block text-lg h-16 leading-[4rem] border-b-4 border-transparent hover:text-red-700 hover:border-current">
                        POS
                    </Link>
                    <Link v-if="route().current() == 'wholesale.home.screen'" :href="route('pos.home.screen')" class="lg:uppercase block text-lg h-16 leading-[4rem] border-b-4 border-transparent hover:text-red-700 hover:border-current">
                        Wholesale
                    </Link>
                    <!-- <a href="" class="block text-lg h-16 leading-[4rem] border-b-4 border-transparent hover:text-red-700 hover:border-current">
                        Close Shift
                    </a>
                    <slot name="cash-box"></slot> -->
                </nav>
                <div class="flex items-center ml-8">
                    <div class="flex items-center border-gray-100 divide-gray-100 border-x">
                        <span>
                            <slot name="hold-list"></slot>
                        </span>
                        <!-- <span>
                            <a href="" class="block p-6 border-b-4 border-transparent hover:text-red-700">
                            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path>
                            </svg>
                            <span class="sr-only">Cart</span>
                            </a>
                        </span>
                        <span>
                            <a href="" class="block p-6 border-b-4 border-transparent hover:text-red-700">
                            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                            </svg>
                            <span class="sr-only"> Account </span>
                            </a>
                        </span> -->
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="pos-body-h-screen">
        <slot></slot>
    </div>
</template>

<script>
import { defineComponent } from 'vue'
import { Link } from '@inertiajs/inertia-vue3';

export default defineComponent({
    components: {
        Link
    },

    setup() {

    },

    methods: {

    }
})
</script>
