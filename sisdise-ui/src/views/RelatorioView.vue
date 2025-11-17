<template>
  <div>
    <div v-if="isLoading" class="text-center">
      <p class="text-lg text-gray-600">Carregando relatório...</p>
    </div>

    <div v-if="diagnostico">

      <div class="flex flex-col sm:flex-row justify-between sm:items-center gap-4 mb-6">
        <div>
          <h1 class="text-3xl font-bold text-gray-900">
            {{ diagnostico.titulo }}
          </h1>
          <p class="text-lg text-gray-600">
            Empresa: {{ diagnostico.empresa ? diagnostico.empresa.razaoSocial : '[Empresa não encontrada]' }}
          </p>
          <p class="text-sm text-gray-500">
            Data: {{ formatarData(diagnostico.dataAnalise) }} | Relatório ID: #{{ diagnostico.id }}
          </p>
        </div>
        <button
          @click="handleDownloadPDF"
          :disabled="isDownloading"
          class="px-4 py-2 text-sm font-medium text-white bg-green-600 rounded-md hover:bg-green-700 disabled:bg-gray-400 w-full sm:w-auto"
        >
          {{ isDownloading ? 'Gerando...' : 'Baixar Relatório PDF' }}
        </button>
      </div>

      <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

        <div class="bg-white p-6 rounded-lg shadow-md flex flex-col items-center">
          <h2 class="text-xl font-semibold text-gray-800">Score Final (Sf)</h2>

          <div class="relative w-48 h-48 my-4">
            <svg class="w-full h-full" viewBox="0 0 36 36">
              <path class="text-gray-200"
                d="M18 2.0845 a 15.9155 15.9155 0 0 1 0 31.831 a 15.9155 15.9155 0 0 1 0 -31.831"
                fill="none" stroke-width="3" stroke-dasharray="75, 25" stroke-linecap="round" />
              <path class="text-yellow-500"
                :stroke-dasharray="`${(diagnostico.escoreFinal / 1000) * 75}, 100`"
                d="M18 2.0845 a 15.9155 15.9155 0 0 1 0 31.831 a 15.9155 15.9155 0 0 1 0 -31.831"
                fill="none" stroke-width="3" stroke-linecap="round" />
            </svg>
            <div class="absolute inset-0 flex flex-col items-center justify-center">
              <span class="text-3xl font-bold text-gray-900">{{ diagnostico.escoreFinal.toFixed(0) }}</span>
              <span class="text-sm text-gray-500">/ 1000</span>
            </div>
          </div>

          <p class="text-lg font-medium text-yellow-600">
            {{ diagnostico.classificacao }}
          </p>

          <div class="flex flex-wrap gap-4 mt-4">
            <button
              @click="showModal = true"
              class="px-4 py-2 text-sm font-medium text-white bg-primary rounded-md hover:bg-primary-dark"
            >
              Visão Detalhada (Sdt1, Sma1...)
            </button>
            <button
              @click="goToPGES"
              class="px-4 py-2 text-sm font-medium text-white bg-blue-600 rounded-md hover:bg-blue-700"
            >
              Gerar Plano de Ação (PGES)
            </button>
          </div>
        </div>

        <div class="bg-white p-6 rounded-lg shadow-md">
          <h2 class="text-xl font-semibold text-gray-800 mb-6">Performance por Princípio</h2>
          <p class="text-sm text-gray-600 mb-4">
            Resumo do desempenho nos três pilares centrais do Pacto Global da ONU.
          </p>
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

    <DetalhesRelatorioModal
      v-if="showModal"
      :diagnostico="diagnostico"
      @close="showModal = false"
    />

  </div>
</template>

<script setup>
import { ref, onMounted, computed } from 'vue';
import { useRouter } from 'vue-router';
import api from '@/api.js';
import { useUserStore } from '@/stores/userStore';
import { useToast } from "vue-toastification"; // <-- 1. Importar o Toast
import DetalhesRelatorioModal from '@/components/DetalhesRelatorioModal.vue'; // <-- 2. Importar o Modal

const props = defineProps({ id: { type: String, required: true } });
const router = useRouter();
const userStore = useUserStore();
const toast = useToast(); // <-- 3. Iniciar o Toast

const isLoading = ref(true);
const diagnostico = ref(null);
const isDownloading = ref(false);
const showModal = ref(false); // <-- 4. Controlar o estado do Modal

// ... (principioMetadados, formatarData - sem alterações) ...
const principioMetadados = {
  'Direitos humanos e trabalho': { cor: 'bg-green-500', maxScore: 300 },
  'Meio ambiente': { cor: 'bg-blue-500', maxScore: 500 },
  'Anticorrupção': { cor: 'bg-red-500', maxScore: 200 },
};

const fetchRelatorio = async () => {
  try {
    isLoading.value = true;
    if (!userStore.user) {
      await userStore.fetchUser();
    }
    // Agora, carrega o diagnóstico E a empresa vinculada a ele
    const response = await api.get(`/diagnosticos/${props.id}?with=empresa`);
    diagnostico.value = response.data;
  } catch (error) {
    console.error('Erro ao buscar relatório:', error);
    toast.error('Não foi possível carregar o relatório.'); // <-- 5. Toast no erro
    if (error.response && error.response.status === 401) {
      router.push('/login');
    }
  } finally {
    isLoading.value = false;
  }
};

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

const formatarData = (dataISO) => {
  const data = new Date(dataISO);
  return data.toLocaleDateString('pt-BR', { day: '2-digit', month: '2-digit', year: 'numeric' });
};

// Função de Download do PDF (Corrigida com Toast)
const handleDownloadPDF = async () => {
  isDownloading.value = true;
  try {
    const response = await api.get(`/relatorio/${props.id}/pdf`, {
      responseType: 'blob',
    });
    const url = window.URL.createObjectURL(new Blob([response.data], { type: 'application/pdf' }));
    const link = document.createElement('a');
    link.href = url;
    const fileName = `SisDISE_Relatorio_${props.id}.pdf`;
    link.setAttribute('download', fileName);
    document.body.appendChild(link);
    link.click();
    document.body.removeChild(link);
    window.URL.revokeObjectURL(url);
  } catch (error) {
    console.error('Erro ao baixar PDF:', error);
    toast.error('Erro ao gerar o PDF. Verifique o servidor.'); // <-- 6. Toast no erro
  } finally {
    isDownloading.value = false;
  }
};

const goToPGES = () => {
  router.push(`/relatorio/${props.id}/pges`);
};

onMounted(() => {
  fetchRelatorio();
});
</script>
