<script setup>
import Layout from "@/Layouts/Authenticated.vue";
import BreadCrumbs from "@/Components/BreadCrumbs.vue";
import Container from "@/Components/Container.vue";
import InputError from "@/Components/InputError.vue";
import Button from "@/Components/Button.vue";
import Card from "@/Components/Card/Card.vue";
import { Head, useForm } from "@inertiajs/vue3";
import { ref,toRef,  computed, watch, onMounted } from "vue";
import CustomHeaderButton from "@/Components/CustomHeaderButton.vue"; 
import useHeaders from "@/Composables/useHeaders.js";
import Table from "@/Components/Table/Table.vue";
import Td from "@/Components/Table/Td.vue";
import Actions from "@/Components/Table/Actions.vue";
import Modal from "@/Components/ConfirmationModal.vue";
import useDialogModal from "@/Composables/useDialogModal.js";
import DialogModal from "@/Components/DialogModal.vue";
import Label from "@/Components/Label.vue";
import Input from "@/Components/Input.vue";
import SelectGroup from "@/Components/SelectGroup.vue";
import AddNew from "@/Components/AddNew.vue";
import Filters from "./Filters.vue";
import useDeleteItem from "@/Composables/useDeleteItem.js";
import useFilters from "@/Composables/useFilters.js";
import CheckboxGroup from "@/Components/CheckboxGroup.vue";
import Checkbox from "@/Components/Checkbox.vue";
import InputGroup from "@/Components/InputGroup.vue";

import { trans } from "laravel-vue-i18n";
import { router } from "@inertiajs/vue3";
import axios from "axios";
import { template } from "lodash";

const props = defineProps({

        edit: {
        type: Boolean,
        default: false,
    },
    title: {
        type: String,
    },
    items: {
        type: Object,
        default: () => {},
    },

    plans: {
        type: Array,
        default: () => [],
    },
    errors: {
        type: Object,
        default: () => {},
    },
    routeResourceName: {
        type: String,
        default: () => '',
    },
        headers: {
        type: Array,
        default: () => [],
    },
        filters: {
        type: Object,
        default: () => ({}),
    },
    errors: {
        type: Object,
        default: () => {},
    },
        method: String,

});


const tenants = toRef(props, "tenants");


///////////////////////filter headers/////////////////////////////////////////////////////
const { filterHeadersMethod, showColumnItems, finalHeaders, filteredHeaders } =
    useHeaders({
        dbHeaders: props.headers,
        headers: props.headers,
        prepairFilteredHeaders: props.headers,
    });

watch(
    () => filteredHeaders.value,
    () => filterHeadersMethod()
);

// /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

const opened = ref(0);
const method = ref("");
const showScreenExeptSubmenu = ref(false);
const routeResourceName = ref(props.routeResourceName);
const editMode = ref(false);

const form = useForm({
    name: "",
    email: "",
    password: "",
    password_confirmation: "",
    subdomain: "",
    plan_id: "",

});





const active = computed(() => {
    return form.active == true ? trans("general.yes") : trans("general.no");
});

const emptyErrors = () => {
    Object.keys(props.errors).forEach((error) => (props.errors[error] = ""));
};

const fireshowDialogModal = () => {
    editMode.value = false;
    emptyErrors();
    showDialogModal();
};

const fireShowEditModal = (item) => {
    form.reset();
    console.log(item)
    editMode.value = true;
    method.value = "update";
    emptyErrors();
    fillForm(item);
    showEditModal(item);
};


const fillForm = (item) => {
    Object.keys(form).forEach((key) =>
        item[key] !== undefined 
            ? (form[key] = item[key])
            : ""
    );

    form.subdomain = item?.domains[0].domain;
    form.plan_id = item?.currentSubscription?.plan_id;
};

const addNewOrEdit = () => {
    return editMode.value == true ? editSubscription() : addNewSubscription();
};

const editSubscription = () => {
    editMode.value == true;
    method.value = "changeSubscription";
    routeResourceName.value = `${props.routeResourceName}`;
    return handleSavingItem();
};

const addNewSubscription = () => {
    // console.log(props.routeResourceName)
    editMode.value == false;
    method.value = "subscribe";
    routeResourceName.value = `${props.routeResourceName}`;
    return handleSavingItem();
};

const type = trans("general.type");

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


// const itemToBeDeleted = ref();

const fireShowCancelSubscription = (item) => {
        // itemToBeDeleted.value = item; 
deleteModal.value = true
itemToDelete.value = item

};




const cancelTenants = () => {
    deleteModal.value = false
    router.delete(
        route("admin.tenants.cancelSubscription", {
            // tenantIds: itemToBeDeleted.value?.id,
            tenantIds: itemToDelete.value?.id,
            // tenantIds: checkedItems.value,
        }),
        {
            preserveScroll: true,
            preserveState: true,
            onBefore: () => {
                // isDeleting.value = true;
            },
            onSuccess: () => {
                // deleteModal.value = false;
                // itemToDelete.value = [];
                // ids.value = [];
                // deleteMultipleItems.value = false;
            },
            onFinish: () => {
                // isDeleting.value = false;
                // usePage().props.menus.forEach((menu) => {
                //     menu.isActive
                //         ? (menu.open = true)
                //         : (menu.open = false);
                // });
            },
        }
    );
};


const { filters, isLoading, isFilled, resetFilter } = useFilters({
    filters: props.filters,
    routeResourceName: props.routeResourceName,
    method: props.method,
});

// const checkedItems = ref([]);
// const checkAllItems = () => {
//     if (!checkedItems.value.length) {
//         props.items.data.forEach((item) => {
//             // props.items.data.forEach((item) => {
//             // if (item.can.delete == true) {
//                 checkedItems.value.push(item);
//             // }
//         });
//     } else {
//         checkedItems.value = [];
//     }
// };
// const checkedAllButton = ref(false);

// watch(
//     () => checkedItems.value,
//     () =>
//         checkedItems.value.length > 0
//             ? (checkedAllButton.value = true)
//             : (checkedAllButton.value = false)
// );

// const deleteAll = () => {
//     deleteMultipleItems.value = true;
//     showDeleteModal(checkedItems.value);
// };

// const headerFooterClasses = computed(() => {
//     return " from-zinc-800 via-orange-200  to-zinc-900 ";
// });

const DialogModalClasses = computed(() => {
    return "from-orange-300 to-zinc-800 ";
});

// watch(
//     () => checkedItems.value.length,
//     () =>
//         checkedItems.value.length > 0
//             ? (checkedAllButton.value = true)
//             : (checkedAllButton.value = false)
// );

// watch(
//     () => deleteMultipleItems.value,
//     () => (deleteMultipleItems.value == false ? (checkedItems.value = []) : "")
// );

const filtersValuesData = ref({});
const filtersValuesDataMethod = (data) => {
    filtersValuesData.value = data;
};

const animate = ref(true);
const startLeaveAnimation = () => {
    animate.value = false;
};
</script>

<template>
    <Head :title="title" />

    <Layout>
        <template #breadcrumbs>
            <!-- <bread-crumbs :crumbs="breadcrumbs"></bread-crumbs> -->
        </template>

        <template #header>
            {{ $t("general." + title) }}
        </template>
        <Container :animate="animate">
            <AddNew
                :show="isFilled"
                @reset="resetFilter"
            >
                <Button
                    color="gradient_blue"
                    @click="fireshowDialogModal"
                >
                    {{ $t("general.add new tenant") }}
                </Button>

                <!-- <template #filters>
                    <Filters
                        :account_types="props.all_account_types"
                        v-model="filters"
                        :is-loading="isLoading"
                        no-padding
                    />
                </template> -->

                <!-- /////////////////////////////////////////custum headers /////////////////////////////////////////////////// -->
                <template #customHeaderButton>
                    <CustomHeaderButton
                        :showTitle="false"
                        button_title="filter"
                        color="gradient_black"
                        width="60"
                        iconType="filter"
                    >
                        <Filters
                            :stores="props.stores"
                            v-model="filters"
                            @filtersValuesData="filtersValuesDataMethod"
                            :is-loading="isLoading"
                            no-padding
                        />
                    </CustomHeaderButton>
                    <CustomHeaderButton>
                        <template #checkedItemHeader>
                            <div
                                v-for="(header, index) in props.headers"
                                :key="index"
                            >
                                <Label
                                    class="mx-1 mt-2 mb-2 rtl:bg-gradient-to-r ltr:bg-gradient-to-l from-yellow-500 via-orange-600 to-red-900 px-2 py-1 rounded shadow-md border border-gray-300"
                                >
                                    <div class="flex justify-between">
                                        <div>
                                            <h1 class="text-gray-200">
                                                <h1 class="text-gray-200">
                                                    {{
                                                        $t(
                                                            "general." +
                                                                header.name
                                                        )
                                                    }}
                                                </h1>
                                            </h1>
                                        </div>
                                        <div>
                                            <input
                                                class="rounded mx-1 border-gray-300 text-indigo-600 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50"
                                                type="checkbox"
                                                :id="header[index]"
                                                :value="header"
                                                v-model="filteredHeaders"
                                            />
                                        </div>
                                    </div>
                                </Label>
                            </div>
                        </template>
                    </CustomHeaderButton>
                </template>
                <!-- //////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////// -->
            </AddNew>

            <!-- <Card class=""> -->
            <Table
                @startLeaveAnimation="startLeaveAnimation"
                :headers="finalHeaders"
                :items="items"
                noNamePadding
                noCheckAll
                class="mt-2"
            >
                <template #title>
                    <span
                        v-if="
                            Object.keys(props.filters).length == 0 ||
                            (Object.keys(props.filters).length == 1 &&
                                Object.keys(props.filters)[0] == 'page') ||
                            (Object.keys(props.filters).length == 1 &&
                                Object.keys(props.filters)[0] ==
                                    'paginationNumber') ||
                            (Object.keys(props.filters).length == 2 &&
                                (Object.keys(props.filters)[0] ==
                                    'paginationNumber' ||
                                    Object.keys(props.filters)[1] ==
                                        'paginationNumber'))
                        "
                    >
                        {{ $t("general.list_of") }}
                        {{ $t("general." + props.title) }}
                    </span>

                    <span v-else class=" ">
                        <span class="text-yellow-100 px-2 text-sm">
                            {{ $t("general.active filters") }} :
                        </span>
                        <!-- <span
                        v-if="props.filters.store_ids"
                    >
                        {{ $t("general.list_of") }}
                    </span> -->

                        <span class="flex flex-wrap">
                            <span v-for="(f, i) in filtersValuesData" :key="i">
                                <Button
                                    v-if="f.data"
                                    class="mx-1 flex justify-between mt-2 items-center"
                                    small
                                    color="transparent_yellow"
                                >
                                    {{ $t("general." + i) }} :
                                    <Button
                                        class="rtl:mr-1 ltr:ml-1 my-1 flex"
                                        small
                                        color="transparent_yellow"
                                    >
                                        <span>
                                            {{ f.data }}
                                        </span>
                                        <span
                                            class="rtl:mr-2 ltr:ml-2 text-xs my-1"
                                            @click="filters[f.id] = ''"
                                        >
                                            x
                                        </span>
                                    </Button>
                                </Button>
                            </span>
                            <Button
                                class="w-40 mx-1 mt-2"
                                small
                                color="transparent_red"
                            >
                                <span @click="resetFilter">{{
                                    $t("general.reset filter")
                                }}</span>
                            </Button>
                        </span>
                    </span>
                </template>
                <template v-slot="{ item, index }">
                    <!-- //////////////////////////checked row item///////////////////////// -->
               
                    <!-- ///////////////////////////////////////////////////// -->
                    <Td light v-show="showColumnItems('#')">
                        {{ items.meta.from + index }}
                    </Td>

                    <Td light v-show="showColumnItems('name')">
                        <Button color="gradient_orange" small class="">
                            {{ item.name }}
                        </Button>
                    </Td>
                    <Td light v-show="showColumnItems('email')">
                        <Button color="gradient_orange" small class="">
                            {{ item.email }}
                        </Button>
                    </Td>
                    <Td light v-show="showColumnItems('status')">
                        <Button color="gradient_orange" small class="">
                            {{ item.status }}
                        </Button>
                    </Td>
                    <Td light v-show="showColumnItems('plan')">
                        <Button color="gradient_orange" small class="">
                            {{ item.plan }}
                        </Button>
                    </Td>
               
                    <Td light v-show="showColumnItems('interval')">
                        <Button color="gradient_orange" small class="">
                            {{ item.interval }}
                        </Button>
                    </Td>
                    <Td light v-show="showColumnItems('price')">
                        <Button color="gradient_orange" small class="">
                            {{ item.price *1 }}
                        </Button>
                    </Td>
                    <Td light v-show="showColumnItems('created at')">
                        <Button wrap color="gradient_orange" small class=" min-w-20 ">
                            {{ item.created_at }}
                        </Button>
                    </Td>
                    <Td light v-show="showColumnItems('ends at')">
                        <Button wrap color="gradient_orange" small class="min-w-20">
                            {{ item.ends_at }}
                        </Button>
                    </Td>
                    <Td light v-show="showColumnItems('trial ends at')">
                        <Button wrap color="gradient_orange" small class=" min-w-20 ">
                            {{ item.trial_ends_at }}
                        </Button>
                    </Td>

                    <Td v-show="showColumnItems('actions')">
       

                        <Actions
                            :showEditModal="true"
                            :show-edit="false"
                            :show-delete="false"

                        >
                        <template #button>
                            <Button  color="gradient_blue" class=" mx-1"  @click="fireShowEditModal(item)">
                                {{ 'edit' }}
                            </Button>
                            <Button color="gradient_red" @click="fireShowCancelSubscription(item)">
                                {{ 'cancel' }}
                            </Button>

                        </template>
                        </Actions>
                    </Td>
                </template>
            </Table>
            <!-- </Card> -->
        </Container>
    </Layout>

    <Modal :show="deleteModal" @close="close">
        <template #title>
            <span class="text-red-800">{{ $t("general.cancel subscription") }} : </span>
            {{
                 itemToDelete.name
            }}
        </template>
        <template #content>
            {{ $t("general.confirmation") }}
        </template>
        <template #footer>
            <Button
                @click="cancelTenants"
                color="red"
            >
                <span >{{ $t("general.confirm") }}</span>
            </Button>
        </template>
    </Modal>

    <!-- //////////////////////////Dialog Modal/////////////////////////////////////// -->

    <DialogModal
        :show="dialogModal"
        @close="closeDialogModal"
        :classes="DialogModalClasses"
        maxWidth="xl"
    >
        <template #title>
            {{
                editMode == true
                    ? $t("general.edit tenant")
                    : $t("general.add new tenant")
            }}
        </template>

        <template #content>
            <form @submit.prevent="addNewOrEdit">
                <div class="grid grid-cols-1 gap-2 mb-1">
                     <InputGroup
                        class="mt-1 px-0"
                        label="name"
                        translationFolder="general."
                        v-model="form.name"
                        :errorMessage="props.errors.name"
                    />

                    <InputGroup
                        class="mt-1 px-0"
                        label="email"
                        type="email"
                        translationFolder="general."
                        v-model="form.email"
                        :errorMessage="props.errors.email"
                    />

              

                <InputGroup
                        class="mt-1 px-0"
                        label="password"
                        type="password"
                        translationFolder="general."
                        v-model="form.password"
                        :errorMessage="props.errors.password"
                    />
                    <InputGroup
                        class="mt-1 px-0"
                        label="password_confirmation"
                        type="password"
                        translationFolder="general."
                        v-model="form.password_confirmation"
                        :errorMessage="props.errors.password_confirmation"
                    />

                 <InputGroup
                        class="mt-1 px-0"
                        label="subdomain"
                        translationFolder="general."
                        v-model="form.subdomain"
                        :errorMessage="props.errors.subdomain"
                    />

                <SelectGroup
                :label="`${$t('general.plan')}`"
                v-model="form.plan_id"
                :items="props.plans"
            />

                </div>
                <div class="flex justify-center mt-4">
                    <button
                        :disabled="isSaving"
                        type="submit"
                        class="mb-2 px-12 py-1 rounded-full text-white bg-gradient-to-l from-orange-800 to-orange-500 hover:from-orange-900 hover:to-orange-500 border-orange-100 duration-300 capitalize tracking-wider ease-in-out hover:scale-110 shadow-black drop-shadow-2xl shadow-2xl border text-sm"
                    >
                        {{
                            isSaving ? $t("general.saving") : $t("general.save")
                        }}
                    </button>
                </div>
            </form>
        </template>
    </DialogModal>
</template>
