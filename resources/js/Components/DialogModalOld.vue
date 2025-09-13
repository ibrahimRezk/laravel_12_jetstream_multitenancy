<script setup>
import Modal from "./Modal.vue";
import { computed } from "vue";

const emit = defineEmits(["close"]);

defineProps({
    show: {
        type: Boolean,
        default: false,
    },
    maxWidth: {
        type: String,
        default: "2xl",
    },
    fullModal: {
        type: Boolean,
        default: false,
    },
    closeable: {
        type: Boolean,
        default: true,
    },
    showModalHeader: {
        type: Boolean,
        default: true,
    },
    classes: {
        type: String,
        default: "",
    },
});

const close = () => {
    emit("close");
};
</script>

<template>
    <!-- <Modal :show="show" :max-width="maxWidth" :closeable="closeable" @close="close">
        <div class="rtl:bg-gradient-to-r  ltr:bg-gradient-to-l    
            bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4" :class="DialogModalClasses">
            <div  class="text-lg text-center  rtl:bg-gradient-to-r ltr:bg-gradient-to-l from-orange-100 to-zinc-900  text-white    shadow-md rounded">
                
         
                <slot name="title" />
            </div>

            <div class="mt-4">
                <slot name="content" />
            </div>
        </div>

        <div class="flex flex-row justify-between px-1 py-1  text-right bg-zinc-900">
            <slot name="footer" />
        </div>
    </Modal> -->

    <!-- ///// modal with dark mode  -->

    <Modal
        :show="show"
        :max-width="maxWidth"
        :full-modal="fullModal"
        :closeable="closeable"
        @close="close"
    >
    
    <div v-if="fullModal == true "
                class=" rtl:bg-gradient-to-r from-orange-100 to-zinc-700 dark:from-gray-500 dark:via-transparent dark:to-gray-500 ltr:bg-gradient-to-l rounded-md  dark:border border-gray-200 dark:border-gray-200/20 shadow-lg min-h-screen w-full"
            >
                <!-- <div
                    class=" p-3 dark:border border-gray-200/30 rounded-md rtl:bg-gradient-to-r from-orange-400 to-zinc-900 dark:from-gray-900 dark:via-zinc-900 dark:to-gray-900 ltr:bg-gradient-to-l shadow-2xl shadow-zinc-800 drop-shadow-md min-h-screen  w-full"
                    
                > -->
                    <slot name="content" />
                <!-- </div> -->
            </div>



<div v-else>

        <div
            class="rtl:bg-gradient-to-r ltr:bg-gradient-to-l px-4 pt-5 pb-4 sm:p-6 sm:pb-4 from-orange-300 to-zinc-800 dark:from-gray-900 dark:via-gray-700 dark:to-gray-900"

        >
            <div v-if="showModalHeader"
                class="dark:from-transparent dark:via-slate-900 dark:to-transparent text-white dark:border border-zinc-400/50 py-1 text-lg text-center rtl:bg-gradient-to-r ltr:bg-gradient-to-l from-orange-100 to-zinc-900 shadow-md rounded"
            >
                <slot name="title" />
            </div>

            <div
                class="mt-4 p-3 rtl:bg-gradient-to-r from-orange-100 to-zinc-700 dark:from-gray-500 dark:via-transparent dark:to-gray-500 ltr:bg-gradient-to-l rounded-md pb-4 px-3 dark:border border-gray-200 dark:border-gray-200/20 shadow-lg"
            >
                <div
                    class="p-4 mt-1 dark:border border-gray-200/30 rounded-md rtl:bg-gradient-to-r from-orange-400 to-zinc-900 dark:from-gray-900 dark:via-zinc-900 dark:to-gray-900 ltr:bg-gradient-to-l shadow-2xl shadow-zinc-800 drop-shadow-md"
                    
                >
                    <slot name="content" />
                </div>
            </div>
        </div>

        <div
            class="flex flex-row justify-between px-1 py-1 text-right bg-zinc-900"
        >
            <slot name="footer" />
        </div>
    </div>

    </Modal>
</template>
