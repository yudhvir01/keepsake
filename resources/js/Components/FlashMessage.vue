<script setup>
import { ref, watch } from 'vue';
import { usePage } from '@inertiajs/vue3';

const page = usePage();
const show = ref(false);
const flash = ref({ type: '', message: '' });
let timer = null;

watch(
    () => page.props.flash,
    (val) => {
        if (val?.message) {
            flash.value = val;
            show.value = true;
            clearTimeout(timer);
            timer = setTimeout(() => { show.value = false; }, 3500);
        }
    },
    { immediate: true, deep: true }
);
</script>

<template>
    <Transition
        enter-active-class="transition ease-out duration-300"
        enter-from-class="translate-x-full opacity-0"
        enter-to-class="translate-x-0 opacity-100"
        leave-active-class="transition ease-in duration-200"
        leave-from-class="translate-x-0 opacity-100"
        leave-to-class="translate-x-full opacity-0"
    >
        <div
            v-if="show"
            class="fixed top-4 right-4 z-[100] max-w-sm rounded-xl px-4 py-3 shadow-2xl backdrop-blur-sm"
            :class="{
                'bg-emerald-500/90 text-white': flash.type === 'success',
                'bg-red-500/90 text-white': flash.type === 'error',
                'bg-indigo-500/90 text-white': flash.type === 'info',
            }"
        >
            <div class="flex items-center gap-2">
                <svg v-if="flash.type === 'success'" class="h-5 w-5 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                </svg>
                <svg v-else-if="flash.type === 'error'" class="h-5 w-5 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m9-.75a9 9 0 1 1-18 0 9 9 0 0 1 18 0Zm-9 3.75h.008v.008H12v-.008Z" />
                </svg>
                <p class="text-sm font-medium">{{ flash.message }}</p>
                <button @click="show = false" class="ml-auto hover:opacity-75">
                    <svg class="h-4 w-4" fill="currentColor" viewBox="0 0 20 20"><path d="M6.28 5.22a.75.75 0 00-1.06 1.06L8.94 10l-3.72 3.72a.75.75 0 101.06 1.06L10 11.06l3.72 3.72a.75.75 0 101.06-1.06L11.06 10l3.72-3.72a.75.75 0 00-1.06-1.06L10 8.94 6.28 5.22z" /></svg>
                </button>
            </div>
        </div>
    </Transition>
</template>
