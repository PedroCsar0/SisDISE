<template>
  <div>
    <div v-if="isLoading" class="text-center">
      <p class="text-lg text-gray-600">Carregando questionário...</p>
    </div>

    <div v-else class="bg-white p-6 rounded-lg shadow-md">

      <div class="flex justify-between items-center mb-4">
        <h1 class="text-3xl font-bold text-gray-900">Novo Diagnóstico</h1>
        <button
          v-if="hasDraft"
          @click="clearDraft"
          class="px-3 py-1 text-sm font-medium text-red-600 bg-red-100 rounded-full hover:bg-red-200"
        >
          Limpar Rascunho
        </button>
      </div>

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

        <div v-if="currentStep === 0" class="space-y-4 bg-gray-50 p-4 rounded-lg shadow-sm">
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

            <div
              v-for="item in grupo.item_parametros"
              :key="item.id"
              class="bg-gray-50 p-4 rounded-lg shadow-sm space-y-3"
            >

              <p class="text-md font-medium text-gray-900">
                {{ item.codigo_item }}. {{ item.descricao }}
              </p>

              <div>
                <label :for="`item-select-${item.id}`" class="block text-sm font-medium text-gray-700">Classificação:</label>
                <select
                  :id="`item-select-${item.id}`"
                  v-model.number="respostas[item.id]"
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

              <div
                v-if="respostas[item.id] !== null && getHint(respostas[item.id])"
                class="p-2 bg-blue-50 border border-blue-200 text-blue-800 text-sm rounded-md"
              >
                <strong>Critério (Nota {{ respostas[item.id] }}):</strong>
                {{ getHint(respostas[item.id]) }}
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
import { ref, onMounted, computed, watch } from 'vue';
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

// (NOVO) Função para extrair a Dica
const getHint = (nota) => {
  if (nota === null) return '';
  const option = notaOptions.find(o => o.value === nota);
  const hintMatch = option.text.match(/\((.*?)\)/);
  return hintMatch ? hintMatch[1] : '';
};

const currentStep = ref(0);
const totalSteps = computed(() => grupos.value.length);

const progressoPercentual = computed(() => {
  if (totalSteps.value === 0) return 0;
  return (currentStep.value / totalSteps.value) * 100;
});

const grupos = ref([]);
const empresas = ref([]);
const selectedEmpresa = ref(null);
const tituloDiagnostico = ref('');
const respostas = ref({});
const hasDraft = ref(false);

const isLoading = ref(true);
const isSubmitting = ref(false);
const router = useRouter();
const userStore = useUserStore();
const toast = useToast();

// --- (NOVO) Função para gerar a chave única do usuário ---
const getDraftKey = () => {
  if (userStore.user && userStore.user.id) {
    return `sisdise_draft_user_${userStore.user.id}`;
  }
  return null;
};

// VIGIAR (Watch): Salva no localStorage com a chave do usuário
watch([respostas, selectedEmpresa, tituloDiagnostico, currentStep],
  ([newRespostas, newEmpresa, newTitulo, newStep]) => {
    const key = getDraftKey();
    if (key) {
      const draft = {
        empresaId: newEmpresa,
        titulo: newTitulo,
        step: newStep,
        respostas: newRespostas
      };
      localStorage.setItem(key, JSON.stringify(draft));
      hasDraft.value = true;
    }
  },
  { deep: true }
);

// LIMPAR (Clear)
const clearDraft = () => {
  if (window.confirm('Tem certeza que deseja apagar seu progresso salvo e recomeçar?')) {
    const key = getDraftKey();
    if (key) localStorage.removeItem(key);

    hasDraft.value = false;
    selectedEmpresa.value = null;
    tituloDiagnostico.value = '';
    currentStep.value = 0;
    Object.keys(respostas.value).forEach(key => {
      respostas.value[key] = null;
    });
    toast.info("Rascunho limpo. Pode começar de novo.");
  }
};

// Lógica de Carregamento
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

    // --- Carregar Rascunho do Usuário Específico ---
    const key = getDraftKey();
    let draftRespostas = {};

    if (key) {
      const savedDraft = localStorage.getItem(key);
      if (savedDraft) {
        toast.info("Seu rascunho salvo foi carregado.");
        const draft = JSON.parse(savedDraft);
        selectedEmpresa.value = draft.empresaId;
        tituloDiagnostico.value = draft.titulo;
        currentStep.value = draft.step || 0;
        draftRespostas = draft.respostas || {};
        hasDraft.value = true;
      }
    }

    for (const grupo of questionarioResponse.data) {
      for (const item of grupo.item_parametros) {
        respostas.value[item.id] = draftRespostas[item.id] || null;
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

// Navegação
const nextStep = () => {
  if (currentStep.value === 0) {
    if (!selectedEmpresa.value) {
      toast.error('Erro: Por favor, selecione uma empresa para diagnosticar.');
      return;
    }
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

// Envio
const handleSubmit = async () => {
  isSubmitting.value = true;

  const todasRespondidas = Object.values(respostas.value).every(nota => nota !== null);
  if (!todasRespondidas || !selectedEmpresa.value || !tituloDiagnostico.value.trim()) {
    toast.error('Erro: Certifique-se de que a empresa, o título e todas as 39 perguntas estão preenchidos.');
    isSubmitting.value = false;
    currentStep.value = 0;
    return;
  }

  const payloadRespostas = Object.keys(respostas.value).map(itemId => {
    return {
      item_parametro_id: parseInt(itemId),
      nota: respostas.value[itemId]
    };
  });

  try {
    const payloadFinal = {
      empresa_id: selectedEmpresa.value,
      titulo: tituloDiagnostico.value,
      respostas: payloadRespostas
    };

    const response = await api.post('/diagnosticos', payloadFinal);
    const diagnosticoSalvo = response.data;

    toast.success("Diagnóstico salvo! A calcular relatório...");

    // Remove o rascunho específico deste usuário
    const key = getDraftKey();
    if (key) localStorage.removeItem(key);
    hasDraft.value = false;

    router.push(`/relatorio/${diagnosticoSalvo.id}`);

  } catch (error) {
    console.error('Erro ao salvar diagnóstico:', error);
    toast.error('Ocorreu um erro ao salvar seu diagnóstico.');
    isSubmitting.value = false;
  }
};
</script>
