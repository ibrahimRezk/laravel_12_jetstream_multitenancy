<script setup>
import { ref, watch, computed, onMounted } from "vue";
import { wTrans } from "laravel-vue-i18n";

import Card from "@/Components/Card/Card.vue"; 
import InputGroup from "@/Components/InputGroup.vue";
import SelectGroup from "@/Components/SelectGroup.vue"; 

const props = defineProps({ 
    modelValue: {
        type: Object,
        default: () => ({}),
    },

    stores: {
        type: Array,
        default: () => [],
    },

    isLoading: {
        type: Boolean,
        default: false,
    },
    noPadding: {
        type: Boolean,
        default: false,
    },
});

const emits = defineEmits(["update:modelValue", "filtersValuesData"]);

// const filters = ref({ ...props.modelValue });
const filters = ref(props.modelValue);

const filtersValuesData = ref({});

watch(
    filters,
    () => {
        emits("update:modelValue", filters.value);
        emits("filtersValuesData", filtersValuesData.value);
        prepairFiltersValuesData.value;

    },
    {
        deep: true,
    }
);




onMounted(() => {
        prepairFiltersValuesData.value;
        emits("filtersValuesData", filtersValuesData.value);
});


const prepairFiltersValuesData = computed(() => {
    

    filtersValuesData.value[name] = {
        id: "name",
        data: filters.value.name,
    };




});


const name = "name";



</script>

<template>
    <Card
        :is-loading="isLoading"
        noPadding
    >
        <form class="flex flex-col gap-2">
 
                <InputGroup
                    :label="`${$t('general.' + name)}`"
                    v-model="filters.name"
                />
            
        </form>
    </Card>
</template> 
