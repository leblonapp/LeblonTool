<script setup>
import { useForm, router } from '@inertiajs/vue3'
import { computed, ref } from 'vue'
import AppLayout from '@/Layouts/AppLayout.vue'

const props = defineProps({
  provider:       Object,
  services:       Array,
  weeklySchedule: Array,
})

const isEditing = computed(() => !!props.provider)
const activeTab = ref('dados')
const galleryUploads = ref([])

const tabs = computed(() => [
  { id: 'dados', label: 'Dados', hint: 'Cadastro', count: null },
  { id: 'servicos', label: 'Serviços', hint: 'Catálogo', count: props.services?.length ?? 0 },
  { id: 'horarios', label: 'Horários', hint: 'Agenda', count: activeDaysCount.value },
  { id: 'galeria', label: 'Galeria', hint: 'Imagens', count: galleryItems.value.length + galleryUploads.value.length },
])

const statusOptions = [
  { value: 'active', label: 'Ativo', description: 'Disponível para reservas', tone: 'emerald' },
  { value: 'inactive', label: 'Inativo', description: 'Oculto temporariamente', tone: 'slate' },
]

const heroImage = 'https://images.unsplash.com/photo-1521590832167-7bcbfaa6381f?auto=format&fit=crop&w=1400&q=80'

const galleryItems = computed(() => [
  {
    id: 'studio',
    title: props.provider?.displayName ?? 'Ambiente do fornecedor',
    tag: 'Destaque',
    src: heroImage,
  },
  {
    id: 'service',
    title: 'Execução do serviço',
    tag: 'Serviços',
    src: 'https://images.unsplash.com/photo-1503951914875-452162b0f3f1?auto=format&fit=crop&w=900&q=80',
  },
  {
    id: 'details',
    title: 'Detalhes do espaço',
    tag: 'Experiência',
    src: 'https://images.unsplash.com/photo-1560066984-138dadb4c035?auto=format&fit=crop&w=900&q=80',
  },
])

const providerInitial = computed(() => props.provider?.displayName?.[0] ?? 'F')
const servicesCount = computed(() => props.services?.length ?? 0)
const galleryCount = computed(() => galleryItems.value.length + galleryUploads.value.length)

const activeDaysCount = computed(() =>
  scheduleForm.days.filter(day => day.isActive).length
)

// Dados básicos
const form = useForm({
  displayName: props.provider?.displayName ?? '',
  email:       props.provider?.email       ?? '',
  phone:       props.provider?.phone       ?? '',
  cnpj:        props.provider?.cnpj        ?? '',
  status:      props.provider?.status      ?? 'active',
})

function submit() {
  if (isEditing.value) {
    form.put(route('providers.update', props.provider.id))
  } else {
    form.post(route('providers.store'))
  }
}

// Serviços
const serviceForm = useForm({ name: '', durationMin: 30, priceType: 0 })
const editingService = ref(null)

function openAddService() {
  editingService.value = null
  serviceForm.reset()
  serviceForm.name = ''
  serviceForm.durationMin = 30
  serviceForm.priceType = 0
}

function openEditService(s) {
  editingService.value = s.id
  serviceForm.name = s.name
  serviceForm.durationMin = s.durationMin
  serviceForm.priceType = s.priceType
}

function saveService() {
  if (editingService.value) {
    serviceForm.put(route('providers.services.update', [props.provider.id, editingService.value]), {
      onSuccess: () => { editingService.value = null; serviceForm.reset() },
    })
  } else {
    serviceForm.post(route('providers.services.store', props.provider.id), {
      onSuccess: () => serviceForm.reset(),
    })
  }
}

function removeService(serviceId) {
  if (confirm('Remover este serviço?')) {
    router.delete(route('providers.services.destroy', [props.provider.id, serviceId]))
  }
}

// Horários semanais
const defaultDays = [
  { dayId: 'seg', dayName: 'Segunda-feira', order: 1 },
  { dayId: 'ter', dayName: 'Terça-feira',   order: 2 },
  { dayId: 'qua', dayName: 'Quarta-feira',  order: 3 },
  { dayId: 'qui', dayName: 'Quinta-feira',  order: 4 },
  { dayId: 'sex', dayName: 'Sexta-feira',   order: 5 },
  { dayId: 'sab', dayName: 'Sábado',        order: 6 },
  { dayId: 'dom', dayName: 'Domingo',       order: 7 },
]

function buildSchedule() {
  return defaultDays.map(def => {
    const existing = props.weeklySchedule?.find(d => d.dayId === def.dayId) ?? {}
    return {
      dayId:         def.dayId,
      dayName:       def.dayName,
      order:         def.order,
      isActive:      existing.isActive      ?? false,
      startTime:     existing.startTime     ?? '08:00',
      endTime:       existing.endTime       ?? '18:00',
      hasLunchBreak: existing.hasLunchBreak ?? false,
      lunchStart:    existing.lunchStart    ?? '12:00',
      lunchEnd:      existing.lunchEnd      ?? '13:00',
    }
  })
}

const scheduleForm = useForm({ days: buildSchedule() })

function saveSchedule() {
  scheduleForm.put(route('providers.schedule.update', props.provider.id))
}

function addGalleryImages(event) {
  const files = Array.from(event.target.files ?? [])

  galleryUploads.value.push(...files.map(file => ({
    id: `${file.name}-${file.lastModified}`,
    title: file.name.replace(/\.[^/.]+$/, ''),
    tag: 'Novo',
    src: URL.createObjectURL(file),
  })))

  event.target.value = ''
}

function removeGalleryUpload(uploadId) {
  const upload = galleryUploads.value.find(item => item.id === uploadId)

  if (upload) {
    URL.revokeObjectURL(upload.src)
  }

  galleryUploads.value = galleryUploads.value.filter(item => item.id !== uploadId)
}
</script>

<template>
  <AppLayout :title="isEditing ? 'Editar Fornecedor' : 'Novo Fornecedor'">
    <div class="min-h-screen bg-slate-100 text-slate-900 transition-colors dark:bg-[#07111f] dark:text-white">
      <div class="relative overflow-hidden border-b border-white/10 bg-[#071b33] text-white">
        <div class="absolute inset-0 pointer-events-none">
          <div class="absolute -top-28 right-[-12%] h-80 w-80 rounded-full bg-orange-400/25 blur-3xl"></div>
          <div class="absolute bottom-[-10rem] left-[-7rem] h-80 w-80 rounded-full bg-sky-400/15 blur-3xl"></div>
          <div class="absolute inset-x-0 bottom-0 h-px bg-gradient-to-r from-transparent via-orange-300/60 to-transparent"></div>
        </div>

        <div class="relative mx-auto max-w-6xl px-5 py-6 sm:px-8 lg:py-8">
          <div class="flex flex-col gap-6 lg:flex-row lg:items-center lg:justify-between">
            <div class="flex items-center gap-4">
              <a
                :href="route('providers.index')"
                class="group flex h-10 w-10 shrink-0 items-center justify-center rounded-xl border border-white/10 bg-white/8 text-slate-300 shadow-sm backdrop-blur transition duration-300 hover:-translate-x-0.5 hover:bg-white/14 hover:text-white hover:shadow-md"
                aria-label="Voltar"
              >
                <svg class="h-4 w-4 transition group-hover:-translate-x-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                </svg>
              </a>

              <div class="flex min-w-0 items-center gap-4">
                <div class="hidden h-14 w-14 shrink-0 items-center justify-center rounded-2xl bg-orange-500 text-lg font-black uppercase text-white shadow-lg shadow-orange-500/25 sm:flex">
                  {{ providerInitial }}
                </div>
                <div class="min-w-0">
                  <p class="text-xs font-semibold uppercase tracking-[0.22em] text-orange-200">
                    Fornecedores
                  </p>
                  <h1 class="mt-1 text-2xl font-black tracking-tight text-white sm:text-3xl">
                    {{ isEditing ? 'Editar fornecedor' : 'Novo fornecedor' }}
                  </h1>
                  <p class="mt-1 max-w-2xl truncate text-sm text-slate-300">
                    {{ isEditing ? provider.displayName : 'Cadastre dados, serviços, agenda e presença visual em uma experiência única.' }}
                  </p>
                </div>
              </div>
            </div>

            <div class="grid grid-cols-3 gap-2 sm:min-w-[26rem]">
              <div class="rounded-2xl border border-white/10 bg-white/8 p-3 shadow-sm backdrop-blur">
                <p class="text-[11px] font-medium uppercase tracking-wide text-slate-400">Serviços</p>
                <p class="mt-1 text-xl font-black text-white">{{ servicesCount }}</p>
              </div>
              <div class="rounded-2xl border border-white/10 bg-white/8 p-3 shadow-sm backdrop-blur">
                <p class="text-[11px] font-medium uppercase tracking-wide text-slate-400">Dias ativos</p>
                <p class="mt-1 text-xl font-black text-white">{{ activeDaysCount }}</p>
              </div>
              <div class="rounded-2xl border border-white/10 bg-white/8 p-3 shadow-sm backdrop-blur">
                <p class="text-[11px] font-medium uppercase tracking-wide text-slate-400">Galeria</p>
                <p class="mt-1 text-xl font-black text-white">{{ galleryCount }}</p>
              </div>
            </div>
          </div>
        </div>
      </div>

      <div v-if="isEditing" class="sticky top-0 z-20 border-b border-slate-200/80 bg-white/85 px-5 backdrop-blur-xl dark:border-white/10 dark:bg-[#071b33]/90 sm:px-8">
        <div class="mx-auto flex max-w-6xl gap-2 overflow-x-auto py-3">
          <button
            v-for="tab in tabs"
            :key="tab.id"
            type="button"
            @click="activeTab = tab.id"
            class="group relative flex min-w-[9.5rem] items-center justify-between rounded-2xl border px-4 py-3 text-left transition duration-300 hover:-translate-y-0.5"
            :class="activeTab === tab.id
              ? 'border-orange-500 bg-orange-500 text-white shadow-lg shadow-orange-500/20'
              : 'border-slate-200 bg-white text-slate-600 hover:border-orange-300 hover:shadow-md dark:border-white/10 dark:bg-white/8 dark:text-slate-300 dark:hover:text-white'"
          >
            <span>
              <span class="block text-sm font-bold">{{ tab.label }}</span>
              <span class="mt-0.5 block text-xs opacity-60">{{ tab.hint }}</span>
            </span>
            <span
              v-if="tab.count !== null"
              class="ml-3 flex h-7 min-w-7 items-center justify-center rounded-full px-2 text-xs font-black transition"
              :class="activeTab === tab.id ? 'bg-white/18' : 'bg-slate-100 text-slate-500 dark:bg-white/10 dark:text-slate-300'"
            >
              {{ tab.count }}
            </span>
          </button>
        </div>
      </div>

      <main class="mx-auto max-w-6xl px-5 py-6 sm:px-8 lg:py-8">
        <Transition name="soft-slide" mode="out-in">
          <section v-if="activeTab === 'dados'" key="dados" class="grid gap-6 lg:grid-cols-[1fr_22rem]">
            <form @submit.prevent="submit" class="panel p-5 sm:p-7">
              <div class="mb-7 flex items-start justify-between gap-4">
                <div>
                  <p class="section-kicker">Perfil</p>
                  <h2 class="section-title">Dados principais</h2>
                </div>
                <span
                  class="rounded-full px-3 py-1 text-xs font-bold"
                  :class="form.status === 'active'
                    ? 'bg-orange-100 text-orange-700 dark:bg-orange-500/10 dark:text-orange-300'
                    : 'bg-slate-100 text-slate-500 dark:bg-slate-800 dark:text-slate-400'"
                >
                  {{ form.status === 'active' ? 'Ativo' : 'Inativo' }}
                </span>
              </div>

              <div class="space-y-5">
                <div>
                  <label class="field-label">Razão Social / Nome</label>
                  <input
                    v-model="form.displayName"
                    type="text"
                    placeholder="Ex: Empresa LTDA"
                    class="field-input"
                    :class="form.errors.displayName ? 'field-input-error' : ''"
                  />
                  <p v-if="form.errors.displayName" class="field-error">{{ form.errors.displayName }}</p>
                </div>

                <div class="grid gap-4 md:grid-cols-2">
                  <div>
                    <label class="field-label">E-mail</label>
                    <input
                      v-model="form.email"
                      type="email"
                      placeholder="contato@empresa.com"
                      class="field-input"
                      :class="form.errors.email ? 'field-input-error' : ''"
                    />
                    <p v-if="form.errors.email" class="field-error">{{ form.errors.email }}</p>
                  </div>
                  <div>
                    <label class="field-label">Telefone</label>
                    <input
                      v-model="form.phone"
                      type="text"
                      placeholder="(11) 99999-9999"
                      class="field-input"
                    />
                  </div>
                </div>

                <div>
                  <label class="field-label">CNPJ</label>
                  <input
                    v-model="form.cnpj"
                    type="text"
                    placeholder="00.000.000/0001-00"
                    class="field-input font-mono"
                  />
                </div>

                <div>
                  <label class="field-label">Status</label>
                  <div class="grid gap-3 sm:grid-cols-2">
                    <label
                      v-for="option in statusOptions"
                      :key="option.value"
                      class="group flex cursor-pointer items-center gap-3 rounded-2xl border p-4 transition duration-300 hover:-translate-y-0.5 hover:shadow-md"
                      :class="form.status === option.value
                        ? 'border-[#071b33] bg-[#071b33] text-white shadow-lg shadow-slate-950/10 dark:border-orange-500 dark:bg-orange-500'
                        : 'border-slate-200 bg-white text-slate-600 dark:border-slate-800 dark:bg-slate-900 dark:text-slate-400'"
                    >
                      <input type="radio" v-model="form.status" :value="option.value" class="hidden" />
                      <span
                        class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl"
                        :class="form.status === option.value
                          ? 'bg-white/15 dark:bg-slate-950/10'
                          : option.tone === 'emerald' ? 'bg-orange-100 text-orange-600 dark:bg-orange-500/10 dark:text-orange-300' : 'bg-slate-100 text-slate-500 dark:bg-slate-800'"
                      >
                        <span class="h-2.5 w-2.5 rounded-full" :class="option.tone === 'emerald' ? 'bg-orange-400' : 'bg-slate-400'"></span>
                      </span>
                      <span>
                        <span class="block text-sm font-bold">{{ option.label }}</span>
                        <span class="mt-0.5 block text-xs opacity-60">{{ option.description }}</span>
                      </span>
                    </label>
                  </div>
                </div>
              </div>

              <div class="mt-7 flex flex-col-reverse gap-3 border-t border-slate-100 pt-5 dark:border-slate-800 sm:flex-row sm:items-center sm:justify-end">
                <a :href="route('providers.index')" class="btn-ghost justify-center">
                  Cancelar
                </a>
                <button type="submit" :disabled="form.processing" class="btn-primary justify-center">
                  <svg v-if="form.processing" class="h-4 w-4 animate-spin" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"/>
                  </svg>
                  {{ isEditing ? 'Salvar alterações' : 'Cadastrar fornecedor' }}
                </button>
              </div>
            </form>

            <aside class="space-y-4">
              <div class="group relative min-h-[24rem] overflow-hidden rounded-[1.5rem] bg-slate-950 shadow-xl shadow-slate-950/10">
                <img :src="heroImage" alt="" class="absolute inset-0 h-full w-full object-cover opacity-80 transition duration-700 group-hover:scale-105" />
                <div class="absolute inset-0 bg-gradient-to-t from-slate-950 via-slate-950/35 to-transparent"></div>
                <div class="absolute bottom-0 p-6 text-white">
                  <p class="text-xs font-bold uppercase tracking-[0.22em] text-orange-200">Experiência</p>
                  <h3 class="mt-2 text-2xl font-black leading-tight">Mostre o fornecedor como ele será vivido pelo cliente.</h3>
                  <p class="mt-3 text-sm leading-6 text-white/75">Dados claros, fotos boas e horários confiáveis reduzem atrito na reserva.</p>
                </div>
              </div>
            </aside>
          </section>

          <section v-else-if="activeTab === 'servicos'" key="servicos" class="space-y-5">
            <div class="panel p-5 sm:p-6">
              <div class="mb-5 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                <div>
                  <p class="section-kicker">Catálogo</p>
                  <h2 class="section-title">{{ editingService ? 'Editar serviço' : 'Adicionar serviço' }}</h2>
                </div>
                <button type="button" @click="openAddService" class="btn-ghost">
                  Limpar
                </button>
              </div>

              <div class="grid gap-4 lg:grid-cols-[1fr_12rem_12rem_auto] lg:items-end">
                <div>
                  <label class="field-label">Nome</label>
                  <input v-model="serviceForm.name" type="text" placeholder="Ex: Barba" class="field-input" />
                  <p v-if="serviceForm.errors.name" class="field-error">{{ serviceForm.errors.name }}</p>
                </div>
                <div>
                  <label class="field-label">Duração (min)</label>
                  <input v-model.number="serviceForm.durationMin" type="number" min="5" step="5" class="field-input" />
                  <p v-if="serviceForm.errors.durationMin" class="field-error">{{ serviceForm.errors.durationMin }}</p>
                </div>
                <div>
                  <label class="field-label">Preço (R$)</label>
                  <input v-model.number="serviceForm.priceType" type="number" min="0" step="0.50" class="field-input" />
                  <p v-if="serviceForm.errors.priceType" class="field-error">{{ serviceForm.errors.priceType }}</p>
                </div>
                <button @click="saveService" :disabled="serviceForm.processing" type="button" class="btn-primary justify-center">
                  {{ editingService ? 'Salvar' : 'Adicionar' }}
                </button>
              </div>

              <button
                v-if="editingService"
                @click="editingService = null; serviceForm.reset()"
                type="button"
                class="mt-3 text-sm font-semibold text-slate-500 transition hover:text-slate-900 dark:text-slate-400 dark:hover:text-white"
              >
                Cancelar edição
              </button>
            </div>

            <div class="grid gap-4 md:grid-cols-2 xl:grid-cols-3">
              <div v-if="!services?.length" class="panel col-span-full flex min-h-48 items-center justify-center p-8 text-center">
                <div>
                  <div class="mx-auto mb-4 flex h-12 w-12 items-center justify-center rounded-2xl bg-slate-100 text-slate-400 dark:bg-slate-800">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M12 6v12m6-6H6"/>
                    </svg>
                  </div>
                  <p class="font-bold text-slate-700 dark:text-slate-200">Nenhum serviço cadastrado</p>
                  <p class="mt-1 text-sm text-slate-400">Adicione o primeiro item do catálogo.</p>
                </div>
              </div>

              <article
                v-for="s in services"
                :key="s.id"
                class="group rounded-[1.25rem] border border-slate-200 bg-white p-5 shadow-sm transition duration-300 hover:-translate-y-1 hover:border-slate-300 hover:shadow-xl hover:shadow-slate-950/10 dark:border-slate-800 dark:bg-slate-900 dark:hover:border-slate-700"
              >
                <div class="flex items-start justify-between gap-4">
                  <div class="flex h-11 w-11 items-center justify-center rounded-2xl bg-orange-100 text-orange-700 dark:bg-orange-500/10 dark:text-orange-300">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M9 12h6m-7 4h8M7 4h10a2 2 0 012 2v12a2 2 0 01-2 2H7a2 2 0 01-2-2V6a2 2 0 012-2z"/>
                    </svg>
                  </div>
                  <div class="flex items-center gap-1 opacity-80 transition group-hover:opacity-100">
                    <button @click="openEditService(s)" class="icon-action text-slate-400 hover:bg-orange-50 hover:text-orange-700 dark:hover:bg-orange-500/10 dark:hover:text-orange-300" aria-label="Editar serviço">
                      <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M16.862 4.487l1.651-1.65a1.875 1.875 0 112.652 2.652L9.75 16.902 6 18l1.098-3.75L16.862 4.487z"/>
                      </svg>
                    </button>
                    <button @click="removeService(s.id)" class="icon-action text-slate-400 hover:bg-rose-50 hover:text-rose-600 dark:hover:bg-rose-500/10 dark:hover:text-rose-300" aria-label="Remover serviço">
                      <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M6 7h12m-9 0V5a1 1 0 011-1h4a1 1 0 011 1v2m2 0l-.7 12A2 2 0 0115.3 21H8.7a2 2 0 01-1.99-2L6 7z"/>
                      </svg>
                    </button>
                  </div>
                </div>
                <h3 class="mt-5 text-lg font-black text-slate-950 dark:text-white">{{ s.name }}</h3>
                <div class="mt-5 grid grid-cols-2 gap-3">
                  <div class="rounded-2xl bg-slate-50 p-3 dark:bg-slate-800/70">
                    <p class="text-xs text-slate-400">Duração</p>
                    <p class="mt-1 font-black text-slate-800 dark:text-white">{{ s.durationMin }} min</p>
                  </div>
                  <div class="rounded-2xl bg-slate-50 p-3 dark:bg-slate-800/70">
                    <p class="text-xs text-slate-400">Preço</p>
                    <p class="mt-1 font-black text-slate-800 dark:text-white">R$ {{ Number(s.priceType).toFixed(2) }}</p>
                  </div>
                </div>
              </article>
            </div>
          </section>

          <section v-else-if="activeTab === 'horarios'" key="horarios">
            <form @submit.prevent="saveSchedule" class="space-y-3">
              <div
                v-for="day in scheduleForm.days"
                :key="day.dayId"
                class="group overflow-hidden rounded-[1.25rem] border bg-white shadow-sm transition duration-300 hover:-translate-y-0.5 hover:shadow-lg dark:bg-slate-900"
                :class="day.isActive ? 'border-orange-200 dark:border-orange-500/30' : 'border-slate-200 dark:border-slate-800'"
              >
                <div class="flex flex-col gap-4 p-5 lg:flex-row lg:items-center lg:justify-between">
                  <div class="flex items-center gap-3">
                    <button
                      type="button"
                      @click="day.isActive = !day.isActive"
                      class="relative h-7 w-12 shrink-0 rounded-full transition-colors duration-300 focus:outline-none focus:ring-4 focus:ring-orange-200 dark:focus:ring-orange-500/20"
                      :class="day.isActive ? 'bg-orange-500' : 'bg-slate-200 dark:bg-slate-700'"
                    >
                      <span
                        class="absolute left-1 top-1 h-5 w-5 rounded-full bg-white shadow transition-transform duration-300"
                        :class="day.isActive ? 'translate-x-5' : 'translate-x-0'"
                      ></span>
                    </button>
                    <div>
                      <p class="text-sm font-black text-slate-950 dark:text-white">{{ day.dayName }}</p>
                      <p class="mt-0.5 text-xs text-slate-400">{{ day.isActive ? 'Aberto para agendamentos' : 'Fechado neste dia' }}</p>
                    </div>
                  </div>

                  <div v-if="day.isActive" class="grid gap-3 sm:grid-cols-2 lg:flex lg:items-center">
                    <label class="compact-field">
                      <span>Início</span>
                      <input v-model="day.startTime" type="time" class="time-input" />
                    </label>
                    <label class="compact-field">
                      <span>Fim</span>
                      <input v-model="day.endTime" type="time" class="time-input" />
                    </label>
                    <button
                      type="button"
                      @click="day.hasLunchBreak = !day.hasLunchBreak"
                      class="flex items-center justify-center gap-2 rounded-xl border px-3 py-2 text-xs font-bold transition duration-300"
                      :class="day.hasLunchBreak
                        ? 'border-amber-300 bg-amber-100 text-amber-800 dark:border-amber-500/30 dark:bg-amber-500/10 dark:text-amber-300'
                        : 'border-slate-200 bg-white text-slate-500 hover:border-slate-300 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-400'"
                    >
                      <span class="h-2 w-2 rounded-full" :class="day.hasLunchBreak ? 'bg-amber-400' : 'bg-slate-300'"></span>
                      Almoço
                    </button>
                  </div>
                </div>

                <Transition name="expand">
                  <div
                    v-if="day.isActive && day.hasLunchBreak"
                    class="grid gap-3 border-t border-amber-100 bg-amber-50/70 p-4 dark:border-amber-500/10 dark:bg-amber-500/5 sm:grid-cols-[auto_1fr_1fr] sm:items-center"
                  >
                    <span class="text-xs font-black uppercase tracking-wide text-amber-700 dark:text-amber-300">Intervalo</span>
                    <label class="compact-field">
                      <span>De</span>
                      <input v-model="day.lunchStart" type="time" class="time-input border-amber-200 dark:border-amber-500/30" />
                    </label>
                    <label class="compact-field">
                      <span>Até</span>
                      <input v-model="day.lunchEnd" type="time" class="time-input border-amber-200 dark:border-amber-500/30" />
                    </label>
                  </div>
                </Transition>
              </div>

              <div class="flex justify-end pt-3">
                <button type="submit" :disabled="scheduleForm.processing" class="btn-primary">
                  <svg v-if="scheduleForm.processing" class="h-4 w-4 animate-spin" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"/>
                  </svg>
                  Salvar horários
                </button>
              </div>
            </form>
          </section>

          <section v-else key="galeria" class="space-y-5">
            <div class="grid gap-5 lg:grid-cols-[1fr_20rem]">
              <div class="panel overflow-hidden p-0">
                <div class="relative min-h-[24rem] overflow-hidden">
                  <img :src="galleryItems[0].src" alt="" class="absolute inset-0 h-full w-full object-cover" />
                  <div class="absolute inset-0 bg-gradient-to-t from-slate-950 via-slate-950/35 to-transparent"></div>
                  <div class="absolute bottom-0 max-w-xl p-6 text-white">
                    <p class="text-xs font-black uppercase tracking-[0.24em] text-orange-200">Galeria</p>
                    <h2 class="mt-2 text-3xl font-black tracking-tight">Fotos que vendem confiança antes do clique.</h2>
                    <p class="mt-3 text-sm leading-6 text-white/75">Use imagens reais do espaço, detalhes do atendimento e resultados para orientar a decisão do cliente.</p>
                  </div>
                </div>
              </div>

              <label class="group flex cursor-pointer flex-col items-center justify-center rounded-[1.25rem] border border-dashed border-slate-300 bg-white p-6 text-center shadow-sm transition duration-300 hover:-translate-y-1 hover:border-orange-400 hover:bg-orange-50/60 hover:shadow-xl hover:shadow-orange-950/10 dark:border-slate-700 dark:bg-slate-900 dark:hover:border-orange-500/60 dark:hover:bg-orange-500/5">
                <input type="file" multiple accept="image/*" class="hidden" @change="addGalleryImages" />
                <span class="flex h-14 w-14 items-center justify-center rounded-2xl bg-orange-100 text-orange-700 transition group-hover:scale-105 dark:bg-orange-500/10 dark:text-orange-300">
                  <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M4 16l4.6-4.6a2 2 0 012.8 0L16 16m-2-2l1.6-1.6a2 2 0 012.8 0L20 14m-8-7h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                  </svg>
                </span>
                <h3 class="mt-4 text-base font-black text-slate-950 dark:text-white">Adicionar imagens</h3>
                <p class="mt-2 text-sm leading-6 text-slate-500 dark:text-slate-400">Pré-visualize fotos nesta tela. A persistência pode ser conectada ao backend depois.</p>
              </label>
            </div>

            <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
              <article
                v-for="item in galleryItems"
                :key="item.id"
                class="gallery-card group"
              >
                <img :src="item.src" :alt="item.title" class="h-full w-full object-cover transition duration-700 group-hover:scale-110" />
                <div class="absolute inset-0 bg-gradient-to-t from-slate-950/80 via-transparent to-transparent opacity-80"></div>
                <div class="absolute bottom-0 p-4 text-white">
                  <span class="rounded-full bg-white/15 px-2.5 py-1 text-[11px] font-bold backdrop-blur">{{ item.tag }}</span>
                  <h3 class="mt-2 text-base font-black">{{ item.title }}</h3>
                </div>
              </article>

              <article
                v-for="item in galleryUploads"
                :key="item.id"
                class="gallery-card group"
              >
                <img :src="item.src" :alt="item.title" class="h-full w-full object-cover transition duration-700 group-hover:scale-110" />
                <div class="absolute inset-0 bg-gradient-to-t from-slate-950/80 via-transparent to-transparent opacity-80"></div>
                <button
                  type="button"
                  @click="removeGalleryUpload(item.id)"
                  class="absolute right-3 top-3 flex h-8 w-8 items-center justify-center rounded-full bg-white/90 text-slate-700 shadow-lg transition hover:bg-rose-500 hover:text-white"
                  aria-label="Remover imagem"
                >
                  <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M6 18L18 6M6 6l12 12"/>
                  </svg>
                </button>
                <div class="absolute bottom-0 p-4 text-white">
                  <span class="rounded-full bg-orange-400/25 px-2.5 py-1 text-[11px] font-bold backdrop-blur">{{ item.tag }}</span>
                  <h3 class="mt-2 text-base font-black">{{ item.title }}</h3>
                </div>
              </article>
            </div>
          </section>
        </Transition>
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
  font-weight: 800;
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
  transition: border-color 180ms ease, box-shadow 180ms ease, background-color 180ms ease, transform 180ms ease;
}

.field-input:focus {
  border-color: rgb(251 146 60);
  background: white;
  box-shadow: 0 0 0 4px rgb(251 146 60 / 0.18);
}

:global(.dark) .field-input {
  border-color: rgb(51 65 85);
  background: rgb(15 23 42);
  color: white;
}

:global(.dark) .field-input:focus {
  border-color: rgb(251 146 60);
  background: rgb(15 23 42);
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

.icon-action {
  display: flex;
  height: 2rem;
  width: 2rem;
  align-items: center;
  justify-content: center;
  border-radius: 0.75rem;
  transition: background-color 180ms ease, color 180ms ease, transform 180ms ease;
}

.icon-action:hover {
  transform: translateY(-1px);
}

.compact-field {
  display: flex;
  align-items: center;
  justify-content: space-between;
  gap: 0.75rem;
  border-radius: 0.9rem;
  background: rgb(248 250 252);
  padding: 0.45rem 0.6rem 0.45rem 0.8rem;
  font-size: 0.75rem;
  font-weight: 800;
  color: rgb(100 116 139);
}

:global(.dark) .compact-field {
  background: rgb(30 41 59 / 0.7);
  color: rgb(148 163 184);
}

.time-input {
  width: 7.5rem;
  border-radius: 0.7rem;
  border: 1px solid rgb(226 232 240);
  background: white;
  padding: 0.45rem 0.55rem;
  font-size: 0.78rem;
  color: rgb(15 23 42);
  outline: none;
  transition: border-color 180ms ease, box-shadow 180ms ease;
}

.time-input:focus {
  border-color: rgb(251 146 60);
  box-shadow: 0 0 0 3px rgb(251 146 60 / 0.18);
}

:global(.dark) .time-input {
  border-color: rgb(51 65 85);
  background: rgb(15 23 42);
  color: white;
}

.gallery-card {
  position: relative;
  aspect-ratio: 4 / 3;
  overflow: hidden;
  border-radius: 1.25rem;
  background: rgb(15 23 42);
  box-shadow: 0 1px 2px rgb(15 23 42 / 0.06);
  transition: transform 300ms ease, box-shadow 300ms ease;
}

.gallery-card:hover {
  transform: translateY(-4px);
  box-shadow: 0 22px 35px rgb(15 23 42 / 0.14);
}

.soft-slide-enter-active,
.soft-slide-leave-active {
  transition: opacity 220ms ease, transform 220ms ease;
}

.soft-slide-enter-from {
  opacity: 0;
  transform: translateY(10px);
}

.soft-slide-leave-to {
  opacity: 0;
  transform: translateY(-8px);
}

.expand-enter-active,
.expand-leave-active {
  transition: opacity 180ms ease, transform 180ms ease;
}

.expand-enter-from,
.expand-leave-to {
  opacity: 0;
  transform: translateY(-6px);
}
</style>
