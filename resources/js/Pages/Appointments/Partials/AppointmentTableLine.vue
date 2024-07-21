<!--
The following code represents a Vue component that displays a single row of a task table. It receives the following props:
- item: an object representing the task to be displayed
- projectColor: a string representing the color of the project associated with the task
- projectTextColor: a string representing the text color of the project associated with the task
- selectedTasks: an array of strings representing the IDs of the selected tasks
- users: an object containing information about the users associated with the task

The component displays the following information about the task:
- Task name
- Task status
- Timeline component displaying the start and due dates of the task
- Percentage completed
- Priority level
- Team members associated with the task
- Labels associated with the task

The component also emits the following events:
- taskCheckbox: emitted when the user clicks on the priority level checkbox
- updateTaskModal: emitted when the user clicks on the task name or timeline component
- commentModal: emitted when the user clicks on the comment icon
-->
<template>
    <td style="background-color: white;"
    :style="{
        color: projectTextColor,
    }"
    >
        <Icon 
            :icon="checkbox ? 'mdi:checkbox-marked' : 'mdi:checkbox-blank-outline'" 
            :class="{
                'transition-all cursor-pointer mx-1': true,
            }"
            @click="toggleCheckbox" 
        />
    </td>
    <td class=" text-sm font-medium text-gray-900 dark:text-gray-100 p-0"
        :style="{
            borderBottom: '2px solid white',
            borderRight: '2px solid white',
        }"
    >
        <div class="flex items-center cursor-pointer" @click="updateTaskModal()">
            <div class="w-[4px] h-[36px]" :style="{
                backgroundColor: projectColor,
            }"></div>
            <Icon class="m-3" :icon="publicc ? 'material-symbols:lock-open-right-outline' : 'material-symbols:lock-outline'" v-tooltip="publicc ? 'Public' : 'Private'"/>
            <div class="pr-3 py-2 w-[300px]" :style="{ 'overflow': 'hidden', 'text-overflow': 'ellipsis', 'white-space': 'nowrap'}" v-tooltip="props.item.title">
                {{ props.item.title }}
            </div>
        </div>
    </td>
    <td class=" text-sm font-medium text-gray-900 dark:text-gray-100 p-0"
        :style="{
            borderBottom: '2px solid white',
            borderRight: '2px solid white',
        }"
    >
        <div class="flex items-center justify-center cursor-pointer" @click="commentModal()">
            <div class="relative">
                <div v-if="props.item.comments.length > 0"  class="w-[14px] h-[14px] aspect-square rounded-full bg-red-500 flex justify-center items-center text-white text-[8px] absolute top-0.5 right-0.5" >
                    {{ props.item.comments.length < 10 ? props.item.comments.length : '9+' }}
                </div>
                <Icon class="m-2" icon="mdi:comment-outline" width="18" height="18"/>
            </div>
        </div>
    </td>
    <td class=" text-sm font-medium text-gray-900 dark:text-gray-100 p-0"
        :style="{
            borderBottom: '2px solid white',
            borderRight: '2px solid white',
        }"
    >
        <div class="flex w-[100px] cursor-pointer" @click="updateTaskModal()">
            <div class="px-3 py-2 w-[100px]" :style="{ 'overflow': 'hidden', 'text-overflow': 'ellipsis', 'white-space': 'nowrap'}" v-tooltip="props.item.user.name">
                {{ props.item.user.name }}
            </div>
        </div>
    </td>
    <td class=" text-sm font-medium text-gray-900 dark:text-gray-100 p-0"
        :style="{
            borderBottom: '2px solid white',
            borderRight: '2px solid white',
        }"
    >
        <div class="flex w-[100px] cursor-pointer" @click="updateTaskModal()">
            <div class="px-3 py-2 w-[100px]" :style="{ 'overflow': 'hidden', 'text-overflow': 'ellipsis', 'white-space': 'nowrap' }" v-tooltip="props.users[props.item.assigned_to]?.name ?? 'Unassigned'">
                {{ props.users[props.item.assigned_to]?.name ?? 'Unassigned' }}
            </div>
        </div>
    </td>
    <td class=" text-sm font-medium text-white dark:text-white p-0 transition-all"
        :class="statusColorDictionary[props.item.status]"
        :style="{
            borderBottom: '2px solid white',
            borderRight: '2px solid white',
        }"
    >
        <div class="flex w-[100px] cursor-pointer" @click="updateTaskModal()">
            <div class="px-3 py-2 w-[100px]" :style="{ 'overflow': 'hidden', 'text-overflow': 'ellipsis', 'white-space': 'nowrap' }" v-tooltip="props.item.status">
                {{ props.item.status }}
            </div>
        </div>
    </td>
    <td class=" text-sm font-medium text-gray-900 dark:text-gray-100 p-0"
        :style="{
            borderBottom: '2px solid white',
            borderRight: '2px solid white',
        }"
    >
        <div class="flex w-[120px] cursor-pointer" @click="updateTaskModal()">
            <div class="w-[120px] " >
                <Timeline v-show="(props.item.start_date !== undefined) && (props.item.due_date !== undefined) " :startDate="props.item.start_date" :dueDate="props.item.due_date" :projectColor="projectColor" :status="props.item.status"/>
            </div>
        </div>
    </td>
    <td class=" text-sm font-medium text-gray-900 dark:text-gray-100 p-0"
        :style="{
            borderBottom: '2px solid white',
            borderRight: '2px solid white',
        }"
    >
        <div class="flex w-[140px] cursor-pointer" @click="updateTaskModal()">
            <div class="w-[140px] " >
                <PercCompleted v-show="props.item.task_progress !== undefined" :progress="props.item.task_progress" :projectColor="projectColor"/>
            </div>
        </div>
    </td>
    <td class=" text-sm font-medium text-gray-900 dark:text-gray-100 p-0"
        :style="{
            borderBottom: '2px solid white',
            borderRight: '2px solid white',
        }"
        v-tooltip="props.item.priority"
    >
        <div class="flex w-[120px] justify-center cursor-pointer" @click="updateTaskModal()">
            <Icon 
                :icon="PriorityDictionary[props.item.priority]?.icon" 
                class="mx-1"
                :class="PriorityDictionary[props.item.priority]?.color"               
            />
        </div>
    </td>
    <td class=" text-sm font-medium text-gray-900 dark:text-gray-100 p-0 min-w-[120px]"
        :style="{
            borderBottom: '2px solid white',
            borderRight: '2px solid white',
        }"
    >
        <div v-show="props.item.team" class="flex justify-center gap-2 px-2 cursor-pointer" @click="updateTaskModal()">
                <div v-for="user_id in props.item.team" :key="user_id" class="inline-block rounded-full h-[24px] w-[24px] text-white flex items-center justify-center text-sm font-bold uppercase" :style="{ backgroundColor: stringToColour(props.users[user_id]?.name) }" v-tooltip="props.users[user_id]?.name">
                    {{ props.users[user_id]?.name.charAt(0) }}
                </div>
        </div>
    </td>
    <td class=" text-sm font-medium text-gray-900 dark:text-gray-100 p-0 min-w-[120px]"
        :style="{
            borderBottom: '2px solid white',
            borderRight: '2px solid white',
        }"
    >
        <div v-show="props.item.labels" class="flex justify-center gap-2 px-2 cursor-pointer" @click="updateTaskModal()">
            <div v-for="(label, index) in props.item.labels" :key="index" 
                class="inline-block rounded-full h-[24px] text-white flex items-center justify-center text-xs font-bold px-3 py-1" 
                :style="{ backgroundColor: stringToColour(label) }"
                v-tooltip="label"
            >
                <span :style="{ whiteSpace: 'nowrap' }">
                    {{ label }}
                </span>
            </div>
        </div>
    </td>
</template>

<script setup>
import { Icon } from '@iconify/vue';
import { ref } from 'vue';
import Timeline from '@/Components/Timeline.vue';
import PercCompleted from '@/Components/PercCompleted.vue';
import { stringToColour } from '@/Utils/globalFunctions';
import eventBus from '@/Utils/eventBus';

const props = defineProps({
    item: {
        type: Object,
        required: true
    },
    projectColor: {
        type: String,
        required: true
    },
    projectTextColor: {
        type: String,
        required: true
    },
    selectedTasks: {
        type: Array,
        required: true
    },
    users: {
        type: Object,
        required: true
    },
});

const checkbox = ref(props.selectedTasks.includes(props.item._id));

const publicc = ref(props.item.public);

const toggleCheckbox = () => {
    checkbox.value = !checkbox.value;
    eventBus.$emit('taskCheckbox', props.item._id);
};

const statusColorDictionary = {
    'Cancelled': 'bg-gray-400',
    'Not Started': 'bg-red-400',
    'In Progress': 'bg-yellow-400',
    'On Hold': 'bg-blue-400',
    'Completed': 'bg-green-400',
};

const PriorityDictionary = {
    'None': {
        'color': 'text-gray-400',
        'icon': 'mdi:checkbox-blank-circle-outline',
    },
    'Lowest': {
        'color': 'text-green-400',
        'icon': 'mdi:chevron-triple-down',
    },
    'Low': {
        'color': 'text-green-400',
        'icon': 'mdi:chevron-double-down',
    },
    'Medium': {
        'color': 'text-yellow-400',
        'icon': 'mdi:equal',
    },
    'High': {
        'color': 'text-red-400',
        'icon': 'mdi:chevron-double-up',
    },
    'Highest': {
        'color': 'text-red-400',
        'icon': 'mdi:chevron-triple-up',
    },
};

function updateTaskModal() {
    eventBus.$emit('updateTaskModal', {task:props.item});
}

function commentModal() {
    eventBus.$emit('commentModal', {task:props.item});
}
</script>
