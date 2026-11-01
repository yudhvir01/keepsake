<script setup>
import { computed, onMounted, onUnmounted, ref } from 'vue';
import { Head, Link } from '@inertiajs/vue3';
import ApplicationLogo from '@/Components/ApplicationLogo.vue';
import PhotoCredit from '@/Components/PhotoCredit.vue';
import TypeIcon from '@/Components/TypeIcon.vue';

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

// The sample vault: the chips and the search field filter these tiles live.
const kinds = [
    { value: 'all', label: 'All' },
    { value: 'link', label: 'Links' },
    { value: 'image', label: 'Images' },
    { value: 'video', label: 'Videos' },
    { value: 'clip', label: 'Clips' },
];

const tiles = [
    {
        id: 1,
        kind: 'link',
        domain: 'figma.com',
        title: 'Design tokens architecture',
        body: 'A walkthrough on bridging design tokens from code to production components.',
        tag: 'design',
        category: 'references',
    },
    {
        id: 2,
        kind: 'image',
        domain: 'unarchive.photo',
        title: 'Monochrome specimen study',
        media: 'Bauhaus specimen plate',
        tag: 'typography',
        category: 'inspiration',
    },
    {
        id: 3,
        kind: 'video',
        domain: 'local upload',
        title: 'Spring physics interaction',
        media: '00:14 clip',
        tag: 'motion',
        category: 'motion-lab',
    },
    {
        id: 4,
        kind: 'clip',
        domain: 'paulgraham.com',
        title: 'How to do great work',
        quote: '“The way to do great work is to cultivate a relentless curiosity for details that others consider negligible.”',
        tag: 'essays',
        category: 'reading',
    },
];

const activeKind = ref('all');
const query = ref('');

const visibleTiles = computed(() => {
    const q = query.value.trim().toLowerCase();
    return tiles.filter((t) => {
        if (activeKind.value !== 'all' && t.kind !== activeKind.value) return false;
        if (!q) return true;
        return [t.title, t.body, t.domain, t.tag, t.category, t.kind]
            .filter(Boolean)
            .some((v) => v.toLowerCase().includes(q));
    });
});

// The three foundations, one open at a time beside its pane.
const pillars = [
    {
        id: 'opengraph',
        title: 'OpenGraph scraping',
        desc: 'Paste any link. Keepsake retrieves the page title, preview image, description, and favicon automatically.',
        rows: ['Page title', 'Preview image', 'Description', 'Favicon'],
        icon: 'link',
        head: 'figma.com',
    },
    {
        id: 'formats',
        title: 'Five media formats',
        desc: 'From web links and screenshots to full-motion video clips and code notes, all stored in one place.',
        rows: ['Links', 'Images', 'Videos', 'Screenshots', 'Clips'],
        icon: 'vault',
        head: 'One place',
    },
    {
        id: 'private',
        title: 'Private and self-hosted',
        desc: 'Runs on an isolated SQLite database with local storage. No external trackers, no telemetry, complete ownership.',
        rows: ['Isolated SQLite database', 'Local storage', 'No external trackers', 'No telemetry', 'Complete ownership'],
        icon: 'check-circle',
        head: 'Yours',
    },
];

const activePillar = ref(pillars[0].id);
const pane = computed(() => pillars.find((p) => p.id === activePillar.value));

// The bar turns to slate glass once the page leaves the photograph.
const scrolled = ref(false);
function onScroll() {
    scrolled.value = window.scrollY > 24;
}
onMounted(() => {
    onScroll();
    window.addEventListener('scroll', onScroll, { passive: true });
});
onUnmounted(() => window.removeEventListener('scroll', onScroll));
</script>

<template>
    <Head title="Keepsake — Personal Media & Bookmark Sanctuary" />

    <div class="welcome-root">
        <!-- Navigation Bar -->
        <header class="w-nav" :class="{ 'w-nav--solid': scrolled }">
            <div class="w-container w-nav__row">
                <Link :href="route('dashboard')" class="brand">
                    <ApplicationLogo />
                    Keepsake
                </Link>

                <nav class="w-nav__actions" aria-label="Account">
                    <template v-if="$page.props.auth?.user">
                        <Link :href="route('dashboard')" class="btn btn--sm">
                            Dashboard
                        </Link>
                    </template>
                    <template v-else>
                        <Link v-if="canLogin" :href="route('login')" class="btn btn--ghost btn--sm">
                            Sign in
                        </Link>
                        <Link v-if="canRegister" :href="route('register')" class="btn btn--sm">
                            Create account
                        </Link>
                    </template>
                </nav>
            </div>
        </header>

        <main>
            <!-- Hero Section -->
            <section class="hero">
                <figure class="hero__photo" data-slot="hero" data-alt="Fog filling a valley below dark ridges at sunset">
                    <img
                        src="/images/dusk-valley.jpg"
                        alt="Fog filling a valley below dark ridges at sunset"
                        width="1920"
                        height="1254"
                        fetchpriority="high"
                    />
                </figure>
                <div class="hero__scrim" aria-hidden="true"></div>

                <div class="w-container hero__inner">
                    <h1 class="hero__title">
                        A quiet sanctuary for your saves.
                    </h1>
                    <p class="hero__lead">
                        Save links, media, screenshots, and visual notes into a private vault.
                    </p>
                    <div class="hero__actions">
                        <Link
                            v-if="$page.props.auth?.user"
                            :href="route('dashboard')"
                            class="btn btn--lg"
                        >
                            Open your vault
                        </Link>
                        <template v-else>
                            <Link
                                v-if="canRegister"
                                :href="route('register')"
                                class="btn btn--lg"
                            >
                                Start your vault
                            </Link>
                            <Link
                                v-if="canLogin"
                                :href="route('login')"
                                class="btn btn--ghost btn--lg"
                            >
                                Sign in
                            </Link>
                        </template>
                    </div>
                </div>
            </section>

            <!-- The vault board -->
            <section class="board-wrap" aria-label="A sample vault">
                <div class="w-container">
                    <div class="board glass glass--strong">
                        <div class="board__bar">
                            <label class="board__search">
                                <TypeIcon name="search" />
                                <input
                                    v-model="query"
                                    type="text"
                                    class="board__input"
                                    placeholder="Search links, media, and tags…"
                                    aria-label="Search the sample vault"
                                    autocomplete="off"
                                />
                            </label>
                            <div class="chips" role="group" aria-label="Filter by kind">
                                <button
                                    v-for="k in kinds"
                                    :key="k.value"
                                    type="button"
                                    class="chip"
                                    :aria-pressed="activeKind === k.value"
                                    @click="activeKind = k.value"
                                >
                                    {{ k.label }}
                                </button>
                            </div>
                        </div>

                        <TransitionGroup name="tile" tag="div" class="tiles">
                            <article
                                v-for="(t, i) in visibleTiles"
                                :key="t.id"
                                class="tile"
                                :style="{ '--i': i }"
                            >
                                <div class="tile__head">
                                    <span class="tile__kind"><TypeIcon :name="t.kind" />{{ t.kind }}</span>
                                    <span class="tile__domain">{{ t.domain }}</span>
                                </div>

                                <div v-if="t.kind === 'image'" class="tile__media tile__media--mono">
                                    <img src="/images/dusk-valley.jpg" alt="" loading="lazy" />
                                    <span class="tile__caption">{{ t.media }}</span>
                                </div>
                                <div v-else-if="t.kind === 'video'" class="tile__media tile__media--clip">
                                    <img src="/images/dusk-valley.jpg" alt="" loading="lazy" />
                                    <span class="tile__play"><TypeIcon name="play" filled /></span>
                                    <span class="tile__caption">{{ t.media }}</span>
                                </div>

                                <h3 class="tile__title">{{ t.title }}</h3>
                                <p v-if="t.body" class="tile__body">{{ t.body }}</p>
                                <blockquote v-if="t.quote" class="tile__quote">{{ t.quote }}</blockquote>

                                <div class="tile__foot">
                                    <span class="tile__tag">{{ t.tag }}</span>
                                    <span class="tile__cat">{{ t.category }}</span>
                                </div>
                            </article>
                        </TransitionGroup>

                        <p v-if="!visibleTiles.length" class="board__none">No keepsakes match.</p>
                    </div>
                </div>
            </section>

            <!-- Product Foundations -->
            <section class="tour">
                <div class="w-container tour__grid">
                    <div class="tour__list" role="tablist" aria-label="What Keepsake does">
                        <div v-for="p in pillars" :key="p.id" class="tour__item" :data-open="activePillar === p.id">
                            <button
                                type="button"
                                role="tab"
                                class="tour__btn"
                                :id="'tab-' + p.id"
                                :aria-selected="activePillar === p.id"
                                :aria-controls="'pane-' + p.id"
                                @click="activePillar = p.id"
                            >
                                <h2 class="tour__title">{{ p.title }}</h2>
                            </button>
                            <div class="tour__desc">
                                <p>{{ p.desc }}</p>
                            </div>
                        </div>
                    </div>

                    <div
                        class="tour__pane glass"
                        role="tabpanel"
                        :id="'pane-' + pane.id"
                        :aria-labelledby="'tab-' + pane.id"
                    >
                        <div :key="pane.id" class="tour__paneinner">
                            <div class="pane__head">
                                <TypeIcon :name="pane.icon" />
                                <span>{{ pane.head }}</span>
                            </div>
                            <ul class="pane__rows">
                                <li v-for="(r, i) in pane.rows" :key="r" class="pane__row" :style="{ '--i': i }">
                                    <span>{{ r }}</span>
                                    <TypeIcon name="check" />
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </section>

            <!-- Bottom Invitation -->
            <section class="closing">
                <figure class="closing__photo" aria-hidden="true">
                    <img src="/images/dusk-valley.jpg" alt="" loading="lazy" width="1920" height="1254" />
                </figure>
                <div class="closing__scrim" aria-hidden="true"></div>
                <div class="w-container closing__inner">
                    <h2 class="closing__title">Begin your collection</h2>
                    <p class="closing__lead">Everything saved stays searchable, categorized, and forever yours.</p>
                    <div class="closing__actions">
                        <Link v-if="canRegister" :href="route('register')" class="btn btn--lg">
                            Create your account
                        </Link>
                    </div>
                </div>
            </section>
        </main>

        <!-- Footer -->
        <footer class="w-foot">
            <div class="w-container w-foot__row">
                <div class="w-foot__brand">
                    <span class="brand">
                        <ApplicationLogo />
                        Keepsake
                    </span>
                    <span class="muted">Self-hosted bookmark and media sanctuary</span>
                </div>
                <PhotoCredit />
            </div>
        </footer>
    </div>
</template>

<style>
/* Hallmark · shape: Stage hero over a dusk photograph, the vault board overlapping · genre: modern-minimal · theme: Dusk (by request) · redesign: full
 * hero: Stage, the head left on a paper scrim, the board rising into the photograph · nav: fixed bar, wordmark left and the two actions right, slate glass after the photograph · footer: a slim mast, the wordmark over the line and the photograph credit · imagery: one dusk photograph, the vault board built · signature: the vault board in smoked glass, its chips and search filtering the tiles live · dusk: fog/periwinkle-apricot/glass
 * axes: dark/grotesk-sans/cool · posture: committed · contrast: pass · fold: pass · v2.0.0 */
/* Hallmark · pre-emit critique: P4 H4 E4 S5 R4 V5 · weakest: E, the tour pane is a list of four to five rows so it carries less than a live product */
/* Hallmark · waive 78 · the tour titles are the rows of one ruled list beside a single pane, set at row size on purpose · the list is one object, not a run of section heads */
/* Hallmark · waive 59 · the closing photograph band runs straight into the Keepsake footer, so its last gap is the footer's own padding · the collection invitation and the footer read as one closing band */
/* Hallmark · waive 83 · every picture is the same file, /images/dusk-valley.jpg, in the hero, two board tiles and the closing · the page carries one photograph, shown at four crops */

.welcome-root { position: relative; background: var(--color-paper); color: var(--color-ink); min-height: 100vh; overflow-x: clip; }
.w-container { width: 100%; max-width: var(--container); margin-inline: auto; padding-inline: var(--gutter); }

/* nav */
.w-nav { position: fixed; inset: 0 0 auto 0; z-index: var(--z-sticky-nav); background: transparent; transition: background-color var(--dur-long) var(--ease-out), box-shadow var(--dur-long) var(--ease-out); }
.w-nav--solid { background: var(--glass-strong); -webkit-backdrop-filter: blur(var(--glass-blur)) saturate(1.35); backdrop-filter: blur(var(--glass-blur)) saturate(1.35); box-shadow: inset 0 -1px 0 var(--color-hair); }
.w-nav__row { display: flex; align-items: center; justify-content: space-between; height: 72px; }
.w-nav__actions { display: flex; align-items: center; gap: var(--space-xs); }

/* hero */
.hero { position: relative; display: grid; align-items: center; min-height: min(94svh, 880px); padding-block: 132px 168px; isolation: isolate; overflow: clip; }
.hero__photo { position: absolute; inset: 0; z-index: -2; margin: 0; background: var(--color-paper-2); }
.hero__photo > img { position: absolute; inset: 0; width: 100%; height: 100%; object-fit: cover; object-position: 50% 30%; transform-origin: 50% 40%; animation: drift 48s var(--ease-in-out) infinite alternate; }
.hero__scrim { position: absolute; inset: 0; z-index: -1; background: linear-gradient(90deg, color-mix(in oklab, var(--color-paper) 92%, transparent) 0%, color-mix(in oklab, var(--color-paper) 70%, transparent) 38%, color-mix(in oklab, var(--color-paper) 8%, transparent) 78%), linear-gradient(to bottom, color-mix(in oklab, var(--color-paper) 55%, transparent) 0%, transparent 22%, transparent 62%, var(--color-paper) 100%); }
.hero__inner { display: grid; justify-items: start; gap: var(--space-lg); }
.hero__title { max-width: 14ch; animation: rise var(--dur-long) var(--ease-out) both; }
.hero__lead { max-width: 34ch; font-size: var(--text-lead); line-height: 1.45; color: var(--color-ink-2); animation: rise var(--dur-long) var(--ease-out) 90ms both; }
.hero__actions { display: flex; flex-wrap: wrap; align-items: center; gap: var(--space-sm); animation: rise var(--dur-long) var(--ease-out) 180ms both; }

/* board */
.board-wrap { position: relative; z-index: var(--z-raised); margin-top: -128px; }
.board .chip { min-height: 44px; }
.board { padding: var(--space-lg); animation: rise 700ms var(--ease-out) 280ms both; }
.board__bar { display: flex; flex-wrap: wrap; align-items: center; justify-content: space-between; gap: var(--space-md); margin-bottom: var(--space-lg); padding-bottom: var(--space-md); border-bottom: 1px solid var(--color-hair); }
.board__search { position: relative; display: flex; align-items: center; flex: 1 1 280px; max-width: 420px; }
.board__search svg { position: absolute; left: var(--space-md); width: 18px; height: 18px; color: var(--color-muted); pointer-events: none; transition: color var(--dur-short) var(--ease-out); }
.board__search:focus-within svg { color: var(--color-accent); }
.board__input { width: 100%; min-height: 48px; padding: 0 var(--space-md) 0 calc(var(--space-xl) + var(--space-sm)); border: 1px solid var(--color-field-edge); border-radius: var(--radius-pill); background: var(--color-tint); color: var(--color-ink); font-size: var(--text-md); transition: background-color var(--dur-short) var(--ease-out), border-color var(--dur-short) var(--ease-out); }
.board__input::placeholder { color: var(--color-muted); }
.board__input:hover { background: var(--color-tint-2); }
.board__input:focus-visible { outline: 2px solid var(--color-focus); outline-offset: 1px; border-color: var(--color-focus); }
.board__none { padding-block: var(--space-xl); color: var(--color-muted); text-align: center; }

.tiles { position: relative; display: grid; grid-template-columns: repeat(1, minmax(0, 1fr)); gap: var(--space-md); align-items: start; }
@media (min-width: 40rem) { .tiles { grid-template-columns: repeat(2, minmax(0, 1fr)); } }
@media (min-width: 64rem) { .tiles { grid-template-columns: minmax(0, 5fr) minmax(0, 4fr) minmax(0, 4fr) minmax(0, 5fr); } }
.tile { position: relative; display: flex; flex-direction: column; padding: var(--space-md); border-radius: var(--radius-inner); background: var(--color-tint); box-shadow: inset 0 0 0 1px var(--color-hair); transition: transform var(--dur-long) var(--ease-out), background-color var(--dur-long) var(--ease-out); animation: rise var(--dur-long) var(--ease-out) both; animation-delay: calc(var(--i, 0) * 70ms + 380ms); }
.tile:hover { transform: translateY(-4px); background: var(--color-tint-2); }
.tile__head { display: flex; align-items: center; justify-content: space-between; gap: var(--space-xs); margin-bottom: var(--space-sm); color: var(--color-muted); font-size: var(--text-xs); font-weight: 500; }
.tile__kind { display: inline-flex; align-items: center; gap: var(--space-xs); }
.tile__kind svg { width: 14px; height: 14px; }
.tile__media { position: relative; aspect-ratio: 4 / 3; margin-bottom: var(--space-sm); overflow: hidden; border-radius: 8px; background: var(--color-paper-3); box-shadow: inset 0 0 0 1px var(--color-hair); }
.tile__media img { position: absolute; inset: 0; width: 100%; height: 100%; object-fit: cover; transition: transform var(--dur-long) var(--ease-out); }
.tile__media--mono img { object-position: 78% 56%; filter: grayscale(1) contrast(1.1); }
.tile__media--clip img { object-position: 20% 80%; }
.tile:hover .tile__media img { transform: scale(1.06); }
.tile__caption { position: absolute; left: var(--space-xs); bottom: var(--space-xs); padding: var(--space-2xs) var(--space-sm); border-radius: var(--radius-pill); background: var(--glass-strong); -webkit-backdrop-filter: blur(10px); backdrop-filter: blur(10px); box-shadow: var(--glass-hi); color: var(--color-ink-2); font-size: var(--text-xs); font-weight: 500; }
.tile__play { position: absolute; inset: 0; display: grid; place-items: center; }
.tile__play svg { width: 44px; height: 44px; padding: var(--space-sm) var(--space-sm) var(--space-sm) calc(var(--space-sm) + var(--space-3xs)); border-radius: var(--radius-pill); background: var(--glass-strong); -webkit-backdrop-filter: blur(10px); backdrop-filter: blur(10px); box-shadow: var(--glass-hi); color: var(--color-ink); }
.tile__title { font-family: var(--font-display); font-weight: var(--display-weight); font-size: var(--text-md); line-height: 1.3; letter-spacing: var(--tracking-display); color: var(--color-ink); }
.tile__body { margin-top: var(--space-2xs); color: var(--color-muted); font-size: var(--text-sm); line-height: 1.5; }
.tile__quote { margin: 0 0 var(--space-sm); padding-left: var(--space-sm); border-left: 2px solid var(--color-accent); color: var(--color-ink-2); font-size: var(--text-sm); line-height: 1.5; order: -1; }
.tile__foot { display: flex; align-items: center; gap: var(--space-sm); margin-top: auto; padding-top: var(--space-md); color: var(--color-muted); font-size: var(--text-xs); font-weight: 500; }
.tile__tag { color: var(--color-accent); }
.tile-move { transition: transform var(--dur-long) var(--ease-out); }
.tile-leave-active { display: none; }

/* tour */
.tour { padding-block: var(--section-gap-s); }
.tour__grid { display: grid; gap: var(--space-xl); grid-template-columns: minmax(0, 1fr); align-items: start; }
@media (min-width: 64rem) { .tour__grid { grid-template-columns: minmax(0, 5fr) minmax(0, 7fr); gap: var(--space-2xl); } }
.tour__list { border-top: 1px solid var(--color-rule); }
.tour__item { border-bottom: 1px solid var(--color-rule); }
.tour__btn { display: flex; align-items: center; gap: var(--space-sm); width: 100%; min-height: 72px; padding: var(--space-md) 0; border: 0; background: transparent; color: var(--color-muted); text-align: start; cursor: pointer; transition: color var(--dur-short) var(--ease-out); }
.tour__btn::before { content: ""; flex: none; width: 8px; height: 8px; border-radius: var(--radius-pill); background: var(--color-accent); transform: scale(0); margin-inline-end: calc((var(--space-sm) + 8px) * -1); transition: transform var(--dur-short) var(--ease-out), margin var(--dur-short) var(--ease-out); }
.tour__btn:hover { color: var(--color-ink); }
.tour__item[data-open="true"] .tour__btn { color: var(--color-ink); }
.tour__item[data-open="true"] .tour__btn::before { transform: scale(1); margin-inline-end: 0; }
.tour__title { font-size: var(--text-xl); line-height: 1.2; color: inherit; }
.tour__desc { display: grid; grid-template-rows: 0fr; transition: grid-template-rows var(--dur-long) var(--ease-out); }
.tour__desc > p { overflow: hidden; max-width: 46ch; color: var(--color-ink-2); opacity: 0; transition: opacity var(--dur-long) var(--ease-out); }
.tour__item[data-open="true"] .tour__desc { grid-template-rows: 1fr; }
.tour__item[data-open="true"] .tour__desc > p { opacity: 1; padding-bottom: var(--space-md); }
.tour__pane { min-height: 360px; padding: var(--space-lg); }
.tour__paneinner { animation: fadein var(--dur-long) var(--ease-out) both; }
.pane__head { display: flex; align-items: center; gap: var(--space-xs); margin-bottom: var(--space-md); color: var(--color-ink-2); font-size: var(--text-sm); font-weight: 500; }
.pane__head svg { width: 18px; height: 18px; color: var(--color-accent); }
.pane__rows { display: grid; gap: var(--space-xs); margin: 0; padding: 0; list-style: none; }
.pane__row { display: flex; align-items: center; justify-content: space-between; gap: var(--space-md); min-height: 56px; padding: 0 var(--space-md); border-radius: var(--radius-inner); background: var(--color-tint); font-family: var(--font-display); font-size: var(--text-lg); letter-spacing: var(--tracking-display); animation: rise var(--dur-long) var(--ease-out) both; animation-delay: calc(var(--i, 0) * 70ms + 80ms); }
.pane__row svg { width: 18px; height: 18px; color: var(--color-accent); }
@keyframes fadein { from { opacity: 0; } to { opacity: 1; } }

/* closing */
.closing { position: relative; display: grid; align-items: center; min-height: 380px; padding-block: var(--section-gap-s); overflow: clip; isolation: isolate; }
.closing__photo { position: absolute; inset: 0; z-index: -2; margin: 0; }
.closing__photo img { width: 100%; height: 100%; object-fit: cover; object-position: 50% 92%; }
.closing__scrim { position: absolute; inset: 0; z-index: -1; background: linear-gradient(to bottom, var(--color-paper) 0%, color-mix(in oklab, var(--color-paper) 58%, transparent) 40%, color-mix(in oklab, var(--color-paper) 82%, transparent) 100%), linear-gradient(90deg, color-mix(in oklab, var(--color-paper) 80%, transparent), transparent 70%); }
.closing__inner { display: grid; justify-items: start; gap: var(--space-md); }
.closing__title { max-width: 14ch; font-size: var(--text-display-s); }
.closing__lead { max-width: 40ch; color: var(--color-ink-2); font-size: var(--text-lead); line-height: 1.45; }
.closing__actions { margin-top: var(--space-sm); }

/* footer */
.w-foot { padding-block: var(--space-xl); box-shadow: inset 0 1px 0 var(--color-rule); }
.w-foot__row { display: flex; flex-wrap: wrap; align-items: center; justify-content: space-between; gap: var(--space-md); }
.w-foot__brand { display: grid; gap: var(--space-2xs); font-size: var(--text-sm); }

@media (max-width: 40rem) {
  .hero { padding-block: 120px 148px; }
  .board-wrap { margin-top: -104px; }
  .w-nav__row { height: 64px; }
}

@media (prefers-reduced-motion: reduce) {
  .hero__photo > img, .hero__title, .hero__lead, .hero__actions, .board, .tile, .pane__row, .tour__paneinner { animation: none; }
}
</style>
