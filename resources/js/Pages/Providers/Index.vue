<script setup>
import { ref, computed } from 'vue'
import { router, usePage } from '@inertiajs/vue3'
import AppLayout from '@/Layouts/AppLayout.vue'

const props = defineProps({
  providers: Array,
})

const search = ref('')
const page = usePage()
const flash = computed(() => page.props.flash ?? {})

const filtered = computed(() =>
  props.providers.filter(p =>
    p.displayName?.toLowerCase().includes(search.value.toLowerCase()) ||
    p.email?.toLowerCase().includes(search.value.toLowerCase()) ||
    p.cnpj?.includes(search.value)
  )
)

function destroy(id) {
  if (confirm('Remover este fornecedor?')) {
    router.delete(route('providers.destroy', id))
  }
}
</script>

<template>
  <AppLayout title="Fornecedores">
    <!-- Page Header -->
    <div class="bg-white dark:bg-gray-900 border-b border-gray-100 dark:border-gray-800 px-8 py-6">
      <div class="max-w-6xl mx-auto flex items-center justify-between">
        <div>
          <h1 class="text-xl font-bold text-gray-900 dark:text-white">Fornecedores</h1>
          <p class="text-sm text-gray-500 dark:text-gray-400 mt-0.5">Gerencie os fornecedores cadastrados no Firestore</p>
        </div>
        <a
          :href="route('providers.create')"
          class="inline-flex items-center gap-2 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium px-4 py-2.5 rounded-lg transition"
        >
          <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
          </svg>
          Novo Fornecedor
        </a>
      </div>
    </div>

    <div class="max-w-6xl mx-auto px-8 py-8 space-y-5">

      <!-- Flash -->
      <Transition enter-active-class="transition duration-300" enter-from-class="opacity-0 -translate-y-2" enter-to-class="opacity-100 translate-y-0">
        <div v-if="flash.success" class="flex items-center gap-3 bg-green-50 dark:bg-green-500/10 border border-green-200 dark:border-green-500/20 text-green-800 dark:text-green-400 px-4 py-3 rounded-lg text-sm">
          <svg class="w-4 h-4 shrink-0" fill="currentColor" viewBox="0 0 20 20">
            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
          </svg>
          {{ flash.success }}
        </div>
      </Transition>

      <!-- Search + Stats -->
      <div class="flex items-center justify-between gap-4">
        <div class="relative flex-1 max-w-sm">
          <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
          </svg>
          <input
            v-model="search"
            type="text"
            placeholder="Buscar por nome, e-mail ou CNPJ..."
            class="w-full pl-10 pr-4 py-2.5 border border-gray-200 dark:border-gray-700 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-indigo-300 dark:focus:ring-indigo-500/40 bg-white dark:bg-gray-800 text-gray-900 dark:text-white placeholder-gray-400 dark:placeholder-gray-500 transition"
          />
        </div>
        <span class="text-sm text-gray-400 dark:text-gray-500 shrink-0">{{ filtered.length }} fornecedor(es)</span>
      </div>

      <!-- Table -->
      <div class="bg-white dark:bg-gray-900 rounded-xl border border-gray-200 dark:border-gray-800 overflow-hidden shadow-sm">
        <table class="w-full text-sm">
          <thead>
            <tr class="border-b border-gray-100 dark:border-gray-800 bg-gray-50 dark:bg-gray-800/50">
              <th class="text-left px-6 py-3 font-medium text-gray-500 dark:text-gray-400 text-xs uppercase tracking-wide">Nome</th>
              <th class="text-left px-6 py-3 font-medium text-gray-500 dark:text-gray-400 text-xs uppercase tracking-wide">E-mail</th>
              <th class="text-left px-6 py-3 font-medium text-gray-500 dark:text-gray-400 text-xs uppercase tracking-wide">CNPJ</th>
              <th class="text-left px-6 py-3 font-medium text-gray-500 dark:text-gray-400 text-xs uppercase tracking-wide">Telefone</th>
              <th class="text-left px-6 py-3 font-medium text-gray-500 dark:text-gray-400 text-xs uppercase tracking-wide">Status</th>
              <th class="px-6 py-3"></th>
            </tr>
          </thead>
          <tbody class="divide-y divide-gray-50 dark:divide-gray-800">
            <tr v-if="filtered.length === 0">
              <td colspan="6" class="text-center py-20 text-gray-400 dark:text-gray-600">
                <svg class="w-10 h-10 mx-auto mb-3 text-gray-200 dark:text-gray-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                </svg>
                <p class="text-sm">Nenhum fornecedor encontrado</p>
                <a :href="route('providers.create')" class="text-indigo-500 hover:text-indigo-600 text-xs mt-1 inline-block">Cadastrar primeiro fornecedor →</a>
              </td>
            </tr>
            <tr
              v-for="provider in filtered"
              :key="provider.id"
              class="hover:bg-gray-50 dark:hover:bg-gray-800/50 transition-colors"
            >
              <td class="px-6 py-4">
                <div class="flex items-center gap-3">
                  <div class="w-8 h-8 rounded-full bg-indigo-100 dark:bg-indigo-500/10 text-indigo-600 dark:text-indigo-400 flex items-center justify-center font-semibold text-xs uppercase shrink-0">
                    {{ provider.displayName?.[0] ?? '?' }}
                  </div>
                  <span class="font-medium text-gray-900 dark:text-white">{{ provider.displayName  }}</span>
                </div>
              </td>
              <td class="px-6 py-4 text-gray-500 dark:text-gray-400">{{ provider.email }}</td>
              <td class="px-6 py-4 text-gray-400 dark:text-gray-500 font-mono text-xs">{{ provider.cnpj ?? '—' }}</td>
              <td class="px-6 py-4 text-gray-500 dark:text-gray-400">{{ provider.phone ?? '—' }}</td>
              <td class="px-6 py-4">
                <span
                  class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-xs font-medium"
                  :class="provider.status === 'active'
                    ? 'bg-green-100 dark:bg-green-500/10 text-green-700 dark:text-green-400'
                    : 'bg-gray-100 dark:bg-gray-700 text-gray-500 dark:text-gray-400'"
                >
                  <span class="w-1.5 h-1.5 rounded-full" :class="provider.status === 'active' ? 'bg-green-500' : 'bg-gray-400'"></span>
                  {{ provider.status === 'active' ? 'Ativo' : 'Inativo' }}
                </span>
              </td>
              <td class="px-6 py-4">
                <div class="flex items-center justify-end gap-1">
                  <a
                    :href="route('providers.edit', provider.id)"
                    class="text-gray-400 hover:text-indigo-600 dark:hover:text-indigo-400 text-xs font-medium px-3 py-1.5 rounded-md hover:bg-indigo-50 dark:hover:bg-indigo-500/10 transition"
                  >Editar</a>
                  <button
                    @click="destroy(provider.id)"
                    class="text-gray-400 hover:text-red-600 dark:hover:text-red-400 text-xs font-medium px-3 py-1.5 rounded-md hover:bg-red-50 dark:hover:bg-red-500/10 transition"
                  >Remover</button>
                </div>
              </td>
            </tr>
          </tbody>
        </table>
      </div>

    </div>
  </AppLayout>
</template>
