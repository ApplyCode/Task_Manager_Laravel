<!--
    DESCRIPTION: This file contains the view for creating a new patient. It imports the AuthenticatedLayout component, Head component from Inertia.js, and NewPatientForm component. The AuthenticatedLayout component provides the layout for the page, while the Head component sets the title of the page. The NewPatientForm component is used to render the form for creating a new patient.
-->
<script setup>
// view for new patients
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head } from '@inertiajs/vue3';
import NewPatientForm from './Partials/NewPatientForm.vue';
</script>

<template>
    <Head title="Patients" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">New patient</h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto px-6 lg:px-8 space-y-6">
                <div class="p-4 sm:p-8 bg-white dark:bg-gray-800 shadow sm:rounded-lg">
                    <NewPatientForm
                        class="max-w-xl"
                    />
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
