<script setup>
import { Head, Link, useForm } from '@inertiajs/vue3';
import AuthenticationCard from '@/Components/AuthenticationCard.vue';
import AuthenticationCardLogo from '@/Components/AuthenticationCardLogo.vue';
import Checkbox from '@/Components/Checkbox.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import { computed } from "vue";
import { trans } from "laravel-vue-i18n";



defineProps({
    canResetPassword: Boolean,
    status: String,
});

const form = useForm({
    email: '',
    password: '',
    remember: false,
});

const submit = () => {
    form.transform(data => ({
        ...data,
        remember: form.remember ? 'on' : '',
    })).post(route('login'), {
        onFinish: () => form.reset('password'),
    });
};




const Email = computed(()=>{
    return trans('auth.email')
})
const Password = computed(()=>{
    return trans('auth.thePassword')
})




</script>

<template>
    <Head title="Log in" />

    <AuthenticationCard>
        <template #logo>
            <AuthenticationCardLogo />
        </template>

        <div v-if="status" class="mb-4 font-medium text-sm text-green-600 dark:text-green-400">
            {{ status }}
        </div>

        <form @submit.prevent="submit">
            <div>
                <InputLabel for="email" :value="Email" class=" text-white/80 "/>
                <TextInput
                    id="email"
                    v-model="form.email"
                    type="email"
                    class="mt-1 block w-full"
                    required
                    autofocus
                    autocomplete="username"
                />
                <!-- <InputError class="mt-2" :message="form.errors.email" /> -->
            </div>

            <div class="mt-4">
                <InputLabel for="password" :value="Password" class=" text-white/80 "/>
                <TextInput
                    id="password"
                    v-model="form.password"
                    type="password"
                    class="mt-1 block w-full"
                    required
                    autocomplete="current-password"
                />
                <!-- <InputError class="mt-2" :message="form.errors.password" /> -->
            </div>

            <div class="block mt-4">
                <label class="flex items-center">
                    <Checkbox v-model:checked="form.remember" name="remember" />
                    <span class="mx-2 text-sm text-gray-300 ">{{ $t('auth.remember')}}</span>
                    <!-- <span class="mx-2 text-sm text-gray-600 dark:text-gray-400">{{ $t('auth.remember')}}</span> -->

                </label>
            </div>

                        <InputError v-if="$attrs?.errorBags?.default?.email[0]" class="mt-2" :message="`${$t('general.'+ $attrs?.errorBags?.default?.email[0])}`" />

            <!-- <InputError class="mt-2" :message="form.errors.email" />
            <InputError class="mt-2" :message="form.errors.password" /> -->



            <div class="flex items-center justify-end mt-4">
                <Link v-if="canResetPassword" :href="route('password.request')" class="underline text-sm text-gray-300 dark:text-gray-300 hover:text-gray-100 dark:hover:text-gray-100 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 dark:focus:ring-offset-gray-800">
                    <span class=" mx-3"> {{ $t('auth.forget_password') }}</span>

                </Link>

                <PrimaryButton class="ms-4" :class="{ 'opacity-25': form.processing }" :disabled="form.processing">
                    <span class=" font-normal">
                        {{ $t('auth.login') }}
                    </span>
                </PrimaryButton>
            </div>
        </form>
    </AuthenticationCard>
</template>
