<script setup>
import { router } from "@inertiajs/vue3";
import SelectGroup from "@/Components/SelectGroup.vue";
import { ref, watch, computed, onMounted } from "vue";
import { usePage } from "@inertiajs/vue3";

import { useGeneralStore } from '@/stores/general';
import { storeToRefs } from 'pinia';
const useGeneral = useGeneralStore()
const { paginationNumber } = storeToRefs(useGeneral)


defineProps({
    links: Array,
    simpleLinks: Array,

    withAxios: {
        type: Boolean,
        default: false,
    },
    showPaginationNumber: {
        type: Boolean,
        default: true,
    },
});


const searchParams = new URLSearchParams(window.location.search);
const urlLink = ref({ url: window.location.href.split("?")[0] });

// const paginationNumber = ref(
//     searchParams.get("paginationNumber") ??
//         sessionStorage.getItem("paginationNumber") ??
//         usePage().props.paginationNumber
// );

// onMounted(() => {
//     sessionStorage.removeItem("paginationNumber");
// });

const emit = defineEmits(["startLeaveAnimation"]);

function goToUrl(link) {

    if (
        usePage().props.paginationNumber == paginationNumber.value &&
        !searchParams.has("paginationNumber")
    ) {
        router.get(link.url);
    } else {
        router.get(link.url, { paginationNumber: paginationNumber.value });
    }

    emit("startLeaveAnimation");
}

onMounted(() => !searchParams.has("paginationNumber") ?  paginationNumber.value = usePage().props.paginationNumber : '')

// function goToUrl(link) {
//     sessionStorage.setItem("paginationNumber", paginationNumber.value);

//     if (
//         usePage().props.paginationNumber == paginationNumber.value &&
//         !searchParams.has("paginationNumber")
//     ) {
//         router.get(link.url);
//     } else {
//         router.get(link.url, { paginationNumber: paginationNumber.value });
//     }
//     emit("startLeaveAnimation");
// }

watch(
    () => paginationNumber.value,
    () =>goToUrl(urlLink.value) 
);

// watch(
//     () => paginationNumber.value,
//     () => paginationNumber.value !== usePage().props.paginationNumber ?  goToUrl(urlLink.value) : ''
// );

function isDisabled(link) {
    return link.url == null || link.active;
}

const link = (label) => {
    return label;
    // return label.includes(previous) ? 'السابق' : label
};
</script>

<template>
    <!-- <div class="flex justify-center"> -->
    <nav aria-label="Page navigation example" class="grid grid-cols-10 w-full">
        <div v-if="withAxios" />
        <div v-else class="px-5">
            <SelectGroup
                v-if="showPaginationNumber"
                v-model="paginationNumber"
                :items="[
                    { id: 10, name: 10 },
                    { id: 20, name: 20 },
                    { id: 30, name: 30 },
                    { id: 40, name: 40 },
                    { id: 50, name: 50 },
                    { id: 100, name: 100 },
                ]"
            />
        </div>

        <div class="flex justify-center col-span-8 py-0.5">
            <ul
                class="col-span-8 flex list-style-none border border-zinc-500 dark:border-zinc-400/70 rounded-full"
            >
                <!-- links for small screens show only previos and next  -->
                <li
                    class="flex-nowrap page-item rounded-sm md:hidden"
                    v-for="link in links"
                    :key="link.label"
                    v-show="link.label.includes('&')"
                >
                    <span v-if="link.label.includes('&')">
                        <button
                            v-if="withAxios"
                            class="page-link relative block py-1.5 px-3 bg-transparent outline-none transition-all duration-300 hover:text-gray-800 hover:bg-gray-200 focus:shadow-none md:hidden"
                            :class="{
                                'text-gray-400  ': isDisabled(link),
                                'text-gray-900 dark:text-gray-400/70 dark:font-normal font-bold':
                                    !isDisabled(link),
                                'bg-zinc-800 dark:bg-zinc-700  text-gray-100 font-normal ':
                                    link.active,
                            }"
                            v-html="link.label"
                            @click.prevent="$emit('callAxiosUrl', link)"
                            :disabled="isDisabled(link)"
                        ></button>

                        <button
                            v-else
                            class="page-link relative block py-1.5 px-3 bg-transparent outline-none transition-all duration-300 hover:text-gray-800 hover:bg-gray-200 focus:shadow-none md:hidden"
                            :class="{
                                'text-gray-400  ': isDisabled(link),
                                'text-gray-900 dark:text-gray-400/70 dark:font-normal font-bold':
                                    !isDisabled(link),
                                'bg-zinc-800 dark:bg-zinc-700  text-gray-100 font-normal ':
                                    link.active,
                            }"
                            v-html="link.label"
                            @click.prevent="goToUrl(link)"
                            :disabled="isDisabled(link)"
                        ></button>
                    </span>
                </li>

                <!-- links for big screens show all pagination items  -->
                <li
                    class="flex-nowrap page-item rounded-sm hidden md:block"
                    v-for="link in links"
                    :key="link.label"
                >
                    <button
                        v-if="withAxios"
                        class="page-link relative py-1.5 px-3 bg-transparent outline-none transition-all duration-300 hover:text-gray-800 hover:bg-zinc-500 focus:shadow-none border-zinc-400 dark:border-zinc-400/40"
                        :class="{
                            'hover:rtl:rounded-r-full hover:ltr:rounded-l-full':
                                link.label.includes('Previous'),
                            'hover:rtl:rounded-l-full hover:ltr:rounded-r-full':
                                link.label.includes('Next'),
                            ' rtl:border-r ltr:border-l':
                                !link.label.includes('Previous'),
                            'text-gray-400 ': isDisabled(link),
                            'text-gray-900 dark:text-gray-400/70 dark:font-normal font-bold':
                                !isDisabled(link),
                            'bg-zinc-800 dark:bg-zinc-500  text-gray-50 font-normal ':
                                link.active,
                        }"
                        @click.prevent="$emit('callAxiosUrl', link)"
                        :disabled="isDisabled(link)"
                    >
                        {{
                            link.label.includes("Previous") ||
                            link.label.includes("Next")
                                ? $t("general." + link.label)
                                : link.label
                        }}
                    </button>

                    <button
                        v-else
                        class="page-link relative py-1.5 px-3 bg-transparent outline-none transition-all duration-300 hover:text-gray-800 hover:bg-zinc-500 focus:shadow-none border-zinc-400 dark:border-zinc-400/40"
                        :class="{
                            'hover:rtl:rounded-r-full hover:ltr:rounded-l-full':
                                link.label.includes('Previous'),
                            'hover:rtl:rounded-l-full hover:ltr:rounded-r-full':
                                link.label.includes('Next'),
                            ' rtl:border-r ltr:border-l':
                                !link.label.includes('Previous'),
                            'text-gray-400 ': isDisabled(link),
                            'text-gray-900 dark:text-gray-400/70 dark:font-normal font-bold':
                                !isDisabled(link),
                            'bg-zinc-800 dark:bg-zinc-700  text-gray-100 font-normal ':
                                link.active,
                        }"
                        @click.prevent="goToUrl(link)"
                        :disabled="isDisabled(link)"
                    >
                        {{
                            link.label.includes("Previous") ||
                            link.label.includes("Next")
                                ? $t("general." + link.label)
                                : link.label
                        }}
                    </button>
                </li>
            </ul>
        </div>
    </nav>
</template>
<!-- @click.prevent="goToUrl(link)" -->
