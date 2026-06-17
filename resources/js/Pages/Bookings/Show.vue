<script setup>
import AppLayout from '@/Layouts/AppLayout.vue'

const props = defineProps({
  booking: Object,
  items: Array,
  payment: Object,
  payments: {
    type: Array,
    default: () => [],
  },
  paymentSummary: {
    type: Object,
    default: () => ({ hasPayment: false, count: 0, status: null, amount: null }),
  },
})

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
  PAID: {
    label: 'Pago',
    badge: 'bg-emerald-100 text-emerald-700 dark:bg-emerald-500/10 dark:text-emerald-300',
    dot: 'bg-emerald-500',
  },
  PENDING: {
    label: 'Pendente',
    badge: 'bg-orange-100 text-orange-700 dark:bg-orange-500/10 dark:text-orange-300',
    dot: 'bg-orange-500',
  },
  FAILED: {
    label: 'Falhou',
    badge: 'bg-rose-100 text-rose-700 dark:bg-rose-500/10 dark:text-rose-300',
    dot: 'bg-rose-500',
  },
  paid: {
    label: 'Pago',
    badge: 'bg-emerald-100 text-emerald-700 dark:bg-emerald-500/10 dark:text-emerald-300',
    dot: 'bg-emerald-500',
  },
}

function money(value) {
  return new Intl.NumberFormat('pt-BR', {
    style: 'currency',
    currency: 'BRL',
  }).format(Number(value ?? 0))
}

function bookingStatus(status) {
  return statusStyles[status] ?? {
    label: status ?? '—',
    badge: 'bg-slate-100 text-slate-500 dark:bg-white/10 dark:text-slate-300',
    dot: 'bg-slate-400',
  }
}

function paymentStatus(status) {
  return paymentStyles[status] ?? {
    label: status ?? 'Sem pagamento',
    badge: 'bg-slate-100 text-slate-500 dark:bg-white/10 dark:text-slate-300',
    dot: 'bg-slate-400',
  }
}
</script>

<template>
  <AppLayout title="Agendamento">
    <div class="min-h-screen bg-slate-100 text-slate-950 transition-colors dark:bg-[#07111f] dark:text-white">
      <section class="relative overflow-hidden bg-[#071b33] text-white">
        <div class="absolute inset-0 pointer-events-none">
          <div class="absolute -right-24 -top-32 h-80 w-80 rounded-full bg-orange-400/25 blur-3xl"></div>
          <div class="absolute -bottom-36 left-[-6rem] h-80 w-80 rounded-full bg-sky-400/15 blur-3xl"></div>
          <div class="absolute inset-x-0 bottom-0 h-px bg-gradient-to-r from-transparent via-orange-300/60 to-transparent"></div>
        </div>

        <div class="relative mx-auto max-w-7xl px-5 py-7 sm:px-8 lg:py-9">
          <div class="flex flex-col gap-6 lg:flex-row lg:items-end lg:justify-between">
            <div class="flex items-start gap-4">
              <a
                :href="route('bookings.index')"
                class="group mt-1 flex h-10 w-10 shrink-0 items-center justify-center rounded-xl border border-white/10 bg-white/8 text-slate-300 shadow-sm backdrop-blur transition duration-300 hover:-translate-x-0.5 hover:bg-white/14 hover:text-white"
                aria-label="Voltar"
              >
                <svg class="h-4 w-4 transition group-hover:-translate-x-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                </svg>
              </a>

              <div>
                <div class="inline-flex items-center gap-2 rounded-full border border-white/10 bg-white/8 px-3 py-1.5 text-xs font-black uppercase tracking-[0.2em] text-orange-200 backdrop-blur">
                  <span class="h-2 w-2 rounded-full bg-orange-400 shadow-[0_0_16px_rgba(251,146,60,0.9)]"></span>
                  Agendamento
                </div>
                <h1 class="mt-5 text-3xl font-black tracking-tight sm:text-4xl">
                  {{ booking.clientName ?? 'Cliente' }} com {{ booking.providerName ?? 'fornecedor' }}
                </h1>
                <p class="mt-3 max-w-2xl font-mono text-xs leading-6 text-slate-400">{{ booking.id }}</p>
              </div>
            </div>

            <div class="flex flex-wrap gap-3">
              <span class="status-badge" :class="bookingStatus(booking.status).badge">
                <span class="h-1.5 w-1.5 rounded-full" :class="bookingStatus(booking.status).dot"></span>
                {{ bookingStatus(booking.status).label }}
              </span>
              <span class="status-badge" :class="paymentStatus(paymentSummary.status).badge">
                <span class="h-1.5 w-1.5 rounded-full" :class="paymentStatus(paymentSummary.status).dot"></span>
                {{ paymentSummary.hasPayment ? paymentStatus(paymentSummary.status).label : 'Sem pagamento' }}
              </span>
            </div>
          </div>
        </div>
      </section>

      <main class="mx-auto max-w-7xl px-5 py-6 sm:px-8 lg:py-8">
        <div class="relative z-10 -mt-12 grid gap-4 md:grid-cols-4">
          <article class="metric-card">
            <p class="metric-label">Data</p>
            <p class="metric-value text-xl">{{ booking.scheduledDate ?? '—' }}</p>
            <p class="metric-hint">{{ booking.scheduledTime ?? 'horário não definido' }}</p>
          </article>
          <article class="metric-card">
            <p class="metric-label">Duração</p>
            <p class="metric-value">{{ booking.totalDurationMin ?? 0 }} min</p>
            <p class="metric-hint">{{ items?.length ?? 0 }} serviço(s)</p>
          </article>
          <article class="metric-card">
            <p class="metric-label">Total booking</p>
            <p class="metric-value">{{ money(booking.totalPrice) }}</p>
            <p class="metric-hint">valor previsto</p>
          </article>
          <article class="metric-card">
            <p class="metric-label">Pagamento</p>
            <p class="metric-value">{{ paymentSummary.hasPayment ? money(paymentSummary.amount) : '—' }}</p>
            <p class="metric-hint">{{ paymentSummary.count }} registro(s)</p>
          </article>
        </div>

        <div class="mt-6 grid gap-6 xl:grid-cols-[1fr_24rem]">
          <div class="space-y-6">
            <section class="grid gap-4 md:grid-cols-2">
              <article class="panel p-5">
                <p class="section-kicker">Partes</p>
                <h2 class="section-title">Cliente e fornecedor</h2>
                <div class="mt-5 space-y-4">
                  <div class="person-row">
                    <div class="person-avatar bg-sky-100 text-sky-700 dark:bg-sky-500/10 dark:text-sky-300">
                      {{ booking.clientName?.[0] ?? '?' }}
                    </div>
                    <div class="min-w-0">
                      <p class="truncate text-sm font-black text-slate-950 dark:text-white">{{ booking.clientName ?? '—' }}</p>
                      <p class="text-xs font-bold text-slate-400">Cliente</p>
                    </div>
                  </div>
                  <div class="person-row">
                    <div class="person-avatar bg-orange-100 text-orange-700 dark:bg-orange-500/10 dark:text-orange-300">
                      {{ booking.providerName?.[0] ?? '?' }}
                    </div>
                    <div class="min-w-0">
                      <p class="truncate text-sm font-black text-slate-950 dark:text-white">{{ booking.providerName ?? '—' }}</p>
                      <p class="text-xs font-bold text-slate-400">Fornecedor</p>
                    </div>
                  </div>
                </div>
              </article>

              <article class="panel p-5">
                <p class="section-kicker">Agenda</p>
                <h2 class="section-title">Detalhes do horário</h2>
                <div class="mt-5 space-y-3">
                  <div class="info-line">
                    <span>Data</span>
                    <strong>{{ booking.scheduledDate ?? '—' }}</strong>
                  </div>
                  <div class="info-line">
                    <span>Horário</span>
                    <strong>{{ booking.scheduledTime ?? '—' }}</strong>
                  </div>
                  <div class="info-line">
                    <span>Status</span>
                    <strong>{{ bookingStatus(booking.status).label }}</strong>
                  </div>
                  <div class="info-line">
                    <span>Pagamento no booking</span>
                    <strong>{{ booking.paymentStatus ?? '—' }}</strong>
                  </div>
                </div>
              </article>
            </section>

            <section class="panel overflow-hidden">
              <div class="border-b border-slate-100 p-5 dark:border-white/10">
                <p class="section-kicker">Serviços</p>
                <h2 class="section-title">Itens incluídos</h2>
              </div>

              <div class="overflow-x-auto">
                <table class="min-w-full text-sm">
                  <thead>
                    <tr class="border-b border-slate-100 bg-slate-50 dark:border-white/10 dark:bg-white/[0.03]">
                      <th class="table-head">Serviço</th>
                      <th class="table-head">Duração</th>
                      <th class="table-head">Local</th>
                      <th class="table-head text-right">Preço</th>
                    </tr>
                  </thead>
                  <tbody class="divide-y divide-slate-100 dark:divide-white/10">
                    <tr v-if="!items?.length">
                      <td colspan="4" class="px-6 py-12 text-center text-sm font-bold text-slate-400">Nenhum item</td>
                    </tr>
                    <tr v-for="item in items" :key="item.id" class="table-row">
                      <td class="px-5 py-4 font-black text-slate-950 dark:text-white">{{ item.serviceName }}</td>
                      <td class="px-5 py-4 font-bold text-slate-500 dark:text-slate-400">{{ item.durationMin }} min</td>
                      <td class="px-5 py-4">
                        <span class="soft-chip">{{ item.locationType ?? '—' }}</span>
                      </td>
                      <td class="px-5 py-4 text-right font-black text-slate-950 dark:text-white">{{ money(item.price) }}</td>
                    </tr>
                  </tbody>
                </table>
              </div>
            </section>
          </div>

          <aside class="space-y-6">
            <section class="panel overflow-hidden">
              <div class="bg-[#071b33] p-5 text-white">
                <p class="text-xs font-black uppercase tracking-[0.2em] text-orange-200">Financeiro</p>
                <h2 class="mt-2 text-2xl font-black">
                  {{ paymentSummary.hasPayment ? money(paymentSummary.amount) : 'Sem pagamento' }}
                </h2>
                <p class="mt-2 text-sm leading-6 text-slate-300">
                  {{ paymentSummary.hasPayment ? 'Pagamento vinculado a este agendamento.' : 'Ainda não existe pagamento vinculado a este agendamento.' }}
                </p>
              </div>

              <div class="p-5">
                <div v-if="payment" class="space-y-3">
                  <div class="info-line">
                    <span>Status</span>
                    <strong>{{ paymentStatus(payment.status).label }}</strong>
                  </div>
                  <div class="info-line">
                    <span>Método</span>
                    <strong>{{ payment.method ?? '—' }}</strong>
                  </div>
                  <div class="info-line">
                    <span>Gateway</span>
                    <strong>{{ payment.gateway ?? '—' }}</strong>
                  </div>
                  <div class="info-line">
                    <span>Pago em</span>
                    <strong>{{ payment.paidAt ?? '—' }}</strong>
                  </div>
                  <div class="info-line">
                    <span>Taxa plataforma</span>
                    <strong>{{ money(payment.platformFee) }}</strong>
                  </div>
                  <div class="info-line">
                    <span>Fornecedor</span>
                    <strong>{{ money(payment.providerAmount) }}</strong>
                  </div>

                  <a :href="route('payments.show', payment.id)" class="btn-primary mt-4 w-full">
                    Ver pagamento
                  </a>
                </div>

                <div v-else class="rounded-2xl border border-dashed border-slate-300 p-5 text-center dark:border-white/15">
                  <div class="mx-auto flex h-12 w-12 items-center justify-center rounded-2xl bg-orange-100 text-orange-700 dark:bg-orange-500/10 dark:text-orange-300">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/>
                    </svg>
                  </div>
                  <p class="mt-3 text-sm font-black text-slate-950 dark:text-white">Pagamento não encontrado</p>
                  <p class="mt-1 text-xs leading-5 text-slate-500 dark:text-slate-400">O booking existe, mas nenhum documento de pagamento foi ligado a ele.</p>
                </div>
              </div>
            </section>

            <section v-if="payments?.length > 1" class="panel p-5">
              <p class="section-kicker">Histórico</p>
              <h2 class="section-title">Pagamentos vinculados</h2>
              <div class="mt-4 space-y-2">
                <a
                  v-for="linkedPayment in payments"
                  :key="linkedPayment.id"
                  :href="route('payments.show', linkedPayment.id)"
                  class="payment-row"
                >
                  <span>
                    <span class="block text-sm font-black">{{ money(linkedPayment.amount) }}</span>
                    <span class="block text-xs text-slate-400">{{ linkedPayment.gateway ?? '—' }}</span>
                  </span>
                  <span class="status-badge" :class="paymentStatus(linkedPayment.status).badge">
                    {{ paymentStatus(linkedPayment.status).label }}
                  </span>
                </a>
              </div>
            </section>

            <section class="panel p-5">
              <p class="section-kicker">Relacionados</p>
              <div class="mt-4 flex flex-col gap-2">
                <a
                  v-if="booking.chatRoomId"
                  :href="route('chat-rooms.show', booking.chatRoomId)"
                  class="btn-ghost"
                >
                  Ver conversa no chat
                </a>
                <span v-else class="rounded-2xl bg-slate-50 px-4 py-3 text-sm font-bold text-slate-400 dark:bg-white/5">
                  Sem chat vinculado
                </span>
              </div>
            </section>
          </aside>
        </div>
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

.status-badge {
  display: inline-flex;
  align-items: center;
  gap: 0.45rem;
  border-radius: 9999px;
  padding: 0.4rem 0.7rem;
  font-size: 0.75rem;
  font-weight: 900;
}

.person-row {
  display: flex;
  align-items: center;
  gap: 0.75rem;
  border-radius: 1.1rem;
  background: rgb(248 250 252);
  padding: 0.8rem;
}

:global(.dark) .person-row {
  background: rgb(255 255 255 / 0.05);
}

.person-avatar {
  display: flex;
  height: 2.5rem;
  width: 2.5rem;
  flex-shrink: 0;
  align-items: center;
  justify-content: center;
  border-radius: 1rem;
  font-size: 0.78rem;
  font-weight: 900;
  text-transform: uppercase;
}

.info-line {
  display: flex;
  align-items: center;
  justify-content: space-between;
  gap: 1rem;
  border-radius: 0.9rem;
  background: rgb(248 250 252);
  padding: 0.75rem;
  font-size: 0.85rem;
}

:global(.dark) .info-line {
  background: rgb(255 255 255 / 0.05);
}

.info-line span {
  font-weight: 800;
  color: rgb(148 163 184);
}

.info-line strong {
  text-align: right;
  font-weight: 900;
  color: rgb(15 23 42);
}

:global(.dark) .info-line strong {
  color: white;
}

.table-head {
  padding: 0.8rem 1.25rem;
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

.soft-chip {
  display: inline-flex;
  border-radius: 9999px;
  background: rgb(241 245 249);
  padding: 0.35rem 0.65rem;
  font-size: 0.75rem;
  font-weight: 900;
  color: rgb(71 85 105);
}

:global(.dark) .soft-chip {
  background: rgb(255 255 255 / 0.08);
  color: rgb(203 213 225);
}

.btn-primary,
.btn-ghost {
  display: inline-flex;
  min-height: 2.55rem;
  align-items: center;
  justify-content: center;
  gap: 0.5rem;
  border-radius: 1rem;
  padding: 0.65rem 1rem;
  font-size: 0.85rem;
  font-weight: 900;
  transition: transform 220ms ease, box-shadow 220ms ease, background-color 220ms ease, color 220ms ease;
}

.btn-primary {
  background: rgb(249 115 22);
  color: white;
  box-shadow: 0 12px 24px rgb(249 115 22 / 0.2);
}

.btn-primary:hover,
.btn-ghost:hover {
  transform: translateY(-1px);
}

.btn-ghost {
  border: 1px solid rgb(226 232 240);
  background: white;
  color: rgb(71 85 105);
}

:global(.dark) .btn-ghost {
  border-color: rgb(30 41 59);
  background: rgb(7 17 31);
  color: rgb(203 213 225);
}

.payment-row {
  display: flex;
  align-items: center;
  justify-content: space-between;
  gap: 1rem;
  border-radius: 1rem;
  background: rgb(248 250 252);
  padding: 0.8rem;
  transition: transform 180ms ease, background-color 180ms ease;
}

.payment-row:hover {
  transform: translateX(2px);
  background: rgb(255 237 213);
}

:global(.dark) .payment-row {
  background: rgb(255 255 255 / 0.05);
}

:global(.dark) .payment-row:hover {
  background: rgb(249 115 22 / 0.1);
}
</style>
