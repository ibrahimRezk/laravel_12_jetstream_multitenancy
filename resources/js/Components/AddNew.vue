<script setup>
import { ref, computed, toRefs } from "vue";

import Button from "@/Components/Button.vue";
import FilterIcon from "@/Components/Icons/Filter.vue";

const props = defineProps({
    show: {
        type: Boolean,
        default: false,
    },
    showDeleteAll: {
        type: Boolean,
        default: false,
    },

    checkedItems: {
        type: Number,
        default: 0,
    },
});

const { show, showDeleteAll, checkedItems } = toRefs(props);

const emit = defineEmits(["reset"]);

// const showFilters = ref(props.show ?? false);
const showFilters = ref(props.show);

const reset = () => {
    showFilters.value = !showFilters.value;

    if (showFilters.value == false) {
        emit("reset");
    }
};

const deleteAll = () => {
    emit("deleteAll");
};
</script>

<style scoped>
.collapsableDiv {
    overflow: visible;
    transform-origin: top;
}
.collapse-enter-active {
    animation: collapse reverse 300ms ease;
}
.collapse-leave-active {
    animation: collapse 200ms ease;
}
@keyframes collapse {
    from {
        max-height: 300px;
    }
    to {
        max-height: 0px;
    }
}
</style>

<template>
    <div>
        <div class="flex flex-col md:flex-row md:items-center justify-between">
            <div class="flex flex-col lg:flex-row gap-2 ">
                <div >
                    <slot />
                </div>
                <Button
                    v-if="$slots.filters"
                    class="rtl:mr-3 ltr:ml-2 inline-flex items-center hover:cursor-pointer"
                    color="gradient_black"
                    @click="reset"
                >
                    <FilterIcon class="mx-2" />
                    <span>{{
                        showFilters
                            ? $t("general.reset filter")
                            : $t("general.filter")
                    }}</span>
                </Button>


                <div class="flex items-center justify-center">
                    <Button
                        v-if="props.showDeleteAll"
                        class="mx-3 hover:cursor-pointer px-5"
                        v-show="checkedItems > 0"
                        color="gradient_red"
                        @click="deleteAll"
                        >{{ $t("general.delete all selected") }}
                    </Button>
                </div>
            </div>
            <div class="flex flex-col lg:flex-row gap-2">
                <slot name="button" />
            </div>
            <Transition name="collapse">
                <div class="collapsableDiv" v-if="props.show">
                    <slot name="filtersExtraButtons" />
                </div>
            </Transition>
            <div class="flex gap-2">
                <slot name="customHeaderButton" />
            </div>
        </div>
        <!-- <Transition name="collapse">
            <div v-if="showFilters" class="collapsableDiv">
                <slot name="filters" />
            </div>
        </Transition> -->
    </div>
</template>
