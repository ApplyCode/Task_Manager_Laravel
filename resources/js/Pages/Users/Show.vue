<!--
    This component is responsible for displaying the tasks and projects of a user.
    It contains tabs for different types of tasks (free, assigned, team) and a tab for projects.
    The tasks are displayed using the ProjectTask component.
    The projects are displayed using the Link component.
    The component also includes a dropdown menu for bulk task deletion.
-->
<script setup>
//view for one user showing (tasks) 
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head } from '@inertiajs/vue3';
import { usePage, Link } from '@inertiajs/vue3';
import { ref, onMounted } from 'vue';
import { Icon } from '@iconify/vue';
import ProjectTask from '../Tasks/Partials/ProjectTask.vue';
import eventBus from '@/Utils/eventBus';
import Dropdown from '@/Components/Dropdown.vue';
import { stringToColour } from '@/Utils/globalFunctions';
import axios from 'axios';

const user = ref(usePage().props.user);
const usersTasks = ref(usePage().props.usersTasks);
const usersProjectTasks = ref(usePage().props.usersProjectTasks);
const assignedTasks = ref(usePage().props.assignedTasks);
const assignedProjectTasks = ref(usePage().props.assignedProjectTasks);
const teamTasks = ref(usePage().props.teamTasks);
const teamProjectTasks = ref(usePage().props.teamProjectTasks);
const projects = ref(usePage().props.projects);

const activeTab = ref('projects');

const selectedTasks = ref([]);
const allTasks = [...usersTasks.value, ...assignedTasks.value, ...teamTasks.value, ...Object.values(usersProjectTasks.value), ...Object.values(assignedProjectTasks.value), ...Object.values(teamProjectTasks.value)].flat().filter((task, index, self) => index === self.findIndex((t) => t._id === task._id));

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
        if (user.value._id === data.user._id) {
            switch (data.type) {
                case 'update':
                    user.value = data.user;
                    break;
                case 'delete':
                    window.location.href = route('users');
                    break;
                default:
                    break;
            }
        }        
    });
    eventBus.$on('projectUpdate', (data) => {
        if (projects.value.some((project) => project._id === data.project._id)) {
            switch (data.type) {
                case 'update':
                    projects.value[projects.value.findIndex((project) => project._id === data.project._id)] = data.project;
                    Object.values(usersProjectTasks.value).forEach((project) => {
                        project.forEach((task) => {
                            if (task.project_id === data.project._id) {
                                task.project = data.project;
                            }
                        });
                    });
                    Object.values(assignedProjectTasks.value).forEach((project) => {
                        project.forEach((task) => {
                            if (task.project_id === data.project._id) {
                                task.project = data.project;
                            }
                        });
                    }); 
                    Object.values(teamProjectTasks.value).forEach((project) => {
                        project.forEach((task) => {
                            if (task.project_id === data.project._id) {
                                task.project = data.project;
                            }
                        });
                    });
                    break;
                case 'delete':
                    projects.value.splice(projects.value.findIndex((project) => project._id === data.project._id), 1);
                    break;
                default:
                    break;
            }
        } 
        if (data.type === 'create' && data.project.user_id === user.value._id) {
            projects.value.push(data.project);
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
    <Head title="Users" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">User {{ user.name }}</h2>
        </template>

        <div class="bg-white dark:bg-gray-800 overflow-hidden">
            <div class="flex bg-gray-50 dark:bg-gray-950">
                <div
                    :class="{
                        'cursor-pointer text-base font-medium p-2 text-center text-gray-600 border-b-2 border-indigo-400 dark:border-indigo-600 text-indigo-800 dark:text-indigo-300 bg-indigo-50 dark:bg-indigo-900/50 focus:outline-none focus:text-indigo-800 dark:focus:text-indigo-200 focus:bg-indigo-100 dark:focus:bg-indigo-900 focus:border-indigo-700 dark:focus:border-indigo-300 transition duration-150 ease-in-out':
                            activeTab === 'projects',
                        'cursor-pointer text-base font-medium p-2 text-center text-gray-600 border-b-2 border-gray-50 dark:border-gray-950':
                            activeTab !== 'projects'
                    }"
                    @click="activeTab = 'projects'"
                >
                    User's Projects
                </div>
                <div
                    :class="{
                        'cursor-pointer text-base font-medium p-2 text-center text-gray-600 border-b-2 border-indigo-400 dark:border-indigo-600 text-indigo-800 dark:text-indigo-300 bg-indigo-50 dark:bg-indigo-900/50 focus:outline-none focus:text-indigo-800 dark:focus:text-indigo-200 focus:bg-indigo-100 dark:focus:bg-indigo-900 focus:border-indigo-700 dark:focus:border-indigo-300 transition duration-150 ease-in-out':
                            activeTab === 'usersTasks',
                        'cursor-pointer text-base font-medium p-2 text-center text-gray-600 border-b-2 border-gray-50 dark:border-gray-950':
                            activeTab !== 'usersTasks'
                    }"
                    @click="activeTab = 'usersTasks'"
                >
                    User's Tasks
                </div>
                <div
                    :class="{
                        'cursor-pointer text-base font-medium p-2 text-center text-gray-600 border-b-2 border-indigo-400 dark:border-indigo-600 text-indigo-800 dark:text-indigo-300 bg-indigo-50 dark:bg-indigo-900/50 focus:outline-none focus:text-indigo-800 dark:focus:text-indigo-200 focus:bg-indigo-100 dark:focus:bg-indigo-900 focus:border-indigo-700 dark:focus:border-indigo-300 transition duration-150 ease-in-out':
                            activeTab === 'assignedTasks',
                        'cursor-pointer text-base font-medium p-2 text-center text-gray-600 border-b-2 border-gray-50 dark:border-gray-950':
                            activeTab !== 'assignedTasks'
                    }"
                    @click="activeTab = 'assignedTasks'"
                >
                    Assigned Tasks
                </div>
                <div
                    :class="{
                        'cursor-pointer text-base font-medium p-2 text-center text-gray-600 border-b-2 border-indigo-400 dark:border-indigo-600 text-indigo-800 dark:text-indigo-300 bg-indigo-50 dark:bg-indigo-900/50 focus:outline-none focus:text-indigo-800 dark:focus:text-indigo-200 focus:bg-indigo-100 dark:focus:bg-indigo-900 focus:border-indigo-700 dark:focus:border-indigo-300 transition duration-150 ease-in-out':
                            activeTab === 'teamTasks',
                        'cursor-pointer text-base font-medium p-2 text-center text-gray-600 border-b-2 border-gray-50 dark:border-gray-950':
                            activeTab !== 'teamTasks'
                    }"
                    @click="activeTab = 'teamTasks'"
                >
                    Team Tasks
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
                <div v-if="activeTab === 'usersTasks'">
                    <h2 class="text-lg font-semibold mb-2">Free Tasks</h2>
                    <div class="flex flex-col gap-2">
                        <div class="p-2">
                            <ProjectTask :project="usersTasks" :selectedTasks="selectedTasks"/>
                        </div>
                    </div>
                    <h2 class="text-lg font-semibold mb-2">Project Tasks</h2>
                    <div class="flex flex-col gap-2">
                        <div v-for="(project, project_id) in usersProjectTasks" :key="project_id" class="p-2">
                            <ProjectTask :project="project" :selectedTasks="selectedTasks"/>
                        </div>
                    </div>
                </div>
                <div v-else-if="activeTab === 'assignedTasks'">
                    <h2 class="text-lg font-semibold mb-2">Free Tasks</h2>
                    <div class="flex flex-col gap-2">
                        <div class="p-2">
                            <ProjectTask :project="assignedTasks" :selectedTasks="selectedTasks"/>
                        </div>
                    </div>
                    <h2 class="text-lg font-semibold mb-2">Project Tasks</h2>
                    <div class="flex flex-col gap-2">
                        <div v-for="(project, project_id) in assignedProjectTasks" :key="project_id" class="p-2">
                            <ProjectTask :project="project" :selectedTasks="selectedTasks"/>
                        </div>
                    </div>
                </div>
                <div v-else-if="activeTab === 'teamTasks'">
                    <h2 class="text-lg font-semibold mb-2">Free Tasks</h2>
                    <div class="flex flex-col gap-2">
                        <div class="p-2">
                            <ProjectTask :project="teamTasks" :selectedTasks="selectedTasks"/>
                        </div>
                    </div>
                    <h2 class="text-lg font-semibold mb-2">Project Tasks</h2>
                    <div class="flex flex-col gap-2">
                        <div v-for="(project, project_id) in teamProjectTasks" :key="project_id" class="p-2">
                            <ProjectTask :project="project" :selectedTasks="selectedTasks"/>
                        </div>
                    </div>
                </div>
                <div v-else-if="activeTab === 'projects'">
                    <h2 class="text-lg font-semibold mb-2">Projects</h2>
                    <div class="flex flex-wrap gap-6 mb-2">
                        <h3 v-if="projects.length == 0" class="text-md font-bold grow text-center">No matching projects.</h3>
                        <div v-for="project in projects" :key="project._id" v-tooltip="project.name">
                            <Link class="inline-block border-4 rounded-full border-white text-white flex items-center justify-center text-lg font-bold cursor-pointer  dark:text-gray-800 hover:text-gray-100 dark:hover:text-gray-600 hover:border-gray-300 dark:hover:border-gray-600 focus:outline-none focus:text-gray-800 dark:focus:text-gray-200 focus:bg-gray-50 dark:focus:bg-gray-700 focus:border-gray-300 dark:focus:border-gray-600 transition-all duration-150 ease-in-out uppercase" :style="{ 'aspectRatio': '1/1', 'width': '48px', 'backgroundColor': stringToColour(project.name) }" :href="route('projects.show', project._id)">
                                {{ project.name.charAt(0) }}
                            </Link>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
