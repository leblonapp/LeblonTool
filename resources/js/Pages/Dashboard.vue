<script setup>
import { computed } from 'vue'
import AppLayout from '@/Layouts/AppLayout.vue'

const metrics = [
  {
    label: 'Receita do mês',
    value: 'R$ 84.720',
    change: '+18,4%',
    hint: 'vs. mês anterior',
    tone: 'orange',
    icon: 'M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z',
  },
  {
    label: 'Fornecedores ativos',
    value: '128',
    change: '+12',
    hint: 'novos em 30 dias',
    tone: 'blue',
    icon: 'M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4',
  },
  {
    label: 'Clientes atendidos',
    value: '4.892',
    change: '+9,1%',
    hint: 'retenção em alta',
    tone: 'emerald',
    icon: 'M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z',
  },
  {
    label: 'Pagamentos pendentes',
    value: '37',
    change: '-6',
    hint: 'fila reduzida hoje',
    tone: 'rose',
    icon: 'M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z',
  },
]

const revenueSeries = [
  { label: 'Jan', value: 42 },
  { label: 'Fev', value: 48 },
  { label: 'Mar', value: 45 },
  { label: 'Abr', value: 61 },
  { label: 'Mai', value: 73 },
  { label: 'Jun', value: 84 },
  { label: 'Jul', value: 92 },
  { label: 'Ago', value: 88 },
  { label: 'Set', value: 103 },
  { label: 'Out', value: 118 },
  { label: 'Nov', value: 126 },
  { label: 'Dez', value: 142 },
]

const bookingSeries = [
  { day: 'Seg', value: 68 },
  { day: 'Ter', value: 74 },
  { day: 'Qua', value: 83 },
  { day: 'Qui', value: 78 },
  { day: 'Sex', value: 96 },
  { day: 'Sáb', value: 112 },
  { day: 'Dom', value: 64 },
]

const paymentMix = [
  { label: 'Pix', value: 54, color: 'bg-orange-500' },
  { label: 'Cartão', value: 32, color: 'bg-sky-500' },
  { label: 'Boleto', value: 14, color: 'bg-slate-500' },
]

const providerHealth = [
  { name: 'Leblon Beauty Studio', category: 'Beleza', revenue: 'R$ 18.420', score: 96, status: 'Excelente' },
  { name: 'Clínica Atlântico', category: 'Saúde', revenue: 'R$ 14.870', score: 89, status: 'Estável' },
  { name: 'Move Fit Ipanema', category: 'Fitness', revenue: 'R$ 12.530', score: 82, status: 'Atenção' },
  { name: 'Casa Pet Prime', category: 'Pet care', revenue: 'R$ 9.840', score: 78, status: 'Acompanhar' },
]

const activity = [
  { title: 'Pagamento aprovado', detail: 'R$ 1.280 recebido de Leblon Beauty Studio', time: 'há 8 min', tone: 'orange' },
  { title: 'Novo fornecedor', detail: 'Studio Mar entrou em análise cadastral', time: 'há 22 min', tone: 'blue' },
  { title: 'Cliente recorrente', detail: 'Ana Paula fez o 5º agendamento no mês', time: 'há 41 min', tone: 'emerald' },
  { title: 'Falha no gateway', detail: '3 tentativas recusadas no cartão', time: 'há 1h', tone: 'rose' },
]

const risks = [
  { label: 'Pagamentos em análise', value: 18, width: 72, color: 'bg-orange-500' },
  { label: 'Fornecedores sem agenda', value: 9, width: 38, color: 'bg-sky-500' },
  { label: 'Clientes sem retorno', value: 26, width: 58, color: 'bg-rose-500' },
]

const revenuePoints = computed(() => {
  const max = Math.max(...revenueSeries.map(item => item.value))

  return revenueSeries
    .map((item, index) => {
      const x = (index / (revenueSeries.length - 1)) * 100
      const y = 100 - (item.value / max) * 84 - 6
      return `${x},${y}`
    })
    .join(' ')
})

const maxBooking = computed(() => Math.max(...bookingSeries.map(item => item.value)))
</script>

<template>
  <AppLayout title="Dashboard">
    <div class="min-h-screen bg-slate-100 text-slate-950 transition-colors dark:bg-[#07111f] dark:text-white">
      <section class="relative overflow-hidden bg-[#071b33] text-white">
        <div class="absolute inset-0 pointer-events-none">
          <div class="absolute right-[-10rem] top-[-12rem] h-96 w-96 rounded-full bg-orange-400/25 blur-3xl"></div>
          <div class="absolute bottom-[-10rem] left-[18%] h-80 w-80 rounded-full bg-sky-400/15 blur-3xl"></div>
          <div class="absolute inset-x-0 bottom-0 h-px bg-gradient-to-r from-transparent via-orange-300/60 to-transparent"></div>
        </div>

        <div class="mb-6 relative mx-auto max-w-7xl px-5 py-7 sm:px-8 lg:py-9">
          <div class="flex flex-col gap-6 xl:flex-row xl:items-end xl:justify-between">
            <div>
              <div class="inline-flex items-center gap-2 rounded-full border border-white/10 bg-white/8 px-3 py-1.5 text-xs font-black uppercase tracking-[0.2em] text-orange-200 backdrop-blur">
                <span class="h-2 w-2 rounded-full bg-orange-400 shadow-[0_0_16px_rgba(251,146,60,0.9)]"></span>
                Admin geral
              </div>
              <h1 class="mt-5 max-w-4xl text-3xl font-black tracking-tight sm:text-4xl lg:text-5xl">
                Visão completa da operação, pagamentos e crescimento.
              </h1>
              <p class="mt-4 max-w-2xl text-sm leading-6 text-slate-300 sm:text-base">
                Dados demonstrativos para acompanhar fornecedores, clientes, agenda, receita e pontos de atenção em tempo real.
              </p>
            </div>

            <div class="grid grid-cols-2 gap-3 sm:min-w-[26rem]">
              <div class="rounded-2xl border border-white/10 bg-white/8 p-4 backdrop-blur transition duration-300 hover:-translate-y-1 hover:bg-white/12">
                <p class="text-xs font-bold uppercase tracking-wide text-slate-400">Hoje</p>
                <p class="mt-2 text-2xl font-black">342</p>
                <p class="mt-1 text-xs text-orange-200">agendamentos</p>
              </div>
              <div class="rounded-2xl border border-white/10 bg-white/8 p-4 backdrop-blur transition duration-300 hover:-translate-y-1 hover:bg-white/12">
                <p class="text-xs font-bold uppercase tracking-wide text-slate-400">SLA suporte</p>
                <p class="mt-2 text-2xl font-black">94%</p>
                <p class="mt-1 text-xs text-sky-200">dentro do prazo</p>
              </div>
            </div>
          </div>
        </div>
      </section>

      <main class="mx-auto max-w-7xl px-5 py-6 sm:px-8 lg:py-8">
        <div class="relative z-10 -mt-12 grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
          <article
            v-for="metric in metrics"
            :key="metric.label"
            class="metric-card group"
          >
            <div class="flex items-start justify-between gap-4">
              <span class="metric-icon" :class="`metric-${metric.tone}`">
                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" :d="metric.icon"/>
                </svg>
              </span>
              <span class="rounded-full bg-slate-100 px-2.5 py-1 text-xs font-black text-slate-600 dark:bg-white/10 dark:text-slate-300">
                {{ metric.change }}
              </span>
            </div>
            <p class="mt-6 text-sm font-bold text-slate-500 dark:text-slate-400">{{ metric.label }}</p>
            <h2 class="mt-1 text-3xl font-black tracking-tight text-slate-950 dark:text-white">{{ metric.value }}</h2>
            <p class="mt-2 text-xs font-medium text-slate-400">{{ metric.hint }}</p>
          </article>
        </div>

        <div class="mt-6 grid gap-6 xl:grid-cols-[1.35fr_0.65fr]">
          <section class="panel p-5 sm:p-6">
            <div class="flex flex-col gap-3 sm:flex-row sm:items-start sm:justify-between">
              <div>
                <p class="section-kicker">Receita</p>
                <h2 class="section-title">Evolução anual</h2>
              </div>
              <div class="flex items-center gap-2 rounded-full bg-orange-100 px-3 py-1.5 text-xs font-black text-orange-700 dark:bg-orange-500/10 dark:text-orange-300">
                <span class="h-2 w-2 rounded-full bg-orange-500"></span>
                R$ 1,08M acumulado
              </div>
            </div>

            <div class="mt-6 h-72 rounded-3xl bg-[#071b33] p-4 sm:p-5">
              <svg viewBox="0 0 100 100" preserveAspectRatio="none" class="h-full w-full overflow-visible">
                <defs>
                  <linearGradient id="revenueFill" x1="0" x2="0" y1="0" y2="1">
                    <stop offset="0%" stop-color="#fb923c" stop-opacity="0.55" />
                    <stop offset="100%" stop-color="#fb923c" stop-opacity="0" />
                  </linearGradient>
                </defs>
                <g class="text-white/10">
                  <line v-for="y in [18, 38, 58, 78]" :key="y" x1="0" x2="100" :y1="y" :y2="y" stroke="currentColor" stroke-width="0.4" />
                </g>
                <polygon :points="`0,100 ${revenuePoints} 100,100`" fill="url(#revenueFill)" />
                <polyline :points="revenuePoints" fill="none" stroke="#fb923c" stroke-width="2.8" stroke-linecap="round" stroke-linejoin="round" vector-effect="non-scaling-stroke" />
                <polyline :points="revenuePoints" fill="none" stroke="#fed7aa" stroke-width="0.8" stroke-linecap="round" stroke-linejoin="round" vector-effect="non-scaling-stroke" opacity="0.8" />
              </svg>
            </div>

            <div class="mt-4 grid grid-cols-6 gap-2 text-center text-[11px] font-bold text-slate-400 sm:grid-cols-12">
              <span v-for="item in revenueSeries" :key="item.label">{{ item.label }}</span>
            </div>
          </section>

          <section class="panel p-5 sm:p-6">
            <div>
              <p class="section-kicker">Pagamentos</p>
              <h2 class="section-title">Mix por método</h2>
            </div>

            <div class="mt-7 flex items-center justify-center">
              <div class="donut">
                <div class="donut-center">
                  <p class="text-xs font-bold text-slate-400">Aprovados</p>
                  <p class="text-3xl font-black text-slate-950 dark:text-white">91%</p>
                </div>
              </div>
            </div>

            <div class="mt-7 space-y-4">
              <div v-for="item in paymentMix" :key="item.label">
                <div class="mb-1 flex items-center justify-between text-sm">
                  <span class="font-bold text-slate-700 dark:text-slate-200">{{ item.label }}</span>
                  <span class="font-black text-slate-950 dark:text-white">{{ item.value }}%</span>
                </div>
                <div class="h-2 rounded-full bg-slate-100 dark:bg-white/10">
                  <div class="h-full rounded-full" :class="item.color" :style="{ width: `${item.value}%` }"></div>
                </div>
              </div>
            </div>
          </section>
        </div>

        <div class="mt-6 grid gap-6 xl:grid-cols-[0.9fr_1.1fr]">
          <section class="panel p-5 sm:p-6">
            <div class="flex items-center justify-between gap-4">
              <div>
                <p class="section-kicker">Agenda</p>
                <h2 class="section-title">Agendamentos por dia</h2>
              </div>
              <span class="rounded-full bg-sky-100 px-3 py-1 text-xs font-black text-sky-700 dark:bg-sky-500/10 dark:text-sky-300">+14%</span>
            </div>

            <div class="mt-8 flex h-64 items-end gap-3">
              <div
                v-for="item in bookingSeries"
                :key="item.day"
                class="flex h-full flex-1 flex-col items-center justify-end gap-3"
              >
                <div class="relative flex w-full items-end justify-center rounded-t-2xl bg-slate-100 dark:bg-white/5">
                  <div
                    class="bar-fill w-full rounded-t-2xl"
                    :style="{ height: `${(item.value / maxBooking) * 100}%` }"
                  >
                    <span class="absolute -top-7 left-1/2 -translate-x-1/2 text-xs font-black text-slate-500 dark:text-slate-300">{{ item.value }}</span>
                  </div>
                </div>
                <span class="text-xs font-black text-slate-400">{{ item.day }}</span>
              </div>
            </div>
          </section>

          <section class="panel overflow-hidden">
            <div class="border-b border-slate-100 p-5 dark:border-white/10 sm:p-6">
              <p class="section-kicker">Fornecedores</p>
              <h2 class="section-title">Saúde operacional</h2>
            </div>

            <div class="divide-y divide-slate-100 dark:divide-white/10">
              <article
                v-for="provider in providerHealth"
                :key="provider.name"
                class="grid gap-4 p-5 transition duration-300 hover:bg-slate-50 dark:hover:bg-white/5 sm:grid-cols-[1fr_auto] sm:items-center"
              >
                <div class="min-w-0">
                  <div class="flex items-center gap-3">
                    <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-2xl bg-[#071b33] text-sm font-black text-orange-300">
                      {{ provider.name[0] }}
                    </div>
                    <div class="min-w-0">
                      <h3 class="truncate text-sm font-black text-slate-950 dark:text-white">{{ provider.name }}</h3>
                      <p class="text-xs text-slate-400">{{ provider.category }} · {{ provider.revenue }}</p>
                    </div>
                  </div>
                </div>
                <div class="sm:w-48">
                  <div class="mb-1 flex items-center justify-between text-xs">
                    <span class="font-bold text-slate-400">{{ provider.status }}</span>
                    <span class="font-black text-slate-950 dark:text-white">{{ provider.score }}%</span>
                  </div>
                  <div class="h-2 rounded-full bg-slate-100 dark:bg-white/10">
                    <div class="h-full rounded-full bg-gradient-to-r from-sky-500 to-orange-400" :style="{ width: `${provider.score}%` }"></div>
                  </div>
                </div>
              </article>
            </div>
          </section>
        </div>

        <div class="mt-6 grid gap-6 xl:grid-cols-[1fr_24rem]">
          <section class="panel overflow-hidden">
            <div class="border-b border-slate-100 p-5 dark:border-white/10 sm:p-6">
              <p class="section-kicker">Tempo real</p>
              <h2 class="section-title">Atividades recentes</h2>
            </div>
            <div class="divide-y divide-slate-100 dark:divide-white/10">
              <article
                v-for="item in activity"
                :key="item.title"
                class="flex gap-4 p-5 transition duration-300 hover:bg-slate-50 dark:hover:bg-white/5"
              >
                <span class="mt-1 h-3 w-3 shrink-0 rounded-full" :class="{
                  'bg-orange-500 shadow-[0_0_16px_rgba(249,115,22,0.55)]': item.tone === 'orange',
                  'bg-sky-500 shadow-[0_0_16px_rgba(14,165,233,0.55)]': item.tone === 'blue',
                  'bg-emerald-500 shadow-[0_0_16px_rgba(16,185,129,0.55)]': item.tone === 'emerald',
                  'bg-rose-500 shadow-[0_0_16px_rgba(244,63,94,0.55)]': item.tone === 'rose',
                }"></span>
                <div class="min-w-0 flex-1">
                  <div class="flex flex-col gap-1 sm:flex-row sm:items-center sm:justify-between">
                    <h3 class="font-black text-slate-950 dark:text-white">{{ item.title }}</h3>
                    <span class="text-xs font-bold text-slate-400">{{ item.time }}</span>
                  </div>
                  <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">{{ item.detail }}</p>
                </div>
              </article>
            </div>
          </section>

          <aside class="panel p-5 sm:p-6">
            <p class="section-kicker">Atenção</p>
            <h2 class="section-title">Fila crítica</h2>
            <div class="mt-6 space-y-5">
              <div v-for="risk in risks" :key="risk.label">
                <div class="mb-2 flex items-center justify-between">
                  <span class="text-sm font-bold text-slate-600 dark:text-slate-300">{{ risk.label }}</span>
                  <span class="text-sm font-black text-slate-950 dark:text-white">{{ risk.value }}</span>
                </div>
                <div class="h-3 rounded-full bg-slate-100 dark:bg-white/10">
                  <div class="h-full rounded-full" :class="risk.color" :style="{ width: `${risk.width}%` }"></div>
                </div>
              </div>
            </div>

            <div class="mt-7 rounded-3xl bg-[#071b33] p-5 text-white">
              <p class="text-xs font-black uppercase tracking-[0.2em] text-orange-200">Próxima ação</p>
              <h3 class="mt-3 text-xl font-black leading-tight">Priorizar conciliação e fornecedores sem agenda.</h3>
              <p class="mt-3 text-sm leading-6 text-slate-300">Esses dois pontos concentram a maior chance de perda de receita nas próximas 24 horas.</p>
            </div>
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
  background: rgb(255 255 255);
  box-shadow: 0 1px 2px rgb(15 23 42 / 0.04);
}

:global(.dark) .panel,
:global(.dark) .metric-card {
  border-color: rgb(30 41 59);
  background: rgb(8 20 36);
}

.metric-card {
  padding: 1.25rem;
  transition: transform 260ms ease, box-shadow 260ms ease, border-color 260ms ease;
}

.metric-card:hover {
  transform: translateY(-4px);
  border-color: rgb(251 146 60 / 0.55);
  box-shadow: 0 22px 35px rgb(15 23 42 / 0.12);
}

.metric-icon {
  display: flex;
  height: 2.75rem;
  width: 2.75rem;
  align-items: center;
  justify-content: center;
  border-radius: 1rem;
}

.metric-orange {
  background: rgb(255 237 213);
  color: rgb(234 88 12);
}

.metric-blue {
  background: rgb(224 242 254);
  color: rgb(2 132 199);
}

.metric-emerald {
  background: rgb(220 252 231);
  color: rgb(22 163 74);
}

.metric-rose {
  background: rgb(255 228 230);
  color: rgb(225 29 72);
}

:global(.dark) .metric-orange {
  background: rgb(249 115 22 / 0.12);
  color: rgb(253 186 116);
}

:global(.dark) .metric-blue {
  background: rgb(14 165 233 / 0.12);
  color: rgb(125 211 252);
}

:global(.dark) .metric-emerald {
  background: rgb(34 197 94 / 0.12);
  color: rgb(134 239 172);
}

:global(.dark) .metric-rose {
  background: rgb(244 63 94 / 0.12);
  color: rgb(253 164 175);
}

.section-kicker {
  font-size: 0.72rem;
  font-weight: 900;
  letter-spacing: 0.18em;
  text-transform: uppercase;
  color: rgb(234 88 12);
}

:global(.dark) .section-kicker {
  color: rgb(253 186 116);
}

.section-title {
  margin-top: 0.25rem;
  font-size: 1.35rem;
  font-weight: 900;
  letter-spacing: 0;
  color: rgb(2 6 23);
}

:global(.dark) .section-title {
  color: white;
}

.donut {
  position: relative;
  height: 13rem;
  width: 13rem;
  border-radius: 9999px;
  background: conic-gradient(#f97316 0 54%, #0ea5e9 54% 86%, #64748b 86% 100%);
  box-shadow: 0 22px 45px rgb(15 23 42 / 0.12);
}

.donut::after {
  content: '';
  position: absolute;
  inset: 1.35rem;
  border-radius: inherit;
  background: white;
}

:global(.dark) .donut::after {
  background: rgb(8 20 36);
}

.donut-center {
  position: absolute;
  inset: 2rem;
  z-index: 1;
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  border-radius: inherit;
  text-align: center;
}

.bar-fill {
  position: relative;
  min-height: 12%;
  background: linear-gradient(180deg, #fb923c 0%, #0ea5e9 100%);
  box-shadow: 0 14px 24px rgb(14 165 233 / 0.18);
  transition: height 500ms ease, filter 240ms ease;
}

.bar-fill:hover {
  filter: brightness(1.08);
}
</style>
