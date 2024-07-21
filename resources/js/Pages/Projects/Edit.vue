<!--
    DESCRIPTION: This file contains the view for editing project details. It imports the AuthenticatedLayout component, Head component from InertiaJS, usePage hook from InertiaJS, and UpdateProjectForm component. It receives the project object as a prop from the server and passes it to the UpdateProjectForm component. The page title is set to "Projects".
-->
<script setup>
// view for editing projects details
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head } from '@inertiajs/vue3';
import { usePage } from '@inertiajs/vue3';
import UpdateProjectForm from './Partials/UpdateProjectForm.vue';

const project = usePage().props.project;

</script>

<template>
    <Head title="Projects" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">Project {{ project.name }}</h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto px-6 lg:px-8 space-y-6">
                <div class="p-4 sm:p-8 bg-white dark:bg-gray-800 shadow sm:rounded-lg">
                    <UpdateProjectForm
                        class="max-w-xl"
                    />
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
