<template>
<auth-layout title="Domains">
    <div class="px-4 py-4">
        <!-- component -->
        <nav class="flex flex pt-4 mb-6" aria-label="Breadcrumb">
            <ol class="inline-flex items-center space-x-1 md:space-x-3">
                <li class="inline-flex items-center">
                    <a href="#" class="inline-flex items-center text-sm font-medium text-gray-700 hover:text-gray-900 dark:text-gray-400 dark:hover:text-white">
                        <svg class="mr-2 w-4 h-4" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                            <path d="M10.707 2.293a1 1 0 00-1.414 0l-7 7a1 1 0 001.414 1.414L4 10.414V17a1 1 0 001 1h2a1 1 0 001-1v-2a1 1 0 011-1h2a1 1 0 011 1v2a1 1 0 001 1h2a1 1 0 001-1v-6.586l.293.293a1 1 0 001.414-1.414l-7-7z"></path>
                        </svg>
                        Home
                    </a>
                </li>
                <li>
                    <div class="flex items-center">
                        <svg class="w-6 h-6 text-gray-400" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                            <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path>
                        </svg>
                        <a href="#" class="ml-1 text-sm font-medium text-gray-700 hover:text-gray-900 md:ml-2 dark:text-gray-400 dark:hover:text-white">
                            Domain
                        </a>
                    </div>
                </li>
            </ol>

        </nav>
        <div class="col-span-2 bg-white rounded-md dark:bg-darker">
            <div class="flex items-center justify-between p-4 border-b dark:border-primary">
                <h4 class="text-lg font-semibold text-gray-500 dark:text-light">Domain</h4>
                <div class="flex items-center space-x-2">
                    <jet-nav-link :href="route('domain.create')" class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 active:bg-gray-900 focus:outline-none focus:border-gray-900 focus:ring focus:ring-gray-300 disabled:opacity-25 transition bg-primary hover:bg-primary-dark focus:outline-none focus:ring-2 focus:ring-primary focus:ring-offset-1 dark:focus:ring-offset-darker" style="color: white;">
                        Create Domain
                    </jet-nav-link>
                </div>
            </div>
            <div class="p-4">
                <Table :resource="domains">
                    <template #cell(id)="{index: key}">
                        {{++key}}
                    </template>
                    <template #cell(company)="{ item: domain }">
                        <span class="font-semibold text-gray-900 dark:text-gray-100">{{ domain.company }}</span>
                    </template>
                    <template #cell(tenant_id)="{ item: domain }">
                        <span class="text-xs font-mono text-gray-600 dark:text-gray-300">{{ domain.tenant_id }}</span>
                    </template>
                    <template #cell(domain)="{ item: domain }">
                        <span class="text-sm text-blue-600 dark:text-blue-400 font-medium">{{ domain.domain }}</span>
                    </template>
                    <template #cell(database)="{ item: domain }">
                        <span class="text-xs font-mono bg-gray-100 dark:bg-gray-800 px-2 py-1 rounded text-gray-700 dark:text-gray-300">
                            {{ domain.tenant?.tenancy_db_name || (domain.tenant_id ? ('tenant' + domain.tenant_id) : '-') }}
                        </span>
                    </template>
                    <template #cell(is_active)="{ item: domain }">
                        <span v-if="domain.is_active" class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800 dark:bg-green-800 dark:text-green-100">
                            Active
                        </span>
                        <span v-else class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800 dark:bg-red-800 dark:text-red-100">
                            Inactive
                        </span>
                    </template>
                    <template #cell(parent.company)="{ item: domain }">
                        {{domain.parent?.company}}
                    </template>
                    <template #cell(createdBy.name)="{ item: domain }">
                        {{ domain.created_by?.name }}
                    </template>
                    <template #cell(updatedBy.name)="{ item: domain }">
                        {{ domain.updated_by?.name }}
                    </template>
                    <template #cell(actions)="{ item: domain }">
                        <div class="flex items-center space-x-2">
                            <!-- Prominent Go To button -->
                            <button
                                type="button"
                                @click="goToSite(domain.domain)"
                                class="inline-flex items-center px-3 py-1.5 bg-blue-600 hover:bg-blue-700 active:bg-blue-800 text-white text-xs font-semibold rounded shadow transition duration-150 ease-in-out cursor-pointer focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-1"
                                title="Go To Tenant"
                            >
                                <GlobeAltIcon class="h-4 w-4 mr-1 text-white" />
                                Go To
                            </button>
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
                                                    <div class="flex items-center h-6" @click="destroy(domain)">
                                                        <TrashIcon class="h-5 w-5 text-red-500 text-right mr-1" />{{tm('delete')}}
                                                    </div>
                                                </div>
                                                <div v-if="isSuperAdmin()" :class="((domain.tenant?.maintenance_mode==null)?'hover:bg-red-100 hover:text-red-500 dark:hover:text-gray-200 dark:hover:bg-red-500 ':'hover:bg-green-100 hover:text-green-500 dark:hover:bg-green-500 ') + 'dark:hover:bg-opacity-75 cursor-pointer text-sm px-2 radius-3'">
                                                        <div class="flex items-center h-6" @click="maintenantMode(domain)">
                                                            <template v-if="domain.tenant?.maintenance_mode == null">
                                                                <StopIcon class="h-5 w-5 text-red-500 text-right mr-1" />{{tm('Stop')}}
                                                            </template>
                                                            <template v-else>
                                                                <PlayIcon class="h-5 w-5 text-green-500 text-right mr-1" />{{tm('Start')}}
                                                            </template>
                                                        </div>
                                                </div>
                                                <div class="hover:bg-indigo-100 hover:text-indigo-500 dark:hover:text-gray-200 dark:hover:bg-indigo-500 dark:hover:bg-opacity-75 cursor-pointer text-sm px-2 radius-3">
                                                    <jet-nav-link preserve-scroll v-if="can(['domain.edit'])" :href="route('domain.edit',`${domain.id}`)">
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
                    </template>
                </Table>
            </div>
        </div>
    </div>
</auth-layout>
</template>

<script>
import {
    defineComponent
} from 'vue'
import AuthLayout from '@/Layouts/AuthLayout.vue'
import JetNavLink from '@/Jetstream/NavLink.vue'
import JetDropdown from '@/Jetstream/Dropdown.vue'
import {
    PencilAltIcon,
    DotsHorizontalIcon,
    TrashIcon,
    GlobeAltIcon,
    PlayIcon,
    StopIcon,
} from '@heroicons/vue/solid'
import JetButton from '@/Jetstream/Button.vue'
import {
    useI18n
} from "vue-i18n";
import {
    lang
} from "@/Lang";
import { useToast } from "vue-toastification";
import { Table } from '@/Components/Datatables'
import axios from 'axios'


export default defineComponent({
    setup() {
        const {
            tm
        } = useI18n({
            inheritLocale: true,
            messages: lang
        })
        return {
            tm
        }
    },
    data() {
        return {
            protocol: '//',
            w60: 'w-60',
            database: null,
        }
    },
    components: {
        AuthLayout,
        Table,
        JetNavLink,
        PencilAltIcon,
        DotsHorizontalIcon,
        JetDropdown,
        JetButton,
        TrashIcon,
        GlobeAltIcon,
        StopIcon,
        PlayIcon,
    },
    props: {
        domains: Object
    },
    mounted() {
        this.protocol = location.protocol + this.protocol
    },
    methods: {
        goToSite(domain){
            let host = domain;
            if (!host.includes('.')) {
                host = host + '.' + window.location.hostname;
            }
            const port = window.location.port ? ':' + window.location.port : '';
            const url = window.location.protocol + '//' + host + port + '/login';
            window.open(url, "_blank");
        },
        doMath(index){
            return index+1
        },
        async clear(type,domain){   
            this.$swal({
                title: 'Are you sure?',
                // icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Yes, delete it!',
                customClass: {
                    confirmButton: 'inline-flex items-center mx-2 px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 active:bg-gray-900 focus:outline-none focus:border-gray-900 focus:ring focus:ring-gray-300 disabled:opacity-25 transition bg-primary hover:bg-primary-dark focus:outline-none focus:ring-2 focus:ring-primary focus:ring-offset-1 dark:focus:ring-offset-darker',
                    cancelButton: 'inline-flex items-center justify-center px-4 py-2 bg-red-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-red-500 focus:outline-none focus:border-red-700 focus:ring focus:ring-red-200 active:bg-red-600 disabled:opacity-25 transition',
                },
                buttonsStyling: false,
                html: '<div >This action <b>CANNOT</b> be undo ! This will clear <span class="text-red-500">'+type+'</span> of database <span class="text-red-500">'+
                        domain.tenant.tenancy_db_name+'</span></div><br/>Please type in database name to confirm.<br/><input id="database" class="swal2-input">',
                preConfirm: () => {
                    if(document.getElementById('database').value == ""){
                      this.$swal.showValidationMessage("The input is required!");
                    }
              }
            }).then(result => {
                if(result.value){
                    
                    this.$inertia.post('/domain/clear-data', {type: type, domain_id: domain.id, input: document.getElementById('database').value},{
                        preserveScroll:true,
                        onSuccess: (res) => { 
                            if(res){
                                useToast().success('Clear data successfully!');
                            }
                        },
                        onError: (res) => {
                            for(let i in res){                         
                                useToast().error(res[i])
                            }
                        }
                    })
                }
            })
        },
        destroy(domain){
            this.$swal({
                title: 'Are you sure?',
                // icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Yes, delete it!',
                customClass: {
                    confirmButton: 'inline-flex items-center mx-2 px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 active:bg-gray-900 focus:outline-none focus:border-gray-900 focus:ring focus:ring-gray-300 disabled:opacity-25 transition bg-primary hover:bg-primary-dark focus:outline-none focus:ring-2 focus:ring-primary focus:ring-offset-1 dark:focus:ring-offset-darker',
                    cancelButton: 'inline-flex items-center justify-center px-4 py-2 bg-red-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-red-500 focus:outline-none focus:border-red-700 focus:ring focus:ring-red-200 active:bg-red-600 disabled:opacity-25 transition',
                },
                buttonsStyling: false,
                html: '<div>This action <b>CANNOT</b> be undone! This will delete domain <span class="text-red-500">'+
                        domain.domain+'</span></div><br/>Please type the domain name to confirm.<br/><input id="delete_database" class="swal2-input">',
                preConfirm: () => {
                    if(document.getElementById('delete_database').value == ""){
                      this.$swal.showValidationMessage("The input is required!");
                    }
              }
            }).then(result => {
                if(result.value){

                    this.$inertia.post('/domain/delete', { domain_id: domain.id, input: document.getElementById('delete_database').value},{
                        preserveScroll:true,
                        onSuccess: (res) => { 
                            useToast().success('Delete domain successfully!');
                        },
                        onError: (res) => {
                            for(let i in res){                         
                                useToast().error(res[i])
                            }
                        }
                    })
                }
            })
        },
        maintenantMode(domain) {
            this.$swal({
                title: 'Are you sure?',
                text: domain.tenant.maintenance_mode?"The domain will can use again!":"The domain will cannot use after stop!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: domain.tenant.maintenance_mode?'Yes, start it!':'Yes, stop it!',
                customClass: {
                    confirmButton: 'inline-flex items-center mx-2 px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 active:bg-gray-900 focus:outline-none focus:border-gray-900 focus:ring focus:ring-gray-300 disabled:opacity-25 transition bg-primary hover:bg-primary-dark focus:outline-none focus:ring-2 focus:ring-primary focus:ring-offset-1 dark:focus:ring-offset-darker',
                    cancelButton: 'inline-flex items-center justify-center px-4 py-2 bg-red-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-red-500 focus:outline-none focus:border-red-700 focus:ring focus:ring-red-200 active:bg-red-600 disabled:opacity-25 transition',
                },
                buttonsStyling: false,
            }).then(result => {
                if(result.value){
                    this.$inertia.post('/domain/maintenance-mode', {tenantId: domain.tenant_id}, {
                        preserveScroll:true,
                        onSuccess: (res) => { 
                            if(res){ 
                                useToast().success(domain.tenant.maintenance_mode?'Start domain successfully!':'Stop domain successfully!');
                            }
                        },
                        onError: (res) => { useToast().error(res.error); }
                    })

                    
                }


            })
        },
        backup(domain) {
            this.$swal({
                title: 'Are you sure?',
                text: "This action will backup the data of the domain!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Yes, backup it!',
                customClass: {
                    confirmButton: 'inline-flex items-center mx-2 px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 active:bg-gray-900 focus:outline-none focus:border-gray-900 focus:ring focus:ring-gray-300 disabled:opacity-25 transition bg-primary hover:bg-primary-dark focus:outline-none focus:ring-2 focus:ring-primary focus:ring-offset-1 dark:focus:ring-offset-darker',
                    cancelButton: 'inline-flex items-center justify-center px-4 py-2 bg-red-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-red-500 focus:outline-none focus:border-red-700 focus:ring focus:ring-red-200 active:bg-red-600 disabled:opacity-25 transition',
                },
                buttonsStyling: false,
            }).then(result => {
                if(result.value){
                    this.$inertia.post('/domain/backup', {domain_id: domain.id}, {
                        preserveScroll:true,
                        onSuccess: (res) => { 
                            if(res){ 
                                useToast().success('Backup successfully');
                            }
                        },
                        onError: (res) => { useToast().error(res.error); }
                    })
                }


            })
        },
    },
})
</script>
