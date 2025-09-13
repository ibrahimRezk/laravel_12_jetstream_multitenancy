<script setup>
// import AppLayout from '@/Layouts/AppLayout.vue';
import DeleteUserForm from "@/Pages/Profile/Partials/DeleteUserForm.vue";
import LogoutOtherBrowserSessionsForm from "@/Pages/Profile/Partials/LogoutOtherBrowserSessionsForm.vue";
import SectionBorder from "@/Components/SectionBorder.vue";
import TwoFactorAuthenticationForm from "@/Pages/Profile/Partials/TwoFactorAuthenticationForm.vue";
import UpdatePasswordForm from "@/Pages/Profile/Partials/UpdatePasswordForm.vue";
import UpdateProfileInformationForm from "@/Pages/Profile/Partials/UpdateProfileInformationForm.vue";
import Layout from "@/Layouts/Authenticated.vue";
import Card from "@/Components/Card/Card.vue";

defineProps({
    confirmsTwoFactorAuthentication: Boolean,
    sessions: Array,
});
</script>

<template>
    <Layout title="Profile">
        <template #breadcrumbs>
            <div class="flex mx-2 text-gray-400 text-sm">
                {{ $t("general.profile") }}
            </div>
        </template>

        <template #header>
                {{ $t("general.profile") }}
        </template>

        <!-- <Card class="mt-3 bg-gradient-to-r p-5 from-orange-200 via-zinc-600 to-zinc-800" no-padding> -->

        <div>
            <div
                class="max-w-7xl mx-auto p-5 my-2 rounded sm:px-6 lg:px-8 dark:bg-black/50 bg-white/30"
            >
                <div v-if="$page.props.jetstream.canUpdateProfileInformation">
                    <UpdateProfileInformationForm
                        :user="$page.props.auth.user"
                    />

                    <SectionBorder />
                </div>

                <div v-if="$page.props.jetstream.canUpdatePassword">
                    <UpdatePasswordForm class="mt-10 sm:mt-0" />

                    <SectionBorder />
                </div>

                <div
                    v-if="
                        $page.props.jetstream.canManageTwoFactorAuthentication
                    "
                >
                    <TwoFactorAuthenticationForm
                        :requires-confirmation="confirmsTwoFactorAuthentication"
                        class="mt-10 sm:mt-0"
                    />

                    <SectionBorder />
                </div>

                <LogoutOtherBrowserSessionsForm
                    :sessions="sessions"
                    class="mt-10 sm:mt-0"
                />

                <template
                    v-if="$page.props.jetstream.hasAccountDeletionFeatures"
                >
                    <SectionBorder />

                    <DeleteUserForm class="mt-10 sm:mt-0" />
                </template>
            </div>
        </div>
        <!-- </Card> -->
    </Layout>
</template>
