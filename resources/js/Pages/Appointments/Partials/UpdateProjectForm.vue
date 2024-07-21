<!--
    FILEPATH: c:\xampp\htdocs\portfolio\laravel-vue-mongodb-task-manager\resources\js\Pages\Projects\Partials\UpdateProjectForm.vue
    DESCRIPTION: This component is responsible for rendering a form to update a project's details. It imports several components such as InputError, InputLabel, PrimaryButton, TextInput, TextAreaInput, Link, useForm, and usePage from InertiaJS. It receives a project object as a prop and uses it to pre-fill the form fields. When the form is submitted, it calls the 'put' method of the useForm hook to update the project via a PUT request to the 'projects.update' route. It also displays a success message when the form is successfully submitted. The form can be cancelled by clicking the 'Cancel' button which redirects to the 'projects' route.
-->
<script setup>
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import TextAreaInput from '@/Components/TextAreaInput.vue';
import { Link, useForm, usePage } from '@inertiajs/vue3';

const props = defineProps({

});

const project = usePage().props.project;

const form = useForm({
    name: project.name,
    description: project.description,
});
</script>

<template>
    <section>
        <header>
            <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">Project Details</h2>

            <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                Isert below the project details.
            </p>
        </header>

        <form @submit.prevent="form.put(route('projects.update', project._id))" class="mt-6 space-y-6">
            <div>
                <InputLabel for="name" value="Name" />

                <TextInput id="name" type="text" class="mt-1 block w-full" v-model="form.name" required autofocus
                    autocomplete="name" />

                <InputError class="mt-2" :message="form.errors.name" />
            </div>

            <div>
                <InputLabel for="description" value="Description" />

                <TextAreaInput id="description" rows="10" class="mt-1 block w-full" v-model="form.description" required
                />

                <InputError class="mt-2" :message="form.errors.description" />
            </div>

            <div class="flex items-center justify-between gap-4">
                <PrimaryButton :disabled="form.processing">Save</PrimaryButton>

                <Transition enter-from-class="opacity-0" leave-to-class="opacity-0" class="transition ease-in-out">
                    <p v-if="form.recentlySuccessful" class="text-sm text-gray-600 dark:text-gray-400">Saved.</p>
                </Transition>

                <Link :href="route('projects')" class="inline-flex items-center px-4 py-2 bg-red-800 dark:bg-red-200 border border-transparent rounded-md font-semibold text-xs text-white dark:text-red-800 uppercase tracking-widest hover:bg-red-700 dark:hover:bg-white focus:bg-red-700 dark:focus:bg-white active:bg-red-900 dark:active:bg-red-300 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 dark:focus:ring-offset-red-800 transition ease-in-out duration-150">Cancel</Link>
            </div>
        </form>
    </section>
</template>