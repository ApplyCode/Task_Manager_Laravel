<!--
    DESCRIPTION: This file contains the view for creating a new project. It imports the AuthenticatedLayout component, Head component from Inertia.js, and NewProjectForm component. The AuthenticatedLayout component provides the layout for the page, while the Head component sets the title of the page. The NewProjectForm component is used to render the form for creating a new project.
-->
<script setup>
// view for new projects
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head } from '@inertiajs/vue3';
import NewProjectForm from './Partials/NewProjectForm.vue';
</script>

<template>
    <Head title="Projects" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">New project</h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto px-6 lg:px-8 space-y-6">
                <div class="p-4 sm:p-8 bg-white dark:bg-gray-800 shadow sm:rounded-lg">
                    <NewProjectForm
                        class="max-w-xl"
                    />
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
