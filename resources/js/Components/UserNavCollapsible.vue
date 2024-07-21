<script setup>
import { ref } from 'vue';
import ResponsiveNavLink from '@/Components/ResponsiveNavLink.vue';
import NavAvatarLink from '@/Components/NavAvatarLink.vue';
import { stringToColour } from '@/Utils/globalFunctions';

const props = defineProps(['users', 'slimNavigation', 'isMobile']);

const isActive = ref(localStorage.getItem('navUsersCollapse') === 'true');

const toggleCollapse = () => {
    isActive.value = !isActive.value;
    localStorage.setItem('navUsersCollapse', isActive.value);
};
</script>

<template>
    <div>
        <div @click="toggleCollapse"
            class="cursor-pointer flex w-full pl-3 pr-4 py-2 border-l-4 border-transparent text-left text-base font-medium text-gray-600 dark:text-gray-400 hover:text-gray-800 dark:hover:text-gray-200 hover:bg-gray-50 dark:hover:bg-gray-700 hover:border-gray-300 dark:hover:border-gray-600 focus:outline-none focus:text-gray-800 dark:focus:text-gray-200 focus:bg-gray-50 dark:focus:bg-gray-700 focus:border-gray-300 dark:focus:border-gray-600 transition duration-150 ease-in-out">
            <slot name="trigger" />
            <span :class="{
                'grow flex justify-end': true,
                'hidden': slimNavigation,
                'transition-all': true
            }">
                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" :class="{
                    'transition-all mr-2': true,
                    'transform -rotate-180': isActive,
                }"><path fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m7 10l5 5m0 0l5-5"/></svg>
            </span>
        </div>
        <transition
                enter-active-class="transition ease-out duration-200"
                enter-from-class="transform opacity-0 scale-95"
                enter-to-class="transform opacity-100 scale-100"
                leave-active-class="transition ease-in duration-75"
                leave-from-class="transform opacity-100 scale-100"
                leave-to-class="transform opacity-0 scale-95"
        >
            <div v-if="isActive" :class="{
                'transition-all': true,
                'pl-3 pr-4': !slimNavigation,
                'px-3': slimNavigation,
            }">
                <ul>
                    <li>
                        <div class="flex grow flex-wrap py-4"
                            :class="{
                                'transition-all': true,
                                'px-4 gap-4': !slimNavigation,
                                'justify-center gap-8': slimNavigation,
                                'hidden': props.users.length === 0
                            }"
                        >
                            <TransitionGroup name="list">
                                <NavAvatarLink v-for="(user, index) in props.users" :key="index" :href="route('users.show', user._id)" :active="route().current('users.show', user._id)" v-tooltip="'User '+ user.name">
                                    <div :class="{
                                        'transition-all inline-block text-white flex items-center justify-center text-xs font-bold rounded-full': true,
                                        'h-[6.33px] w-[6.33px]': slimNavigation,
                                        'h-6 w-6': !slimNavigation
                                    }" :style="{ backgroundColor: stringToColour(user.name) }">
                                        <span :class="{
                                            'transition-all uppercase': true,
                                            'hidden': slimNavigation
                                        }">
                                            {{ user.name.charAt(0) }}
                                        </span>
                                    </div>
                                </NavAvatarLink>
                            </TransitionGroup>
                        </div>
                    </li>
                    <li>
                        <ResponsiveNavLink :href="route('users')" :active="route().current('users')" class="flex items-center" v-tooltip="'All the users'">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 48 48" :class="{
                                'transition-all': true,
                                'mr-2': !slimNavigation,
                                '-mr-4 -ml-3': slimNavigation,
                            }"><path fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="4" d="M42 24H6m24-12l12 12l-12 12"/></svg>
                            <span :class="{
                                'hidden': slimNavigation,
                                'transition-all': true
                            }"
                            :style="{ 'overflow': 'hidden', 'text-overflow': 'ellipsis', 'white-space': 'nowrap' }"
                            >All the users</span>
                        </ResponsiveNavLink>
                    </li>
                </ul>
            </div>
        </transition>
    </div>
</template>
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
