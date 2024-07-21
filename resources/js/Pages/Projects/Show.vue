<!--
    COMPONENT: Show
    DESCRIPTION: This component displays the details of a project, including its name, description, and tasks. It also allows the user to delete multiple tasks at once.
    PROPS:
        - project: The project object containing the project's details and tasks.
    DATA:
        - activeTab: The currently active tab (either 'projectTasks' or 'other').
        - selectedTasks: An array of selected tasks to be deleted.
        - allTasks: An array of all tasks in the project.
        - displayMenu: A boolean indicating whether or not to display the dropdown menu.
    METHODS:
        - displayMenuFunc(): A function that returns a boolean indicating whether or not to display the dropdown menu.
        - deleteBulkTasks(): A function that deletes the selected tasks.
    EVENTS:
        - taskCheckbox: An event that is emitted when a task checkbox is clicked.
        - projectUpdate: An event that is emitted when the project is updated or deleted.
-->
<script setup>
//view for one project showing (tasks) 
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head } from '@inertiajs/vue3';
import { usePage } from '@inertiajs/vue3';
import { ref , onMounted} from 'vue';
import { Icon } from '@iconify/vue';
import ProjectTask from '../Tasks/Partials/ProjectTask.vue';
import eventBus from '@/Utils/eventBus';
import Dropdown from '@/Components/Dropdown.vue';
import axios from 'axios';

const project = ref(usePage().props.project);

const activeTab = ref('projectTasks');

const selectedTasks = ref([]);
const allTasks = [...project.value.tasks].flat().filter((task, index, self) => index === self.findIndex((t) => t._id === task._id));

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
    eventBus.$on('projectUpdate', (data) => {
        switch (data.type) {
            case 'update':
                if (project.value._id === data.project._id) {
                    project.value.name = data.project.name;
                    project.value.description = data.project.description;
                    Object.keys(project.value.tasks).forEach((key) => {
                        if (project.value.tasks[key].project) {
                            project.value.tasks[key].project.name = data.project.name;
                        }
                    });
                }  
                break;
            case 'delete':
                window.location.href = route('projects');
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
    <Head title="Projects" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">Project {{ project.name }}</h2>
        </template>
        
        <div class="bg-white dark:bg-gray-800 overflow-hidden">
            <div class="flex bg-gray-50 dark:bg-gray-950">
                <div
                    :class="{
                        'cursor-pointer text-base font-medium p-2 text-center text-gray-600 border-b-2 border-indigo-400 dark:border-indigo-600 text-indigo-800 dark:text-indigo-300 bg-indigo-50 dark:bg-indigo-900/50 focus:outline-none focus:text-indigo-800 dark:focus:text-indigo-200 focus:bg-indigo-100 dark:focus:bg-indigo-900 focus:border-indigo-700 dark:focus:border-indigo-300 transition duration-150 ease-in-out':
                            activeTab === 'projectTasks',
                        'cursor-pointer text-base font-medium p-2 text-center text-gray-600 border-b-2 border-gray-50 dark:border-gray-950':
                            activeTab !== 'projectTasks'
                    }"
                    @click="activeTab = 'projectTasks'"
                >
                    Project Tasks
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
                <div v-if="activeTab === 'projectTasks'">
                    <h2 class="text-lg font-semibold mb-2">Tasks</h2>
                    <div class="flex flex-col gap-2">
                        <div class="p-2">
                            <ProjectTask :project="project.tasks" :selectedTasks="selectedTasks" :projectName="project.name" :projectId="project._id"/>
                        </div>
                    </div>
                </div>
                <div v-else-if="activeTab === 'other'">
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>