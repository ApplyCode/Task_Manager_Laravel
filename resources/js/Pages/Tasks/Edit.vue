<!--
    DESCRIPTION: This file contains the code for the edit task page. It imports the AuthenticatedLayout component and Head component from InertiaJS. The page displays a header with the title "Task" and a content section with the text "Your task! edit". The layout is wrapped in an AuthenticatedLayout component.
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
                    <div class="p-6 text-gray-900 dark:text-gray-100">Your task! edit</div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
