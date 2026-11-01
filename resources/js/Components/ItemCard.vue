<script setup>
import { computed } from 'vue';
import { router } from '@inertiajs/vue3';
import TagBadge from '@/Components/TagBadge.vue';

const props = defineProps({
    item: { type: Object, required: true },
});

const isMedia = computed(() => ['image', 'screenshot'].includes(props.item.type));
const isVideo = computed(() => props.item.type === 'video');
const isLink = computed(() => props.item.type === 'link');

const domain = computed(() => {
    if (props.item.url) {
        try { return new URL(props.item.url).hostname.replace('www.', ''); } catch { return ''; }
    }
    return props.item.metadata?.domain || '';
});

const typeIcon = computed(() => {
    const icons = { link: '🔗', image: '🖼️', video: '🎬', screenshot: '📸', clip: '🎞️' };
    return icons[props.item.type] || '📄';
});

function togglePin() {
    router.post(route('items.pin', props.item.id), {}, { preserveScroll: true });
}

function deleteItem() {
    if (confirm('Delete this item?')) {
        router.delete(route('items.destroy', props.item.id), { preserveScroll: true });
    }
}

function openItem() {
    if (isLink.value && props.item.url) {
        window.open(props.item.url, '_blank');
    } else {
        router.get(route('items.show', props.item.id));
    }
}
</script>

<template>
    <div
        class="group relative break-inside-avoid mb-4 rounded-2xl bg-gray-800/80 border border-gray-700/50 overflow-hidden cursor-pointer transition-all duration-300 hover:shadow-2xl hover:shadow-indigo-500/10 hover:border-gray-600/50 hover:-translate-y-0.5"
        @click="openItem"
    >
        <!-- Pin button -->
        <button
            @click.stop="togglePin"
            class="absolute top-2 right-2 z-10 rounded-full p-1.5 transition-all duration-200"
            :class="item.is_pinned ? 'bg-amber-500/20 text-amber-400' : 'bg-gray-900/60 text-gray-500 opacity-0 group-hover:opacity-100'"
        >
            <svg class="h-4 w-4" :fill="item.is_pinned ? 'currentColor' : 'none'" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="M11.48 3.499a.562.562 0 011.04 0l2.125 5.111a.563.563 0 00.475.345l5.518.442c.499.04.701.663.321.988l-4.204 3.602a.563.563 0 00-.182.557l1.285 5.385a.562.562 0 01-.84.61l-4.725-2.885a.562.562 0 00-.586 0L6.982 20.54a.562.562 0 01-.84-.61l1.285-5.386a.562.562 0 00-.182-.557l-4.204-3.602a.562.562 0 01.321-.988l5.518-.442a.563.563 0 00.475-.345L11.48 3.5z" />
            </svg>
        </button>

        <!-- Delete button -->
        <button
            @click.stop="deleteItem"
            class="absolute top-2 left-2 z-10 rounded-full p-1.5 bg-gray-900/60 text-gray-500 opacity-0 group-hover:opacity-100 hover:text-red-400 hover:bg-red-500/20 transition-all duration-200"
        >
            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0" />
            </svg>
        </button>

        <!-- Image / Video thumbnail -->
        <div v-if="isMedia && item.file_url" class="aspect-auto">
            <img :src="item.file_url" :alt="item.title" class="w-full object-cover" loading="lazy" />
        </div>
        <div v-else-if="isVideo && item.file_url" class="aspect-video bg-gray-900 flex items-center justify-center">
            <span class="text-4xl">▶️</span>
        </div>
        <div v-else-if="isLink && item.thumbnail_url" class="aspect-video">
            <img :src="item.thumbnail_url" :alt="item.title" class="w-full h-full object-cover" loading="lazy" />
        </div>
        <div v-else-if="isLink" class="aspect-video bg-gradient-to-br from-indigo-900/40 to-violet-900/40 flex items-center justify-center">
            <span class="text-5xl opacity-50">{{ typeIcon }}</span>
        </div>

        <!-- Content -->
        <div class="p-4">
            <!-- Type badge -->
            <div class="flex items-center gap-2 mb-2">
                <span class="text-xs">{{ typeIcon }}</span>
                <span class="text-xs font-medium text-gray-500 uppercase tracking-wider">{{ item.type }}</span>
                <span v-if="domain" class="text-xs text-gray-600 truncate">· {{ domain }}</span>
            </div>

            <!-- Title -->
            <h3 class="font-semibold text-gray-100 line-clamp-2 leading-snug">{{ item.title }}</h3>

            <!-- Description -->
            <p v-if="item.description" class="mt-1.5 text-sm text-gray-400 line-clamp-2">{{ item.description }}</p>

            <!-- Category -->
            <div v-if="item.category" class="mt-2 flex items-center gap-1">
                <span class="text-xs">{{ item.category.icon || '📁' }}</span>
                <span class="text-xs text-gray-500">{{ item.category.name }}</span>
            </div>

            <!-- Tags -->
            <div v-if="item.tags?.length" class="mt-3 flex flex-wrap gap-1.5">
                <TagBadge v-for="tag in item.tags" :key="tag.id" :tag="tag" small />
            </div>
        </div>
    </div>
</template>
