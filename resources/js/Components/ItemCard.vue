<script setup>
import { computed } from 'vue';
import { router } from '@inertiajs/vue3';
import TagBadge from '@/Components/TagBadge.vue';
import TypeIcon from '@/Components/TypeIcon.vue';

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

const hasMedia = computed(() => (
    (isMedia.value || isVideo.value) && !!props.item.file_url
) || isLink.value);

const typeIcon = computed(() => (
    ['link', 'image', 'video', 'screenshot', 'clip'].includes(props.item.type) ? props.item.type : 'file'
));

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

// The lit edge follows the pointer.
function track(e) {
    const r = e.currentTarget.getBoundingClientRect();
    e.currentTarget.style.setProperty('--mx', `${e.clientX - r.left}px`);
    e.currentTarget.style.setProperty('--my', `${e.clientY - r.top}px`);
}
</script>

<template>
    <div
        class="card"
        :class="{ 'card--text': !hasMedia }"
        tabindex="0"
        @click="openItem"
        @keydown.enter.self="openItem"
        @pointermove="track"
    >
        <!-- Pin button -->
        <button
            @click.stop="togglePin"
            class="card__act card__act--pin"
            :aria-pressed="!!item.is_pinned"
            :aria-label="item.is_pinned ? 'Unpin' : 'Pin'"
        >
            <TypeIcon name="star" :filled="!!item.is_pinned" />
        </button>

        <!-- Delete button -->
        <button @click.stop="deleteItem" class="card__act card__act--del" aria-label="Delete">
            <TypeIcon name="trash" />
        </button>

        <!-- Image / Video thumbnail -->
        <div v-if="isMedia && item.file_url" class="card__media">
            <img :src="item.file_url" :alt="item.title" loading="lazy" />
        </div>
        <div v-else-if="isVideo && item.file_url" class="card__media card__media--wide">
            <div class="card__play"><span><TypeIcon name="play" filled /></span></div>
        </div>
        <div v-else-if="isLink && item.thumbnail_url" class="card__media card__media--wide">
            <img :src="item.thumbnail_url" :alt="item.title" loading="lazy" />
        </div>
        <div v-else-if="isLink" class="card__poster">
            <TypeIcon name="link" />
            <b>{{ domain || item.title }}</b>
        </div>

        <!-- Content -->
        <div class="card__body">
            <h3 class="card__title">{{ item.title }}</h3>

            <p v-if="item.description" class="card__desc">{{ item.description }}</p>

            <div class="card__meta" style="margin: var(--space-xs) 0 0">
                <TypeIcon :name="typeIcon" />
                <span class="kind">{{ item.type }}</span>
                <span v-if="domain" class="domain">{{ domain }}</span>
            </div>

            <div v-if="item.category" class="card__cat">
                <span v-if="item.category.icon">{{ item.category.icon }}</span>
                <TypeIcon v-else name="folder" />
                <span>{{ item.category.name }}</span>
            </div>

            <div v-if="item.tags?.length" class="card__tags">
                <TagBadge v-for="tag in item.tags" :key="tag.id" :tag="tag" small />
            </div>
        </div>
    </div>
</template>
