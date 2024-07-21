<!--
The Login component is responsible for rendering the login form for the user to enter their email and password to authenticate. It also provides the option to remember the user's login session. The component receives the following props:
- canRegister: a boolean indicating whether the user can register a new account.
- canResetPassword: a boolean indicating whether the user can reset their password.
- status: a string containing a success message to be displayed after a successful login.

The component imports the following child components:
- Checkbox: a custom checkbox component.
- GuestLayout: a layout component for guest users.
- InputError: a component to display form input errors.
- InputLabel: a component to display form input labels.
- PrimaryButton: a custom button component.
- TextInput: a custom text input component.

The component uses the following composition API functions:
- defineProps: to define the component props.
- useForm: to create a form object to handle form submission.
- Head: to set the page title.
- Link: to create links to other pages.

The component template contains a form with two input fields for email and password, a checkbox for remembering the user's login session, and buttons to submit the form, register a new account, or reset the password. The form submission is handled by the submit function, which sends a POST request to the login route with the form data. The form processing state is handled by the form object, which disables the form buttons while the form is being processed.
-->
<script setup>
import Checkbox from '@/Components/Checkbox.vue';
import GuestLayout from '@/Layouts/GuestLayout.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';

defineProps({
    canRegister: Boolean,
    canResetPassword: Boolean,
    status: String,
});

const form = useForm({
    email: '',
    password: '',
    remember: false,
});

const submit = () => {
    form.post(route('login'), {
        onFinish: () => form.reset('password'),
    });
};
</script>

<template>
    <GuestLayout>
        <Head title="Log in" />

        <div v-if="status" class="mb-4 font-medium text-sm text-green-600">
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

            <div class="mt-4">
                <InputLabel for="password" value="Password" />

                <TextInput
                    id="password"
                    type="password"
                    class="mt-1 block w-full"
                    v-model="form.password"
                    required
                    autocomplete="current-password"
                />

                <InputError class="mt-2" :message="form.errors.password" />
            </div>

            <div class="block mt-4">
                <label class="flex items-center">
                    <Checkbox name="remember" v-model:checked="form.remember" />
                    <span class="ml-2 text-sm text-gray-600 dark:text-gray-400">Remember me</span>
                </label>
            </div>

            <div class="flex items-center justify-between mt-4 space-x-4">
                <Link
                    v-if="canRegister"
                    :href="route('register')"
                    class="text-center underline text-sm text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-100 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 dark:focus:ring-offset-gray-800"
                >
                    Register
                </Link>
                <Link
                    v-if="canResetPassword"
                    :href="route('password.request')"
                    class="text-center underline text-sm text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-100 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 dark:focus:ring-offset-gray-800"
                >
                    Forgot your password?
                </Link>

                <PrimaryButton :class="{ 'opacity-25': form.processing }" :disabled="form.processing">
                    Log in
                </PrimaryButton>
            </div>
        </form>
    </GuestLayout>
</template>
