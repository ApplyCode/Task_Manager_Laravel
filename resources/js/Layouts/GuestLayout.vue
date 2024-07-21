<!--
DESCRIPTION: This Vue component represents the layout for the guest user. It displays the application logo and a container for the content of the page.
USAGE: This component is used as a layout for pages that are accessible to guest users.
-->
<script setup>
import ApplicationLogo from '@/Components/ApplicationLogo.vue';
import { Link } from '@inertiajs/vue3';
</script>

<template>
    <div class="min-h-screen flex flex-col justify-center items-center bg-gray-100 dark:bg-gray-900">
        <div>
            <Link href="/">
                <ApplicationLogo class="w-20 h-20 fill-current text-gray-500" />
            </Link>
        </div>

        <div
            class="max-w-md mx-6 sm:max-w-md mt-6 px-6 py-4 bg-white dark:bg-gray-800 shadow-md overflow-hidden sm:rounded-lg"
        >
            <slot />
        </div>
    </div>
</template>
