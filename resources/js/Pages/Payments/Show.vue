<script setup>
import AppLayout from '@/Layouts/AppLayout.vue'

const props = defineProps({ payment: Object })

const statusStyles = {
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
}

function statusFor(status) {
  return statusStyles[status] ?? {
    label: status ?? '—',
    badge: 'bg-slate-100 text-slate-500 dark:bg-white/10 dark:text-slate-300',
    dot: 'bg-slate-400',
  }
}

function money(value) {
  return new Intl.NumberFormat('pt-BR', {
    style: 'currency',
    currency: 'BRL',
  }).format(Number(value ?? 0))
}

function safe(value) {
  return String(value ?? '—')
    .replaceAll('&', '&amp;')
    .replaceAll('<', '&lt;')
    .replaceAll('>', '&gt;')
    .replaceAll('"', '&quot;')
    .replaceAll("'", '&#039;')
}

function printReceipt() {
  const receipt = window.open('', '_blank', 'width=820,height=980')

  if (!receipt) {
    window.print()
    return
  }

  receipt.document.write(`
    <!doctype html>
    <html>
      <head>
        <title>Recibo ${safe(props.payment.id)}</title>
        <style>
          * { box-sizing: border-box; }
          body {
            margin: 0;
            padding: 40px;
            background: #eef2f7;
            color: #071b33;
            font-family: Inter, ui-sans-serif, system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", sans-serif;
          }
          .receipt {
            max-width: 760px;
            margin: 0 auto;
            background: white;
            border: 1px solid #e2e8f0;
            border-radius: 28px;
            overflow: hidden;
            box-shadow: 0 24px 60px rgba(15, 23, 42, .12);
          }
          .hero {
            background: #071b33;
            color: white;
            padding: 32px;
          }
          .eyebrow {
            color: #fed7aa;
            font-size: 11px;
            font-weight: 900;
            letter-spacing: .18em;
            text-transform: uppercase;
          }
          h1 {
            margin: 12px 0 0;
            font-size: 32px;
            line-height: 1;
          }
          .amount {
            margin-top: 18px;
            font-size: 42px;
            font-weight: 950;
          }
          .content {
            padding: 32px;
          }
          .grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 14px;
          }
          .box {
            border-radius: 18px;
            background: #f8fafc;
            padding: 16px;
          }
          .label {
            color: #94a3b8;
            font-size: 11px;
            font-weight: 850;
            letter-spacing: .08em;
            text-transform: uppercase;
          }
          .value {
            margin-top: 6px;
            color: #071b33;
            font-size: 14px;
            font-weight: 850;
            overflow-wrap: anywhere;
          }
          .footer {
            margin-top: 24px;
            border-top: 1px solid #e2e8f0;
            padding-top: 18px;
            color: #64748b;
            font-size: 12px;
            line-height: 1.6;
          }
          @media print {
            body { background: white; padding: 0; }
            .receipt { box-shadow: none; border-radius: 0; border: 0; }
          }
        </style>
      </head>
      <body>
        <main class="receipt">
          <section class="hero">
            <div class="eyebrow">LeblonApp - recibo de pagamento</div>
            <h1>${safe(statusFor(props.payment.status).label)}</h1>
            <div class="amount">${safe(money(props.payment.amount))}</div>
          </section>
          <section class="content">
            <div class="grid">
              <div class="box"><div class="label">Fornecedor</div><div class="value">${safe(props.payment.providerName)}</div></div>
              <div class="box"><div class="label">Método</div><div class="value">${safe(props.payment.method)}</div></div>
              <div class="box"><div class="label">Gateway</div><div class="value">${safe(props.payment.gateway)}</div></div>
              <div class="box"><div class="label">Pago em</div><div class="value">${safe(props.payment.paidAt)}</div></div>
              <div class="box"><div class="label">Taxa plataforma</div><div class="value">${safe(money(props.payment.platformFee))}</div></div>
              <div class="box"><div class="label">Valor fornecedor</div><div class="value">${safe(money(props.payment.providerAmount))}</div></div>
              <div class="box"><div class="label">ID pagamento</div><div class="value">${safe(props.payment.id)}</div></div>
              <div class="box"><div class="label">ID gateway</div><div class="value">${safe(props.payment.gatewayTransactionId)}</div></div>
            </div>
            <div class="footer">
              Este recibo foi gerado pelo painel administrativo do LeblonApp. Para salvar como PDF, escolha a opção "Salvar como PDF" na janela de impressão do navegador.
            </div>
          </section>
        </main>
        <script>
          window.onload = () => {
            window.focus();
            window.print();
          };
        <\/script>
      </body>
    </html>
  `)
  receipt.document.close()
}
</script>

<template>
  <AppLayout title="Pagamento">
    <div class="min-h-screen bg-slate-100 text-slate-950 transition-colors dark:bg-[#07111f] dark:text-white">
      <section class="relative overflow-hidden bg-[#071b33] text-white">
        <div class="absolute inset-0 pointer-events-none">
          <div class="absolute -right-24 -top-32 h-80 w-80 rounded-full bg-orange-400/25 blur-3xl"></div>
          <div class="absolute -bottom-36 left-[-6rem] h-80 w-80 rounded-full bg-sky-400/15 blur-3xl"></div>
          <div class="absolute inset-x-0 bottom-0 h-px bg-gradient-to-r from-transparent via-orange-300/60 to-transparent"></div>
        </div>

        <div class="relative mx-auto max-w-7xl px-5 py-7 sm:px-8 lg:py-9">
          <div class="flex flex-col gap-6 xl:flex-row xl:items-end xl:justify-between">
            <div class="flex items-start gap-4">
              <a
                :href="route('payments.index')"
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
                  Pagamento
                </div>
                <h1 class="mt-5 text-3xl font-black tracking-tight sm:text-4xl">
                  {{ money(payment.amount) }}
                </h1>
                <p class="mt-3 max-w-2xl font-mono text-xs leading-6 text-slate-400">{{ payment.id }}</p>
              </div>
            </div>

            <div class="flex flex-wrap gap-3">
              <button type="button" @click="printReceipt" class="btn-white">
                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M12 3v12m0 0l4-4m-4 4l-4-4M5 21h14"/>
                </svg>
                Baixar recibo
              </button>
              <span class="status-badge" :class="statusFor(payment.status).badge">
                <span class="h-1.5 w-1.5 rounded-full" :class="statusFor(payment.status).dot"></span>
                {{ statusFor(payment.status).label }}
              </span>
            </div>
          </div>
        </div>
      </section>

      <main class="mx-auto max-w-7xl px-5 py-6 sm:px-8 lg:py-8">
        <div class="relative z-10 -mt-12 grid gap-4 md:grid-cols-4">
          <article class="metric-card md:col-span-2">
            <p class="metric-label">Valor total</p>
            <p class="metric-value">{{ money(payment.amount) }}</p>
            <p class="metric-hint">{{ payment.isTestMode ? 'transação de teste' : 'transação real' }}</p>
          </article>
          <article class="metric-card">
            <p class="metric-label">Taxa plataforma</p>
            <p class="metric-value">{{ money(payment.platformFee) }}</p>
            <p class="metric-hint">retenção do app</p>
          </article>
          <article class="metric-card">
            <p class="metric-label">Fornecedor</p>
            <p class="metric-value">{{ money(payment.providerAmount) }}</p>
            <p class="metric-hint">repasse previsto</p>
          </article>
        </div>

        <div class="mt-6 grid gap-6 xl:grid-cols-[1fr_24rem]">
          <div class="space-y-6">
            <section class="panel p-5 sm:p-6">
              <div class="flex flex-col gap-4 sm:flex-row sm:items-start sm:justify-between">
                <div>
                  <p class="section-kicker">Resumo</p>
                  <h2 class="section-title">Dados da transação</h2>
                </div>
                <span v-if="payment.isTestMode" class="status-badge bg-orange-100 text-orange-700 dark:bg-orange-500/10 dark:text-orange-300">
                  Modo teste
                </span>
              </div>

              <div class="mt-6 grid gap-3 md:grid-cols-2">
                <div class="info-card">
                  <p class="info-label">Fornecedor</p>
                  <p class="info-value">{{ payment.providerName ?? '—' }}</p>
                </div>
                <div class="info-card">
                  <p class="info-label">Status</p>
                  <p class="info-value">{{ statusFor(payment.status).label }}</p>
                </div>
                <div class="info-card">
                  <p class="info-label">Método</p>
                  <p class="info-value">{{ payment.method ?? '—' }}</p>
                </div>
                <div class="info-card">
                  <p class="info-label">Gateway</p>
                  <p class="info-value">{{ payment.gateway ?? '—' }}</p>
                </div>
                <div class="info-card">
                  <p class="info-label">Pago em</p>
                  <p class="info-value">{{ payment.paidAt ?? '—' }}</p>
                </div>
                <div class="info-card">
                  <p class="info-label">Liberado em</p>
                  <p class="info-value">{{ payment.releasedAt ?? '—' }}</p>
                </div>
              </div>
            </section>

            <section class="panel overflow-hidden">
              <div class="border-b border-slate-100 p-5 dark:border-white/10 sm:p-6">
                <p class="section-kicker">Conciliação</p>
                <h2 class="section-title">IDs e diagnóstico</h2>
              </div>

              <div class="grid gap-3 p-5 sm:p-6 lg:grid-cols-2">
                <div class="code-card">
                  <p class="info-label">ID pagamento</p>
                  <p class="code-value">{{ payment.id }}</p>
                </div>
                <div class="code-card">
                  <p class="info-label">ID gateway</p>
                  <p class="code-value">{{ payment.gatewayTransactionId || '—' }}</p>
                </div>
                <div class="code-card lg:col-span-2">
                  <p class="info-label">Motivo falha</p>
                  <p class="code-value">{{ payment.failureReason === 'none' ? '—' : (payment.failureReason ?? '—') }}</p>
                </div>
              </div>
            </section>
          </div>

          <aside class="space-y-6">
            <section class="panel overflow-hidden">
              <div class="bg-[#071b33] p-5 text-white">
                <p class="text-xs font-black uppercase tracking-[0.2em] text-orange-200">Recibo</p>
                <h2 class="mt-2 text-2xl font-black">Pronto para PDF</h2>
                <p class="mt-2 text-sm leading-6 text-slate-300">Gere um recibo formatado e salve pelo navegador como PDF.</p>
              </div>
              <div class="p-5">
                <button type="button" @click="printReceipt" class="btn-primary w-full">
                  Baixar recibo
                </button>
              </div>
            </section>

            <section class="panel p-5">
              <p class="section-kicker">Distribuição</p>
              <h2 class="section-title">Split financeiro</h2>
              <div class="mt-5 space-y-3">
                <div class="split-row">
                  <span>Valor total</span>
                  <strong>{{ money(payment.amount) }}</strong>
                </div>
                <div class="split-row">
                  <span>Taxa plataforma</span>
                  <strong>{{ money(payment.platformFee) }}</strong>
                </div>
                <div class="split-row">
                  <span>Repasse fornecedor</span>
                  <strong>{{ money(payment.providerAmount) }}</strong>
                </div>
              </div>
            </section>

            <section class="panel p-5">
              <p class="section-kicker">Relacionados</p>
              <div class="mt-4 flex flex-col gap-2">
                <a
                  v-if="payment.bookingId"
                  :href="route('bookings.show', payment.bookingId)"
                  class="btn-ghost"
                >
                  Ver agendamento
                </a>
                <span v-else class="rounded-2xl bg-slate-50 px-4 py-3 text-sm font-bold text-slate-400 dark:bg-white/5">
                  Sem booking vinculado
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
.section-kicker,
.info-label {
  font-size: 0.72rem;
  font-weight: 900;
  letter-spacing: 0.14em;
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

.info-card,
.code-card,
.split-row {
  border-radius: 1rem;
  background: rgb(248 250 252);
  padding: 0.85rem;
}

:global(.dark) .info-card,
:global(.dark) .code-card,
:global(.dark) .split-row {
  background: rgb(255 255 255 / 0.05);
}

.info-value {
  margin-top: 0.3rem;
  font-size: 0.95rem;
  font-weight: 900;
  color: rgb(15 23 42);
}

:global(.dark) .info-value {
  color: white;
}

.code-value {
  margin-top: 0.35rem;
  overflow-wrap: anywhere;
  font-family: ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas, "Liberation Mono", monospace;
  font-size: 0.78rem;
  font-weight: 800;
  color: rgb(71 85 105);
}

:global(.dark) .code-value {
  color: rgb(203 213 225);
}

.split-row {
  display: flex;
  align-items: center;
  justify-content: space-between;
  gap: 1rem;
  font-size: 0.88rem;
}

.split-row span {
  font-weight: 800;
  color: rgb(148 163 184);
}

.split-row strong {
  font-weight: 900;
  color: rgb(15 23 42);
}

:global(.dark) .split-row strong {
  color: white;
}

.btn-primary,
.btn-ghost,
.btn-white {
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

.btn-white {
  background: white;
  color: rgb(7 27 51);
  box-shadow: 0 16px 30px rgb(0 0 0 / 0.18);
}

.btn-primary:hover,
.btn-ghost:hover,
.btn-white:hover {
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
</style>
