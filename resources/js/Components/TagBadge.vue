<script setup>
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
        class="inline-flex items-center gap-1 rounded-full font-medium cursor-pointer transition-all duration-150"
        :class="[
            small ? 'px-2 py-0.5 text-xs' : 'px-3 py-1 text-sm',
            selected ? 'ring-2 ring-white/30 shadow-lg scale-105' : 'hover:scale-105',
        ]"
        :style="{
            backgroundColor: tag.color + '25',
            color: tag.color,
            borderColor: tag.color + '40',
            borderWidth: '1px',
        }"
    >
        <svg v-if="selected" class="h-3 w-3" fill="currentColor" viewBox="0 0 20 20">
            <path fill-rule="evenodd" d="M16.704 4.153a.75.75 0 01.143 1.052l-8 10.5a.75.75 0 01-1.127.075l-4.5-4.5a.75.75 0 011.06-1.06l3.894 3.893 7.48-9.817a.75.75 0 011.05-.143z" clip-rule="evenodd" />
        </svg>
        {{ tag.name }}
        <button
            v-if="removable"
            @click.stop="$emit('remove', tag)"
            class="ml-0.5 hover:opacity-75"
        >
            <svg class="h-3 w-3" fill="currentColor" viewBox="0 0 20 20">
                <path d="M6.28 5.22a.75.75 0 00-1.06 1.06L8.94 10l-3.72 3.72a.75.75 0 101.06 1.06L10 11.06l3.72 3.72a.75.75 0 101.06-1.06L11.06 10l3.72-3.72a.75.75 0 00-1.06-1.06L10 8.94 6.28 5.22z" />
            </svg>
        </button>
    </span>
</template>
