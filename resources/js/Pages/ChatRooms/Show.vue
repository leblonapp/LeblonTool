<script setup>
import { ref, computed, nextTick, onMounted } from 'vue'
import AppLayout from '@/Layouts/AppLayout.vue'

const props = defineProps({
  room:     Object,
  messages: Array,
})

// ─── Composer state ───────────────────────────────────────────────────────────
const inputText  = ref('')
const inputRef   = ref(null)
const msgsRef    = ref(null)
const isTyping   = ref(false)

function autoResize() {
  const el = inputRef.value
  if (!el) return
  el.style.height = 'auto'
  el.style.height = Math.min(el.scrollHeight, 120) + 'px'
}

function handleKey(e) {
  if (e.key === 'Enter' && !e.shiftKey) {
    e.preventDefault()
    sendMessage()
  }
}

function sendMessage() {
  if (!inputText.value.trim()) return
  inputText.value = ''
  nextTick(() => {
    if (inputRef.value) inputRef.value.style.height = 'auto'
    scrollBottom()
  })
}

function scrollBottom() {
  nextTick(() => {
    if (msgsRef.value) msgsRef.value.scrollTop = msgsRef.value.scrollHeight
  })
}

onMounted(() => scrollBottom())

// ─── Helpers ──────────────────────────────────────────────────────────────────
function senderRole(msg) {
  if (msg.senderId === 'system') return 'system'
  if (msg.senderId === props.room?.providerId) return 'provider'
  return 'client'
}

function isRight(msg) {
  return msg.senderId === props.room?.providerId
}

const unreadTotal = computed(() =>
  (props.room?.unreadClient ?? 0) + (props.room?.unreadProvider ?? 0)
)
</script>

<template>
  <AppLayout title="Conversa">

    <!-- ── Page shell (same dark navy as booking show) ────────────────────── -->
    <div class="min-h-screen bg-[#07111f] text-white flex flex-col">

      <!-- ── Hero header (mirrors booking show hero) ─────────────────────── -->
      <section class="relative overflow-hidden bg-[#071b33]">
        <!-- Decorative glows -->
        <div class="absolute inset-0 pointer-events-none">
          <div class="absolute -right-24 -top-32 h-80 w-80 rounded-full bg-orange-400/20 blur-3xl"></div>
          <div class="absolute -bottom-36 -left-24 h-80 w-80 rounded-full bg-sky-400/10 blur-3xl"></div>
          <div class="absolute inset-x-0 bottom-0 h-px bg-gradient-to-r from-transparent via-orange-300/50 to-transparent"></div>
        </div>

        <div class="relative mx-auto max-w-5xl px-5 py-7 sm:px-8 lg:py-9">
          <div class="flex items-start gap-4">
            <!-- Back button -->
            <a
              :href="route('chat-rooms.index')"
              class="group mt-1 flex h-10 w-10 shrink-0 items-center justify-center rounded-xl border border-white/10 bg-white/8 text-slate-300 backdrop-blur transition duration-300 hover:-translate-x-0.5 hover:bg-white/14 hover:text-white"
              aria-label="Voltar"
            >
              <svg class="h-4 w-4 transition group-hover:-translate-x-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
              </svg>
            </a>

            <!-- Title block -->
            <div class="flex-1 min-w-0">
              <div class="inline-flex items-center gap-2 rounded-full border border-white/10 bg-white/8 px-3 py-1.5 text-xs font-black uppercase tracking-[0.2em] text-orange-200 backdrop-blur">
                <span class="h-2 w-2 rounded-full bg-orange-400 shadow-[0_0_16px_rgba(251,146,60,0.9)]"></span>
                Conversa
              </div>
              <h1 class="mt-4 text-3xl font-black tracking-tight sm:text-4xl truncate">
                {{ room.clientName }} → {{ room.providerName }}
              </h1>
              <p class="mt-2 font-mono text-xs text-slate-400">
                {{ messages.length }} mensagem(ns) · atualizado em {{ room.updatedAt }}
              </p>
            </div>

            <!-- Status badge -->
            <span
              class="mt-1 shrink-0 inline-flex items-center gap-1.5 rounded-full px-3 py-1.5 text-xs font-black"
              :class="room.status === 'active'
                ? 'bg-green-500/10 text-green-400 border border-green-500/20'
                : 'bg-white/5 text-slate-400 border border-white/10'"
            >
              <span
                class="h-1.5 w-1.5 rounded-full"
                :class="room.status === 'active' ? 'bg-green-400 animate-pulse' : 'bg-slate-500'"
              ></span>
              {{ room.status === 'active' ? 'Ativo' : room.status }}
            </span>
          </div>
        </div>
      </section>

      <!-- ── Info cards row (same metric-card style) ─────────────────────── -->
      <div class="mx-auto w-full max-w-5xl px-5 sm:px-8">
        <div class="relative z-10 -mt-5 grid grid-cols-2 gap-3 md:grid-cols-4">

          <article class="metric-card">
            <p class="metric-label">Cliente</p>
            <p class="metric-value text-base truncate">{{ room.clientName }}</p>
            <p class="metric-hint font-mono truncate">{{ room.clientId }}</p>
          </article>

          <article class="metric-card">
            <p class="metric-label">Fornecedor</p>
            <p class="metric-value text-base truncate">{{ room.providerName }}</p>
            <p class="metric-hint font-mono truncate">{{ room.providerId }}</p>
          </article>

          <article class="metric-card">
            <p class="metric-label">Agendamento</p>
            <p class="metric-value text-base font-mono truncate">{{ room.bookingId ?? '—' }}</p>
            <p class="metric-hint">vinculado</p>
          </article>

          <article class="metric-card">
            <p class="metric-label">Não lidas</p>
            <p class="metric-value">{{ unreadTotal }}</p>
            <div class="mt-1 flex gap-3">
              <span class="metric-hint">📩 cli: <strong class="text-slate-300">{{ room.unreadClient ?? 0 }}</strong></span>
              <span class="metric-hint">📩 for: <strong class="text-slate-300">{{ room.unreadProvider ?? 0 }}</strong></span>
            </div>
          </article>

        </div>
      </div>

      <!-- ── Chat panel ───────────────────────────────────────────────────── -->
      <div class="mx-auto w-full max-w-5xl flex-1 px-5 pb-8 pt-5 sm:px-8 flex flex-col">
        <div class="flex flex-1 flex-col rounded-3xl border border-white/7 bg-[#08243e] overflow-hidden shadow-2xl">

          <!-- Messages area -->
          <div
            ref="msgsRef"
            class="flex-1 overflow-y-auto px-5 py-6 space-y-1 scrollbar-thin scrollbar-track-transparent scrollbar-thumb-white/10"
          >
            <!-- Empty state -->
            <div v-if="!messages.length" class="flex flex-col items-center justify-center h-48 gap-3">
              <div class="h-14 w-14 rounded-2xl bg-white/5 flex items-center justify-center">
                <svg class="h-6 w-6 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
                </svg>
              </div>
              <p class="text-sm font-bold text-slate-500">Nenhuma mensagem ainda</p>
            </div>

            <!-- Message list -->
            <template v-for="(msg, idx) in messages" :key="msg.id">

              <!-- System pill -->
              <div v-if="msg.senderId === 'system'" class="flex justify-center py-2">
                <span class="inline-flex items-center gap-1.5 rounded-full border border-amber-500/20 bg-amber-500/8 px-4 py-1.5 text-xs font-bold text-amber-300">
                  <svg class="h-3 w-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                  </svg>
                  {{ msg.text }}
                </span>
              </div>

              <!-- Regular message -->
              <div
                v-else
                class="flex items-end gap-2.5 msg-row"
                :class="isRight(msg) ? 'flex-row-reverse' : ''"
                :style="`animation-delay: ${Math.min(idx * 30, 300)}ms`"
              >
                <!-- Avatar -->
                <div
                  class="h-7 w-7 shrink-0 rounded-full flex items-center justify-center text-xs font-black uppercase mb-0.5"
                  :class="{
                    'bg-sky-500/15 text-sky-300':    senderRole(msg) === 'client',
                    'bg-orange-500/15 text-orange-300': senderRole(msg) === 'provider',
                  }"
                >
                  {{ msg.senderName?.[0] ?? '?' }}
                </div>

                <!-- Bubble group -->
                <div class="flex flex-col gap-1 max-w-[65%]" :class="isRight(msg) ? 'items-end' : 'items-start'">
                  <!-- Sender name (first of consecutive) -->
                  <span
                    v-if="idx === 0 || messages[idx-1]?.senderId !== msg.senderId"
                    class="px-2 text-[10px] font-black uppercase tracking-wider"
                    :class="senderRole(msg) === 'provider' ? 'text-orange-400' : 'text-sky-400'"
                  >
                    {{ msg.senderName }}
                  </span>

                  <!-- Bubble -->
                  <div
                    class="rounded-2xl px-4 py-2.5 text-sm leading-relaxed transition-transform duration-100 hover:scale-[1.01]"
                    :class="{
                      'rounded-bl-sm bg-[#1e3a5f] border border-sky-500/10 text-slate-100':          senderRole(msg) === 'client',
                      'rounded-br-sm bg-orange-500/12 border border-orange-500/20 text-slate-100':   senderRole(msg) === 'provider',
                    }"
                  >
                    {{ msg.text }}
                  </div>

                  <!-- Meta row -->
                  <div
                    class="flex items-center gap-1.5 px-1 text-[10px] text-slate-600"
                    :class="isRight(msg) ? 'flex-row-reverse' : ''"
                  >
                    <span>{{ msg.createdAt }}</span>
                    <span v-if="!msg.isRead" class="h-1.5 w-1.5 rounded-full bg-sky-400"></span>
                    <span v-else class="text-sky-400">
                      <svg class="h-3 w-3 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
                      </svg>
                    </span>
                  </div>
                </div>
              </div>

            </template>
          </div>

          <!-- Divider with glow -->
          <div class="relative h-px">
            <div class="absolute inset-0 bg-gradient-to-r from-transparent via-orange-400/40 to-transparent"></div>
          </div>

          <!-- Composer -->
          <div class="bg-[#071b33] px-4 py-3">
            <div
              class="flex items-end gap-2 rounded-2xl border border-white/8 bg-[#0d2d4a] px-4 py-2 transition-all duration-200 focus-within:border-orange-400/40 focus-within:shadow-[0_0_0_3px_rgba(249,115,22,0.06)]"
            >
              <textarea
                ref="inputRef"
                v-model="inputText"
                placeholder="Escreva uma mensagem…"
                rows="1"
                class="flex-1 resize-none bg-transparent text-sm text-slate-100 placeholder-slate-600 outline-none transition-all"
                style="min-height: 22px; max-height: 120px; line-height: 1.55;"
                @input="autoResize"
                @keydown="handleKey"
              ></textarea>

              <div class="flex items-center gap-1.5 pb-0.5">
                <!-- Attach -->
                <button
                  class="flex h-8 w-8 items-center justify-center rounded-xl text-slate-500 transition hover:bg-white/6 hover:text-slate-300"
                  aria-label="Anexar arquivo"
                >
                  <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"/>
                  </svg>
                </button>

                <!-- Send -->
                <button
                  class="flex h-8 w-8 items-center justify-center rounded-xl bg-orange-500 text-white shadow-[0_8px_20px_rgba(249,115,22,0.25)] transition duration-150 hover:bg-orange-400 hover:scale-105 active:scale-95 disabled:opacity-40 disabled:cursor-not-allowed"
                  :disabled="!inputText.trim()"
                  aria-label="Enviar mensagem"
                  @click="sendMessage"
                >
                  <svg class="h-4 w-4 translate-x-px" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/>
                  </svg>
                </button>
              </div>
            </div>

            <p class="mt-2 text-center text-[10px] text-slate-700">
              Enter para enviar · Shift+Enter para nova linha
            </p>
          </div>

        </div>
      </div>

    </div>
  </AppLayout>
</template>

<style scoped>
/* ── Metric cards (same token system as Show.vue do booking) ──────────────── */
.metric-card {
  border: 1px solid rgb(255 255 255 / 0.07);
  border-radius: 1.5rem;
  background: rgb(8 20 36);
  padding: 1rem;
  box-shadow: 0 1px 2px rgb(0 0 0 / 0.2);
  transition: transform 240ms ease, box-shadow 240ms ease, border-color 240ms ease;
}
.metric-card:hover {
  transform: translateY(-3px);
  border-color: rgb(249 115 22 / 0.4);
  box-shadow: 0 18px 28px rgb(0 0 0 / 0.25);
}
.metric-label {
  font-size: 0.7rem;
  font-weight: 900;
  letter-spacing: 0.18em;
  text-transform: uppercase;
  color: rgb(234 88 12);
}
.metric-value {
  margin-top: 0.25rem;
  font-size: 1.4rem;
  font-weight: 900;
  color: white;
}
.metric-hint {
  margin-top: 0.15rem;
  font-size: 0.75rem;
  font-weight: 700;
  color: rgb(100 116 139);
}

/* ── Message row entrance animation ──────────────────────────────────────── */
.msg-row {
  animation: fadeUp 280ms ease both;
}
@keyframes fadeUp {
  from { opacity: 0; transform: translateY(8px); }
  to   { opacity: 1; transform: translateY(0); }
}

/* ── Thin scrollbar ───────────────────────────────────────────────────────── */
.scrollbar-thin { scrollbar-width: thin; scrollbar-color: rgb(255 255 255 / 0.1) transparent; }
.scrollbar-thin::-webkit-scrollbar { width: 4px; }
.scrollbar-thin::-webkit-scrollbar-track { background: transparent; }
.scrollbar-thin::-webkit-scrollbar-thumb { background: rgb(255 255 255 / 0.1); border-radius: 4px; }
</style>
