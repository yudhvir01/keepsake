<script setup>
import Modal from '@/Components/Modal.vue';
import DangerButton from '@/Components/DangerButton.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';

defineProps({
    show: { type: Boolean, default: false },
    title: { type: String, default: 'Are you sure?' },
    message: { type: String, default: 'This action cannot be undone.' },
    confirmLabel: { type: String, default: 'Delete' },
    cancelLabel: { type: String, default: 'Cancel' },
    variant: { type: String, default: 'danger' },
});

defineEmits(['confirm', 'cancel']);
</script>

<template>
    <Modal :show="show" @close="$emit('cancel')" max-width="md">
        <div class="sheet">
            <h3 class="sheet__title">{{ title }}</h3>
            <p class="panel-sub">{{ message }}</p>
            <div class="actions" style="margin-top: var(--space-lg)">
                <SecondaryButton @click="$emit('cancel')">{{ cancelLabel }}</SecondaryButton>
                <DangerButton v-if="variant === 'danger'" @click="$emit('confirm')">{{ confirmLabel }}</DangerButton>
                <button v-else @click="$emit('confirm')" class="btn">{{ confirmLabel }}</button>
            </div>
        </div>
    </Modal>
</template>
