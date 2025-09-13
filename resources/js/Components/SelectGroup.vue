<script setup>
import Input from "@/Components/Input.vue";
import Label from "@/Components/Label.vue";
import InputError from "@/Components/InputError.vue";
import Select from "@/Components/Select.vue";
import { trans } from "laravel-vue-i18n";
import { computed  } from "@vue/runtime-core";

const props = defineProps({  
    items: Array,
    itemText: {
        type: String,
        default: "name",
    },
    itemValue: { 
        type: String,
        default: "id",
    },
    customLabel: {
        type: String,
        default: "",
    },
    extra_text: { 
        type: String,
        default: "",
    },
    extra_item_data: { 
        type: String,
        default: "",
    },
    withoutSelect: {
        type: Boolean,
        default: false, 
    },
    flex: {
        type: Boolean,
        default: false, 
    },
    label: {
        type: String,
        default: "",
    },
    translationFolder: {
        type: String,
        default: "",
    },


    errorMessage: {
        type: String,
        default: "",
    },
    passIdWithName: { 
        type: Boolean,
        default: false,
    },
    disabled_id: { 
        type: String,
        default: null ,
    },
    disabledCheckKey: { 
        type: String,
        default: '',
    },
});

const flex = props.flex ? 'flex' : ''

const tanslatedLabel = computed(()=>{
    return trans(props.translationFolder+props.label)
})

const modelValue = defineModel()

</script>

<template> 
    <div class=" ">
        <div :class="flex">
            <Label class=" flex  align-middle items-center text-nowrap" :customLabel="props.customLabel" v-if="label" :value="tanslatedLabel"  />


        <Select class="mt-1 text-xs" 
                v-model="modelValue" 
                @update="$emit('update:modelValue', $event)"
                :items="items"
                :item-text="itemText"
                :item-value="itemValue"
                :extra_text="extra_text"
                :extra_item_data="extra_item_data"
                :passIdWithName="passIdWithName"
                :without-select="withoutSelect"
                :translationFolder="translationFolder"
                :disabledCheckKey="props.disabledCheckKey"
                :disabled_id="props.disabled_id"
                v-bind="$attrs" />
            </div>
        <InputError v-if="errorMessage" 
                    class="mt-1"
                    :message="errorMessage" />
    </div>
</template>