<template>
    <div ref="templateRef" class="flex flex-col bg-white dark:bg-gray-800 overflow-hidden shadow-sm rounded-lg p-6 gap-6">
        <div class="text-xl font-light text-gray-500 flex flex-row justify-between">
            <span>
                {{ title }}
            </span>
        </div>
        <div class="overflow-y-auto max-h-[230px] custom-scroll">
            <table class="table-fixed min-w-full" :style="{
                width: '100%',
            }">
                <thead>
                    <tr>
                        <th class="text-center sticky top-0 bg-white dark:bg-gray-800">Task</th>
                        <th class="text-center sticky top-0 bg-white dark:bg-gray-800">Assingee</th>
                        <th class="text-center sticky top-0 bg-white dark:bg-gray-800">Due Date</th>
                        <th class="text-center sticky top-0 bg-white dark:bg-gray-800">Priority</th>
                    </tr>
                </thead>
                <tbody>
                    <TransitionGroup name="list">
                        <tr v-for="task in tasks" :key="task._id" class="cursor-pointer hover:bg-slate-200 h-[40px]"
                            @click="eventBus.$emit('updateTaskModal', { task: task });"
                        >
                            <td class="w-[20%] pl-2.5" :style="{ 'overflow': 'hidden', 'text-overflow': 'ellipsis', 'white-space': 'nowrap' }" v-tooltip="task.title">{{ task.title }}</td>
                            <td>
                                <span class="flex justify-center">
                                    <div class="inline-block rounded-full h-[24px] w-[24px] text-white flex items-center justify-center text-sm font-bold uppercase" :style="{ backgroundColor: stringToColour(users[task.assigned_to]?.name) }" v-tooltip="users[task.assigned_to]?.name">
                                        {{ users[task.assigned_to]?.name.charAt(0) }}
                                    </div>
                                </span>
                            </td>
                            <td class="text-center">{{ new Date(
                                parseInt(task?.due_date?.$date.$numberLong)
                            ).toLocaleDateString('en-CA', { timeZone: "UTC" }) }}</td>
                            <td v-tooltip="task?.priority">
                                <span class="flex justify-center">
                                    <Icon 
                                        :icon="PriorityDictionary[task?.priority]?.icon" 
                                        class="mx-1"
                                        :class="PriorityDictionary[task?.priority]?.color"               
                                    />    
                                </span>
                            </td>
                        </tr>
                    </TransitionGroup>
                </tbody>
            </table>
        </div>
    </div>
</template>
<script setup>
import { ref } from 'vue';
import { stringToColour } from '@/Utils/globalFunctions';
import { Icon } from '@iconify/vue';
import eventBus from '@/Utils/eventBus';


const templateRef = ref(null);

const props = defineProps({
    title: String,
    tasks: Array,
    users: Object,
});

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

</script>
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
.list-enter-active,
.list-leave-active {
  transition: all 0.5s ease;
}
.list-enter-from,
.list-leave-to {
  opacity: 0;
}

</style>
