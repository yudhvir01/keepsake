<script setup>
import { ref } from 'vue';
import { Head, router, useForm } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import TagBadge from '@/Components/TagBadge.vue';
import ConfirmDialog from '@/Components/ConfirmDialog.vue';
import TypeIcon from '@/Components/TypeIcon.vue';

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

const typeIcon = ['link', 'image', 'video', 'screenshot', 'clip'].includes(props.item.type) ? props.item.type : 'file';
const domain = (() => {
    try { return new URL(props.item.url).hostname.replace('www.', ''); } catch { return ''; }
})();
const createdOn = new Date(props.item.created_at).toLocaleDateString('en-US', { year: 'numeric', month: 'short', day: 'numeric' });
</script>

<template>
    <Head :title="item.title" />

    <AuthenticatedLayout>
        <div class="page page--narrow">
            <!-- Back button -->
            <div class="page-head" style="padding-block: var(--space-md) var(--space-xs)">
                <button @click="router.get(route('dashboard'))" class="backlink">
                    <TypeIcon name="back" />
                    Back to Keepsake
                </button>
            </div>

            <article class="glass detail rise" style="overflow: hidden">
                <!-- Media display -->
                <div v-if="isMedia && item.file_url" class="detail__media">
                    <img :src="item.file_url" :alt="item.title" />
                </div>
                <div v-else-if="isVideo && item.file_url" class="detail__media">
                    <video controls preload="metadata" :poster="item.thumbnail_url || undefined">
                        <source :src="item.file_url" />
                    </video>
                </div>
                <div v-else-if="isLink && item.thumbnail_url" class="detail__media">
                    <img :src="item.thumbnail_url" :alt="item.title" />
                </div>
                <div v-else class="card__poster" style="position: relative; aspect-ratio: 21 / 8">
                    <TypeIcon :name="typeIcon" />
                    <b v-if="domain">{{ domain }}</b>
                </div>

                <!-- Content -->
                <div class="sheet" style="padding: var(--space-lg) var(--space-lg) var(--space-xl)">
                    <!-- Actions bar -->
                    <div class="sheet__head">
                        <div class="card__meta" style="margin: 0">
                            <TypeIcon :name="typeIcon" />
                            <span class="kind">{{ item.type }}</span>
                            <span>{{ createdOn }}</span>
                        </div>
                        <div class="row" style="gap: var(--space-2xs)">
                            <button
                                @click="togglePin"
                                class="iconbtn iconbtn--tint"
                                :aria-pressed="!!item.is_pinned"
                                :aria-label="item.is_pinned ? 'Unpin' : 'Pin'"
                            >
                                <TypeIcon name="star" :filled="!!item.is_pinned" />
                            </button>
                            <button @click="editing = !editing" class="iconbtn iconbtn--tint" :aria-pressed="editing" aria-label="Edit">
                                <TypeIcon name="edit" />
                            </button>
                            <button @click="showDeleteConfirm = true" class="iconbtn iconbtn--tint iconbtn--danger" aria-label="Delete">
                                <TypeIcon name="trash" />
                            </button>
                        </div>
                    </div>

                    <!-- View / Edit mode -->
                    <div v-if="!editing" class="stack--sm stack">
                        <h1 class="detail__title">{{ item.title }}</h1>
                        <p v-if="item.description" class="muted" style="white-space: pre-wrap">{{ item.description }}</p>

                        <div>
                            <a
                                v-if="item.url"
                                :href="item.url"
                                target="_blank"
                                rel="noopener noreferrer"
                                class="btn btn--ghost btn--sm"
                            >
                                <TypeIcon name="external" />
                                Open Link
                            </a>
                        </div>

                        <div v-if="item.category" class="card__cat" style="margin: 0; font-size: var(--text-sm)">
                            <span v-if="item.category.icon">{{ item.category.icon }}</span>
                            <TypeIcon v-else name="folder" />
                            <span>{{ item.category.name }}</span>
                        </div>

                        <div v-if="item.tags?.length" class="chips">
                            <TagBadge v-for="tag in item.tags" :key="tag.id" :tag="tag" />
                        </div>
                    </div>

                    <!-- Edit form -->
                    <form v-else @submit.prevent="save" class="formgrid">
                        <div>
                            <label class="label" for="edit-title">Title</label>
                            <input id="edit-title" v-model="form.title" class="field" />
                        </div>
                        <div>
                            <label class="label" for="edit-description">Description</label>
                            <textarea id="edit-description" v-model="form.description" rows="3" class="field"></textarea>
                        </div>
                        <div v-if="isLink">
                            <label class="label" for="edit-url">URL</label>
                            <input id="edit-url" v-model="form.url" type="url" class="field" />
                        </div>
                        <div>
                            <label class="label" for="edit-category">Category</label>
                            <select id="edit-category" v-model="form.category_id" class="field">
                                <option :value="null">None</option>
                                <option v-for="cat in allCategories" :key="cat.id" :value="cat.id">{{ cat.icon }} {{ cat.name }}</option>
                            </select>
                        </div>
                        <div>
                            <span class="label">Tags</span>
                            <div class="chips">
                                <TagBadge
                                    v-for="tag in allTags"
                                    :key="tag.id"
                                    :tag="tag"
                                    :selected="form.tags.includes(tag.id)"
                                    @click="toggleTag(tag.id)"
                                />
                            </div>
                        </div>
                        <div class="actions" style="justify-content: flex-start">
                            <button type="submit" :disabled="form.processing" class="btn">
                                {{ form.processing ? 'Saving...' : 'Save Changes' }}
                            </button>
                            <button type="button" @click="editing = false" class="btn btn--ghost">Cancel</button>
                        </div>
                    </form>
                </div>
            </article>
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
