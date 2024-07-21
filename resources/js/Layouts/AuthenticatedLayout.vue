<!--
The AuthenticatedLayout.vue file is a Vue.js component that represents the layout of the authenticated pages of the application. It contains a navigation bar, a settings dropdown, an overlay, and the main content of the page.

Props:
- None

Slots:
- header: the content of the header section of the page.

Events:
- None

Usage:
<AuthenticatedLayout>
    <template #header>
        <h1>Page Title</h1>
    </template>
    <div>
        Page Content
    </div>
</AuthenticatedLayout>
-->
<script setup>
import { ref, onMounted } from 'vue';
import ApplicationLogo from '@/Components/ApplicationLogo.vue';
import Dropdown from '@/Components/Dropdown.vue';
import DropdownLink from '@/Components/DropdownLink.vue';
import ResponsiveNavLink from '@/Components/ResponsiveNavLink.vue';
import ProjectNavCollapsible from '@/Components/ProjectNavCollapsible.vue';
import PatientNavCollapsible from '@/Components/PatientNavCollapsible.vue';
import UserNavCollapsible from '@/Components/UserNavCollapsible.vue';
import SearchForm from '@/Layouts/Partials/SearchForm.vue';
import { Link, usePage } from '@inertiajs/vue3';
import eventBus from '@/Utils/eventBus';
import { stringToColour } from '@/Utils/globalFunctions';
import Modal from '@/Components/Modal.vue';
import TaskForm from '@/Pages/Tasks/Partials/TaskForm.vue';
import CommentsForm from '@/Pages/Tasks/Partials/CommentsForm.vue';
import { useToast } from 'vue-toastification';
import ClickableToast from '@/Components/ClickableToast.vue';

//function that verify if screen is mobile
function isMobile() {
    return window.innerWidth < 768;
}
// ref  if slim navigation is active
const slimNavigation = ref(isMobile());
const modalTask = ref(undefined);
const modalProject = ref(undefined);
const modalPatient = ref(undefined);
const modalComment = ref(undefined);
const modalCommentTaskId = ref(undefined);
const modalCommentTaskTitle = ref(undefined);
const modalType = ref(undefined);
const toast = useToast();

onMounted(() => {
    eventBus.$on('topPatientsUpdate', (content) => {
        topPatients.value = content.value;
    });
    eventBus.$on('topProjectsUpdate', (content) => {
        topProjects.value = content.value;
    });
    eventBus.$on('topUsersUpdate', (content) => {
        topUsers.value = content.value;
    });
    eventBus.$on('newTaskModal', (content) => {
        modalTask.value = content.task;
        modalProject.value = content.project;
        modalType.value = 'task';
        showModal.value = true;
    });
    eventBus.$on('updateTaskModal', (content) => {
        modalTask.value = content.task;
        modalType.value = 'task';
        showModal.value = true;
    });
    eventBus.$on('commentModal', (content) => {
        modalComment.value = content.task;
        modalCommentTaskId.value = content.task._id;
        modalCommentTaskTitle.value = content.task.title;
        modalType.value = 'comment';
        showModal.value = true;
    });
});
const $page = usePage();
const topProjects = ref($page.props.topProjects);
const topPatients = ref($page.props.topPatients);
const topUsers = ref($page.props.topUsers);

const whiteRoutes = ['tasks', 'projects.show', 'patients.show', 'users.show', 'search.show']

const squad_id = $page.props.auth.user.squad_id;
const user_id = $page.props.auth.user._id;

Echo.leaveAllChannels();

const channel1 = Echo.private('squad.' + squad_id);
channel1.listen('.user-event', function (data) {
    eventBus.$emit('userUpdate', data);
    if (data.type == 'update') {
        let index = topUsers.value.findIndex((user) => user._id == data.user._id);
        if (index != -1) {
            topUsers.value[index] = data.user;
        }
        topProjects.value.forEach((project) => {
            if (project.user._id == data.user._id) {
                project.user = data.user;
            }
        });
    }
});
channel1.listen('.project-event', function (data) {
    eventBus.$emit('projectUpdate', data);
    if (data.type == 'update') {
        let index = topProjects.value.findIndex((project) => project._id == data.project._id);
        if (index != -1) {
            topProjects.value[index] = data.project;
        }
    }
});
channel1.listen('.patient-event', function (data) {
    eventBus.$emit('patientUpdate', data);
    if (data.type == 'update') {
        let index = topPatients.value.findIndex((patient) => patient._id == data.patient._id);
        if (index != -1) {
            topPatients.value[index] = data.patient;
        }
    }
});
channel1.listen('.task-event', function (data) {
    eventBus.$emit('taskUpdate', data);
});
channel1.listen('.comment-event', function (data) {
    if (data.message.user_id != user_id){
        toast.info(
            {
                component: ClickableToast,
                props: {
                    message: 'You have a new comment on task "' + data.message.task_title + '" click here to see it',
                    func: () => {
                        modalComment.value = data.message;
                        modalCommentTaskId.value = data.message.task_id;
                        modalCommentTaskTitle.value = data.message.task_title;
                        modalType.value = 'comment';
                        showModal.value = true;
                    },
                },
            },
            {
                position: "bottom-left",
                timeout: 10000,
                closeOnClick: true,
                pauseOnFocusLoss: true,
                pauseOnHover: true,
                draggable: true,
                draggablePercent: 0.6,
                showCloseButtonOnHover: true,
                hideProgressBar: false,
                closeButton: "button",
                icon: true,
                rtl: false
            }
        );
    }
    if (modalType.value == 'comment' && modalComment.value._id == data.message.task_id) {
        modalComment.value.comments = data.message.comments;
    }
    eventBus.$emit('commentUpdate', data);
});

const channel2 = Echo.private('user.' + user_id);
channel2.listen('.task-event', function (data) {
    eventBus.$emit('taskUpdate', data);
});
channel2.listen('.comment-event', function (data) {
    if (data.message.user_id != user_id) {
        toast.info(
            {
                component: ClickableToast,
                props: {
                    message: 'You have a new comment on task "' + data.message.task_title + '" click here to see it',
                    func: () => {
                        modalComment.value = data.message;
                        modalComment.value._id = data.message.task_id;
                        modalCommentTaskId.value = data.message.task_id;
                        modalCommentTaskTitle.value = data.message.task_title;
                        modalType.value = 'comment';
                        showModal.value = true;
                    },
                },
            },
            {
                position: "bottom-left",
                timeout: 10000,
                closeOnClick: true,
                pauseOnFocusLoss: true,
                pauseOnHover: true,
                draggable: true,
                draggablePercent: 0.6,
                showCloseButtonOnHover: true,
                hideProgressBar: false,
                closeButton: "button",
                icon: true,
                rtl: false
            }
        );
    }
    if (modalType.value == 'comment' && modalComment.value._id == data.message.task_id) {
        modalComment.value.comments = data.message.comments;
    }
    eventBus.$emit('commentUpdate', data);
});

const showModal = ref(false);

</script>

<template>
    <Modal
        :show="showModal"
        @close="showModal = false"
    >
        <TaskForm
            v-if="modalType == 'task'"
            :task="modalTask"
            :project="modalProject"
        />
        <CommentsForm
            v-if="modalType == 'comment'"
            :taskId="modalCommentTaskId"
            :taskTitle="modalCommentTaskTitle"
            :comments="modalComment.comments"
        />
    </Modal>
    <div>
        <div class="flex h-full">
            <!-- Side Menu -->
            <div :class="{
                'transition-all bg-white dark:bg-gray-800 h-screen fixed': true,
                'w-0 -left-64': slimNavigation && isMobile(),
                'w-12': slimNavigation && !isMobile(),
                'z-20': !slimNavigation && isMobile(),
                'w-64': !slimNavigation
            }">
                <div class="flex flex-col justify-between h-screen shadow">
                    <div class="grow custom-scroll"
                        :style="{
                            'overflow-y': 'overlay',
                        }"
                    >
                        <!-- Logo -->
                        <div class="flex flex-col items-center justify-center h-32 sticky top-0 bg-white dark:bg-gray-800 z-10 py-4 gap-4">

                            <!-- Hamburger -->
                            <Link :href="route('dashboard')" class="flex flex-grow items-center justify-center">
                                <ApplicationLogo
                                    class="block h-9 w-auto fill-current text-gray-800 dark:text-gray-200"
                                />
                            </Link>
                            <SearchForm :class="{
                                'transition-all': true,
                                'opacity-0': slimNavigation && !isMobile(),
                                'opacity-100': !slimNavigation || isMobile(),
                            }"></SearchForm>
                        </div>

                        <!-- Navigation Links -->
                        <div class="py-4 space-y-1 flex flex-col">
                            <ResponsiveNavLink :href="route('dashboard')" :active="route().current('dashboard')" class="flex items-center" v-tooltip="'Dashboard'">
                                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 1024 1024" :class="{'mr-2': !slimNavigation, 'transition-all': true}"><path fill="currentColor" d="M924.8 385.6a446.7 446.7 0 0 0-96-142.4a446.7 446.7 0 0 0-142.4-96C631.1 123.8 572.5 112 512 112s-119.1 11.8-174.4 35.2a446.7 446.7 0 0 0-142.4 96a446.7 446.7 0 0 0-96 142.4C75.8 440.9 64 499.5 64 560c0 132.7 58.3 257.7 159.9 343.1l1.7 1.4c5.8 4.8 13.1 7.5 20.6 7.5h531.7c7.5 0 14.8-2.7 20.6-7.5l1.7-1.4C901.7 817.7 960 692.7 960 560c0-60.5-11.9-119.1-35.2-174.4zM761.4 836H262.6A371.12 371.12 0 0 1 140 560c0-99.4 38.7-192.8 109-263c70.3-70.3 163.7-109 263-109c99.4 0 192.8 38.7 263 109c70.3 70.3 109 163.7 109 263c0 105.6-44.5 205.5-122.6 276zM623.5 421.5a8.03 8.03 0 0 0-11.3 0L527.7 506c-18.7-5-39.4-.2-54.1 14.5a55.95 55.95 0 0 0 0 79.2a55.95 55.95 0 0 0 79.2 0a55.87 55.87 0 0 0 14.5-54.1l84.5-84.5c3.1-3.1 3.1-8.2 0-11.3l-28.3-28.3zM490 320h44c4.4 0 8-3.6 8-8v-80c0-4.4-3.6-8-8-8h-44c-4.4 0-8 3.6-8 8v80c0 4.4 3.6 8 8 8zm260 218v44c0 4.4 3.6 8 8 8h80c4.4 0 8-3.6 8-8v-44c0-4.4-3.6-8-8-8h-80c-4.4 0-8 3.6-8 8zm12.7-197.2l-31.1-31.1a8.03 8.03 0 0 0-11.3 0l-56.6 56.6a8.03 8.03 0 0 0 0 11.3l31.1 31.1c3.1 3.1 8.2 3.1 11.3 0l56.6-56.6c3.1-3.1 3.1-8.2 0-11.3zm-458.6-31.1a8.03 8.03 0 0 0-11.3 0l-31.1 31.1a8.03 8.03 0 0 0 0 11.3l56.6 56.6c3.1 3.1 8.2 3.1 11.3 0l31.1-31.1c3.1-3.1 3.1-8.2 0-11.3l-56.6-56.6zM262 530h-80c-4.4 0-8 3.6-8 8v44c0 4.4 3.6 8 8 8h80c4.4 0 8-3.6 8-8v-44c0-4.4-3.6-8-8-8z"/></svg>
                                <span :class="{
                                    'ml-2': !slimNavigation,
                                    'hidden': slimNavigation,
                                    'transition-all': true
                                }">Dashboard</span>
                            </ResponsiveNavLink>
                            <ResponsiveNavLink :href="route('tasks')" :active="route().current('tasks')" class="flex items-center" v-tooltip="'Tasks'">
                                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" :class="{'mr-2': !slimNavigation, 'transition-all': true}"><path fill="currentColor" d="M22 5.18L10.59 16.6l-4.24-4.24l1.41-1.41l2.83 2.83l10-10L22 5.18zm-2.21 5.04c.13.57.21 1.17.21 1.78c0 4.42-3.58 8-8 8s-8-3.58-8-8s3.58-8 8-8c1.58 0 3.04.46 4.28 1.25l1.44-1.44A9.9 9.9 0 0 0 12 2C6.48 2 2 6.48 2 12s4.48 10 10 10s10-4.48 10-10c0-1.19-.22-2.33-.6-3.39l-1.61 1.61z"/></svg>
                                <span :class="{
                                    'ml-2': !slimNavigation,
                                    'hidden': slimNavigation,
                                    'transition-all': true
                                }">Tasks</span>
                            </ResponsiveNavLink>
                            <ProjectNavCollapsible :projects='topProjects' class="" :slimNavigation="slimNavigation" :isMobile="isMobile()">
                                <template #trigger>
                                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 1024 1024" :class="{ 'mr-2': !slimNavigation, 'transition-all': true }"><path fill="currentColor" d="M280 752h80c4.4 0 8-3.6 8-8V280c0-4.4-3.6-8-8-8h-80c-4.4 0-8 3.6-8 8v464c0 4.4 3.6 8 8 8zm192-280h80c4.4 0 8-3.6 8-8V280c0-4.4-3.6-8-8-8h-80c-4.4 0-8 3.6-8 8v184c0 4.4 3.6 8 8 8zm192 72h80c4.4 0 8-3.6 8-8V280c0-4.4-3.6-8-8-8h-80c-4.4 0-8 3.6-8 8v256c0 4.4 3.6 8 8 8zm216-432H144c-17.7 0-32 14.3-32 32v736c0 17.7 14.3 32 32 32h736c17.7 0 32-14.3 32-32V144c0-17.7-14.3-32-32-32zm-40 728H184V184h656v656z"/></svg>
                                    <span :class="{
                                        'ml-2': !slimNavigation,
                                        'hidden': slimNavigation,
                                        'transition-all': true
                                    }">Projects</span>
                                </template>
                            </ProjectNavCollapsible>
                            <UserNavCollapsible :users='topUsers' class="" :slimNavigation="slimNavigation" :isMobile="isMobile()">
                                <template #trigger>
                                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" :class="{ 'mr-2': !slimNavigation, 'transition-all': true }"><path fill="currentColor" d="M16 11c1.66 0 2.99-1.34 2.99-3S17.66 5 16 5c-1.66 0-3 1.34-3 3s1.34 3 3 3zm-8 0c1.66 0 2.99-1.34 2.99-3S9.66 5 8 5C6.34 5 5 6.34 5 8s1.34 3 3 3zm0 2c-2.33 0-7 1.17-7 3.5V19h14v-2.5c0-2.33-4.67-3.5-7-3.5zm8 0c-.29 0-.62.02-.97.05c1.16.84 1.97 1.97 1.97 3.45V19h6v-2.5c0-2.33-4.67-3.5-7-3.5z"/></svg>
                                    <span :class="{
                                        'ml-2': !slimNavigation,
                                        'hidden': slimNavigation,
                                        'transition-all': true
                                    }">Users</span>
                                </template>
                            </UserNavCollapsible>
                            <PatientNavCollapsible :patients='topPatients' class="" :slimNavigation="slimNavigation" :isMobile="isMobile()">
                                <template #trigger>
                                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" :class="{ 'mr-2': !slimNavigation, 'transition-all': true }"><path fill="currentColor" d="M16 11c1.66 0 2.99-1.34 2.99-3S17.66 5 16 5c-1.66 0-3 1.34-3 3s1.34 3 3 3zm-8 0c1.66 0 2.99-1.34 2.99-3S9.66 5 8 5C6.34 5 5 6.34 5 8s1.34 3 3 3zm0 2c-2.33 0-7 1.17-7 3.5V19h14v-2.5c0-2.33-4.67-3.5-7-3.5zm8 0c-.29 0-.62.02-.97.05c1.16.84 1.97 1.97 1.97 3.45V19h6v-2.5c0-2.33-4.67-3.5-7-3.5z"/></svg>
                                    <span :class="{
                                        'ml-2': !slimNavigation,
                                        'hidden': slimNavigation,
                                        'transition-all': true
                                    }">Patients</span>
                                </template>
                            </PatientNavCollapsible>
                        </div>
                    </div>
                    <div>
                        <!-- Settings Dropdown -->
                        <div class="flex items-center justify-center">
                            <!-- Settings Dropdown -->
                            <div class="relative">
                                <Dropdown align="bla" width="48">
                                    <template #trigger>
                                        <div class="cursor-pointer flex flex-row items-center justify-center space-x-4 p-2 border-t-2">
                                            <div class="inline-block rounded-full h-8 w-8 text-white flex items-center justify-center text-xl font-bold uppercase" :style="{ backgroundColor: stringToColour($page.props.auth.user.name) }">
                                                {{ $page.props.auth.user.name.charAt(0) }}
                                            </div>
                                            <div :class="{
                                                'flex flex-col space-y-0.5 transition-all': true,
                                                'hidden': slimNavigation
                                            }">
                                                <span class="text-base font-medium text-gray-600">
                                                    {{ $page.props.auth.user.name }}
                                                </span>
                                                <span class="text-sm font-light text-gray-500">
                                                    {{ $page.props.auth.user.email }}
                                                </span>
                                            </div>
                                        </div>
                                    </template>

                                    <template #content>
                                        <span class="block w-full px-4 text-center">
                                            Hi {{ $page.props.auth.user.name }}!
                                        </span>
                                        <DropdownLink :href="route('profile.edit')"> Profile </DropdownLink>
                                        <DropdownLink :href="route('logout')" method="post" as="button">
                                            Log Out
                                        </DropdownLink>
                                    </template>
                                </Dropdown>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- Overlay -->
            <div :class="{
                'transition-all fixed inset-0 bg-black': true,
                'opacity-25 z-10': !slimNavigation && isMobile(),
                'opacity-0 -z-10': slimNavigation && isMobile(),
                'hidden': !isMobile()
             }" @click="slimNavigation = !slimNavigation"></div>
            <!-- Main Content -->
            <div :class="{
                'transition-all flex-1 h-screen overflow-y-auto': true,
                'bg-gray-100 dark:bg-gray-900': !whiteRoutes.includes(route().current()),
                'bg-white dark:bg-gray-800': whiteRoutes.includes(route().current()),
                'ml-12': slimNavigation && !isMobile(),
                'ml-64': !slimNavigation && !isMobile(),
            }">
            <!-- Page Heading -->
                <header class="bg-white dark:bg-gray-800 shadow flex place-items-center" v-if="$slots.header">
                    <button
                        @click="slimNavigation = !slimNavigation"
                        class="inline-flex items-center justify-center p-2 ml-4 rounded-md text-gray-400 dark:text-gray-500 hover:text-gray-500 dark:hover:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-900 focus:outline-none focus:bg-gray-100 dark:focus:bg-gray-900 focus:text-gray-500 dark:focus:text-gray-400 transition duration-150 ease-in-out"
                    >
                        <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                            <path
                                class="inline-flex"
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                stroke-width="2"
                                fill="currentColor"
                                d="M 4 6 h 16 M 4 12 h 16 M 4 18 h 16"
                            />
                        </svg>
                    </button>
                    <div class="max-w-7xl mr-auto py-6 px-4 sm:px-6 lg:px-8">
                        <slot name="header" />
                    </div>
                </header>

                <!-- Page Content -->
                <main>
                    <slot />
                </main>
            </div>
        </div>
    </div>
</template>

<style scoped>

.custom-scroll::-webkit-scrollbar {
    width: 8px;
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

</style>
