<template>
    <div class="col-span-2 bg-white rounded-md dark:bg-darker">
        <div class="flex items-center justify-between p-4 border-b dark:border-primary">
            <h4 class="text-lg font-semibold text-gray-500 dark:text-light">{{tm(label)}}</h4>
            <div class="flex items-center space-x-2">
                <slot name="button"/>
            </div>
        </div>
        <div class="p-4" v-if="query">
            <Table :filters="queryBuilderProps.filters" :search="queryBuilderProps.search" :columns="queryBuilderProps.columns" :on-update="setQueryBuilder" :meta="query">
                <template #head>
                    <tr>
                        <template v-for="(header, index) in headers" :key="index">
                            <th
                                class="text-left"
                                @click.prevent="header.sort ? sortBy(header.key) : ''"
                                v-show="showColumn(header.key)"
                            >
                                {{tm(header.label)}}
                            </th>
                        </template>
                        <th v-if='action' class="text-right"></th>
                    </tr>
                </template>

                <template #body>
                    <tr v-for="(query,index) in query.data" :key="query.id">
                        <template v-if="showIndex">
                            <td 
                                class="text-left" 
                                v-show="showColumn('id')"
                            >
                                {{index+1}}
                            </td>
                        </template>
                        <template v-for="(column, index) in columns
                            .map(col => {
                                var split = col.split('.')
                                let callback
                                if(split) {
                                    if(split[1]=='status'){
                                        callback = {
                                            key: split[0],
                                            field: split[1]
                                        }
                                    }else{
                                        callback = {
                                            key: split[0],
                                            field: split[1]
                                        }
                                    }
                                }else{
                                    callback = {
                                        key: col
                                    }
                                }
                                return callback;
                            })
                        
                        " :key="index">
                            <td 
                                v-show="showColumn(column.key) && column.key != 'id'"
                            >
                                <template v-if="column.field">
                                    <template v-if="column.field=='status'">
                                        <span :class="query.is_active?'bg-primary-100 text-primary text-sm font-medium mr-2 px-2.5 py-0.5 rounded dark:bg-primary-100 dark:text-primary':'bg-red-100 text-red-800 text-sm font-medium mr-2 px-2.5 py-0.5 rounded dark:bg-red-200 dark:text-red-900' "> {{ query[column.key] ? tm('Active') : tm('Inactive') }}</span>
                                    </template>
                                    <template v-else>
                                        {{query[column.key][column.field]}}
                                    </template>
                                </template>
                                <template v-else>
                                    {{query[column.key]}}
                                </template>
                            </td>
                        </template>
                        <td v-if='action' class="py-4 px-6 text-sm font-medium text-right whitespace-nowrap">
                            <div class="flex justify-end">
                                <jet-dropdown :width="60">

                                    <template #trigger>
                                        <button class="flex text-sm border-2 border-transparent rounded-full focus:outline-none focus:border-gray-300 transition">
                                            <DotsHorizontalIcon class="h-5 w-5 text-blue-500 text-right" />
                                        </button>
                                    </template>
                                    
                                    <template #content>
                                        <transition name="fade">
                                            <div class="dropdown-rich-trigger-content dropdown-content-right dropdown-content-fit">
                                                <div class="flex justify-between items-center px-2 border dark:border-transparent dark:shadow-dark bg-white dark:bg-gray-700 text-gray-700 dark:text-gray-200 p-1 radius-3">
                                                    
                                                    <div v-if="action.delete" class="hover:bg-red-100 hover:text-red-500 dark:hover:text-gray-200 dark:hover:bg-red-500 dark:hover:bg-opacity-75 cursor-pointer text-sm px-2 radius-3">
                                                        <div class="flex items-center h-6" @click="destroy(route(action.delete.route,query.id))">
                                                            <TrashIcon class="h-5 w-5 text-red-500 text-right mr-1" />{{tm('delete')}}
                                                        </div>
                                                    </div>

                                                    <div v-if="action.edit" class="hover:bg-indigo-100 hover:text-indigo-500 dark:hover:text-gray-200 dark:hover:bg-indigo-500 dark:hover:bg-opacity-75 cursor-pointer text-sm px-2 radius-3">
                                                        <jet-nav-link :href="route(action.edit.route,query.id)">
                                                            <div class="flex items-center h-6">
                                                                <PencilAltIcon class="h-5 w-5 text-blue-500 text-right mr-1" />{{tm('edit')}}
                                                            </div>
                                                        </jet-nav-link>
                                                    </div>

                                                </div>
                                            </div>
                                        </transition>
                                    </template>

                                </jet-dropdown>
                            </div>
                        </td>
                    </tr>
                </template>
            </Table>
        </div>
    </div>
</template>

<script>
import {useI18n} from "vue-i18n";
import { lang } from "@/Lang";
import { InteractsWithQueryBuilder, Tailwind2 } from '@/Components/Datatables';
import Destory from "@/Libs/Action/Delete";
import {
    PencilAltIcon,
    DotsHorizontalIcon,
    TrashIcon,
    ViewBoardsIcon
} from '@heroicons/vue/solid'
import JetDropdown from '@/Jetstream/Dropdown'
import JetNavLink from '@/Jetstream/NavLink.vue'

export default {
    Table: Tailwind2.Table,
    mixins: [InteractsWithQueryBuilder,Destory],
    components: {
        Table: Tailwind2.Table,
        PencilAltIcon,
        DotsHorizontalIcon,
        TrashIcon,
        ViewBoardsIcon,
        JetDropdown,
        JetNavLink
    },
    props: {
        label: {
            type: String,
            default: ''
        },
        headers: {
            type: Array,
            default: []
        },
        columns: {
            type: Array,
            default: []
        },
        query: {
            type: Object,
            default: () => ({})
        },
        showIndex: {
            type: Boolean,
            default: false
        },
        action: {
            type: Object,
            default: null
        }
    },
    setup() {
        const {tm} = useI18n({
            inheritLocale: true,
            messages: lang
        })
        return {
            tm
        }
    },
    methods: {
        in_array(value, array) {
            for(var i = 0; i < array.length; i++) {
                if(array[i] == value) {
                    return true;
                }
            }
            return false;
        },
    }
}
</script>