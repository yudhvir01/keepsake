<script setup>
import { ref, watch } from 'vue';
import { router } from '@inertiajs/vue3';

const props = defineProps({
    modelValue: { type: String, default: '' },
});

const emit = defineEmits(['update:modelValue']);
const search = ref(props.modelValue);
let timeout = null;

watch(search, (val) => {
    clearTimeout(timeout);
    timeout = setTimeout(() => {
        emit('update:modelValue', val);
    }, 300);
});

function clear() {
    search.value = '';
    emit('update:modelValue', '');
}
</script>

<template>
    <div class="relative">
        <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
            <svg class="h-5 w-5 text-gray-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="m21 21-5.197-5.197m0 0A7.5 7.5 0 1 0 5.196 5.196a7.5 7.5 0 0 0 10.607 10.607Z" />
            </svg>
        </div>
        <input
            v-model="search"
            type="text"
            placeholder="Search your keepsakes..."
            class="block w-full rounded-xl border-0 bg-gray-800 py-3 pl-10 pr-12 text-gray-100 ring-1 ring-inset ring-gray-700 placeholder:text-gray-500 focus:ring-2 focus:ring-indigo-500 sm:text-sm sm:leading-6"
        />
        <button
            v-if="search"
            @click="clear"
            class="absolute inset-y-0 right-0 flex items-center pr-3 text-gray-500 hover:text-gray-300"
        >
            <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12" />
            </svg>
        </button>
        <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center pr-10" v-if="!search">
            <kbd class="rounded bg-gray-700 px-1.5 py-0.5 text-xs text-gray-400">Ctrl+K</kbd>
        </div>
    </div>
</template>
