<script setup>
import { ref, computed } from 'vue'
import { usePage } from '@inertiajs/vue3'
import AppLayout from '@/Layouts/AppLayout.vue'

const props = defineProps({
  rooms: Array,
})

const search = ref('')
const statusFilter = ref('all')
const unreadOnly = ref(false)
const page = usePage()
const flash = computed(() => page.props.flash ?? {})

const filtered = computed(() => {
  const term = search.value.toLowerCase()

  return props.rooms.filter(r => {
    const unreadTotal = Number(r.unreadClient ?? 0) + Number(r.unreadProvider ?? 0)
    const matchesSearch =
      r.clientName?.toLowerCase().includes(term) ||
      r.providerName?.toLowerCase().includes(term) ||
      r.lastMessage?.toLowerCase().includes(term) ||
      r.bookingId?.toLowerCase().includes(term)

    const matchesStatus = statusFilter.value === 'all' || r.status === statusFilter.value
    const matchesUnread = !unreadOnly.value || unreadTotal > 0

    return matchesSearch && matchesStatus && matchesUnread
  })
})

const activeCount = computed(() => props.rooms.filter(room => room.status === 'active').length)
const unreadCount = computed(() =>
  props.rooms.reduce((total, room) => total + Number(room.unreadClient ?? 0) + Number(room.unreadProvider ?? 0), 0)
)
const closedCount = computed(() => props.rooms.filter(room => room.status === 'closed').length)

const statusOptions = [
  { value: 'all', label: 'Todos' },
  { value: 'active', label: 'Ativos' },
  { value: 'inactive', label: 'Inativos' },
  { value: 'closed', label: 'Encerrados' },
]

const statusStyles = {
  active: {
    label: 'Ativo',
    badge: 'bg-emerald-100 text-emerald-700 dark:bg-emerald-500/10 dark:text-emerald-300',
    dot: 'bg-emerald-500',
  },
  inactive: {
    label: 'Inativo',
    badge: 'bg-slate-100 text-slate-500 dark:bg-white/10 dark:text-slate-300',
    dot: 'bg-slate-400',
  },
  closed: {
    label: 'Encerrado',
    badge: 'bg-rose-100 text-rose-700 dark:bg-rose-500/10 dark:text-rose-300',
    dot: 'bg-rose-500',
  },
}

function statusFor(status) {
  return statusStyles[status] ?? statusStyles.inactive
}

function unreadTotal(room) {
  return Number(room.unreadClient ?? 0) + Number(room.unreadProvider ?? 0)
}
</script>

<template>
  <AppLayout title="Chat">
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
                Atendimento
              </div>
              <h1 class="mt-5 text-3xl font-black tracking-tight sm:text-4xl">Chat</h1>
              <p class="mt-3 max-w-2xl text-sm leading-6 text-slate-300">
                Acompanhe conversas entre clientes e fornecedores, priorizando mensagens não lidas e salas ativas.
              </p>
            </div>

            <div class="rounded-3xl border border-white/10 bg-white/8 p-4 backdrop-blur sm:min-w-[20rem]">
              <p class="text-xs font-black uppercase tracking-[0.18em] text-slate-400">Inbox</p>
              <div class="mt-3 flex items-end justify-between gap-4">
                <p class="text-4xl font-black">{{ unreadCount }}</p>
                <p class="pb-1 text-sm font-bold text-orange-200">não lidas</p>
              </div>
            </div>
          </div>
        </div>
      </section>

      <main class="mx-auto max-w-7xl px-5 py-6 sm:px-8 lg:py-8">
        <div class="relative z-10 -mt-12 grid gap-4 md:grid-cols-3">
          <article class="metric-card">
            <p class="metric-label">Ativas</p>
            <p class="metric-value">{{ activeCount }}</p>
            <p class="metric-hint">conversas em andamento</p>
          </article>
          <article class="metric-card">
            <p class="metric-label">Não lidas</p>
            <p class="metric-value">{{ unreadCount }}</p>
            <p class="metric-hint">cliente + fornecedor</p>
          </article>
          <article class="metric-card">
            <p class="metric-label">Encerradas</p>
            <p class="metric-value">{{ closedCount }}</p>
            <p class="metric-hint">histórico fechado</p>
          </article>
        </div>

        <Transition enter-active-class="transition duration-300" enter-from-class="opacity-0 -translate-y-2" enter-to-class="opacity-100 translate-y-0">
          <div v-if="flash.success" class="mt-6 flex items-center gap-3 rounded-2xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm font-bold text-emerald-800 dark:border-emerald-500/20 dark:bg-emerald-500/10 dark:text-emerald-300">
            <svg class="h-4 w-4 shrink-0" fill="currentColor" viewBox="0 0 20 20">
              <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 0 001.414 0l4-4z" clip-rule="evenodd"/>
            </svg>
            {{ flash.success }}
          </div>
        </Transition>

        <section class="panel mt-6 overflow-hidden">
          <div class="border-b border-slate-100 p-4 dark:border-white/10">
            <div class="grid gap-3 lg:grid-cols-[minmax(18rem,1fr)_auto_auto]">
              <div class="relative">
                <svg class="pointer-events-none absolute left-4 top-1/2 h-4 w-4 -translate-y-1/2 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                </svg>
                <input
                  v-model="search"
                  type="text"
                  placeholder="Buscar cliente, fornecedor, booking ou mensagem..."
                  class="search-input"
                />
              </div>

              <select v-model="statusFilter" class="filter-select">
                <option v-for="option in statusOptions" :key="option.value" :value="option.value">
                  {{ option.label }}
                </option>
              </select>

              <button
                type="button"
                @click="unreadOnly = !unreadOnly"
                class="toggle-filter"
                :class="unreadOnly ? 'toggle-filter-active' : ''"
              >
                Não lidas
              </button>
            </div>
          </div>

          <div class="divide-y divide-slate-100 dark:divide-white/10">
            <div v-if="filtered.length === 0" class="px-6 py-20 text-center text-slate-400">
              <div class="mx-auto mb-4 flex h-14 w-14 items-center justify-center rounded-3xl bg-orange-100 text-orange-600 dark:bg-orange-500/10 dark:text-orange-300">
                <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.7" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
                </svg>
              </div>
              <p class="font-bold">Nenhuma conversa encontrada</p>
            </div>

            <a
              v-for="room in filtered"
              :key="room.id"
              :href="route('chat-rooms.show', room.id)"
              class="conversation-row"
            >
              <div class="flex min-w-0 items-center gap-3">
                <div class="avatar-stack">
                  <span class="avatar-main">{{ room.clientName?.[0] ?? '?' }}</span>
                  <span class="avatar-sub">{{ room.providerName?.[0] ?? '?' }}</span>
                </div>

                <div class="min-w-0">
                  <div class="flex min-w-0 flex-wrap items-center gap-2">
                    <h2 class="truncate text-sm font-black text-slate-950 dark:text-white">{{ room.clientName }}</h2>
                    <span class="text-xs font-bold text-slate-400">com</span>
                    <h3 class="truncate text-sm font-black text-slate-700 dark:text-slate-200">{{ room.providerName }}</h3>
                  </div>
                  <p class="mt-1 max-w-3xl truncate text-sm font-semibold text-slate-500 dark:text-slate-400">
                    {{ room.lastMessage ?? 'Sem mensagem recente' }}
                  </p>
                  <p class="mt-1 font-mono text-[11px] font-bold text-slate-400">{{ room.bookingId ?? room.id }}</p>
                </div>
              </div>

              <div class="flex flex-wrap items-center justify-end gap-2">
                <span v-if="unreadTotal(room)" class="unread-pill">{{ unreadTotal(room) }}</span>
                <span class="status-badge" :class="statusFor(room.status).badge">
                  <span class="h-1.5 w-1.5 rounded-full" :class="statusFor(room.status).dot"></span>
                  {{ statusFor(room.status).label }}
                </span>
                <span class="text-xs font-bold text-slate-400">{{ room.updatedAt ?? '—' }}</span>
              </div>
            </a>
          </div>
        </section>
      </main>
    </div>
  </AppLayout>
</template>

<style scoped>
.panel,
.metric-card {
  border: 1px solid rgb(226 232 240);
  border-radius: 1.5rem;
  background: white;
  box-shadow: 0 1px 2px rgb(15 23 42 / 0.04);
}

:global(.dark) .panel,
:global(.dark) .metric-card {
  border-color: rgb(30 41 59);
  background: rgb(8 20 36);
}

.metric-card {
  padding: 1rem;
  transition: transform 240ms ease, box-shadow 240ms ease, border-color 240ms ease;
}

.metric-card:hover {
  transform: translateY(-3px);
  border-color: rgb(251 146 60 / 0.5);
  box-shadow: 0 18px 28px rgb(15 23 42 / 0.11);
}

.metric-label {
  font-size: 0.72rem;
  font-weight: 900;
  letter-spacing: 0.18em;
  text-transform: uppercase;
  color: rgb(234 88 12);
}

.metric-value {
  margin-top: 0.25rem;
  font-size: 1.55rem;
  font-weight: 900;
  color: rgb(2 6 23);
}

:global(.dark) .metric-value {
  color: white;
}

.metric-hint {
  margin-top: 0.15rem;
  font-size: 0.8rem;
  font-weight: 700;
  color: rgb(148 163 184);
}

.search-input,
.filter-select,
.toggle-filter {
  width: 100%;
  border-radius: 1rem;
  border: 1px solid rgb(226 232 240);
  background: white;
  padding: 0.75rem 1rem;
  font-size: 0.875rem;
  font-weight: 800;
  color: rgb(15 23 42);
  outline: none;
  transition: border-color 180ms ease, box-shadow 180ms ease, background-color 180ms ease, color 180ms ease, transform 180ms ease;
}

.search-input {
  padding-left: 2.75rem;
}

.search-input:focus,
.filter-select:focus {
  border-color: rgb(251 146 60);
  box-shadow: 0 0 0 4px rgb(251 146 60 / 0.16);
}

:global(.dark) .search-input,
:global(.dark) .filter-select,
:global(.dark) .toggle-filter {
  border-color: rgb(30 41 59);
  background: rgb(7 17 31);
  color: white;
}

.toggle-filter:hover {
  transform: translateY(-1px);
}

.toggle-filter-active {
  border-color: rgb(249 115 22);
  background: rgb(249 115 22);
  color: white;
  box-shadow: 0 10px 20px rgb(249 115 22 / 0.18);
}

.conversation-row {
  display: grid;
  grid-template-columns: minmax(0, 1fr) auto;
  gap: 1rem;
  align-items: center;
  padding: 1rem;
  transition: background-color 180ms ease, transform 180ms ease;
}

.conversation-row:hover {
  transform: translateX(2px);
  background: rgb(248 250 252);
}

:global(.dark) .conversation-row:hover {
  background: rgb(255 255 255 / 0.04);
}

.avatar-stack {
  position: relative;
  height: 3rem;
  width: 3.5rem;
  flex-shrink: 0;
}

.avatar-main,
.avatar-sub {
  position: absolute;
  display: flex;
  align-items: center;
  justify-content: center;
  border-radius: 1.15rem;
  font-size: 0.78rem;
  font-weight: 900;
  text-transform: uppercase;
}

.avatar-main {
  left: 0;
  top: 0;
  height: 2.75rem;
  width: 2.75rem;
  background: rgb(7 27 51);
  color: rgb(254 215 170);
}

.avatar-sub {
  bottom: 0;
  right: 0;
  height: 1.8rem;
  width: 1.8rem;
  border: 3px solid white;
  background: rgb(249 115 22);
  color: white;
}

:global(.dark) .avatar-sub {
  border-color: rgb(8 20 36);
}

.status-badge,
.unread-pill {
  display: inline-flex;
  align-items: center;
  justify-content: center;
  gap: 0.45rem;
  border-radius: 9999px;
  padding: 0.4rem 0.7rem;
  font-size: 0.75rem;
  font-weight: 900;
}

.unread-pill {
  min-width: 2rem;
  background: rgb(249 115 22);
  color: white;
  box-shadow: 0 10px 20px rgb(249 115 22 / 0.2);
}

@media (max-width: 760px) {
  .conversation-row {
    grid-template-columns: 1fr;
  }
}
</style>
