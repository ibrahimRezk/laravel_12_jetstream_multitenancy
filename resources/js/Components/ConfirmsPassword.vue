<script setup>
import { ref, reactive, nextTick } from 'vue';
import DialogModal from './DialogModal.vue';
import InputError from './InputError.vue';
import PrimaryButton from './PrimaryButton.vue';
import SecondaryButton from './SecondaryButton.vue';
import TextInput from './TextInput.vue';
import Button from './Button.vue';

const emit = defineEmits(['confirmed']);

defineProps({
    title: {
        type: String,
        default: 'Confirm Password',
    },
    content: {
        type: String,
        default: 'For your security, please confirm your password to continue',
    },
    button: {
        type: String,
        default: 'confirm',
    },
});

const confirmingPassword = ref(false);

const form = reactive({
    password: '',
    error: '',
    processing: false,
});

const passwordInput = ref(null);

const startConfirmingPassword = () => { 
    axios.get(route('password.confirmation')).then(response => {
        if (response.data.confirmed) {
            emit('confirmed');
        } else {
            confirmingPassword.value = true;

            setTimeout(() => passwordInput.value.focus(), 250);
        }
    });
};

const confirmPassword = () => {
    form.processing = true;

    axios.post(route('password.confirm'), {
        password: form.password,
    }).then(() => {
        form.processing = false;

        closeModal();
        nextTick().then(() => emit('confirmed'));

    }).catch(error => {
        form.processing = false;
        form.error = error.response.data.errors.password[0];
        passwordInput.value.focus();
    });
};

const closeModal = () => {
    confirmingPassword.value = false;
    form.password = '';
    form.error = '';
};
</script>

<template>
    <span>
        <span @click="startConfirmingPassword">
            <slot />
        </span>

        <DialogModal :show="confirmingPassword" @close="closeModal">
            <template #title>
                {{ $t('general.' + title ) }}
            </template>

            <template #content>
                    <span class="text-gray-300 text-sm font-normal  border border-gray-200/20 rounded px-5 bg-black/30">
                 {{ $t('general.' + content) }}</span>

                <div class="mt-4">
                    <TextInput
                        ref="passwordInput"
                        v-model="form.password"
                        type="password"
                        class="mt-1 block w-3/4"
                            :placeholder="`${$t('general.password')}`"
                        autocomplete="current-password"
                        @keyup.enter="confirmPassword"
                    />

                    <InputError :message="form.error" class="mt-2" />
                </div>



                                    <div class=" mt-8 flex justify-center gap-10 border-t border-gray-200/30 py-4 ">
                        <Button class=" hover:cursor-pointer px-5 py-1" color="gradient_white" @click="closeModal">
                            {{ $t("general.cancel") }}

                        </Button>
                        <Button  class=" hover:cursor-pointer px-5 py-1" color="gradient_red"
                    :class="{ 'opacity-25': form.processing }"
                    :disabled="form.processing"
                    @click="confirmPassword"
                >
                        {{ $t('general.' + button) }}

                        </Button>
                    </div>

            </template>
            
            <!-- <template #footer>
                <SecondaryButton @click="closeModal">
                    {{ $t('general.cancel') }}
                </SecondaryButton>
    
                <PrimaryButton
                    class="ml-3"
                    :class="{ 'opacity-25': form.processing }"
                    :disabled="form.processing"
                    @click="confirmPassword"
                >
                        {{ $t('general.' + button) }}
    
                </PrimaryButton>
            </template> -->
        </DialogModal>
    </span>
</template>
