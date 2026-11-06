<template>
<Head title="Edit User" />
<div class="px-4 py-4">

    <nav class="flex pt-4 mb-6" aria-label="Breadcrumb">
        <ol class="inline-flex items-center space-x-1 md:space-x-3">
            <li class="inline-flex items-center">
                <Link preserve-scroll="true" :href="dashboard" class="inline-flex items-center text-sm font-medium text-gray-700 hover:text-gray-900 dark:text-gray-400 dark:hover:text-white">
                    <svg class="mr-2 w-4 h-4" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                        <path d="M10.707 2.293a1 1 0 00-1.414 0l-7 7a1 1 0 001.414 1.414L4 10.414V17a1 1 0 001 1h2a1 1 0 001-1v-2a1 1 0 011-1h2a1 1 0 011 1v2a1 1 0 001 1h2a1 1 0 001-1v-6.586l.293.293a1 1 0 001.414-1.414l-7-7z"></path>
                    </svg>
                    Home
                </Link>
            </li>
            <li>
                <div class="flex items-center">
                    <svg class="w-6 h-6 text-gray-400" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                        <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path>
                    </svg>
                    <Link preserve-scroll="true" :href="route('user.index')" class="ml-1 text-sm font-medium text-gray-700 hover:text-gray-900 md:ml-2 dark:text-gray-400 dark:hover:text-white">
                        User
                    </Link>
                </div>
            </li>
            <li>
                <div class="flex items-center">
                    <svg class="w-6 h-6 text-gray-400" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                        <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path>
                    </svg>
                    <span class="ml-1 text-sm font-medium text-gray-700 hover:text-gray-900 md:ml-2 dark:text-gray-400 dark:hover:text-white">
                        Edit
                    </span>
                </div>
            </li>
        </ol>
    </nav>
    <form @submit.prevent="update">
        <div class="col-span-2 bg-white rounded-md dark:bg-darker">
            <div class="flex items-center justify-between p-4 border-b dark:border-primary">
                <h4 class="text-sm font-semibold text-gray-500 dark:text-light">Edit User</h4>
            </div>
            <div class="p-4">
                <div class="flex flex-wrap -mb-8 -mr-6 p-4">
                    <text-input v-model="form.name" required="required" :error="form.errors.name" class="pb-2 pr-6 w-full sm:w-1/2" :label="tm('Name')" />
                    <text-input v-model="form.email" required="required" :error="form.errors.email" class="pb-2 pr-6 w-full lg:w-1/2" :label="tm('Email')" disabled/>
                </div>

            </div>
            <div class="flex items-center justify-between mt-6 p-4 border-b dark:bg-darker dark:border-primary">
                <h5 class="text-sm font-semibold text-gray-500 dark:text-light">Account Status</h5>
            </div>
            <div class="flex flex-wrap -mb-8 -mr-6 p-4">
                 <div class="flex p-4 pr-6 w-full lg:w-1/2">
                    <switch-input v-model="form.is_active" :value="form.is_active" :error="form.errors.is_active" label="Status" class="pb-2 pr-6 w-full lg:w-1/2"/> 
                    <switch-input v-if="isSuperAdmin() || isAdmin()" v-model="form.level_access" :value="form.level_access" :error="form.errors.level_access" label="Is Admin" class="pb-2 pr-6 w-full lg:w-1/2 text-right sm:text-left"/>
                 </div>
            </div>
            <div class="p-4">
                <div class="flex items-center justify-end px-4 py-4">
                    <save-button v-if="can(['user.store'])"></save-button>
                    <back-button></back-button>
                </div>
            </div>
        </div>
    </form>

</div>
</template>

<script>
import { defineComponent } from 'vue'
import AuthLayout from '@/Layouts/AuthLayout.vue'
import TextInput from "@/Components/Form/TextInput.vue"
import SelectInput from "@/Components/Form/SelectInput.vue"
import TextareaInput from "@/Components/Form/TextareaInput.vue"
import SwitchInput from "@/Components/Form/SwitchInput.vue"
import BackButton from "@/Components/Button/Back.vue"
import SaveButton from "@/Components/Button/Save.vue"
import { useI18n } from "vue-i18n";
import { lang } from "@/Lang";
import { Link } from '@inertiajs/inertia-vue3';
import { useToast } from "vue-toastification";

export default defineComponent({
    layout: AuthLayout,
    components: {
        TextInput,
        SelectInput,
        TextareaInput,
        SwitchInput,
        BackButton,
        SaveButton,
        Link,
    },
    props: {
        user: Object,
    },
    setup() {
        const { tm } = useI18n({
            inheritLocale: true,
            messages: lang
        })
        const toast = useToast();
        return { tm, toast }
    },
    data() {
        return {
            form: this.$inertia.form({
                name: this.user.name,
                email: this.user.email,
                is_active: this.user.is_active,
                level_access: this.user.level_access,
                terms: true
            }),
        }
    },
    methods: {
        update() {
            this.form.put(this.route('user.update', this.user.id), {
                preserveScroll: true,
                preserveState: true,
                onSuccess: () => {
                    this.toast.success("User has been updated successfully");
                }
            })
        },
    },
})
</script>
