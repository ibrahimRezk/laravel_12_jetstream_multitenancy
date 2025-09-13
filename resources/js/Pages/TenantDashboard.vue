<script setup>
import Layout from "@/Layouts/Authenticated.vue";
import BreadCrumbs from "@/Components/BreadCrumbs.vue";
import Button from "@/Components/Button.vue";
import { Head, router, useForm, usePage } from "@inertiajs/vue3";
import LoadingAnimationPartly from "@/Components/LoadingAnimationPartly.vue";
import { ref, onMounted, nextTick, watch, toRef } from "vue";
import SelectGroup from "@/Components/SelectGroup.vue";
import Widget from "@/Components/Widget.vue";
import InputGroup from "@/Components/InputGroup.vue";
import { Deferred } from "@inertiajs/vue3";
import { getActiveLanguage } from "laravel-vue-i18n";
import { useSubscription } from "@/Composables/useSubscription";
import { Link } from "@inertiajs/vue3";

const lang = getActiveLanguage();

const props = defineProps({
    title: {
        type: String,
        required: true,
    },

    tenantSubscription: Object,

    routeResourceName: {
        type: String,
        required: true,
    },

    breadcrumbs: {
        type: [Array, Object],
        default: [{}],
    },
});

const {
    fetchPlans,
    // currentActiveSubscription,
    fetchTenantSubscription,
    cancelSubscription,
} = useSubscription();
</script>

<template>
    <!-- <Head :title="title" /> -->

    <Layout :title="title">
        <template #breadcrumbs>
            <bread-crumbs :crumbs="breadcrumbs"></bread-crumbs>
        </template>

        <template #header>
            {{ $t("general." + title) }}
        </template>

        <div>
            <div class="overflow-y-auto px-4 sm:px-0 z-50">
                <div>
                    <div
                        class="rtl:bg-gradient-to-r ltr:bg-gradient-to-l from-orange-300/20 to-zinc-800/50 dark:from-gray-900 dark:via-gray-700 dark:to-gray-900"
                    >
                        <div
                            class="p-3 rtl:bg-gradient-to-r from-orange-100 to-zinc-700 dark:from-gray-500 dark:via-transparent dark:to-gray-500 ltr:bg-gradient-to-l rounded-md pb-4 px-3 dark:border border-gray-200 dark:border-gray-200/20 shadow-lg"
                        >
                            <div
                                class="p-4 mt-1 dark:border border-gray-200/30 rounded-md rtl:bg-gradient-to-r from-orange-400/20 to-zinc-900/30 dark:from-gray-900 dark:via-zinc-900 dark:to-gray-900 ltr:bg-gradient-to-l shadow-2xl shadow-zinc-800 drop-shadow-md"
                            >
                                <div
                                    class="flex justify-center text-gray-950 dark:text-gray-300 text-lg"
                                >
                                    <span> {{ $t("general.welcome") }} </span> :
                                    <span
                                        class="font-bold mx-2 dark:font-normal"
                                        >{{
                                            $page.props?.auth?.user?.name
                                        }}</span
                                    >
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="grid auto-rows-min gap-4 md:grid-cols-3">
            <div
                class="relative aspect-video overflow-hidden rounded-xl border border-sidebar-border/70 dark:border-sidebar-border"
            >
                <Button
                    v-show="!props.tenantSubscription"
                    variant="outline"
                    @click="fetchPlans('tenant')"
                    class="flex items-center gap-x-3 rounded-lg px-3 py-2 text-sm font-medium hover:bg-accent hover:cursor-pointer"
                >
                    choose a paln
                    <span class="text-red-500"
                        >..........(only view if no subscription)</span
                    >
                </Button>

                <!-- :href="route('tenant.getTenantSubscription')" -->
                <Button
                    v-show="props.tenantSubscription"
                    variant="outline"
                    @click="
                        fetchTenantSubscription(
                            'tenant',
                            props.tenantSubscription?.tenant_id
                        )
                    "
                    class="flex items-center gap-x-3 rounded-lg px-3 py-2 text-sm font-medium hover:bg-accent hover:cursor-pointer"
                >
                    view subscription
                    <span class="text-green-500"
                        >............. (only view if subscription exists)</span
                    >
                </Button>
                <!-- <Link v-show="currentActiveSubscription"
                        :href="route('tenant.getTenantSubscription')"
                        class="flex items-center gap-x-3 rounded-lg px-3 py-2 text-sm font-medium hover:bg-accent"
                    >
                        view subscriptions <span class=" text-green-500">............. (only view if subscription exists)</span>
                    </Link> -->

                <Link
                    v-show="props.tenantSubscription"
                    :href="
                        route('tenant.plans', {
                            type: 'changePlan',
                        })
                    "
                    class="flex items-center gap-x-3 rounded-lg px-3 py-2 text-sm font-medium hover:bg-accent"
                >
                    change paln
                    <span class="text-green-500"
                        >............. (only view if subscription exists)</span
                    >
                </Link>

                <Link
                    :href="route('tenant.addUser')"
                    class="flex items-center gap-x-3 rounded-lg px-3 py-2 text-sm font-medium hover:bg-accent"
                >
                    add tenant user
                    <span class="text-orange-500"
                        >........(add random user)</span
                    >
                </Link>

                <form
                    v-show="props.tenantSubscription"
                    @submit.prevent="
                        cancelSubscription(
                            'tenant',
                            props.tenantSubscription?.tenant_id
                        )
                    "
                    class="flex items-center gap-x-3 rounded-lg px-3 py-2 text-sm font-medium hover:bg-accent"
                >
                    <button type="submit" class="hover:cursor-pointer">
                        cancel subscription
                        <span class="text-green-500"
                            >............. (only view if subscription
                            exists)</span
                        >
                    </button>
                </form>
            </div>
        </div>
    </Layout>
</template>
