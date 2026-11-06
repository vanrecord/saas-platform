<template>
    <auth-layout title="Setting">
        <div class="px-4 py-4">
            <nav class="flex pt-4 mb-6" aria-label="Breadcrumb">
                <ol class="inline-flex items-center space-x-1 md:space-x-3">
                    <li class="inline-flex items-center">
                        <Link preserve-scroll href="#" class="inline-flex items-center text-sm font-medium text-gray-700 hover:text-gray-900 dark:text-gray-400 dark:hover:text-white">
                            <svg class="mr-2 w-4 h-4" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                                <path d="M10.707 2.293a1 1 0 00-1.414 0l-7 7a1 1 0 001.414 1.414L4 10.414V17a1 1 0 001 1h2a1 1 0 001-1v-2a1 1 0 011-1h2a1 1 0 011 1v2a1 1 0 001 1h2a1 1 0 001-1v-6.586l.293.293a1 1 0 001.414-1.414l-7-7z"></path>
                            </svg>
                            {{ tm('Home')}}
                        </Link>
                    </li>
                    <li>
                        <div class="flex items-center">
                            <svg class="w-6 h-6 text-gray-400" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                                <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path>
                            </svg>
                            <Link preserve-scroll class="ml-1 text-sm font-medium text-gray-700 hover:text-gray-900 md:ml-2 dark:text-gray-400 dark:hover:text-white">
                                {{ tm('setting') }}
                            </Link>
                        </div>
                    </li>
                </ol>
            </nav>

                <div class="col-span-2 bg-white rounded-md dark:bg-darker">
                    <div class="flex items-center justify-between p-4 border-b dark:border-primary">
                        <h4 class="text-lg font-semibold text-gray-500 dark:text-light">{{ tm('setting') }}</h4>
                        <div class="flex items-center justify-end ">
                            <form @submit.prevent="submit">
                                <save-button></save-button>
                                <back-button></back-button>
                            </form>
                        </div>
                    </div>
                    <div class="w-full bg-white">
                        <ul class="flex flex-wrap -mb-px text-center" id="myTab" data-tabs-toggle="#myTabContent" role="tablist">
                            <template  v-for="(screen , index) in screens" :key="index">
                                <li class="ml-8" role="presentation">
                                    <button @click="changeScreen(index)" :class="screen.active?'inline-block p-2 rounded-t-lg border-b-2 active text-blue-600 hover:text-blue-600 dark:text-blue-500 dark:hover:text-blue-400 border-blue-600 dark:border-blue-500':'inline-block p-2 rounded-t-lg border-b-2 border-transparent hover:text-gray-600 hover:border-gray-300 dark:hover:text-gray-300 text-gray-500 dark:text-gray-400 border-gray-100 dark:border-gray-700'">{{screen.name}}</button>
                                </li>
                            </template>
                        </ul>
                        <!-- <component 
                            :is="screens[activeScreen]?.component"
                            :form="form"
                            :myfile="myfile"
                            :myfileschool="myfileschool"
                        /> -->
                        <component 
                            :is="screens[activeScreen]?.component"
                            :form="form"
                            :myfile="myfile"
                        /> 
                    </div>
                </div>
        </div>
    </auth-layout>
</template>
<script>
import { defineComponent } from 'vue'
import AuthLayout from '@/Layouts/AuthLayout.vue'
import { Link } from '@inertiajs/inertia-vue3';
import { useI18n } from "vue-i18n"
import { lang } from "@/Lang"
import { useToast } from 'vue-toastification'
import BackButton from "@/Components/Button/Back.vue"
import SaveButton from "@/Components/Button/Save.vue"
import TextInput from "@/Components/Forms/TextInput.vue"
import SelectInput from "@/Components/Forms/SelectInput.vue"
import SwitchInput from "@/Components/Form/SwitchInput.vue"
import QRAttendance from '@/Pages/Setting/Tabs/QRAttendance.vue';
import QRSchool from '@/Pages/Setting/Tabs/QRSchool.vue';

export default defineComponent({
    components: {
        AuthLayout,
        Link,
        BackButton,
        SaveButton,
        TextInput,
        SelectInput,
        SwitchInput,
        QRAttendance,
        QRSchool
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
    props: {
        upload_background_qr: Array,
        // upload_background_qr_school: Array,
        // format_background_qr_school: Array,
    },
    data() {
        return {
            form: this.$inertia.form({
                'upload_background_qr'     : this.upload_background_qr??null,
                // upload_background_qr_school: this.upload_background_qr_school??null,
                // push_qr_school             : [],
                push_qr_attendance         : [],
                // format_background_qr_school: {
                //     margin_qr_a4 : {
                //         top_a4      : this.format_background_qr_school?.margin_qr_a4?.top_a4??null,
                //         left_a4     : this.format_background_qr_school?.margin_qr_a4?.left_a4??null,
                //     },
                //     margin_qr_a5 : {
                //         top_a5      : this.format_background_qr_school?.margin_qr_a5?.top_a5??null,
                //         left_a5     : this.format_background_qr_school?.margin_qr_a5?.left_a5??null,
                //     },
                //     margin_qr_stand : {
                //         top_stand   : this.format_background_qr_school?.margin_qr_stand?.right_a5??null,
                //         left_stand  : this.format_background_qr_school?.margin_qr_stand?.left_stand??null,
                //     },
                //     margin_name : {
                //         top_a4   : this.format_background_qr_school?.margin_name?.top_a4??null,
                //         top_a5   : this.format_background_qr_school?.margin_name?.top_a5??null,
                //         top_stand: this.format_background_qr_school?.margin_name?.top_stand??null,
                //     }
                    
                // }

            }),
            myfile:[],
            activeScreen : 0,
            screens      : [
                {
                    name        : 'QR Attendance',
                    component   : 'QRAttendance',
                    active      : true
                },
                // {
                //     name        : 'QR School',
                //     component   : 'QRSchool',
                //     active      : false
                // }
            ]
        }
    },
    created(){
        if(this.form.upload_background_qr){
            this.myfile = this.form.upload_background_qr;
        }
        // if(this.form.upload_background_qr_school){
        //     this.myfileschool = this.form.upload_background_qr_school;
        // }
    },

    methods: {
        submit(){
            this.form.post(this.route('upload-background-qr.store'), {
                preserveScroll: true,
                preserveState: true,
                onSuccess: () => {
                    // this.$inertia.visit(this.route('upload-background-qr.index'),'get');
                    this.toast.success("successfully");
                },
                onError: (result) => {
                   
                },
            })
        },
        changeScreen(index){
            this.screens.forEach(screen=>{
                screen.active = false;
            })
            this.screens[index].active = true;
            this.activeScreen = index;

        },
    }
})
</script>
