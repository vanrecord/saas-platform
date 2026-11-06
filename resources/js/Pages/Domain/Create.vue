<template>
<auth-layout title="Dashboard">
    <div class="px-4 py-4">

        <Head title="Create Domain" />
        <nav class="flex flex pt-4 mb-6" aria-label="Breadcrumb">
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
                        <Link :href="route('domain.index')" class="ml-1 text-sm font-medium text-gray-700 hover:text-gray-900 md:ml-2 dark:text-gray-400 dark:hover:text-white">
                            {{tm('domain')}}
                        </LInk>
                    </div>
                </li>
                <li>
                    <div class="flex items-center">
                        <svg class="w-6 h-6 text-gray-400" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                            <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path>
                        </svg>
                        <a href="#" class="ml-1 text-sm font-medium text-gray-700 hover:text-gray-900 md:ml-2 dark:text-gray-400 dark:hover:text-white">
                            {{tm('Create')}}
                        </a>
                    </div>
                </li>
            </ol>

        </nav>
        
        <div class="col-span-2 bg-white rounded-md dark:bg-darker">
            <div class="flex items-center justify-between p-4 border-b dark:border-primary">
                <h4 class="text-lg font-semibold text-gray-500 dark:text-light">Create Domain</h4>
            </div>
            <div class="p-4">
                <form @submit.prevent="store">
                    <div class="p-2 pt-0">
                        <Card
                            class="p-3 mb-2"
                        >
                            <div class="w-full mb-4 text-lg text-black font-semibold text-gray-500 dark:text-light">
                                Domain information
                                <hr class="w-full">
                            </div>
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-y-2 gap-x-2 w-full pb-5">
                                <div>
                                    <TextInput 
                                        v-model="form.company" 
                                        :error="form.errors.company" 
                                        :label="tm('Company_Organization')"
                                        required="required"
                                        placeholder="Company"
                                    />
                                </div>
                                <div>
                                    <TextInput 
                                        v-model="form.site_number" 
                                        :error="form.errors.site_number"
                                        :label="tm('Site_Code')"
                                        required="required" 
                                        placeholder="Site Number"
                                    />
                                </div>
                                <div>
                                    <label class="block font-medium text-sm pb-2 text-gray-700 dark:text-light">
                                        {{ tm('domain') }}
                                        <span class="text-danger pull-right">&nbsp;*</span>
                                    </label>
                                    <Input v-model="form.domain" :errors="form.errors.domain" placeholder="domain" feedback="Domain should contain only letters, numbers, and dashes">
                                        <template #after="{ className }">
                                            <span class="inline-flex items-center px-3 text-sm text-gray-900 bg-gray-200 rounded-r-md border border-l-0 border-gray-300 dark:bg-gray-600 dark:text-gray-400 dark:border-gray-600 h-full -mr-4">
                                               {{domain}}
                                            </span>
                                        </template>
                                    </Input>
                                </div>
                                <div v-if="boolDomain ==true">
                                    <SelectInput
                                        v-model="form.parent_domain_id"
                                        :label="tm('Parent')"
                                        name="parent_domain_id"
                                        :options="parentOption"
                                        :clearable="true"
                                        value-attribute="id"
                                        text-attribute="company"
                                        placeholder="Please select an parent"
                                        :required="parentOption.length>0&&!isSuperAdmin()?'required':''"
                                        :error="form.errors.parent_domain_id"
                                    />
                                </div>
                                <span v-if="!boolDomain"></span>
                                <!-- prevent autocomplete  -->
                                <input name="username" hidden/>
                                <div v-if="boolDomain ==true">
                                    <TextInput 
                                        v-model="form.name" 
                                        :error="form.errors.name"
                                        :label="tm('name')"
                                        required="required" 
                                        placeholder="name"
                                    />
                                </div>
                                <div v-if="boolDomain ==true">
                                    <TextInput 
                                        v-model="form.email" 
                                        :error="form.errors.email" 
                                        :label="tm('Email')"
                                        required="required"
                                        placeholder="email"
                                    />
                                </div>
                                <div v-if="boolDomain ==true">
                                    <TextInput 
                                        v-model="form.password"
                                        :label="tm('password')"
                                        required="required" 
                                        type="password" 
                                        :error="form.errors.password" 
                                        placeholder="Password"
                                    />
                                </div>
                                <div v-if="boolDomain ==true">
                                    <TextInput 
                                        v-model="form.password_confirmation" 
                                        :label="tm('Password_Confirmation')"
                                        type="password" 
                                        :error="form.errors.password_confirmation" 
                                        placeholder="Password Confirmation"
                                    />
                                </div>
                            </div>
                        </Card>
                        <Card
                            class="p-3 mt-4"
                        >
                            <div class="w-full mb-4 text-lg text-black font-semibold text-gray-500 dark:text-light">
                                Company/Organization information
                                <hr class="w-full">
                            </div>
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-y-2 gap-x-2 w-full pb-5">
                                <div>
                                    <label class="block font-medium text-sm pb-2 text-gray-700 dark:text-light">
                                        {{ tm('phone') }}
                                    </label>
                                    <PhoneInput
                                        v-model="form.phone"
                                        country-code="KH"
                                        country-placeholder="Pick your phone country"
                                        phone-placeholder="National Number"
                                    />
                                </div>
                                <div v-if="boolDomain ==true">
                                    <label class="block font-medium text-sm pb-2 text-gray-700 dark:text-light">
                                        {{ tm('Allow_Create_Domain') }}
                                    </label>
                                    <div class="flex items-center mt-2">
                                        <Checkbox
                                            v-model="form.allow_create_domain"
                                            :value="form.allow_create_domain"
                                            :checked-value="true"
                                            :unchecked-value="false"
                                            align="center-center"
                                            class="bg-white"
                                        />
                                        <pre :class="`text-xs ${form.allow_create_domain?'text-blue-500':''}`"> {{ form.allow_create_domain?'Allow':'Not Allow' }}</pre>
                                    </div>
                                </div>
                                <div>
                                    <SelectInput
                                        v-model="form.province_id"
                                        :label="tm('province')"
                                        name="parent"
                                        :options="provinceOption"
                                        :clearable="true"
                                        value-attribute="id"
                                        text-attribute="name_en"
                                        placeholder="Please select an province"
                                    />
                                </div>
                                <div>
                                    <SelectInput
                                        v-model="form.district_id"
                                        :label="tm('district')"
                                        name="parent"
                                        :options="districtOption"
                                        :clearable="true"
                                        value-attribute="id"
                                        text-attribute="name_en"
                                        placeholder="Please select an district"
                                    />
                                </div>
                            </div>
                        </Card>
                    </div>
                    <div class="flex items-center justify-end px-8 py-4 bg-gray-50 border-t border-gray-100">
                        <button type="submit" class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 active:bg-gray-900 focus:outline-none focus:border-gray-900 focus:ring focus:ring-gray-300 disabled:opacity-25 transition bg-primary hover:bg-primary-dark focus:outline-none focus:ring-2 focus:ring-primary focus:ring-offset-1 dark:focus:ring-offset-darker">
                            Save

                        </button>
                        &nbsp;<back-button></back-button>
                    </div>
                </form>
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
import TextInputAdonRight from "@/Components/Form/TextInputAdonRight.vue"
import TextInput from "@/Components/Forms/TextInput.vue"
import SelectInput from "@/Components/Forms/SelectInput.vue"
import SwitchInput from "@/Components/Form/SwitchInput.vue"
import {
    Head, Link
} from '@inertiajs/inertia-vue3'
import { useToast } from "vue-toastification"
import { useI18n } from "vue-i18n"
import { lang } from "@/Lang"
import BackButton from "@/Components/Button/Back.vue"
import { Input,FormLabel,Toggle,RichSelect,PhoneInput,Checkbox,Skeleton, Card } from '@flavorly/vanilla-components'
import EmployeeInputVisibility from '../../Libs/EmployeeInputVisibility.vue'

export default defineComponent({
    mixins: [EmployeeInputVisibility],

    props: {
        centralDomain: String,
        boolDomain: Boolean,
        parentOption: Array,
        provinceOption: Array,
    },

    components: {
        AuthLayout,
        JetNavLink,
        Head,
        Link,
        TextInput,
        SelectInput,
        TextInputAdonRight,
        SwitchInput,
        BackButton,
        Input,
        FormLabel,
        Toggle,
        RichSelect,
        PhoneInput,
        Checkbox,
        Skeleton, 
        Card
    },
    setup(){
        const { tm } = useI18n({
            inheritLocale: true,
            messages: lang
        })
        const toast = useToast()
        return { tm,toast }
    },
    watch:{
        'form.province_id':function(val){
            this.form.district_id = null
            this.districtOption = []
            this.onDistrict(val)
        },
    },
    data() {
        return {
            form: this.$inertia.form({
                company:null,
                domain: null,
                name:null,
                email:null,
                password:null,
                password_confirmation:null,
                is_active: true,
                site_number: null,
                phone:null,
                parent_domain_id: null,
                allow_create_domain:true,
                province_id:null,
                district_id:null,
            }),
            domain: (this.centralDomain.startsWith('.') ? this.centralDomain : '.' + this.centralDomain).replace(/^\.+http(s)?:\/\//, '.'),
            districtOption:[],
        }
    },
    methods: {
        store() {
            this.form.post(this.route('domain.store'), {
                onSuccess: () => {
                    this.toast.success("Domain created successfully");
                },
                onFinish: () => this.form.reset('password', 'password_confirmation'),
                onError: (errors) => {
                    this.form.reset('password', 'password_confirmation');
                    if (errors) {
                        if (typeof errors === 'string') {
                            this.toast.error(errors);
                        } else if (typeof errors === 'object') {
                            Object.values(errors).flat().forEach(msg => {
                                if (typeof msg === 'string') {
                                    this.toast.error(msg);
                                }
                            });
                        }
                    }
                },
            })
        },
        onDistrict(province_id){
            if(province_id != null) {
                axios.get(route('domain.district', province_id))
                .then(response=>{
                    let data = response.data
                    this.districtOption = data
                })
            }
        },
    },
})
</script>
