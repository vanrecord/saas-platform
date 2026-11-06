<template>
  <div>
    <Head :title="title" />
    <slot></slot>
  </div>
</template>

<script setup>
import { Head } from '@inertiajs/inertia-vue3'

defineProps({
  title: {
    type: String,
    default: ''
  }
})
</script>
