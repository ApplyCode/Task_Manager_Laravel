<!--
  DESCRIPTION: This component is responsible for rendering a search form that allows users to search for tasks, projects or users.
-->

<script setup>
import TextInput from '@/Components/TextInput.vue';
import { useForm } from '@inertiajs/vue3';

const props = defineProps({

});

const form = useForm({
    search: '',
});
</script>

<template>
    <form @submit.prevent="form.get(route('search.show'))">
        <div class="flex items-center justify-between px-4">
            <TextInput type="search" class="mt-1 block w-full" v-model="form.search" required placeholder="Task, project or user"/>
        </div>
    </form>
</template>