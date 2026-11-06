<template>
<span v-if="checkHeaderObject(item.header)" class="flex font-medium text-sm text-gray-700 px-1 pt-  my-4 uppercase dark:text-light">{{item.header.title?tm(item.header.title):''}}</span>
<jet-nav-link  v-if="item.type==='route' && can(item.link?[item.link]:[])" :href="route(item.link)" :active="route().current(item.link) || isChildRoute(item.child)">
    <span aria-hidden="true" v-if="item.icon!=null">
        <component
      :is="getIcon(item.icon,  'solid')"
      class="h-6 w-6 text-blue-500 dark:text-light"
    />
    </span>
    <span class="text-sm"> {{tm(item.label)}} </span>
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
        isChildRoute(childArray){
            if(childArray){
                for(let i=0; i<childArray.length; i++){
                    if(route().current(childArray[i]))
                        return true
                }
            }
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
