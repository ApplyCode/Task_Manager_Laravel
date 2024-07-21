<!--
    DESCRIPTION: This file contains the view for creating a new user. It imports the AuthenticatedLayout component, Head component from Inertia.js, and NewUserForm component. The Head component sets the title of the page to "Users". The AuthenticatedLayout component provides the layout for the page. The NewUserForm component is used to render the form for creating a new user.
-->
<script setup>
// view for new users
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head } from '@inertiajs/vue3';
import NewUserForm from './Partials/NewUserForm.vue';
</script>

<template>
    <Head title="Users" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">New user</h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto px-6 lg:px-8 space-y-6">
                <div class="p-4 sm:p-8 bg-white dark:bg-gray-800 shadow sm:rounded-lg">
                    <NewUserForm class="max-w-xl" />
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
