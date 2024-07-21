<!--
    COMPONENT: Show
    DESCRIPTION: This component displays the details of a patient, including its name, description, and appointments. It also allows the user to delete multiple appointments at once.
    PROPS:
        - patient: The patient object containing the patient's details and appointments.
    DATA:
        - activeTab: The currently active tab (either 'patientAppointments' or 'other').
        - selectedAppointments: An array of selected appointments to be deleted.
        - allAppointments: An array of all appointments in the patient.
        - displayMenu: A boolean indicating whether or not to display the dropdown menu.
    METHODS:
        - displayMenuFunc(): A function that returns a boolean indicating whether or not to display the dropdown menu.
        - deleteBulkAppointments(): A function that deletes the selected appointments.
    EVENTS:
        - appointmentCheckbox: An event that is emitted when a appointment checkbox is clicked.
        - patientUpdate: An event that is emitted when the patient is updated or deleted.
-->
<script setup>
//view for one patient showing (appointments)
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head } from '@inertiajs/vue3';
import { usePage } from '@inertiajs/vue3';
import { ref , onMounted} from 'vue';
import { Icon } from '@iconify/vue';
import PatientAppointment from '../Appointments/Partials/PatientAppointment.vue';
import eventBus from '@/Utils/eventBus';
import Dropdown from '@/Components/Dropdown.vue';
import axios from 'axios';

const patient = ref(usePage().props.patient);

const activeTab = ref('patientAppointments');

const selectedAppointments = ref([]);
const allAppointments = [...patient.value.appointments].flat().filter((appointment, index, self) => index === self.findIndex((t) => t._id === appointment._id));

function displayMenuFunc() {
    return selectedAppointments?.value.some((appointment) => allAppointments.some((t) => t._id === appointment));
};

const displayMenu = ref(displayMenuFunc());

onMounted(() => {
    eventBus.$on('appointmentCheckbox', (content) => {
        //check if content is in selectedAppointments if not add it if yes remove it
        if (selectedAppointments.value.includes(content)) {
            selectedAppointments.value = selectedAppointments.value.filter((appointment) => appointment !== content);
        } else {
            selectedAppointments.value = [...selectedAppointments.value, content];
        }
        displayMenu.value = displayMenuFunc();
    });
    eventBus.$on('patientUpdate', (data) => {
        switch (data.type) {
            case 'update':
                if (patient.value._id === data.patient._id) {
                    patient.value.name = data.patient.name;
                    patient.value.description = data.patient.description;
                    Object.keys(patient.value.appointments).forEach((key) => {
                        if (patient.value.appointments[key].patient) {
                            patient.value.appointments[key].patient.name = data.patient.name;
                        }
                    });
                }
                break;
            case 'delete':
                window.location.href = route('patients');
                break;
            default:
                break;
        }
    });
});

const deleteBulkAppointments = () => {
    if (confirm('Are you sure you want to delete these appointments?')) {
        axios.delete(route('appointments.destroy.bulk', { ids: Object.values(selectedAppointments.value) }))
            .then(() => {
                window.location.reload();
            })
    }
}

</script>

<template>
    <Head title="PatientAppointment>

    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">PatientAppointmentpatient.name }}</h2>
        </template>

        <div class="bg-white dark:bg-gray-800 overflow-hidden">
            <div class="flex bg-gray-50 dark:bg-gray-950">
                <div
                    :class="{
                        'cursor-pointer text-base font-medium p-2 text-center text-gray-600 border-b-2 border-indigo-400 dark:border-indigo-600 text-indigo-800 dark:text-indigo-300 bg-indigo-50 dark:bg-indigo-900/50 focus:outline-none focus:text-indigo-800 dark:focus:text-indigo-200 focus:bg-indigo-100 dark:focus:bg-indigo-900 focus:border-indigo-700 dark:focus:border-indigo-300 transition duration-150 ease-in-out':
                            activeTab === 'patientAppointments',
                        'cursor-pointer text-base font-medium p-2 text-center text-gray-600 border-b-2 border-gray-50 dark:border-gray-950':
                            activeTab !== 'patientAppointments'
                    }"
                    @click="activeTab = 'patientAppointments'"
                >
                    PatientAppointmentks
                </div>
                <div
                    class="grow grid justify-end items-center"
                >

                    <div class="relative">
                        <Dropdown align="appointmentContext" width="48">
                            <template #trigger>
                                <div class="px-4"
                                    :class="{
                                        'text-gray-600 dark:text-gray-300 transition duration-150 ease-in-out': true,
                                        'opacity-0': !displayMenu,
                                        'opacity-100': displayMenu,
                                    }"
                                >
                                    <Icon icon="mdi:dots-vertical" class="cursor-pointer" v-tooltip="'More Options'"></Icon>
                                </div>
                            </template>

                            <template #content>
                                <span class="block w-full px-4 py-2 text-left text-sm leading-5 text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-800 focus:outline-none focus:bg-gray-100 dark:focus:bg-gray-800 transition duration-150 ease-in-out cursor-pointer" @click.prevent="deleteBulkAppointments()"> Delete appointments </span>
                            </template>
                        </Dropdown>
                    </div>
                </div>
            </div>
            <div class="p-4">
                <div v-if="activeTab === 'patientAppointments'">
                    <h2 class="text-lg font-semibold mb-2">Appointments</h2>
                    <div class="flex flex-col gap-2">
                        <div class="p-2">
                            <PatientAppointment :patient="patient.appointments" :selectedAppointments="selectedAppointments" :patientName="patient.name" :patientId="patient._id"/>
                        </div>
                    </div>
                </div>
                <div v-else-if="activeTab === 'other'">
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
