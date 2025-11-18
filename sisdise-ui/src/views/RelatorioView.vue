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

        <div class="bg-white p-6 rounded-lg shadow-md flex flex-col justify-between">
          <div>
            <h2 class="text-xl font-semibold text-gray-800 text-center">Score Final</h2>

            <div class="my-6 text-center">
              <span
                :class="scoreColorClasses.text"
                class="text-6xl font-bold"
              >
                {{ diagnostico.escoreFinal.toFixed(0) }}
              </span>
              <span class="text-3xl text-gray-500">/ 1000</span>
            </div>

            <div class="w-full bg-gray-200 rounded-full h-4">
              <div
                :class="scoreColorClasses.bg"
                class="h-4 rounded-full transition-all duration-500"
                :style="{ width: scorePercent + '%' }"
              ></div>
            </div>

            <p
              :class="scoreColorClasses.text"
              class="text-xl font-semibold text-center mt-4"
            >
              {{ diagnostico.classificacao }}
            </p>
          </div>

          <div class="flex flex-wrap gap-4 mt-8 pt-6 border-t">
            <button
              @click="showModal = true"
              class="flex-1 px-4 py-2 text-sm font-medium text-white bg-primary rounded-md hover:bg-primary-dark"
            >
              Visão Detalhada
            </button>
            <button
              @click="goToPGES"
              class="flex-1 px-4 py-2 text-sm font-medium text-white bg-blue-600 rounded-md hover:bg-blue-700"
            >
              Gerar Plano de Ação
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
import { useToast } from "vue-toastification";
import DetalhesRelatorioModal from '@/components/DetalhesRelatorioModal.vue';

const props = defineProps({ id: { type: String, required: true } });
const router = useRouter();
const userStore = useUserStore();
const toast = useToast();

const isLoading = ref(true);
const diagnostico = ref(null);
const isDownloading = ref(false);
const showModal = ref(false);

const principioMetadados = {
  'Direitos humanos e trabalho': { cor: 'bg-green-500', maxScore: 300 },
  'Meio ambiente': { cor: 'bg-blue-500', maxScore: 500 },
  'Anticorrupção': { cor: 'bg-red-500', maxScore: 200 },
};

// --- (NOVO) Lógica de Cores e Percentagem ---

// 1. Calcula a percentagem da "barrinha"
const scorePercent = computed(() => {
  if (!diagnostico.value) return 0;
  return (diagnostico.value.escoreFinal / 1000) * 100;
});

// 2. Define as cores intuitivas
const scoreColorClasses = computed(() => {
  if (!diagnostico.value) return { text: 'text-gray-600', bg: 'bg-gray-600' };

  const score = diagnostico.value.escoreFinal;

  if (score <= 500) { // Baixa ou Inexistente
    return { text: 'text-red-600', bg: 'bg-red-500' };
  }
  if (score <= 750) { // Moderada
    return { text: 'text-yellow-600', bg: 'bg-yellow-500' };
  }
  return { text: 'text-green-600', bg: 'bg-green-500' }; // Sustentável
});
// --- FIM DO NOVO BLOCO ---


const fetchRelatorio = async () => {
  try {
    isLoading.value = true;
    if (!userStore.user) {
      await userStore.fetchUser();
    }
    const response = await api.get(`/diagnosticos/${props.id}`);
    diagnostico.value = response.data;
  } catch (error) {
    console.error('Erro ao buscar relatório:', error);
    toast.error('Não foi possível carregar o relatório.');
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
  // Corrigido para lidar com fuso horário (bug da data)
  if (!dataISO) return '';
  const dataApenas = dataISO.split('T')[0];
  const partes = dataApenas.split('-');
  if (partes.length !== 3) return dataISO;
  return `${partes[2]}/${partes[1]}/${partes[0]}`;
};

const handleDownloadPDF = async () => {
  isDownloading.value = true;
  try {
    const response = await api.get(`/relatorio/${props.id}/pdf`, {
      responseType: 'blob',
    });
    const url = window.URL.createObjectURL(new Blob([response.data], { type: 'application/pdf' }));
    const link = document.createElement('a');
    link.href = url;
    const fileName = `SisDISE_Relatorio_${diagnostico.value.titulo || props.id}.pdf`;
    link.setAttribute('download', fileName);
    document.body.appendChild(link);
    link.click();
    document.body.removeChild(link);
    window.URL.revokeObjectURL(url);
  } catch (error) {
    console.error('Erro ao baixar PDF:', error);
    toast.error('Erro ao gerar o PDF. Verifique o servidor.');
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
