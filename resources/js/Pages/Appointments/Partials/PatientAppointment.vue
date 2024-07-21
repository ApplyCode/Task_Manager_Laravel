
<!--
The following code represents the template and style of a Vue component that displays a table of appointments for a patient. The component receives the following props:
- patient: an array of appointments to be displayed in the table
- patientColor: a string representing the color of the patient
- patientTextColor: a string representing the text color of the patient
- selectedAppointments: an array of selected appointments
- usersDictionary: an object containing user information

The component displays a table with columns for appointment name, due date, priority, team, and labels. The table can be sorted by clicking on the column headers. The component also includes a button to add a new appointment to the patient.

The component uses Vue transitions to animate the appearance and disappearance of table rows.

The component also includes a custom scrollbar style.

Props:
- patient: Array of appointments to be displayed in the table
- patientColor: String representing the color of the patient
- patientTextColor: String representing the text color of the patient
- selectedAppointments: Array of selected appointments
- usersDictionary: Object containing user information

Emits:
- new-appointment: Event emitted when the user clicks the "Add" button to create a new appointment

Example Usage:
<PatientAppointment :patient="appointments" patientColor="#FF0000" patientTextColor="#FFFFFF" :selectedAppointments="selectedAppointments" :usersDictionary="users" @new-appointment="createAppointment"/>
-->
<script setup>
import { ref, onMounted } from 'vue';
import { Icon } from '@iconify/vue';
import AppointmentTableLine from './AppointmentTableLine.vue';
import { stringToColour } from '@/Utils/globalFunctions';
import { usePage, Link } from '@inertiajs/vue3';
import eventBus from '@/Utils/eventBus';

const props = defineProps({
    patient: {
        type: Array,
        required: true
    },
    selectedAppointments: {
        type: Array,
        required: true
    },
    patientName: {
        type: String,
        required: false
    },
    patientId: {
        type: String,
        required: false
    },
});
const patient = ref(props.patient);


const collapsed = ref(false);
const patientName = props.patientName ?? (patient.value[0]?.patient != null ? patient.value[0]?.patient?.name : '');
const patientColor = ref(stringToColour(patientName));

const patientBackgroundColor = ref(patientColor.value + '1a');
const patientTextColor = ref(patientColor.value + 'e6');

const toggleCollapse = () => {
    collapsed.value = !collapsed.value;
};

const ordedBy = ref();
const orderDirection = ref();

const usersDictionary = ref(Object.fromEntries(usePage().props.allUsers.map(user => [user._id, user])));
const statusDictionary = {
    'Cancelled': 0,
    'Not Started': 1,
    'In Progress': 2,
    'On Hold': 3,
    'Completed': 4,
};

const priorityDictionary = {
    'None': 0,
    'Lowest': 1,
    'Low': 2,
    'Medium': 3,
    'High': 4,
    'Highest': 5,
};

const orderBy = (field) => {
    const fields = field.split('.');
    if (ordedBy.value === field) {
        orderDirection.value = orderDirection.value === 'asc' ? 'desc' : 'asc';
    } else {
        ordedBy.value = field;
        orderDirection.value = 'asc';
    }
    switch (field) {
        case 'assigned_to':
            patient.value.sort((a, b) => {
                if (orderDirection.value === 'asc') {
                    return usersDictionary.value[a[field]]?.name > usersDictionary.value[b[field]]?.name ? 1 : -1;
                }
                return usersDictionary.value[a[field]]?.name < usersDictionary.value[b[field]]?.name ? 1 : -1;
            });
            break;
        case 'status':
            patient.value.sort((a, b) => {
                if (orderDirection.value === 'asc') {
                    return statusDictionary[a[field]] > statusDictionary[b[field]] ? 1 : -1;
                }
                return statusDictionary[a[field]] < statusDictionary[b[field]] ? 1 : -1;
            });
            break;
        case 'priority':
            patient.value.sort((a, b) => {
                if (orderDirection.value === 'asc') {
                    return priorityDictionary[a[field]] > priorityDictionary[b[field]] ? 1 : -1;
                }
                return priorityDictionary[a[field]] < priorityDictionary[b[field]] ? 1 : -1;
            });
            break;
        case 'start_date':
            patient.value.sort((a, b) => {
                const dateA = a[field]?.$date?.$numberLong ? new Date(parseInt(a[field]?.$date.$numberLong)) : new Date(9999999999999);
                const dateB = b[field]?.$date?.$numberLong ? new Date(parseInt(b[field]?.$date.$numberLong)) : new Date(9999999999999);
                if (orderDirection.value === 'asc') {
                    return dateA > dateB ? 1 : -1;
                }
                return dateA < dateB ? 1 : -1;
            });
            break;
        default:
            patient.value.sort((a, b) => {
                if (orderDirection.value === 'asc') {
                    return fields.reduce((acc, cur) => acc[cur], a) > fields.reduce((acc, cur) => acc[cur], b) ? 1 : -1;
                }
                return fields.reduce((acc, cur) => acc[cur], a) < fields.reduce((acc, cur) => acc[cur], b) ? 1 : -1;
            });
            break;
    }
};

function newAppointmentModal() {
    eventBus.$emit('newAppointmentModal', {
        appointment: undefined,
        patient: props.patientId ?? patient.value[0]?.patient?._id,
    });
}

onMounted(() => {
    eventBus.$on('userUpdate', (data) => {
        switch (data.type) {
            case 'update':
            case 'create':
                usersDictionary.value[data.user._id] = data.user;
                patient.value.forEach((item) => {
                    if (item.user._id === data.user._id) {
                        item.user = data.user;
                    }
                });
                break;
            case 'delete':
                patient.value = patient.value.filter((item) => item.user._id !== data.user._id).filter((item) => item.patient?.user_id !== data.user._id);
                break;
            default:
                break;
        }
    });
    eventBus.$on('commentUpdate', (data) => {
        switch (data.type) {
            case 'create':
                patient.value.forEach((item) => {
                    if (item._id === data.message.appointment_id) {
                        item.comments = data.message.comments;
                    }
                });
                break;
            default:
                break;
        }
    });
    eventBus.$on('patientUpdate', (data) => {
        if  ((data.patient._id === patient.value[0]?.patient?._id) && (data.type === 'update')) {
            patient.value.name = data.patient.name;
            patientColor.value = stringToColour(data.patient.name);
            patientBackgroundColor.value = patientColor.value + '1a';
            patientTextColor.value = patientColor.value + 'e6';
        }
    });
    eventBus.$on('appointmentUpdate', (data) => {
        switch (data.type) {
            case 'update':
                if ((data.appointment.patient_id === patient.value[0]?.patient?._id) || (data.appointment.patient_id === props.patientId)) {
                    patient.value  = patient.value.map((item) => {
                        if (item._id === data.appointment._id) {
                            return data.appointment;
                        }
                        return item;
                    });
                    if (!patient.value.find((item) => item._id === data.appointment._id)) {
                        patient.value.push(data.appointment);
                    }
                }
                break;
            case 'create':
                if ((data.appointment.patient_id === patient.value[0]?.patient?._id) || (data.appointment.patient_id === props.patientId)) {
                    patient.value.push(data.appointment);
                }
                break;
            case 'delete':
                if (data.appointment.patient_id === patient.value[0]?.patient?._id) {
                    patient.value = patient.value.filter((item) => item._id !== data.appointment._id);
                }
                break;
            default:
                break;
        }

    });
})
</script>

<template>
    <div class="mb-1 overflow-x-auto custom-scroll">
        <table class="">
            <thead>

                <transition
                    enter-active-class="transition ease-out duration-200"
                    enter-from-class="transform opacity-0 scale-95"
                    enter-to-class="transform opacity-100 scale-100"
                    leave-active-class="transition ease-in duration-75"
                    leave-from-class="transform opacity-100 scale-100"
                    leave-to-class="transform opacity-0 scale-95"
                >
                    <tr>
                        <th>
                            <Icon icon="iconamoon:arrow-up-2-bold" :class="{
                                'transition-all mx-1 cursor-pointer': true,
                                'transform rotate-180': collapsed,
                            }"
                            :style="{ color: patientTextColor }"
                            @click="toggleCollapse"/>
                        </th>
                        <th class="text-start px-1 text-lg flex items-center cursor-pointer"
                            :style="{ color: patientTextColor }"
                             @click="orderBy('title')"
                             v-tooltip="patientName"
                        >
                            <span class="max-w-[300px]" style="overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">
                                {{ patientName }}
                            </span>
                            <Icon icon="iconamoon:arrow-up-2-bold" :class="{
                                'transition-all mx-1': true,
                                'transform rotate-180': orderDirection === 'asc',
                                'opacity-0': ordedBy !== 'title',
                                'opacity-100': ordedBy === 'title',
                            }"/>
                        </th>
                        <th v-if="!collapsed && patient.length !== 0" class="px-1 text-base font-medium">
                            <div class="flex items-center justify-center cursor-pointer">
                                <span>
                                    Comments
                                </span>
                            </div>
                        </th>
                        <th v-if="!collapsed && patient.length !== 0" class="px-1 text-base font-medium" @click="orderBy('user.name')">
                            <div class="flex items-center justify-center cursor-pointer">
                                <span>
                                    Owner
                                </span>
                                <Icon icon="iconamoon:arrow-up-2-bold" :class="{
                                    'transition-all mx-1': true,
                                    'transform rotate-180': orderDirection === 'asc',
                                    'opacity-0': ordedBy !== 'user.name',
                                    'opacity-100': ordedBy === 'user.name',
                                }"/>
                            </div>
                        </th>
                        <th v-if="!collapsed && patient.length !== 0" class="px-1 text-base font-medium" @click="orderBy('assigned_to')">
                            <div class="flex items-center justify-center cursor-pointer">
                                <span>
                                    Assingee
                                </span>
                                <Icon icon="iconamoon:arrow-up-2-bold" :class="{
                                    'transition-all mx-1': true,
                                    'transform rotate-180': orderDirection === 'asc',
                                    'opacity-0': ordedBy !== 'assigned_to',
                                    'opacity-100': ordedBy === 'assigned_to',
                                }"/>
                            </div>
                        </th>
                        <th v-if="!collapsed && patient.length !== 0" class="px-1 text-base font-medium" @click="orderBy('status')">
                            <div class="flex items-center justify-center cursor-pointer">
                                <span>
                                    Status
                                </span>
                                <Icon icon="iconamoon:arrow-up-2-bold" :class="{
                                    'transition-all mx-1': true,
                                    'transform rotate-180': orderDirection === 'asc',
                                    'opacity-0': ordedBy !== 'status',
                                    'opacity-100': ordedBy === 'status',
                                }"/>
                            </div>
                        </th>
                        <th v-if="!collapsed && patient.length !== 0" class="px-1 text-base font-medium" @click="orderBy('start_date')">
                            <div class="flex items-center justify-center cursor-pointer">
                                <span>
                                    Timeline
                                </span>
                                <Icon icon="iconamoon:arrow-up-2-bold" :class="{
                                    'transition-all mx-1': true,
                                    'transform rotate-180': orderDirection === 'asc',
                                    'opacity-0': ordedBy !== 'start_date',
                                    'opacity-100': ordedBy === 'start_date',
                                }"/>
                            </div>
                        </th>
                        <th v-if="!collapsed && patient.length !== 0" class="px-1 text-base font-medium" @click="orderBy('progress')">
                            <div class="flex items-center justify-center cursor-pointer">
                                <span>
                                    % Completed
                                </span>
                                <Icon icon="iconamoon:arrow-up-2-bold" :class="{
                                    'transition-all mx-1': true,
                                    'transform rotate-180': orderDirection === 'asc',
                                    'opacity-0': ordedBy !== 'progress',
                                    'opacity-100': ordedBy === 'progress',
                                }"/>
                            </div>
                        </th>
                        <th v-if="!collapsed && patient.length !== 0" class="px-1 text-base font-medium" @click="orderBy('priority')">
                            <div class="flex items-center justify-center cursor-pointer">
                                <span>
                                    Priority
                                </span>
                                <Icon icon="iconamoon:arrow-up-2-bold" :class="{
                                    'transition-all mx-1': true,
                                    'transform rotate-180': orderDirection === 'asc',
                                    'opacity-0': ordedBy !== 'priority',
                                    'opacity-100': ordedBy === 'priority',
                                }"/>
                            </div>
                        </th>
                        <th v-if="!collapsed && patient.length !== 0" class="px-1 text-base font-medium" @click="orderBy('team')">
                            <div class="flex items-center justify-center cursor-pointer">
                                <span>
                                    Team
                                </span>
                                <Icon icon="iconamoon:arrow-up-2-bold" :class="{
                                    'transition-all mx-1': true,
                                    'transform rotate-180': orderDirection === 'asc',
                                    'opacity-0': ordedBy !== 'team',
                                    'opacity-100': ordedBy === 'team',
                                }"/>
                            </div>
                        </th>
                        <th v-if="!collapsed && patient.length !== 0" class="px-1 text-base font-medium" @click="orderBy('labels')">
                            <div class="flex items-center justify-center cursor-pointer">
                                <span>
                                    Labels
                                </span>
                                <Icon icon="iconamoon:arrow-up-2-bold" :class="{
                                    'transition-all mx-1': true,
                                    'transform rotate-180': orderDirection === 'asc',
                                    'opacity-0': ordedBy !== 'labels',
                                    'opacity-100': ordedBy === 'labels',
                                }"/>
                            </div>
                        </th>
                    </tr>
                </transition>
            </thead>
            <transition
                enter-active-class="transition ease-out duration-200"
                enter-from-class="transform opacity-0 scale-95"
                enter-to-class="transform opacity-100 scale-100"
                leave-active-class="transition ease-in duration-75"
                leave-from-class="transform opacity-100 scale-100"
                leave-to-class="transform opacity-0 scale-95"
            >
                <tbody v-if="!collapsed" >
                    <TransitionGroup name="list">
                        <tr v-for="item in patient" :key="item._id" :style="{backgroundColor: patientBackgroundColor}">
                            <AppointmentTableLine :item="{...item}" :patientColor="patientColor" :patientTextColor="patientTextColor" :selectedAppointments="selectedAppointments" :users="usersDictionary"/>
                        </tr>
                    </TransitionGroup>
                    <tr>
                        <td></td>
                        <td colspan="100%" class=" text-sm font-medium text-gray-900 dark:text-gray-100 p-0"
                            :style="{
                                borderBottom: '2px solid white',
                                borderRight: '2px solid white',
                            }"
                        >
                            <div class="flex" @click="newAppointmentModal()">
                                    <div class="w-[4px]" :style="{
                                    backgroundColor: patientBackgroundColor,
                                }"></div>
                                <div class="cursor-pointer px-3 py-2 grow" :style="{ 'overflow': 'hidden', 'text-overflow': 'ellipsis', 'white-space': 'nowrap' }">
                                    + Add
                                </div>
                            </div>
                        </td>
                    </tr>
                </tbody>
                <tbody v-else>
                    <tr>
                        <td></td>
                        <td colspan="100%" class=" text-sm font-medium text-gray-900 dark:text-gray-100 p-0"
                            :style="{
                                borderBottom: '2px solid white',
                                borderRight: '2px solid white',
                            }"
                        >
                            <div class="flex" @click="newAppointmentModal()">
                                <div class="w-[4px]" :style="{
                                    backgroundColor: patientBackgroundColor,
                                }"></div>
                                <div class="cursor-pointer px-3 py-2 grow" :style="{ 'overflow': 'hidden', 'text-overflow': 'ellipsis', 'white-space': 'nowrap' }">
                                    + Add
                                </div>
                            </div>
                        </td>
                    </tr>

                </tbody>
            </transition>
        </table>
    </div>
</template>

<style scoped>
.custom-scroll::-webkit-scrollbar {
    height: 8px;
}

.custom-scroll::-webkit-scrollbar-track {
    background-color: transparent;
}

.custom-scroll::-webkit-scrollbar-thumb {
    background-color: transparent;
    border-radius: 8px;
}

.custom-scroll:hover::-webkit-scrollbar-thumb {
    background-color: #90909090;
}

.list-enter-active,
.list-leave-active {
  transition: all 0.5s ease;
}
.list-enter-from,
.list-leave-to {
  opacity: 0;
}

</style>
