<script setup>
import { ref } from 'vue';
import { Head, useForm, router } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import ConfirmDialog from '@/Components/ConfirmDialog.vue';

const props = defineProps({ categories: Array });

const showForm = ref(false);
const editingCat = ref(null);
const showDeleteConfirm = ref(false);
const catToDelete = ref(null);

const emojis = ['📁', '💼', '🎨', '💡', '🔥', '💎', '🎯', '📚', '🎵', '🎮', '🏋️', '🍕', '✈️', '💰', '❤️', '🛠️'];

const form = useForm({ name: '', icon: '📁' });

function startCreate() {
    editingCat.value = null;
    form.reset();
    showForm.value = true;
}

function startEdit(cat) {
    editingCat.value = cat;
    form.name = cat.name;
    form.icon = cat.icon || '📁';
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
        <div class="min-h-screen py-8 px-4 sm:px-6 lg:px-8">
            <div class="mx-auto max-w-4xl">
                <div class="flex items-center justify-between mb-8">
                    <div>
                        <h1 class="text-2xl font-bold text-gray-100">📂 Categories</h1>
                        <p class="text-sm text-gray-500 mt-1">Group your saved items into categories</p>
                    </div>
                    <button
                        @click="startCreate"
                        class="inline-flex items-center gap-2 rounded-xl bg-indigo-600 px-4 py-2.5 text-sm font-semibold text-white shadow-lg shadow-indigo-500/25 hover:bg-indigo-500 transition"
                    >
                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                        </svg>
                        New Category
                    </button>
                </div>

                <!-- Create/Edit Form -->
                <Transition
                    enter-active-class="transition ease-out duration-200"
                    enter-from-class="opacity-0 -translate-y-2"
                    enter-to-class="opacity-100 translate-y-0"
                    leave-active-class="transition ease-in duration-150"
                    leave-from-class="opacity-100"
                    leave-to-class="opacity-0"
                >
                    <div v-if="showForm" class="mb-6 rounded-2xl bg-gray-800 border border-gray-700/50 p-6">
                        <h3 class="text-sm font-semibold text-gray-300 mb-4">{{ editingCat ? 'Edit Category' : 'New Category' }}</h3>
                        <form @submit.prevent="submit" class="flex flex-wrap items-end gap-4">
                            <div class="flex-1 min-w-[200px]">
                                <label class="text-xs text-gray-500 mb-1 block">Name</label>
                                <input
                                    v-model="form.name"
                                    type="text"
                                    placeholder="e.g. Memes, Tutorials, Recipes"
                                    class="w-full rounded-lg bg-gray-900 border-gray-700 text-gray-100 text-sm focus:border-indigo-500 focus:ring-indigo-500"
                                    autofocus
                                />
                                <p v-if="form.errors.name" class="text-xs text-red-400 mt-1">{{ form.errors.name }}</p>
                            </div>
                            <div>
                                <label class="text-xs text-gray-500 mb-1 block">Icon</label>
                                <div class="flex flex-wrap gap-1.5 max-w-xs">
                                    <button
                                        v-for="e in emojis"
                                        :key="e"
                                        type="button"
                                        @click="form.icon = e"
                                        class="h-9 w-9 rounded-lg flex items-center justify-center text-lg transition-all duration-150"
                                        :class="form.icon === e ? 'bg-indigo-600/30 ring-2 ring-indigo-500 scale-110' : 'bg-gray-900 hover:bg-gray-700 hover:scale-110'"
                                    >
                                        {{ e }}
                                    </button>
                                </div>
                            </div>
                            <div class="flex gap-2">
                                <button type="submit" :disabled="form.processing" class="rounded-lg bg-indigo-600 px-4 py-2 text-sm font-medium text-white hover:bg-indigo-500 transition">
                                    {{ editingCat ? 'Update' : 'Create' }}
                                </button>
                                <button type="button" @click="showForm = false" class="rounded-lg bg-gray-700 px-4 py-2 text-sm text-gray-300 hover:bg-gray-600 transition">Cancel</button>
                            </div>
                        </form>
                    </div>
                </Transition>

                <!-- Categories grid -->
                <div v-if="categories.length" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3">
                    <div
                        v-for="cat in categories"
                        :key="cat.id"
                        class="group flex items-center justify-between rounded-xl bg-gray-800/80 border border-gray-700/50 p-4 hover:border-gray-600/50 transition-all duration-200"
                    >
                        <button @click="filterByCat(cat)" class="flex items-center gap-3 flex-1 min-w-0 text-left">
                            <span class="text-2xl">{{ cat.icon || '📁' }}</span>
                            <div class="min-w-0">
                                <p class="font-medium text-gray-200 truncate">{{ cat.name }}</p>
                                <p class="text-xs text-gray-600">{{ cat.items_count || 0 }} items</p>
                            </div>
                        </button>
                        <div class="flex items-center gap-1 opacity-0 group-hover:opacity-100 transition">
                            <button @click="startEdit(cat)" class="rounded-lg p-1.5 text-gray-500 hover:text-indigo-400 hover:bg-gray-700 transition">
                                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L6.832 19.82a4.5 4.5 0 0 1-1.897 1.13l-2.685.8.8-2.685a4.5 4.5 0 0 1 1.13-1.897L16.863 4.487Z" /></svg>
                            </button>
                            <button @click="confirmDelete(cat)" class="rounded-lg p-1.5 text-gray-500 hover:text-red-400 hover:bg-gray-700 transition">
                                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0" /></svg>
                            </button>
                        </div>
                    </div>
                </div>

                <div v-else class="text-center py-16">
                    <span class="text-5xl mb-4 block">📂</span>
                    <p class="text-gray-400">No categories yet. Create your first one!</p>
                </div>
            </div>
        </div>

        <ConfirmDialog
            :show="showDeleteConfirm"
            title="Delete category?"
            :message="`Items in '${catToDelete?.name}' won't be deleted, just uncategorized.`"
            @confirm="deleteCat"
            @cancel="showDeleteConfirm = false"
        />
    </AuthenticatedLayout>
</template>
