<!--
The following code defines a Vue.js component for displaying a list of projects. 

Props:
- myProjects: an object representing the projects created by the authenticated user.
- projects: an object representing all the projects.

Computed properties:
- None.

Data:
- myProjects: a reactive reference to the myProjects prop.
- projects: a reactive reference to the projects prop.

Events:
- userUpdate: an event that is triggered when a user is updated or deleted.
- projectUpdate: an event that is triggered when a project is created, updated or deleted.

Methods:
- None.

Template:
- AuthenticatedLayout: a custom layout component that displays the authenticated user's navigation bar and content.
- ProjectsTable: a custom component that displays a table of projects.

-->
<script setup>
// view for projects listing
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import ProjectsTable from './Partials/ProjectsTable.vue';
import { Head, usePage } from '@inertiajs/vue3';
import { useToast } from "vue-toastification";
import { defineProps, onMounted, ref } from 'vue';
import eventBus from '@/Utils/eventBus';

const props = defineProps({
    myProjects: Object,
    projects: Object,
});

const myProjects = ref(props.myProjects);
const projects = ref(props.projects);

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
                myProjects.value.forEach((project, index) => {
                    if (project.user_id === data.user._id) {
                        myProjects.value[index].user = data.user;
                    }
                });
                projects.value.forEach((project, index) => {
                    if (project.user_id === data.user._id) {
                        projects.value[index].user = data.user;
                    }
                });
                break;
            case 'delete':
                myProjects.value = myProjects.value.filter((project) => project.user_id !== data.user._id);
                projects.value = projects.value.filter((project) => project.user_id !== data.user._id);
                break;
            default:
                break;
        }
    });
    eventBus.$on('projectUpdate', (data) => {
        switch (data.type) {
            case 'update':
                myProjects.value.forEach((project, index) => {
                    if (project._id === data.project._id) {
                        myProjects.value[index] = data.project;
                    }
                });
                projects.value.forEach((project, index) => {
                    if (project._id === data.project._id) {
                        projects.value[index] = data.project;
                    }
                });       
                break;         
            case 'create':
                projects.value = [...projects.value, data.project];
                break;
            case 'delete':
                myProjects.value = myProjects.value.filter((project) => project._id !== data.project._id);
                projects.value = projects.value.filter((project) => project._id !== data.project._id);
                break;
            default:
                break;
        }
    })
});

</script>

<template>
    <Head title="Projects" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">Projects</h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto px-6 lg:px-8 gap-12 flex flex-col">
                <ProjectsTable 
                    :my-projects="myProjects"
                    :projects="projects"
                />
            </div>
        </div>
    </AuthenticatedLayout>
</template>
