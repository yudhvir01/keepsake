<script setup>
import TypeIcon from '@/Components/TypeIcon.vue';

defineProps({
    tag: { type: Object, required: true },
    removable: { type: Boolean, default: false },
    selected: { type: Boolean, default: false },
    small: { type: Boolean, default: false },
});

defineEmits(['click', 'remove']);
</script>

<template>
    <span
        @click="$emit('click', tag)"
        @keydown.enter.prevent="$emit('click', tag)"
        role="button"
        tabindex="0"
        class="tag"
        :class="small ? 'tag--sm' : 'tag--md'"
        :aria-pressed="selected"
        :style="{
            backgroundColor: tag.color + '25',
            color: tag.color,
            borderColor: tag.color + '40',
        }"
    >
        <TypeIcon v-if="selected" name="check" />
        {{ tag.name }}
        <button
            v-if="removable"
            type="button"
            @click.stop="$emit('remove', tag)"
            :aria-label="'Remove ' + tag.name"
        >
            <TypeIcon name="close" />
        </button>
    </span>
</template>
