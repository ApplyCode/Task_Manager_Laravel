<!--
The Register component is responsible for rendering the registration form for new users. It imports several components such as GuestLayout, InputError, InputLabel, PrimaryButton, TextInput, and Checkbox. It also imports Head and Link from InertiaJS/vue3. 

The component uses the useForm method from InertiaJS/vue3 to create a reactive form object with fields for name, email, password, password_confirmation, terms, and squad_id. 

The component also defines a submit method that sends a POST request to the server to register the user. 

The iHaveSquad variable is a reactive reference to a boolean value that is used to toggle the display of the squad_id input field.

The template contains a form element that binds to the submit method. It contains several input fields for name, email, password, password_confirmation, and squad_id (if the user belongs to a squad). It also contains a checkbox for agreeing to the terms of service.

The template also contains links to the login page and a register button that is disabled while the form is processing.
-->
<script setup>
import GuestLayout from '@/Layouts/GuestLayout.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import Checkbox from '@/Components/Checkbox.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import { ref } from 'vue';

const form = useForm({
    name: '',
    email: '',
    password: '',
    password_confirmation: '',
    terms: false,
    squad_id: '',
});

const submit = () => {
    form.post(route('register'), {
        onFinish: () => form.reset('password', 'password_confirmation'),
    });
};

const iHaveSquad = ref(false);
</script>

<template>
    <GuestLayout>
        <Head title="Register" />

        <form @submit.prevent="submit">
            <div>
                <InputLabel for="name" value="Name" />

                <TextInput
                    id="name"
                    type="text"
                    class="mt-1 block w-full"
                    v-model="form.name"
                    required
                    autofocus
                    autocomplete="name"
                />

                <InputError class="mt-2" :message="form.errors.name" />
            </div>

            <div class="mt-4">
                <InputLabel for="email" value="Email" />

                <TextInput
                    id="email"
                    type="email"
                    class="mt-1 block w-full"
                    v-model="form.email"
                    required
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
                    autocomplete="new-password"
                />

                <InputError class="mt-2" :message="form.errors.password" />
            </div>

            <div class="mt-4">
                <InputLabel for="password_confirmation" value="Confirm Password" />

                <TextInput
                    id="password_confirmation"
                    type="password"
                    class="mt-1 block w-full"
                    v-model="form.password_confirmation"
                    required
                    autocomplete="new-password"
                />

                <InputError class="mt-2" :message="form.errors.password_confirmation" />
            </div>

            <div class="mt-4">
                <span class="block font-medium text-sm text-gray-700 dark:text-gray-300 cursor-pointer" v-if="!iHaveSquad" @click="() => iHaveSquad = !iHaveSquad">
                    I belong to a squad
                </span>
                <div v-else>
                    <InputLabel for="squad_id" value="Squad ID" />

                    <TextInput
                        id="squad_id"
                        type="text"
                        class="mt-1 block w-full"
                        v-model="form.squad_id"
                        autocomplete="squad_id"
                    />

                    <InputError class="mt-2" :message="form.errors.squad_id" />
                </div>
            </div>            

            <div class="block mt-4">
                <label class="flex items-center">
                    <Checkbox name="terms" v-model:checked="form.terms" />
                    <span class="ml-2 text-sm text-gray-600 dark:text-gray-400">
                        I agree to the <a href="#" class="underline">terms of service</a>.
                    </span>
                </label>
                <InputError class="mt-2" :message="form.errors.terms" />
            </div>

            <div class="flex items-center justify-between mt-4">
                <Link
                    :href="route('login')"
                    class="underline text-sm text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-100 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 dark:focus:ring-offset-gray-800"
                >
                    Already registered?
                </Link>

                <PrimaryButton class="ml-4" :class="{ 'opacity-25': form.processing }" :disabled="form.processing">
                    Register
                </PrimaryButton>
            </div>
        </form>
    </GuestLayout>
</template>
