<template>
  <div>
    <header class="bg-white shadow-md">
      <nav class="container mx-auto max-w-5xl px-4 py-3 flex justify-between items-center">
        <div>
          <img class="h-10 w-auto" src="/src/assets/logo.svg" alt="SisDISE Logo" />
        </div>
        <button @click="router.push('/dashboard')" class="text-sm font-medium text-gray-600 hover:text-indigo-600">
          Voltar ao Dashboard
        </button>
      </nav>
    </header>

    <main class="container mx-auto max-w-5xl p-4 mt-6">

      <div v-if="isLoading" class="text-center">
        <p class="text-lg text-gray-600">Carregando relatório...</p>
      </div>

      <div v-if="diagnostico">

        <h1 class="text-3xl font-bold text-gray-900 mb-2">
          Relatório de diagnóstico, [NOME_DA_EMPRESA]
        </h1>
        <p class="text-lg text-gray-600 mb-6">
          Data: {{ formatarData(diagnostico.dataAnalise) }}
        </p>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

          <div class="bg-white p-6 rounded-lg shadow-md flex flex-col items-center">
            <h2 class="text-xl font-semibold text-gray-800">Score Final</h2>

            <div class="relative w-48 h-48 my-4">
              <svg class="w-full h-full" viewBox="0 0 36 36">
                <path class="text-gray-200"
                  d="M18 2.0845
                    a 15.9155 15.9155 0 0 1 0 31.831
                    a 15.9155 15.9155 0 0 1 0 -31.831"
                  fill="none" stroke-width="3" stroke-dasharray="75, 25" stroke-linecap="round" />
                <path class="text-yellow-500"
                  :stroke-dasharray="`${(diagnostico.escoreFinal / 1000) * 75}, 100`"
                  d="M18 2.0845
                    a 15.9155 15.9155 0 0 1 0 31.831
                    a 15.9155 15.9155 0 0 1 0 -31.831"
                  fill="none" stroke-width="3" stroke-linecap="round" />
              </svg>
              <div class="absolute inset-0 flex flex-col items-center justify-center">
                <span class="text-3xl font-bold text-gray-900">{{ diagnostico.escoreFinal }}</span>
                <span class="text-sm text-gray-500">/ 1000</span>
              </div>
            </div>

            <p class="text-lg font-medium text-yellow-600">
              {{ diagnostico.classificacao }}
            </p>
            <button class="mt-4 px-4 py-2 text-sm font-medium text-white bg-indigo-600 rounded-md hover:bg-indigo-700">
              Visão detalhada
            </button>
            <button
            @click="handleDownloadPDF"
            :disabled="isDownloading"
            class="px-4 py-2 text-sm font-medium text-white bg-green-600 rounded-md hover:bg-green-700 disabled:bg-gray-400"
            >
            {{ isDownloading ? 'Gerando...' : 'Baixar PDF' }}
            </button>
            <button
              @click="goToPGES"
              class="px-4 py-2 text-sm font-medium text-white bg-blue-600 rounded-md hover:bg-blue-700"
            >
              Gerar Plano de Ação (PGES)
            </button>
          </div>

          <div class="bg-white p-6 rounded-lg shadow-md">
            <h2 class="text-xl font-semibold text-gray-800 mb-6">Performance</h2>

            <ul class="space-y-4">
              <li v-for="principio in principiosComPercentual" :key="principio.id">
                <div class="flex justify-between items-center mb-1">
                  <span class="flex items-center">
                    <span :class="`w-3 h-3 rounded-full mr-2 ${principio.cor}`"></span>
                    <span class="text-gray-700">{{ principio.nomePrincipio }}</span>
                  </span>
                  <span class="font-medium" :class="`text-${principio.cor.split('-')[1]}-600`">
                    {{ principio.escoreObtido.toFixed(0) }} / {{ principio.maxScore }} ({{ principio.percentual.toFixed(0) }}%)
                  </span>
                </div>
                <div class="w-full bg-gray-200 rounded-full h-2">
                  <div :class="`h-2 rounded-full ${principio.cor}`" :style="{ width: principio.percentual + '%' }"></div>
                </div>
              </li>
            </ul>
          </div>

        </div>
      </div>
    </main>
  </div>
</template>

<script setup>
import { ref, onMounted, computed } from 'vue';
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
const diagnostico = ref(null);
const isDownloading = ref(false);

// 2. Mapeamento de Cores e Scores Máximos (baseado na Tabela 7 [cite: 192])
const principioMetadados = {
  'Direitos humanos e trabalho': { cor: 'bg-green-500', maxScore: 300 },
  'Meio ambiente': { cor: 'bg-blue-500', maxScore: 500 },
  'Anticorrupção': { cor: 'bg-red-500', maxScore: 200 },
};

// 3. Função para buscar o relatório da API
const fetchRelatorio = async () => {
  try {
    isLoading.value = true;
    // Chama a nova rota 'GET /api/diagnosticos/{id}'
    const response = await api.get(`/diagnosticos/${props.id}`);
    diagnostico.value = response.data;
  } catch (error) {
    console.error('Erro ao buscar relatório:', error);
    if (error.response && error.response.status === 401) {
      router.push('/login');
    }
  } finally {
    isLoading.value = false;
  }
};

// 4. Propriedade Computada
// Calcula as porcentagens para a Figura 5 dinamicamente
const principiosComPercentual = computed(() => {
  if (!diagnostico.value || !diagnostico.value.principios) return [];

  return diagnostico.value.principios.map(p => {
    const meta = principioMetadados[p.nomePrincipio] || { cor: 'bg-gray-500', maxScore: 1 };
    return {
      ...p,
      cor: meta.cor,
      maxScore: meta.maxScore,
      percentual: (p.escoreObtido / meta.maxScore) * 100,
    };
  });
});

// 5. Função de formatação de data
const formatarData = (dataISO) => {
  const data = new Date(dataISO);
  return data.toLocaleDateString('pt-BR', {
    day: '2-digit',
    month: '2-digit',
    year: 'numeric',
  });
};

// 6. Hook de Carregamento
onMounted(() => {
  fetchRelatorio();
});

const handleDownloadPDF = async () => {
  isDownloading.value = true;
  try {
    // 1. Chame a API, esperando um 'blob' (dados binários)
    const response = await api.get(`/relatorio/${props.id}/pdf`, {
      responseType: 'blob', // <-- MUITO IMPORTANTE
    });

    // 2. Crie um URL temporário para o blob (os dados do PDF)
    const url = window.URL.createObjectURL(new Blob([response.data], { type: 'application/pdf' }));

    // 3. Crie um link <a> invisível
    const link = document.createElement('a');
    link.href = url;
    const fileName = `SisDISE_Relatorio_${props.id}.pdf`;
    link.setAttribute('download', fileName);

    // 4. Adicione, clique e remova o link (simula o download)
    document.body.appendChild(link);
    link.click();
    document.body.removeChild(link);
    window.URL.revokeObjectURL(url); // Libera a memória

  } catch (error) {
    console.error('Erro ao baixar PDF:', error);
    // (Adicionaríamos um feedback de erro para o usuário aqui)
  } finally {
    isDownloading.value = false;
  }

};

const goToPGES = () => {
  router.push(`/relatorio/${props.id}/pges`);
};
</script>
