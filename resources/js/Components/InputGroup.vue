<script setup>
import Input from "@/Components/Input.vue";
import Label from "@/Components/Label.vue";
import InputError from "@/Components/InputError.vue";
import { trans } from "laravel-vue-i18n";
import { computed  } from "@vue/runtime-core";
const props = defineProps({
    type: {
        type: String,
        default: "text",
    },
    step:{
        type:String,
        default: ''
    },
    label: {
        type: String,
        default: "", 
    },
    customLabel: {
        type: String,
        default: "",
    },
    labelFontSize: {
        type: String,
        default: "sm",
    },
    focusHere: {
        type: Boolean,
        default: false,
    },
    translationFolder: {
        type: String,
        default: "",
    },
    errorMessage: {
        type: String,
        default: "", 
    },

    printMode: {
        type: Boolean,
        default: false,
    },
  
});


const modelValue = defineModel()


const tanslatedLabel = computed(()=>{
    return trans(props.translationFolder+props.label)
})



</script>

<template>


    <div>
        <div >
            <Label :print-mode="props.printMode" :fontSize="props.labelFontSize" :customLabel="props.customLabel" v-if="label" :value="tanslatedLabel" class="mx-1"/>
            <Input
            :print-mode="props.printMode"
            :focusHere="props.focusHere"
                :type="type"
                :step="step"
                class="mt-1 block w-full "
                v-bind="$attrs"
                v-model="modelValue"
            />
        </div>
        <InputError v-if="errorMessage" class="mt-1" :message="errorMessage" />
    </div>
</template>
