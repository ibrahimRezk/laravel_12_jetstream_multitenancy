<script setup>
import { trans } from 'laravel-vue-i18n';
import { computed  } from "@vue/runtime-core";


const props = defineProps({
    value: String,
    customLabel: {
        type: String,
        default: "",
    },
    fontSize : {
        type: String,
        default: 'sm',
    },

    printMode: {
        type: Boolean,
        default: false,
    },
});

const sizeClasses= computed(()=>{
    return {
        'sm':'text-sm ',
        'xs':'text-xs mb-2'
    }[props.fontSize]
})


const modeClasses = computed(()=>{
   return  props.printMode ?  'text-gray-900 ' : 'text-gray-100 '
})

const classes = computed(()=>{
    return  `${sizeClasses.value} ${modeClasses.value} block font-medium `
})


const customLableClasses = computed(()=>{
    return  ` ${modeClasses} bmx-3 text-xs  w-full `
})




const customLabel = computed(()=>{
    return trans(`general.${props.customLabel}`)
})
</script>

<template>
    <label :class="classes" >
        <span v-if="value">{{ value }}

            <span  v-if="props.customLabel" :class="customLableClasses">{{customLabel}}</span>
        </span>
        
        <span v-else><slot /></span>
    </label>
</template>
