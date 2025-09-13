<script setup>
import { computed } from "vue";
import { Link } from '@inertiajs/vue3'

const props = defineProps({  

    type: {
        type: String,
        default: "button",
    },
    href: {
        type: String,
        default: "",
    },
    color: {
        type: String,
        default: "blue",
    },
    custom: {
        type: String, 
        default: "",
    },
    
    small: {
        type: Boolean,
        default: false,
    },
    medium: {
        type: Boolean,
        default: false,
    },
    big: {
        type: Boolean,
        default: false,
    },
    maxWidth: {
        type: Number,
        default:  0,
    },
    truncate: { /// works with maxwidth to make button content limited to button width and extra text will show as ...
        type: Boolean,
        default: false,
    },
    wrap: { /// works with maxwidth to make button content limited to button width and extra text will move the next line if wrap is true
        type: Boolean,
        default: false,
    },
    
}); 


//// make color live change ////////////////////////////
const emit = defineEmits(['update:color']); 
const classes = computed({
    get() {
        return `  max-w-${props.maxWidth}   rounded px-2 capitalize tracking-wider focus:outline-none transition ease-in-out duration-150 ${props.wrap == false ? ' text-nowrap' : ''}  ${props.truncate == true ? 'truncate' : ''}  ${colorClasses.value} ${sizeClasses.value} ${submitButtonClasses.value}`;
    },
    
    set(val) {
        emit('update:color', val);
    },
});
///////////////////////////////////////////////////////////

const submitButtonClasses = computed(()=>{ 
    return {
     submit : 'hover:scale-110 curser-pointer' ,
     button : 'cursor-default' ,
     default :  'cursor-default'
}[props.type]
})
// const submitButtonClasses = computed(()=>{ return props.type == 'submit'  ? 'hover:scale-110 curser-pointer' :  props.type == 'button' ? 'curser-pointer' : 'cursor-default'})

const colorClasses = computed(() => { 
    return {

        lime:"text-gray-900 bg-gradient-to-r from-lime-200 via-lime-400 to-lime-500 hover:bg-gradient-to-br focus:ring-4 focus:outline-none focus:ring-lime-300 dark:focus:ring-lime-800 shadow-lg shadow-lime-500/50 dark:shadow-lg dark:shadow-lime-800/80 font-medium rounded-lg text-sm px-10 py-2.5 text-center me-2 mb-2 transition duration-300 ease-in-out transform hover:scale-105" ,
        cyan:"text-white bg-gradient-to-r from-cyan-400 via-cyan-500 to-cyan-600 hover:bg-gradient-to-br focus:ring-4 focus:outline-none focus:ring-cyan-300 dark:focus:ring-cyan-800 shadow-lg shadow-cyan-500/50 dark:shadow-lg dark:shadow-cyan-800/80 font-medium rounded-lg text-sm px-10 py-2.5 text-center me-2 mb-2 transition duration-300 ease-in-out transform hover:scale-105",
        violet:"bg-gradient-to-r from-blue-500 to-purple-500 text-white py-2 px-4 rounded transition duration-300 ease-in-out transform hover:scale-105",
        roundedBlue:"bg-blue-500 hover:bg-blue-700 mt-5 text-white  py-2 px-4 rounded-full border border-blue-500 shadow-md transition duration-300 ease-in-out transform hover:scale-105",
        colorized: "bg-gradient-to-r from-[#F10E17] via-[#F1770E] to-[#F10E88]  text-white  rounded-xl border border-gray-200/40 shadow-md transition duration-300 ease-in-out transform hover:scale-105 text-sm ", 
        
        
        black: "font-normal bg-gray-800 hover:bg-gray-900 active:bg-gray-900 focus:border-white/50 focus:shadow-outline-gray text-white  shadow-md border border-zinc-500 text-shadow-none ",   
        gradient_black: "bg-gradient-to-l font-normal  from-black to-gray-800 hover:from-black hover:to-gray-900 active:bg-gray-900 focus:border-white/50 focus:shadow-outline-gray text-white  shadow-md border border-zinc-500 text-shadow-none    ",
        gradient_black_normal: " bg-gradient-to-l font-normal  from-black to-gray-800 hover:from-black hover:to-gray-900 active:bg-gray-900 focus:border-white/50 focus:shadow-outline-gray text-white  shadow-md border border-zinc-500 text-shadow-none   ",

        red: "font-normal bg-red-600 hover:bg-red-700 active:bg-red-700 focus:border-white/50 focus:shadow-outline-white text-white border border-red-100 ",
        gradient_red: "font-normal bg-gradient-to-l from-red-700 to-red-800 hover:from-red-800 hover:to-red-900 active:bg-red-700/20 focus:border-red-400 focus:shadow-outline-white text-white border border-red-500  ",
        orange: "font-normal  bg-orange-600 hover:bg-orange-900 active:bg-orange-900 focus:border-white/50 focus:shadow-outline-orange text-white  shadow-md border border-orange-300 text-shadow-none ",
        gradient_orange: `font-normal bg-gradient-to-l from-orange-800  to-orange-400 hover:from-gray-900 hover:to-orange-500 active:bg-orange-900 focus:border-white/50 focus:shadow-outline-orange text-white  shadow-md border border-orange-400 text-shadow-none   ${props.custom} `,
        gradient_orange_normal: `font-normal bg-gradient-to-l from-orange-800  to-orange-400 hover:from-gray-900 hover:to-orange-500 active:bg-orange-900 focus:border-white/50 focus:shadow-outline-orange text-white  shadow-md border border-orange-300 text-shadow-none   ${props.custom} `,
        green: "font-normal bg-lime-600 hover:bg-lime-700 active:bg-lime-700 focus:border-lime-700 focus:shadow-outline-lime text-white shadow-md border border-lime-300 ",
        gradient_green: "font-normal bg-gradient-to-l from-lime-900 to-lime-600 hover:from-lime-600 hover:to-lime-400 active:bg-lime-700 focus:border-lime-700 focus:shadow-outline-lime text-white shadow-md border border-lime-600   ",
        blue: "bg-sky-600 hover:bg-sky-700 active:bg-sky-700 focus:bg-sky-700 focus:border-blue-700 focus:shadow-outline-blue text-white  shadow-xl border border-lime-400 ",
        gradient_blue: " font-normal bg-gradient-to-l from-blue-800 to-blue-500 hover:from-blue-600 hover:to-blue-400 active:bg-blue-700 focus:bg-blue-700 focus:border-blue-700 focus:shadow-outline-blue text-white  shadow-xl border border-blue-400  ",
        white: "bg-gray-200 hover:bg-gray-100 active:bg-gray-50 focus:border-gray-300 focus:shadow-outline-gray text-black  font-bold drop-shadow-md border ",
        gradient_white: "bg-gradient-to-l  from-gray-400 to-gray-200 hover:from-gray-500 hover:to-gray-300 active:bg-gray-50 focus:border-gray-300 focus:shadow-outline-gray text-black border border-gray-500  font-bold drop-shadow-md border border-black  ",
        gradient_white_normal: "bg-gradient-to-l from-gray-400 to-gray-200 hover:from-gray-500 hover:to-gray-300 active:bg-gray-50 focus:border-gray-300 focus:shadow-outline-gray text-black border border-gray-500  font-bold drop-shadow-md border border-black ",
        yellow: "bg-yellow-100 hover:bg-yellow-50 active:bg-red-500 focus:border-gray-200 focus:shadow-outline-black text-black font-bold border border-gray-300  border border-yellow-700  font-normal",
        gradient_yellow: " bg-gradient-to-l from-yellow-800 to-yellow-500 hover:from-gray-900 hover:to-yellow-500 active:bg-yellow-900 focus:border-white/50 focus:shadow-outline-yellow text-white font-normal  shadow-md border border-yellow-600 text-shadow-none  ",
        gradient_yellow_normal: " bg-gradient-to-l from-yellow-800 to-yellow-500 hover:from-gray-900 hover:to-yellow-500 active:bg-yellow-900 focus:border-white/50 focus:shadow-outline-yellow text-white font-normal  shadow-md border border-yellow-300 text-shadow-none  ",
        
        transparent_white: "bg-gradient-to-l  from-transparent via-gray-200/50 to-transparent hover:from-gray-500 hover:to-gray-300 active:bg-gray-50 focus:border-gray-300 focus:shadow-outline-gray text-black dark:text-gray-200 dark:font-normal dark:via-gray-800 border border-gray-200/20  font-bold drop-shadow-md   ",

        transparent_yellow: " align-middle  rounded-lg shadow-sm ring-1 transition duration-75  focus-within:ring-20 focus-within:ring-orange-300/20 ring-yellow-500/30  border-none  text-base text-yellow-500  placeholder:text-gray-400 focus:ring-1   xs:text-xs sm:leading-6    bg-zinc-900/80 transpare    focus:border-yellow-400  focus:ring-yellow-200/40 focus:ring-opacity-60 ",
        transparent_red: " align-middle  rounded-lg shadow-sm ring-1 transition duration-75  focus-within:ring-20 focus-within:ring-orange-300/20 ring-red-500/30  border-none  text-base text-red-400  placeholder:text-gray-400 focus:ring-1   xs:text-xs sm:leading-6    bg-zinc-900 transpare    focus:border-red-400  focus:ring-red-200/40 focus:ring-opacity-60 ",
        transparent_orange: " align-middle  rounded-lg shadow-sm ring-1 transition duration-75  focus-within:ring-20 focus-within:ring-orange-300/20 ring-orange-500/30  border-none  text-base text-orange-300  placeholder:text-gray-400 focus:ring-1   xs:text-xs sm:leading-6    bg-zinc-900/80 transpare    focus:border-orange-400  focus:ring-orange-200/40 focus:ring-opacity-60 ",
        transparent_green: " align-middle  rounded-lg shadow-sm ring-1 transition duration-75  focus-within:ring-20 focus-within:ring-orange-300/20 ring-lime-500/30  border-none  text-base text-lime-400  placeholder:text-gray-400 focus:ring-1   xs:text-xs sm:leading-6    bg-zinc-900/80 transpare    focus:border-lime-400  focus:ring-lime-200/40 focus:ring-opacity-60 ",
        transparent_blue: " align-middle  rounded-lg shadow-sm ring-1 transition duration-75  focus-within:ring-20 focus-within:ring-orange-300/20 ring-blue-500/30  border-none  text-base text-blue-400  placeholder:text-gray-400 focus:ring-1   xs:text-xs sm:leading-6    bg-zinc-900/80 transpare    focus:border-blue-400  focus:ring-blue-200/40 focus:ring-opacity-60 ",

        transparent_black: " align-middle  rounded-lg shadow-sm ring-1 transition duration-75  focus-within:ring-20 focus-within:ring-orange-300/20 ring-orange-500/30  border-none  text-base text-gray-400  placeholder:text-gray-400 focus:ring-1   xs:text-xs     bg-zinc-900/80 transpare    focus:border-orange-400  focus:ring-orange-200/40 focus:ring-opacity-60 ",
    }[props.color]; 
});

// in transparent black this part deleted sm:leading-6 to make button smaller vertically
const sizeClasses = computed(() => { 
    if(props.small){
        return "px-1   rounded text-xs"
    }else if(props.big){
        return " px-1 py-5 mx-5 rounded text-xl "
    }else if(props.medium){
        return "px-1 py-0.5  rounded text-sm"
    }else{
        return "px-1 py-1 rounded text-xs"
    }
});



</script>

<template>
        <Link v-if="href"

            type="button"
              :href="href"
              :class="classes">
        <slot />
        </Link>
        <button v-else

                :type="type"
                :class="classes">
            <slot />
        </button>
</template>
