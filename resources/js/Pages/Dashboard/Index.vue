<!--
The Index.vue component is the main dashboard page of the application. It displays a summary of the user's tasks, assigned tasks, and team tasks. 

Props:
- status: A string representing the status of the tasks to be displayed.

Usage:
<Index :status="'completed'" />

Dependencies:
- AuthenticatedLayout.vue: A layout component for authenticated pages.
- TaskSummary.vue: A component for displaying a summary of tasks.
- @inertiajs/vue3: A package for using Inertia.js with Vue 3.
-->
<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import TaskSummary from '@/Pages/Dashboard/Partials/TaskSummary.vue';
import { Head, usePage } from '@inertiajs/vue3';
import { computed, onMounted, ref } from 'vue';
import TasksPerTime from './Partials/TasksPerTime.vue';
import eventBus from '@/Utils/eventBus';
import PendingHighPriorityTasks from './Partials/PendingHighPriorityTasks.vue';
import TasksPercentage from './Partials/TasksPercentage.vue';
import TasksPriority from './Partials/TasksPriority.vue';
import { stringToColour } from '@/Utils/globalFunctions';
import html2canvas from 'html2canvas'; 
import { Icon } from '@iconify/vue';

const props = defineProps({
    tasks: Array,
});

const dashboardRef = ref(null);

const selectedType = ref([]);
const allTasks = ref(props.tasks);

const $page = usePage();
const usersDictionary = ref(Object.fromEntries($page.props.allUsers.map(user => [user._id, user])));

const myCompletedTasks = computed(() => allTasks.value.filter(task => task.task_progress === 100 && task.user_id === $page.props.auth.user._id));
const myPendingTasks = computed(() => allTasks.value.filter(task => task.task_progress !== 100 && task.user_id === $page.props.auth.user._id));
const assignedCompletedTasks = computed(() => allTasks.value.filter(task => task.task_progress === 100 && task.assigned_to === $page.props.auth.user._id));
const assignedPendingTasks = computed(() => allTasks.value.filter(task => task.task_progress !== 100 && task.assigned_to === $page.props.auth.user._id));
const teamCompletedTasks = computed(() => allTasks.value.filter(task => task.task_progress === 100 && task.team.includes($page.props.auth.user._id)));
const teamPendingTasks = computed(() => allTasks.value.filter(task => task.task_progress !== 100 && task.team.includes($page.props.auth.user._id)));
const publicCompletedTasks = computed(() => allTasks.value.filter(task => task.task_progress === 100 && task.public));
const publicPendingTasks = computed(() => allTasks.value.filter(task => task.task_progress !== 100 && task.public));

const TasksPercentageDataSet = computed(()=>{
    const datasets = [];
    if (selectedType.value.length === 0) {
        datasets.push({
            label: 'My tasks',
            backgroundColor: '#60a5fa',
            data: myPendingTasks.value.map(task => ({ x: task.title, y: task.task_progress })),
        });
        datasets.push({
            label: 'Assigned tasks',
            backgroundColor: '#f87171',
            data: assignedPendingTasks.value.map(task => ({ x: task.title, y: task.task_progress })),
        });
        datasets.push({
            label: 'Team tasks',
            backgroundColor: '#34d399',
            data: teamPendingTasks.value.map(task => ({ x: task.title, y: task.task_progress })),
        });
        datasets.push({
            label: 'Public tasks',
            backgroundColor: '#fbbf24',
            data: publicPendingTasks.value.map(task => ({ x: task.title, y: task.task_progress })),
        });
    } else {
        if (selectedType.value.includes('myTasks')) {
            datasets.push({
                label: 'My tasks',
                backgroundColor: '#60a5fa',
                data: myPendingTasks.value.map(task => ({ x: task.title, y: task.task_progress })),
            });
        }
        if (selectedType.value.includes('assignedTasks')) {
            datasets.push({
                label: 'Assigned tasks',
                backgroundColor: '#f87171',
                data: assignedPendingTasks.value.map(task => ({ x: task.title, y: task.task_progress })),
            });
        }
        if (selectedType.value.includes('teamTasks')) {
            datasets.push({
                label: 'Team tasks',
                backgroundColor: '#34d399',
                data: teamPendingTasks.value.map(task => ({ x: task.title, y: task.task_progress })),
            });
        }
        if (selectedType.value.includes('publicTasks')) {
            datasets.push({
                label: 'Public tasks',
                backgroundColor: '#fbbf24',
                data: publicPendingTasks.value.map(task => ({ x: task.title, y: task.task_progress })),
            });
        }
    }
    return datasets;
})

const TasksPerTimeLabels = computed(() =>{
    const labels = [];
    const today = new Date();
    const threeMonthsAgoSunday = new Date(today.getFullYear(), today.getMonth() - 3, today.getDate() - today.getDay());
    const oneWeek = 1000 * 60 * 60 * 24 * 7;
    const oneDay = 1000 * 60 * 60 * 24;
    let date = threeMonthsAgoSunday;
    while (date < today) {
        const nextDate = new Date(date.getTime() + oneWeek);
        labels.push(`${date.toLocaleString('default', { month: 'short' })} ${date.getDate()} - ${new Date(nextDate.getTime() - oneDay).toLocaleString('default', { month: 'short' })} ${new Date(nextDate.getTime() - oneDay).getDate()}`);
        date = nextDate;
    }
    return labels;

})

const TasksPerTimeDataSet = computed(() => {
    const datasets = [];
    if (selectedType.value.includes('myTasks')) {
        datasets.push({
            label: 'My tasks',
            borderColor: '#60a5fa',
            backgroundColor: '#60a5fa',
            fill: false,
            data: getDataAlongTimeDataSet(myCompletedTasks.value),
            tension: 0.4,
        });
    }
    if (selectedType.value.includes('assignedTasks')) {
        datasets.push({
            label: 'Assigned tasks',
            borderColor: '#f87171',
            backgroundColor: '#f87171',
            fill: false,
            data: getDataAlongTimeDataSet(assignedCompletedTasks.value),
            tension: 0.4,
        });
    }
    if (selectedType.value.includes('teamTasks')) {
        datasets.push({
            label: 'Team tasks',
            borderColor: '#34d399',
            backgroundColor: '#34d399',
            fill: false,
            data: getDataAlongTimeDataSet(teamCompletedTasks.value),
            tension: 0.4,
        });
    }
    if (selectedType.value.includes('publicTasks')) {
        datasets.push({
            label: 'Public tasks',
            borderColor: '#fbbf24',
            backgroundColor: '#fbbf24',
            fill: false,
            data: getDataAlongTimeDataSet(publicCompletedTasks.value),
            tension: 0.4,
        });
    }
    if (selectedType.value.length === 0) {
        datasets.push({
            label: 'My tasks',
            borderColor: '#60a5fa',
            backgroundColor: '#60a5fa',
            fill: false,
            data: getDataAlongTimeDataSet(myCompletedTasks.value),
            tension: 0.4,
        });
        datasets.push({
            label: 'Assigned tasks',
            borderColor: '#f87171',
            backgroundColor: '#f87171',
            fill: false,
            data: getDataAlongTimeDataSet(assignedCompletedTasks.value),
            tension: 0.4,
        });
        datasets.push({
            label: 'Team tasks',
            borderColor: '#34d399',
            backgroundColor: '#34d399',
            fill: false,
            data: getDataAlongTimeDataSet(teamCompletedTasks.value),
            tension: 0.4,
        });
        datasets.push({
            label: 'Public tasks',
            borderColor: '#fbbf24',
            backgroundColor: '#fbbf24',
            fill: false,
            data: getDataAlongTimeDataSet(publicCompletedTasks.value),
            tension: 0.4,
        });
    }

    return datasets;
})


const getDataAlongTimeDataSet = (tasksArray) => {
    const data = [];
    const today = new Date();
    const threeMonthsAgoSunday = new Date(today.getFullYear(), today.getMonth() - 3, today.getDate() - today.getDay());
    const oneWeek = 1000 * 60 * 60 * 24 * 7;
    let date = threeMonthsAgoSunday;
    while (date < today) {
        const nextDate = new Date(date.getTime() + oneWeek);
        const tasksCompleted = tasksArray.filter(
            (task) => {
                console.log("🚀 ~ getDataAlongTimeDataSet ~ task.completed_at:", task)
                return new Date(parseInt(task.completed_at.$date.$numberLong)) >= date && new Date(parseInt(task.completed_at.$date.$numberLong)) < nextDate
            }
        );
        data.push(tasksCompleted.length);
        date = nextDate;
    }   
    return data;
};

const priorityDictionary = {
    'None': 0,
    'Lowest': 1,
    'Low': 2,
    'Medium': 3,
    'High': 4,
    'Highest': 5,
};

const pendingTasksForThisWeek = computed(() => {
    const today = new Date();
    const sunday = new Date(today.getFullYear(), today.getMonth(), today.getDate() - today.getDay());
    const nextSunday = new Date(sunday.getTime() + 1000 * 60 * 60 * 24 * 7);
    const tasks = [];
    if (selectedType.value.length === 0) {
        tasks.push(...myPendingTasks.value.filter(task => new Date(parseInt(task?.due_date?.$date?.$numberLong)) >= sunday && new Date(parseInt(task?.due_date?.$date?.$numberLong)) < nextSunday));
        tasks.push(...assignedPendingTasks.value.filter(task => new Date(parseInt(task?.due_date?.$date?.$numberLong)) >= sunday && new Date(parseInt(task?.due_date?.$date?.$numberLong)) < nextSunday));
        tasks.push(...teamPendingTasks.value.filter(task => new Date(parseInt(task?.due_date?.$date?.$numberLong)) >= sunday && new Date(parseInt(task?.due_date?.$date?.$numberLong)) < nextSunday));
        tasks.push(...publicPendingTasks.value.filter(task => new Date(parseInt(task?.due_date?.$date?.$numberLong)) >= sunday && new Date(parseInt(task?.due_date?.$date?.$numberLong)) < nextSunday));
    } else {
        if (selectedType.value.includes('myTasks')) {
            tasks.push(...myPendingTasks.value.filter(task => new Date(parseInt(task?.due_date?.$date?.$numberLong)) >= sunday && new Date(parseInt(task?.due_date?.$date?.$numberLong)) < nextSunday));
        }
        if (selectedType.value.includes('assignedTasks')) {
            tasks.push(...assignedPendingTasks.value.filter(task => new Date(parseInt(task?.due_date?.$date?.$numberLong)) >= sunday && new Date(parseInt(task?.due_date?.$date?.$numberLong)) < nextSunday));
        }
        if (selectedType.value.includes('teamTasks')) {
            tasks.push(...teamPendingTasks.value.filter(task => new Date(parseInt(task?.due_date?.$date?.$numberLong)) >= sunday && new Date(parseInt(task?.due_date?.$date?.$numberLong)) < nextSunday));
        }
        if (selectedType.value.includes('publicTasks')) {
            tasks.push(...publicPendingTasks.value.filter(task => new Date(parseInt(task?.due_date?.$date?.$numberLong)) >= sunday && new Date(parseInt(task?.due_date?.$date?.$numberLong)) < nextSunday));
        }
    }
    const uniqueTasks = [];
    tasks.forEach(task => {
        if (!uniqueTasks.find(t => t._id === task._id)) {
            uniqueTasks.push(task);
        }
    });
    uniqueTasks.sort((a, b) => {
        if (priorityDictionary[a.priority] > priorityDictionary[b.priority]) {
            return -1;
        }
        if (priorityDictionary[a.priority] < priorityDictionary[b.priority]) {
            return 1;
        }
        return 0;
    });
    return uniqueTasks;
})
const overdueTasks = computed(() => {
    const today = new Date();
    const tasks = [];
    if (selectedType.value.length === 0) {
        tasks.push(...myPendingTasks.value.filter(task => new Date(parseInt(task?.due_date?.$date?.$numberLong)) < today && task?.progress !== 100));
        tasks.push(...assignedPendingTasks.value.filter(task => new Date(parseInt(task?.due_date?.$date?.$numberLong)) < today && task?.progress !== 100));
        tasks.push(...teamPendingTasks.value.filter(task => new Date(parseInt(task?.due_date?.$date?.$numberLong)) < today && task?.progress !== 100));
        tasks.push(...publicPendingTasks.value.filter(task => new Date(parseInt(task?.due_date?.$date?.$numberLong)) < today && task?.progress !== 100));
    } else {
        if (selectedType.value.includes('myTasks')) {
            tasks.push(...myPendingTasks.value.filter(task => new Date(parseInt(task?.due_date?.$date?.$numberLong)) < today && task?.progress !== 100));
        }
        if (selectedType.value.includes('assignedTasks')) {
            tasks.push(...assignedPendingTasks.value.filter(task => new Date(parseInt(task?.due_date?.$date?.$numberLong)) < today && task?.progress !== 100));
        }
        if (selectedType.value.includes('teamTasks')) {
            tasks.push(...teamPendingTasks.value.filter(task => new Date(parseInt(task?.due_date?.$date?.$numberLong)) < today && task?.progress !== 100));
        }
        if (selectedType.value.includes('publicTasks')) {
            tasks.push(...publicPendingTasks.value.filter(task => new Date(parseInt(task?.due_date?.$date?.$numberLong)) < today && task?.progress !== 100));
        }
    }
    const uniqueTasks = [];
    tasks.forEach(task => {
        if (!uniqueTasks.find(t => t._id === task._id)) {
            uniqueTasks.push(task);
        }
    });
    uniqueTasks.sort((a, b) => {
        if (priorityDictionary[a.priority] > priorityDictionary[b.priority]) {
            return -1;
        }
        if (priorityDictionary[a.priority] < priorityDictionary[b.priority]) {
            return 1;
        }
        return 0;
    });
    return uniqueTasks;
})

const futureTasks = computed(() => {
    const today = new Date();
    const tasks = [];
    if (selectedType.value.length === 0) {
        tasks.push(...myPendingTasks.value.filter(task => new Date(parseInt(task?.due_date?.$date?.$numberLong)) > today && task?.progress !== 100));
        tasks.push(...assignedPendingTasks.value.filter(task => new Date(parseInt(task?.due_date?.$date?.$numberLong)) > today && task?.progress !== 100));
        tasks.push(...teamPendingTasks.value.filter(task => new Date(parseInt(task?.due_date?.$date?.$numberLong)) > today && task?.progress !== 100));
        tasks.push(...publicPendingTasks.value.filter(task => new Date(parseInt(task?.due_date?.$date?.$numberLong)) > today && task?.progress !== 100));
    } else {
        if (selectedType.value.includes('myTasks')) {
            tasks.push(...myPendingTasks.value.filter(task => new Date(parseInt(task?.due_date?.$date?.$numberLong)) > today && task?.progress !== 100));
        }
        if (selectedType.value.includes('assignedTasks')) {
            tasks.push(...assignedPendingTasks.value.filter(task => new Date(parseInt(task?.due_date?.$date?.$numberLong)) > today && task?.progress !== 100));
        }
        if (selectedType.value.includes('teamTasks')) {
            tasks.push(...teamPendingTasks.value.filter(task => new Date(parseInt(task?.due_date?.$date?.$numberLong)) > today && task?.progress !== 100));
        }
        if (selectedType.value.includes('publicTasks')) {
            tasks.push(...publicPendingTasks.value.filter(task => new Date(parseInt(task?.due_date?.$date?.$numberLong)) > today && task?.progress !== 100));
        }
    }
    const uniqueTasks = [];
    tasks.forEach(task => {
        if (!uniqueTasks.find(t => t._id === task._id)) {
            uniqueTasks.push(task);
        }
    });
    uniqueTasks.sort((a, b) => {
        if (priorityDictionary[a.priority] > priorityDictionary[b.priority]) {
            return -1;
        }
        if (priorityDictionary[a.priority] < priorityDictionary[b.priority]) {
            return 1;
        }
        return 0;
    });
    return uniqueTasks;
})

onMounted(() => {
    eventBus.$on('userUpdate', (data) => {
        switch (data.type) {
            case 'update':
                allTasks.value = allTasks.value.map(task => {
                    if (task.user_id === data.user._id) {
                        task.user = data.user;
                    }
                    return task;
                });                
            case 'create':
                usersDictionary.value[data.user._id] = data.user;
                break;        
            case 'delete':
                delete usersDictionary.value[data.user._id];
                allTasks.value = allTasks.value.filter(task => task.user_id !== data.user._id);
                break;
        }
    });
    eventBus.$on('projectUpdate', (data) => {
        switch (data.type) {
            case 'update':
                allTasks.value = allTasks.value.map(task => {
                    if (task.project_id === data.project._id) {
                        task.project = data.project;
                    }
                    return task;
                });
                break;  
            case 'delete':
                allTasks.value = allTasks.value.filter(task => task.project_id !== data.project._id);
                break;
        }
    });
    eventBus.$on('taskUpdate', (data) => {
        switch (data.type) {
            case 'update':
                allTasks.value = allTasks.value.map(task => {
                    if (task._id === data.task._id) {
                        task = data.task;
                    }
                    return task;
                });
                break;
            case 'create':
                allTasks.value.push(data.task);               
                break;        
            case 'delete':
                allTasks.value = allTasks.value.filter(task => task._id !== data.task._id);
                break;
        }
    });
});

const toogleFromSelectedType = (type) => {
    if (selectedType.value.includes(type)) {
        selectedType.value = selectedType.value.filter(t => t !== type);
    } else {
        selectedType.value.push(type);
    }
}

const PriorityArray = [
    'None',
    'Lowest',
    'Low',
    'Medium',
    'High',
    'Highest',
];

const priorityColors = [
    // scale from green to red in 6 steps
    'rgba(128, 128, 128, 0.2)',
    'rgba(0, 255, 0, 0.2)',
    'rgba(0, 255, 0, 0.4)',
    'rgba(0, 255, 0, 0.6)',
    'rgba(255, 0, 0, 0.6)',
    'rgba(255, 0, 0, 0.8)',
]

const TasksPriorityPerUserDataSet = computed(() => {
    const data = {
        labels: [],
        datasets: [
            {
                label: 'None',
                data: [],
                backgroundColor: [],
            },
            {
                label: 'Lowest',
                data: [],
                backgroundColor: [],
            },
            {
                label: 'Low',
                data: [],
                backgroundColor: [],
            },
            {
                label: 'Medium',
                data: [],
                backgroundColor: [],
            },
            {
                label: 'High',
                data: [],
                backgroundColor: [],
            },
            {
                label: 'Highest',
                data: [],
                backgroundColor: [],
            },
        ]
    };
    const tasks = [];
    if (selectedType.value.length === 0) {
        tasks.push(...allTasks.value);
    } else {
        if (selectedType.value.includes('myTasks')) {
            tasks.push(...myPendingTasks.value);
        }
        if (selectedType.value.includes('assignedTasks')) {
            tasks.push(...assignedPendingTasks.value);
        }
        if (selectedType.value.includes('teamTasks')) {
            tasks.push(...teamPendingTasks.value);
        }
        if (selectedType.value.includes('publicTasks')) {
            tasks.push(...publicPendingTasks.value);
        }
    }
    // remove duplicates
    const uniqueTasks = [];
    tasks.forEach(task => {
        if (!uniqueTasks.find(t => t._id === task._id)) {
            uniqueTasks.push(task);
        }
    });
    const users = [];
    uniqueTasks.forEach(task => {
        if (!users.includes(task.assigned_to)) {
            users.push(task.assigned_to);
        }
    });
    users.forEach(user => {
        const userTasks = uniqueTasks.filter(task => task.assigned_to === user);
        const userTasksPriority = [];
        userTasks.forEach(task => {
            userTasksPriority.push(task.priority || 0);
        });
        data.labels.push(usersDictionary.value[user]?.name);
        data.datasets[0].data.push(userTasksPriority.filter(priority => priority === PriorityArray[0]).length || 0);
        data.datasets[1].data.push(userTasksPriority.filter(priority => priority === PriorityArray[1]).length || 0);
        data.datasets[2].data.push(userTasksPriority.filter(priority => priority === PriorityArray[2]).length || 0);
        data.datasets[3].data.push(userTasksPriority.filter(priority => priority === PriorityArray[3]).length || 0);
        data.datasets[4].data.push(userTasksPriority.filter(priority => priority === PriorityArray[4]).length || 0);
        data.datasets[5].data.push(userTasksPriority.filter(priority => priority === PriorityArray[5]).length || 0);
        data.datasets.forEach((dataset, index) => {
            dataset.backgroundColor.push(stringToColour(usersDictionary.value[user]?.name || 'none'));
        });
    });
    return data;
});

const downloadTemplate = () => {
    html2canvas(dashboardRef.value).then(canvas => {
        const url = canvas.toDataURL('image/png');
        const link = document.createElement('a');
        link.href = url;
        link.download = 'dashboard.png';
        link.click();
    });
}

</script>

<template>
    <Head title="Dashboard" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">Dashboard</h2>
        </template>

        <div class="py-6 ">
            <div ref="dashboardRef" class="mx-auto px-6 lg:px-8 gap-6 grid bg-gray-100 dark:bg-gray-900">
                <div>
                    <div class="text-lg font-medium text-gray-600 flex justify-between">
                        <span>
                            Welcome back, {{ $page.props.auth.user.name }}!
                        </span>
                        <span>
                            <Icon @click="downloadTemplate" icon="mingcute:download-line" width="24" height="24" class="cursor-pointer mx-2"/>
                        </span>
                    </div>
                    <div class="text-base font-light text-gray-500">
                        Squad ID: {{ $page.props.auth.user.squad_id }}
                    </div>
                </div>
                <div class="grid gap-6 sm:grid-cols-2 lg:grid-cols-4">
                    <TaskSummary
                        icon="fluent:task-list-square-ltr-20-regular"
                        title="My tasks"
                        color="#60a5fa"
                        :completed="myCompletedTasks.length"
                        :pending="myPendingTasks.length" 
                        :isSelected="selectedType.includes('myTasks')"
                        @click="toogleFromSelectedType('myTasks')"
                    />
                    <TaskSummary
                        icon="fluent:task-list-square-person-20-regular"
                        title="Assigned tasks"
                        color="#f87171"
                        :completed="assignedCompletedTasks.length"
                        :pending="assignedPendingTasks.length"
                        :isSelected="selectedType.includes('assignedTasks')"
                        @click="toogleFromSelectedType('assignedTasks')"
                    />
                    <TaskSummary
                        icon="fluent:task-list-square-database-20-regular"
                        title="Team tasks"
                        color="#34d399"
                        :completed="teamCompletedTasks.length"
                        :pending="teamPendingTasks.length"
                        :isSelected="selectedType.includes('teamTasks')"
                        @click="toogleFromSelectedType('teamTasks')"
                    />
                    <TaskSummary
                        icon="fluent:task-list-square-add-20-regular"
                        title="Public tasks"
                        color="#fbbf24"
                        :completed="publicCompletedTasks.length"
                        :pending="publicPendingTasks.length"
                        :isSelected="selectedType.includes('publicTasks')"
                        @click="toogleFromSelectedType('publicTasks')"
                    />
                </div>
                <div class="grid gap-6 sm:grid-cols-1 lg:grid-cols-2">
                    <div class="gap-6 grid sm:grid-rows-1 lg:grid-rows-3">
                        <TasksPerTime :data-labels="TasksPerTimeLabels" :data-sets="TasksPerTimeDataSet" title="Completed tasks per week"/>
                        <TasksPercentage :data-sets="TasksPercentageDataSet" title="Pending tasks percentage"/>
                        <TasksPriority :data-sets="TasksPriorityPerUserDataSet" title="Tasks assignee per priority"/>
                    </div>
                    <div class="gap-6 grid sm:grid-rows-1 lg:grid-rows-3">
                        <PendingHighPriorityTasks title="Pending tasks for the week" :tasks="pendingTasksForThisWeek" :users="usersDictionary"/>
                        <PendingHighPriorityTasks title="Overdue tasks" :tasks="overdueTasks" :users="usersDictionary"/>
                        <PendingHighPriorityTasks title="Future tasks" :tasks="futureTasks" :users="usersDictionary"/>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
