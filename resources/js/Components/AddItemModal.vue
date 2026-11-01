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
import TypeIcon from '@/Components/TypeIcon.vue';

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
    { value: 'image', label: 'Image', icon: 'image' },
    { value: 'video', label: 'Video', icon: 'video' },
    { value: 'screenshot', label: 'Screenshot', icon: 'screenshot' },
    { value: 'clip', label: 'Clip', icon: 'clip' },
];
</script>

<template>
    <Modal :show="show" @close="close" max-width="2xl">
        <div class="sheet">
            <!-- Header -->
            <div class="sheet__head">
                <h2 class="sheet__title">Save to Keepsake</h2>
                <button @click="close" class="iconbtn" aria-label="Close">
                    <TypeIcon name="close" />
                </button>
            </div>

            <!-- Mode tabs -->
            <div class="tabs" role="tablist" style="margin-bottom: var(--space-lg)">
                <button
                    @click="switchMode('link')"
                    class="tab"
                    role="tab"
                    :aria-selected="mode === 'link'"
                    type="button"
                >
                    <TypeIcon name="link" />
                    Save Link
                </button>
                <button
                    @click="switchMode('upload')"
                    class="tab"
                    role="tab"
                    :aria-selected="mode === 'upload'"
                    type="button"
                >
                    <TypeIcon name="upload" />
                    Upload File
                </button>
            </div>

            <form @submit.prevent="submit" class="formgrid">
                <!-- URL input (link mode) -->
                <div v-if="mode === 'link'">
                    <InputLabel for="item-url" value="URL" />
                    <TextInput
                        id="item-url"
                        v-model="form.url"
                        type="url"
                        placeholder="https://reddit.com/r/..."
                    />
                    <InputError :message="form.errors.url" />
                </div>

                <!-- File upload (upload mode) -->
                <div v-if="mode === 'upload'">
                    <!-- Type selector -->
                    <div class="chips" style="margin-bottom: var(--space-md)">
                        <button
                            v-for="t in uploadTypes"
                            :key="t.value"
                            type="button"
                            @click="form.type = t.value"
                            class="chip"
                            :aria-pressed="form.type === t.value"
                        >
                            <TypeIcon :name="t.icon" />
                            {{ t.label }}
                        </button>
                    </div>

                    <!-- Drop zone -->
                    <div
                        @dragover.prevent="dragOver = true"
                        @dragleave="dragOver = false"
                        @drop.prevent="handleDrop"
                        @click="fileInput?.click()"
                        @keydown.enter.prevent="fileInput?.click()"
                        class="drop"
                        role="button"
                        tabindex="0"
                        :data-over="dragOver"
                    >
                        <input ref="fileInput" type="file" class="hidden" @change="handleFileSelect" accept="image/*,video/*" />
                        <template v-if="!form.file">
                            <TypeIcon name="upload" />
                            <p>Drop file here or <u>click to browse</u></p>
                        </template>
                        <div v-else class="drop__file">
                            <TypeIcon :name="form.type === 'image' ? 'image' : 'video'" />
                            <div style="text-align: left">
                                <b style="display: block; font-weight: 500">{{ fileName }}</b>
                                <span class="muted" style="font-size: var(--text-xs)">{{ (form.file.size / 1024 / 1024).toFixed(1) }} MB</span>
                            </div>
                            <button type="button" @click.stop="form.file = null" class="iconbtn" aria-label="Remove file">
                                <TypeIcon name="close" />
                            </button>
                        </div>
                    </div>
                    <InputError :message="form.errors.file" />
                </div>

                <!-- Title -->
                <div>
                    <InputLabel for="item-title" value="Title" />
                    <TextInput
                        id="item-title"
                        v-model="form.title"
                        type="text"
                        placeholder="Give it a name..."
                    />
                    <InputError :message="form.errors.title" />
                </div>

                <!-- Description -->
                <div>
                    <InputLabel for="item-description" value="Description (optional)" />
                    <textarea
                        id="item-description"
                        v-model="form.description"
                        rows="2"
                        placeholder="Notes about this item..."
                        class="field"
                    ></textarea>
                    <InputError :message="form.errors.description" />
                </div>

                <!-- Category -->
                <div>
                    <InputLabel for="item-category" value="Category" />
                    <select id="item-category" v-model="form.category_id" class="field">
                        <option :value="null">No category</option>
                        <option v-for="cat in categories" :key="cat.id" :value="cat.id">
                            {{ cat.icon }} {{ cat.name }}
                        </option>
                    </select>
                </div>

                <!-- Tags -->
                <div>
                    <InputLabel value="Tags" />
                    <div class="chips">
                        <TagBadge
                            v-for="tag in tags"
                            :key="tag.id"
                            :tag="tag"
                            :selected="form.tags.includes(tag.id)"
                            @click="toggleTag(tag.id)"
                        />
                        <span v-if="!tags.length" class="muted" style="font-size: var(--text-sm)">No tags yet. Create some in Tags page.</span>
                    </div>
                </div>

                <!-- Submit -->
                <div class="actions">
                    <SecondaryButton @click="close">Cancel</SecondaryButton>
                    <PrimaryButton :disabled="form.processing" :aria-busy="form.processing">
                        {{ form.processing ? 'Saving...' : 'Save to Keepsake' }}
                    </PrimaryButton>
                </div>
            </form>
        </div>
    </Modal>
</template>
