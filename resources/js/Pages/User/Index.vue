<template>
<Head title="User" />
<div class="px-4 py-4">
    <jet-banner />
    <nav class="flex pt-4 mb-6" aria-label="Breadcrumb">
        <ol class="inline-flex items-center space-x-1 md:space-x-3">
            <li class="inline-flex items-center">
                <a href="#" class="inline-flex items-center text-sm font-medium text-gray-700 hover:text-gray-900 dark:text-gray-400 dark:hover:text-white">
                    <svg class="mr-2 w-4 h-4" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                        <path d="M10.707 2.293a1 1 0 00-1.414 0l-7 7a1 1 0 001.414 1.414L4 10.414V17a1 1 0 001 1h2a1 1 0 001-1v-2a1 1 0 011-1h2a1 1 0 011 1v2a1 1 0 001 1h2a1 1 0 001-1v-6.586l.293.293a1 1 0 001.414-1.414l-7-7z"></path>
                    </svg>
                    {{tm('Home')}}
                </a>
            </li>
            <li>
                <div class="flex items-center">
                    <svg class="w-6 h-6 text-gray-400" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                        <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path>
                    </svg>
                    <a href="#" class="ml-1 text-sm font-medium text-gray-700 hover:text-gray-900 md:ml-2 dark:text-gray-400 dark:hover:text-white">
                        {{tm('user')}}
                    </a>
                </div>
            </li>
        </ol>
    </nav>

    <div class="col-span-2 bg-white rounded-md dark:bg-darker">
        <div class="flex items-center justify-between p-4 border-b dark:border-primary">
            <h4 class="text-lg font-semibold text-gray-500 dark:text-light">{{tm('user')}}</h4>
            <div class="flex items-center space-x-2">
                <Link preserve-scroll v-if="can(['user.create'])" :href="route('user.create')" class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 active:bg-gray-900 focus:outline-none focus:border-gray-900 focus:ring-gray-300 disabled:opacity-25 transition bg-primary hover:bg-primary-dark focus:outline-none focus:ring-2 focus:ring-primary focus:ring-offset-1 dark:focus:ring-offset-darker">
                Create User
                </Link>
            </div>
        </div>
        <div class="p-4">
             <Table :resource="users">
                <template #cell(id)="{index: key}">
                    {{++key}}
                </template>
                <template #cell(createdBy.name)="{ item: user }">
                    {{ user.created_by?.name }}
                </template>
                <template #cell(updatedBy.name)="{ item: user }">
                    {{ user.updated_by?.name }}
                </template>
                <template #cell(profile_photo_url)="{ item: user }">
                    <img class="h-8 w-8 rounded-full object-cover" :src="user.profile_photo_url">
                </template>
                <template #cell(is_active)="{ item: user }">
                    <span :class="user.is_active ?'bg-primary-100 text-primary text-sm font-medium mr-2 px-2.5 py-0.5 rounded dark:bg-primary-100 dark:text-primary':'bg-red-100 text-red-800 text-sm font-medium mr-2 px-2.5 py-0.5 rounded dark:bg-red-200 dark:text-red-900' "> {{ user.is_active ? tm('Active') : tm('Inactive') }}</span>
                </template>
                <template #cell(level_access)="{ item: user }">
                    <CheckCircleIcon v-show="user.level_access" class="h-5 w-5 text-green-500 text-right"></CheckCircleIcon>
                    <XCircleIcon v-show="!user.level_access" class="h-5 w-5 text-red-500 text-right" fill="none"></XCircleIcon>
                </template>
                <template #cell(actions)="{ item: user }">
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

                                        <div class="hover:bg-red-100 hover:text-red-500 dark:hover:text-gray-200 dark:hover:bg-red-500 dark:hover:bg-opacity-75 cursor-pointer text-sm px-2 radius-3">

                                            <div class="flex items-center h-6" v-if="can(['user.destroy'])" @click="destroy(route('user.destroy',`${user.id}`))">
                                                <TrashIcon class="h-5 w-5 text-red-500 text-right mr-1" />{{tm('delete')}}
                                            </div>
                                        </div>

                                        <div class="hover:bg-indigo-100 hover:text-indigo-500 dark:hover:text-gray-200 dark:hover:bg-indigo-500 dark:hover:bg-opacity-75 cursor-pointer text-sm px-2 radius-3">
                                            <jet-nav-link v-if="can(['user.edit'])" :href="route('user.edit',`${user.id}`)">
                                                <div class="flex items-center h-6">
                                                    <PencilAltIcon class="h-5 w-5 text-blue-500 text-right mr-1" />{{tm('edit')}}
                                                </div>
                                            </jet-nav-link>
                                        </div>
                                        <div class="hover:bg-indigo-100 hover:text-indigo-500 dark:hover:text-gray-200 dark:hover:bg-indigo-500 dark:hover:bg-opacity-75 cursor-pointer text-sm px-2 radius-3">
                                            <div class="flex items-center h-6"
                                                v-if="isSuperAdmin() || isAdmin()"
                                                @click="resetPassword(user.id)"
                                            >
                                                <RefreshIcon class="h-5 w-5 text-blue-500 text-right mr-1" />{{tm('reset')}}
                                            </div>
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
</template>

<script>
import {
    defineComponent
} from 'vue'
import AuthLayout from '@/Layouts/AuthLayout.vue'
import { Table } from '@/Components/Datatables'
import {
    useI18n
} from "vue-i18n";
import {
    lang
} from "@/Lang";
import JetDropdown from '@/Jetstream/Dropdown.vue'
import { Link } from '@inertiajs/inertia-vue3';
import JetBanner from '@/Jetstream/Banner.vue'
import JetButton from '@/Jetstream/Button.vue'
import JetNavLink from '@/Jetstream/NavLink.vue'
import Destory from "@/Libs/Action/Delete.vue"
import SwitchInput from "@/Components/Form/SwitchInput.vue"
import { Inertia } from '@inertiajs/inertia'
import { useToast } from "vue-toastification";

import {
    DotsHorizontalIcon,
    ViewBoardsIcon,
    PencilAltIcon,
    TrashIcon,
    RefreshIcon,
} from '@heroicons/vue/solid'

import {
    CheckCircleIcon,
    XCircleIcon
} from '@heroicons/vue/outline'

export default defineComponent({
    layout: AuthLayout,
    mixins: [Destory],
    components: {
        Table,
        JetDropdown,
        Link,
        JetBanner,
        DotsHorizontalIcon,
        ViewBoardsIcon,
        JetButton,
        CheckCircleIcon,
        XCircleIcon,
        PencilAltIcon,
        TrashIcon,
        JetNavLink,
        SwitchInput,
        RefreshIcon,
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
            tm,toast
        }
    },
    props: {
        users: Object,

    },

    data() {
        return {
            w60: 'w-60',
            form: this.$inertia.form({
                email_verified_at: null
            }),
        }
    },

    methods: {
        doMath(index) {
            return index + 1
        },
        transform(roles){
           if(_.isEmpty(roles)) return '';

           return  _.toString(_.map(roles,'name'))
        },
        isEnable(data){
            this.$swal({
                title: 'Are you sure?',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Yes, Verify it!',
                customClass: {
                    confirmButton: 'inline-flex items-center mx-2 px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 active:bg-gray-900 focus:outline-none focus:border-gray-900 focus:ring focus:ring-gray-300 disabled:opacity-25 transition bg-primary hover:bg-primary-dark focus:outline-none focus:ring-2 focus:ring-primary focus:ring-offset-1 dark:focus:ring-offset-darker',
                    cancelButton: 'inline-flex items-center justify-center px-4 py-2 bg-red-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-red-500 focus:outline-none focus:border-red-700 focus:ring focus:ring-red-200 active:bg-red-600 disabled:opacity-25 transition',
                },
                buttonsStyling: false,
                html: '<div>This action will make the user cannot do any action in system.</div>'
            }).then(result => {
                if(result.value){
                    if(data){
                        this.form.email_verified_at = data.verified
                    }
                    this.form.post(this.route('user.email_verified',data.id), {
                        preserveScroll: true,
                        preserveState: true,
                        onSuccess: (res) => { 
                            if(res){
                                this.toast.success("Email verified successfully",{
                                    position: "top-right",
                                    timeout: 5000,
                                });
                            }
                        },
                    })
                }else{
                    Inertia.reload({ only: ['users'], preserveScroll: true, preserveState: true })
                }
            })

        },
        resetPassword(id) {
            this.$swal({
                title: 'Are you sure?',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Yes, reset it!',
                customClass: {
                    confirmButton: 'inline-flex items-center mx-2 px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 active:bg-gray-900 focus:outline-none focus:border-gray-900 focus:ring focus:ring-gray-300 disabled:opacity-25 transition bg-primary hover:bg-primary-dark focus:outline-none focus:ring-2 focus:ring-primary focus:ring-offset-1 dark:focus:ring-offset-darker',
                    cancelButton: 'inline-flex items-center justify-center px-4 py-2 bg-red-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-red-500 focus:outline-none focus:border-red-700 focus:ring focus:ring-red-200 active:bg-red-600 disabled:opacity-25 transition',
                },
                buttonsStyling: false,
                html: '<div>This action will make the user reset password do any action in system.</div>',
            }).then(result => {
                if(result.value){
                    this.$inertia.post(`/user/reset/${id}`,{
                        preserveScroll:true,
                        preserveState: true,
                    },{
                        onSuccess: (res) => { 
                            if(res){
                                this.toast.success("Reset password successfully",{
                                    position: "top-right",
                                    timeout: 5000,
                                });
                            }
                        },
                        onError: (res) => {
                            console.log(res)
                        }
                    });
                }
            })
        }
    },
})
</script>