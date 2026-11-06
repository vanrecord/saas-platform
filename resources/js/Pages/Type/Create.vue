<template>
    <auth-layout title="Create Type">
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
                            <Link :href="route('package.index')" class="ml-1 text-sm font-medium text-gray-700 hover:text-gray-900 md:ml-2 dark:text-gray-400 dark:hover:text-white">
                                {{tm('Type')}}
                            </Link>
                        </div>
                    </li>
                    <li>
                        <div class="flex items-center">
                            <svg class="w-6 h-6 text-gray-400" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                                <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path>
                            </svg>
                            <a href="#" class="ml-1 text-sm font-medium text-gray-700 hover:text-gray-900 md:ml-2 dark:text-gray-400 dark:hover:text-white">
                                {{ tm('Create') }}
                            </a>
                        </div>
                    </li>
                </ol>
            </nav>

            <div class="col-span-2 bg-white rounded-md dark:bg-darker">
                <div class="flex items-center justify-between p-4 border-b dark:border-primary">
                    <h4 class="text-lg font-semibold text-gray-500 dark:text-light">{{tm('Create_Type')}}</h4>
                </div>
                <div class="p-4">
                    <!-- <form-type 
                        :form="form"
                        :config_modules="config_modules"
                    /> -->
                    <form @submit.prevent="submit">        
                        <div class="flex flex-wrap -mb-8 -mr-6 p-4">
                            <text-input 
                                :label="tm('Name')"
                                v-model="form.name"
                                required="required"
                                :error="form.errors.name"
                                class="pb-2 pr-6 w-full lg:w-1/2"
                            />
                            <switch-input 
                                :label="tm('Status')"
                                v-model="form.is_active"
                                class="w-full lg:w-1/2"
                            />
                        </div>
                        <div class="flex items-center justify-between p-4 border-b dark:border-primary">
                            <h4 class="text-lg font-semibold text-gray-500 dark:text-light">Modules</h4>
                        </div>
                        <div>
                            <ul class="p-4">
                                <template v-for="(mod,key) in config_modules">
                                    <li>
                                        <JetCheckbox @change="checkMod($event,mod)" />
                                        <label v-if="key=='hr'" class="pl-4 uppercase">{{key}}</label>
                                        <label v-else class="pl-4 capitalize">{{key}}</label>
                                    </li>
                                    <template v-for="(action , index) in mod">
                                        <li class="pl-4 hidden">
                                            <JetCheckbox v-model:checked="form.modules" :value="index" />
                                            <label class="pl-4 capitalize">{{action}}</label>
                                        </li>
                                    </template>
                                </template>
                            </ul>
                        </div>
                        <div class="flex items-center justify-end px-4 py-4 space-x-2">
                            <div class="flex items-center justify-end px-4 py-4">
                                <save-button></save-button>
                                <back-button></back-button>
                            </div>
                        </div>
                    </form>
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
import TextInput from '@/Components/Forms/TextInput.vue'
import SwitchInput from '@/Components/Forms/SwitchInput.vue'
import FormType from '@/Pages/Type/Components/Form.vue'
import { Link } from '@inertiajs/inertia-vue3'
import BackButton from "@/Components/Button/Back.vue"
import SaveButton from "@/Components/Button/Save.vue"
import JetCheckbox from '@/Jetstream/Checkbox.vue';
export default defineComponent({
    components: {
        AuthLayout,
        TextInput,
        SwitchInput,
        FormType,
        Link,
        BackButton,
        SaveButton,
        JetCheckbox,
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
        'config_modules'
    ],
    data() {
        return {
            form: this.$inertia.form({
                name: null,
                is_active: true,
                modules: []
            })
        }
    },
    methods: {
        submit() {
            this.form.post(this.route('package.store'), {
                preserveScroll: true,
                preserveState: true,
                onSuccess: () => {
                    this.toast.success("Type created successfully");
                }
            })
        },
        checkMod(event, modul) {
            if (event.target.checked) {
                this.form.modules = [...this.form.modules, ...Object.keys(modul)]
            }else {
                Object.keys(modul).forEach((item) => this.removeModule(item))
            }
        },
        removeModule(elem) {
            var index = this.form.modules.indexOf(elem);
            if (index > -1) {
                this.form.modules.splice(index, 1)
            }
        }
    }
})
</script>
