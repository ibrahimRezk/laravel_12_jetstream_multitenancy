<script setup>
import { Link } from "@inertiajs/vue3";
import { router, Head } from "@inertiajs/vue3";
import { onMounted, ref, computed, watch, nextTick } from "vue";
import { getActiveLanguage, isLoaded } from "laravel-vue-i18n";
import { usePage } from "@inertiajs/vue3";
import { loadLanguageAsync } from "laravel-vue-i18n";

import JetDropdown from "@/Components/Dropdown.vue";
import Translations from "@/Components/Translations/Translations.vue";
import Alert from "@/Components/Alert.vue";
import Button from "@/Components/Button.vue";
import DropdownLink from "@/Components/DropdownLink.vue";
import { toRef, toRefs } from "vue";
import SidebarIcon from "../Components/Icons/SidebarIcon.vue";

import { useGeneralStore } from "@/stores/general";
import { storeToRefs } from "pinia";
const useGeneral = useGeneralStore();
const { smallSideBar } = storeToRefs(useGeneral);
const { paginationNumber } = storeToRefs(useGeneral);

const props = defineProps({
    title: String,
    success: {
        type: String,
        default: "",
    },
    error: {
        type: String,
        default: "",
    },
});

const menus = toRef(usePage().props, "menus");

const year = ref(usePage().props.year);

const mode = ref("system");

onMounted(() => {
    // On page load or when changing themes, best to add inline in `head` to avoid FOUC
    if (
        !("theme" in localStorage) &&
        window.matchMedia("(prefers-color-scheme: dark)").matches
    ) {
        document.documentElement.classList.add("dark");
        mode.value = "system";
    } else if (
        !("theme" in localStorage) &&
        !window.matchMedia("(prefers-color-scheme: dark)").matches
    ) {
        document.documentElement.classList.remove("dark");
        mode.value = "system";
    } else if (
        localStorage.theme === "dark" ||
        (!("theme" in localStorage) &&
            window.matchMedia("(prefers-color-scheme: dark)").matches)
    ) {
        document.documentElement.classList.add("dark");
        mode.value = "dark";
    } else {
        document.documentElement.classList.remove("dark");
        mode.value = "light";
    }
});

const switchMode = (value) => {
    if (value == "dark") {
        (localStorage.theme = "dark"),
            document.documentElement.classList.remove("light"),
            document.documentElement.classList.add("dark"),
            (mode.value = "dark");
    } else if (value == "light") {
        (localStorage.theme = "light"),
            document.documentElement.classList.remove("dark"),
            document.documentElement.classList.add("light"),
            (mode.value = "light");
    } else {
        localStorage.removeItem("theme");
        mode.value = "system";
        if (window.matchMedia("(prefers-color-scheme: dark)").matches) {
            document.documentElement.classList.add("dark");
        } else {
            document.documentElement.classList.remove("dark");
        }
    }
};

const logout = () => {
    router.post(route("logout"));
};

//////////////////////////////////session issues solutions /////////////////////////////////////

onMounted(() => {
    window.axios.interceptors.response.use(
        function (response) {
            // Call was successful, don't do anything special.
            return response;
        },
        function (error) {
            switch (error?.response?.status) {
                case 401: // Not logged in
                case 419: // Session expired
                case 503: // Down for maintenance
                    // Bounce the user to the login screen with a redirect back
                    window.location.reload();

                    break;
                case 500:
                    alert("Oops, something went wrong!");
                    break;
                default:
                    // Allow individual requests to handle other errors
                    return Promise.reject(error);
            }
        }
    );
});

//////////////////////////////////////////////////////////////////////////////////////////////

const animate = ref();

onMounted(() => {
    animate.value = true;
});

const startLeaveAnimation = () => {
    paginationNumber.value = usePage().props.paginationNumber; // very important   its prevent call the page twice if we switch to another page
    animate.value = false;
};

//////////////////////////////////////////////////////////////////////////
// const userType = (usePage().props.user.roles[0].name).toLowerCase()

// const profileLink = computed(()=>{
//     return route(`${userType}.profile.show`)
// })
const profileLink = computed(() => {
    return route("profile.show");
});

// const isVisibleSidebar = ref(true)
const current_lang = document
    .getElementsByTagName("html")[0]
    .getAttribute("lang");

const loadLanguage = async (selectedLang) => {
    // isVisibleSidebar.value = true

    await loadLanguageAsync(selectedLang);

    
    // const lang = getActiveLanguage();
    document.getElementsByTagName("html")[0].getAttribute("lang") == "ar"
    ? document.getElementsByTagName("html")[0].setAttribute("dir", "rtl")
    : document.getElementsByTagName("html")[0].setAttribute("dir", "ltr");
    await router.get(route("lang", [selectedLang]));
    
    // nextTick().then(() =>  isVisibleSidebar.value = true);
};

const direction = computed(() => {
    if (document.getElementsByTagName("html")[0].getAttribute("lang") == "en")
        return "left";
    else return "right";
});

const showHideSidebar = ref(false);

const showHideClass = computed(() => {
    if (showHideSidebar.value) {
        return true;
    } else {
        return false;
    }
});

watch(
    () => showHideSidebar.value,
    () =>
        showHideSidebar.value == true
            ? (smallSideBar.value = false)
            : (smallSideBar.value = true)
);

const items = ref([]); // to be removed after making pusher notification work , i put it here to avoide error in console says property items was accessed during render but is  not  defined on instance

///////////////////////////////////////// pusher notifications /////////////////////////////////////////////
const notificationsCount = ref();
// notificationsCount.value = parseInt(usePage().props.notificationsCount);
const notifications = usePage().props.notifications;

// const showNewMessage = ref(false);
const open = ref(false);
// const items = ref([]);
// const patientDetails = ref();

// const userId = usePage().props.user.id;

// Echo.private(`create-invoice.${userId}`).listen(".create-invoice", (e) => {
//     showNewMessage.value = true;
//     items.value.push(e);
//     open.value = true;
//     setTimeout(() => (open.value = false), 4000);
//     notificationsCount.value += 1;
//     patientDetails.value = `/doctor/patient_details/${e.patient_id} `;
// });
///////////////////////////////////////////////////////////////////////////////////////////////////

////// to open submenu and close other submenus in the same time use this /////////////////////////
/////////////////remember to add function openCloseSubMenu(menu) in template /////////////////////

// to get element height for css  /// need more work
// let divElement = document.querySelector(".collapsableDiv");
// let elemHeight = divElement.offsetHeight;
// let elemHeight = divElement.clientHeight;
// console.log(elemHeight);

//         const styleElementHeight = getComputedStyle(document.querySelector(".collapsableDiv")).height;
// console.log('style Element Height : ', styleElementHeight);

const slideActionName = ref(""); // to make animation on sidebar menu only on first time not every click on the menu even if we are in the same page

const openCloseSubMenu = (activeMenu) => {
    if (activeMenu.hasSubmenu) {
        slideActionName.value = "accordion";
        activeMenu.open = !activeMenu.open;

        menus.value.forEach((menu) => {
            if (menu.label !== activeMenu.label) {
                return (menu.open = false);
            }
        });
    }
};
////////////////////////////////////////////////////////////////
//// otherwise just use onClick = "menu.open != menu.open" in template
////////////////////////////////////////////////////////////////
// to keep menu opend when submenu is active
onMounted(() => {
    menus.value.forEach((menu) => {
        if (menu.hasSubmenu) {
            menu.subMenus.forEach((submenu) => {
                if (submenu.isActive) {
                    slideActionName.value = ""; // keep it empty to prevent animation on sidebar menu when clicking on the current page or filter on it
                    return (menu.open = true);
                    // return openCloseSubMenu(menu);
                }
            });
        }
    });
});

const start = (el) => {
    el.style.height = el.scrollHeight + "px";
};

const end = (el) => {
    return (el.style.height = "");
};

///////////// general alert section  from settings////////////////////////
const generalAlertText = ref();
const showGeneralAlert = ref(true);
const hideAlert = () => {
    showGeneralAlert.value = false;
    let generalAlert = {
        text: usePage().props.general_alert,
        closed: true,
    };
    // Convert the object into a JSON string
    var jsonString = JSON.stringify(generalAlert);
    sessionStorage.setItem("generalAlert", jsonString);
};

onMounted(() => saveAlert());
const saveAlert = () => {
    // Create a new object with the user input
    let generalAlert = {
        text: usePage().props?.general_alert,
        closed: false,
    };
    let jsonString = JSON.stringify(generalAlert);

    generalAlertText.value = generalAlert?.text;

    let getCurrentAlert = sessionStorage.getItem("generalAlert");

    if (getCurrentAlert != null) {
        let currentAlert = JSON.parse(getCurrentAlert);
        if (
            currentAlert?.closed == false ||
            currentAlert?.text !== generalAlert?.text
        ) {
            // Convert the object into a JSON string
            sessionStorage.setItem("generalAlert", jsonString);
        } else if (currentAlert?.closed == true) {
            showGeneralAlert.value = false;
        } else {
            sessionStorage.setItem("generalAlert", jsonString);
        }
    }
};

/////////////////////////////////////////////////////////////////////////

// const smallSideBar = ref(false)
const mainAreaSize = computed(() =>
    smallSideBar.value == true
        ? "ltr:xl:ml-12 rtl:xl:mr-12"
        : "ltr:xl:ml-56 rtl:xl:mr-56"
);
const sideBarSize = computed(() =>
    smallSideBar.value == true ? "xl:w-[50px]" : "xl:w-[225px]"
);

// important
// z-*  is for the arrangement of the item on other items => controlling the stack order of an element
// z-990 in class in template down can cause apperance of sidebar items in white above all items
</script>


<template>
    <div
        class="flex flex-col justify-between overflow-auto min-h-screen h-fit ease-soft-in-out relative transition-all duration-200 bg-orange-100/50 dark:bg-zinc-800 z-0"
        :class="mainAreaSize"
    >
        <div
            class="w-full h-full absolute top-0 left-0 bg-[url('/assets/img/noise.jpg')] bg-contain bg-center opacity-10 -z-10"
        />
        <div
            class="w-full h-full absolute top-0 left-0 bg-[url('/assets/img/grid.svg')] bg-contain bg-center opacity-20 dark:opacity-10 -z-10"
        />

        <Head :title="props.title" />

        <Alert :success="props.success" :error="props.error" />

        <div
            v-show="showHideSidebar"
            class="z-40 fixed inset-0 transform transition-all"
            @click="showHideSidebar = !showHideSidebar"
        >
            <div class="absolute inset-0 bg-gray-500 opacity-10" />
        </div>

        <!-- <aside
            :class="showHideClass"
            class="rtl:translate-x-full ltr:-translate-x-full xl:translate-x-0 z-990  rtl:xl:right-0 ltr:xl:left-0 w-56 ease-nav-brand fixed inset-y-0 mt-0z ltr:ml-0 rtl:mr-0 block flex-wrap items-center justify-between overflow-y-auto p-0 antialiased shadow-none transition-transform duration-200 xl:bg-transparent bg-black
            
            "
        > -->
        <!-- v-if="isVisibleSidebar" -->
        <aside
            :style="{ '--width': showHideClass ? '225px' : '0px' }"
            class="w-[--width] transition-all translate-x-0 duration-200 z-990 xl:block rtl:xl:right-0 ltr:xl:left-0 ease-nav-brand fixed inset-y-0 mt-0z ltr:ml-0 rtl:mr-0 flex-wrap items-center justify-between overflow-y-auto p-0 antialiased shadow-none xl:bg-transparent bg-black"
            :class="sideBarSize"
        >
            <!-- left -->
            <!-- <aside
            :class="showHideClass"
            class="max-w-62.5 ease-nav-brand z-990 fixed inset-y-0 my-4 ml-4 block w-full -translate-x-full flex-wrap items-center justify-between overflow-y-auto rounded-2xl border-0 bg-white p-0 antialiased shadow-none transition-transform duration-200 xl:left-0 xl:translate-x-0 xl:bg-transparent "
        > -->

            <!-- right -->
            <!-- <aside
        :class="showHideClass"
        class="max-w-62.5 ease-nav-brand z-990 fixed inset-y-0 my-4 mr-4 block w-full translate-x-full flex-wrap items-center justify-between overflow-y-auto rounded-2xl border-0 bg-white p-0 antialiased shadow-none transition-transform duration-200 xl:right-0 xl:translate-x-0 xl:bg-transparent  rtl:ps__rtl rtl:ps--active-y "       > -->

            <div
                class="w-full h-full absolute top-0 left-0 bg-[url('/assets/img/noise.jpg')] bg-contain bg-center opacity-10"
            />
            <div
                class="w-full h-full absolute top-0 left-0 bg-[url('/assets/img/grid.svg')] bg-contain bg-center opacity-10"
            />

            <div
                class="h-full items-center block w-auto overflow-auto grow basis-full 2xl shadow-xl"
            >
                <perfectScrollbar>
                    <i
                        class="absolute top-0 right-0 hidden p-4 opacity-50 cursor-pointer fas fa-times text-gray-400 xl:hidden"
                        sidenav-close
                    ></i>
                    <div
                        class="block pb-px m-0 text-size-sm whitespace-nowrap text-center"
                    >
                        <img
                            :src="$page.props?.logo ?? ''"
                            :alt="$page.props.auth.user?.name"
                            class="inline h-full max-w-full transition-all duration-200 ease-nav-brand max-h-24 w-full object-cover"
                        />
                    </div>

                    <ul>
                        <li
                            v-show="menu.isVisible"
                            v-for="menu in menus"
                            :key="menu.label"
                            :active="menu.isActive"
                        >
                            <div>
                                <div v-if="menu.hasSubmenu">
                                    <div
                                        class="transition text-gray-400 hover:text-gray-200"
                                    >
                                        <button
                                            :ref="'header-' + menu.label"
                                            class="py-2 w-full flex items-center justify-between rounded hover:bg-zinc-800 hover:opacity-100 hover:text-gray-200"
                                            @click="openCloseSubMenu(menu)"
                                            :class="
                                                menu.isActive
                                                    ? 'border border-zinc-500 shadow-soft-xl  text-size-sm ease-nav-brand flex items-center whitespace-nowrap rounded rtl:bg-gradient-to-l ltr:bg-gradient-to-r from-zinc-100  to-transparent  px-2  text-black hover:text-zinc-900 font-semibold transition-colors'
                                                    : 'text-size-sm ease-nav-brand    flex   items-center whitespace-nowrap px-2 transition-colors'
                                            "
                                        >
                                            <SidebarIcon
                                                :active="menu.isActive"
                                                :icon="menu.label"
                                            />

                                            <span
                                                class="w-full flex items-center justify-between"
                                            >
                                                <Translations
                                                    v-if="smallSideBar == false"
                                                    class="mr-1 duration-300 opacity-100 pointer-events-none ease-soft first-line:"
                                                    :label="menu.label"
                                                />

                                                <span
                                                    v-if="smallSideBar == false"
                                                >
                                                    <svg
                                                        v-if="
                                                            current_lang == 'ar'
                                                        "
                                                        :class="{
                                                            '-rotate-90  transition ease-in-out duration-300':
                                                                menu.open,
                                                            'rotate-0  transition ease-in-out duration-300':
                                                                !menu.open,
                                                        }"
                                                        xmlns="http://www.w3.org/2000/svg"
                                                        viewBox="0 0 24 24"
                                                        fill="currentColor"
                                                        class="w-4 h-4"
                                                    >
                                                        <path
                                                            fill-rule="evenodd"
                                                            d="M7.72 12.53a.75.75 0 010-1.06l7.5-7.5a.75.75 0 111.06 1.06L9.31 12l6.97 6.97a.75.75 0 11-1.06 1.06l-7.5-7.5z"
                                                            clip-rule="evenodd"
                                                        />
                                                    </svg>

                                                    <svg
                                                        v-else
                                                        :class="{
                                                            'rotate-90  transition ease-in-out duration-300':
                                                                menu.open,
                                                            'rotate-0  transition ease-in-out duration-300':
                                                                !menu.open,
                                                        }"
                                                        xmlns="http://www.w3.org/2000/svg"
                                                        viewBox="0 0 24 24"
                                                        fill="currentColor"
                                                        class="w-4 h-4"
                                                    >
                                                        <path
                                                            fill-rule="evenodd"
                                                            d="M16.28 11.47a.75.75 0 010 1.06l-7.5 7.5a.75.75 0 01-1.06-1.06L14.69 12 7.72 5.03a.75.75 0 011.06-1.06l7.5 7.5z"
                                                            clip-rule="evenodd"
                                                        />
                                                    </svg>
                                                </span>
                                            </span>
                                        </button>
                                    </div>

                                    <transition
                                        :name="slideActionName"
                                        @enter="start"
                                        @after-enter="end"
                                        @before-leave="start"
                                        @after-leave="end"
                                    >
                                        <div
                                            class="collapsableDiv relative rtl:bg-gradient-to-l ltr:bg-gradient-to-r from-transparent to-transparent via-zinc-300/20"
                                            v-show="menu.open"
                                        >
                                            <div
                                                class="w-full h-full absolute top-0 left-0 bg-[url('/assets/img/noise.jpg')] bg-contain bg-center opacity-20 -z-10"
                                            />
                                            <div
                                                class="w-full h-full absolute top-0 left-0 bg-[url('/assets/img/grid.svg')] bg-contain bg-center opacity-20 dark:opacity-10 -z-10"
                                            />

                                            <div
                                                class="text-gray-400 hover:text-gray-600"
                                                v-for="(
                                                    submenu, index
                                                ) in menu.subMenus"
                                                :key="index"
                                                :class="`${
                                                    smallSideBar
                                                        ? ''
                                                        : 'px-1 rtl:mr-2 ltr:ml-2'
                                                }`"
                                                id="filter-1"
                                            >
                                                <Link
                                                    v-show="submenu.isVisible"
                                                    class="hover:bg-zinc-800 hover:opacity-100 hover:text-gray-200 rounded rtl:pr-1 ltr:pl-1 my-1 py-1"
                                                    :class="
                                                        submenu.isActive
                                                            ? 'shadow-soft-xl hover:text-sky-900  text-sm ease-nav-brand   flex justify-between  rtl:bg-gradient-to-l ltr:bg-gradient-to-r from-zinc-200  to-transparent border-zinc-400 text-gray-950 font-semibold transition-colors border '
                                                            : smallSideBar
                                                            ? ' text-size-sm ease-nav-brand  text-zinc-400  flex  justify-between   transition-colors rtl:bg-gradient-to-l ltr:bg-gradient-to-r from-zinc-900 rounded to-zinc-900  border border-gray-600 '
                                                            : ' text-size-sm ease-nav-brand  text-zinc-400  flex  justify-between   transition-colors rtl:bg-gradient-to-l ltr:bg-gradient-to-r from-zinc-900 rounded to-transparent  border border-gray-600 '
                                                    "
                                                    :href="submenu.url"
                                                    @click="startLeaveAnimation"
                                                >
                                                    <SidebarIcon
                                                        :active="
                                                            submenu.isActive
                                                        "
                                                        :icon="submenu.label"
                                                        small
                                                    />

                                                    <span
                                                        class="w-full flex items-center justify-between"
                                                    >
                                                        <span
                                                            class="mr-1 duration-300 opacity-100 pointer-events-none ease-soft"
                                                        >
                                                            <Translations
                                                                v-if="
                                                                    smallSideBar ==
                                                                    false
                                                                "
                                                                :label="
                                                                    submenu.label
                                                                "
                                                                :active="
                                                                    submenu.isActive
                                                                "
                                                            />
                                                        </span>
                                                    </span>
                                                </Link>
                                            </div>
                                        </div>
                                    </transition>

                                    <hr class="h-px bg-white/30" />
                                </div>

                                <div v-else>
                                    <Link
                                        class="py-2 w-full flex items-center justify-between rounded hover:bg-zinc-800 hover:opacity-100 hover:text-gray-200"
                                        :class="
                                            menu.isActive
                                                ? 'border border-zinc-500 shadow-soft-xl  text-size-sm ease-nav-brand flex items-center whitespace-nowrap rounded rtl:bg-gradient-to-l ltr:bg-gradient-to-r from-zinc-100  to-transparent  px-2  text-black hover:text-zinc-900 font-semibold transition-colors'
                                                : 'text-size-sm ease-nav-brand text-gray-400   flex   items-center whitespace-nowrap px-2 transition-colors'
                                        "
                                        :href="menu.url"
                                        @click="startLeaveAnimation"
                                    >
                                        <SidebarIcon
                                            :active="menu.isActive"
                                            :icon="menu.label"
                                        />

                                        <span
                                            class="w-full flex items-center justify-between"
                                        >
                                            <span
                                                class="mr-1 duration-300 opacity-100 pointer-events-none ease-soft"
                                            >
                                                <Translations
                                                    v-if="smallSideBar == false"
                                                    :label="menu.label"
                                                    :active="menu.isActive"
                                                />
                                            </span>
                                        </span>
                                    </Link>

                                    <hr class="h-px bg-white/30" />
                                </div>
                            </div>
                        </li>
                    </ul>

                    <li class="xl:hidden"></li>
                </perfectScrollbar>
            </div>
        </aside>
        <main>
            <div
                v-show="showGeneralAlert && generalAlertText"
                class="flex justify-center w-full bg-lime-600/20 border-y border-gray-200/20 text-gray-200"
            >
                <div class="flex justify-center w-full">
                    {{ generalAlertText }}
                </div>
                <Button
                    @click="hideAlert"
                    color="gradient_yellow"
                    class="hover:cursor-pointer"
                >
                    <span class="px-2">X</span></Button
                >
            </div>

            <nav
                class="rtl:bg-gradient-to-l ltr:bg-gradient-to-r from-black/70 via-black/70 relative flex flex-wrap items-center justify-between px-0 transition-all shadow-none duration-250 ease-soft-in lg:flex-nowrap lg:justify-start"
                navbar-main
                navbar-scroll="true"
            >
                <div
                    class="flex items-center w-full justify-between rtl:pl-4 ltr:pr-4 py-1 mx-auto flex-wrap-inherit"
                >
                    <div
                        class="items-center hidden xl:block mx-3 mt-1"
                        @click="smallSideBar = !smallSideBar"
                    >
                        <button
                            class="bg-black/30 inline-flex items-center justify-center gap-2 border border-white/30 whitespace-nowrap rounded-md text-sm transition-all hover:scale-110 text-gray-400 font-medium ring-offset-background focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:pointer-events-none disabled:opacity-50 [&amp;_svg]:pointer-events-none [&amp;_svg]:size-4 [&amp;_svg]:shrink-0 hover:bg-accent hover:text-accent-foreground h-7 w-7 -ml-1 p-1"
                        >
                            <svg
                                xmlns="http://www.w3.org/2000/svg"
                                width="24"
                                height="24"
                                viewBox="0 0 24 24"
                                fill="none"
                                stroke="currentColor"
                                stroke-width="1"
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                class="lucide lucide-panel-left-icon"
                            >
                                <rect
                                    width="18"
                                    height="18"
                                    x="3"
                                    y="3"
                                    rx="2"
                                ></rect>
                                <path d="M9 3v18"></path>
                            </svg>
                        </button>
                    </div>

                    <div
                        class="flex items-center xl:hidden mx-3"
                        @click="showHideSidebar = !showHideSidebar"
                    >
                        <button
                            class="bg-black/30 inline-flex items-center justify-center gap-2 border border-white/30 whitespace-nowrap rounded-md text-sm transition-all hover:scale-110 text-gray-400 font-medium ring-offset-background focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:pointer-events-none disabled:opacity-50 [&amp;_svg]:pointer-events-none [&amp;_svg]:size-4 [&amp;_svg]:shrink-0 hover:bg-accent hover:text-accent-foreground h-7 w-7 -ml-1 p-1"
                        >
                            <svg
                                xmlns="http://www.w3.org/2000/svg"
                                width="24"
                                height="24"
                                viewBox="0 0 24 24"
                                fill="none"
                                stroke="currentColor"
                                stroke-width="1"
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                class="lucide lucide-panel-left-icon"
                            >
                                <rect
                                    width="18"
                                    height="18"
                                    x="3"
                                    y="3"
                                    rx="2"
                                ></rect>
                                <path d="M9 3v18"></path>
                            </svg>
                        </button>
                    </div>
                    <nav
                        class="bg-gray-900 shadow border border-gray-600 rtl:rounded-l-full ltr:rounded-r-full"
                    >
                        <slot name="breadcrumbs" />
                    </nav>

                    <div
                        class="flex items-center mt-2 grow sm:mt-0 sm:mr-6 md:mr-0 lg:flex lg:basis-auto"
                    >
                        <div class="flex items-center md:ml-auto md:pr-4"></div>

                        <ul
                            class="flex justify-between flex-row max:w-full border border-zinc-800 dark:border-zinc-600 rounded-full bg-gradient-to-r from-zinc-900 to-zinc-900 mx-2"
                        >
                            <button
                                :title="`${$t('general.switch to light mode')}`"
                                v-show="mode == 'system'"
                                @click="switchMode('light')"
                                class="transition ease-in-out duration-400 p-2 rounded-full text-zinc-300 hover:scale-110 truncate dark:border-zinc-700 flex border border-zinc-700 text-sm bg-zinc-900"
                            >
                                <svg
                                    xmlns="http://www.w3.org/2000/svg"
                                    viewBox="0 0 20 20"
                                    fill="currentColor"
                                    class="w-5 h-5"
                                >
                                    <path
                                        fill-rule="evenodd"
                                        d="M2 4.25A2.25 2.25 0 0 1 4.25 2h11.5A2.25 2.25 0 0 1 18 4.25v8.5A2.25 2.25 0 0 1 15.75 15h-3.105a3.501 3.501 0 0 0 1.1 1.677A.75.75 0 0 1 13.26 18H6.74a.75.75 0 0 1-.484-1.323A3.501 3.501 0 0 0 7.355 15H4.25A2.25 2.25 0 0 1 2 12.75v-8.5Zm1.5 0a.75.75 0 0 1 .75-.75h11.5a.75.75 0 0 1 .75.75v7.5a.75.75 0 0 1-.75.75H4.25a.75.75 0 0 1-.75-.75v-7.5Z"
                                        clip-rule="evenodd"
                                    />
                                </svg>
                            </button>
                            <button
                                :title="`${$t('general.switch to dark mode')}`"
                                v-show="mode == 'light'"
                                @click="switchMode('dark')"
                                class="transition ease-in-out duration-400 p-2 rounded-full text-zinc-300 hover:scale-110 truncate dark:border-zinc-700 flex border border-zinc-700 text-sm bg-zinc-900"
                            >
                                <svg
                                    xmlns="http://www.w3.org/2000/svg"
                                    fill="none"
                                    viewBox="0 0 24 24"
                                    stroke-width="1.5"
                                    stroke="currentColor"
                                    class="w-5 h-5 text-white"
                                >
                                    <path
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        d="M12 3v2.25m6.364.386-1.591 1.591M21 12h-2.25m-.386 6.364-1.591-1.591M12 18.75V21m-4.773-4.227-1.591 1.591M5.25 12H3m4.227-4.773L5.636 5.636M15.75 12a3.75 3.75 0 1 1-7.5 0 3.75 3.75 0 0 1 7.5 0Z"
                                    />
                                </svg>
                            </button>
                            <button
                                :title="`${$t(
                                    'general.switch to system theme'
                                )}`"
                                v-show="mode == 'dark'"
                                @click="switchMode('system')"
                                class="transition ease-in-out duration-400 p-2 rounded-full text-zinc-300 hover:scale-110 truncate dark:border-zinc-700 flex border border-zinc-700 text-sm bg-zinc-900"
                            >
                                <svg
                                    viewBox="0 0 15 15"
                                    width="1.2em"
                                    height="1.2em"
                                    class="w-[20px] h-5 text-foreground"
                                >
                                    <path
                                        fill="currentColor"
                                        fill-rule="evenodd"
                                        d="M2.9.5a.4.4 0 0 0-.8 0v.6h-.6a.4.4 0 1 0 0 .8h.6v.6a.4.4 0 1 0 .8 0v-.6h.6a.4.4 0 0 0 0-.8h-.6zm3 3a.4.4 0 1 0-.8 0v.6h-.6a.4.4 0 1 0 0 .8h.6v.6a.4.4 0 1 0 .8 0v-.6h.6a.4.4 0 0 0 0-.8h-.6zm-4 3a.4.4 0 1 0-.8 0v.6H.5a.4.4 0 1 0 0 .8h.6v.6a.4.4 0 0 0 .8 0v-.6h.6a.4.4 0 0 0 0-.8h-.6zM8.544.982l-.298-.04c-.213-.024-.34.224-.217.4A6.57 6.57 0 0 1 9.203 5.1a6.602 6.602 0 0 1-6.243 6.59c-.214.012-.333.264-.183.417a6.8 6.8 0 0 0 .21.206l.072.066l.26.226l.188.148l.121.09l.187.131l.176.115c.12.076.244.149.37.217l.264.135l.26.12l.303.122l.244.086a6.568 6.568 0 0 0 1.103.26l.317.04l.267.02a6.6 6.6 0 0 0 6.943-7.328l-.037-.277a6.557 6.557 0 0 0-.384-1.415l-.113-.268l-.077-.166l-.074-.148a6.602 6.602 0 0 0-.546-.883l-.153-.2l-.199-.24l-.163-.18l-.12-.124l-.16-.158l-.223-.2l-.32-.26l-.245-.177l-.292-.19l-.321-.186l-.328-.165l-.113-.052l-.24-.101l-.276-.104l-.252-.082l-.325-.09l-.265-.06zm1.86 4.318a7.578 7.578 0 0 0-.572-2.894a5.601 5.601 0 1 1-4.748 10.146a7.61 7.61 0 0 0 3.66-2.51a.749.749 0 0 0 1.355-.442a.75.75 0 0 0-.584-.732c.062-.116.122-.235.178-.355A1.25 1.25 0 1 0 10.35 6.2c.034-.295.052-.595.052-.9"
                                        clip-rule="evenodd"
                                    ></path>
                                </svg>
                            </button>

                            <!-- <li
                                class="relative flex items-center dropdown-notifications show rtl:mr-1 ltr:ml-1"
                            >
                                <div class="sm:flex sm:items-center">
                                    <div class="relative">
                                        <JetDropdown
                                            :align="direction"
                                            :open="open"
                                            width="60"
                                        >
                                            <template #trigger>
                                                <button
                                                    class="text-yellow-300 font-normal flex hover:scale-110 border border-zinc-700 text-sm bg-zinc-900 rtl:rounded-l-full rounded-full focus:outline-none focus:border-gray-900 transition"
                                                    small
                                                >
                                                    <span
                                                        class="inline-flex rounded-md"
                                                    >
                                                        <button
                                                            type="button"
                                                            class="inline-flex items-center px-2 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-gray-500 bg-transparent hover:text-gray-700 focus:outline-none transition"
                                                        >
                                                            <svg
                                                                xmlns="http://www.w3.org/2000/svg"
                                                                class="h-4 w-4 text-yellow-300 font-normal"
                                                                fill="none"
                                                                viewBox="0 0 24 24"
                                                                stroke="currentColor"
                                                                stroke-width="2"
                                                            >
                                                                <path
                                                                    stroke-linecap="round"
                                                                    stroke-linejoin="round"
                                                                    d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"
                                                                />
                                                            </svg>
                                                        </button>
                                                        <div>
                                                            <h1
                                                                v-show="
                                                                    notificationsCount >
                                                                    0
                                                                "
                                                                class="block px-2 py-0 my-2 text-xs text-gray-100 bg-red-600 rounded-full border border-white border-opacity-50"
                                                                id="notifications-count"
                                                            >
                                                                {{
                                                                    notificationsCount
                                                                }}
                                                            </h1>
                                                        </div>
                                                    </span>
                                                </button>
                                            </template>

                                            <template #content>
                                                <div
                                                    class="flex justify-between"
                                                >
                                                    <div
                                                        class="block px-4 py-2 text-xs text-gray-400"
                                                    >
                                                        {{
                                                            $t(
                                                                "general.notifications"
                                                            )
                                                        }}
                                                    </div>
                                                </div>
                                                <div
                                                    class="border-t border-gray-200"
                                                />

                                                <DropdownLink
                                                    class="new_message"
                                                    v-for="(
                                                        item, index
                                                    ) in items"
                                                    :key="index"
                                                    v-show="showNewMessage"
                                                >
                                                    <div class="flex py-1">
                                                        <div
                                                            class="my-auto rtl:ml-2 ltr:mr-2"
                                                        >
                                                            <svg
                                                                xmlns="http://www.w3.org/2000/svg"
                                                                viewBox="0 0 24 24"
                                                                fill="currentColor"
                                                                class="w-10 h-10"
                                                            >
                                                                <path
                                                                    fill-rule="evenodd"
                                                                    d="M4.5 3.75a3 3 0 00-3 3v10.5a3 3 0 003 3h15a3 3 0 003-3V6.75a3 3 0 00-3-3h-15zm4.125 3a2.25 2.25 0 100 4.5 2.25 2.25 0 000-4.5zm-3.873 8.703a4.126 4.126 0 017.746 0 .75.75 0 01-.351.92 7.47 7.47 0 01-3.522.877 7.47 7.47 0 01-3.522-.877.75.75 0 01-.351-.92zM15 8.25a.75.75 0 000 1.5h3.75a.75.75 0 000-1.5H15zM14.25 12a.75.75 0 01.75-.75h3.75a.75.75 0 010 1.5H15a.75.75 0 01-.75-.75zm.75 2.25a.75.75 0 000 1.5h3.75a.75.75 0 000-1.5H15z"
                                                                    clip-rule="evenodd"
                                                                />
                                                            </svg>
                                                        </div>
                                                    </div>
                                                </DropdownLink>
                                                <DropdownLink
                                                    v-for="(
                                                        item, index
                                                    ) in notifications"
                                                    :key="index"
                                                >
                                                    <div class="flex py-1">
                                                        <div
                                                            class="my-auto rtl:ml-2 ltr:mr-2"
                                                        >
                                                            <svg
                                                                xmlns="http://www.w3.org/2000/svg"
                                                                viewBox="0 0 24 24"
                                                                fill="currentColor"
                                                                class="w-10 h-10"
                                                            >
                                                                <path
                                                                    fill-rule="evenodd"
                                                                    d="M4.5 3.75a3 3 0 00-3 3v10.5a3 3 0 003 3h15a3 3 0 003-3V6.75a3 3 0 00-3-3h-15zm4.125 3a2.25 2.25 0 100 4.5 2.25 2.25 0 000-4.5zm-3.873 8.703a4.126 4.126 0 017.746 0 .75.75 0 01-.351.92 7.47 7.47 0 01-3.522.877 7.47 7.47 0 01-3.522-.877.75.75 0 01-.351-.92zM15 8.25a.75.75 0 000 1.5h3.75a.75.75 0 000-1.5H15zM14.25 12a.75.75 0 01.75-.75h3.75a.75.75 0 010 1.5H15a.75.75 0 01-.75-.75zm.75 2.25a.75.75 0 000 1.5h3.75a.75.75 0 000-1.5H15z"
                                                                    clip-rule="evenodd"
                                                                />
                                                            </svg>
                                                        </div>
                                                    </div>
                                                </DropdownLink>

                                                <div
                                                    class="border-t border-gray-100"
                                                />

                                                <div
                                                    class="border-t border-gray-100"
                                                />
                                            </template>
                                        </JetDropdown>
                                    </div>
                                </div>
                            </li> -->

                            <li class="relative flex items-center">
                                <div class="sm:flex sm:items-center mx-1">
                                    <div class="relative">
                                        <JetDropdown :align="direction">
                                            <template #trigger>
                                                <Button
                                                    color="gradient_black"
                                                    class="text-yellow-300 font-normal border-zinc-700 hover:scale-105 rounded-full"
                                                    small
                                                >
                                                    <span
                                                        class="inline-flex rounded-md"
                                                    >
                                                        <button
                                                            type="button"
                                                            class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium text-gray-500 bg-transparent hover:text-gray-700 focus:outline-none transition"
                                                        >
                                                            <svg
                                                                class="rtl:ml-2 ltr:mr-2 -mr-0.5 h-4 w-4"
                                                                xmlns="http://www.w3.org/2000/svg"
                                                                viewBox="0 0 20 20"
                                                                fill="currentColor"
                                                            >
                                                                <path
                                                                    fill-rule="evenodd"
                                                                    d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"
                                                                    clip-rule="evenodd"
                                                                />
                                                            </svg>
                                                            <span
                                                                class="flex justify-between"
                                                                v-if="
                                                                    current_lang ==
                                                                    'en'
                                                                "
                                                            >
                                                                <img
                                                                    src="../../../public/assets/img/us-flag-icon.jpg"
                                                                    class="border border-white inline-flex items-center justify-center ltr:mr-4 rtl:ml-4 text-white text-size-sm h-4 w-full max-w-none"
                                                                    alt="us-flag-icon"
                                                                />
                                                                <span
                                                                    class="text-yellow-300"
                                                                >
                                                                    English
                                                                </span>
                                                            </span>

                                                            <span
                                                                class="flex justify-between"
                                                                v-if="
                                                                    current_lang ==
                                                                    'ar'
                                                                "
                                                            >
                                                                <img
                                                                    src="../../../public/assets/img/sa-flag-icon.jpg"
                                                                    class="border border-white inline-flex items-center justify-center ltr:mr-4 rtl:ml-4 text-white text-size-sm h-4 w-full max-w-none"
                                                                    class1=" rounded-xl"
                                                                    alt="sa-flag-icon"
                                                                />
                                                                <span
                                                                    class="text-yellow-300"
                                                                    >العربية</span
                                                                >
                                                            </span>
                                                        </button>
                                                    </span>
                                                </Button>
                                            </template>

                                            <template #content>
                                                <div
                                                    class="block px-4 py-2 text-xs text-gray-400"
                                                >
                                                    {{
                                                        $t("general.languages")
                                                    }}
                                                </div>
                                                <div
                                                    class="border-t border-gray-400 dark:border-gray-200/20"
                                                />
                                                <DropdownLink
                                                    as="button"
                                                    @click="loadLanguage('ar')"
                                                >
                                                    <div class="flex py-1">
                                                        <div class="my-auto">
                                                            <img
                                                                alt="image"
                                                                src="../../../public/assets/img/sa-flag-icon.jpg"
                                                                class="inline-flex items-center justify-center ltr:mr-4 rtl:ml-4 text-white text-size-sm h-5 w-9"
                                                            />
                                                        </div>
                                                        <div
                                                            class="flex flex-col justify-center"
                                                        >
                                                            <span
                                                                class="mb-1 font-normal leading-normal text-size-sm"
                                                                >العربية</span
                                                            >
                                                        </div>
                                                    </div>
                                                </DropdownLink>

                                                <DropdownLink
                                                    class="border-t border-gray-200 dark:border-gray-200/10"
                                                    as="button"
                                                    @click="loadLanguage('en')"
                                                >
                                                    <div class="flex py-1">
                                                        <div class="my-auto">
                                                            <img
                                                                alt="image"
                                                                src="../../../public/assets/img/us-flag-icon.jpg"
                                                                class="inline-flex items-center justify-center ltr:mr-4 rtl:ml-4 text-white text-size-sm h-5 w-9 max-w-none"
                                                            />
                                                        </div>
                                                        <div
                                                            class="flex flex-col justify-center"
                                                        >
                                                            <span
                                                                class="mb-1 font-normal leading-normal text-size-sm"
                                                                >English</span
                                                            >
                                                        </div>
                                                    </div>
                                                </DropdownLink>
                                            </template>
                                        </JetDropdown>
                                    </div>
                                </div>
                            </li>

                            <li class="relative flex items-center">
                                <div class="relative">
                                    <JetDropdown :align="direction" width="48">
                                        <template #trigger>
                                            <button
                                                v-if="
                                                    $page.props.jetstream
                                                        .managesProfilePhotos
                                                "
                                                class="flex hover:scale-110 border border-zinc-700 text-sm rtl:rounded-l-full rounded-full focus:outline-none focus:border-gray-900 transition"
                                            >
                                                <img
                                                    class="h-8 w-8 rounded-full object-cover"
                                                    :src="
                                                        $page.props.auth.user
                                                            .profile_photo_url
                                                    "
                                                    :alt="
                                                        $page.props.auth.user
                                                            ?.name
                                                    "
                                                />
                                            </button>

                                            <span
                                                v-else
                                                class="inline-flex rounded-md"
                                            >
                                                <button
                                                    type="button"
                                                    class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-gray-500 bg-white hover:text-gray-700 focus:outline-none transition"
                                                >
                                                    {{ $page.props.user?.name }}

                                                    <svg
                                                        class="ml-2 -mr-0.5 h-4 w-4"
                                                        xmlns="http://www.w3.org/2000/svg"
                                                        viewBox="0 0 20 20"
                                                        fill="currentColor"
                                                    >
                                                        <path
                                                            fill-rule="evenodd"
                                                            d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"
                                                            clip-rule="evenodd"
                                                        />
                                                    </svg>
                                                </button>
                                            </span>
                                        </template>

                                        <template #content>
                                            <div
                                                class="block px-4 py-2 text-xs text-gray-400"
                                            >
                                                {{
                                                    $t("general.manage account")
                                                }}
                                            </div>
                                            <div
                                                class="border-t border-gray-200 dark:border-gray-200/20"
                                            />

                                            <DropdownLink
                                                :href="profileLink"
                                                @click="startLeaveAnimation"
                                            >
                                                {{ $t("general.profile") }}
                                            </DropdownLink>

                                            <DropdownLink
                                                v-if="
                                                    $page.props.jetstream
                                                        .hasApiFeatures
                                                "
                                                :href="
                                                    route('api-tokens.index')
                                                "
                                            >
                                                API Tokens
                                            </DropdownLink>

                                            <div
                                                class="border-t border-gray-100 dark:border-gray-200/10"
                                            />

                                            <form
                                                @submit.prevent="logout"
                                                method="post"
                                            >
                                                <DropdownLink as="button">
                                                    {{ $t("general.sign out") }}
                                                </DropdownLink>
                                            </form>
                                        </template>
                                    </JetDropdown>
                                </div>
                            </li>
                        </ul>
                    </div>
                </div>
            </nav>

            <header class="shadow" v-if="$slots.header">
                <div
                    class="flex justify-start mx-auto py-2.5 px-2 sm:px-6 lg:px-5 rtl:bg-gradient-to-l ltr:bg-gradient-to-r from-zinc-950 via-zinc-950 to-zinc-950/20 font-bold text-3xl text-white leading-tight capitalize"
                >
                    <slot name="header" />
                </div>
            </header>
            <!-- <header class="bg-gray-300 shadow" v-if="$slots.header">
                <div
                    class="flex justify-center mx-auto py-1.5 px-4 sm:px-6 lg:px-8 border-t border-gray-400/70 rtl:bg-gradient-to-l ltr:bg-gradient-to-r from-black via-zinc-600 to-black dark:from-black dark:via-gray-900 dark:to-black"
                >
                    <slot name="header" />
                </div>
            </header> -->

            <transition name="page" mode="out-in" appear>
                <div v-if="animate">
                    <slot />
                </div>
            </transition>
        </main>

        <footer
            class="bg-orange-100/40 dark:bg-zinc-900 border-t dark:border-zinc-100/10 border-zinc-100/40"
        >
            <div
                class="w-full mx-auto max-w-screen-xl p-2 md:flex md:items-center md:justify-between"
            >
                <span
                    class="text-xs text-gray-700 sm:text-center dark:text-gray-400"
                >
                    <a
                        href="https://ibrahimrezk.com/"
                        target="”_blank”"
                        class="hover:underline"
                        >developed by : ibrahim rezk </a
                    >... All Rights Reserved. © {{ year }} ...
                    ibrahimrezk@live.com
                </span>
                <ul
                class="flex flex-wrap items-center mt-3 text-sm font-medium text-gray-700 dark:text-gray-400 sm:mt-0"
                >
                <!-- <li>
                    <a href="#" class="hover:underline me-4 md:me-6"
                    >About</a
                    >
                </li>
                    <li>
                        <a href="#" class="hover:underline me-4 md:me-6"
                            >Privacy Policy</a
                        >
                    </li>
                    <li>
                        <a href="#" class="hover:underline me-4 md:me-6"
                            >Licensing</a
                        >
                    </li>
                    <li>
                        <a href="#" class="hover:underline">Contact</a>
                    </li> -->
                </ul>
            </div>
        </footer>
    </div>
</template>


<style src="vue-multiselect/dist/vue-multiselect.css"></style>


<style scoped>
.accordion-enter-active,
.accordion-leave-active {
    will-change: height, opacity;
    transition: height 0.5s ease, opacity 0.5s ease;
    overflow: hidden;
}

.accordion-enter-from,
.accordion-leave-to {
    height: 0 !important;
    opacity: 0;
}

/* durations and timing functions.*/
.page-enter-active,
.page-leave-active {
    transition: all 0.8s;
}

.page-enter-from {
    transform: translateY(40px);
    opacity: 0;
}

.page-leave-to {
    opacity: 0;
    transform: translateY(-70px);
}
</style>

<style scoped>
.ps {
    height: 98vh;
}

[dir="rtl"] .ps {
    transform: scaleX(-1);
}

[dir="rtl"] .ps > * {
    transform: scaleX(-1);
}
</style>

<style>
.multiselect__content-wrapper {
    position: absolute;
    display: block;
    background: #31353a;
    width: 100%;
    max-height: 240px;
    overflow: auto;
    border: 1px solid #e8e8e8;
    border-top: none;
    border-bottom-left-radius: 5px;
    border-bottom-right-radius: 5px;
    z-index: 50;
    -webkit-overflow-scrolling: touch;
}

.multiselect__tags {
    min-height: 40px;
    display: block;
    padding: 8px 40px 0 8px;
    border-radius: 5px;
    border: 1px solid #e8e8e8;
    background: #6866662a;
    font-size: 14px;
}

.multiselect__placeholder {
    color: #c9d1cb;
    display: inline-block;
    margin-bottom: 10px;
    padding-top: 2px;
}

.multiselect__input,
.multiselect__single {
    position: relative;
    display: inline-block;
    min-height: 20px;
    line-height: 20px;
    border: none;
    background: #68666600;
    padding: 0 0 0 5px;
    width: calc(100%);
    transition: border 0.1s ease;
    box-sizing: border-box;
    margin-bottom: 8px;
    vertical-align: top;
}

.multiselect {
    box-sizing: content-box;
    display: block;
    position: relative;
    width: 100%;
    min-height: 40px;
    text-align: left;
    color: #c9d1cb;
}

.multiselect__input::placeholder {
    color: #c9d1cb;
}

.multiselect__option--highlight {
    background: #376c8b;
    outline: none;
    color: #dbdfe0;
}

.multiselect__option--selected {
    background: #61b861;
    color: #35495e;
    font-weight: bold;
}
</style>



<style scoped>
.collapsableDiv {
    overflow: hidden;
    transform-origin: top;
}

.collapse-enter-active {
    animation: collapse reverse 500ms ease-out;
}

.collapse-leave-active {
    animation: collapse 500ms ease-out;
}

@keyframes collapse {
    from {
        max-height: 350px;
        max-width: 300px;
    }

    to {
        max-height: 0px;
        max-width: 0px;
    }
}
</style>



