<script setup>
import { onMounted, watch ,computed, ref } from 'vue';

const props = defineProps({
    focusHere: {
        type: Boolean,
        default: false,
    },

    type: {
        type: String,
        default: "text",
    },
    step:{
        type:String,
        default: ''
    },
    printMode: {
        type: Boolean,
        default: false,
    },
    
});

const modelValue = defineModel()

defineEmits(['update:focusHere']);


const input = ref(null);

onMounted(() => {

    if (input.value.hasAttribute('autofocus')) {
        input.value.focus();
    }
});

watch(()=> props.focusHere,
()=>     props.focusHere == true ? input.value.focus() : '' 
)


defineExpose({ focus: () => input.value.focus() });

// watch(()=> focusHere,
// () => focusHere == true ? console.log('yes')  : console.log('no'))
// // input.value.focus()s 


// const setNewAttribute = computed({
//     get() {
//         focusHere;
//     },
    
//     set(val) {
//         emit('update:focusHere', val);
//     },
// });

const modeClasses = computed(()=>{
   return  props.printMode ?  'text-gray-900 ring-black/30' : 'text-gray-100 ring-white/30'
})

const classes = computed(()=>{
    return  `${modeClasses.value} block w-full  rounded-lg shadow-sm ring-1 transition duration-75  focus-within:ring-20 focus-within:ring-primary-600 ring-white/30  border-none py-1.5 text-base   placeholder:text-gray-300 dark:placeholder:text-gray-400 focus:ring-1   sm:text-sm sm:leading-6 px-3   bg-white/10 transpare    focus:border-yellow-500  focus:ring-yellow-100 focus:ring-opacity-60`
})

</script>

<template>
    <input
    autocomplete="off" 
        ref="input"
        :type="props.type"
        :step="props.step"
        :class=" classes"

        :name="modelValue"
        v-model="modelValue">

</template>

