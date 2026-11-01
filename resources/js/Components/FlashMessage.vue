<script setup>
import { ref, watch } from 'vue';
import { usePage } from '@inertiajs/vue3';
import TypeIcon from '@/Components/TypeIcon.vue';

const page = usePage();
const show = ref(false);
const flash = ref({ type: '', message: '' });
let timer = null;

watch(
    () => page.props.flash,
    (val) => {
        if (val?.message) {
            flash.value = val;
            show.value = true;
            clearTimeout(timer);
            timer = setTimeout(() => { show.value = false; }, 3500);
        }
    },
    { immediate: true, deep: true }
);
</script>

<template>
    <Transition name="slide">
        <div
            v-if="show"
            class="toast glass glass--strong glass--pop"
            :class="'toast--' + (flash.type || 'info')"
            role="status"
        >
            <TypeIcon :name="flash.type === 'error' ? 'alert' : flash.type === 'success' ? 'check-circle' : 'info'" class="i" />
            <p>{{ flash.message }}</p>
            <button @click="show = false" class="iconbtn" aria-label="Dismiss">
                <TypeIcon name="close" />
            </button>
        </div>
    </Transition>
</template>
