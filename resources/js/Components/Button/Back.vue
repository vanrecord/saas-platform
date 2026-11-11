<template>
  <a @click="goBack()" class="inline-flex items-center px-4 py-2 bg-red-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-red-700 active:bg-red-900 focus:outline-none focus:border-red-900 focus:ring-red-300 disabled:opacity-25 transition bg-primary cursor-pointer focus:outline-none focus:ring-2 focus:ring-primary focus:ring-offset-1 dark:focus:ring-offset-darker">
      <slot>{{tm("Back")}}</slot>
  </a>
</template>

<script>
import { v4 as uuid } from 'uuid'
import { useI18n } from "vue-i18n";
import { lang } from "@/Lang";

export default {
  inheritAttrs: false,
  props: ['url'],
  setup(){
      const {tm} = useI18n({
      inheritLocale: true,
          messages: lang
      })
      return {tm}
  },
  methods: {
    goBack() {
      if(this.url) {
        this.$inertia.visit(this.url)
      } else {
        window.history.back();
      }
    }
  },
}
</script>