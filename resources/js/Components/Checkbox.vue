<script setup>
import { ref } from 'vue';


const props = defineProps({

    disabled:{
        type:Boolean,
        default:false
    },

    value: {
        type: [String,Number,Object],
        default: null,
    },

});

// const modelValue = defineModel()
const checked = defineModel('checked')
const input = ref(null);


</script>


<template>
    <input
  
    ref="input"
    :value="props.value"
    v-model="checked"
    :disabled="props.disabled"
    type="checkbox"
    class=" rounded text-yellow-600 dark:text-slate-400/10 dark:border-gray-300/50 checked:border-gray-100/50 border dark:bg-black/40  border-gray-300 shadow-sm focus:ring-indigo-500 checked:hover:border-gray-200"
>
</template>
