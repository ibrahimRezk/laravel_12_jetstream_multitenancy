<script setup>
import Button from "@/Components/Button.vue";

defineProps({
    message: String,
});
</script>

<template>
    <div v-show="message">
        <!-- <span  class="text-sm px-2 text-red-400  dark:text-red-400 ">
            {{ message }}
        </span> -->
        <!-- <span>
          <h1 class=" text-xs text-red-300  [text-shadow:_0_1px_0_var(--tw-shadow-color)]"> {{ message }}</h1> 
        </span> -->
        <span  class="text-xs px-2 text-yellow-50 rounded-full  bg-gradient-to-b from-transparent  to-transparent via-red-600">
           *  {{ message }}
        </span>
        <!-- <span  class="text-xs px-2 text-red-100   rounded border  border-white/10 rtl:bg-gradient-to-l ltr:bg-gradient-to-r from-red-950  to-red-950 via-black">
            {{ message }}
        </span> -->
        <!-- <Button disabled color="transparent_white"> {{ message }}  </Button> -->
    </div>
</template>
