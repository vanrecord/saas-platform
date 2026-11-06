<template>
<div class=" relative">
    <jet-dropdown :width="width">
        <template #trigger>
            <button class="flex text-sm border-2 border-transparent rounded-full focus:outline-none focus:border-gray-300 transition">
                <CogIcon class="h-7 w-7 text-blue-500 text-right" />
            </button>
        </template>
        <template #content>
            <transition name="fade">
                <div class="absolute z-50 mt-2 rounded-md shadow-lg origin-top-right right-0" style="">
                    <div class="rounded-xl ring-1 ring-black ring-opacity-5 bg-white">
                        <div class="dropdown-rich-trigger-content dropdown-content-right dropdown-content-fit">
                            <div style="align-items: baseline;" class="border-separate border border-slate-400 pb-4 rounded-md flex justify-between items-center   bg-white dark:bg-dark dark:text-light radius-3  py-2">
                                <div class="px-3 text-11 radius-3 w-48">
                                    <nav class="list-none">
                                        <li v-for="item in settingItem" :key="item.id">
                                            <menu-dropdown-link  :item="item"/> 
                                        </li>
                        
                                    </nav>
                                </div>
                                <!-- <div class="px-3 text-11 radius-3 w-48">
                                    <nav class="list-none">
                                        <li v-for="item in settingItem" :key="item.id">
                                            <menu-dropdown-link :item="item"/> 
                                        </li>
                        
                                    </nav>
                                </div> -->
                            </div>
                        </div>
                    </div>
                </div>
            </transition>   
        </template>

    </jet-dropdown>
</div>
</template>

<script>
import {
    CogIcon
} from '@heroicons/vue/solid'
import JetDropdown from '@/Jetstream/Dropdown.vue'
import MenuDropdownLink from "@/Layouts/Menu/DropdownLink.vue"
import settings from "@/Layouts/Setting"
export default {
    name: 'settings-dropdown',
    props:{
        width:{
            type: String,
            default: 'w-full'
        },
        settingItem: {
            type: Object,
            default: null
        }
    },
    setup(){
        const settingItem = settings
        return {settingItem}
    },

    components: {
        CogIcon,
        JetDropdown,
        MenuDropdownLink
    },
}
</script>
