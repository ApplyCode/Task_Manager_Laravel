<!--
The CommentsForm component displays a form for adding comments to a task. It receives the following props:
- taskId: the ID of the task being commented on
- taskTitle: the title of the task being commented on
- comments: an array of comments to display

The component uses the following child components:
- TextAreaInput: a custom text area input component
- InputError: a component for displaying form input errors
- CommentBox: a component for displaying individual comments

The component sets up a form using the useForm function from the @inertiajs/vue3 package. It also sets up a userDictionary object to map user IDs to user objects for displaying user names in the CommentBox component.

The component listens for updates to the comments prop and scrolls to the bottom of the comment section when new comments are added.

The component emits a 'userUpdate' event on the eventBus when a user is updated or created.
-->
<template>
    <section>
        <header class="flex justify-between items-start">
            <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">Comments for "<a :href="route('search.show', {'search': taskTitle})" v-tooltip="'Click here to see the task'">
                {{ taskTitle }}
                </a>"</h2>
            <button @click="closeModal()" class="text-gray-500 hover:text-blue-700 dark:text-gray-400 dark:hover:text-blue-300">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        stroke-width="2"
                        d="M6 18L18 6M6 6l12 12"
                    ></path>
                </svg>
            </button>
        </header>
        <div class="h-80 overflow-y-auto custom-scroll space-y-4 py-4">
            <CommentBox v-for="(comment, key) in props.comments" :key="key"
                :user-name="userDictionary[comment.user_id].name"
                :text="comment.body"
                :timestamp="comment.created_at"
                :is-left="comment.user_id !== user._id"
            />
            <span ref="scrollBottom"></span>
        </div>
    </section>
    <section>
        <form @submit.prevent="submit()" @keydown="keydown($event)">
            <div class="flex flex-col space-y-4">
                <div class="grow">
                    <TextAreaInput
                        id="comment"
                        class="block w-full mt-1"
                        rows="3"
                        v-model="form.body"
                        :error="form.errors.body"
                        placeholder="Enter your comment here..."
                    />
                    <InputError :message="form.errors.body" />
                </div>
                <div class="flex justify-end">
                    <button
                        type="submit"
                        class="inline-flex items-center px-4 py-2 text-sm font-medium text-white bg-indigo-600 border border-transparent rounded-md shadow-sm hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
                    >
                        Add Comment
                    </button>
                </div>
            </div>
        </form>
        
    </section>
</template>
<script setup>
import { defineProps, ref, onMounted, watch } from 'vue';
import { useForm, Link, usePage } from '@inertiajs/vue3';
import TextAreaInput from '@/Components/TextAreaInput.vue';
import InputError from '@/Components/InputError.vue';
import CommentBox from '@/Components/CommentBox.vue';
import eventBus from '@/Utils/eventBus';

const scrollBottom = ref(null)
const props = defineProps({
    taskId: {
        type: String,
        required: true,
    },
    taskTitle: {
        type: String,
        required: true,
    },
    comments: {
        type: Array,
        required: true,
    },
});

const taskId = ref(props.taskId);
const taskTitle = ref(props.taskTitle);
const comment = ref('');

const form = useForm({
    task_id: taskId.value,
    body: comment.value,
});
const userDictionary = ref(Object.fromEntries(usePage().props.allUsers.map(user => [user._id, user])));

const user = ref(
    usePage()
    .props
    .auth
    .user
);

const closeModal = () => {
    const esc = new KeyboardEvent('keydown', {
        key: 'Escape',
        code: 'Escape',
        keyCode: 27,
        bubbles: true
    });
    document.dispatchEvent(esc);
}
const cleanForm = () => {
    form.body = '';
}
const scrollToBottom = () => {
    setTimeout(() => {
        scrollBottom.value?.scrollIntoView()
    }, 300);
}
onMounted(() => {
    scrollToBottom();
    eventBus.$on('userUpdate', (data) => {
        switch (data.type) {
            case 'update':
            case 'create':
                userDictionary.value[data.user._id] = data.user;
                break;
            default:
                break;
        }
    });
})
watch(() => props.comments, () => {
    scrollToBottom();
})

const submit = () => {
    form.post(route('comments.store'));
    cleanForm();
}
const keydown = (e) => {
    if (e.key === 'Enter' && e.shiftKey === false) {
        e.preventDefault();
        submit();
    }
}


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
</style>