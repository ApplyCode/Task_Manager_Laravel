<!--
    DESCRIPTION: This file contains the code for the task creation page. It imports the AuthenticatedLayout component and Head component from external files. The page displays a header with the title "Task" and a content section with the text "Your task! create". The content section is wrapped in an AuthenticatedLayout component, which ensures that only authenticated users can access the page.
-->
<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head } from '@inertiajs/vue3';
</script>

<template>
    <Head title="Task" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">Task</h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-gray-900 dark:text-gray-100">Your task! create</div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
