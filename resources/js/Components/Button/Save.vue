<template>
    <button @click='Submit' class="mr-2 inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 active:bg-gray-900 focus:outline-none focus:border-gray-900 focus:ring-gray-300 disabled:opacity-25 transition bg-primary hover:bg-primary-dark focus:outline-none focus:ring-2 focus:ring-primary focus:ring-offset-1 dark:focus:ring-offset-darker">
        {{tm("Save")}}
    </button>
</template>

<script>
import { v4 as uuid } from 'uuid'
import {useI18n} from "vue-i18n";
import { lang } from "@/Lang";

export default {
  inheritAttrs: false,
  props: {
  },
  setup(){
      const {tm} = useI18n({
      inheritLocale: true,
          messages: lang
      })
      return {tm}
  },
  methods: {
    Submit(){
        this.$emit('submit')
    }
  },
}
</script>