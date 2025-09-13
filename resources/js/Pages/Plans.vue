<script setup>
import { ref, computed, watch, onMounted } from "vue";
import { router, useForm, usePage } from "@inertiajs/vue3";
import { useSubscription } from "@/Composables/useSubscription";
import Layout from "@/Layouts/Authenticated.vue";
import Container from "@/Components/Container.vue";
import { Head } from "@inertiajs/vue3";
import useDialogModal from "@/Composables/useDialogModal.js";
import DialogModal from "@/Components/DialogModal.vue";
import Card from "@/Components/Card/Card.vue";
import Button from "@/Components/Button.vue";

const props = defineProps({
    plans: {
        type: Array,
        default: () => [],
    },
    tenantId: {
        type: Number,
        default: () => 0,
        required: true,
    },
    planChoosed: {
        type: Boolean,
        default: () => 0,
        required: false,
    },
    planPaid: {
        type: Boolean,
        default: () => 0,
        required: false,
    },

    plan: {
        type: Object,
        default: () => {},
    },

    subscription: {
        type: Object,
        default: () => {},
    },
    type: {
        type: String,
        default: () => null,
    },

    errors: {
        type: Object,
        default: () => {},
    },
    routeResourceName: {
        type: String,
        required: true,
    },
    method: String,
    breadcrumbs: {
        type: [Array, Object],
        default: [{}],
    },
});

const opened = ref(0);
const method = ref("");
const activeForm = ref("");
const showScreenExeptSubmenu = ref(false);
const routeResourceName = ref(props.routeResourceName);
const editMode = ref(false);

const selectedPlan = ref(null);
const changePlan = ref(false);

const openSubscriptionModal = (plan) => {
    selectedPlan.value = plan;
    dialogModal.value = true;
};

const form = useForm({
    plan_id: selectedPlan.id,
});

const {
    closeDialogModal,
    dialogModal,
    itemToSave,
    secondDialogModal,
    thirdDialogModal,
    fourthDialogModal,
    fifthDialogModal,
    sixthDialogModal,
    seventhDialogModal,
    eighthDialogModal,
    isSaving,
    showDialogModal,
    showEditModal,
    handleSavingItem,
    handleGetData,
} = useDialogModal({
    routeResourceName: routeResourceName,
    form: form,
    opened,
    showScreenExeptSubmenu,
    method,
    editMode,
});

////////////////////////////////////////////////////////////////////////////////

onMounted(() =>
    props.planChoosed && props.planPaid
        ? (changePlan.value = true)
        : (changePlan.value = false)
);

// Methods
const formatPrice = (price) => {
    return parseFloat(price).toFixed(2);
};

const formatFeature = (feature) => {
    return feature
        .split("_")
        .map((word) => word.charAt(0).toUpperCase() + word.slice(1))
        .join(" ");
};
</script>

<template>
    <Head title="Dashboard" />

    <Layout :breadcrumbs="breadcrumbs">
        <Container>
            <div class="flex flex-col gap-4">
                <div class="container mx-auto px-4 py-8">
                    <!-- Header Section -->

                    <div class="text-center mb-12">
                        <h1 class="text-4xl font-bold mb-4">
                            Choose Your Plan
                        </h1>
                        <p class="text-xl text-gray-600">
                            Select the perfect plan for your business needs
                        </p>
                    </div>

                    <div
                        class="grid gap-5 lg:grid-cols-2 xl:grid-cols-3 place-items-center h-full"
                    >
                        <Card
                            v-for="(plan, index) in plans"
                            :key="plan.id"
                            class="flex h-full"
                        >
                            <div
                                class="p-2 h-full min-w-[280px] gap-3 flex justify-between flex-col"
                            >
                                <div>
                                    <div class="relative">
                                        {{ plan.name }}
                                    </div>
                                    <div>
                                        {{ plan.description }}
                                    </div>
                                </div>
                                <div
                                    class="flex flex-col h-full gap-5 justify-between"
                                >
                                    <div class="grid gap-4">
                                        <div class="mb-4">
                                            <span class="text-4xl font-bold">
                                                ${{ formatPrice(plan.price) }}
                                            </span>
                                            <span class="text-gray-600"
                                                >/ {{ plan.interval }}</span
                                            >
                                        </div>

                                        <!-- Trial Badge -->
                                        <div
                                            v-if="plan.trial_days > 0"
                                            class="bg-green-100/10 dark:text-green-600 text-green-800 px-3 py-1 rounded-full text-sm inline-block mb-4"
                                        >
                                            {{ plan.trial_days }} days free
                                            trial
                                        </div>

                                        <div>
                                            <div
                                                v-for="feature in plan.features"
                                                :key="feature"
                                                class="mb-4 grid grid-cols-[25px_minmax(0,1fr)] items-start pb-4 last:mb-0 last:pb-0"
                                            >
                                                <span
                                                    class="flex h-2 w-2 translate-y-1 rounded-full bg-sky-500"
                                                />
                                                <div class="space-y-1">
                                                    <p
                                                        class="text-sm font-medium leading-none"
                                                    >
                                                        {{
                                                            formatFeature(
                                                                feature
                                                            )
                                                        }}
                                                    </p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div>
                                    <div class="w-full">
                                        <div class="flex flex-col gap-2 w-full">
                                            <Button
                                                v-if="props.plan?.id != plan.id"
                                                @click="
                                                    openSubscriptionModal(plan)
                                                "
                                                class="w-full hover:cursor-pointer"
                                                color="transparent_white"
                                            >
                                                choose
                                            </Button>
                                            <Button
                                                v-else     
                                                disabled                                          
                                                class="w-full"
                                                color="transparent_green"
                                            >
                                                your plan :
                                                <br/>
                                               {{ props.subscription?.status }}
                                            </Button>
                                        </div>

                                        <div class="flex justify-center w-full">
                                            <Button
                                                v-if="
                                                    props.plan?.id == plan.id &&
                                                    props.planChoosed &&
                                                    props.planPaid == false
                                                "
                                                color="gradient_orange"
                                            >
                                                choosed but not paid yet
                                            </Button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </Card>
                    </div>
                </div>
            </div>
        </Container>

        <!-- <AlertDialog v-model:open="confirmModal">
            <AlertDialogContent>
                <AlertDialogHeader>
                    <AlertDialogTitle>
   
                        subscribe to plan : {{ selectedPlan.name }}
                    </AlertDialogTitle>
                    <AlertDialogDescription>
                        by clicking confirm you will be subscribed to this plan

                    </AlertDialogDescription>
                </AlertDialogHeader>
                <AlertDialogFooter>
                    <AlertDialogCancel>Cancel</AlertDialogCancel>

             

                    <Button
                    as="a"
                            :href="
                            route('tenant.checkout', {
                                plan_id: selectedPlan.id,
                            })
                        ">
                        confirm

                    </Button>

                </AlertDialogFooter>
            </AlertDialogContent>
        </AlertDialog> -->
    </Layout>

    <DialogModal :show="dialogModal" @close="closeDialogModal">
        <template #title> confirm </template>

        <template #content>
            <!-- <form @submit.prevent="confirm"> -->
            <div class="flex flex-col items-center justify-center mt-4">
                <div
                    class="border-y w-full border-gray-300/20 py-2 bg-gradient-to-r from-transparent via-black text-yellow-100 text-xl to-transparent flex justify-center items-center"
                >
                    subscribe to plan : {{ selectedPlan.name }}
                </div>

                <!-- <button
                        type="submit"
                        class="mb-2 px-12 py-1 mt-5 rounded-full text-white bg-gradient-to-l border-orange-100 duration-300 capitalize tracking-wider shadow-black drop-shadow-2xl shadow-2xl border text-sm ease-in-out hover:scale-110 from-orange-800 to-orange-500 hover:from-orange-900 hover:to-orange-500"
                    >
                        {{
                            isSaving ? $t("general.saving") : $t("general.yes")
                        }}
                    </button> -->
                <!-- </form> -->

                <a
                    as="a"
                    class="mb-2 px-12 py-1 mt-5 rounded-full text-white bg-gradient-to-l border-orange-100 duration-300 capitalize tracking-wider shadow-black drop-shadow-2xl shadow-2xl border text-sm ease-in-out hover:scale-110 from-orange-800 to-orange-500 hover:from-orange-900 hover:to-orange-500"
                    :href="
                        route('tenant.checkout', {
                            plan_id: selectedPlan.id,
                        })
                    "
                >
                    {{
                        isSaving ? $t("general.saving") : $t("general.confirm")
                    }}
                </a>
            </div>
        </template>
    </DialogModal>
</template>
