<script setup>
import AppLayout from "@/Layouts/Authenticated.vue";
import { Head } from "@inertiajs/vue3";

import { useSubscription } from "@/Composables/useSubscription";
import Button from "@/Components/Button.vue";

const { fetchPlans, fetchTenants } = useSubscription();
</script>

<template>
    <Head title="Dashboard" />

    <AppLayout >
        <div class="flex w-64 h-full flex-1 flex-col gap-4 rounded-xl p-4">
            <Button color="gradient_orange" @click="fetchTenants()" class="hover:cursor-pointer py-2">
                view all tenants
            </Button>
            <Button color="gradient_white" @click="fetchPlans('admin')" class="hover:cursor-pointer py-2">
                view plans
            </Button>
        </div>
    </AppLayout>
</template>
