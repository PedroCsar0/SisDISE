<template>
  <div>
    <header class="bg-white shadow-md">
      <nav class="container mx-auto max-w-5xl px-4 py-3 flex justify-between items-center">
        <div>
          <img class="h-10 w-auto" src="/src/assets/logo.svg" alt="SisDISE Logo" />
        </div>
        <button @click="router.push(`/relatorio/${id}`)" class="text-sm font-medium text-gray-600 hover:text-indigo-600">
          Voltar ao Relatório
        </button>
      </nav>
    </header>

    <main class="container mx-auto max-w-5xl p-4 mt-6">

      <div v-if="isLoading" class="text-center">
        <p class="text-lg text-gray-600">Gerando plano de ação...</p>
      </div>

      <div v-if="itensParaMelhorar">
        <h1 class="text-3xl font-bold text-gray-900 mb-2">
          Plano de Gestão Empresarial Sustentável (PGES)
        </h1>
        <p class="text-lg text-gray-600 mb-6">
          Relatório de referência: #{{ id }}
        </p>

        <p class="mb-4 text-gray-700">
          Com base no diagnóstico, os seguintes parâmetros foram identificados como críticos (nota 0, 1 ou 2) e requerem um plano de ação para melhoria:
        </p>

        <div class="bg-white p-6 rounded-lg shadow-md">
          <ul class="space-y-4">
            <li
              v-for="item in itensParaMelhorar"
              :key="item.id"
              class="p-4 border border-red-300 bg-red-50 rounded-md"
            >
              <p class="font-semibold text-red-800">
                Parâmetro: {{ item.item_parametro.codigo_item }}
              </p>
              <p class="text-gray-700 mt-1">
                {{ item.item_parametro.descricao }}
              </p>
              <p class="text-red-700 font-bold mt-2">
                Nota Recebida: {{ item.nota }}
              </p>
            </li>
          </ul>

          <p v-if="itensParaMelhorar.length === 0" class="text-green-700 font-semibold">
            Parabéns! Nenhum item foi classificado com nota baixa (≤ 2).
          </p>
        </div>
      </div>
    </main>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import { useRouter } from 'vue-router';
import api from '@/api.js';

// 1. Recebe o 'id' da rota como uma prop
const props = defineProps({
  id: {
    type: String,
    required: true,
  },
});

const router = useRouter();
const isLoading = ref(true);
const itensParaMelhorar = ref(null);

// 2. Função para buscar o PGES da API
const fetchPGES = async () => {
  try {
    isLoading.value = true;
    // Chama a nova rota 'GET /api/diagnosticos/{id}/pges'
    const response = await api.get(`/diagnosticos/${props.id}/pges`);
    itensParaMelhorar.value = response.data;
  } catch (error) {
    console.error('Erro ao gerar PGES:', error);
    if (error.response && error.response.status === 401) {
      router.push('/login');
    }
  } finally {
    isLoading.value = false;
  }
};

// 3. Hook de Carregamento
onMounted(() => {
  fetchPGES();
});
</script>
