<script setup>
import { ref, watch, computed } from 'vue';
import { Head, router } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import ItemCard from '@/Components/ItemCard.vue';
import SearchBar from '@/Components/SearchBar.vue';
import TagBadge from '@/Components/TagBadge.vue';
import EmptyState from '@/Components/EmptyState.vue';
import AddItemModal from '@/Components/AddItemModal.vue';
import TypeIcon from '@/Components/TypeIcon.vue';

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

// Expose for layout FAB
defineExpose({ showAddModal });
</script>

<template>
    <Head title="Keepsake" />

    <AuthenticatedLayout @open-add-modal="showAddModal = true">
        <div class="page">
            <!-- Search and filters -->
            <div class="toolbar glass rise">
                <div class="toolbar__search">
                    <SearchBar v-model="search" />
                </div>

                <!-- Type filters -->
                <div class="chips">
                    <button
                        @click="setType('')"
                        class="chip"
                        :aria-pressed="!activeType"
                    >
                        All
                    </button>
                    <button
                        v-for="type in types"
                        :key="type"
                        @click="setType(type)"
                        class="chip"
                        :aria-pressed="activeType === type"
                    >
                        <TypeIcon :name="type" />
                        {{ type.charAt(0).toUpperCase() + type.slice(1) }}s
                    </button>
                    <button
                        @click="togglePinned"
                        class="chip chip--pin"
                        :aria-pressed="pinnedOnly"
                    >
                        <TypeIcon name="star" :filled="pinnedOnly" />
                        Pinned
                    </button>
                </div>

                <!-- Tags & Categories row -->
                <div v-if="tags.length || categories.length" class="chips">
                    <TagBadge
                        v-for="tag in tags"
                        :key="tag.id"
                        :tag="tag"
                        :selected="activeTag === tag.slug"
                        @click="setTag(tag.slug)"
                        small
                    />
                    <span v-if="tags.length && categories.length" class="chip-sep" aria-hidden="true"></span>
                    <button
                        v-for="cat in categories"
                        :key="cat.id"
                        @click="setCategory(cat.slug)"
                        class="chip"
                        :aria-pressed="activeCategory === cat.slug"
                    >
                        <span v-if="cat.icon">{{ cat.icon }}</span>
                        <TypeIcon v-else name="folder" />
                        {{ cat.name }}
                    </button>

                    <button
                        v-if="hasFilters"
                        @click="clearFilters"
                        class="linkbtn"
                    >
                        Clear filters
                    </button>
                </div>
            </div>

            <!-- Items Grid -->
            <div v-if="items.data.length" class="wall">
                <ItemCard
                    v-for="(item, index) in items.data"
                    :key="item.id"
                    :item="item"
                    :style="{ '--i': Math.min(index, 14) }"
                />
            </div>

            <EmptyState
                v-else
                :title="hasFilters ? 'No items match your filters' : 'Your Keepsake is empty'"
                :description="hasFilters ? 'Try adjusting your search or filters.' : 'Start saving links, images, videos, and more!'"
                :kind="hasFilters ? 'search' : 'vault'"
                :action-label="hasFilters ? null : 'Save your first item'"
                @action="showAddModal = true"
            />

            <!-- Pagination -->
            <div v-if="items.last_page > 1" class="pager">
                <template v-for="link in items.links" :key="link.label">
                    <button
                        v-if="link.url"
                        @click="router.get(link.url, {}, { preserveState: true, preserveScroll: true })"
                        :aria-current="link.active ? 'page' : undefined"
                        v-html="link.label"
                    />
                    <span
                        v-else
                        v-html="link.label"
                    />
                </template>
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
