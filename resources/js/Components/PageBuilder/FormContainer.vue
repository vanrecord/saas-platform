<template>
    <div class="col-span-2 bg-white rounded-md dark:bg-darker">
        <div class="flex items-center justify-between p-4 border-b dark:border-primary">
            <h4 class="text-lg font-semibold text-gray-500 dark:text-light">{{tm(title)}}</h4>
        </div>
        <div class="p-4">
            <div class="p-4 flex flex-wrap">
                <slot name="body"/>
            </div>
            <slot name="items-detail"/>
            <div class="flex items-center justify-end px-4 py-4 space-x-2">
                <slot name="button"/>
            </div>
        </div>
    </div>
</template>

<script>
import { useI18n } from "vue-i18n";
import { lang } from "@/Lang";

export default {
    props: {
        title: {
            type: String,
            required: true,
        },
    },
    setup() {
        const { tm } = useI18n({
            inheritLocale: true,
            messages: lang
        });
        return {
            tm
        };
    }
};
</script>