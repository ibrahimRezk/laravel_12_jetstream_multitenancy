<script setup>

import { Link } from '@inertiajs/vue3'
import { getActiveLanguage } from "laravel-vue-i18n";


const props = defineProps({ 
    crumbs: {
        type: [Array, Object],
            required: true,
            default: [{}]
    } ,
    translate: {
        type: Boolean,
            default:false
    } 
})

const lang = getActiveLanguage();


</script>

<template>
    <div>
        
        <ol v-if="crumbs.length" class="breadcrumb flex mx-2"> 
            <li
                v-for="(crumb, index) in crumbs"
                class="breadcrumb-item mx-0.5"
                :class="{ active: index != crumbs.length - 1 }">


                    <Link class="text-yellow-500 text-sm  flex h-full align-middle justify-center items-center" v-if="index != crumbs.length - 1" :href="crumb.url" >
                        {{ translate ? $t('general.' +crumb.title ) : crumb.title  }} 
                        <span v-if="lang== 'ar'" class="text-gray-500  text-xs mx-2 ">  /  </span>
                        <span v-else class="text-gray-500  text-xs mx-2 ">  \  </span>
                    </Link>

                    <span class=" text-gray-400 text-sm" v-else>
                        {{ translate ? $t('general.' +crumb.title ) : crumb.title  }} 
                    </span>

            </li>
        </ol>
    </div>
</template>