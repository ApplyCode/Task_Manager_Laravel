<!--
The following code defines a Vue.js component for displaying a list of appointments.

Props:
- myAppointments: an object representing the appointments created by the authenticated user.
- appointments: an object representing all the appointments.

Computed properties:
- None.

Data:
- myAppointments: a reactive reference to the myAppointments prop.
- appointments: a reactive reference to the appointments prop.

Events:
- userUpdate: an event that is triggered when a user is updated or deleted.
- appointmentUpdate: an event that is triggered when a appointment is created, updated or deleted.

Methods:
- None.

Template:
- AuthenticatedLayout: a custom layout component that displays the authenticated user's navigation bar and content.
- AppointmentsTable: a custom component that displays a table of appointments.

-->
<script setup>
// view for appointments listing
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import AppointmentsTable from './Partials/AppointmentsTable.vue';
import { Head, usePage } from '@inertiajs/vue3';
import { useToast } from "vue-toastification";
import { defineProps, onMounted, ref } from 'vue';
import eventBus from '@/Utils/eventBus';

const props = defineProps({
    myAppointments: Object,
    appointments: Object,
});

const myAppointments = ref(props.myAppointments);
const appointments = ref(props.appointments);

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
                myAppointments.value.forEach((appointment, index) => {
                    if (appointment.user_id === data.user._id) {
                        myAppointments.value[index].user = data.user;
                    }
                });
                appointments.value.forEach((appointment, index) => {
                    if (appointment.user_id === data.user._id) {
                        appointments.value[index].user = data.user;
                    }
                });
                break;
            case 'delete':
                myAppointments.value = myAppointments.value.filter((appointment) => appointment.user_id !== data.user._id);
                appointments.value = appointments.value.filter((appointment) => appointment.user_id !== data.user._id);
                break;
            default:
                break;
        }
    });
    eventBus.$on('appointmentUpdate', (data) => {
        switch (data.type) {
            case 'update':
                myAppointments.value.forEach((appointment, index) => {
                    if (appointment._id === data.appointment._id) {
                        myAppointments.value[index] = data.appointment;
                    }
                });
                appointments.value.forEach((appointment, index) => {
                    if (appointment._id === data.appointment._id) {
                        appointments.value[index] = data.appointment;
                    }
                });
                break;
            case 'create':
                appointments.value = [...appointments.value, data.appointment];
                break;
            case 'delete':
                myAppointments.value = myAppointments.value.filter((appointment) => appointment._id !== data.appointment._id);
                appointments.value = appointments.value.filter((appointment) => appointment._id !== data.appointment._id);
                break;
            default:
                break;
        }
    })
});

</script>

<template>
    <Head title="Appointments" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">Appointments</h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto px-6 lg:px-8 gap-12 flex flex-col">
                <AppointmentsTable
                    :my-appointments="myAppointments"
                    :appointments="appointments"
                />
            </div>
        </div>
    </AuthenticatedLayout>
</template>
