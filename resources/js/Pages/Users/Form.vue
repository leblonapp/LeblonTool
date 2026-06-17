<script setup>
import { useForm } from '@inertiajs/vue3'
import { computed } from 'vue'
import AppLayout from '@/Layouts/AppLayout.vue'

const props = defineProps({
  user: Object,
})

const isEditing = computed(() => !!props.user)

const form = useForm({
  display_name: props.user?.display_name ?? '',
  email: props.user?.email ?? '',
  role: props.user?.role ?? 'viewer',
})

const roleOptions = [
  {
    value: 'viewer',
    label: 'Viewer',
    eyebrow: 'Leitura',
    description: 'Acompanha dados e consultas sem alterar registros.',
    color: 'slate',
    iconPath: 'M2.25 12s3.75-6.75 9.75-6.75S21.75 12 21.75 12s-3.75 6.75-9.75 6.75S2.25 12 2.25 12z M15 12a3 3 0 11-6 0 3 3 0 016 0z',
  },
  {
    value: 'editor',
    label: 'Editor',
    eyebrow: 'Operação',
    description: 'Pode editar conteúdos, fornecedores e fluxos operacionais.',
    color: 'cyan',
    iconPath: 'M16.862 4.487l1.651-1.65a1.875 1.875 0 112.652 2.652L9.75 16.902 6 18l1.098-3.75L16.862 4.487z M19 20H5',
  },
  {
    value: 'admin',
    label: 'Admin',
    eyebrow: 'Controle',
    description: 'Gerencia usuários, permissões e áreas sensíveis do painel.',
    color: 'rose',
    iconPath: 'M12 3l7 4v5c0 4.5-3 7.5-7 9-4-1.5-7-4.5-7-9V7l7-4z M9.5 12l1.8 1.8 3.7-4',
  },
]

const selectedRole = computed(() =>
  roleOptions.find(option => option.value === form.role) ?? roleOptions[0]
)

const userInitials = computed(() => {
  const name = form.display_name?.trim()

  if (!name) {
    return 'US'
  }

  return name
    .split(/\s+/)
    .slice(0, 2)
    .map(part => part[0])
    .join('')
    .toUpperCase()
})

const displayNamePreview = computed(() => form.display_name || 'Nome do usuário')
const emailPreview = computed(() => form.email || 'email@empresa.com')

function submit() {
  if (isEditing.value) {
    form.put(route('users.update', props.user.id))
  } else {
    form.post(route('users.store'))
  }
}
</script>

<template>
  <AppLayout :title="isEditing ? 'Editar Usuário' : 'Novo Usuário'">
    <div class="min-h-screen bg-slate-50 text-slate-900 transition-colors dark:bg-slate-950 dark:text-white">
      <div class="relative overflow-hidden border-b border-white/70 bg-white dark:border-slate-800 dark:bg-slate-950">
        <div class="absolute inset-0 pointer-events-none">
          <div class="absolute -right-24 -top-32 h-80 w-80 rounded-full bg-cyan-300/30 blur-3xl dark:bg-cyan-500/10"></div>
          <div class="absolute -bottom-40 left-[-8rem] h-80 w-80 rounded-full bg-rose-300/25 blur-3xl dark:bg-rose-500/10"></div>
        </div>

        <div class="relative mx-auto max-w-6xl px-5 py-6 sm:px-8 lg:py-8">
          <div class="flex flex-col gap-6 lg:flex-row lg:items-center lg:justify-between">
            <div class="flex items-center gap-4">
              <a
                :href="route('users.index')"
                class="group flex h-10 w-10 shrink-0 items-center justify-center rounded-xl border border-slate-200 bg-white text-slate-500 shadow-sm transition duration-300 hover:-translate-x-0.5 hover:border-slate-300 hover:text-slate-950 hover:shadow-md dark:border-slate-800 dark:bg-slate-900 dark:text-slate-400 dark:hover:text-white"
                aria-label="Voltar"
              >
                <svg class="h-4 w-4 transition group-hover:-translate-x-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                </svg>
              </a>

              <div>
                <p class="text-xs font-semibold uppercase tracking-[0.22em] text-cyan-600 dark:text-cyan-300">
                  Acesso e permissões
                </p>
                <h1 class="mt-1 text-2xl font-black tracking-tight text-slate-950 sm:text-3xl dark:text-white">
                  {{ isEditing ? 'Editar usuário' : 'Novo usuário' }}
                </h1>
                <p class="mt-1 max-w-2xl text-sm text-slate-500 dark:text-slate-400">
                  {{ isEditing ? 'Atualize identidade e perfil de acesso com cuidado.' : 'Crie um acesso claro, com permissão adequada desde o primeiro login.' }}
                </p>
              </div>
            </div>

            <div class="rounded-2xl border border-slate-200 bg-white/80 p-3 shadow-sm backdrop-blur dark:border-slate-800 dark:bg-slate-900/80">
              <div class="flex items-center gap-3">
                <div class="flex h-11 w-11 items-center justify-center rounded-2xl bg-slate-950 text-sm font-black text-white dark:bg-white dark:text-slate-950">
                  {{ userInitials }}
                </div>
                <div class="min-w-0">
                  <p class="truncate text-sm font-black text-slate-950 dark:text-white">{{ displayNamePreview }}</p>
                  <p class="truncate text-xs text-slate-400">{{ selectedRole.label }} · {{ emailPreview }}</p>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>

      <main class="mx-auto max-w-6xl px-5 py-6 sm:px-8 lg:py-8">
        <div class="grid gap-6 lg:grid-cols-[1fr_22rem]">
          <form @submit.prevent="submit" class="panel p-5 sm:p-7">
            <div class="mb-7 flex flex-col gap-2 sm:flex-row sm:items-start sm:justify-between">
              <div>
                <p class="section-kicker">Identidade</p>
                <h2 class="section-title">Dados do usuário</h2>
              </div>
              <span
                class="inline-flex w-fit items-center gap-2 rounded-full px-3 py-1 text-xs font-black"
                :class="{
                  'bg-slate-100 text-slate-600 dark:bg-slate-800 dark:text-slate-300': form.role === 'viewer',
                  'bg-cyan-100 text-cyan-700 dark:bg-cyan-500/10 dark:text-cyan-300': form.role === 'editor',
                  'bg-rose-100 text-rose-700 dark:bg-rose-500/10 dark:text-rose-300': form.role === 'admin',
                }"
              >
                <span class="h-2 w-2 rounded-full bg-current"></span>
                {{ selectedRole.label }}
              </span>
            </div>

            <div class="space-y-5">
              <div>
                <label class="field-label">Nome completo</label>
                <div class="relative">
                  <svg class="pointer-events-none absolute left-4 top-1/2 h-4 w-4 -translate-y-1/2 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M15.75 7.5a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.5 20.25a7.5 7.5 0 0115 0"/>
                  </svg>
                  <input
                    v-model="form.display_name"
                    type="text"
                    placeholder="Ex: João Silva"
                    class="field-input pl-11"
                    :class="form.errors.display_name ? 'field-input-error' : ''"
                  />
                </div>
                <p v-if="form.errors.display_name" class="field-error">{{ form.errors.display_name }}</p>
              </div>

              <div>
                <label class="field-label">E-mail</label>
                <div class="relative">
                  <svg class="pointer-events-none absolute left-4 top-1/2 h-4 w-4 -translate-y-1/2 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M21.75 7.5v9a2.25 2.25 0 01-2.25 2.25h-15A2.25 2.25 0 012.25 16.5v-9m19.5 0A2.25 2.25 0 0019.5 5.25h-15A2.25 2.25 0 002.25 7.5m19.5 0l-8.55 5.7a2.25 2.25 0 01-2.4 0L2.25 7.5"/>
                  </svg>
                  <input
                    v-model="form.email"
                    type="email"
                    placeholder="joao@exemplo.com"
                    class="field-input pl-11"
                    :class="form.errors.email ? 'field-input-error' : ''"
                  />
                </div>
                <p v-if="form.errors.email" class="field-error">{{ form.errors.email }}</p>
              </div>

              <div>
                <label class="field-label">Perfil de acesso</label>
                <div class="grid gap-3 xl:grid-cols-3">
                  <label
                    v-for="option in roleOptions"
                    :key="option.value"
                    class="role-card group"
                    :class="form.role === option.value ? `role-card-active role-${option.color}` : ''"
                  >
                    <input type="radio" v-model="form.role" :value="option.value" class="hidden" />
                    <span class="role-icon">
                      <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" :d="option.iconPath"/>
                      </svg>
                    </span>
                    <span class="mt-4 block text-[11px] font-black uppercase tracking-[0.18em] opacity-60">{{ option.eyebrow }}</span>
                    <span class="mt-1 block text-base font-black">{{ option.label }}</span>
                    <span class="mt-2 block text-xs leading-5 opacity-70">{{ option.description }}</span>
                  </label>
                </div>
                <p v-if="form.errors.role" class="field-error">{{ form.errors.role }}</p>
              </div>
            </div>

            <div class="mt-7 flex flex-col-reverse gap-3 border-t border-slate-100 pt-5 dark:border-slate-800 sm:flex-row sm:items-center sm:justify-end">
              <a :href="route('users.index')" class="btn-ghost justify-center">
                Cancelar
              </a>
              <button type="submit" :disabled="form.processing" class="btn-primary justify-center">
                <svg v-if="form.processing" class="h-4 w-4 animate-spin" fill="none" viewBox="0 0 24 24">
                  <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                  <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"/>
                </svg>
                {{ isEditing ? 'Salvar alterações' : 'Criar usuário' }}
              </button>
            </div>
          </form>

          <aside class="space-y-4">
            <div class="panel overflow-hidden p-0">
              <div class="relative bg-slate-950 p-6 text-white">
                <div class="absolute -right-10 -top-10 h-36 w-36 rounded-full bg-cyan-400/30 blur-2xl"></div>
                <div class="relative">
                  <p class="text-xs font-black uppercase tracking-[0.22em] text-cyan-200">Preview</p>
                  <div class="mt-6 flex items-center gap-4">
                    <div class="flex h-16 w-16 shrink-0 items-center justify-center rounded-3xl bg-white text-xl font-black text-slate-950 shadow-xl">
                      {{ userInitials }}
                    </div>
                    <div class="min-w-0">
                      <h3 class="truncate text-lg font-black">{{ displayNamePreview }}</h3>
                      <p class="truncate text-sm text-white/60">{{ emailPreview }}</p>
                    </div>
                  </div>
                  <div class="mt-6 rounded-2xl bg-white/10 p-4 backdrop-blur">
                    <p class="text-xs text-white/50">Perfil selecionado</p>
                    <p class="mt-1 text-2xl font-black">{{ selectedRole.label }}</p>
                    <p class="mt-2 text-sm leading-6 text-white/65">{{ selectedRole.description }}</p>
                  </div>
                </div>
              </div>
            </div>

            <div class="panel p-5">
              <p class="section-kicker">Checklist</p>
              <h3 class="mt-1 text-base font-black text-slate-950 dark:text-white">Antes de salvar</h3>
              <div class="mt-4 space-y-3">
                <div class="check-row" :class="form.display_name ? 'check-row-ok' : ''">
                  <span class="check-dot"></span>
                  Nome identificável
                </div>
                <div class="check-row" :class="form.email ? 'check-row-ok' : ''">
                  <span class="check-dot"></span>
                  E-mail de acesso
                </div>
                <div class="check-row check-row-ok">
                  <span class="check-dot"></span>
                  Perfil definido como {{ selectedRole.label }}
                </div>
              </div>
            </div>
          </aside>
        </div>
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
  background: rgb(15 23 42);
}

.section-kicker {
  font-size: 0.72rem;
  font-weight: 800;
  letter-spacing: 0.18em;
  text-transform: uppercase;
  color: rgb(8 145 178);
}

:global(.dark) .section-kicker {
  color: rgb(103 232 249);
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

.field-label {
  margin-bottom: 0.45rem;
  display: block;
  font-size: 0.78rem;
  font-weight: 800;
  color: rgb(71 85 105);
}

:global(.dark) .field-label {
  color: rgb(203 213 225);
}

.field-input {
  width: 100%;
  border-radius: 1rem;
  border: 1px solid rgb(226 232 240);
  background: rgb(248 250 252);
  padding: 0.85rem 1rem;
  font-size: 0.9rem;
  color: rgb(15 23 42);
  outline: none;
  transition: border-color 180ms ease, box-shadow 180ms ease, background-color 180ms ease;
}

.field-input:focus {
  border-color: rgb(34 211 238);
  background: white;
  box-shadow: 0 0 0 4px rgb(103 232 249 / 0.18);
}

:global(.dark) .field-input {
  border-color: rgb(51 65 85);
  background: rgb(15 23 42);
  color: white;
}

.field-input-error {
  border-color: rgb(251 113 133);
  background: rgb(255 241 242);
}

.field-error {
  margin-top: 0.35rem;
  font-size: 0.75rem;
  font-weight: 700;
  color: rgb(225 29 72);
}

.role-card {
  min-height: 12rem;
  cursor: pointer;
  border-radius: 1.25rem;
  border: 1px solid rgb(226 232 240);
  background: rgb(255 255 255);
  padding: 1rem;
  color: rgb(71 85 105);
  transition: transform 240ms ease, box-shadow 240ms ease, border-color 240ms ease, background-color 240ms ease, color 240ms ease;
}

.role-card:hover {
  transform: translateY(-3px);
  border-color: rgb(203 213 225);
  box-shadow: 0 16px 30px rgb(15 23 42 / 0.08);
}

:global(.dark) .role-card {
  border-color: rgb(51 65 85);
  background: rgb(15 23 42);
  color: rgb(203 213 225);
}

.role-card-active {
  color: white;
  box-shadow: 0 18px 35px rgb(15 23 42 / 0.16);
}

.role-slate {
  border-color: rgb(15 23 42);
  background: rgb(15 23 42);
}

.role-cyan {
  border-color: rgb(8 145 178);
  background: rgb(8 145 178);
}

.role-rose {
  border-color: rgb(225 29 72);
  background: rgb(225 29 72);
}

:global(.dark) .role-card-active {
  border-color: transparent;
}

.role-icon {
  display: flex;
  height: 2.75rem;
  width: 2.75rem;
  align-items: center;
  justify-content: center;
  border-radius: 1rem;
  background: rgb(241 245 249);
  color: rgb(15 23 42);
  transition: transform 240ms ease, background-color 240ms ease, color 240ms ease;
}

.role-card:hover .role-icon {
  transform: scale(1.05);
}

.role-card-active .role-icon {
  background: rgb(255 255 255 / 0.16);
  color: white;
}

:global(.dark) .role-icon {
  background: rgb(30 41 59);
  color: rgb(226 232 240);
}

.btn-primary,
.btn-ghost {
  display: inline-flex;
  min-height: 2.75rem;
  align-items: center;
  gap: 0.5rem;
  border-radius: 1rem;
  padding: 0.7rem 1rem;
  font-size: 0.875rem;
  font-weight: 900;
  transition: transform 220ms ease, box-shadow 220ms ease, background-color 220ms ease, color 220ms ease, border-color 220ms ease;
}

.btn-primary {
  background: rgb(8 145 178);
  color: white;
  box-shadow: 0 12px 24px rgb(8 145 178 / 0.18);
}

.btn-primary:hover {
  transform: translateY(-1px);
  background: rgb(14 116 144);
  box-shadow: 0 18px 30px rgb(8 145 178 / 0.22);
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
  border-color: rgb(203 213 225);
  color: rgb(15 23 42);
  box-shadow: 0 10px 20px rgb(15 23 42 / 0.06);
}

:global(.dark) .btn-ghost {
  border-color: rgb(51 65 85);
  background: rgb(15 23 42);
  color: rgb(203 213 225);
}

:global(.dark) .btn-ghost:hover {
  color: white;
}

.check-row {
  display: flex;
  align-items: center;
  gap: 0.65rem;
  border-radius: 1rem;
  background: rgb(248 250 252);
  padding: 0.8rem;
  font-size: 0.82rem;
  font-weight: 800;
  color: rgb(100 116 139);
  transition: background-color 180ms ease, color 180ms ease;
}

:global(.dark) .check-row {
  background: rgb(30 41 59 / 0.7);
  color: rgb(148 163 184);
}

.check-row-ok {
  color: rgb(21 128 61);
  background: rgb(240 253 244);
}

:global(.dark) .check-row-ok {
  color: rgb(134 239 172);
  background: rgb(34 197 94 / 0.08);
}

.check-dot {
  height: 0.55rem;
  width: 0.55rem;
  border-radius: 9999px;
  background: rgb(203 213 225);
}

.check-row-ok .check-dot {
  background: rgb(34 197 94);
}
</style>
