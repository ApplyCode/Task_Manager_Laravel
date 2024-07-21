<!--
The following code defines a Vue.js component for displaying a list of patients.

Props:
- myPatients: an object representing the patients created by the authenticated user.
- patients: an object representing all the patients.

Computed properties:
- None.

Data:
- myPatients: a reactive reference to the myPatients prop.
- patients: a reactive reference to the patients prop.

Events:
- userUpdate: an event that is triggered when a user is updated or deleted.
- patientUpdate: an event that is triggered when a patient is created, updated or deleted.

Methods:
- None.

Template:
- AuthenticatedLayout: a custom layout component that displays the authenticated user's navigation bar and content.
- PatientsTable: a custom component that displays a table of patients.

-->
<script setup>
// view for patients listing
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import PatientsTable from './Partials/PatientsTable.vue';
import { Head, usePage } from '@inertiajs/vue3';
import { useToast } from "vue-toastification";
import { defineProps, onMounted, ref } from 'vue';
import eventBus from '@/Utils/eventBus';

const props = defineProps({
    myPatients: Object,
    patients: Object,
});

const myPatients = ref(props.myPatients);
const patients = ref(props.patients);

onMounted(() => {
    const redirectStatus = usePage().props.redirectStatus;
    const toast = useToast();
    if (redirectStatus.success) {
        toast.success(redirectStatus.success);
    }
    if (redirectStatus.error) {
        toast.error(redirectStatus.error);
    }
    eventBus.$on('userUpdate', (data) => {
        switch (data.type) {
            case 'update':
                myPatients.value.forEach((patient, index) => {
                    if (patient.user_id === data.user._id) {
                        myPatients.value[index].user = data.user;
                    }
                });
                patients.value.forEach((patient, index) => {
                    if (patient.user_id === data.user._id) {
                        patients.value[index].user = data.user;
                    }
                });
                break;
            case 'delete':
                myPatients.value = myPatients.value.filter((patient) => patient.user_id !== data.user._id);
                patients.value = patients.value.filter((patient) => patient.user_id !== data.user._id);
                break;
            default:
                break;
        }
    });
    eventBus.$on('patientUpdate', (data) => {
        switch (data.type) {
            case 'update':
                myPatients.value.forEach((patient, index) => {
                    if (patient._id === data.patient._id) {
                        myPatients.value[index] = data.patient;
                    }
                });
                patients.value.forEach((patient, index) => {
                    if (patient._id === data.patient._id) {
                        patients.value[index] = data.patient;
                    }
                });
                break;
            case 'create':
                patients.value = [...patients.value, data.patient];
                break;
            case 'delete':
                myPatients.value = myPatients.value.filter((patient) => patient._id !== data.patient._id);
                patients.value = patients.value.filter((patient) => patient._id !== data.patient._id);
                break;
            default:
                break;
        }
    })
});

</script>

<template>
    <Head title="Patients" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">Patients</h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto px-6 lg:px-8 gap-12 flex flex-col">
                <PatientsTable
                    :my-patients="myPatients"
                    :patients="patients"
                />
            </div>
        </div>
    </AuthenticatedLayout>
</template>
