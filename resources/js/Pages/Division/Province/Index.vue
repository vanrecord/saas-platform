<template>
<Head title="Province" />
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
                        {{tm('province')}}
                    </a>
                </div>
            </li>
        </ol>
    </nav>
    <div class="col-span-2 bg-white rounded-md dark:bg-darker">
        <div class="flex items-center justify-between p-4 border-b dark:border-primary">
            <h4 class="text-lg font-semibold text-gray-500 dark:text-light">{{tm('province')}}</h4>
        </div>
        <div class="p-4">
             <Table :resource="provinces">
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
import { useToast } from "vue-toastification";

export default defineComponent({
    layout: AuthLayout,
    components: {
        Table
    },
    setup() {
        const {
            tm
        } = useI18n({
            inheritLocale: true,
            messages: lang
        });
        const toast = useToast();

        return {
            tm,toast
        }
    },
    props: {
        provinces: Object,

    },
    data() {
        return {
            w60: 'w-60',
        }
    }
})
</script>
