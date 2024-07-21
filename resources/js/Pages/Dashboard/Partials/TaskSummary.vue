/**
 * A component that displays a summary of a task, including its icon, title, and completion status.
 *
 * @props {String} icon - The name of the icon to display.
 * @props {String} title - The title of the task.
 * @props {String} color - The background color of the summary box.
 * @props {Number} completed - The number of completed subtasks.
 * @props {Number} pending - The number of pending subtasks.
 */
<template>
    <div class="flex bg-white dark:bg-gray-800 overflow-hidden shadow-sm rounded-lg cursor-pointer transition-all"
        :class="{
            'ring': isSelected,
        }"
    >
        <div class="p-3 text-white text-sm font-light flex flex-col items-center justify-center aspect-square w-28"
            :style="{
                backgroundColor: color,
            }"
        >
            <Icon :icon="icon" width="24" height="24" />
            <span class="text-center">
                {{ title }}
            </span>
        </div>
        <div class="grow grid grid-cols-2"
        :style="{
            color: color,
        }"
        >
            <div class="flex flex-col items-center justify-center">
                <transition
                name="list"
                >
                    <span class="text-2xl">{{ completed }}</span>
                </transition>
                <span class="text-sm">Completed</span>
            </div>
            <div class="flex flex-col items-center justify-center">
                <transition
                name="list"
                >
                    <span class="text-2xl">{{ pending }}</span>
                </transition>
                <span class="text-sm">Pending</span>
            </div>
        </div>
    </div>
</template>
<script setup>
import { Icon } from '@iconify/vue';
const props = defineProps({
    icon: String,
    title: String,
    color: String,
    completed: Number,
    pending: Number,
    isSelected: Boolean,
});
</script>
<style scoped>
.list-enter-active,
.list-leave-active {
  transition: all 0.5s ease;
}
.list-enter-from,
.list-leave-to {
  opacity: 0;
}
</style>