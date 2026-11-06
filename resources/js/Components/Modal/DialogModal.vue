<script setup>
import Modal from './Modal.vue';

const emit = defineEmits(['close']);

defineProps({
    show: {
        type: Boolean,
        default: false,
    },
    maxWidth: {
        type: String,
        default: '2xl',
    },
    closeable: {
        type: Boolean,
        default: true,
    },
    hiddenFooter: {
        type: Boolean,
        default:false
    },
    modelPadding: {
        type: String,
        default:'px-6 py-4'
    }
});

const close = () => {
    emit('close');
};
</script>

<template>
    <Modal
        :show="show"
        :max-width="maxWidth"
        :closeable="closeable"
        @close="close"
    >
        <div :class="modelPadding">
            <div class="text-lg">
                <slot name="title" />
            </div>

            <div class="mt-4">
                <slot name="content" />
            </div>
        </div>

        <div :class="hiddenFooter?'flex flex-row justify-end px-6 py-4 bg-gray-100 text-right hidden':'flex flex-row justify-end px-6 py-4 bg-gray-100 text-right' ">
            <slot name="footer" />
        </div>
    </Modal>
</template>
