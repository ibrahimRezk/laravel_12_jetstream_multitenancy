<script setup>
import { Link } from '@inertiajs/vue3';

const props = defineProps({
    href: String,
    as: String,
});
</script>

<template>
    <div>
        <button v-if="props.as == 'button'" type="submit" class="flex items-start justify-start px-4 py-2 text-sm  rtl:text-right ltr:trxt-left leading-5 text-gray-700 dark:text-gray-200 hover:bg-gray-100 dark:hover:text-gray-800 dark:hover:bg-gray-300   focus:outline-none focus:bg-gray-100 transition duration-150 ease-in-out w-full ">
            <slot />
        </button>

        <a v-else-if="props.as =='a'"  :href="props.href"  class="block px-4 py-2 text-sm  rtl:text-right ltr:trxt-left leading-5 text-gray-700 dark:text-gray-200 hover:bg-gray-100 dark:hover:text-gray-800 dark:hover:bg-gray-300  focus:outline-none focus:bg-gray-100 transition duration-150 ease-in-out">
            <slot />
        </a>

        <Link v-else :href="props.href" class="block px-4 py-2 text-sm leading-5 text-gray-700 dark:text-gray-200 hover:bg-gray-100 dark:hover:text-gray-800 dark:hover:bg-gray-300  rtl:text-right ltr:trxt-left  focus:outline-none focus:bg-gray-100 transition duration-150 ease-in-out">
            <slot />
        </Link>
    </div>
</template>
