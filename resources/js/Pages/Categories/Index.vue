<script setup>
import { ref, computed } from 'vue'
import { router, usePage } from '@inertiajs/vue3'
import AppLayout from '@/Layouts/AppLayout.vue'

const props = defineProps({
  categories: Array,
})

const search = ref('')
const viewMode = ref('grid')
const page = usePage()
const flash = computed(() => page.props.flash ?? {})

const filtered = computed(() =>
  props.categories.filter(c =>
    c.name?.toLowerCase().includes(search.value.toLowerCase()) ||
    c.icon?.toLowerCase().includes(search.value.toLowerCase()) ||
    c.defaultBookingType?.toLowerCase().includes(search.value.toLowerCase())
  )
)

const activeCount = computed(() => props.categories.filter(category => category.isActive).length)
const instantCount = computed(() => props.categories.filter(category => category.allowsInstant).length)
const flexibleCount = computed(() => props.categories.filter(category => category.defaultBookingType === 'flexivel').length)

const sortedCategories = computed(() =>
  [...filtered.value].sort((a, b) => Number(a.sortOrder ?? 999) - Number(b.sortOrder ?? 999))
)

function categoryAccent(index) {
  const accents = [
    'from-orange-500 to-amber-400',
    'from-sky-500 to-cyan-400',
    'from-slate-700 to-slate-500',
    'from-rose-500 to-orange-400',
    'from-emerald-500 to-teal-400',
  ]

  return accents[index % accents.length]
}

function categoryInitial(category) {
  return (category.name?.[0] || '?').toUpperCase()
}

function destroy(id) {
  if (confirm('Remover esta categoria?')) {
    router.delete(route('categories.destroy', id))
  }
}
</script>

<template>
  <AppLayout title="Categorias">
    <div class="min-h-screen bg-slate-100 text-slate-950 transition-colors dark:bg-[#07111f] dark:text-white">
      <section class="relative overflow-hidden bg-[#071b33] text-white">
        <div class="absolute inset-0 pointer-events-none">
          <div class="absolute -right-24 -top-32 h-80 w-80 rounded-full bg-orange-400/25 blur-3xl"></div>
          <div class="absolute -bottom-36 left-[-6rem] h-80 w-80 rounded-full bg-sky-400/15 blur-3xl"></div>
          <div class="absolute inset-x-0 bottom-0 h-px bg-gradient-to-r from-transparent via-orange-300/60 to-transparent"></div>
        </div>

        <div class="relative mx-auto max-w-7xl px-5 py-7 sm:px-8 lg:py-9">
          <div class="flex flex-col gap-6 lg:flex-row lg:items-end lg:justify-between">
            <div>
              <div class="inline-flex items-center gap-2 rounded-full border border-white/10 bg-white/8 px-3 py-1.5 text-xs font-black uppercase tracking-[0.2em] text-orange-200 backdrop-blur">
                <span class="h-2 w-2 rounded-full bg-orange-400 shadow-[0_0_16px_rgba(251,146,60,0.9)]"></span>
                Catálogo do app
              </div>
              <h1 class="mt-5 text-3xl font-black tracking-tight sm:text-4xl">Categorias</h1>
              <p class="mt-3 max-w-2xl text-sm leading-6 text-slate-300">
                Organize a vitrine do cliente por áreas, prioridade, tipo de agenda e disponibilidade instantânea.
              </p>
            </div>

            <a :href="route('categories.create')" class="btn-primary-white">
              <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
              </svg>
              Nova categoria
            </a>
          </div>
        </div>
      </section>

      <main class="mx-auto max-w-7xl px-5 py-6 sm:px-8 lg:py-8">
        <div class="relative z-10 -mt-12 grid gap-4 sm:grid-cols-3">
          <article class="metric-card">
            <p class="metric-label">Total</p>
            <p class="metric-value">{{ categories.length }}</p>
            <p class="metric-hint">categorias cadastradas</p>
          </article>
          <article class="metric-card">
            <p class="metric-label">Ativas</p>
            <p class="metric-value">{{ activeCount }}</p>
            <p class="metric-hint">visíveis no app</p>
          </article>
          <article class="metric-card">
            <p class="metric-label">Instantâneas</p>
            <p class="metric-value">{{ instantCount }}</p>
            <p class="metric-hint">{{ flexibleCount }} com agenda flexível</p>
          </article>
        </div>

        <Transition enter-active-class="transition duration-300" enter-from-class="opacity-0 -translate-y-2" enter-to-class="opacity-100 translate-y-0">
          <div v-if="flash.success" class="mt-6 flex items-center gap-3 rounded-2xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm font-bold text-emerald-800 dark:border-emerald-500/20 dark:bg-emerald-500/10 dark:text-emerald-300">
            <svg class="h-4 w-4 shrink-0" fill="currentColor" viewBox="0 0 20 20">
              <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
            </svg>
            {{ flash.success }}
          </div>
        </Transition>

        <div class="mt-6 flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">
          <div class="relative w-full lg:max-w-md">
            <svg class="pointer-events-none absolute left-4 top-1/2 h-4 w-4 -translate-y-1/2 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
            </svg>
            <input
              v-model="search"
              type="text"
              placeholder="Buscar por nome, ícone ou tipo..."
              class="search-input"
            />
          </div>
          <div class="flex flex-wrap items-center gap-3">
            <div class="view-toggle">
              <button
                type="button"
                @click="viewMode = 'grid'"
                class="view-toggle-button"
                :class="viewMode === 'grid' ? 'view-toggle-active' : ''"
                aria-label="Ver em grid"
              >
                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M4 4h6v6H4V4zm10 0h6v6h-6V4zM4 14h6v6H4v-6zm10 0h6v6h-6v-6z"/>
                </svg>
                Grid
              </button>
              <button
                type="button"
                @click="viewMode = 'list'"
                class="view-toggle-button"
                :class="viewMode === 'list' ? 'view-toggle-active' : ''"
                aria-label="Ver em lista"
              >
                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M4 7h16M4 12h16M4 17h16"/>
                </svg>
                Lista
              </button>
            </div>
            <span class="rounded-full bg-white px-4 py-2 text-sm font-black text-slate-500 shadow-sm ring-1 ring-slate-200 dark:bg-[#081424] dark:text-slate-300 dark:ring-white/10">
              {{ filtered.length }} resultado(s)
            </span>
          </div>
        </div>

        <div v-if="sortedCategories.length === 0" class="panel mt-6 flex min-h-80 items-center justify-center p-8 text-center">
          <div>
            <div class="mx-auto mb-4 flex h-16 w-16 items-center justify-center rounded-3xl bg-orange-100 text-orange-600 dark:bg-orange-500/10 dark:text-orange-300">
              <svg class="h-7 w-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.7" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A2 2 0 013 12V7a4 4 0 014-4z"/>
              </svg>
            </div>
            <h2 class="text-lg font-black text-slate-950 dark:text-white">Nenhuma categoria encontrada</h2>
            <p class="mt-2 text-sm text-slate-500 dark:text-slate-400">Crie uma categoria para começar a organizar a vitrine.</p>
            <a :href="route('categories.create')" class="btn-primary mt-5">Cadastrar primeira categoria</a>
          </div>
        </div>

        <div
          v-else
          class="mt-6 grid gap-3"
          :class="viewMode === 'grid' ? 'md:grid-cols-2 xl:grid-cols-3' : 'grid-cols-1'"
        >
          <article
            v-for="(category, index) in sortedCategories"
            :key="category.id"
            class="category-card group"
            :class="viewMode === 'list' ? 'category-card-list' : ''"
          >
            <div class="absolute inset-x-0 top-0 h-1.5 bg-gradient-to-r" :class="categoryAccent(index)"></div>

            <div class="category-head">
              <div class="flex min-w-0 items-center gap-3">
                <div class="category-avatar bg-gradient-to-br" :class="categoryAccent(index)">
                  <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.9" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A2 2 0 013 12V7a4 4 0 014-4z"/>
                  </svg>
                </div>
                <div class="min-w-0">
                  <div class="flex min-w-0 items-center gap-2">
                    <h2 class="truncate text-base font-black text-slate-950 dark:text-white">{{ category.name }}</h2>
                    <span class="category-initial">{{ categoryInitial(category) }}</span>
                  </div>
                  <p class="mt-1 truncate text-[11px] font-bold uppercase tracking-[0.14em] text-slate-400">
                    #{{ category.sortOrder ?? '—' }} · {{ category.icon ?? 'sem icone' }}
                  </p>
                </div>
              </div>

              <span
                class="shrink-0 rounded-full px-2.5 py-1 text-xs font-black"
                :class="category.isActive
                  ? 'bg-emerald-100 text-emerald-700 dark:bg-emerald-500/10 dark:text-emerald-300'
                  : 'bg-slate-100 text-slate-500 dark:bg-white/10 dark:text-slate-400'"
              >
                {{ category.isActive ? 'Ativa' : 'Inativa' }}
              </span>
            </div>

            <div class="details-grid mt-4 grid grid-cols-2 gap-2">
              <div class="info-tile">
                <p class="info-label">Agenda</p>
                <p class="info-value capitalize">{{ category.defaultBookingType ?? '—' }}</p>
              </div>
              <div class="info-tile">
                <p class="info-label">Instant</p>
                <p class="info-value">{{ category.allowsInstant ? 'Sim' : 'Não' }}</p>
              </div>
            </div>

            <div class="icon-panel mt-4 rounded-2xl bg-[#071b33] p-3 text-white">
              <div class="flex items-center justify-between gap-3">
                <div class="min-w-0">
                  <p class="text-[11px] font-bold text-slate-400">Ícone cadastrado</p>
                  <p class="mt-1 truncate font-mono text-xs font-black text-orange-200">{{ category.icon ?? 'sem_icone' }}</p>
                </div>
                <div class="flex h-9 w-9 shrink-0 items-center justify-center rounded-2xl bg-white/10 text-orange-200">
                  <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A2 2 0 013 12V7a4 4 0 014-4z"/>
                  </svg>
                </div>
              </div>
            </div>

            <div class="card-footer mt-4 flex items-center justify-between gap-2 border-t border-slate-100 pt-3 dark:border-white/10">
              <a :href="route('categories.edit', category.id)" class="card-action">
                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M16.862 4.487l1.651-1.65a1.875 1.875 0 112.652 2.652L9.75 16.902 6 18l1.098-3.75L16.862 4.487z"/>
                </svg>
                Editar
              </a>
              <button @click="destroy(category.id)" class="card-danger">
                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M6 7h12m-9 0V5a1 1 0 011-1h4a1 1 0 011 1v2m2 0l-.7 12A2 2 0 0115.3 21H8.7a2 2 0 01-1.99-2L6 7z"/>
                </svg>
                Remover
              </button>
            </div>
          </article>
        </div>
      </main>
    </div>
  </AppLayout>
</template>

<style scoped>
.panel,
.metric-card,
.category-card {
  border: 1px solid rgb(226 232 240);
  border-radius: 1.5rem;
  background: rgb(255 255 255);
  box-shadow: 0 1px 2px rgb(15 23 42 / 0.04);
}

:global(.dark) .panel,
:global(.dark) .metric-card,
:global(.dark) .category-card {
  border-color: rgb(30 41 59);
  background: rgb(8 20 36);
}

.metric-card {
  padding: 1rem;
  transition: transform 240ms ease, box-shadow 240ms ease, border-color 240ms ease;
}

.metric-card:hover {
  transform: translateY(-4px);
  border-color: rgb(251 146 60 / 0.5);
  box-shadow: 0 22px 35px rgb(15 23 42 / 0.12);
}

.metric-label {
  font-size: 0.72rem;
  font-weight: 900;
  letter-spacing: 0.18em;
  text-transform: uppercase;
  color: rgb(234 88 12);
}

.metric-value {
  margin-top: 0.2rem;
  font-size: 1.75rem;
  font-weight: 900;
  color: rgb(2 6 23);
}

:global(.dark) .metric-value {
  color: white;
}

.metric-hint {
  margin-top: 0.1rem;
  font-size: 0.8rem;
  font-weight: 700;
  color: rgb(148 163 184);
}

.search-input {
  width: 100%;
  border-radius: 1.2rem;
  border: 1px solid rgb(226 232 240);
  background: white;
  padding: 0.78rem 1rem 0.78rem 2.75rem;
  font-size: 0.9rem;
  font-weight: 700;
  color: rgb(15 23 42);
  outline: none;
  transition: border-color 180ms ease, box-shadow 180ms ease, background-color 180ms ease;
}

.search-input:focus {
  border-color: rgb(251 146 60);
  box-shadow: 0 0 0 4px rgb(251 146 60 / 0.16);
}

:global(.dark) .search-input {
  border-color: rgb(30 41 59);
  background: rgb(8 20 36);
  color: white;
}

.category-card {
  position: relative;
  overflow: hidden;
  padding: 1rem;
  transition: transform 260ms ease, box-shadow 260ms ease, border-color 260ms ease;
}

.category-card:hover {
  transform: translateY(-3px);
  border-color: rgb(251 146 60 / 0.48);
  box-shadow: 0 18px 28px rgb(15 23 42 / 0.11);
}

.category-head {
  display: flex;
  align-items: flex-start;
  justify-content: space-between;
  gap: 0.9rem;
}

.category-avatar {
  display: flex;
  height: 2.75rem;
  width: 2.75rem;
  flex-shrink: 0;
  align-items: center;
  justify-content: center;
  border-radius: 1rem;
  color: white;
  box-shadow: 0 10px 18px rgb(15 23 42 / 0.12);
}

.category-initial {
  display: inline-flex;
  height: 1.25rem;
  min-width: 1.25rem;
  align-items: center;
  justify-content: center;
  border-radius: 9999px;
  background: rgb(255 237 213);
  padding: 0 0.35rem;
  font-size: 0.68rem;
  font-weight: 900;
  color: rgb(194 65 12);
}

:global(.dark) .category-initial {
  background: rgb(249 115 22 / 0.14);
  color: rgb(253 186 116);
}

.category-card-list {
  display: grid;
  grid-template-columns: minmax(0, 1.3fr) minmax(15rem, 0.8fr) auto;
  align-items: center;
  gap: 1rem;
  padding: 0.85rem 1rem;
}

.category-card-list:hover {
  transform: translateX(2px);
}

.category-card-list .category-head,
.category-card-list .details-grid,
.category-card-list .card-footer {
  margin: 0;
}

.category-card-list .category-head {
  align-items: center;
}

.category-card-list .details-grid {
  display: grid;
  grid-template-columns: repeat(2, minmax(0, 1fr));
}

.category-card-list .icon-panel {
  display: none;
}

.category-card-list .card-footer {
  border: 0;
  padding: 0;
  justify-content: flex-end;
}

.info-tile {
  border-radius: 0.9rem;
  background: rgb(248 250 252);
  padding: 0.72rem;
}

:global(.dark) .info-tile {
  background: rgb(255 255 255 / 0.05);
}

.info-label {
  font-size: 0.68rem;
  font-weight: 800;
  color: rgb(148 163 184);
}

.info-value {
  margin-top: 0.15rem;
  font-size: 0.84rem;
  font-weight: 900;
  color: rgb(15 23 42);
}

:global(.dark) .info-value {
  color: white;
}

.btn-primary,
.btn-primary-white,
.card-action,
.card-danger {
  display: inline-flex;
  align-items: center;
  justify-content: center;
  gap: 0.5rem;
  border-radius: 1rem;
  font-size: 0.875rem;
  font-weight: 900;
  transition: transform 220ms ease, box-shadow 220ms ease, background-color 220ms ease, color 220ms ease, border-color 220ms ease;
}

.btn-primary {
  background: rgb(249 115 22);
  color: white;
  padding: 0.75rem 1rem;
  box-shadow: 0 12px 24px rgb(249 115 22 / 0.2);
}

.btn-primary-white {
  background: white;
  color: rgb(7 27 51);
  padding: 0.85rem 1.1rem;
  box-shadow: 0 16px 30px rgb(0 0 0 / 0.18);
}

.btn-primary:hover,
.btn-primary-white:hover,
.card-action:hover,
.card-danger:hover {
  transform: translateY(-1px);
}

.card-action,
.card-danger {
  min-height: 2.25rem;
  padding: 0.5rem 0.7rem;
}

.card-action {
  background: rgb(255 237 213);
  color: rgb(194 65 12);
}

.card-danger {
  background: transparent;
  color: rgb(148 163 184);
}

.card-danger:hover {
  background: rgb(255 228 230);
  color: rgb(225 29 72);
}

:global(.dark) .card-action {
  background: rgb(249 115 22 / 0.12);
  color: rgb(253 186 116);
}

:global(.dark) .card-danger:hover {
  background: rgb(244 63 94 / 0.12);
  color: rgb(253 164 175);
}

.view-toggle {
  display: inline-flex;
  align-items: center;
  gap: 0.25rem;
  border-radius: 1rem;
  border: 1px solid rgb(226 232 240);
  background: white;
  padding: 0.25rem;
  box-shadow: 0 1px 2px rgb(15 23 42 / 0.04);
}

:global(.dark) .view-toggle {
  border-color: rgb(30 41 59);
  background: rgb(8 20 36);
}

.view-toggle-button {
  display: inline-flex;
  align-items: center;
  gap: 0.4rem;
  border-radius: 0.75rem;
  padding: 0.48rem 0.7rem;
  font-size: 0.78rem;
  font-weight: 900;
  color: rgb(100 116 139);
  transition: background-color 180ms ease, color 180ms ease, transform 180ms ease;
}

.view-toggle-button:hover {
  transform: translateY(-1px);
  color: rgb(15 23 42);
}

:global(.dark) .view-toggle-button {
  color: rgb(148 163 184);
}

.view-toggle-active {
  background: rgb(249 115 22);
  color: white;
  box-shadow: 0 8px 16px rgb(249 115 22 / 0.18);
}

.view-toggle-active:hover,
:global(.dark) .view-toggle-active {
  color: white;
}

@media (max-width: 900px) {
  .category-card-list {
    grid-template-columns: 1fr;
  }

  .category-card-list .card-footer {
    justify-content: space-between;
  }
}
</style>
