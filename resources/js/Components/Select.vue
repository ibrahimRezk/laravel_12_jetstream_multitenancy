<script setup>
import { computed, onMounted, ref } from "vue";
import { trans } from "laravel-vue-i18n";


const props = defineProps({
    items: Array,
    itemText: {
        type: String,
        default: "",
    },
    itemValue: {
        type: String,
        default: "id",
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
    passIdWithName: { 
        type: Boolean,
        default: false,
    },
    disabledCheckKey: { 
        type: String,
        default: '',
    },
    disabled_id: { 
        type: String,
        default: null ,
    },
    focus: { 
        type: Boolean,
        default: false,
    },

});

const modelValue = defineModel()

const options = computed(() => {
    
    if (props.withoutSelect) return props.items;
    return [
        { 
        [props.itemText]: trans('general.select'),
        [props.itemValue]: "" 
        },
        ...props.items,
    ];
});


const extra_text_value = computed(()=>{
    return trans('general.' + props.extra_text)
})



const select = ref(null);

onMounted(() => {
    if (select.value.hasAttribute("autofocus")) {  
        select.value.focus();
    }
}); 


const disabled = (item)=> {
   return  item[props.disabledCheckKey] ?? item.id == props.disabled_id  ?? false
    

}



</script>

<template>
    
    <!-- class="      [&_option]:bg-zinc-700  [&_option]:text-zinc-200    -->
    <select v-model="modelValue"
    :autofocus = "props.focus"
    
    ref="select"
    class="          

    block w-full   rounded-lg shadow-sm ring-1 transition duration-75  focus-within:ring-20 focus-within:ring-primary-600 ring-white/30  border-none py-1.5 text-base   placeholder:text-zinc-400 focus:ring-1   sm:text-sm sm:leading-6    bg-white/10    text-slate-100 focus:border-yellow-500  focus:ring-yellow-100 focus:ring-opacity-60
    
    "
    >
    <!-- class=" block w-full  rounded-lg shadow-sm ring-1 transition duration-75  focus-within:ring-20 focus-within:ring-primary-600 ring-white/30  border-none py-1.5 text-base text-gray-950  placeholder:text-gray-400 focus:ring-1   sm:text-sm sm:leading-6    bg-white/10 transpare   text-gray-200 focus:border-yellow-500  focus:ring-yellow-100 focus:ring-opacity-60" -->

    
    <!-- class="  border-gray-300 py-1 mb-0.5 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 rounded-md shadow-sm w-full block " -->
            
           <!-- // down here we use JSON.parse to pass object as string then reconvert it to string because we can not pass object from here -->

        <option v-for="item in options"  
                :disabled="disabled(item)"
                :key="item[itemValue]"
                :value="props.passIdWithName == true  &&  item.id > 0  ? JSON.stringify(item) :  item[itemValue]"
                :class="item.id == props.disabled_id ? 'bg-gray-600 text-zinc-500' : 'bg-zinc-700 text-zinc-200'"

                
                >
            {{item[itemText]}} 

            <!-- this line above has a response of showing option name in menu and selected one witch come from database -->
            <span  v-if="props.extra_item_data && item[props.extra_item_data] ">  {{ '(' }} {{ item[extra_item_data]}} {{ ')' }}</span>
            <span  v-if="props.extra_text && item[props.extra_text] ">  {{ '(' }}  {{extra_text_value}}  {{ ')' }}</span>
        </option>
    </select> 
</template>










<!-- 

      .multiselect__content-wrapper {
    position: absolute;
    display: block;
    background: #202225;
    width: 100%;
    max-height: 240px;
    overflow: auto;
    border: 1px solid #e8e8e8;
    border-top: none;
    border-bottom-left-radius: 5px;
    border-bottom-right-radius: 5px;
    z-index: 50;
    -webkit-overflow-scrolling: touch;
  }

  
.multiselect__tags {
    min-height: 40px;
    display: block;
    padding: 8px 40px 0 8px;
    border-radius: 5px;
    border: 1px solid #e8e8e8;
    background: #6866662a;
    font-size: 14px;
  }


  .multiselect__placeholder {
    color: #c9d1cb;
    display: inline-block;
    margin-bottom: 10px;
    padding-top: 2px;
  }


  .multiselect__input,
  .multiselect__single {
    position: relative;
    display: inline-block;
    min-height: 20px;
    line-height: 20px;
    border: none;
    background: #68666600;
    padding: 0 0 0 5px;
    width: calc(100%);
    transition: border 0.1s ease;
    box-sizing: border-box;
    margin-bottom: 8px;
    vertical-align: top;
  }

  .multiselect {
    box-sizing: content-box;
    display: block;
    position: relative;
    width: 100%;
    min-height: 40px;
    text-align: left;
    color: #c9d1cb;
    
  }

  .multiselect__input::placeholder {
    color: #c9d1cb;
    
  }



  .multiselect__option--highlight {
    background: #376c8b;
    outline: none;
    color: #dbdfe0;
    
  }

  .multiselect__option--selected {
    background: #61b861;
    color: #35495e;
    font-weight: bold;
  }


 -->
