<script setup>
import { computed, onMounted, onUnmounted, ref, watch } from 'vue';

const props = defineProps({
    show: {
        type: Boolean,
        default: false,
    },
    maxWidth: {
        type: String,
        default: '2xl',
    },
    closeable: {
        type: Boolean,
        default: true,
    },
});

const emit = defineEmits(['close']);
const dialog = ref();
const showSlot = ref(props.show);

watch(
    () => props.show,
    () => {
        if (props.show) {
            document.body.style.overflow = 'hidden';
            showSlot.value = true;

            dialog.value?.showModal();
        } else {
            document.body.style.overflow = '';

            setTimeout(() => {
                dialog.value?.close();
                showSlot.value = false;
            }, 200);
        }
    },
);

const close = () => {
    if (props.closeable) {
        emit('close');
    }
};

const closeOnEscape = (e) => {
    if (e.key === 'Escape') {
        e.preventDefault();

        if (props.show) {
            close();
        }
    }
};

onMounted(() => document.addEventListener('keydown', closeOnEscape));

onUnmounted(() => {
    document.removeEventListener('keydown', closeOnEscape);

    document.body.style.overflow = '';
});

const maxWidthPx = computed(() => {
    return {
        sm: '384px',
        md: '448px',
        lg: '512px',
        xl: '576px',
        '2xl': '672px',
    }[props.maxWidth];
});
</script>

<template>
    <dialog class="modal" ref="dialog">
        <div class="modal__scroll" scroll-region>
            <Transition name="veil">
                <div v-show="show" class="modal__veil" @click="close" />
            </Transition>

            <Transition name="pane">
                <div
                    v-show="show"
                    class="glass glass--strong glass--pop modal__panel"
                    :style="{ maxWidth: maxWidthPx }"
                >
                    <slot v-if="showSlot" />
                </div>
            </Transition>
        </div>
    </dialog>
</template>
