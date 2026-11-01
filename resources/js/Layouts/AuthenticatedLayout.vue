<script setup>
import { ref } from 'vue';
import Dropdown from '@/Components/Dropdown.vue';
import DropdownLink from '@/Components/DropdownLink.vue';
import NavLink from '@/Components/NavLink.vue';
import ResponsiveNavLink from '@/Components/ResponsiveNavLink.vue';
import FlashMessage from '@/Components/FlashMessage.vue';
import ApplicationLogo from '@/Components/ApplicationLogo.vue';
import Backdrop from '@/Components/Backdrop.vue';
import PhotoCredit from '@/Components/PhotoCredit.vue';
import TypeIcon from '@/Components/TypeIcon.vue';
import { Link } from '@inertiajs/vue3';

const showingNavigationDropdown = ref(false);

const emit = defineEmits(['open-add-modal']);
</script>

<template>
    <div>
        <Backdrop />

        <nav class="topbar glass glass--strong glass--bar" aria-label="Main">
            <!-- Primary Navigation Menu -->
            <div class="page">
                <div class="topbar__row">
                    <div class="row">
                        <!-- Logo -->
                        <Link :href="route('dashboard')" class="brand">
                            <ApplicationLogo />
                            Keepsake
                        </Link>

                        <!-- Navigation Links -->
                        <div class="nav">
                            <NavLink :href="route('dashboard')" :active="route().current('dashboard')">Dashboard</NavLink>
                            <NavLink :href="route('tags.index')" :active="route().current('tags.*')">Tags</NavLink>
                            <NavLink :href="route('categories.index')" :active="route().current('categories.*')">Categories</NavLink>
                        </div>
                    </div>

                    <div class="account">
                        <!-- Settings Dropdown -->
                        <Dropdown align="right" width="48">
                            <template #trigger>
                                <button type="button" class="menu-btn" aria-haspopup="menu">
                                    <span class="avatar">{{ $page.props.auth.user.name.charAt(0).toUpperCase() }}</span>
                                    {{ $page.props.auth.user.name }}
                                    <TypeIcon name="chevron" />
                                </button>
                            </template>

                            <template #content>
                                <DropdownLink :href="route('profile.edit')">Profile</DropdownLink>
                                <DropdownLink :href="route('logout')" method="post" as="button">Log Out</DropdownLink>
                            </template>
                        </Dropdown>
                    </div>

                    <!-- Hamburger -->
                    <button
                        @click="showingNavigationDropdown = !showingNavigationDropdown"
                        class="burger"
                        :aria-expanded="showingNavigationDropdown"
                        aria-label="Menu"
                    >
                        <TypeIcon :name="showingNavigationDropdown ? 'close' : 'menu'" />
                    </button>
                </div>
            </div>

            <!-- Responsive Navigation Menu -->
            <Transition name="slide">
                <div v-if="showingNavigationDropdown" class="mobile-nav">
                    <ResponsiveNavLink :href="route('dashboard')" :active="route().current('dashboard')">Dashboard</ResponsiveNavLink>
                    <ResponsiveNavLink :href="route('tags.index')" :active="route().current('tags.*')">Tags</ResponsiveNavLink>
                    <ResponsiveNavLink :href="route('categories.index')" :active="route().current('categories.*')">Categories</ResponsiveNavLink>

                    <!-- Responsive Settings Options -->
                    <div class="mobile-who">
                        <b>{{ $page.props.auth.user.name }}</b>
                        <span>{{ $page.props.auth.user.email }}</span>
                    </div>
                    <ResponsiveNavLink :href="route('profile.edit')">Profile</ResponsiveNavLink>
                    <ResponsiveNavLink :href="route('logout')" method="post" as="button">Log Out</ResponsiveNavLink>
                </div>
            </Transition>
        </nav>

        <!-- Page Content -->
        <main>
            <slot />
        </main>

        <footer class="page shell-foot">
            <PhotoCredit />
        </footer>

        <!-- Floating Add Button -->
        <button @click="$emit('open-add-modal')" class="fab" aria-label="Save something new">
            <TypeIcon name="plus" />
        </button>

        <!-- Flash Messages -->
        <FlashMessage />
    </div>
</template>
