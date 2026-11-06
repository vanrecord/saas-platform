<template>
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
                <template v-for="mod in config_modules">
                    <template v-for="(action , index) in mod">
                        <li>
                            <Checkbox
                                v-model="form.modules"
                                :value="index"
                                :checked-value="form.modules"
                                :unchecked-value="form.modules"
                            >
                                <label class="pl-5">{{action}}</label>
                            </Checkbox>
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
</template>
<script>
import { defineComponent } from 'vue'
import { useI18n } from "vue-i18n"
import { lang } from "@/Lang"
import { useToast } from 'vue-toastification'
import TextInput from '@/Components/Forms/TextInput.vue'
import SwitchInput from '@/Components/Forms/SwitchInput.vue'
import BackButton from "@/Components/Button/Back.vue"
import SaveButton from "@/Components/Button/Save.vue"
import { Checkbox } from '@flavorly/vanilla-components'

export default defineComponent({
    
    components: {
        TextInput,
        SwitchInput,
        BackButton,
        SaveButton,
        Checkbox,
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
    props:[
        'config_modules',
        'form'
    ],
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
    },

})
</script>
