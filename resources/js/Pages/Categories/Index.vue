<script setup>
import { ref } from 'vue';
import { Head, useForm, router } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import ConfirmDialog from '@/Components/ConfirmDialog.vue';
import TypeIcon from '@/Components/TypeIcon.vue';

const props = defineProps({ categories: Array });

const showForm = ref(false);
const editingCat = ref(null);
const showDeleteConfirm = ref(false);
const catToDelete = ref(null);

// Icon choices stored with a category, written as code points.
const emojis = ['\u{1F4BC}', '\u{1F3A8}', '\u{1F4A1}', '\u{1F525}', '\u{1F48E}', '\u{1F3AF}', '\u{1F4DA}', '\u{1F3B5}', '\u{1F3AE}', '\u{1F3CB}\u{FE0F}', '\u{1F355}', '\u{2708}\u{FE0F}', '\u{1F4B0}', '\u{2764}\u{FE0F}', '\u{1F6E0}\u{FE0F}'];

const form = useForm({ name: '', icon: '' });

function startCreate() {
    editingCat.value = null;
    form.reset();
    showForm.value = true;
}

function startEdit(cat) {
    editingCat.value = cat;
    form.name = cat.name;
    form.icon = cat.icon || '';
    showForm.value = true;
}

function submit() {
    if (editingCat.value) {
        form.put(route('categories.update', editingCat.value.id), {
            preserveScroll: true,
            onSuccess: () => { showForm.value = false; form.reset(); },
        });
    } else {
        form.post(route('categories.store'), {
            preserveScroll: true,
            onSuccess: () => { showForm.value = false; form.reset(); },
        });
    }
}

function confirmDelete(cat) {
    catToDelete.value = cat;
    showDeleteConfirm.value = true;
}

function deleteCat() {
    router.delete(route('categories.destroy', catToDelete.value.id), {
        preserveScroll: true,
        onSuccess: () => { showDeleteConfirm.value = false; },
    });
}

function filterByCat(cat) {
    router.get(route('dashboard', { category: cat.slug }));
}
</script>

<template>
    <Head title="Categories" />

    <AuthenticatedLayout>
        <div class="page page--narrow">
            <div class="page-head">
                <div>
                    <h1 class="page-title">Categories</h1>
                    <p class="page-sub">Group your saved items into categories</p>
                </div>
                <button @click="startCreate" class="btn">
                    <TypeIcon name="plus" />
                    New Category
                </button>
            </div>

            <!-- Create/Edit Form -->
            <Transition name="slide">
                <div v-if="showForm" class="glass panel" style="margin-bottom: var(--space-lg)">
                    <h2 class="panel-title" style="margin-bottom: var(--space-md)">{{ editingCat ? 'Edit Category' : 'New Category' }}</h2>
                    <form @submit.prevent="submit" class="formrow" style="align-items: flex-end">
                        <div style="flex: 1; min-width: 200px">
                            <label class="label" for="cat-name">Name</label>
                            <input
                                id="cat-name"
                                v-model="form.name"
                                type="text"
                                placeholder="e.g. Memes, Tutorials, Recipes"
                                class="field"
                                autofocus
                            />
                            <p v-if="form.errors.name" class="err">{{ form.errors.name }}</p>
                        </div>
                        <div>
                            <span class="label">Icon</span>
                            <div class="chips" style="gap: 6px; max-width: 360px">
                                <button
                                    v-for="e in emojis"
                                    :key="e"
                                    type="button"
                                    @click="form.icon = e"
                                    class="emoji-btn"
                                    :aria-pressed="form.icon === e"
                                    :aria-label="'Icon ' + e"
                                >
                                    {{ e }}
                                </button>
                            </div>
                        </div>
                        <div class="row">
                            <button type="submit" :disabled="form.processing" class="btn">
                                {{ editingCat ? 'Update' : 'Create' }}
                            </button>
                            <button type="button" @click="showForm = false" class="btn btn--ghost">Cancel</button>
                        </div>
                    </form>
                </div>
            </Transition>

            <!-- Categories list -->
            <div v-if="categories.length" class="glass list rise">
                <div
                    v-for="(cat, index) in categories"
                    :key="cat.id"
                    class="list__row"
                    :style="{ '--i': Math.min(index, 12) }"
                >
                    <button @click="filterByCat(cat)" class="list__main linkreset">
                        <span class="cat-icon">
                            <template v-if="cat.icon">{{ cat.icon }}</template>
                            <TypeIcon v-else name="folder" />
                        </span>
                        <span class="list__name">{{ cat.name }}</span>
                        <span class="list__count">{{ cat.items_count || 0 }} items</span>
                    </button>
                    <div class="row row-actions" style="gap: var(--space-2xs)">
                        <button @click="startEdit(cat)" class="iconbtn" :aria-label="'Edit ' + cat.name">
                            <TypeIcon name="edit" />
                        </button>
                        <button @click="confirmDelete(cat)" class="iconbtn iconbtn--danger" :aria-label="'Delete ' + cat.name">
                            <TypeIcon name="trash" />
                        </button>
                    </div>
                </div>
            </div>

            <div v-else class="empty glass rise">
                <span class="empty__icon"><TypeIcon name="folder" /></span>
                <p>No categories yet. Create your first one!</p>
            </div>
        </div>

        <ConfirmDialog
            :show="showDeleteConfirm"
            title="Delete category?"
            :message="`Items in “${catToDelete?.name}” won’t be deleted, just uncategorized.`"
            @confirm="deleteCat"
            @cancel="showDeleteConfirm = false"
        />
    </AuthenticatedLayout>
</template>
