<script setup>
import Checkbox from "@/Components/Checkbox.vue";
import Label from "@/Components/Label.vue";
import InputError from "@/Components/InputError.vue";
import { computed } from 'vue';

const props = defineProps({
    disabled:{
        type:Boolean,
        default:false
    },
    label: { 
        type: String,
        default: "",
    },
    errorMessage: {
        type: String,
        default: "",
    },
    value: {
        type: [String,Number,Object],
        default: null,
    },
});

// const modelValue = defineModel();
const checked = defineModel('checked')




</script> 

<template>
    <div>
        <Label class="">
            <input
            :value="props.value"
            v-model="checked"
            :disabled="props.disabled"
            type="checkbox"
            class="rounded my-2  text-yellow-600 dark:text-slate-400/10 dark:border-gray-300/50 checked:border-gray-100/50 border dark:bg-black/40  border-gray-300 shadow-sm focus:ring-indigo-500 checked:hover:border-gray-200"
            v-bind="$attrs"
    >
            <span class=" mx-3">{{ label }}</span>
        </Label>

        <InputError v-if="errorMessage"
                    class="mt-1"
                    :message="errorMessage" />
    </div>





    <!-- <div>
    <Label>
        <Checkbox 
        :value="props.value"
        v-model="checked"
        :disabled="props.disabled"
      />
        <span class=" mx-3">{{ label }}</span>
    </Label>

    <InputError v-if="errorMessage"
                class="mt-1"
                :message="errorMessage" />
</div> -->

</template>

<!-- this below makes event run twice -->
<!-- <div>
    <Label>
        <Checkbox 
        :value="value" :checked="checked"
        @update:checked="$emit('update:checked', $event)"
        v-bind="$attrs" />
        <span class=" mx-3">{{ label }}</span>
    </Label>

    <InputError v-if="errorMessage"
                class="mt-1"
                :message="errorMessage" />
</div> -->