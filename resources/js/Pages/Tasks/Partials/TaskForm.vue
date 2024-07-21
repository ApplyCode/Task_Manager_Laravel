/**
 * TaskForm component for creating and editing tasks.
 * 
 * @component
 * 
 * @prop {Object} task - The task object to edit. Optional.
 * @prop {String} project - The project ID for the task. Optional.
 * 
 * @emits {Object} form - The form data object when submitted.
 * 
 * @example
 * <TaskForm :task="task" :project="project" />
 */
 <template>
    <section>
        <header>
            <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">Task Details</h2>

            <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                Isert below the task details.
            </p>
        </header>

        <form @submit.prevent="task?._id ? form.put(route('tasks.update', task._id)) : form.post(route('tasks.store')); closeModal()" class="mt-6 space-y-6">
            <div>
                <InputLabel for="project_id" value="Project" />

                <SelectInput id="project_id" class="mt-1 block w-full" v-model="form.project_id"
                    :options="projectDictionary" :firstDisabled='false'/>

                <InputError class="mt-2" :message="form.errors.project_id" />

            </div>

            <div>
                <InputLabel for="title" value="Title" />

                <TextInput id="title" type="text" class="mt-1 block w-full" v-model="form.title" required autofocus
                    autocomplete="title" />

                <InputError class="mt-2" :message="form.errors.title" />
            </div>

            <div>
                <InputLabel for="assigned_to" value="Assigned To" />

                <SelectInput id="assigned_to" class="mt-1 block w-full" v-model="form.assigned_to"
                    :options="userDictionary" :firstDisabled='false'/>

                <InputError class="mt-2" :message="form.errors.assigned_to" />
            </div>

            <div class="flex flex-col sm:flex-row gap-4">
                <div class="w-full">
                    <InputLabel for="start_date" value="Start Date" />

                    <TextInput id="start_date" type="date" class="mt-1 block w-full" v-model="form.start_date"
                        autocomplete="start_date" />

                    <InputError class="mt-2" :message="form.errors.start_date" />
                </div>

                <div class="w-full">
                    <InputLabel for="due_date" value="Due Date" />

                    <TextInput id="due_date" type="date" class="mt-1 block w-full" v-model="form.due_date"
                        autocomplete="due_date" />

                    <InputError class="mt-2" :message="form.errors.due_date" />
                </div>
            </div>

            <div class="flex flex-col sm:flex-row gap-4">
                <div class="w-full">
                    <InputLabel for="actual_start_date" value="Actual Start Date" />

                    <TextInput id="actual_start_date" type="date" class="mt-1 block w-full"
                        v-model="form.actual_start_date" autocomplete="actual_start_date" />

                    <InputError class="mt-2" :message="form.errors.actual_start_date" />
                </div>

                <div class="w-full">
                    <InputLabel for="completed_at" value="Completed At" />

                    <TextInput id="completed_at" type="date" class="mt-1 block w-full" v-model="form.completed_at"
                        autocomplete="completed_at" />

                    <InputError class="mt-2" :message="form.errors.completed_at" />
                </div>
            </div>

            <div>
                <InputLabel for="status" value="Status" />

                <SelectInput id="status" class="mt-1 block w-full" v-model="form.status" :options="{
                    'Cancelled': 'Cancelled',
                    'Completed': 'Completed',
                    'In Progress': 'In Progress',
                    'Not Started': 'Not Started',
                    'On Hold': 'On Hold',
                }" :firstDisabled='true'/>

                <InputError class="mt-2" :message="form.errors.status" />
            </div>

            <div>
                <InputLabel for="priority" value="Priority" />

                <SelectInput id="priority" class="mt-1 block w-full" v-model="form.priority" :options="{
                    'None': 'None',
                    'Lowest': 'Lowest',
                    'Low': 'Low',
                    'Medium': 'Medium',
                    'High': 'High',
                    'Highest': 'Highest',
                }" :firstDisabled='true'/>

                <InputError class="mt-2" :message="form.errors.priority" />
            </div>

            <div>
                <InputLabel for="team" value="Team" />

                <vue-select
                    id="team"
                    v-model="form.team"
                    :options="teamDictionary"
                    multiple
                    label="name"
                    :searchable="true"
                    :close-on-select="false"
                    :clear-on-select="false"
                    placeholder="Select a team" 
                    class="border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm mt-1 block w-full"              
                    :components="{ OpenIndicator }"
                />

                <InputError class="mt-2" :message="form.errors.team" />
            </div>

            <div>
                <InputLabel for="labels" value="Labels" />

                <vue-select
                    id="labels"
                    v-model="form.labels"
                    :options="labelArray"
                    multiple
                    searchable
                    taggable
                    :close-on-select="false"
                    :clear-on-select="false"
                    placeholder="Select a label" 
                    class="border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm mt-1 block w-full"              
                    :components="{ OpenIndicator }"
                />

                <InputError class="mt-2" :message="form.errors.labels" />
            </div>

            <div>
                <InputLabel for="category" value="Category" />

                <vue-select
                    id="category"
                    v-model="form.category"
                    :options="categoryArray"
                    searchable
                    taggable
                    placeholder="Select a category" 
                    class="border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm mt-1 block w-full"              
                    :components="{ OpenIndicator }"
                />


            </div>

            <div>
                <InputLabel for="task_progress" value="Progress" />

                <TextInput id="task_progress" type="number" class="mt-1 block w-full" v-model="form.task_progress"
                    autocomplete="task_progress" min="0" max="100"/>

                <InputError class="mt-2" :message="form.errors.task_progress" />
            </div>

            <div>
                <InputLabel for="public" value="Public" />

                <SelectInput id="public" class="mt-1 block w-full" v-model="form.public" required :options="{
                    false: 'No',
                    true: 'Yes',
                }" :firstDisabled='true'/>

                <InputError class="mt-2" :message="form.errors.public" />
            </div>

            <div>
                <InputLabel for="working_days" value="Working Days" />

                <TextInput id="working_days" type="number" class="mt-1 block w-full" v-model="form.working_days"
                    autocomplete="working_days" />

                <InputError class="mt-2" :message="form.errors.working_days" />
            </div>

            <div>
                <InputLabel for="planned_effort" value="Planned Effort (hours)" />

                <TextInput id="planned_effort" type="number" class="mt-1 block w-full" v-model="form.planned_effort"
                    autocomplete="planned_effort" />

                <InputError class="mt-2" :message="form.errors.planned_effort" />
            </div>

            <div>
                <InputLabel for="actual_effort" value="Actual Effort (hours)" />

                <TextInput id="actual_effort" type="number" class="mt-1 block w-full" v-model="form.actual_effort"
                    autocomplete="actual_effort" />

                <InputError class="mt-2" :message="form.errors.actual_effort" />
            </div>

            <div>
                <InputLabel for="cost" value="Cost" />

                <TextInput id="cost" type="number" class="mt-1 block w-full" v-model="form.cost"
                    autocomplete="cost" />

                <InputError class="mt-2" :message="form.errors.cost" />
            </div>

            <div>
                <InputLabel for="description" value="Description" />

                <TextAreaInput id="description" rows="10" class="mt-1 block w-full" v-model="form.description"  />

                <InputError class="mt-2" :message="form.errors.description" />
            </div>

            <div>
                <InputLabel for="reminder_date" value="Reminder Date" />

                <TextInput id="reminder_date" type="date" class="mt-1 block w-full" v-model="form.reminder_date"
                    autocomplete="reminder_date" />

                <InputError class="mt-2" :message="form.errors.reminder_date" />
            </div>

            <div class="flex items-center justify-between gap-4">
                <PrimaryButton :disabled="form.processing">Save</PrimaryButton>

                <Transition enter-from-class="opacity-0" leave-to-class="opacity-0" class="transition ease-in-out">
                    <p v-if="form.recentlySuccessful" class="text-sm text-gray-600 dark:text-gray-400">Saved.</p>
                </Transition>
                <span @click.prevent="deleteTask()" 
                    v-if="task?.project_id"
                    class="cursor-pointer inline-flex items-center px-4 py-2 bg-red-800 dark:bg-red-200 border border-transparent rounded-md font-semibold text-xs text-white dark:text-red-800 uppercase tracking-widest hover:bg-red-700 dark:hover:bg-white focus:bg-red-700 dark:focus:bg-white active:bg-red-900 dark:active:bg-red-300 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 dark:focus:ring-offset-red-800 transition ease-in-out duration-150">
                Delete</span>
                <span @click.prevent="closeModal()" 
                    class="cursor-pointer inline-flex items-center px-4 py-2 bg-yellow-600 dark:bg-red-200 border border-transparent rounded-md font-semibold text-xs text-white dark:text-red-800 uppercase tracking-widest hover:bg-red-700 dark:hover:bg-white focus:bg-red-700 dark:focus:bg-white active:bg-red-900 dark:active:bg-red-300 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 dark:focus:ring-offset-red-800 transition ease-in-out duration-150">
                Cancel</span>
            </div>
        </form>
    </section>
</template>
<script setup>
import { defineProps, ref, h } from 'vue';
import { useForm, Link, usePage } from '@inertiajs/vue3';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import TextAreaInput from '@/Components/TextAreaInput.vue';
import SelectInput from '@/Components/SelectInput.vue';

const props = defineProps({
    task: {
        type: Object,
        required: false
    },
    project: {
        type: String,
        required: false
    },
});

const task = ref(props.task);
const project = ref(props.project ? props.project : task.value?.project_id);
const startDate = ref(new Date(
    parseInt(props?.task?.start_date?.$date.$numberLong)
).toLocaleDateString('en-CA', { timeZone: "UTC" }));
const dueDate = ref(new Date(
    parseInt(props?.task?.due_date?.$date.$numberLong)
).toLocaleDateString('en-CA', { timeZone: "UTC" }));

const actualStartDate = ref(new Date(
    parseInt(props?.task?.actual_start_date?.$date.$numberLong)
).toLocaleDateString('en-CA', { timeZone: "UTC" }));

const completedAt = ref(new Date(
    parseInt(props?.task?.completed_at?.$date.$numberLong)
).toLocaleDateString('en-CA', { timeZone: "UTC" }));

const reminderDate = ref(new Date(
    parseInt(props?.task?.reminder_date?.$date.$numberLong)
).toLocaleDateString('en-CA', { timeZone: "UTC" }));

const selectedTeam = ref(
    usePage()
        .props
        .allUsers
        .filter(user => {
            return task.value?.team?.includes(user._id);
        })
        .map(user => {
            return {
                name: user.name,
                id: user._id,
            }
        })
);

const labelArray = ref(
    usePage()
        .props
        .allLabels
);

const categoryArray = ref(
    usePage()
        .props
        .allCategories
);

const form = useForm({
    project_id: project.value,
    title: task.value?.title,
    start_date: task.value?.start_date == undefined ? undefined : startDate,
    due_date: task.value?.due_date == undefined ? undefined : dueDate,
    actual_start_date: task.value?.actual_start_date == undefined ? undefined : actualStartDate,
    completed_at: task.value?.completed_at == undefined ? undefined : completedAt,
    task_progress: task.value?.task_progress,
    completed: task.value?.task_progress == 100,
    priority: task.value?.priority,
    status: task.value?.status,
    public: task.value?.public,
    assigned_to: task.value?.assigned_to,
    team: selectedTeam,
    labels: task.value?.labels,
    category: task.value?.category,
    working_days: task.value?.working_days,
    planned_effort: task.value?.planned_effort,
    actual_effort: task.value?.actual_effort,
    cost: task.value?.cost,
    reminder_date: task.value?.reminder_date == undefined ? undefined : reminderDate,
    description: task.value?.description,
});

const projectDictionary = ref(
    usePage()
    .props
    .allProjects
    .reduce((acc, project) => {
        acc[project._id] = project.name;
        return acc;
    }, {})
);

const userDictionary = ref(
    usePage()
    .props
    .allUsers
    .reduce((acc, user) => {
        acc[user._id] = user.name;
        return acc;
    }, {})
);

const teamDictionary = ref(
    usePage()
    .props
    .allUsers
    .map(user => {
        return {
            name: user.name,
            id: user._id,
        }
    })
);

const OpenIndicator = {
    render: () => h('span', { class: 'vs__open-indicator' }, [
        h('svg', {
            xmlns: 'http://www.w3.org/2000/svg',
            width: '20',
            height: '20',
            viewBox: '0 0 48 48'
        }, [
            h('path', {
                fill: 'none',
                stroke: 'currentColor',
                'stroke-linecap': 'round',
                'stroke-linejoin': 'round',
                'stroke-width': '4',
                d: 'M36 18L24 30L12 18'
            })
        ])
    ])
}

const closeModal = () => {
    const esc = new KeyboardEvent('keydown', {
        key: 'Escape',
        code: 'Escape',
        keyCode: 27,
        bubbles: true
    });
    document.dispatchEvent(esc);
}

const deleteTask = () => {
    if (confirm('Are you sure you want to delete this task?')) {
        form.delete(route('tasks.destroy', task.value._id));
        closeModal();
    }
}

</script>
<style>
.vs__search{
    margin-top: 8px !important;
    margin-bottom: 4px !important;
}
.vs__selected {
    min-height: 30.4px !important;
    border: unset !important;
}
.vs__actions {
    right: 7px !important;
    position: relative !important;
}
.vs__deselect {
    padding-left: 5px !important;
    padding-right: 5px !important;
}
</style>