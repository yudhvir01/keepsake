<script setup>
import TypeIcon from '@/Components/TypeIcon.vue';

defineProps({
    title: { type: String, default: 'Nothing here yet' },
    description: { type: String, default: 'Get started by adding your first item.' },
    kind: { type: String, default: 'vault' },
    actionLabel: { type: String, default: null },
});

defineEmits(['action']);
</script>

<template>
    <div class="empty glass rise">
        <span class="empty__icon"><TypeIcon :name="kind" /></span>
        <h3>{{ title }}</h3>
        <p>{{ description }}</p>
        <button v-if="actionLabel" @click="$emit('action')" class="btn">
            <TypeIcon name="plus" />
            {{ actionLabel }}
        </button>
    </div>
</template>
