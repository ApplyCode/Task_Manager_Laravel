<!--
This component displays a table of all users. It includes a search bar to filter users by name, a star icon to toggle user favorites, and a button to invite new users. Each user is displayed in a card with their name and an "Actions" dropdown menu that links to their profile page. If there are no matching users, a message is displayed with a link to invite a new user.
-->
<script setup>
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import Dropdown from '@/Components/Dropdown.vue';
import DropdownLink from '@/Components/DropdownLink.vue';
import TextInput from '@/Components/TextInput.vue';
import { Link, usePage } from '@inertiajs/vue3';
import { ref, computed } from 'vue';
import axios from 'axios';
import eventBus from '@/Utils/eventBus';

const props = defineProps({
    users: Object,
});
const searchAllUsers = ref('');
const thisUser = usePage().props.auth.user;

// general function that returns the filtered users, based on the search input
function filterUsers(users, search) {
    if (search === '') {
        return users;
    }
    return users.filter((user) => {
        return user.name.toLowerCase().includes(search.toLowerCase())
    });
}

// filter the users based on an array of _ids 
function filterUsersById(users, ids) {
    return users.filter((user) => {
        return ids.includes(user._id);
    });
}

// function that filters the users based on the search input, using filterUsers function
const filteredUsers = computed(() => {
    return filterUsers(props.users, searchAllUsers.value);
});

// function that uses the filterUsersById function to filter the users based on the starred_users array
const topUsers = computed(() => {
    return filterUsersById(props.users, thisUser.starred_users);
});

// function that marks a user for a user as starred or not and send to the backend
function toggleStarred(user) {
    const starredUsers = (thisUser.starred_users == undefined ? new Array : thisUser.starred_users);
    // if starred_users contains user id, remove the user from the array, otherwise add it
    if (starredUsers.includes(user._id)) {
        starredUsers.splice(starredUsers.indexOf(user._id), 1);
    } else {
        starredUsers.push(user._id);
    }
    axios.put(route('profile.starred-users.update'), {
        starredUsers        
    }).then((response) => {
        if (response.status == 200) {
            thisUser.starred_users = starredUsers;
        }
        eventBus.$emit('topUsersUpdate', topUsers);
    }).catch((error) => {
        console.log("🚀 ~ file: UsersTable.vue:71 ~ toggleStarred ~ error", error)
    });
}




</script>

<template>
        <section>
            <header class="mb-4 flex justify-between items-center">
                <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">All the users</h2>
                <div>
                    <InputLabel for="searchAllUsers" value="Search all users" />
                    <TextInput
                        id="searchAllUsers"
                        type="text"
                        class="block"
                        v-model="searchAllUsers"
                        autocomplete="searchAllUsers"
                    />
                </div>
            </header>
            <div class="flex flex-wrap gap-4">
                <TransitionGroup name="list">
                    <div v-for="user in filteredUsers" :key="user._id">
                        <div class="flex flex-col bg-white rounded-lg shadow p-4 justify-around" :style="{'aspectRatio': '1/1' , 'width': '155px'}">
                                <div class="flex justify-end items-center">
                                    <svg v-if="!thisUser.starred_users?.includes(user._id)" xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 256 256" class="cursor-pointer" @click="toggleStarred(user)"><path fill="currentColor" d="M237.3 97.9a13.78 13.78 0 0 0-12.08-9.6l-59.46-5.14a2 2 0 0 1-1.65-1.22l-23.23-55.36a14 14 0 0 0-25.76 0L91.89 81.94a2 2 0 0 1-1.65 1.22L30.78 88.3a13.78 13.78 0 0 0-12.08 9.6a14 14 0 0 0 4.11 15l45.11 39.35a2.06 2.06 0 0 1 .64 2L55 212.76a14 14 0 0 0 5.45 14.56a13.74 13.74 0 0 0 15.4.62l51.11-31a1.9 1.9 0 0 1 2 0l51.11 31A14 14 0 0 0 201 212.76l-13.52-58.53a2.06 2.06 0 0 1 .64-2l45.11-39.35a14 14 0 0 0 4.07-14.98Zm-12 5.92l-45.11 39.35a14 14 0 0 0-4.44 13.76l13.52 58.53a2 2 0 0 1-.79 2.13a1.81 1.81 0 0 1-2.14.09l-51.11-31a13.92 13.92 0 0 0-14.46 0l-51.11 31a1.81 1.81 0 0 1-2.14-.09a2 2 0 0 1-.79-2.13l13.52-58.53a14 14 0 0 0-4.44-13.76L30.7 103.82a2 2 0 0 1-.59-2.19a1.86 1.86 0 0 1 1.7-1.38l59.47-5.14A14 14 0 0 0 103 86.58l23.23-55.36a2 2 0 0 1 3.62 0L153 86.58a14 14 0 0 0 11.68 8.53l59.47 5.14a1.86 1.86 0 0 1 1.7 1.38a2 2 0 0 1-.55 2.19Z"/></svg>
                                    <svg v-if="thisUser.starred_users?.includes(user._id)" xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 256 256" class="cursor-pointer" @click="toggleStarred(user)"><path fill="yellow" d="m234.5 114.38l-45.1 39.36l13.51 58.6a16 16 0 0 1-23.84 17.34l-51.11-31l-51 31a16 16 0 0 1-23.84-17.34l13.49-58.54l-45.11-39.42a16 16 0 0 1 9.11-28.06l59.46-5.15l23.21-55.36a15.95 15.95 0 0 1 29.44 0L166 81.17l59.44 5.15a16 16 0 0 1 9.11 28.06Z"/></svg>
                                </div>
                                <div class="flex justify-between items-center">
                                    <h3 class="text-md font-bold" :style="{ 'overflow': 'hidden', 'text-overflow': 'ellipsis', 'white-space': 'nowrap', 'width': '120px' }" v-tooltip="user.name">{{ user.name }}</h3>
                                </div>
                                <div class="flex items-center justify-between">
                                <div class="">
                                    <div class="relative">
                                        <Dropdown align="bla" width="48">
                                            <template #trigger>
                                                <button class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">Actions</button>
                                            </template>
                                            <template #content>
                                                <DropdownLink :href="route('users.show', user._id)"> Show </DropdownLink>
                                            </template>
                                        </Dropdown>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div v-if="filteredUsers.length != 0" v-tooltip="'Invite an user'" key="last">
                        <Link :href="route('users.create')" class="text-blue-500 hover:text-blue-700 font-bold">
                            <div class="flex flex-col rounded-lg shadow p-4 justify-around border-dashed border-2 border-blue-500 hover:border-blue-700" :style="{ 'aspectRatio': '1/1', 'width': '155px' }">
                                <div class="flex items-center justify-center">
                                    <div class="">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="36" height="36" viewBox="0 0 28 28"><path fill="currentColor" d="M24 13h-9V4a1 1 0 1 0-2 0v9H4a1 1 0 1 0 0 2h9v9a1 1 0 1 0 2 0v-9h9a1 1 0 1 0 0-2Z"/></svg>
                                    </div>
                                </div>
                            </div>
                        </Link>
                    </div>
                </TransitionGroup>
                <Transition name="list">
                    <h3 v-if="filteredUsers.length == 0" class="text-xl font-bold grow text-center">No matching users, <Link :href="route('users.create')" class="text-blue-500 hover:text-blue-700 ">invite one</Link> to your squad!</h3>
                </Transition>
            </div>
        </section>
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
