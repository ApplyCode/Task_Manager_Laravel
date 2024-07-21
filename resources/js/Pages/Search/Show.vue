<!--
This component is responsible for displaying the search results for users, projects, and tasks. It receives the following props:
- searchedUsers: an array of users that match the search term.
- searchedProjects: an array of projects that match the search term.
- searchedTasks: an object containing arrays of tasks for each project that match the search term.
- searchedTerm: the search term entered by the user.
- route: a function that generates a URL for a given route name and parameters.
- stringToColour: a function that generates a color based on a given string.

The component consists of two tabs: "Searched term" and "Filters". The "Searched term" tab displays the search results for users, projects, and tasks. The "Filters" tab allows the user to filter the search results by various criteria.

The component uses the following sub-components:
- AuthenticatedLayout: a layout component that displays the authenticated user's navigation bar and sidebar.
- Dropdown: a component that displays a dropdown menu.
- Icon: a component that displays an icon.
- Link: a component that displays a link.

The component emits the following events:
- deleteBulkTasks: an event that is emitted when the user clicks the "Delete tasks" option in the task context menu.

Example usage:
<SearchShow
    :searchedUsers="searchedUsers"
    :searchedProjects="searchedProjects"
    :searchedTasks="searchedTasks"
    :searchedTerm="searchedTerm"
    :route="route"
    :stringToColour="stringToColour"
    @deleteBulkTasks="deleteBulkTasks"
/>
-->
<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head } from '@inertiajs/vue3';
import { Link, usePage } from '@inertiajs/vue3';
import { ref, onMounted } from 'vue';
import { Icon } from '@iconify/vue';
import eventBus from '@/Utils/eventBus';
import Dropdown from '@/Components/Dropdown.vue';
import ProjectTask from '../Tasks/Partials/ProjectTask.vue';
import { stringToColour } from '@/Utils/globalFunctions';
import axios from 'axios';

const searchedUsers = ref(usePage().props.searchedUsers);
const searchedProjects = ref(usePage().props.searchedProjects);
const searchedTasks = ref(usePage().props.searchedTasks);
const activeTab = ref('searchedTerm');
const params = new URLSearchParams(window.location.search)
const searchedTerm = params.get('search')

const selectedTasks = ref([]);
const allTasks = [...Object.values(searchedTasks.value)].flat().filter((task, index, self) => index === self.findIndex((t) => t._id === task._id));

function displayMenuFunc() {
    return selectedTasks?.value.some((task) => allTasks.some((t) => t._id === task));
};

const displayMenu = ref(displayMenuFunc());

onMounted(() => {
    eventBus.$on('taskCheckbox', (content) => {
        //check if content is in selectedTasks if not add it if yes remove it
        if (selectedTasks.value.includes(content)) {
            selectedTasks.value = selectedTasks.value.filter((task) => task !== content);
        } else {
            selectedTasks.value = [...selectedTasks.value, content];
        }
        displayMenu.value = displayMenuFunc();
    });
    eventBus.$on('userUpdate', (data) => {
        switch (data.type) {
            case 'create':
                if (data.user.name.toLowerCase().includes(searchedTerm.toLowerCase()) || data.user.email.toLowerCase().includes(searchedTerm.toLowerCase())) {
                    searchedUsers.value = [...searchedUsers.value, data.user];
                }                    
                break;
            case 'update':
                searchedUsers.value = Object.values(searchedUsers.value).map((user) => {
                    if (user._id === data.user._id) {
                        user = data.user;
                    }
                    return user;
                });
            case 'delete':
                searchedUsers.value = searchedUsers.value.filter((user) => user._id !== data.user._id);
                break;
        }
    });
    eventBus.$on('projectUpdate', (data) => {
        switch (data.type) {
            case 'create':
                if (data.project.name.toLowerCase().includes(searchedTerm.toLowerCase()) || data.project.description.toLowerCase().includes(searchedTerm.toLowerCase())) {
                    searchedProjects.value[data.project._id] = data.project;
                }                    
                break;
            case 'update':
                // map all the searched projects, if the project id is in the searched projects then update the project in the searched projects
                searchedProjects.value = Object.values(searchedProjects.value).map((project) => {
                    if (project._id === data.project._id) {
                        project.name = data.project.name;
                        project.description = data.project.description;
                    }
                    return project;
                });
                Object.values(searchedTasks.value).forEach((project) => {
                    project.forEach((task) => {
                        if (task.project_id === data.project._id) {
                            task.project = data.project;
                        }
                    });
                });


                break;
            case 'delete':
                searchedProjects.value = searchedProjects.value.filter((project) => project._id !== data.project._id);
                break;
            default:
                break;
        }
    });
});

const deleteBulkTasks = () => {
    if (confirm('Are you sure you want to delete these tasks?')) {
        axios.delete(route('tasks.destroy.bulk', { ids: Object.values(selectedTasks.value) }))
            .then(() => {
                window.location.reload();
            })
    }
}

</script>

<template>
    <Head title="Task" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">Search</h2>
        </template>

        <div class="bg-white dark:bg-gray-800 overflow-hidden">
            <div class="flex bg-gray-50 dark:bg-gray-950">
                <div
                    :class="{
                        'cursor-pointer text-base font-medium p-2 text-center text-gray-600 border-b-2 border-indigo-400 dark:border-indigo-600 text-indigo-800 dark:text-indigo-300 bg-indigo-50 dark:bg-indigo-900/50 focus:outline-none focus:text-indigo-800 dark:focus:text-indigo-200 focus:bg-indigo-100 dark:focus:bg-indigo-900 focus:border-indigo-700 dark:focus:border-indigo-300 transition duration-150 ease-in-out':
                            activeTab === 'searchedTerm',
                        'cursor-pointer text-base font-medium p-2 text-center text-gray-600 border-b-2 border-gray-50 dark:border-gray-950':
                            activeTab !== 'searchedTerm'
                    }"
                    @click="activeTab = 'searchedTerm'"
                >
                    Searched term "{{ searchedTerm }}"
                </div>
                <div
                    class="grow grid justify-end items-center"
                >

                    <div class="relative">
                        <Dropdown align="taskContext" width="48">
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
                                <span class="block w-full px-4 py-2 text-left text-sm leading-5 text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-800 focus:outline-none focus:bg-gray-100 dark:focus:bg-gray-800 transition duration-150 ease-in-out cursor-pointer" @click.prevent="deleteBulkTasks()"> Delete tasks </span>
                            </template>
                        </Dropdown>
                    </div>
                </div>
            </div>
            <div class="p-4">
                <div v-if="activeTab === 'searchedTerm'">
                    <h2 class="text-lg font-semibold mb-2">Users</h2>
                    <div class="flex flex-wrap gap-6 mb-2">
                        <h3 v-if="searchedUsers.length == 0" class="text-md font-bold grow text-center">No matching users.</h3>
                        <div v-for="user in searchedUsers" :key="user._id" v-tooltip="user.name">
                            <Link class="inline-block border-4 rounded-full border-white text-white flex items-center justify-center text-lg font-bold cursor-pointer  dark:text-gray-800 hover:text-gray-100 dark:hover:text-gray-600 hover:border-gray-300 dark:hover:border-gray-600 focus:outline-none focus:text-gray-800 dark:focus:text-gray-200 focus:bg-gray-50 dark:focus:bg-gray-700 focus:border-gray-300 dark:focus:border-gray-600 transition duration-150 ease-in-out uppercase" :style="{ 'aspectRatio': '1/1', 'width': '48px', 'backgroundColor': stringToColour(user.name) }" :href="route('users.show', user._id)">
                                {{ user.name.charAt(0) }}
                            </Link>
                        </div>
                    </div>
                    <h2 class="text-lg font-semibold mb-2">Projects</h2>
                        <div class="flex flex-wrap gap-6 mb-2">
                            <h3 v-if="searchedProjects.length == 0" class="text-md font-bold grow text-center">No matching projects, <Link :href="route('projects.create')" class="text-blue-500 hover:text-blue-700 ">create one</Link>!</h3>
                        <div v-for="project in searchedProjects" :key="project._id" v-tooltip="project.name">
                            <Link class="inline-block border-4 rounded-full border-white text-white flex items-center justify-center text-lg font-bold cursor-pointer  dark:text-gray-800 hover:text-gray-100 dark:hover:text-gray-600 hover:border-gray-300 dark:hover:border-gray-600 focus:outline-none focus:text-gray-800 dark:focus:text-gray-200 focus:bg-gray-50 dark:focus:bg-gray-700 focus:border-gray-300 dark:focus:border-gray-600 transition duration-150 ease-in-out uppercase" :style="{ 'aspectRatio': '1/1', 'width': '48px', 'backgroundColor': stringToColour(project.name) }" :href="route('projects.show', project._id)">
                                {{ project.name.charAt(0) }}
                            </Link>
                        </div>
                    </div>
                    <h2 class="text-lg font-semibold mb-2">Tasks</h2>
                    <div class="flex flex-col gap-2">
                        <div v-for="(project, project_id) in searchedTasks" :key="project_id" class="p-2">
                            <ProjectTask :project="project" :selectedTasks="selectedTasks"/>
                        </div>
                        <h3 v-if="searchedTasks.length == 0" class="text-md font-bold grow text-center">No matching tasks, <Link :href="route('tasks.create')" class="text-blue-500 hover:text-blue-700 ">create one</Link>!</h3>
                    </div>
                </div>
            </div>
        </div>

    </AuthenticatedLayout>
</template>
