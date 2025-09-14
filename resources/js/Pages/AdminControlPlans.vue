<script setup>
import Layout from "@/Layouts/Authenticated.vue";
import useDialogModal from "@/Composables/useDialogModal.js";

import { router, useForm, usePage, Head } from "@inertiajs/vue3";
import DialogModal from "@/Components/DialogModal.vue";
import Modal from "@/Components/ConfirmationModal.vue";

import { ref, toRef } from "vue";
import { watch } from "vue";
import useDeleteItem from "@/Composables/useDeleteItem";
import Container from "@/Components/Container.vue";
import Card from "@/Components/Card/Card.vue";
import Button from "@/Components/Button.vue";
import Label from "@/Components/Label.vue";
import Input from "@/Components/Input.vue";
import InputError from "@/Components/InputError.vue";
import AddNew from "@/Components/AddNew.vue";

const props = defineProps({
    plans: {
        type: Array,
        default: () => [],
    },
    title: {
        type: String,
    },
    method: {
        type: String,
    },
    popularPlan: {
        type: Object,
        required: {},
    },
    routeResourceName: {
        type: String,
        required: "",
    },

    errors: {
        type: Object,
        default: () => {},
    },
});

const opened = ref(0);
const method = ref("");
const showScreenExeptSubmenu = ref(false);
const routeResourceName = ref(props.routeResourceName);
const editMode = ref(false);
// const isDialogOpen = ref(false);

const form = useForm({
    name: "",
    description: "",
    product_id_on_stripe: "",
    price_id_on_stripe: "",
    price: "",
    currency: "",
    interval: "",
    trial_days: "",
    features: [],
});

const addNewFeature = () => {
    if (form.features.length > 0) {
        let emptyInput = form.features.find((item) => item == "");
        emptyInput != "" ? form.features.push("") : "";
    } else {
        form.features.push("");
    }
};

const removeFeature = (index) => {
    form.features.splice(index, 1);
};

const {
    closeDialogModal,
    dialogModal,
    itemToSave,
    isSaving,
    showDialogModal,
    showEditModal,
    handleSavingItem,
} = useDialogModal({
    routeResourceName: routeResourceName,
    form: form,
    opened,
    showScreenExeptSubmenu,
    method,
    editMode,
});

const {
    close,
    deleteModal,
    itemToDelete,
    isDeleting,
    showDeleteModal,
    handleDeleteItem,
    deleteMultipleItems,
} = useDeleteItem({
    routeResourceName: props.routeResourceName,
});

const fillForm = (item) => {
    Object.keys(form).forEach((key) =>
        item[key] !== undefined ? (form[key] = item[key]) : ""
    );

    form.features = [];

    item.features.forEach((i) => form.features.push(formatFeature(i)));
};

const emptyErrors = () => {
    Object.keys(props.errors).forEach((error) => (props.errors[error] = ""));
};

const fireshowDialogModal = () => {
    editMode.value = false;
    emptyErrors();
    showDialogModal();
};

const fireShowEditModal = (item) => {
    console.log("fireShowEditModal", item);
    form.reset();
    editMode.value = true;
    method.value = "update";
    emptyErrors();
    fillForm(item);
    showEditModal(item);
};

const addNewOrEdit = () => {
    return editMode.value == true ? editPlan() : addNewPlan();
};

const editPlan = () => {
    editMode.value == true;
    method.value = "update";
    routeResourceName.value = `${props.routeResourceName}`;
    return handleSavingItem();
};

const addNewPlan = () => {
    // console.log(props.routeResourceName)
    editMode.value == false;
    method.value = "store";
    routeResourceName.value = `${props.routeResourceName}`;
    return handleSavingItem();
};

// const submit = () => {
//     if (props.action == "edit") {
//         editPlan();
//     } else {
//         addNewPlan();
//     }
// };

// watch(
//     () => props.isDialogOpen,
//     () => (props.action == "edit" ? fillForm(props.item) : "")
// );

// watch(
//     () => props.isDialogOpen,
//     () => (props.isDialogOpen == false ? form.reset() : "")
// );

// watch(
//     () => props.isDialogOpen,
//     () => (isDialogOpen.value = props.isDialogOpen)
// );

watch(
    () => dialogModal.value,
    () => (dialogModal.value == false ? closeModal() : "")
);

// const emit = defineEmits(["close"]);

const closeModal = () => {
    editMode.value = false;
    form.reset();
};

const formatPrice = (price) => {
    return parseFloat(price).toFixed(2) * 1;
};

const formatFeature = (feature) => {
    return feature
        .split("_")
        .map((word) => word.charAt(0).toUpperCase() + word.slice(1))
        .join(" ");
};

// const breadcrumbs = [
//     {
//         title: "Dashboard",
//         href: "/dashboard",
//     },
// ];
</script>

<template>
    <Head title="plans" />

    <Layout>
        <template #breadcrumbs>
            <!-- <bread-crumbs :crumbs="breadcrumbs"></bread-crumbs> -->
        </template>
        <template #header>
            {{ $t("general." + title) }}
        </template>
        <Container>
            <AddNew>
                <Button color="gradient_blue" @click="fireshowDialogModal">
                    {{ $t("general.add new plan") }}
                </Button>
            </AddNew>

            <div
                class="mt-5 grid w-full gap-5 lg:grid-cols-2 xl:grid-cols-3 2xl:grid-cols-4"
            >
                <Card
                    v-for="(plan, index) in plans"
                    :key="plan.id"
                    class="h-full grid"
                >
                    <div class="grid grid-cols-1 w-full h-full">
                        <div>
                            <div class="relative">
                                <div
                                    class="absolute -top-6 left-1/2 transform -translate-x-1/2 mt-1"
                                    v-if="plan.id == popularPlan?.id"
                                >
                                    <span
                                        class="bg-gradient-to-r from-orange-500 to-red-500 text-white px-3 py-0.5 rounded-full text-sm font-medium"
                                    >
                                        Most Popular
                                    </span>
                                </div>
                                {{ plan.name }}
                            </div>
                            <div>
                                {{ plan.description }}
                            </div>
                        </div>
                        <div class="flex flex-col h-full gap-5 justify-between">
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
                                    {{ plan.trial_days }} days free trial
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
                                                {{ formatFeature(feature) }}
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div>
                                <div class="flex flex-col gap-2 w-full">
                                    <Button
                                        color="transparent_green"
                                        @click="fireShowEditModal(plan)"
                                        class="w-full hover:cursor-pointer"
                                    >
                                        edit
                                    </Button>
                                    <Button
                                        color="gradient_red"
                                        @click="showDeleteModal(plan)"
                                        class="w-full hover:cursor-pointer"
                                    >
                                        delete
                                    </Button>
                                </div>
                            </div>
                        </div>
                    </div>
                </Card>
            </div>
        </Container>

        <!-- <AlertDialog v-model:open="deleteModal">

            <AlertDialogContent>
                <AlertDialogHeader>
                    <AlertDialogTitle>
                        Are you absolutely sure?
                        <br />
                        delete plan :
                         {{ itemToDelete[0].name }}
                    </AlertDialogTitle>
                    <AlertDialogDescription>
                        This action cannot be undone. This will permanently
                        delete your account and remove your data from our
                        servers.
                    </AlertDialogDescription>
                </AlertDialogHeader>
                <AlertDialogFooter>
                    <AlertDialogCancel>Cancel</AlertDialogCancel>
                    <AlertDialogAction
                        @click="handleDeleteItem"
                        :disabled="isDeleting"
                    >
                        <span v-if="isDeleting">deleting</span>
                        <span v-else> delete </span>
                    </AlertDialogAction>
                </AlertDialogFooter>
            </AlertDialogContent>
        </AlertDialog> -->
    </Layout>

    <DialogModal :show="dialogModal" @close="closeDialogModal">
        <template #title> add new plan </template>

        <template #content>
            <div class="sm:max-w-[800px]">
                <form @submit.prevent="submit">
                    <div class="grid grid-cols-2 gap-5">
                        <div>
                            <div class="grid gap-2 mt-4">
                                <Label for="name"> name </Label>
                                <Input
                                    id="name"
                                    v-model="form.name"
                                    type="text"
                                    class="mt-1 block w-full"
                                    autofocus
                                    autocomplete="name"
                                />
                                <InputError
                                    class="mt-2"
                                    :message="props.errors?.name"
                                />
                            </div>
                            <div class="grid gap-2 mt-4">
                                <Label for="name"> description </Label>
                                <Input
                                    id="description"
                                    v-model="form.description"
                                    type="text"
                                    class="mt-1 block w-full"
                                    autofocus
                                    autocomplete="description"
                                />
                                <InputError
                                    class="mt-2"
                                    :message="props.errors?.description"
                                />
                            </div>
                            <div class="grid gap-2 mt-4">
                                <Label for="name"> product id on stripe </Label>
                                <Input
                                    id="product_id_on_stripe"
                                    v-model="form.product_id_on_stripe"
                                    type="text"
                                    class="mt-1 block w-full"
                                    autofocus
                                    autocomplete="product_id_on_stripe"
                                />
                                <InputError
                                    class="mt-2"
                                    :message="
                                        props.errors?.product_id_on_stripe
                                    "
                                />
                            </div>
                            <div class="grid gap-2 mt-4">
                                <Label for="name"> price id on stripe </Label>
                                <Input
                                    id="price_id_on_stripe"
                                    v-model="form.price_id_on_stripe"
                                    type="text"
                                    class="mt-1 block w-full"
                                    autofocus
                                    autocomplete="price_id_on_stripe"
                                />
                                <InputError
                                    class="mt-2"
                                    :message="props.errors?.price_id_on_stripe"
                                />
                            </div>
                            <div class="grid gap-2 mt-4">
                                <Label for="name"> price </Label>
                                <Input
                                    id="price"
                                    v-model="form.price"
                                    type="number"
                                    step="0.01"
                                    class="mt-1 block w-full"
                                    autofocus
                                    autocomplete="price"
                                />
                                <InputError
                                    class="mt-2"
                                    :message="props.errors?.price"
                                />
                            </div>
                            <div class="grid gap-2 mt-4">
                                <Label for="name"> currency </Label>
                                <Input
                                    id="currency"
                                    v-model="form.currency"
                                    class="mt-1 block w-full"
                                    autofocus
                                    autocomplete="currency"
                                />
                                <InputError
                                    class="mt-2"
                                    :message="props.errors?.currency"
                                />
                            </div>
                            <div class="grid gap-2 mt-4">
                                <Label for="name"> interval </Label>
                                <Input
                                    id="interval"
                                    v-model="form.interval"
                                    type="text"
                                    class="mt-1 block w-full"
                                    autofocus
                                    autocomplete="interval"
                                />
                                <InputError
                                    class="mt-2"
                                    :message="props.errors?.interval"
                                />
                            </div>
                            <div class="grid gap-2 mt-4">
                                <Label for="name"> trial days </Label>
                                <Input
                                    id="trial_days"
                                    v-model="form.trial_days"
                                    type="number"
                                    step="0.01"
                                    class="mt-1 block w-full"
                                    autofocus
                                    autocomplete="trial_days"
                                />
                                <InputError
                                    class="mt-2"
                                    :message="props.errors?.trial_days"
                                />
                            </div>
                        </div>
                        <div class="border rounded border-gray-300/50 p-2">
                            <div
                                class="text-gray-300 font-bold border-b border-orange-200/20"
                            >
                                features
                            </div>

                            <div class="flex justify-center items-center">
                                <ul class="  ">
                                    <li
                                        v-for="(
                                            feature, index2
                                        ) in form.features"
                                        :key="index2"
                                        class="flex justify-center items-center mt-3 gap-3"
                                    >
                                        <div
                                            class="flex w-full align-middle items-center gap-4"
                                        >
                                            <div
                                                class="text-gray-300 border rounded-full px-1 text-xs"
                                            >
                                                {{ index2 + 1 }}
                                            </div>

                                            <Input
                                                id="name"
                                                v-model="form.features[index2]"
                                                type="text"
                                                class="mt-1 block w-full"
                                                autofocus
                                                autocomplete="name"
                                            />

                                            <!-- <Button small color="gradient_white">
                                                             {{ formatFeature(feature) }}
                                                         </Button> -->
                                        </div>

                                        <!-- <InputGroup
                                 nolabel
                                 class="px-8 col-span-6 w-full"
                                 v-model="form.features[index2]"
                                 :message="props.errors?.features"
                             /> -->

                                        <div>
                                            <Button
                                                @click="removeFeature(index2)"
                                                medium
                                                color="gradient_red"
                                                class="mt- hover:cursor-pointer mx-1 px-4 hover:scale-110 curser-pointer"
                                            >
                                                {{ "-" }}
                                            </Button>
                                        </div>

                                        <InputError
                                            class="mt-2"
                                            :message="
                                                props.errors[
                                                    'features.' + index2
                                                ]
                                            "
                                        />
                                    </li>
                                    <div class="mt-3 flex justify-center">
                                        <Button
                                            @click="addNewFeature"
                                            medium
                                            color="gradient_blue"
                                            class="hover:cursor-pointer mx-1 px-4 hover:scale-110 curser-pointer"
                                        >
                                            {{ "+" }}
                                        </Button>
                                    </div>
                                </ul>
                                <InputError
                                    class="mt-2"
                                    :message="props.errors?.features"
                                />
                            </div>
                        </div>
                    </div>

                    <div class="gap-2 mt-8">
                        <Button
                            @click="addNewOrEdit"
                            class="hover:cursor-pointer"
                            type="submit"
                            :disabled="form.processing"
                        >
                            confirm
                        </Button>
                    </div>
                </form>
            </div>
        </template>
    </DialogModal>

    <Modal :show="deleteModal" @close="close">
        <template #title>
            <span class="text-red-800">{{ $t("general.delete plan") }} : </span>
            {{ itemToDelete[0].name }}
        </template>
        <template #content>
            {{ $t("general.confirmation") }}
        </template>
        <template #footer>
            <Button @click="handleDeleteItem" color="red">
                <span>{{ $t("general.confirm") }}</span>
            </Button>
        </template>
    </Modal>
</template>
