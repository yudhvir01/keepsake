<script setup>
import { Link } from '@inertiajs/vue3';

defineProps({
    href: {
        type: String,
        required: true,
    },
    active: {
        type: Boolean,
    },
});
</script>

<template>
    <Link :href="href" class="navlink" :aria-current="active ? 'page' : undefined">
        <slot />
    </Link>
</template>
