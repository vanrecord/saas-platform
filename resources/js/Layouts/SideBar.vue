<template>
<aside class="flex-shrink-0 hidden w-64 bg-white border-r dark:border-primary-darker dark:bg-darker md:block">
    <div class="flex flex-col h-full">
        <nav aria-label="Main" class="flex-1 px-2 py-4 space-y-2 overflow-y-hidden hover:overflow-y-auto">
             <template v-for="item in sidebarItem" :key="item">
                <menu-item :item="item"/>
            </template>
        </nav>
    </div>
</aside>
</template>

<script>
import JetNavLink from '@/Jetstream/NavLink.vue'
import {
    CalculatorIcon,
    ViewGridIcon
} from '@heroicons/vue/solid'
import MenuItem from "@/Layouts/Menu/MenuItem.vue" 
import sidebar from "@/Layouts/Navs"

export default {
    props: {
        user: Object
    },
    setup(){
        const sidebarItem = sidebar
        return {sidebarItem}
    },
    components: {
        JetNavLink,
        CalculatorIcon,
        ViewGridIcon,
        MenuItem
    },
    data() {
        return {
            open: false, 
        }
    },
    methods:{
        activeChild(url){
            this.open = this.$page.url.startsWith(url)
        }
    }
}
</script>
