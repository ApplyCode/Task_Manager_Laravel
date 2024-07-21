<!--
    DESCRIPTION: This component is responsible for rendering a form to create a new user. It imports InputError, InputLabel, PrimaryButton, TextInput, Link, useForm, and usePage from Inertiajs/vue3. It defines props and form using defineProps and useForm respectively. The form has two fields, name and email, and it is submitted to the route 'users.store' when the user clicks the Invite button. The form also displays validation errors if any. The Cancel button redirects the user to the 'users' page. 
-->
<script setup>
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import { Link, useForm, usePage } from '@inertiajs/vue3';

const props = defineProps({

});

const form = useForm({
    name: '',
    email: '',
});
</script>

<template>
    <section>
        <header>
            <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">User Details</h2>

            <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                Isert below the user details.
            </p>
        </header>

        <form @submit.prevent="form.post(route('users.store'))" class="mt-6 space-y-6">
            <div>
                <InputLabel for="name" value="Name" />

                <TextInput id="name" type="text" class="mt-1 block w-full" v-model="form.name" required autofocus
                    autocomplete="name" />

                <InputError class="mt-2" :message="form.errors.name" />
            </div>

            <div>
                <InputLabel for="email" value="Email" />

                <TextInput id="email" type="email" class="mt-1 block w-full" v-model="form.email" required
                />

                <InputError class="mt-2" :message="form.errors.email" />
            </div>

            <div class="flex items-center justify-between gap-4">
                <PrimaryButton :disabled="form.processing">Invite</PrimaryButton>

                <Transition enter-from-class="opacity-0" leave-to-class="opacity-0" class="transition ease-in-out">
                    <p v-if="form.recentlySuccessful" class="text-sm text-gray-600 dark:text-gray-400">Invited.</p>
                </Transition>

                <Link :href="route('users')" class="inline-flex items-center px-4 py-2 bg-red-800 dark:bg-red-200 border border-transparent rounded-md font-semibold text-xs text-white dark:text-red-800 uppercase tracking-widest hover:bg-red-700 dark:hover:bg-white focus:bg-red-700 dark:focus:bg-white active:bg-red-900 dark:active:bg-red-300 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 dark:focus:ring-offset-red-800 transition ease-in-out duration-150">Cancel</Link>
            </div>
        </form>
    </section>
</template>