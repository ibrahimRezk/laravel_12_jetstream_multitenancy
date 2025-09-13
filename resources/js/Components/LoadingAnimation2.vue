<script setup>
import { computed, onMounted, onUnmounted, watch } from "vue";

const props = defineProps({
    show: {
        type: Boolean,
        default: false,
    },
});

watch(
    () => props.show,
    () => {
        if (props.show) {
            document.body.style.overflow = "hidden";
        } else {
            document.body.style.overflow = null;
        }
    }
);
</script>

<template>
    <teleport to="body">
        <transition leave-active-class="duration-200">
            <div
                v-show="show"
                class="fixed inset-0 overflow-y-auto px-4 py-6 sm:px-0 z-50 flex justify-center items-center"
                scroll-region
            >
                <transition
                    enter-active-class="ease-out duration-300"
                    enter-from-class="opacity-0"
                    enter-to-class="opacity-100"
                    leave-active-class="ease-in duration-200"
                    leave-from-class="opacity-100"
                    leave-to-class="opacity-0"
                >
                    <div
                        v-show="show"
                        class="fixed inset-0 transform transition-all"
                    >
                        <div
                            class="absolute inset-0 bg-yellow-100 opacity-30"
                        />
                    </div>
                </transition>

                <transition
                    enter-active-class="ease-out duration-300"
                    enter-from-class="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                    enter-to-class="opacity-100 translate-y-0 sm:scale-100"
                    leave-active-class="ease-in duration-200"
                    leave-from-class="opacity-100 translate-y-0 sm:scale-100"
                    leave-to-class="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                >
                    <div v-show="show" class="  ">
                        <div
                            class="flex items-center justify-center relative py-60"
                        >
                            <!-- <div
                                class="fixed flex justify-center items-center w-44 h-44 rounded-full  shadow-md  bg-gradient-to-tr from-red-600  via-black   to-lime-600 opacity-80 border border-gray-400
                                " 
                            /> -->

                            <div
                                class="fixed flex justify-center items-center w-44 h-44 rounded-full shadow-md bg-gradient-to-r from-black/70 via-black/50 to-sky-900 opacity-90 border border-gray-400"
                            />

                            <div
                                class="fixed w-24 h-24 rotate-270 border rounded-full border-red-400 animate-reverse-spin opacity-40"
                            />
                            <div class="fixed animate-spin w-24 h-24">
                                <div
                                    class="fixed w-24 h-24 border border-red-400 opacity-60 rounded-lg"
                                />
                                <div
                                    class="fixed rotate-45 w-24 h-24 border border-red-400 opacity-60 rounded-lg"
                                />
                            </div>

                            <!-- 
                            <div
                                class="fixed flex justify-center items-center w-24 h-24 rounded-full animate-reverse-spin border-y border-solid border-fuchsia-400 border-t-transparent shadow-md bg--500 opacity-40"
                            /> -->

                            <!-- <svg
                                class="w-60 h-60 animate-spin text-yellow-100"
                                viewBox="0 0 24 24"
                                fill="none"
                            >
                                <path
                                    d="M12 4.75V6.25"
                                    stroke="currentColor"
                                    stroke-width="0.2"
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                ></path>
                                <path
                                    d="M17.1266 6.87347L16.0659 7.93413"
                                    stroke="currentColor"
                                    stroke-width="0.2"
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                ></path>
                                <path
                                    d="M19.25 12L17.75 12"
                                    stroke="currentColor"
                                    stroke-width="0.2"
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                ></path>
                                <path
                                    d="M17.1266 17.1265L16.0659 16.0659"
                                    stroke="currentColor"
                                    stroke-width="0.2"
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                ></path>
                                <path
                                    d="M12 17.75V19.25"
                                    stroke="currentColor"
                                    stroke-width="0.2"
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                ></path>
                                <path
                                    d="M7.9342 16.0659L6.87354 17.1265"
                                    stroke="currentColor"
                                    stroke-width="0.2"
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                ></path>
                                <path
                                    d="M6.25 12L4.75 12"
                                    stroke="currentColor"
                                    stroke-width="0.2"
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                ></path>
                                <path
                                    d="M7.9342 7.93413L6.87354 6.87347"
                                    stroke="currentColor"
                                    stroke-width="0.2"
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                ></path>
                            </svg> -->

                            <div
                                class="fixed flex justify-center items-center w-40 h-40 rounded-full animate-spin border-y border-solid border-fuchsia-400 border-t-transparent shadow-md bg--500 opacity-40"
                            />
                            <div
                                class="fixed flex justify-center items-center w-40 h-40 rounded-full animate-spin border-y border-solid border-cyan-400 border-t-transparent shadow-md"
                            />

                            <div
                                class="fixed flex justify-center items-center w-36 h-36 rounded-full animate-reverse-spin border-y border-solid border- border-green-500 border-t-transparent shadow-md"
                            />
                            <div
                                class="fixed flex justify-center items-center w-36 h-36 rounded-full animate-reverse-spin border-y border-solid border- border-yellow-500 border-t-transparent shadow-md"
                            />

                            <div
                                class="fixed flex justify-center items-center w-12 h-12 rounded-full animate-reverse-spin border-y border-solid border-blue-100 border-t-transparent shadow-md"
                            />
                            <div
                                class="fixed flex justify-center items-center w-12 h-12 rounded-full animate-reverse-spin border-y border-solid border-orange-500 border-t-transparent shadow-md"
                            />

                            <span
                                class="fixed animate-pulse border w-32 rounded-full px-5 border-orange-100 shadow-md text-sky-900 bg-gradient-to-tr from-lime-400 to-sky-300 text-sm font-bold"
                                >{{ $t("general.loading") }}</span
                            >
                        </div>
                    </div>
                </transition>
            </div>
        </transition>
    </teleport>
</template>
