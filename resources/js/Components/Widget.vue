<script setup>
import { Link } from '@inertiajs/vue3';
import { computed  , onMounted} from "vue";


const props = defineProps({
    color: {
        type: String,
        default: "",
    },
    paddingY: {
        type: String,
        default: '5',
    },
    openNewPage: {
        type: Boolean,
        default: true,
    },
    hasLink: {
        type: Boolean,
        default: true,
    },
    title: String,
    href: String,
    value: Number,

});



const color = computed(() => { 
    return {
        lime: {
            bg100 : 'bg-lime-100 dark:bg-zinc-900',
            bg500 : 'bg-lime-500',
            bg500_20 : 'bg-lime-500/20',
            text500 : 'text-lime-500',
            text700 : 'text-lime-700',
            gorup_hover_bg_400 : 'group-hover:bg-lime-400',
            gorup_hover_text_900 : 'group-hover:btextlime-900',
           } ,
        sky: {
            bg100 : 'bg-sky-100 dark:bg-zinc-900',
            bg500 : 'bg-sky-500',
            bg500_20 : 'bg-sky-500/20',
            text500 : 'text-sky-500',
            text700 : 'text-sky-700',
            gorup_hover_bg_400 : 'group-hover:bg-sky-400',
            gorup_hover_text_900 : 'group-hover:text-sky-900',
           } ,
        green: {
            bg100 : 'bg-green-100 dark:bg-zinc-900',
            bg500 : 'bg-green-500',
            bg500_20 : 'bg-green-500/20',
            text500 : 'text-green-500',
            text700 : 'text-green-700',
            gorup_hover_bg_400 : 'group-hover:bg-green-400',
            gorup_hover_text_900 : 'group-hover:bgtextreen-900',
           } ,
        orange: {
            bg100 : 'bg-orange-100 dark:bg-zinc-900',
            bg500 : 'bg-orange-500',
            bg500_20 : 'bg-orange-500/20',
            text500 : 'text-orange-500',
            text700 : 'text-orange-700',
            gorup_hover_bg_400 : 'group-hover:bg-orange-400',
            gorup_hover_text_900 : 'group-hover:bg-textange-900',
           } ,
        red: {
            bg100 : 'bg-red-100 dark:bg-zinc-900',
            bg500 : 'bg-red-500',
            bg500_20 : 'bg-red-500/20',
            text500 : 'text-red-500',
            text700 : 'text-red-700',
            gorup_hover_bg_400 : 'group-hover:bg-red-400',
            gorup_hover_text_900 : 'group-hover:text-red-900',
           } ,
        danger: {
            bg100 : 'bg-danger-100 dark:bg-zinc-900',
            bg500 : 'bg-danger-500',
            bg500_20 : 'bg-danger-500/20',
            text500 : 'text-danger-500',
            text700 : 'text-danger-700',
            gorup_hover_bg_400 : 'group-hover:bg-danger-400',
            gorup_hover_text_900 : 'group-hover:bg-textnger-900',
           } ,
        yellow: {
            bg100 : 'bg-yellow-100 dark:bg-zinc-900',
            bg500 : 'bg-yellow-500',
            bg500_20 : 'bg-yellow-500/20',
            text500 : 'text-yellow-500',
            text700 : 'text-yellow-700',
            gorup_hover_bg_400 : 'group-hover:bg-yellow-400',
            gorup_hover_text_900 : 'group-hover:bg-textllow-900',
           } ,
        fuchsia: {
            bg100 : 'bg-fuchsia-100 dark:bg-zinc-900',
            bg500 : 'bg-fuchsia-500',
            bg500_20 : 'bg-fuchsia-500/20',
            text500 : 'text-fuchsia-500',
            text700 : 'text-fuchsia-700',
            gorup_hover_bg_400 : 'group-hover:bg-fuchsia-400',
            gorup_hover_text_900 : 'group-hover:bg-ftexthsia-900',
           } ,
        pink: {
            bg100 : 'bg-pink-100 dark:bg-zinc-900',
            bg500 : 'bg-pink-500',
            bg500_20 : 'bg-pink-500/20',
            text500 : 'text-pink-500',
            text700 : 'text-pink-700',
            gorup_hover_bg_400 : 'group-hover:bg-pink-400',
            gorup_hover_text_900 : 'group-hover:btextpink-900',
           } ,
        indigo: {
            bg100 : 'bg-indigo-100 dark:bg-zinc-900',
            bg500 : 'bg-indigo-500',
            bg500_20 : 'bg-indigo-500/20',
            text500 : 'text-indigo-500',
            text700 : 'text-indigo-700',
            gorup_hover_bg_400 : 'group-hover:bg-indigo-400',
            gorup_hover_text_900 : 'group-hover:bg-textdigo-900',
           } ,

 

    }[props.color]; 
});



</script>

<template>
    <div
    :class="`group  relative  py-${props.paddingY} px-20  overflow-hidden shadow-xl ${color?.bg100} ring-1 ring-gray-900/5 transition-all duration-300 hover:-translate-y-1 hover:shadow-2xl sm:rounded-lg sm:px-10`"
    >
    <div class="relative z-10 mx-auto max-w-md">
        <div class=" flex justify-between ">
            <span :class="`absolute   -z-10 h-20 w-20 rounded-full  ${color?.bg500}  transition-all duration-300 group-hover:scale-[15] `"/>
            <span :class="`grid   h-20 z-10 w-20 place-items-center rounded-full  ${color?.bg500} transition-all duration-300 ${color?.gorup_hover_bg_400}`">
            
                <slot/>
    
            </span>
            <span :class="`grid text-base font-semibold  leading-7  h-20 z-10 w-20 place-items-center rounded-full ${color?.text700} ${color?.bg500_20} transition-all duration-300 ${color?.gorup_hover_bg_400} ${color?.gorup_hover_text_900} `"> 
                {{ value }}
            </span>
        </div>
        <div
            class="space-y-6 pt-5 text-base leading-7 text-gray-600 transition-all duration-300 group-hover:text-white/90">
            <p> {{ $t("general." + title ) }} </p>
        </div>
        <div v-if="hasLink" class="pt-5 text-base font-semibold leading-7">
            <p >
                <a v-if="props.openNewPage" :href="href" target="_blank"  :class="`${color?.text500} transition-all duration-300 group-hover:text-white`">
                     {{ $t('general.more') }}
                    &rarr;
                </a>
                <Link v-else :href="href"  :class="`${color?.text500} transition-all duration-300 group-hover:text-white`">
                    
                    {{ $t('general.more') }}
                   &rarr;
                </Link>
            </p>
        </div>
    </div>
</div>

</template>
