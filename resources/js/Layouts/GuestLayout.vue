<script setup>
import { Link } from '@inertiajs/vue3';
</script>

<template>
    <div
        class="flex min-h-screen flex-col items-center bg-gray-100 pt-6 sm:justify-center sm:pt-0 dark:bg-gray-950"
    >
        <div>
            <Link :href="route('login')" class="flex items-center gap-2">
                <span class="text-3xl">🗃️</span>
                <span class="text-2xl font-bold bg-gradient-to-r from-indigo-400 to-violet-400 bg-clip-text text-transparent">Keepsake</span>
            </Link>
        </div>

        <div
            class="mt-6 w-full overflow-hidden bg-white px-6 py-6 shadow-md sm:max-w-md sm:rounded-2xl dark:bg-gray-900 dark:border dark:border-gray-800"
        >
            <slot />
        </div>
    </div>
</template>
