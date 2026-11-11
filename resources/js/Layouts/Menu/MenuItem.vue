<template>
<span v-if="checkHeaderObject(item.header)" class=" flex font-medium bg-indigo-50 text-sm text-gray-400 px-1 pt-4 pb-4 my-4 uppercase dark:text-light">{{item.header.title?tm(item.header.title):''}}</span>
<jet-nav-link v-if="item.type==='route' && can(item.link?[item.link]:[])" :href="route(item.link)" :active="isUrl(item.link) || isUrl(item.parent) || isUrl(item.seg)">
    <span aria-hidden="true" v-if="item.icon!=null">
        <component
      :is="getIcon(item.icon,  'solid')"
      class="h-6 w-6 text-blue-500 dark:text-light"
    />
    </span>
    <span class="ml-2 text-md"> {{tm(item.label)}} </span>
</jet-nav-link>
</template>

<script>
import JetNavLink from '@/Jetstream/NavLink.vue'
import * as solid from '@heroicons/vue/solid'
import * as outline from '@heroicons/vue/outline'
import {useI18n} from "vue-i18n";
import {lang} from "@/Lang";

export default {
    props: {
        item: {
            type: Object,
            default: null
        }
    },
    setup(){
        const {tm} = useI18n({
        inheritLocale: true,
            messages: lang
        })
        return {tm}
    },
    components: {
        JetNavLink,

    },
    computed: {
        icons() {
            return {
                solid,
                outline
            }
        },
    },
    methods: {
        getIcon(name, type) {
            return this.icons[type][name]
        },
        checkHeaderObject(obj){
            if(_.isString(obj) || typeof obj === 'undefined'){
                return false ;
            }else{
                return this.can(obj.action)
            }
        },
        isUrl(...urls) {
            let currentUrl = this.$page.url.substr(1)
            if (urls[0] === '') {
                return currentUrl === ''
            }
            return urls.filter((url) => currentUrl.startsWith(url)).length
            },
    }
    
}
</script>
