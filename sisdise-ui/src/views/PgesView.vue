<template>
  <div>
    <div v-if="isLoading" class="text-center">
      <p class="text-lg text-gray-600">Gerando plano de ação...</p>
    </div>

    <div v-if="itensParaMelhorar">

      <div class="flex flex-col sm:flex-row justify-between sm:items-center gap-4 mb-6">
        <h1 class="text-3xl font-bold text-gray-900">
          Plano de Gestão Empresarial Sustentável (PGES)
        </h1>

        <button
          @click="handleDownloadPGES"
          :disabled="isDownloading"
          class="px-4 py-2 text-sm font-medium text-white bg-green-600 rounded-md hover:bg-green-700 disabled:bg-gray-400 w-full sm:w-auto"
        >
          {{ isDownloading ? 'Gerando...' : 'Baixar PDF do Plano' }}
        </button>
      </div>

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
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import { useRouter } from 'vue-router';
import api from '@/api.js';
import { useUserStore } from '@/stores/userStore'; // Importar o store

const props = defineProps({
  id: {
    type: String,
    required: true,
  },
});

const router = useRouter();
const userStore = useUserStore(); // Usar o store
const isLoading = ref(true);
const itensParaMelhorar = ref(null);
const isDownloading = ref(false);

// Função para buscar o PGES da API
const fetchPGES = async () => {
  try {
    isLoading.value = true;

    // Garantir que o utilizador está carregado (para o 'Gate' funcionar no recarregamento)
    if (!userStore.user) {
      await userStore.fetchUser();
    }

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

// Função de Download do PDF
const handleDownloadPGES = async () => {
  isDownloading.value = true;
  try {
    const response = await api.get(`/diagnosticos/${props.id}/pges/pdf`, {
      responseType: 'blob',
    });
    const url = window.URL.createObjectURL(new Blob([response.data], { type: 'application/pdf' }));
    const link = document.createElement('a');
    link.href = url;
    const fileName = `SisDISE_PlanoDeAcao_${props.id}.pdf`;
    link.setAttribute('download', fileName);
    document.body.appendChild(link);
    link.click();
    document.body.removeChild(link);
    window.URL.revokeObjectURL(url);
  } catch (error) {
    console.error('Erro ao baixar PDF do PGES:', error);
  } finally {
    isDownloading.value = false;
  }
};

// Hook de Carregamento
onMounted(() => {
  fetchPGES();
});
</script>
