<script setup>
import { ref, computed } from 'vue'
import { usePage } from '@inertiajs/vue3'
import AppLayout from '@/Layouts/AppLayout.vue'

const props = defineProps({ bookings: Array })

const search = ref('')
const statusFilter = ref('all')
const paymentFilter = ref('all')
const page = usePage()
const flash = computed(() => page.props.flash ?? {})

const statusOptions = [
  { value: 'all', label: 'Todos' },
  { value: 'pending', label: 'Pendentes' },
  { value: 'confirmed', label: 'Confirmados' },
  { value: 'completed', label: 'Concluídos' },
  { value: 'cancelled', label: 'Cancelados' },
]

const paymentOptions = [
  { value: 'all', label: 'Todos' },
  { value: 'paid', label: 'Pagos' },
  { value: 'pending', label: 'Pendentes' },
  { value: 'failed', label: 'Falhou' },
]

const filtered = computed(() => {
  const term = search.value.toLowerCase()

  return props.bookings.filter(b => {
    const matchesSearch =
      b.clientName?.toLowerCase().includes(term) ||
      b.providerName?.toLowerCase().includes(term) ||
      b.status?.toLowerCase().includes(term) ||
      b.paymentStatus?.toLowerCase().includes(term)

    const matchesStatus = statusFilter.value === 'all' || b.status === statusFilter.value
    const matchesPayment = paymentFilter.value === 'all' || b.paymentStatus === paymentFilter.value

    return matchesSearch && matchesStatus && matchesPayment
  })
})

const todayCount = computed(() => props.bookings.filter(booking => booking.status === 'confirmed').length)
const pendingCount = computed(() => props.bookings.filter(booking => booking.status === 'pending').length)
const paidCount = computed(() => props.bookings.filter(booking => booking.paymentStatus === 'paid').length)
const totalRevenue = computed(() =>
  props.bookings.reduce((total, booking) => total + Number(booking.totalPrice ?? 0), 0)
)

const statusStyles = {
  pending: {
    label: 'Pendente',
    badge: 'bg-orange-100 text-orange-700 dark:bg-orange-500/10 dark:text-orange-300',
    dot: 'bg-orange-500',
  },
  confirmed: {
    label: 'Confirmado',
    badge: 'bg-sky-100 text-sky-700 dark:bg-sky-500/10 dark:text-sky-300',
    dot: 'bg-sky-500',
  },
  completed: {
    label: 'Concluído',
    badge: 'bg-emerald-100 text-emerald-700 dark:bg-emerald-500/10 dark:text-emerald-300',
    dot: 'bg-emerald-500',
  },
  cancelled: {
    label: 'Cancelado',
    badge: 'bg-rose-100 text-rose-700 dark:bg-rose-500/10 dark:text-rose-300',
    dot: 'bg-rose-500',
  },
}

const paymentStyles = {
  paid: {
    label: 'Pago',
    badge: 'bg-emerald-100 text-emerald-700 dark:bg-emerald-500/10 dark:text-emerald-300',
    dot: 'bg-emerald-500',
  },
  pending: {
    label: 'Pendente',
    badge: 'bg-orange-100 text-orange-700 dark:bg-orange-500/10 dark:text-orange-300',
    dot: 'bg-orange-500',
  },
  failed: {
    label: 'Falhou',
    badge: 'bg-rose-100 text-rose-700 dark:bg-rose-500/10 dark:text-rose-300',
    dot: 'bg-rose-500',
  },
}

function money(value) {
  return new Intl.NumberFormat('pt-BR', {
    style: 'currency',
    currency: 'BRL',
  }).format(Number(value ?? 0))
}

function statusFor(status) {
  return statusStyles[status] ?? {
    label: status ?? '—',
    badge: 'bg-slate-100 text-slate-500 dark:bg-white/10 dark:text-slate-300',
    dot: 'bg-slate-400',
  }
}

function paymentFor(status) {
  return paymentStyles[status] ?? {
    label: status ?? '—',
    badge: 'bg-slate-100 text-slate-500 dark:bg-white/10 dark:text-slate-300',
    dot: 'bg-slate-400',
  }
}
</script>

<template>
  <AppLayout title="Agendamentos">
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
                Operação
              </div>
              <h1 class="mt-5 text-3xl font-black tracking-tight sm:text-4xl">Agendamentos</h1>
              <p class="mt-3 max-w-2xl text-sm leading-6 text-slate-300">
                Monitore agenda, status operacional e pagamento em uma visão rápida para suporte e financeiro.
              </p>
            </div>

            <div class="rounded-3xl border border-white/10 bg-white/8 p-4 backdrop-blur sm:min-w-[20rem]">
              <p class="text-xs font-black uppercase tracking-[0.18em] text-slate-400">Volume filtrado</p>
              <div class="mt-3 flex items-end justify-between gap-4">
                <p class="text-4xl font-black">{{ filtered.length }}</p>
                <p class="pb-1 text-sm font-bold text-orange-200">de {{ bookings.length }}</p>
              </div>
            </div>
          </div>
        </div>
      </section>

      <main class="mx-auto max-w-7xl px-5 py-6 sm:px-8 lg:py-8">
        <div class="relative z-10 -mt-12 grid gap-4 md:grid-cols-4">
          <article class="metric-card">
            <p class="metric-label">Confirmados</p>
            <p class="metric-value">{{ todayCount }}</p>
            <p class="metric-hint">agenda ativa</p>
          </article>
          <article class="metric-card">
            <p class="metric-label">Pendentes</p>
            <p class="metric-value">{{ pendingCount }}</p>
            <p class="metric-hint">aguardam ação</p>
          </article>
          <article class="metric-card">
            <p class="metric-label">Pagos</p>
            <p class="metric-value">{{ paidCount }}</p>
            <p class="metric-hint">com pagamento ok</p>
          </article>
          <article class="metric-card">
            <p class="metric-label">Total</p>
            <p class="metric-value">{{ money(totalRevenue) }}</p>
            <p class="metric-hint">valor dos bookings</p>
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

        <section class="panel mt-6 overflow-hidden">
          <div class="border-b border-slate-100 p-4 dark:border-white/10">
            <div class="flex flex-col gap-4 xl:flex-row xl:items-end xl:justify-between">
              <div>
                <p class="section-kicker">Fila</p>
                <h2 class="section-title">Lista operacional</h2>
              </div>

              <div class="grid gap-3 lg:grid-cols-[minmax(18rem,1fr)_auto_auto]">
                <div class="relative">
                  <svg class="pointer-events-none absolute left-4 top-1/2 h-4 w-4 -translate-y-1/2 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                  </svg>
                  <input
                    v-model="search"
                    type="text"
                    placeholder="Buscar cliente, fornecedor ou status..."
                    class="search-input"
                  />
                </div>

                <select v-model="statusFilter" class="filter-select">
                  <option v-for="option in statusOptions" :key="option.value" :value="option.value">
                    {{ option.label }}
                  </option>
                </select>

                <select v-model="paymentFilter" class="filter-select">
                  <option v-for="option in paymentOptions" :key="option.value" :value="option.value">
                    Pagamento: {{ option.label }}
                  </option>
                </select>
              </div>
            </div>
          </div>

          <div class="overflow-x-auto">
            <table class="min-w-full text-sm">
              <thead>
                <tr class="border-b border-slate-100 bg-slate-50 dark:border-white/10 dark:bg-white/[0.03]">
                  <th class="table-head">Cliente</th>
                  <th class="table-head">Fornecedor</th>
                  <th class="table-head">Data / Hora</th>
                  <th class="table-head">Total</th>
                  <th class="table-head">Pagamento</th>
                  <th class="table-head">Status</th>
                  <th class="px-4 py-3"></th>
                </tr>
              </thead>
              <tbody class="divide-y divide-slate-100 dark:divide-white/10">
                <tr v-if="filtered.length === 0">
                  <td colspan="7" class="px-6 py-20 text-center text-slate-400">
                    <div class="mx-auto mb-4 flex h-14 w-14 items-center justify-center rounded-3xl bg-orange-100 text-orange-600 dark:bg-orange-500/10 dark:text-orange-300">
                      <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.7" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                      </svg>
                    </div>
                    <p class="font-bold">Nenhum agendamento encontrado</p>
                  </td>
                </tr>

                <tr v-for="booking in filtered" :key="booking.id" class="table-row">
                  <td class="px-4 py-4">
                    <div class="flex items-center gap-3">
                      <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-2xl bg-[#071b33] text-xs font-black uppercase text-orange-200 shadow-sm">
                        {{ booking.clientName?.[0] ?? '?' }}
                      </div>
                      <div class="min-w-0">
                        <p class="truncate font-black text-slate-950 dark:text-white">{{ booking.clientName }}</p>
                        <p class="mt-0.5 font-mono text-xs font-bold text-slate-400">{{ booking.id }}</p>
                      </div>
                    </div>
                  </td>

                  <td class="px-4 py-4 font-bold text-slate-500 dark:text-slate-300">{{ booking.providerName }}</td>

                  <td class="px-4 py-4">
                    <p class="font-black text-slate-950 dark:text-white">{{ booking.scheduledDate ?? '—' }}</p>
                    <p class="text-xs font-bold text-slate-400">{{ booking.scheduledTime ?? '—' }}</p>
                  </td>

                  <td class="px-4 py-4 font-black text-slate-950 dark:text-white">
                    {{ money(booking.totalPrice) }}
                  </td>

                  <td class="px-4 py-4">
                    <span class="status-badge" :class="paymentFor(booking.paymentStatus).badge">
                      <span class="h-1.5 w-1.5 rounded-full" :class="paymentFor(booking.paymentStatus).dot"></span>
                      {{ paymentFor(booking.paymentStatus).label }}
                    </span>
                  </td>

                  <td class="px-4 py-4">
                    <span class="status-badge" :class="statusFor(booking.status).badge">
                      <span class="h-1.5 w-1.5 rounded-full" :class="statusFor(booking.status).dot"></span>
                      {{ statusFor(booking.status).label }}
                    </span>
                  </td>

                  <td class="px-4 py-4 text-right">
                    <a :href="route('bookings.show', booking.id)" class="detail-link">
                      Ver detalhes
                      <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                      </svg>
                    </a>
                  </td>
                </tr>
              </tbody>
            </table>
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

.metric-label,
.section-kicker {
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

.section-title {
  margin-top: 0.25rem;
  font-size: 1.2rem;
  font-weight: 900;
  color: rgb(2 6 23);
}

:global(.dark) .section-title {
  color: white;
}

.search-input,
.filter-select {
  width: 100%;
  border-radius: 1rem;
  border: 1px solid rgb(226 232 240);
  background: white;
  padding: 0.75rem 1rem;
  font-size: 0.875rem;
  font-weight: 800;
  color: rgb(15 23 42);
  outline: none;
  transition: border-color 180ms ease, box-shadow 180ms ease, background-color 180ms ease;
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
:global(.dark) .filter-select {
  border-color: rgb(30 41 59);
  background: rgb(7 17 31);
  color: white;
}

.table-head {
  padding: 0.8rem 1rem;
  text-align: left;
  font-size: 0.68rem;
  font-weight: 900;
  letter-spacing: 0.14em;
  text-transform: uppercase;
  color: rgb(100 116 139);
}

.table-row {
  transition: background-color 180ms ease;
}

.table-row:hover {
  background: rgb(248 250 252);
}

:global(.dark) .table-row:hover {
  background: rgb(255 255 255 / 0.04);
}

.status-badge {
  display: inline-flex;
  align-items: center;
  gap: 0.45rem;
  border-radius: 9999px;
  padding: 0.4rem 0.7rem;
  font-size: 0.75rem;
  font-weight: 900;
}

.detail-link {
  display: inline-flex;
  align-items: center;
  gap: 0.35rem;
  border-radius: 0.9rem;
  padding: 0.55rem 0.75rem;
  font-size: 0.75rem;
  font-weight: 900;
  color: rgb(194 65 12);
  transition: transform 180ms ease, background-color 180ms ease, color 180ms ease;
}

.detail-link:hover {
  transform: translateX(2px);
  background: rgb(255 237 213);
}

:global(.dark) .detail-link {
  color: rgb(253 186 116);
}

:global(.dark) .detail-link:hover {
  background: rgb(249 115 22 / 0.12);
}
</style>
