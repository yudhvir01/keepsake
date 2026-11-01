<script setup>
import { ref } from 'vue';
import { Head, useForm, router } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import ConfirmDialog from '@/Components/ConfirmDialog.vue';
import TypeIcon from '@/Components/TypeIcon.vue';

const props = defineProps({ tags: Array });

const showForm = ref(false);
const editingTag = ref(null);
const showDeleteConfirm = ref(false);
const tagToDelete = ref(null);

const colors = ['#6366f1', '#ec4899', '#f59e0b', '#10b981', '#3b82f6', '#8b5cf6', '#ef4444', '#14b8a6', '#f97316', '#06b6d4'];

const form = useForm({ name: '', color: '#6366f1' });

function startCreate() {
    editingTag.value = null;
    form.reset();
    showForm.value = true;
}

function startEdit(tag) {
    editingTag.value = tag;
    form.name = tag.name;
    form.color = tag.color;
    showForm.value = true;
}

function submit() {
    if (editingTag.value) {
        form.put(route('tags.update', editingTag.value.id), {
            preserveScroll: true,
            onSuccess: () => { showForm.value = false; form.reset(); },
        });
    } else {
        form.post(route('tags.store'), {
            preserveScroll: true,
            onSuccess: () => { showForm.value = false; form.reset(); },
        });
    }
}

function confirmDelete(tag) {
    tagToDelete.value = tag;
    showDeleteConfirm.value = true;
}

function deleteTag() {
    router.delete(route('tags.destroy', tagToDelete.value.id), {
        preserveScroll: true,
        onSuccess: () => { showDeleteConfirm.value = false; },
    });
}

function filterByTag(tag) {
    router.get(route('dashboard', { tag: tag.slug }));
}
</script>

<template>
    <Head title="Tags" />

    <AuthenticatedLayout>
        <div class="page page--narrow">
            <div class="page-head">
                <div>
                    <h1 class="page-title">Tags</h1>
                    <p class="page-sub">Organize your saved items with color-coded tags</p>
                </div>
                <button @click="startCreate" class="btn">
                    <TypeIcon name="plus" />
                    New Tag
                </button>
            </div>

            <!-- Create/Edit Form -->
            <Transition name="slide">
                <div v-if="showForm" class="glass panel" style="margin-bottom: var(--space-lg)">
                    <h2 class="panel-title" style="margin-bottom: var(--space-md)">{{ editingTag ? 'Edit Tag' : 'New Tag' }}</h2>
                    <form @submit.prevent="submit" class="formrow" style="align-items: flex-end">
                        <div style="flex: 1; min-width: 200px">
                            <label class="label" for="tag-name">Name</label>
                            <input
                                id="tag-name"
                                v-model="form.name"
                                type="text"
                                placeholder="e.g. Inspiration"
                                class="field"
                                autofocus
                            />
                            <p v-if="form.errors.name" class="err">{{ form.errors.name }}</p>
                        </div>
                        <div>
                            <span class="label">Color</span>
                            <div class="chips" style="gap: 6px">
                                <button
                                    v-for="c in colors"
                                    :key="c"
                                    type="button"
                                    @click="form.color = c"
                                    class="swatch-btn"
                                    :aria-pressed="form.color === c"
                                    :aria-label="'Color ' + c"
                                    :style="{ '--swatch': c }"
                                />
                            </div>
                        </div>
                        <div class="row">
                            <button type="submit" :disabled="form.processing" class="btn">
                                {{ editingTag ? 'Update' : 'Create' }}
                            </button>
                            <button type="button" @click="showForm = false" class="btn btn--ghost">Cancel</button>
                        </div>
                    </form>
                </div>
            </Transition>

            <!-- Tags list -->
            <div v-if="tags.length" class="glass list rise">
                <div
                    v-for="(tag, index) in tags"
                    :key="tag.id"
                    class="list__row group-row"
                    :style="{ '--i': Math.min(index, 12) }"
                >
                    <button @click="filterByTag(tag)" class="list__main linkreset">
                        <span class="swatch" :style="{ backgroundColor: tag.color }"></span>
                        <span class="list__name">{{ tag.name }}</span>
                        <span class="list__count">{{ tag.items_count || 0 }} items</span>
                    </button>
                    <div class="row row-actions" style="gap: var(--space-2xs)">
                        <button @click="startEdit(tag)" class="iconbtn" :aria-label="'Edit ' + tag.name">
                            <TypeIcon name="edit" />
                        </button>
                        <button @click="confirmDelete(tag)" class="iconbtn iconbtn--danger" :aria-label="'Delete ' + tag.name">
                            <TypeIcon name="trash" />
                        </button>
                    </div>
                </div>
            </div>

            <div v-else class="empty glass rise">
                <span class="empty__icon"><TypeIcon name="tag" /></span>
                <p>No tags yet. Create your first one!</p>
            </div>
        </div>

        <ConfirmDialog
            :show="showDeleteConfirm"
            title="Delete tag?"
            :message="`This will remove the tag “${tagToDelete?.name}” from all items.`"
            @confirm="deleteTag"
            @cancel="showDeleteConfirm = false"
        />
    </AuthenticatedLayout>
</template>
