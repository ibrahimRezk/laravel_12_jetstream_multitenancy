<script setup>
import { computed  } from "@vue/runtime-core";

const props = defineProps({
    value: String,
    class:{
        type: String,
        default: '',
        imprtant : true
    },
       printMode: {
        type: Boolean,
        default: false,
    },
});

const modeClasses = computed(()=>{
   return  props.printMode ?  'text-gray-700 ' : 'text-gray-700 dark:text-gray-300 '
})

const classes = computed(()=>{
    return  `${props.class} ${modeClasses} block font-medium text-sm  `
})  
</script>

<template>
    <label 
    :class="classes"
     >
        <span v-if="props.value">{{ props.value }}</span>
        <span v-else><slot /></span>
    </label>
</template>
