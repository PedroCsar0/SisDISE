<template>
  <div>
    <div v-if="isLoading" class="text-center">
      <p class="text-lg text-gray-600">Carregando questionário...</p>
    </div>

    <div v-else class="bg-white p-6 rounded-lg shadow-md">
      <h1 class="text-3xl font-bold text-gray-900 mb-4">Novo Diagnóstico</h1>

      <div class="mb-6">
        <div class="flex justify-between mb-1">
          <span class="text-sm font-medium text-primary-dark">Progresso</span>
          <span class="text-sm font-medium text-primary-dark">{{ progressoPercentual.toFixed(0) }}%</span>
        </div>
        <div class="w-full bg-gray-200 rounded-full h-2.5">
          <div
            class="bg-primary h-2.5 rounded-full transition-all duration-500"
            :style="{ width: progressoPercentual + '%' }"
          ></div>
        </div>
      </div>
      <form class="space-y-8" @submit.prevent="handleSubmit">

        <div v-if="currentStep === 0" class="space-y-4">
          <h2 class="text-2xl font-semibold text-gray-800">Passo 1: Detalhes do Diagnóstico</h2>

          <div>
            <label for="empresa-select" class="block text-sm font-medium text-gray-700">Empresa a ser diagnosticada:</label>
            <select
              id="empresa-select"
              v-model="selectedEmpresa"
              class="mt-1 block w-full py-2 px-3 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none focus:ring-primary-light focus:border-primary-light"
            >
              <option :value="null" disabled>-- Por favor, escolha uma empresa --</option>
              <option v-for="empresa in empresas" :key="empresa.id" :value="empresa.id">
                {{ empresa.razaoSocial }} (CNPJ: {{ empresa.cnpj }})
              </option>
            </select>
            <p v-if="empresas.length === 0" class="text-sm text-gray-500 mt-2">
              Nenhuma empresa cadastrada.
              <router-link to="/empresas" class="text-primary-dark hover:underline">Cadastre uma empresa primeiro.</router-link>
            </p>
          </div>

          <div class="mt-4">
            <label for="titulo-diagnostico" class="block text-sm font-medium text-gray-700">
              Título Personalizado do Diagnóstico
            </label>
            <input
              v-model="tituloDiagnostico"
              type="text"
              id="titulo-diagnostico"
              placeholder="Ex: Diagnóstico Inicial - Transportadora"
              class="mt-1 block w-full py-2 px-3 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none focus:ring-primary-light focus:border-primary-light"
            />
          </div>
        </div>

        <div v-for="(grupo, index) in grupos" :key="grupo.id">
          <div v-if="currentStep === (index + 1)" class="space-y-6">
            <h2 class="text-2xl font-semibold text-gray-800 border-b pb-2">
              Grupo {{ index + 1 }}: {{ grupo.nome }}
            </h2>

            <div v-for="item in grupo.item_parametros" :key="item.id" class="border-b border-gray-200 pb-4">
              <p class="text-md font-medium text-gray-700">
                {{ item.codigo_item }}. {{ item.descricao }}
              </p>

              <div class="mt-3">
                <label :for="`item-select-${item.id}`" class="block text-sm font-medium text-gray-700">Classificação:</label>
                <select
                  :id="`item-select-${item.id}`"
                  v-model="respostas[item.id]"
                  class="mt-1 block w-full py-2 px-3 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none focus:ring-primary-light focus:border-primary-light"
                >
                  <option
                    v-for="option in notaOptions"
                    :key="option.value"
                    :value="option.value"
                    :disabled="option.value === null"
                  >
                    {{ option.text }}
                  </option>
                </select>
              </div>
            </div>
          </div>
        </div>

        <div class="flex justify-between pt-4 border-t">
          <button
            type="button"
            @click="prevStep"
            :disabled="currentStep === 0"
            class="px-6 py-2 font-medium text-gray-700 bg-gray-200 rounded-md hover:bg-gray-300 disabled:opacity-50"
          >
            Anterior
          </button>

          <button
            v-if="currentStep < totalSteps"
            type="button"
            @click="nextStep"
            class="px-6 py-2 font-medium text-white bg-primary rounded-md hover:bg-primary-dark"
          >
            Próximo
          </button>

          <button
            v-if="currentStep === totalSteps"
            type="submit"
            class="px-8 py-3 font-semibold text-white bg-green-600 rounded-md hover:bg-green-700"
            :disabled="isSubmitting"
          >
            {{ isSubmitting ? 'Calculando...' : 'Finalizar e Calcular Escore' }}
          </button>
        </div>

      </form>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted, computed } from 'vue';
import { useRouter } from 'vue-router';
import api from '@/api.js';
import { useUserStore } from '@/stores/userStore';
import { useToast } from "vue-toastification";

const notaOptions = [
  { value: null, text: 'Selecione uma classificação' },
  { value: 0, text: '0 - Não cumpre (Ruim)' },
  { value: 1, text: '1 - Cumpre parcialmente (Razoável)' },
  { value: 2, text: '2 - Cumpre a lei (Mediano)' },
  { value: 3, text: '3 - Ações informais (Bom)' },
  { value: 4, text: '4 - Políticas oficializadas (Ótimo)' },
  { value: 5, text: '5 - Monitora e divulga (Excelente)' }
];

const currentStep = ref(0);
const totalSteps = computed(() => grupos.value.length);

const progressoPercentual = computed(() => {
  if (totalSteps.value === 0) return 0;
  return (currentStep.value / totalSteps.value) * 100;
});

const grupos = ref([]);
const empresas = ref([]);
const selectedEmpresa = ref(null);
const tituloDiagnostico = ref(''); // <-- A variável do Título
const respostas = ref({});

const isLoading = ref(true);
const isSubmitting = ref(false);
const router = useRouter();
const userStore = useUserStore();
const toast = useToast();
// errorMessage foi removido (corrigindo o Vue warn)

onMounted(() => {
  fetchInitialData();
});

const fetchInitialData = async () => {
  try {
    isLoading.value = true;
    if (!userStore.user) {
      await userStore.fetchUser();
    }
    const [questionarioResponse, empresasResponse] = await Promise.all([
      api.get('/questionario'),
      api.get('/empresas')
    ]);
    grupos.value = questionarioResponse.data;
    empresas.value = empresasResponse.data;
    for (const grupo of questionarioResponse.data) {
      for (const item of grupo.item_parametros) {
        respostas.value[item.id] = null;
      }
    }
  } catch (error) {
    console.error('Erro ao buscar dados iniciais:', error);
    toast.error('Não foi possível carregar os dados. Tente novamente.');
    if (error.response && error.response.status === 401) {
      router.push('/login');
    }
  } finally {
    isLoading.value = false;
  }
};

const nextStep = () => {
  if (currentStep.value === 0) {
    if (!selectedEmpresa.value) {
      toast.error('Erro: Por favor, selecione uma empresa para diagnosticar.');
      return;
    }
    // Adiciona a validação do título
    if (!tituloDiagnostico.value.trim()) {
      toast.error('Erro: Por favor, insira um título para este diagnóstico.');
      return;
    }
  }

  if (currentStep.value > 0 && currentStep.value <= totalSteps.value) {
    const grupoAtual = grupos.value[currentStep.value - 1];
    const todasRespondidas = grupoAtual.item_parametros.every(item => {
      return respostas.value[item.id] !== null;
    });
    if (!todasRespondidas) {
      toast.error(`Erro: Por favor, responda todas as perguntas do grupo "${grupoAtual.codigo}".`);
      return;
    }
  }

  if (currentStep.value < totalSteps.value) {
    currentStep.value++;
  }
};

const prevStep = () => {
  if (currentStep.value > 0) {
    currentStep.value--;
  }
};

const handleSubmit = async () => {
  isSubmitting.value = true;

  const todasRespondidas = Object.values(respostas.value).every(nota => nota !== null);
  if (!todasRespondidas || !selectedEmpresa.value || !tituloDiagnostico.value.trim()) {
    toast.error('Erro: Certifique-se de que a empresa, o título e todas as 39 perguntas estão preenchidos.');
    isSubmitting.value = false;
    currentStep.value = 0; // Volta ao primeiro passo se faltar empresa/título
    return;
  }

  const payloadRespostas = Object.keys(respostas.value).map(itemId => {
    return {
      item_parametro_id: parseInt(itemId),
      nota: respostas.value[itemId]
    };
  });

  try {
    // CORRIGIDO: Adiciona o 'titulo' ao payload final
    const payloadFinal = {
      empresa_id: selectedEmpresa.value,
      titulo: tituloDiagnostico.value,
      respostas: payloadRespostas
    };

    const response = await api.post('/diagnosticos', payloadFinal);
    const diagnosticoSalvo = response.data;

    toast.success("Diagnóstico salvo! A calcular relatório...");
    router.push(`/relatorio/${diagnosticoSalvo.id}`);

  } catch (error) {
    console.error('Erro ao salvar diagnóstico:', error);
    toast.error('Ocorreu um erro ao salvar seu diagnóstico.');
    isSubmitting.value = false;
  }
};
</script>
