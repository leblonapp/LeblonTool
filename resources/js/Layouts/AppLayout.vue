<script setup>
import { computed, ref, onMounted } from 'vue'
import { Link, router, usePage } from '@inertiajs/vue3'

defineProps({
    title: String,
})

const page = usePage()

const dark = ref(false)
const mobileOpen = ref(false)

onMounted(() => {
    dark.value = localStorage.getItem('theme') === 'dark' ||
        (!localStorage.getItem('theme') && window.matchMedia('(prefers-color-scheme: dark)').matches)
    applyTheme()
})

function toggleDark() {
    dark.value = !dark.value
    localStorage.setItem('theme', dark.value ? 'dark' : 'light')
    applyTheme()
}

function applyTheme() {
    document.documentElement.classList.toggle('dark', dark.value)
}

const navLinks = [
    { label: 'Dashboard', route: 'dashboard', badge: 'Live', icon: 'M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6' },
    { label: 'Usuários', route: 'users.index', badge: null, icon: 'M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z' },
    { label: 'Fornecedores', route: 'providers.index', badge: null, icon: 'M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4' },
    { label: 'Categorias', route: 'categories.index', badge: null, icon: 'M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A2 2 0 013 12V7a4 4 0 014-4z' },
    { label: 'Chat', route: 'chat-rooms.index', badge: '3', icon: 'M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z' },
    { label: 'Agendamentos', route: 'bookings.index', badge: null, icon: 'M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z' },
    { label: 'Pagamentos', route: 'payments.index', badge: null, icon: 'M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z' },
]

const currentUser = computed(() => page.props.auth?.user ?? {})
const userInitial = computed(() => currentUser.value?.name?.[0] ?? currentUser.value?.email?.[0] ?? 'U')

function logout() {
    router.post(route('logout'))
}
</script>

<template>
    <div class="min-h-screen bg-slate-100 text-slate-950 transition-colors duration-300 dark:bg-[#07111f] dark:text-white">
        <aside class="fixed inset-y-0 left-0 z-30 hidden w-72 flex-col overflow-hidden border-r border-white/10 bg-[#071b33] text-white shadow-2xl shadow-slate-950/20 lg:flex">
            <div class="pointer-events-none absolute inset-0">
                <div class="absolute -right-24 -top-24 h-64 w-64 rounded-full bg-orange-400/20 blur-3xl"></div>
                <div class="absolute -bottom-28 left-[-6rem] h-72 w-72 rounded-full bg-sky-400/12 blur-3xl"></div>
            </div>

            <div class="relative flex h-20 items-center px-5">
                <Link :href="route('dashboard')" class="group flex min-w-0 items-center gap-3">
                    <div class="flex h-11 w-11 shrink-0 items-center justify-center rounded-2xl bg-orange-500 text-white shadow-lg shadow-orange-500/25 transition duration-300 group-hover:scale-105">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                        </svg>
                    </div>
                    <div class="min-w-0">
                        <p class="truncate text-base font-black tracking-tight">LeblonApp</p>
                        <p class="truncate text-xs font-medium text-slate-400">Admin command center</p>
                    </div>
                </Link>
            </div>

            <div class="relative px-4">
                <div class="rounded-3xl border border-white/10 bg-white/8 p-4 backdrop-blur">
                    <div class="flex items-center justify-between gap-3">
                        <div>
                            <p class="text-xs font-bold uppercase tracking-[0.18em] text-orange-200">Operação</p>
                            <p class="mt-1 text-2xl font-black">94%</p>
                        </div>
                        <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-white/10 text-orange-200">
                            <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M3 13.5l5 5L21 5.5"/>
                            </svg>
                        </div>
                    </div>
                    <div class="mt-4 h-2 rounded-full bg-white/10">
                        <div class="h-full w-[94%] rounded-full bg-gradient-to-r from-orange-400 to-sky-400"></div>
                    </div>
                </div>
            </div>

            <nav class="relative flex-1 space-y-1 overflow-y-auto px-3 py-5">
                <p class="px-3 pb-2 text-[11px] font-black uppercase tracking-[0.22em] text-slate-500">Menu</p>
                <Link
                    v-for="link in navLinks"
                    :key="link.route"
                    :href="route(link.route)"
                    class="nav-link group"
                    :class="route().current(link.route) ? 'nav-link-active' : 'nav-link-idle'"
                >
                    <span class="nav-icon" :class="route().current(link.route) ? 'nav-icon-active' : ''">
                        <svg class="h-[18px] w-[18px]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" :d="link.icon"/>
                        </svg>
                    </span>
                    <span class="min-w-0 flex-1 truncate">{{ link.label }}</span>
                    <span v-if="link.badge" class="rounded-full bg-orange-400/15 px-2 py-0.5 text-[11px] font-black text-orange-200">
                        {{ link.badge }}
                    </span>
                </Link>
            </nav>

            <div class="relative space-y-3 border-t border-white/10 p-4">
                <button
                    @click="toggleDark"
                    class="flex w-full items-center justify-between rounded-2xl border border-white/10 bg-white/8 px-3 py-3 text-sm font-bold text-slate-200 transition duration-300 hover:-translate-y-0.5 hover:bg-white/12"
                >
                    <span class="flex items-center gap-3">
                        <span class="flex h-9 w-9 items-center justify-center rounded-xl bg-white/10 text-orange-200">
                            <svg v-if="!dark" class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"/>
                            </svg>
                            <svg v-else class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z"/>
                            </svg>
                        </span>
                        {{ dark ? 'Modo claro' : 'Modo escuro' }}
                    </span>
                    <span class="h-2 w-2 rounded-full" :class="dark ? 'bg-orange-300' : 'bg-slate-500'"></span>
                </button>

                <div class="flex items-center gap-3 rounded-2xl border border-white/10 bg-white/8 p-3 backdrop-blur">
                    <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-2xl bg-orange-500 text-sm font-black uppercase text-white">
                        {{ userInitial }}
                    </div>
                    <div class="min-w-0 flex-1">
                        <p class="truncate text-sm font-black">{{ currentUser?.name ?? 'Usuário' }}</p>
                        <p class="truncate text-xs text-slate-400">{{ currentUser?.email }}</p>
                    </div>
                    <button @click="logout" class="flex h-9 w-9 items-center justify-center rounded-xl text-slate-400 transition hover:bg-rose-500/12 hover:text-rose-300" title="Sair">
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                        </svg>
                    </button>
                </div>
            </div>
        </aside>

        <div class="fixed left-0 right-0 top-0 z-30 flex h-16 items-center justify-between border-b border-slate-200/70 bg-white/85 px-4 shadow-sm backdrop-blur-xl dark:border-white/10 dark:bg-[#071b33]/90 lg:hidden">
            <Link :href="route('dashboard')" class="flex items-center gap-2.5">
                <div class="flex h-9 w-9 items-center justify-center rounded-2xl bg-orange-500 text-white shadow-lg shadow-orange-500/20">
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                    </svg>
                </div>
                <div>
                    <p class="text-sm font-black text-slate-950 dark:text-white">LeblonApp</p>
                    <p class="text-[11px] font-bold text-slate-400">Admin</p>
                </div>
            </Link>

            <div class="flex items-center gap-2">
                <button @click="toggleDark" class="mobile-icon-button" aria-label="Alternar tema">
                    <svg v-if="!dark" class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"/>
                    </svg>
                    <svg v-else class="h-4 w-4 text-orange-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z"/>
                    </svg>
                </button>
                <button @click="mobileOpen = !mobileOpen" class="mobile-icon-button" aria-label="Abrir menu">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path v-if="!mobileOpen" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                        <path v-else stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>
        </div>

        <Transition
            enter-active-class="transition duration-300 ease-out"
            enter-from-class="opacity-0"
            enter-to-class="opacity-100"
            leave-active-class="transition duration-200 ease-in"
            leave-from-class="opacity-100"
            leave-to-class="opacity-0"
        >
            <div v-if="mobileOpen" class="fixed inset-0 z-20 bg-slate-950/50 pt-16 backdrop-blur-sm lg:hidden" @click.self="mobileOpen = false">
                <Transition
                    appear
                    enter-active-class="transition duration-300 ease-out"
                    enter-from-class="-translate-x-full"
                    enter-to-class="translate-x-0"
                    leave-active-class="transition duration-200 ease-in"
                    leave-from-class="translate-x-0"
                    leave-to-class="-translate-x-full"
                >
                    <nav class="h-full w-80 max-w-[88vw] overflow-y-auto bg-[#071b33] p-4 text-white shadow-2xl">
                        <div class="mb-4 rounded-3xl border border-white/10 bg-white/8 p-4">
                            <p class="text-xs font-black uppercase tracking-[0.2em] text-orange-200">Navegação</p>
                            <p class="mt-1 text-sm text-slate-300">Controle geral do LeblonApp</p>
                        </div>

                        <div class="space-y-1">
                            <Link
                                v-for="link in navLinks"
                                :key="link.route"
                                :href="route(link.route)"
                                @click="mobileOpen = false"
                                class="nav-link group"
                                :class="route().current(link.route) ? 'nav-link-active' : 'nav-link-idle'"
                            >
                                <span class="nav-icon" :class="route().current(link.route) ? 'nav-icon-active' : ''">
                                    <svg class="h-[18px] w-[18px]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" :d="link.icon"/>
                                    </svg>
                                </span>
                                <span class="min-w-0 flex-1 truncate">{{ link.label }}</span>
                                <span v-if="link.badge" class="rounded-full bg-orange-400/15 px-2 py-0.5 text-[11px] font-black text-orange-200">
                                    {{ link.badge }}
                                </span>
                            </Link>
                        </div>

                        <div class="mt-4 border-t border-white/10 pt-4">
                            <button @click="logout" class="flex w-full items-center gap-3 rounded-2xl px-3 py-3 text-sm font-bold text-rose-200 transition hover:bg-rose-500/10">
                                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                                </svg>
                                Sair
                            </button>
                        </div>
                    </nav>
                </Transition>
            </div>
        </Transition>

        <div class="min-h-screen pt-16 lg:pl-72 lg:pt-0">
            <main class="min-h-screen">
                <slot />
            </main>
        </div>
    </div>
</template>

<style scoped>
.nav-link {
    display: flex;
    align-items: center;
    gap: 0.75rem;
    border-radius: 1rem;
    padding: 0.72rem 0.75rem;
    font-size: 0.9rem;
    font-weight: 800;
    transition: transform 220ms ease, background-color 220ms ease, color 220ms ease, box-shadow 220ms ease;
}

.nav-link:hover {
    transform: translateX(2px);
}

.nav-link-idle {
    color: rgb(203 213 225);
}

.nav-link-idle:hover {
    background: rgb(255 255 255 / 0.08);
    color: white;
}

.nav-link-active {
    background: rgb(255 255 255);
    color: rgb(7 27 51);
    box-shadow: 0 14px 28px rgb(0 0 0 / 0.16);
}

.nav-icon {
    display: flex;
    height: 2.15rem;
    width: 2.15rem;
    flex-shrink: 0;
    align-items: center;
    justify-content: center;
    border-radius: 0.85rem;
    background: rgb(255 255 255 / 0.08);
    color: rgb(148 163 184);
    transition: background-color 220ms ease, color 220ms ease;
}

.nav-icon-active {
    background: rgb(251 146 60 / 0.16);
    color: rgb(234 88 12);
}

.mobile-icon-button {
    display: flex;
    height: 2.5rem;
    width: 2.5rem;
    align-items: center;
    justify-content: center;
    border-radius: 0.95rem;
    color: rgb(71 85 105);
    transition: background-color 180ms ease, color 180ms ease, transform 180ms ease;
}

.mobile-icon-button:hover {
    transform: translateY(-1px);
    background: rgb(241 245 249);
    color: rgb(15 23 42);
}

:global(.dark) .mobile-icon-button {
    color: rgb(226 232 240);
}

:global(.dark) .mobile-icon-button:hover {
    background: rgb(255 255 255 / 0.08);
    color: white;
}
</style>
