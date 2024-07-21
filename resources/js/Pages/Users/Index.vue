<!--
    DESCRIPTION: This file contains the view for users listing. It imports AuthenticatedLayout, UsersTable, Head, usePage, useToast, defineProps, onMounted, ref, and eventBus from various sources. It defines props and users as reactive variables using defineProps and ref respectively. It also listens to the userUpdate event emitted by eventBus and updates the users variable accordingly. Finally, it renders the UsersTable component inside the AuthenticatedLayout component.
-->
<script setup>
// view for users listing
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import UsersTable from './Partials/UsersTable.vue';
import { Head, usePage } from '@inertiajs/vue3';
import { useToast } from "vue-toastification";
import { defineProps, onMounted, ref } from 'vue';
import eventBus from '@/Utils/eventBus';


const props = defineProps({
    users: Object,
});
const users = ref(props.users);

onMounted(() => {
    const  redirectStatus = usePage().props.redirectStatus;
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
                users.value = users.value.map((user) => {
                    if (user.id === data.user.id) {
                        return data.user;
                    }
                    return user;
                });
                break;
            case 'delete':
                users.value = users.value.filter((user) => user.id !== data.user.id);
                break;
            case 'create':
                users.value = [...users.value, data.user];
                break;
            default:
                break;
        }
    });
});


</script>

<template>
    <Head title="Users" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">Users</h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto px-6 lg:px-8 gap-12 flex flex-col">
                <UsersTable 
                    :users="users"
                />
            </div>
        </div>
    </AuthenticatedLayout>
</template>
