<script setup>
import Modal from './Modal.vue';
import { computed } from "vue";


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
    classes: {
        type: String,
        default: '',
    },
});



const close = () => {
    emit('close');
};
</script>

<template>
    <Modal :show="show" :max-width="maxWidth" :closeable="closeable" @close="close">
        <div v-if="closeable == false" class="flex justify-end">
            <slot name="closeButton"/>
        </div>
        <div class="rtl:bg-gradient-to-r  ltr:bg-gradient-to-l px-4 pt-5 pb-4 sm:p-6 sm:pb-4 from-orange-300   to-zinc-800  dark:from-gray-900 dark:via-gray-700 dark:to-gray-900" >

            <div  class="    dark:from-transparent dark:via-slate-900 dark:to-transparent  text-white dark:border border-zinc-400/50 py-1 text-lg text-center  rtl:bg-gradient-to-r ltr:bg-gradient-to-l from-orange-100  to-zinc-900      shadow-md rounded">

                <slot name="title" />
            <!-- </div> -->
            </div>

                <div
                class="mt-4 p-3 rtl:bg-gradient-to-r from-orange-100  to-zinc-700 dark:from-gray-500 dark:via-transparent dark:to-gray-500 ltr:bg-gradient-to-l rounded-md pb-4 px-3 dark:border  border-gray-200     dark:border-gray-200/20   shadow-lg"
            >
                <!-- <div
                    class="p-4 mt-1 dark:border border-gray-200/30 rounded-md rtl:bg-gradient-to-r  from-zinc-900 via-orange-400/70 to-zinc-900 dark:from-gray-900 dark:via-zinc-900 dark:to-gray-900  ltr:bg-gradient-to-l shadow-2xl shadow-zinc-800 drop-shadow-md"
                > -->
                <div
                    class="p-4 mt-1 dark:border border-gray-200/30 rounded-md rtl:bg-gradient-to-r  from-orange-400  to-zinc-900 dark:from-gray-900 dark:via-zinc-900 dark:to-gray-900  ltr:bg-gradient-to-l shadow-2xl shadow-zinc-800 drop-shadow-md"
                >
                <slot name="content" />
            </div>
            </div>
        </div>

        <div class="flex flex-row justify-between px-10 py-1  text-right bg-zinc-900">
            <slot name="footer" />
        </div>
    </Modal>
</template>
