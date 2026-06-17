<script setup>
import { useForm } from '@inertiajs/vue3'
import { computed } from 'vue'
import AppLayout from '@/Layouts/AppLayout.vue'

const props = defineProps({
  category: Object,
})

const isEditing = computed(() => !!props.category)

const form = useForm({
  name:               props.category?.name               ?? '',
  icon:               props.category?.icon               ?? '',
  defaultBookingType: props.category?.defaultBookingType ?? 'flexivel',
  sortOrder:          props.category?.sortOrder          ?? 0,
  allowsInstant:      props.category?.allowsInstant      ?? false,
  isActive:           props.category?.isActive           ?? true,
})

const bookingOptions = [
  {
    value: 'flexivel',
    label: 'Flexível',
    description: 'Cliente escolhe melhor horário dentro da agenda disponível.',
    icon: 'M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z',
  },
  {
    value: 'fixo',
    label: 'Fixo',
    description: 'Horários e duração seguem uma configuração mais rígida.',
    icon: 'M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z',
  },
]

const toggles = [
  {
    field: 'allowsInstant',
    label: 'Agendamento instantâneo',
    desc: 'Permite que o cliente confirme a reserva sem análise manual.',
  },
  {
    field: 'isActive',
    label: 'Categoria ativa',
    desc: 'Exibe esta categoria no app e nos fluxos de contratação.',
  },
]

const previewName = computed(() => form.name || 'Nome da categoria')
const previewIcon = computed(() => form.icon || 'sem_icone')
const previewInitial = computed(() => (form.name?.[0] || 'C').toUpperCase())

function submit() {
  if (isEditing.value) {
    form.put(route('categories.update', props.category.id))
  } else {
    form.post(route('categories.store'))
  }
}
</script>

<template>
  <AppLayout :title="isEditing ? 'Editar Categoria' : 'Nova Categoria'">
    <div class="min-h-screen bg-slate-100 text-slate-950 transition-colors dark:bg-[#07111f] dark:text-white">
      <section class="relative overflow-hidden bg-[#071b33] text-white">
        <div class="absolute inset-0 pointer-events-none">
          <div class="absolute -right-24 -top-32 h-80 w-80 rounded-full bg-orange-400/25 blur-3xl"></div>
          <div class="absolute -bottom-36 left-[-6rem] h-80 w-80 rounded-full bg-sky-400/15 blur-3xl"></div>
          <div class="absolute inset-x-0 bottom-0 h-px bg-gradient-to-r from-transparent via-orange-300/60 to-transparent"></div>
        </div>

        <div class="relative mx-auto max-w-6xl px-5 py-7 sm:px-8 lg:py-9">
          <div class="flex flex-col gap-6 lg:flex-row lg:items-center lg:justify-between">
            <div class="flex items-center gap-4">
              <a
                :href="route('categories.index')"
                class="group flex h-10 w-10 shrink-0 items-center justify-center rounded-xl border border-white/10 bg-white/8 text-slate-300 shadow-sm backdrop-blur transition duration-300 hover:-translate-x-0.5 hover:bg-white/14 hover:text-white"
                aria-label="Voltar"
              >
                <svg class="h-4 w-4 transition group-hover:-translate-x-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                </svg>
              </a>

              <div>
                <div class="inline-flex items-center gap-2 rounded-full border border-white/10 bg-white/8 px-3 py-1.5 text-xs font-black uppercase tracking-[0.2em] text-orange-200 backdrop-blur">
                  <span class="h-2 w-2 rounded-full bg-orange-400 shadow-[0_0_16px_rgba(251,146,60,0.9)]"></span>
                  Catálogo
                </div>
                <h1 class="mt-4 text-3xl font-black tracking-tight sm:text-4xl">
                  {{ isEditing ? 'Editar categoria' : 'Nova categoria' }}
                </h1>
                <p class="mt-2 max-w-2xl text-sm leading-6 text-slate-300">
                  Configure como esta categoria aparece para clientes, fornecedores e regras de agendamento.
                </p>
              </div>
            </div>

            <div class="rounded-3xl border border-white/10 bg-white/8 p-4 backdrop-blur">
              <p class="text-xs font-black uppercase tracking-[0.18em] text-slate-400">Preview</p>
              <div class="mt-3 flex items-center gap-3">
                <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-gradient-to-br from-orange-500 to-amber-400 text-sm font-black text-white shadow-lg shadow-orange-500/25">
                  {{ previewInitial }}
                </div>
                <div class="min-w-0">
                  <p class="truncate text-sm font-black">{{ previewName }}</p>
                  <p class="truncate font-mono text-xs text-orange-200">{{ previewIcon }}</p>
                </div>
              </div>
            </div>
          </div>
        </div>
      </section>

      <main class="mx-auto max-w-6xl px-5 py-6 sm:px-8 lg:py-8">
        <form @submit.prevent="submit" class="grid gap-6 lg:grid-cols-[1fr_22rem]">
          <section class="panel p-5 sm:p-6">
            <div class="mb-6">
              <p class="section-kicker">Identidade</p>
              <h2 class="section-title">Dados principais</h2>
            </div>

            <div class="space-y-5">
              <div>
                <label class="field-label">Nome</label>
                <input
                  v-model="form.name"
                  type="text"
                  placeholder="Ex: Barbearia"
                  class="field-input"
                  :class="form.errors.name ? 'field-input-error' : ''"
                />
                <p v-if="form.errors.name" class="field-error">{{ form.errors.name }}</p>
              </div>

              <div class="grid gap-4 md:grid-cols-[1fr_10rem]">
                <div>
                  <label class="field-label">Ícone técnico</label>
                  <div class="relative">
                    <input
                      v-model="form.icon"
                      type="text"
                      placeholder="Ex: content_cut"
                      class="field-input pr-12 font-mono"
                      :class="form.errors.icon ? 'field-input-error' : ''"
                    />
                    <div class="pointer-events-none absolute right-3 top-1/2 flex h-8 w-8 -translate-y-1/2 items-center justify-center rounded-xl bg-orange-100 text-orange-700 dark:bg-orange-500/10 dark:text-orange-300">
                      <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A2 2 0 013 12V7a4 4 0 014-4z"/>
                      </svg>
                    </div>
                  </div>
                  <p v-if="form.errors.icon" class="field-error">{{ form.errors.icon }}</p>
                </div>

                <div>
                  <label class="field-label">Ordem</label>
                  <input
                    v-model.number="form.sortOrder"
                    type="number"
                    min="0"
                    placeholder="1"
                    class="field-input"
                  />
                  <p v-if="form.errors.sortOrder" class="field-error">{{ form.errors.sortOrder }}</p>
                </div>
              </div>

              <div>
                <label class="field-label">Tipo de agendamento padrão</label>
                <div class="grid gap-3 md:grid-cols-2">
                  <label
                    v-for="option in bookingOptions"
                    :key="option.value"
                    class="option-card"
                    :class="form.defaultBookingType === option.value ? 'option-card-active' : ''"
                  >
                    <input type="radio" v-model="form.defaultBookingType" :value="option.value" class="hidden" />
                    <span class="option-icon">
                      <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" :d="option.icon"/>
                      </svg>
                    </span>
                    <span class="min-w-0">
                      <span class="block text-sm font-black">{{ option.label }}</span>
                      <span class="mt-1 block text-xs leading-5 opacity-70">{{ option.description }}</span>
                    </span>
                  </label>
                </div>
                <p v-if="form.errors.defaultBookingType" class="field-error">{{ form.errors.defaultBookingType }}</p>
              </div>
            </div>
          </section>

          <aside class="space-y-4">
            <section class="panel overflow-hidden">
              <div class="bg-[#071b33] p-5 text-white">
                <p class="text-xs font-black uppercase tracking-[0.2em] text-orange-200">Card no app</p>
                <div class="mt-5 rounded-3xl bg-white p-4 text-slate-950 shadow-2xl shadow-slate-950/25">
                  <div class="flex items-center gap-3">
                    <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-gradient-to-br from-orange-500 to-amber-400 text-sm font-black text-white">
                      {{ previewInitial }}
                    </div>
                    <div class="min-w-0">
                      <p class="truncate text-base font-black">{{ previewName }}</p>
                      <p class="truncate font-mono text-xs font-bold text-slate-400">{{ previewIcon }}</p>
                    </div>
                  </div>
                  <div class="mt-4 grid grid-cols-2 gap-2">
                    <div class="rounded-2xl bg-slate-50 p-3">
                      <p class="text-[11px] font-bold text-slate-400">Agenda</p>
                      <p class="mt-1 text-sm font-black capitalize">{{ form.defaultBookingType }}</p>
                    </div>
                    <div class="rounded-2xl bg-slate-50 p-3">
                      <p class="text-[11px] font-bold text-slate-400">Instant</p>
                      <p class="mt-1 text-sm font-black">{{ form.allowsInstant ? 'Sim' : 'Não' }}</p>
                    </div>
                  </div>
                </div>
              </div>
            </section>

            <section class="panel p-5">
              <p class="section-kicker">Publicação</p>
              <h3 class="mt-1 text-base font-black text-slate-950 dark:text-white">Regras rápidas</h3>

              <div class="mt-4 space-y-3">
                <div
                  v-for="toggle in toggles"
                  :key="toggle.field"
                  class="toggle-card"
                  :class="form[toggle.field] ? 'toggle-card-active' : ''"
                >
                  <div class="min-w-0">
                    <p class="text-sm font-black">{{ toggle.label }}</p>
                    <p class="mt-1 text-xs leading-5 opacity-65">{{ toggle.desc }}</p>
                  </div>
                  <button
                    type="button"
                    @click="form[toggle.field] = !form[toggle.field]"
                    class="toggle-switch"
                    :class="form[toggle.field] ? 'toggle-switch-on' : ''"
                    :aria-label="toggle.label"
                  >
                    <span :class="form[toggle.field] ? 'translate-x-5' : 'translate-x-0'"></span>
                  </button>
                </div>
              </div>
            </section>

            <div class="panel p-4">
              <div class="flex flex-col-reverse gap-3 sm:flex-row sm:justify-end lg:flex-col-reverse">
                <a :href="route('categories.index')" class="btn-ghost">
                  Cancelar
                </a>
                <button type="submit" :disabled="form.processing" class="btn-primary">
                  <svg v-if="form.processing" class="h-4 w-4 animate-spin" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"/>
                  </svg>
                  {{ isEditing ? 'Salvar alterações' : 'Cadastrar categoria' }}
                </button>
              </div>
            </div>
          </aside>
        </form>
      </main>
    </div>
  </AppLayout>
</template>

<style scoped>
.panel {
  border: 1px solid rgb(226 232 240);
  border-radius: 1.5rem;
  background: rgb(255 255 255);
  box-shadow: 0 1px 2px rgb(15 23 42 / 0.04);
}

:global(.dark) .panel {
  border-color: rgb(30 41 59);
  background: rgb(8 20 36);
}

.section-kicker {
  font-size: 0.72rem;
  font-weight: 900;
  letter-spacing: 0.18em;
  text-transform: uppercase;
  color: rgb(234 88 12);
}

.section-title {
  margin-top: 0.25rem;
  font-size: 1.35rem;
  font-weight: 900;
  color: rgb(2 6 23);
}

:global(.dark) .section-title {
  color: white;
}

.field-label {
  margin-bottom: 0.45rem;
  display: block;
  font-size: 0.78rem;
  font-weight: 900;
  color: rgb(71 85 105);
}

:global(.dark) .field-label {
  color: rgb(203 213 225);
}

.field-input {
  width: 100%;
  border-radius: 1rem;
  border: 1px solid rgb(226 232 240);
  background: white;
  padding: 0.85rem 1rem;
  font-size: 0.9rem;
  font-weight: 700;
  color: rgb(15 23 42);
  outline: none;
  transition: border-color 180ms ease, box-shadow 180ms ease, background-color 180ms ease;
}

.field-input:focus {
  border-color: rgb(251 146 60);
  box-shadow: 0 0 0 4px rgb(251 146 60 / 0.16);
}

:global(.dark) .field-input {
  border-color: rgb(30 41 59);
  background: rgb(7 17 31);
  color: white;
}

.field-input-error {
  border-color: rgb(251 113 133);
  background: rgb(255 241 242);
}

.field-error {
  margin-top: 0.35rem;
  font-size: 0.75rem;
  font-weight: 800;
  color: rgb(225 29 72);
}

.option-card {
  display: flex;
  min-height: 7.2rem;
  cursor: pointer;
  gap: 0.9rem;
  border-radius: 1.25rem;
  border: 1px solid rgb(226 232 240);
  background: white;
  padding: 1rem;
  color: rgb(71 85 105);
  transition: transform 220ms ease, border-color 220ms ease, box-shadow 220ms ease, background-color 220ms ease, color 220ms ease;
}

.option-card:hover {
  transform: translateY(-2px);
  border-color: rgb(251 146 60 / 0.5);
  box-shadow: 0 16px 26px rgb(15 23 42 / 0.08);
}

.option-card-active {
  border-color: rgb(249 115 22);
  background: rgb(255 237 213);
  color: rgb(124 45 18);
}

:global(.dark) .option-card {
  border-color: rgb(30 41 59);
  background: rgb(7 17 31);
  color: rgb(203 213 225);
}

:global(.dark) .option-card-active {
  border-color: rgb(249 115 22);
  background: rgb(249 115 22 / 0.12);
  color: rgb(253 186 116);
}

.option-icon {
  display: flex;
  height: 2.5rem;
  width: 2.5rem;
  flex-shrink: 0;
  align-items: center;
  justify-content: center;
  border-radius: 0.9rem;
  background: rgb(241 245 249);
  color: rgb(15 23 42);
}

.option-card-active .option-icon {
  background: rgb(249 115 22);
  color: white;
}

:global(.dark) .option-icon {
  background: rgb(255 255 255 / 0.08);
  color: rgb(226 232 240);
}

.toggle-card {
  display: flex;
  align-items: center;
  justify-content: space-between;
  gap: 1rem;
  border-radius: 1.15rem;
  border: 1px solid rgb(226 232 240);
  background: rgb(248 250 252);
  padding: 0.9rem;
  color: rgb(71 85 105);
  transition: border-color 200ms ease, background-color 200ms ease, color 200ms ease;
}

.toggle-card-active {
  border-color: rgb(251 146 60 / 0.45);
  background: rgb(255 247 237);
  color: rgb(124 45 18);
}

:global(.dark) .toggle-card {
  border-color: rgb(30 41 59);
  background: rgb(255 255 255 / 0.04);
  color: rgb(203 213 225);
}

:global(.dark) .toggle-card-active {
  border-color: rgb(249 115 22 / 0.5);
  background: rgb(249 115 22 / 0.1);
  color: rgb(253 186 116);
}

.toggle-switch {
  position: relative;
  height: 1.75rem;
  width: 3.25rem;
  flex-shrink: 0;
  border-radius: 9999px;
  background: rgb(203 213 225);
  transition: background-color 200ms ease, box-shadow 200ms ease;
}

.toggle-switch-on {
  background: rgb(249 115 22);
  box-shadow: 0 8px 18px rgb(249 115 22 / 0.24);
}

.toggle-switch span {
  position: absolute;
  left: 0.25rem;
  top: 0.25rem;
  height: 1.25rem;
  width: 1.25rem;
  border-radius: 9999px;
  background: white;
  box-shadow: 0 4px 10px rgb(15 23 42 / 0.2);
  transition: transform 200ms ease;
}

.btn-primary,
.btn-ghost {
  display: inline-flex;
  min-height: 2.65rem;
  align-items: center;
  justify-content: center;
  gap: 0.5rem;
  border-radius: 1rem;
  padding: 0.7rem 1rem;
  font-size: 0.875rem;
  font-weight: 900;
  transition: transform 220ms ease, box-shadow 220ms ease, background-color 220ms ease, color 220ms ease, border-color 220ms ease;
}

.btn-primary {
  background: rgb(249 115 22);
  color: white;
  box-shadow: 0 12px 24px rgb(249 115 22 / 0.2);
}

.btn-primary:hover {
  transform: translateY(-1px);
  background: rgb(234 88 12);
  box-shadow: 0 18px 30px rgb(249 115 22 / 0.26);
}

.btn-primary:disabled {
  cursor: not-allowed;
  opacity: 0.65;
  transform: none;
}

.btn-ghost {
  border: 1px solid rgb(226 232 240);
  background: white;
  color: rgb(71 85 105);
}

.btn-ghost:hover {
  transform: translateY(-1px);
  color: rgb(15 23 42);
  box-shadow: 0 10px 20px rgb(15 23 42 / 0.06);
}

:global(.dark) .btn-ghost {
  border-color: rgb(30 41 59);
  background: rgb(7 17 31);
  color: rgb(203 213 225);
}
</style>
