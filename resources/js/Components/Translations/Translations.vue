<script setup>

import { computed , onMounted, ref } from "vue";
import { wTrans } from "laravel-vue-i18n";

const props = defineProps({
    label: {
        type: String,
        default: "",
    },
    textSize: {
        type: String,
        default: "text-sm",
    },
  
    active: Boolean, 
    default : false,
});

const sidebarlabel = ref('')

onMounted(() => {
    return sidebarlabel.value =  wTrans(`mainMenu.` + props.label);
})

// const sidebarlabel = computed(() => {
//     return trans(`mainMenu.` + props.label);
// });

const colorClasses = computed(() => { 
        return props.active ?  "text-black font-semibold  " :  ""

}); 
const classes = computed(() => { 
        return `group-hover:text-zinc-500 ${props.textSize} ${colorClasses}`

}); 



</script>

<template>
    <div  :class="classes" >
        {{ sidebarlabel }}
    </div>
</template>
