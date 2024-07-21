<!--
The ForgotPassword component is responsible for rendering the forgot password form. It imports GuestLayout, InputError, InputLabel, PrimaryButton, TextInput, Head, and useForm from various files. It defines a prop called status which is a string. It also defines a form object using the useForm function and a submit function that sends a post request to the password.email route. 

The template contains a GuestLayout component, a Head component with the title "Forgot Password", a message explaining how to reset the password, a form with an email input field, and a button to submit the form. If there is a status message, it will be displayed in a green color. 

Props:
- status: A string that represents the status message.

Data:
- form: An object that contains the email field.

Methods:
- submit(): A function that sends a post request to the password.email route.

Events:
- submit: The form submit event.

Child Components:
- GuestLayout: A layout component for guest users.
- Head: A component for setting the page title.
- InputError: A component for displaying input errors.
- InputLabel: A component for displaying input labels.
- PrimaryButton: A component for displaying primary buttons.
- TextInput: A component for displaying text input fields.
-->
<script setup>
import GuestLayout from '@/Layouts/GuestLayout.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import { Head, useForm } from '@inertiajs/vue3';

defineProps({
    status: String,
});

const form = useForm({
    email: '',
});

const submit = () => {
    form.post(route('password.email'));
};
</script>

<template>
    <GuestLayout>
        <Head title="Forgot Password" />

        <div class="mb-4 text-sm text-gray-600 dark:text-gray-400">
            Forgot your password? No problem. Just let us know your email address and we will email you a password reset
            link that will allow you to choose a new one.
        </div>

        <div v-if="status" class="mb-4 font-medium text-sm text-green-600 dark:text-green-400">
            {{ status }}
        </div>

        <form @submit.prevent="submit">
            <div>
                <InputLabel for="email" value="Email" />

                <TextInput
                    id="email"
                    type="email"
                    class="mt-1 block w-full"
                    v-model="form.email"
                    required
                    autofocus
                    autocomplete="username"
                />

                <InputError class="mt-2" :message="form.errors.email" />
            </div>

            <div class="flex items-center justify-between mt-4">
                <PrimaryButton :class="{ 'opacity-25': form.processing }" :disabled="form.processing">
                    Email Password Reset Link
                </PrimaryButton>
            </div>
        </form>
    </GuestLayout>
</template>
