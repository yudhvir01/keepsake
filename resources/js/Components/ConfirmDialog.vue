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
        <div class="p-6 bg-gray-800">
            <h3 class="text-lg font-semibold text-gray-100">{{ title }}</h3>
            <p class="mt-2 text-sm text-gray-400">{{ message }}</p>
            <div class="mt-6 flex justify-end gap-3">
                <SecondaryButton @click="$emit('cancel')">{{ cancelLabel }}</SecondaryButton>
                <DangerButton v-if="variant === 'danger'" @click="$emit('confirm')">{{ confirmLabel }}</DangerButton>
                <button v-else @click="$emit('confirm')" class="rounded-lg bg-amber-600 px-4 py-2 text-sm font-medium text-white hover:bg-amber-500 transition">{{ confirmLabel }}</button>
            </div>
        </div>
    </Modal>
</template>
