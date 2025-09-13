<script setup>
import Th from "@/Components/Table/Th.vue";
import Td from "@/Components/Table/Td.vue";
import Pagination from "@/Components/Table/Pagination.vue";
import { trans } from "laravel-vue-i18n";
import Checkbox from "@/Components/Checkbox.vue";
import { ref, computed, watch, onMounted } from "vue";
import Button from "@/Components/Button.vue";

// import { emit } from "process";

const props = defineProps({
    headers: {
        type: Array,
        default: () => [],
    },
    items: {
        type: Object,
        default: () => ({}),
    },
    headersClasses: {
        type: String,
        default: () => "",
    },

    headerFooterClasses: {
        type: String,
        default: () => "",
    },
    trClasses: {
        type: String,
        default: () => "",
    },
    bodyClasses: {
        type: String,
        default: () => "",
    },
    hoverClasses: {
        type: String,
        default: () => "",
    },
    noCheckAll: {
        type: Boolean,
        default: false,
    },
    checkedAllButton: {
        type: Boolean,
        default: false,
    },
    noNamePadding: {
        type: Boolean,
        default: false,
    },
    withAxios: {
        type: Boolean,
        default: false,
    },
    noPagination: {
        type: Boolean,
        default: false,
    },
    has_extra_final_row: {
        type: Boolean,
        default: false,
    },
    showPaginationNumber: {
        type: Boolean,
        default: true,
    },
    tableHeight: {
        type: String,
        default: "",
    },
});

// const animate = ref();

// onMounted(() => {
//     animate.value = true;
// });

const startLeaveAnimation = () => {
    // animate.value = false;
    emit("startLeaveAnimation", false);
};

const checkedAllButton = ref();
watch(
    () => props.checkedAllButton,
    () => (checkedAllButton.value = props.checkedAllButton),
    { deep: true }
);

const emit = defineEmits(["callAxiosUrl", "checkedAll", "startLeaveAnimation"]);

const callAxiosUrl = (payload) => {
    emit("callAxiosUrl", payload);
    emit("startLeaveAnimation", false);
};

const theHeadersClasses = computed(() => {
    return `rtl:bg-gradient-to-r  ltr:bg-gradient-to-l  rounded-2xl font-normal text-white  from-gray-800  to-gray-700 dark:via-zinc-700 dark:from-black dark:to-black  ${
        props.tableHeight == "" ? "border-y border-gray-200/40" : ""
    }  ${props.headersClasses} `;
});
const theheaderFooterClasses = computed(() => {
    return `rtl:bg-gradient-to-r   ltr:bg-gradient-to-l    font-normal text-white from-zinc-800 via-orange-200  to-zinc-900 dark:via-zinc-900  to-zinc-900 ${
        props.headerFooterClasses
    } ${props.noPagination ? " " : "rounded-2xl "}`;
});

const theTrClasses = (index) => {
    return ` ${
        index % 2 === 0
            ? " rtl:bg-gradient-to-r ltr:bg-gradient-to-l from-orange-300 to-black dark:from-zinc-900 dark:via-gray-800 dark:to-zinc-900 rtl:hover:bg-gradient-to-r ltr:hover:bg-gradient-to-l    rounded-none hover:dark:from-zinc-800 hover:dark:to-gray-600 hover:from-orange-300/90 hover:to-black/80" +
              props.trClasses +
              " "
            : "rtl:bg-gradient-to-r  ltr:bg-gradient-to-l    rounded-none from-orange-200 to-black/90 dark:from-zinc-800 dark:via-gray-700 dark:to-zinc-800  rtl:hover:bg-gradient-to-r ltr:hover:bg-gradient-to-l    rounded-none hover:dark:from-zinc-800 hover:dark:to-gray-600 hover:from-orange-200/90 hover:to-black/80 " +
              props.trClasses +
              " "
    }`;
};

const headerTextColor = (index) => {
    return index == 0
        ? "text-green-300"
        : index == 1
        ? "text-cyan-300"
        : index == 2
        ? "text-orange-300"
        : index == 3
        ? "text-red-300"
        : index == 4
        ? "text-indigo-300"
        : index == 5
        ? "text-pink-300"
        : index == 6
        ? "text-yellow-300"
        : index == 7
        ? "text-sky-300"
        : index == 8
        ? "text-green-300"
        : index == 9
        ? "text-orange-300"
        : index == 10
        ? "text-indigo-300"
        : index == 11
        ? "text-cyan-300"
        : index == 12
        ? "text-pink-300"
        : index == 13
        ? "text-green-300"
        : index == 14
        ? "text-yellow-300"
        : index == 15
        ? "text-orange-300"
        : "text-gray-300";
};

// const theTrClasses = computed(() => {
//     return `rtl:bg-gradient-to-r  ltr:bg-gradient-to-l    rounded-none from-orange-200 dark:from-zinc-800 dark:via-gray-700 dark:to-zinc-800 to-black  ${props.trClasses}`;
// });
const theHoverClasses = computed(() => {
    return `rtl:hover:bg-gradient-to-r ltr:hover:bg-gradient-to-l    rounded-none hover:dark:from-zinc-600 hover:dark:to-gray-600 hover:from-amber-50 hover:to-gray-800  ${props.hoverClasses}`;
});
</script>

<template>
    <!-- <transition name="page" mode="out-in" >
        <div v-if="animate"> -->
    <div class="flex flex-wrap">
        <div class="flex-none w-full max-w-full">
            <div
                class="flex flex-col min-w-0 break-words border-zinc-400/20 border border-solid shadow-black shadow-md bg-clip-border"
                :class="theheaderFooterClasses"
            >
                <div
                    class="z-10 px-5 borde border-b-0 border-b-solid rounded-t-2xl border-b-transparent rtl:bg-gradient-to-r ltr:bg-gradient-to-l rounded-none"
                >
                    <div
                        class="flex flex-row gap-2 text-yellow-50 text-lg py-2"
                    >
                        <slot name="title"> </slot>
                    </div>
                </div>

                <div class="flex-auto">
                    <div
                        class="flex-grow overflow-auto tableheight"
                        :class="props.tableHeight"
                    >
                        <!-- <div class=" overflow-scroll h-screen  p-0 overflow-x-auto "> -->
                        <!-- <div class="p-0 "> -->

                        <table
                            class="items-center w-full mb-3 align-top text-slate-500"
                        >
                            <thead
                                class="sticky top-0 z-10 align-bottom bg-gray-800"
                                :class="theHeadersClasses"
                            >
                                <Th
                                    v-if="!props.noCheckAll"
                                    class="border-b border-gray-300/50"
                                >
                                    <Checkbox
                                        class="rtl:mr-1.5 ltr:ml-1.5"
                                        v-model:checked="checkedAllButton"
                                        @change="$emit('checkedAll')"
                                    />
                                </Th>

                                <Th
                                    v-for="(header, index) in headers"
                                    :key="header.label"
                                    :class="`${header.classes}  `"
                                    class="border-b border-gray-300/50"
                                >
                                    <span
                                        v-if="
                                            header.name == 'name' &&
                                            !noNamePadding
                                        "
                                        class="mx-12 text-wrap"
                                        :class="`${props.headersClasses} ${
                                            header?.color
                                        } ${headerTextColor(index)}`"
                                    >
                                        {{ $t("general." + header.label + "") }}
                                    </span>
                                    <span
                                        v-else
                                        :class="`${props.headersClasses} ${
                                            header?.color
                                        } ${headerTextColor(index)}`"
                                        class="text-md text-wrap"
                                    >
                                        {{ $t("general." + header.label + "") }}
                                    </span>
                                </Th>
                            </thead>

                            <tbody
                                v-if="props.noPagination"
                                :class="props.bodyClasses"
                            >
                                <!-- :class="`${theHoverClasses} ${index%2 === 1 ? 'from-orange-200 dark:from-zinc-800 dark:via-gray-900 dark:to-zinc-800 to-zinc-900' : ''}`" -->

                                <tr
                                    v-for="(item, index) in items"
                                    :class="theTrClasses(index)"
                                    :key="index"
                                >
                                    <slot :item="item" :index="index"></slot>

                                    <slot
                                        name="item"
                                        :item="item"
                                        :index="1"
                                    ></slot>
                                </tr>
                                <tr v-if="items?.length === 0">
                                    <Td :colspan="headers.length + 1">
                                        {{ $t("general.no data available") }}
                                    </Td>
                                </tr>

                                <tr v-if="props.has_extra_final_row">
                                    <slot name="finalRow" />
                                </tr>
                            </tbody>

                            <tbody v-else :class="props.bodyClasses">
                                <tr
                                    class=" "
                                    :class="theTrClasses(index)"
                                    v-for="(item, index) in items.data"
                                    :key="index"
                                >
                                    <slot :item="item" :index="index"></slot>

                                    <slot
                                        name="item"
                                        :item="item"
                                        :index="1"
                                    ></slot>
                                </tr>
                                <tr v-if="items.data?.length === 0">
                                    <Td :colspan="headers.length + 1">
                                        {{ $t("general.no data available") }}
                                    </Td>
                                </tr>

                                <tr v-if="props.has_extra_final_row">
                                    <slot name="finalRow" />
                                </tr>
                            </tbody>
                        </table>
                        <div v-if="props.noPagination == false">
                            <!-- v-if="items.meta?.links?.length > 3" -->
                            <div class="items-center justify-center flex py-2">
                                <!-- <Pagination
                                    @startLeaveAnimation="startLeaveAnimation"
                                    @callAxiosUrl="callAxiosUrl"
                                    :links="items?.meta?.links"
                                    :withAxios="props.withAxios"
                                    :showPaginationNumber="
                                        props.showPaginationNumber
                                    "
                                /> -->
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- </div>
</transition> -->
</template>

<style scoped>
.customtableheight {
    height: 75vh;
}
</style>

<style scoped>
/* durations and timing functions.*/
.page-enter-active,
.page-leave-active {
    transition: all 0.8s;
}

.page-enter-from {
    transform: translateY(40px);
    opacity: 0;
}

.page-leave-to {
    opacity: 0;
    transform: translateY(-70px);
}
</style>
