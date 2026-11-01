<script setup>
import { ref, watch, computed } from 'vue';
import { Head, router } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import ItemCard from '@/Components/ItemCard.vue';
import SearchBar from '@/Components/SearchBar.vue';
import TagBadge from '@/Components/TagBadge.vue';
import EmptyState from '@/Components/EmptyState.vue';
import AddItemModal from '@/Components/AddItemModal.vue';

const props = defineProps({
    items: Object,
    filters: Object,
    tags: Array,
    categories: Array,
    types: Array,
});

const showAddModal = ref(false);
const search = ref(props.filters.search || '');
const activeType = ref(props.filters.type || '');
const activeTag = ref(props.filters.tag || '');
const activeCategory = ref(props.filters.category || '');
const pinnedOnly = ref(props.filters.pinned || false);

function applyFilters() {
    router.get(route('dashboard'), {
        search: search.value || undefined,
        type: activeType.value || undefined,
        tag: activeTag.value || undefined,
        category: activeCategory.value || undefined,
        pinned: pinnedOnly.value || undefined,
    }, {
        preserveState: true,
        preserveScroll: true,
    });
}

watch(search, applyFilters);

function setType(type) {
    activeType.value = activeType.value === type ? '' : type;
    applyFilters();
}

function setTag(slug) {
    activeTag.value = activeTag.value === slug ? '' : slug;
    applyFilters();
}

function setCategory(slug) {
    activeCategory.value = activeCategory.value === slug ? '' : slug;
    applyFilters();
}

function togglePinned() {
    pinnedOnly.value = !pinnedOnly.value;
    applyFilters();
}

function clearFilters() {
    search.value = '';
    activeType.value = '';
    activeTag.value = '';
    activeCategory.value = '';
    pinnedOnly.value = false;
    applyFilters();
}

const hasFilters = computed(() =>
    search.value || activeType.value || activeTag.value || activeCategory.value || pinnedOnly.value
);

const typeIcons = {
    link: '🔗',
    image: '🖼️',
    video: '🎬',
    screenshot: '📸',
    clip: '🎞️',
};

// Expose for layout FAB
defineExpose({ showAddModal });
</script>

<template>
    <Head title="Keepsake" />

    <AuthenticatedLayout @open-add-modal="showAddModal = true">
        <div class="min-h-screen">
            <!-- Hero / Search Section -->
            <div class="bg-gradient-to-b from-gray-900 via-gray-900 to-transparent px-4 pt-6 pb-4 sm:px-6 lg:px-8">
                <div class="mx-auto max-w-7xl">
                    <!-- Search -->
                    <div class="max-w-2xl mx-auto mb-6">
                        <SearchBar v-model="search" />
                    </div>

                    <!-- Type filters -->
                    <div class="flex flex-wrap items-center justify-center gap-2 mb-4">
                        <button
                            @click="setType('')"
                            class="rounded-xl px-4 py-2 text-sm font-medium transition-all duration-200"
                            :class="!activeType ? 'bg-indigo-600 text-white shadow-lg shadow-indigo-500/25' : 'bg-gray-800 text-gray-400 hover:bg-gray-700 hover:text-gray-200'"
                        >
                            All
                        </button>
                        <button
                            v-for="type in types"
                            :key="type"
                            @click="setType(type)"
                            class="rounded-xl px-4 py-2 text-sm font-medium transition-all duration-200"
                            :class="activeType === type ? 'bg-indigo-600 text-white shadow-lg shadow-indigo-500/25' : 'bg-gray-800 text-gray-400 hover:bg-gray-700 hover:text-gray-200'"
                        >
                            {{ typeIcons[type] }} {{ type.charAt(0).toUpperCase() + type.slice(1) }}s
                        </button>
                        <button
                            @click="togglePinned"
                            class="rounded-xl px-4 py-2 text-sm font-medium transition-all duration-200"
                            :class="pinnedOnly ? 'bg-amber-600 text-white shadow-lg shadow-amber-500/25' : 'bg-gray-800 text-gray-400 hover:bg-gray-700 hover:text-gray-200'"
                        >
                            ⭐ Pinned
                        </button>
                    </div>

                    <!-- Tags & Categories row -->
                    <div v-if="tags.length || categories.length" class="flex flex-wrap items-center justify-center gap-2">
                        <TagBadge
                            v-for="tag in tags"
                            :key="tag.id"
                            :tag="tag"
                            :selected="activeTag === tag.slug"
                            @click="setTag(tag.slug)"
                            small
                        />
                        <span v-if="tags.length && categories.length" class="text-gray-700 mx-1">|</span>
                        <button
                            v-for="cat in categories"
                            :key="cat.id"
                            @click="setCategory(cat.slug)"
                            class="inline-flex items-center gap-1 rounded-full px-2.5 py-0.5 text-xs font-medium transition-all duration-200"
                            :class="activeCategory === cat.slug ? 'bg-violet-600/30 text-violet-300 ring-1 ring-violet-500' : 'bg-gray-800 text-gray-500 hover:text-gray-300'"
                        >
                            {{ cat.icon || '📁' }} {{ cat.name }}
                        </button>

                        <button
                            v-if="hasFilters"
                            @click="clearFilters"
                            class="ml-2 text-xs text-gray-600 hover:text-gray-400 underline transition"
                        >
                            Clear filters
                        </button>
                    </div>
                </div>
            </div>

            <!-- Items Grid -->
            <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 pb-12">
                <div v-if="items.data.length" class="columns-1 sm:columns-2 lg:columns-3 xl:columns-4 gap-4">
                    <ItemCard
                        v-for="item in items.data"
                        :key="item.id"
                        :item="item"
                    />
                </div>

                <EmptyState
                    v-else
                    :title="hasFilters ? 'No items match your filters' : 'Your Keepsake is empty'"
                    :description="hasFilters ? 'Try adjusting your search or filters.' : 'Start saving links, images, videos, and more!'"
                    :icon="hasFilters ? '🔍' : '🗃️'"
                    :action-label="hasFilters ? null : '+ Save your first item'"
                    @action="showAddModal = true"
                />

                <!-- Pagination -->
                <div v-if="items.last_page > 1" class="mt-8 flex justify-center gap-2">
                    <template v-for="link in items.links" :key="link.label">
                        <button
                            v-if="link.url"
                            @click="router.get(link.url, {}, { preserveState: true, preserveScroll: true })"
                            class="rounded-lg px-3 py-2 text-sm transition-all duration-200"
                            :class="link.active ? 'bg-indigo-600 text-white' : 'bg-gray-800 text-gray-400 hover:bg-gray-700'"
                            v-html="link.label"
                        />
                        <span
                            v-else
                            class="rounded-lg px-3 py-2 text-sm text-gray-600"
                            v-html="link.label"
                        />
                    </template>
                </div>
            </div>
        </div>

        <!-- Add Item Modal -->
        <AddItemModal
            :show="showAddModal"
            :tags="tags"
            :categories="categories"
            @close="showAddModal = false"
        />
    </AuthenticatedLayout>
</template>
