<script setup>
import { Link } from '@inertiajs/vue3';
import ApplicationLogo from '@/Components/ApplicationLogo.vue';
import Backdrop from '@/Components/Backdrop.vue';
import PhotoCredit from '@/Components/PhotoCredit.vue';
</script>

<template>
    <div class="auth">
        <Backdrop />

        <div class="auth__stack">
            <Link :href="route('login')" class="brand">
                <ApplicationLogo />
                Keepsake
            </Link>

            <div class="auth__card glass glass--strong glass--pop">
                <slot />
            </div>

            <PhotoCredit />
        </div>
    </div>
</template>
