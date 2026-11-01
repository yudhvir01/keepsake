<script setup>
import { Head, Link } from '@inertiajs/vue3';

defineProps({
    canLogin: {
        type: Boolean,
        default: true,
    },
    canRegister: {
        type: Boolean,
        default: true,
    },
});
</script>

<template>
    <Head title="Keepsake — Personal Media & Bookmark Sanctuary" />

    <div class="welcome-root">
        <!-- Navigation Bar -->
        <header class="welcome-header">
            <div class="welcome-container nav-row">
                <Link :href="route('dashboard')" class="brand-link">
                    <svg class="brand-svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                        <rect width="20" height="8" x="2" y="3" rx="2" />
                        <rect width="20" height="8" x="2" y="13" rx="2" />
                        <line x1="10" x2="14" y1="7" y2="7" />
                        <line x1="10" x2="14" y1="17" y2="17" />
                    </svg>
                    <span class="brand-name">Keepsake</span>
                </Link>

                <nav class="nav-actions">
                    <template v-if="$page.props.auth?.user">
                        <Link :href="route('dashboard')" class="btn-primary">
                            Dashboard
                        </Link>
                    </template>
                    <template v-else>
                        <Link v-if="canLogin" :href="route('login')" class="btn-ghost">
                            Sign in
                        </Link>
                        <Link v-if="canRegister" :href="route('register')" class="btn-primary">
                            Create account
                        </Link>
                    </template>
                </nav>
            </div>
        </header>

        <main>
            <!-- Hero Section -->
            <section class="hero-section">
                <div class="welcome-container hero-measure">
                    <h1 class="hero-title">
                        A quiet sanctuary for your saves.
                    </h1>
                    <p class="hero-lead">
                        Save links, media, screenshots, and visual notes into a private vault.
                    </p>
                    <div class="hero-actions">
                        <Link
                            v-if="$page.props.auth?.user"
                            :href="route('dashboard')"
                            class="btn-primary btn-large"
                        >
                            Open your vault
                        </Link>
                        <template v-else>
                            <Link
                                v-if="canRegister"
                                :href="route('register')"
                                class="btn-primary btn-large"
                            >
                                Start your vault
                            </Link>
                            <Link
                                v-if="canLogin"
                                :href="route('login')"
                                class="btn-ghost btn-large"
                            >
                                Sign in
                            </Link>
                        </template>
                    </div>
                </div>
            </section>

            <!-- Interface Gallery -->
            <section class="preview-section">
                <div class="welcome-container">
                    <div class="preview-board">
                        <div class="preview-bar">
                            <div class="preview-search">
                                <svg class="search-svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" aria-hidden="true">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="m21 21-5.197-5.197m0 0A7.5 7.5 0 1 0 5.196 5.196a7.5 7.5 0 0 0 10.607 10.607Z" />
                                </svg>
                                <span class="search-placeholder">Search links, media, and tags…</span>
                            </div>
                            <div class="preview-types">
                                <span class="type-pill active">All</span>
                                <span class="type-pill">Links</span>
                                <span class="type-pill">Images</span>
                                <span class="type-pill">Videos</span>
                                <span class="type-pill">Clips</span>
                            </div>
                        </div>

                        <!-- Masonry preview tiles -->
                        <div class="tiles-grid">
                            <!-- Card 1: Rich Link -->
                            <article class="vault-tile">
                                <div class="tile-header">
                                    <span class="tile-kind">link</span>
                                    <span class="tile-domain">figma.com</span>
                                </div>
                                <h2 class="tile-heading">Design tokens architecture</h2>
                                <p class="tile-body">A walkthrough on bridging design tokens from code to production components.</p>
                                <div class="tile-footer">
                                    <span class="tile-tag">design</span>
                                    <span class="tile-category">references</span>
                                </div>
                            </article>

                            <!-- Card 2: Image Reference -->
                            <article class="vault-tile">
                                <div class="tile-header">
                                    <span class="tile-kind">image</span>
                                    <span class="tile-domain">unarchive.photo</span>
                                </div>
                                <div class="tile-media-standin media-dark">
                                    <span class="media-title">Bauhaus specimen plate</span>
                                </div>
                                <h2 class="tile-heading">Monochrome specimen study</h2>
                                <div class="tile-footer">
                                    <span class="tile-tag">typography</span>
                                    <span class="tile-category">inspiration</span>
                                </div>
                            </article>

                            <!-- Card 3: Video clip -->
                            <article class="vault-tile">
                                <div class="tile-header">
                                    <span class="tile-kind">video</span>
                                    <span class="tile-domain">local upload</span>
                                </div>
                                <div class="tile-media-standin media-mid">
                                    <span class="media-indicator">00:14 clip</span>
                                </div>
                                <h2 class="tile-heading">Spring physics interaction</h2>
                                <div class="tile-footer">
                                    <span class="tile-tag">motion</span>
                                    <span class="tile-category">motion-lab</span>
                                </div>
                            </article>

                            <!-- Card 4: Reading clip -->
                            <article class="vault-tile">
                                <div class="tile-header">
                                    <span class="tile-kind">clip</span>
                                    <span class="tile-domain">paulgraham.com</span>
                                </div>
                                <blockquote class="tile-quote">
                                    “The way to do great work is to cultivate a relentless curiosity for details that others consider negligible.”
                                </blockquote>
                                <h2 class="tile-heading">How to do great work</h2>
                                <div class="tile-footer">
                                    <span class="tile-tag">essays</span>
                                    <span class="tile-category">reading</span>
                                </div>
                            </article>
                        </div>
                    </div>
                </div>
            </section>

            <!-- Product Foundations -->
            <section class="pillars-section">
                <div class="welcome-container">
                    <div class="pillars-grid">
                        <div class="pillar-card">
                            <h2 class="pillar-title">OpenGraph scraping</h2>
                            <p class="pillar-desc">
                                Paste any link. Keepsake retrieves the page title, preview image, description, and favicon automatically.
                            </p>
                        </div>

                        <div class="pillar-card">
                            <h2 class="pillar-title">Five media formats</h2>
                            <p class="pillar-desc">
                                From web links and screenshots to full-motion video clips and code notes, all stored in one place.
                            </p>
                        </div>

                        <div class="pillar-card">
                            <h2 class="pillar-title">Private and self-hosted</h2>
                            <p class="pillar-desc">
                                Runs on an isolated SQLite database with local storage. No external trackers, no telemetry, complete ownership.
                            </p>
                        </div>
                    </div>
                </div>
            </section>

            <!-- Bottom Invitation -->
            <section class="closing-section">
                <div class="welcome-container hero-measure text-center">
                    <h2 class="closing-title">Begin your collection</h2>
                    <p class="closing-lead">Everything saved stays searchable, categorized, and forever yours.</p>
                    <div class="closing-actions">
                        <Link v-if="canRegister" :href="route('register')" class="btn-primary btn-large">
                            Create your account
                        </Link>
                    </div>
                </div>
            </section>
        </main>

        <!-- Footer -->
        <footer class="welcome-footer">
            <div class="welcome-container footer-row">
                <div class="footer-brand">
                    <svg class="brand-svg-small" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                        <rect width="20" height="8" x="2" y="3" rx="2" />
                        <rect width="20" height="8" x="2" y="13" rx="2" />
                        <line x1="10" x2="14" y1="7" y2="7" />
                        <line x1="10" x2="14" y1="17" y2="17" />
                    </svg>
                    <span class="brand-name">Keepsake</span>
                </div>
                <div class="footer-meta">
                    <span>Self-hosted bookmark and media sanctuary</span>
                </div>
            </div>
        </footer>
    </div>
</template>

<style>
/* Hallmark · atmospheric · midnight · stamp: keepsake-welcome-01 */
/* Hallmark · pre-emit critique: P5 H5 E5 S5 R5 V5 · weakest: none; precise token-driven atmospheric dark canvas */

:root {
  --color-paper: oklch(14% 0.048 264);
  --color-paper-2: oklch(18.5% 0.052 264);
  --color-paper-3: oklch(23% 0.055 264);
  --color-ink: oklch(95% 0.016 250);
  --color-ink-2: oklch(88% 0.05 245);
  --color-muted: oklch(73% 0.04 256);
  --color-rule: oklch(29% 0.058 264);
  --color-rule-2: oklch(40% 0.06 262);
  --color-accent: oklch(80% 0.11 230);
  --color-accent-ink: oklch(16% 0.05 264);
  --color-focus: oklch(84% 0.12 228);
  --font-system: ui-sans-serif, system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
}

::selection {
  background-color: var(--color-accent);
  color: var(--color-accent-ink);
}

* {
  scrollbar-color: var(--color-rule-2) var(--color-paper);
}

html, body {
  overflow-x: clip;
  background-color: var(--color-paper);
  color: var(--color-ink);
}

.welcome-root {
  background-color: var(--color-paper);
  color: var(--color-ink);
  font-family: var(--font-system);
  min-height: 100vh;
  overflow-x: clip;
}

.welcome-container {
  max-width: 1120px;
  margin-left: auto;
  margin-right: auto;
  padding-left: 24px;
  padding-right: 24px;
}

.welcome-header {
  border-bottom: 1px solid var(--color-rule);
  background-color: var(--color-paper);
  position: sticky;
  top: 0;
  z-index: 40;
}

.nav-row {
  display: flex;
  height: 64px;
  align-items: center;
  justify-content: space-between;
}

.brand-link {
  display: flex;
  align-items: center;
  gap: 8px;
  text-decoration: none;
  color: var(--color-ink);
}

.brand-svg {
  width: 20px;
  height: 20px;
  color: var(--color-accent);
}

.brand-svg-small {
  width: 16px;
  height: 16px;
  color: var(--color-accent);
}

.brand-name {
  font-size: 18px;
  font-weight: 600;
  letter-spacing: -0.01em;
  color: var(--color-ink);
}

.nav-actions {
  display: flex;
  align-items: center;
  gap: 12px;
}

.btn-primary {
  display: inline-flex;
  align-items: center;
  justify-content: center;
  padding: 8px 16px;
  background-color: var(--color-accent);
  color: var(--color-accent-ink);
  font-size: 14px;
  font-weight: 500;
  text-decoration: none;
  border-radius: 4px;
  transition: opacity 160ms ease, transform 160ms ease;
}

.btn-primary:hover {
  opacity: 0.92;
}

.btn-primary:active {
  transform: translateY(1px);
}

.btn-primary:focus-visible {
  outline: 2px solid var(--color-focus);
  outline-offset: 2px;
}

.btn-ghost {
  display: inline-flex;
  align-items: center;
  justify-content: center;
  padding: 8px 16px;
  color: var(--color-muted);
  font-size: 14px;
  font-weight: 500;
  text-decoration: none;
  border-radius: 4px;
  transition: color 160ms ease;
}

.btn-ghost:hover {
  color: var(--color-ink);
}

.btn-ghost:active {
  color: var(--color-ink-2);
}

.btn-ghost:focus-visible {
  outline: 2px solid var(--color-focus);
  outline-offset: 2px;
}

.btn-large {
  padding: 12px 24px;
  font-size: 15px;
}

.hero-section {
  padding-top: 80px;
  padding-bottom: 48px;
}

.hero-measure {
  max-width: 720px;
}

.hero-title {
  font-size: clamp(32px, 5vw, 52px);
  line-height: 1.1;
  font-weight: 400;
  letter-spacing: -0.02em;
  color: var(--color-ink);
  margin: 0 0 20px 0;
  overflow-wrap: break-word;
  min-width: 0;
}

.hero-lead {
  font-size: 18px;
  line-height: 1.5;
  color: var(--color-muted);
  margin: 0 0 32px 0;
}

.hero-actions {
  display: flex;
  align-items: center;
  gap: 16px;
}

.preview-section {
  padding-top: 24px;
  padding-bottom: 72px;
}

.preview-board {
  border: 1px solid var(--color-rule);
  background-color: var(--color-paper-2);
  border-radius: 4px;
  padding: 24px;
}

.preview-bar {
  display: flex;
  flex-wrap: wrap;
  align-items: center;
  justify-content: space-between;
  gap: 16px;
  margin-bottom: 24px;
  padding-bottom: 20px;
  border-bottom: 1px solid var(--color-rule);
}

.preview-search {
  display: flex;
  align-items: center;
  gap: 8px;
  background-color: var(--color-paper-3);
  padding: 8px 16px;
  border-radius: 4px;
  min-width: 280px;
}

.search-svg {
  width: 16px;
  height: 16px;
  color: var(--color-muted);
}

.search-placeholder {
  font-size: 13px;
  color: var(--color-muted);
}

.preview-types {
  display: flex;
  align-items: center;
  gap: 8px;
}

.type-pill {
  padding: 4px 12px;
  font-size: 12px;
  border-radius: 4px;
  color: var(--color-muted);
  background-color: var(--color-paper-3);
}

.type-pill.active {
  background-color: var(--color-rule-2);
  color: var(--color-ink);
}

.tiles-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(0, 1fr));
  gap: 16px;
}

@media (min-width: 768px) {
  .tiles-grid {
    grid-template-columns: repeat(4, minmax(0, 1fr));
  }
}

.vault-tile {
  background-color: var(--color-paper);
  border: 1px solid var(--color-rule);
  border-radius: 4px;
  padding: 16px;
  display: flex;
  flex-direction: column;
  justify-content: space-between;
}

.tile-header {
  display: flex;
  align-items: center;
  justify-content: space-between;
  margin-bottom: 12px;
}

.tile-kind {
  font-size: 12px;
  color: var(--color-muted);
}

.tile-domain {
  font-size: 11px;
  color: var(--color-muted);
}

.tile-heading {
  font-size: 15px;
  font-weight: 500;
  line-height: 1.35;
  color: var(--color-ink);
  margin: 0 0 8px 0;
  overflow-wrap: break-word;
}

.tile-body {
  font-size: 13px;
  line-height: 1.45;
  color: var(--color-muted);
  margin: 0 0 16px 0;
}

.tile-quote {
  font-size: 13px;
  line-height: 1.45;
  color: var(--color-ink-2);
  margin: 0 0 12px 0;
  border-left: 2px solid var(--color-rule-2);
  padding-left: 12px;
}

.tile-media-standin {
  height: 120px;
  border-radius: 2px;
  margin-bottom: 12px;
  display: flex;
  align-items: flex-end;
  padding: 12px;
}

.media-dark {
  background-color: var(--color-paper-3);
  color: var(--color-ink);
  border: 1px solid var(--color-rule);
}

.media-mid {
  background-color: var(--color-rule);
  color: var(--color-ink-2);
}

.media-title {
  font-size: 11px;
  color: var(--color-muted);
}

.media-indicator {
  font-size: 11px;
  color: var(--color-ink-2);
}

.tile-footer {
  display: flex;
  align-items: center;
  gap: 8px;
  margin-top: auto;
  padding-top: 12px;
  border-top: 1px solid var(--color-rule);
}

.tile-tag {
  font-size: 11px;
  color: var(--color-accent);
}

.tile-category {
  font-size: 11px;
  color: var(--color-muted);
}

.pillars-section {
  padding-top: 48px;
  padding-bottom: 72px;
  border-top: 1px solid var(--color-rule);
}

.pillars-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(0, 1fr));
  gap: 32px;
}

@media (min-width: 768px) {
  .pillars-grid {
    grid-template-columns: repeat(3, minmax(0, 1fr));
  }
}

.pillar-card {
  padding: 8px;
}

.pillar-title {
  font-size: 17px;
  font-weight: 500;
  color: var(--color-ink);
  margin: 0 0 8px 0;
  letter-spacing: -0.01em;
}

.pillar-desc {
  font-size: 14px;
  line-height: 1.5;
  color: var(--color-muted);
  margin: 0;
}

.closing-section {
  padding-top: 64px;
  padding-bottom: 96px;
  border-top: 1px solid var(--color-rule);
}

.text-center {
  text-align: center;
  margin-left: auto;
  margin-right: auto;
}

.closing-title {
  font-size: clamp(28px, 4vw, 40px);
  font-weight: 400;
  color: var(--color-ink);
  margin: 0 0 12px 0;
  letter-spacing: -0.02em;
}

.closing-lead {
  font-size: 16px;
  color: var(--color-muted);
  margin: 0 0 28px 0;
}

.closing-actions {
  display: flex;
  justify-content: center;
}

.welcome-footer {
  border-top: 1px solid var(--color-rule);
  background-color: var(--color-paper);
  color: var(--color-muted);
  padding: 32px 0;
}

.footer-row {
  display: flex;
  flex-direction: column;
  align-items: center;
  gap: 12px;
}

@media (min-width: 640px) {
  .footer-row {
    flex-direction: row;
    justify-content: space-between;
  }
}

.footer-brand {
  display: flex;
  align-items: center;
  gap: 8px;
}

.footer-meta {
  font-size: 13px;
  color: var(--color-muted);
}

@media (prefers-reduced-motion: reduce) {
  *, *::before, *::after {
    animation-duration: 0.01ms !important;
    animation-iteration-count: 1 !important;
    transition-duration: 0.01ms !important;
  }
}
</style>
