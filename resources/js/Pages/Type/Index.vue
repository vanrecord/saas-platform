<template>
    <auth-layout title="Type">
        <div class="px-4 py-4">
            <nav class="flex pt-4 mb-6" aria-label="Breadcrumb">
                <ol class="inline-flex items-center space-x-1 md:space-x-3">
                    <li class="inline-flex items-center">
                        <Link :href="route('dashboard.index')" class="inline-flex items-center text-sm font-medium text-gray-700 hover:text-gray-900 dark:text-gray-400 dark:hover:text-white">
                            <svg class="mr-2 w-4 h-4" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                                <path d="M10.707 2.293a1 1 0 00-1.414 0l-7 7a1 1 0 001.414 1.414L4 10.414V17a1 1 0 001 1h2a1 1 0 001-1v-2a1 1 0 011-1h2a1 1 0 011 1v2a1 1 0 001 1h2a1 1 0 001-1v-6.586l.293.293a1 1 0 001.414-1.414l-7-7z"></path>
                            </svg>
                            {{tm('Home')}}
                        </Link>
                    </li>
                    <li>
                        <div class="flex items-center">
                            <svg class="w-6 h-6 text-gray-400" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                                <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path>
                            </svg>
                            <a href="#" class="ml-1 text-sm font-medium text-gray-700 hover:text-gray-900 md:ml-2 dark:text-gray-400 dark:hover:text-white">
                                {{tm('Type')}}
                            </a>
                        </div>
                    </li>
                </ol>
            </nav>

            <div class="col-span-2 bg-white rounded-md dark:bg-darker">
                <div class="flex items-center justify-between p-4 border-b dark:border-primary">
                    <h4 class="text-lg font-semibold text-gray-500 dark:text-light">{{tm('Type')}}</h4>
                    <div class="flex items-center space-x-2">
                        <Link preserve-scroll v-if="can(['package.create'])" :href="route('package.create')" class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 active:bg-gray-900 focus:outline-none focus:border-gray-900 focus:ring-gray-300 disabled:opacity-25 transition bg-primary hover:bg-primary-dark focus:outline-none focus:ring-2 focus:ring-primary focus:ring-offset-1 dark:focus:ring-offset-darker">
                            {{tm('Create_Type')}}
                        </Link>
                    </div>
                </div>
                <div class="p-4">
                    <Table :resource="types">
                        <template #cell(id)="{index: key}">
                            {{++key}}
                        </template>
                        <template #cell(is_active)="{ item: type }">
                            <span :class="type.is_active ?'bg-primary-100 text-primary text-sm font-medium mr-2 px-2.5 py-0.5 rounded dark:bg-primary-100 dark:text-primary':'bg-red-100 text-red-800 text-sm font-medium mr-2 px-2.5 py-0.5 rounded dark:bg-red-200 dark:text-red-900' "> {{ type.is_active ? tm('Active') : tm('Inactive') }}</span>
                        </template>
                        <template #cell(createdBy.name)="{ item: type }">
                            {{ type.created_by?.name }}
                        </template>
                        <template #cell(updatedBy.name)="{ item: type }">
                            {{ type.updated_by?.name }}
                        </template>
                        <template #cell(actions)="{ item: type }">
                            <jet-dropdown :width="w60" align="left">
                                <template #trigger>
                                    <button class="flex text-sm border-2 border-transparent rounded-full focus:outline-none focus:border-gray-300 transition">
                                        <DotsHorizontalIcon class="h-5 w-5 text-blue-500 text-right" />
                                    </button>
                                </template>

                                <template #content>
                                    <transition name="fade">
                                        <div class="dropdown-rich-trigger-content dropdown-content-right dropdown-content-fit">
                                            <div class="flex justify-between items-center px-2 border dark:border-transparent dark:shadow-dark bg-white dark:bg-gray-700 text-gray-700 dark:text-gray-200 p-1 radius-3">
                                                <div v-if="type.domains_count==0" class="hover:bg-red-100 hover:text-red-500 dark:hover:text-gray-200 dark:hover:bg-red-500 dark:hover:bg-opacity-75 cursor-pointer text-sm px-2 radius-3">
                                                    <div class="flex items-center h-6" @click="destroy(route('package.destroy',`${type.id}`))">
                                                        <TrashIcon class="h-5 w-5 text-red-500 text-right mr-1" />{{tm('delete')}}
                                                    </div>
                                                </div>

                                                <div class="hover:bg-indigo-100 hover:text-indigo-500 dark:hover:text-gray-200 dark:hover:bg-indigo-500 dark:hover:bg-opacity-75 cursor-pointer text-sm px-2 radius-3">
                                                    <jet-nav-link :href="route('package.edit',`${type.id}`)">
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
                        </template>
                    </Table>
                </div>

            </div>
        </div>
    </auth-layout>
</template>
<script>
import { defineComponent } from 'vue'
import AuthLayout from '@/Layouts/AuthLayout.vue'
import { useI18n } from "vue-i18n"
import { lang } from "@/Lang"
import { useToast } from 'vue-toastification'
import { Table } from '@/Components/Datatables'
import { Link } from '@inertiajs/inertia-vue3'
import {
    DotsHorizontalIcon,
    PencilAltIcon,
    TrashIcon,
} from '@heroicons/vue/solid'
import JetDropdown from '@/Jetstream/Dropdown.vue'
import JetNavLink from '@/Jetstream/NavLink.vue'
import Destory from "@/Libs/Action/Delete.vue"
export default defineComponent({
    mixins: [Destory],
    components: {
        AuthLayout,
        Table,
        Link,
        DotsHorizontalIcon,
        PencilAltIcon,
        TrashIcon,
        JetDropdown,
        JetNavLink
    },
    setup() {
        const {
            tm
        } = useI18n({
            inheritLocale: true,
            messages: lang
        })
        
        const toast = useToast();
        
        return {
            tm,
            toast
        }
    },

    props: [
        'types'
    ],
    
    data() {
        return {
            w60: 'w-60',
        }
    },
    
})
</script>
