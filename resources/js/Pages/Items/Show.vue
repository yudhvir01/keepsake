<script setup>
import { ref } from 'vue';
import { Head, router, useForm } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import TagBadge from '@/Components/TagBadge.vue';
import ConfirmDialog from '@/Components/ConfirmDialog.vue';

const props = defineProps({
    item: Object,
    allTags: Array,
    allCategories: Array,
});

const editing = ref(false);
const showDeleteConfirm = ref(false);

const form = useForm({
    title: props.item.title,
    description: props.item.description || '',
    url: props.item.url || '',
    category_id: props.item.category_id,
    tags: props.item.tags?.map(t => t.id) || [],
});

function save() {
    form.put(route('items.update', props.item.id), {
        preserveScroll: true,
        onSuccess: () => { editing.value = false; },
    });
}

function togglePin() {
    router.post(route('items.pin', props.item.id), {}, { preserveScroll: true });
}

function deleteItem() {
    router.delete(route('items.destroy', props.item.id));
}

function toggleTag(tagId) {
    const idx = form.tags.indexOf(tagId);
    if (idx >= 0) form.tags.splice(idx, 1);
    else form.tags.push(tagId);
}

const isMedia = ['image', 'screenshot'].includes(props.item.type);
const isVideo = props.item.type === 'video';
const isLink = props.item.type === 'link';

const typeIcons = { link: '🔗', image: '🖼️', video: '🎬', screenshot: '📸', clip: '🎞️' };
</script>

<template>
    <Head :title="item.title" />

    <AuthenticatedLayout>
        <div class="min-h-screen py-8 px-4 sm:px-6 lg:px-8">
            <div class="mx-auto max-w-4xl">
                <!-- Back button -->
                <button
                    @click="router.get(route('dashboard'))"
                    class="mb-6 inline-flex items-center gap-2 text-sm text-gray-500 hover:text-gray-300 transition"
                >
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5 3 12m0 0 7.5-7.5M3 12h18" />
                    </svg>
                    Back to Keepsake
                </button>

                <div class="rounded-2xl bg-gray-800 border border-gray-700/50 overflow-hidden">
                    <!-- Media display -->
                    <div v-if="isMedia && item.file_url" class="bg-gray-900 flex items-center justify-center p-4">
                        <img :src="item.file_url" :alt="item.title" class="max-h-[70vh] rounded-lg object-contain" />
                    </div>
                    <div v-else-if="isVideo && item.file_url" class="bg-gray-900 p-4">
                        <video controls class="mx-auto max-h-[70vh] rounded-lg">
                            <source :src="item.file_url" />
                        </video>
                    </div>
                    <div v-else-if="isLink && item.thumbnail_url" class="aspect-video max-h-80">
                        <img :src="item.thumbnail_url" :alt="item.title" class="w-full h-full object-cover" />
                    </div>
                    <div v-else class="h-40 bg-gradient-to-br from-indigo-900/30 to-violet-900/30 flex items-center justify-center">
                        <span class="text-6xl opacity-40">{{ typeIcons[item.type] || '📄' }}</span>
                    </div>

                    <!-- Content -->
                    <div class="p-6 sm:p-8">
                        <!-- Actions bar -->
                        <div class="flex items-center justify-between mb-4">
                            <div class="flex items-center gap-2">
                                <span class="text-sm">{{ typeIcons[item.type] }}</span>
                                <span class="text-xs font-medium text-gray-500 uppercase tracking-wider">{{ item.type }}</span>
                                <span class="text-xs text-gray-600">· {{ new Date(item.created_at).toLocaleDateString('en-US', { year: 'numeric', month: 'short', day: 'numeric' }) }}</span>
                            </div>
                            <div class="flex items-center gap-2">
                                <button
                                    @click="togglePin"
                                    class="rounded-lg p-2 transition-all duration-200"
                                    :class="item.is_pinned ? 'bg-amber-500/20 text-amber-400' : 'bg-gray-700 text-gray-400 hover:text-amber-400'"
                                >
                                    <svg class="h-5 w-5" :fill="item.is_pinned ? 'currentColor' : 'none'" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M11.48 3.499a.562.562 0 011.04 0l2.125 5.111a.563.563 0 00.475.345l5.518.442c.499.04.701.663.321.988l-4.204 3.602a.563.563 0 00-.182.557l1.285 5.385a.562.562 0 01-.84.61l-4.725-2.885a.562.562 0 00-.586 0L6.982 20.54a.562.562 0 01-.84-.61l1.285-5.386a.562.562 0 00-.182-.557l-4.204-3.602a.562.562 0 01.321-.988l5.518-.442a.563.563 0 00.475-.345L11.48 3.5z" />
                                    </svg>
                                </button>
                                <button
                                    @click="editing = !editing"
                                    class="rounded-lg bg-gray-700 p-2 text-gray-400 hover:text-indigo-400 transition"
                                >
                                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L10.582 16.07a4.5 4.5 0 0 1-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 0 1 1.13-1.897l8.932-8.931Zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0 1 15.75 21H5.25A2.25 2.25 0 0 1 3 18.75V8.25A2.25 2.25 0 0 1 5.25 6H10" />
                                    </svg>
                                </button>
                                <button
                                    @click="showDeleteConfirm = true"
                                    class="rounded-lg bg-gray-700 p-2 text-gray-400 hover:text-red-400 transition"
                                >
                                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0" />
                                    </svg>
                                </button>
                            </div>
                        </div>

                        <!-- View / Edit mode -->
                        <div v-if="!editing">
                            <h1 class="text-2xl font-bold text-gray-100 mb-2">{{ item.title }}</h1>
                            <p v-if="item.description" class="text-gray-400 mb-4 whitespace-pre-wrap">{{ item.description }}</p>

                            <a
                                v-if="item.url"
                                :href="item.url"
                                target="_blank"
                                class="inline-flex items-center gap-2 rounded-xl bg-indigo-600/20 px-4 py-2 text-sm font-medium text-indigo-400 hover:bg-indigo-600/30 transition mb-4"
                            >
                                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 6H5.25A2.25 2.25 0 0 0 3 8.25v10.5A2.25 2.25 0 0 0 5.25 21h10.5A2.25 2.25 0 0 0 18 18.75V10.5m-10.5 6L21 3m0 0h-5.25M21 3v5.25" />
                                </svg>
                                Open Link
                            </a>

                            <div v-if="item.category" class="flex items-center gap-2 mb-3">
                                <span>{{ item.category.icon || '📁' }}</span>
                                <span class="text-sm text-gray-400">{{ item.category.name }}</span>
                            </div>

                            <div v-if="item.tags?.length" class="flex flex-wrap gap-2">
                                <TagBadge v-for="tag in item.tags" :key="tag.id" :tag="tag" />
                            </div>
                        </div>

                        <!-- Edit form -->
                        <form v-else @submit.prevent="save" class="space-y-4">
                            <div>
                                <label class="text-sm text-gray-400">Title</label>
                                <input v-model="form.title" class="mt-1 w-full rounded-lg bg-gray-900 border-gray-700 text-gray-100 focus:border-indigo-500 focus:ring-indigo-500" />
                            </div>
                            <div>
                                <label class="text-sm text-gray-400">Description</label>
                                <textarea v-model="form.description" rows="3" class="mt-1 w-full rounded-lg bg-gray-900 border-gray-700 text-gray-100 focus:border-indigo-500 focus:ring-indigo-500"></textarea>
                            </div>
                            <div v-if="isLink">
                                <label class="text-sm text-gray-400">URL</label>
                                <input v-model="form.url" type="url" class="mt-1 w-full rounded-lg bg-gray-900 border-gray-700 text-gray-100 focus:border-indigo-500 focus:ring-indigo-500" />
                            </div>
                            <div>
                                <label class="text-sm text-gray-400">Category</label>
                                <select v-model="form.category_id" class="mt-1 w-full rounded-lg bg-gray-900 border-gray-700 text-gray-100 focus:border-indigo-500 focus:ring-indigo-500">
                                    <option :value="null">None</option>
                                    <option v-for="cat in allCategories" :key="cat.id" :value="cat.id">{{ cat.icon }} {{ cat.name }}</option>
                                </select>
                            </div>
                            <div>
                                <label class="text-sm text-gray-400 mb-2 block">Tags</label>
                                <div class="flex flex-wrap gap-2">
                                    <TagBadge
                                        v-for="tag in allTags"
                                        :key="tag.id"
                                        :tag="tag"
                                        :selected="form.tags.includes(tag.id)"
                                        @click="toggleTag(tag.id)"
                                    />
                                </div>
                            </div>
                            <div class="flex gap-3">
                                <button type="submit" :disabled="form.processing" class="rounded-lg bg-indigo-600 px-4 py-2 text-sm font-medium text-white hover:bg-indigo-500 transition">
                                    {{ form.processing ? 'Saving...' : 'Save Changes' }}
                                </button>
                                <button type="button" @click="editing = false" class="rounded-lg bg-gray-700 px-4 py-2 text-sm text-gray-300 hover:bg-gray-600 transition">Cancel</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <ConfirmDialog
            :show="showDeleteConfirm"
            title="Delete this item?"
            message="This will permanently remove this item and its files from your Keepsake."
            @confirm="deleteItem"
            @cancel="showDeleteConfirm = false"
        />
    </AuthenticatedLayout>
</template>
