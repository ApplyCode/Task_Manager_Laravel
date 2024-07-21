<!--
    DESCRIPTION: This component displays the user's squad information and allows them to update their squad ID.
    PROPS:
        - None
    DATA:
        - user: The authenticated user object.
        - form: The form object used to update the user's squad ID.
    METHODS:
        - None
-->
<script setup>
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import TextInput from '@/Components/TextInput.vue';
import { useForm, usePage } from '@inertiajs/vue3';

const props = defineProps({
});

const user = usePage().props.auth.user;

const form = useForm({
    squad_id: user.squad_id,
});
</script>

<template>
    <section>
        <header>
            <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">Squad Information</h2>

            <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                Find below your squad ID.
            </p>
        </header>

        <form @submit.prevent="form.patch(route('profile.update'))" class="mt-6 space-y-6">
            <div>
                <InputLabel for="squad_id" :value="`Squad ID: ${form.squad_id}`" />
            </div>
        </form>
    </section>
</template>
