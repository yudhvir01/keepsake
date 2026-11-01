<script setup>
import { Link } from '@inertiajs/vue3';

defineProps({
    title: { type: String, default: 'Nothing here yet' },
    description: { type: String, default: 'Get started by adding your first item.' },
    icon: { type: String, default: '📭' },
    actionLabel: { type: String, default: null },
    actionRoute: { type: String, default: null },
});

defineEmits(['action']);
</script>

<template>
    <div class="flex flex-col items-center justify-center py-20 text-center">
        <span class="text-6xl mb-4">{{ icon }}</span>
        <h3 class="text-xl font-semibold text-gray-200">{{ title }}</h3>
        <p class="mt-2 text-sm text-gray-500 max-w-md">{{ description }}</p>
        <button
            v-if="actionLabel"
            @click="$emit('action')"
            class="mt-6 inline-flex items-center gap-2 rounded-xl bg-indigo-600 px-5 py-2.5 text-sm font-semibold text-white shadow-lg shadow-indigo-500/25 hover:bg-indigo-500 transition-all duration-200"
        >
            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
            </svg>
            {{ actionLabel }}
        </button>
    </div>
</template>
