<script setup>
import {  computed , ref } from "vue";
import JetDropdown from "@/Components/Dropdown.vue";
import Button from "@/Components/Button.vue";
import FilterIcon from "@/Components/Icons/Filter.vue";





const props = defineProps({
    width: {
        type: String,
        default: '36',
    },
    color: {
        type: String,
        default: 'gradient_orange',
    },
    title: {
        type: String,
        default: 'headers',
    },
    iconType: {
        type: String,
        default: '',
    },
    button_title: {
        type: String,
        default: 'show_headers',
    },
    showTitle: {
        type: Boolean,
        default: true,
    },

});



const contentClasses = ref([
    "py-2 rtl:bg-gradient-to-r ltr:bg-gradient-to-l from-orange-600 to-gray-900"
])

const direction = computed(() => {
    if (document.getElementsByTagName("html")[0].getAttribute("lang") == "ar")
        return "right";
    else return "left";
});


</script>

<template>

        <JetDropdown 
           
            :align="direction"
            :width="props.width"
            :keepOpened = true
            :contentClasses= "props.showTitle == true ? contentClasses : '' "
            
        >
            <template #trigger>
                <span class="inline-flex rounded-md " >
                    <Button class="flex justify-center   " type="button" :color="props.color">
                        {{ $t("general." + props.button_title) }}
                        <FilterIcon v-if="props.iconType == 'filter'" class="rtl:mr-2 ltr:ml-2" />
                        </Button
                    >
                </span>
            </template>
    
            <template #content>
                <div v-show="props.showTitle"
                    class="flex justify-center px-4 py-1 text-xs text-gray-200 "
                >
                    {{ $t("general." + props.title) }}
    
                </div>
                <div v-show="props.showTitle" class="border-t border-gray-200" />
    
    
                <slot/> 
                <div
                :class="props.color == 'gradient_orange' ?  'from-orange-400' : 'from-zinc-900' "
                
                class="flex flex-col justify-between rtl:bg-gradient-to-r ltr:bg-gradient-to-l  to-gray-800"
                >
                    <slot name="checkedItemHeader"> </slot>
                </div>
    
                <div v-show="props.showTitle" class="border-t border-gray-100" />
            </template>
        </JetDropdown>
</template>
