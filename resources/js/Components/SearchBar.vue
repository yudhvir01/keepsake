<script setup>
import { onMounted, onUnmounted, ref, watch } from 'vue';
import TypeIcon from '@/Components/TypeIcon.vue';

const props = defineProps({
    modelValue: { type: String, default: '' },
});

const emit = defineEmits(['update:modelValue']);
const search = ref(props.modelValue);
const input = ref(null);
let timeout = null;

watch(search, (val) => {
    clearTimeout(timeout);
    timeout = setTimeout(() => {
        emit('update:modelValue', val);
    }, 300);
});

function clear() {
    search.value = '';
    emit('update:modelValue', '');
    input.value?.focus();
}

function focusOnShortcut(e) {
    if ((e.ctrlKey || e.metaKey) && e.key.toLowerCase() === 'k') {
        e.preventDefault();
        input.value?.focus();
        input.value?.select();
    }
}

onMounted(() => document.addEventListener('keydown', focusOnShortcut));
onUnmounted(() => document.removeEventListener('keydown', focusOnShortcut));
</script>

<template>
    <div class="search">
        <TypeIcon name="search" class="s" />
        <input
            ref="input"
            v-model="search"
            type="text"
            aria-label="Search your keepsakes"
            placeholder="Search your keepsakes…"
            class="field"
            autocomplete="off"
        />
        <button v-if="search" @click="clear" class="clear" aria-label="Clear search">
            <TypeIcon name="close" />
        </button>
        <kbd v-else>Ctrl+K</kbd>
    </div>
</template>
