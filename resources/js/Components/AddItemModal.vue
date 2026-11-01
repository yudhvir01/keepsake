<script setup>
import { ref, computed } from 'vue';
import { useForm } from '@inertiajs/vue3';
import Modal from '@/Components/Modal.vue';
import InputLabel from '@/Components/InputLabel.vue';
import TextInput from '@/Components/TextInput.vue';
import InputError from '@/Components/InputError.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import TagBadge from '@/Components/TagBadge.vue';

const props = defineProps({
    show: { type: Boolean, default: false },
    tags: { type: Array, default: () => [] },
    categories: { type: Array, default: () => [] },
});

const emit = defineEmits(['close']);

const mode = ref('link'); // 'link' or 'upload'
const fileInput = ref(null);
const dragOver = ref(false);

const form = useForm({
    type: 'link',
    title: '',
    description: '',
    url: '',
    file: null,
    category_id: null,
    tags: [],
});

const fileName = computed(() => form.file?.name || '');

function switchMode(m) {
    mode.value = m;
    form.type = m === 'link' ? 'link' : 'image';
    form.url = '';
    form.file = null;
}

function toggleTag(tagId) {
    const idx = form.tags.indexOf(tagId);
    if (idx >= 0) {
        form.tags.splice(idx, 1);
    } else {
        form.tags.push(tagId);
    }
}

function handleFileSelect(e) {
    const file = e.target.files?.[0];
    if (file) setFile(file);
}

function handleDrop(e) {
    dragOver.value = false;
    const file = e.dataTransfer?.files?.[0];
    if (file) setFile(file);
}

function setFile(file) {
    form.file = file;
    if (!form.title) {
        form.title = file.name.replace(/\.[^.]+$/, '').replace(/[-_]/g, ' ');
    }
    // Auto-detect type
    if (file.type.startsWith('image/')) form.type = 'image';
    else if (file.type.startsWith('video/')) form.type = 'video';
    else form.type = 'clip';
}

function submit() {
    form.post(route('items.store'), {
        preserveScroll: true,
        forceFormData: true,
        onSuccess: () => {
            form.reset();
            emit('close');
        },
    });
}

function close() {
    form.reset();
    mode.value = 'link';
    emit('close');
}

const uploadTypes = [
    { value: 'image', label: '🖼️ Image' },
    { value: 'video', label: '🎬 Video' },
    { value: 'screenshot', label: '📸 Screenshot' },
    { value: 'clip', label: '🎞️ Clip' },
];
</script>

<template>
    <Modal :show="show" @close="close" max-width="2xl">
        <div class="bg-gray-800 p-6">
            <!-- Header -->
            <div class="flex items-center justify-between mb-6">
                <h2 class="text-xl font-bold text-gray-100">Save to Keepsake</h2>
                <button @click="close" class="text-gray-500 hover:text-gray-300 transition">
                    <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            <!-- Mode tabs -->
            <div class="flex rounded-xl bg-gray-900 p-1 mb-6">
                <button
                    @click="switchMode('link')"
                    class="flex-1 rounded-lg py-2.5 text-sm font-medium transition-all duration-200"
                    :class="mode === 'link' ? 'bg-indigo-600 text-white shadow-lg' : 'text-gray-400 hover:text-gray-200'"
                >
                    🔗 Save Link
                </button>
                <button
                    @click="switchMode('upload')"
                    class="flex-1 rounded-lg py-2.5 text-sm font-medium transition-all duration-200"
                    :class="mode === 'upload' ? 'bg-indigo-600 text-white shadow-lg' : 'text-gray-400 hover:text-gray-200'"
                >
                    📤 Upload File
                </button>
            </div>

            <form @submit.prevent="submit" class="space-y-5">
                <!-- URL input (link mode) -->
                <div v-if="mode === 'link'">
                    <InputLabel value="URL" class="text-gray-300" />
                    <TextInput
                        v-model="form.url"
                        type="url"
                        placeholder="https://reddit.com/r/..."
                        class="mt-1 w-full bg-gray-900 border-gray-700 text-gray-100 placeholder-gray-600 focus:border-indigo-500 focus:ring-indigo-500"
                    />
                    <InputError :message="form.errors.url" class="mt-1" />
                </div>

                <!-- File upload (upload mode) -->
                <div v-if="mode === 'upload'">
                    <!-- Type selector -->
                    <div class="flex gap-2 mb-4">
                        <button
                            v-for="t in uploadTypes"
                            :key="t.value"
                            type="button"
                            @click="form.type = t.value"
                            class="rounded-lg px-3 py-2 text-sm transition-all duration-200"
                            :class="form.type === t.value ? 'bg-indigo-600/20 text-indigo-400 ring-1 ring-indigo-500' : 'bg-gray-900 text-gray-400 hover:text-gray-200'"
                        >
                            {{ t.label }}
                        </button>
                    </div>

                    <!-- Drop zone -->
                    <div
                        @dragover.prevent="dragOver = true"
                        @dragleave="dragOver = false"
                        @drop.prevent="handleDrop"
                        @click="fileInput?.click()"
                        class="relative flex flex-col items-center justify-center rounded-xl border-2 border-dashed py-10 cursor-pointer transition-all duration-200"
                        :class="dragOver ? 'border-indigo-500 bg-indigo-500/10' : 'border-gray-700 bg-gray-900/50 hover:border-gray-500'"
                    >
                        <input ref="fileInput" type="file" class="hidden" @change="handleFileSelect" accept="image/*,video/*" />
                        <svg v-if="!form.file" class="h-10 w-10 text-gray-600 mb-3" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75V16.5m-13.5-9L12 3m0 0 4.5 4.5M12 3v13.5" />
                        </svg>
                        <p v-if="!form.file" class="text-sm text-gray-500">Drop file here or <span class="text-indigo-400">click to browse</span></p>
                        <div v-else class="flex items-center gap-3">
                            <span class="text-2xl">{{ form.type === 'image' ? '🖼️' : '🎬' }}</span>
                            <div>
                                <p class="text-sm font-medium text-gray-200">{{ fileName }}</p>
                                <p class="text-xs text-gray-500">{{ (form.file.size / 1024 / 1024).toFixed(1) }} MB</p>
                            </div>
                            <button type="button" @click.stop="form.file = null" class="text-gray-500 hover:text-red-400">
                                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12" />
                                </svg>
                            </button>
                        </div>
                    </div>
                    <InputError :message="form.errors.file" class="mt-1" />
                </div>

                <!-- Title -->
                <div>
                    <InputLabel value="Title" class="text-gray-300" />
                    <TextInput
                        v-model="form.title"
                        type="text"
                        placeholder="Give it a name..."
                        class="mt-1 w-full bg-gray-900 border-gray-700 text-gray-100 placeholder-gray-600 focus:border-indigo-500 focus:ring-indigo-500"
                    />
                    <InputError :message="form.errors.title" class="mt-1" />
                </div>

                <!-- Description -->
                <div>
                    <InputLabel value="Description (optional)" class="text-gray-300" />
                    <textarea
                        v-model="form.description"
                        rows="2"
                        placeholder="Notes about this item..."
                        class="mt-1 w-full rounded-md bg-gray-900 border-gray-700 text-gray-100 placeholder-gray-600 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                    ></textarea>
                    <InputError :message="form.errors.description" class="mt-1" />
                </div>

                <!-- Category -->
                <div>
                    <InputLabel value="Category" class="text-gray-300" />
                    <select
                        v-model="form.category_id"
                        class="mt-1 w-full rounded-md bg-gray-900 border-gray-700 text-gray-100 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                    >
                        <option :value="null">No category</option>
                        <option v-for="cat in categories" :key="cat.id" :value="cat.id">
                            {{ cat.icon || '📁' }} {{ cat.name }}
                        </option>
                    </select>
                </div>

                <!-- Tags -->
                <div>
                    <InputLabel value="Tags" class="text-gray-300 mb-2" />
                    <div class="flex flex-wrap gap-2">
                        <TagBadge
                            v-for="tag in tags"
                            :key="tag.id"
                            :tag="tag"
                            :selected="form.tags.includes(tag.id)"
                            @click="toggleTag(tag.id)"
                        />
                        <span v-if="!tags.length" class="text-sm text-gray-600 italic">No tags yet. Create some in Tags page.</span>
                    </div>
                </div>

                <!-- Submit -->
                <div class="flex justify-end gap-3 pt-2">
                    <SecondaryButton @click="close">Cancel</SecondaryButton>
                    <PrimaryButton
                        :disabled="form.processing"
                        class="bg-indigo-600 hover:bg-indigo-500"
                    >
                        <svg v-if="form.processing" class="animate-spin -ml-1 mr-2 h-4 w-4" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                        </svg>
                        {{ form.processing ? 'Saving...' : '💾 Save to Keepsake' }}
                    </PrimaryButton>
                </div>
            </form>
        </div>
    </Modal>
</template>
