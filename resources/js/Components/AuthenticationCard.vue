<script setup>
import { onMounted } from "vue";

onMounted(() => {
    // On page load or when changing themes, best to add inline in `head` to avoid FOUC
    if (
        !("theme" in localStorage) &&
        window.matchMedia("(prefers-color-scheme: dark)").matches
    ) {
        document.documentElement.classList.add("dark");
    } else if (
        !("theme" in localStorage) &&
        !window.matchMedia("(prefers-color-scheme: dark)").matches
    ) {
        document.documentElement.classList.remove("dark");
    } else if (
        localStorage.theme === "dark" ||
        (!("theme" in localStorage) &&
            window.matchMedia("(prefers-color-scheme: dark)").matches)
    ) {
        document.documentElement.classList.add("dark");
    } else {
        document.documentElement.classList.remove("dark");
    }
});
</script>
<template>
    <div
    class="bg-gray-100 min-h-screen flex flex-col sm:justify-center items-center pt-6 sm:pt-0 bg-no-repeat bg-cover bg-[url('../../../assets/img/2420.jpg')]"
>
    <div
        class="w-full sm:max-w-md mt-6 px-6 py-4 bg-black overflow-hidden sm:rounded-lg border border-zinc-600 bg-opacity-40 shadow-black shadow-2xl"
    >
        <slot />
    </div>
</div>
    <!-- <div
        class="min-h-screen flex flex-col sm:justify-center items-center pt-6 sm:pt-0 bg-gray-100 dark:bg-gray-900"
    >
        <div>
            <slot name="logo" />
        </div>

        <div
            class="w-full sm:max-w-md mt-6 px-6 py-4 bg-white dark:bg-gray-800 shadow-md overflow-hidden sm:rounded-lg"
        >
            <slot />
        </div>
    </div> -->
</template>
