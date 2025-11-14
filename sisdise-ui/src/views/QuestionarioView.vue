<template>
  <div>
    <div v-if="isLoading" class="text-center">
      <p class="text-lg text-gray-600">Carregando questionário...</p>
    </div>

    <div v-else class="bg-white p-6 rounded-lg shadow-md">
      <h1 class="text-3xl font-bold text-gray-900 mb-4">Novo Diagnóstico</h1>

      <div v-if="errorMessage" class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-md mb-6">
        {{ errorMessage }}
      </div>

      <div class="border-b border-gray-200 mb-6">
        <nav class="-mb-px flex space-x-8 overflow-x-auto" aria-label="Tabs">
          <a
            href="#"
            @click.prevent="goToStep(0)"
            :class="[
              currentStep === 0 ? 'border-primary text-primary-dark' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300',
              'whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm'
            ]"
          >
            Empresa
          </a>

          <a
            v-for="(grupo, index) in grupos"
            :key="grupo.id"
            href="#"
            @click.prevent="goToStep(index + 1)"
            :class="[
              currentStep === (index + 1) ? 'border-primary text-primary-dark' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300',
              'whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm'
            ]"
          >
            {{ grupo.codigo }} </a>
        </nav>
      </div>

      <form class="space-y-8" @submit.prevent="handleSubmit">

        <div v-if="currentStep === 0" class="space-y-4">
          <h2 class="text-2xl font-semibold text-gray-800">Passo 1: Selecione a Empresa</h2>
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

        <div v-for="(grupo, index) in grupos" :key="grupo.id">
          <div v-if="currentStep === (index + 1)" class="space-y-6">
            <h2 class="text-2xl font-semibold text-gray-800 border-b pb-2">
              Grupo {{ index + 1 }}: {{ grupo.nome }}
            </h2>

            <div v-for="item in grupo.item_parametros" :key="item.id" class="border-b border-gray-200 pb-4">
              <p class="text-md font-medium text-gray-700">
                {{ item.codigo_item }}. {{ item.descricao }}
              </p>

              <fieldset class="mt-3 flex flex-wrap gap-x-6 gap-y-2">
                <legend class="sr-only">Classificação de {{ item.id }}</legend>
                <div v-for="nota in [0, 1, 2, 3, 4, 5]" :key="nota" class="flex items-center">
                  <input
                    :id="`item-${item.id}-nota-${nota}`"
                    :name="`item-${item.id}`"
                    type="radio"
                    :value="nota"
                    v-model="respostas[item.id]"
                    class="h-4 w-4 text-primary border-gray-300 focus:ring-primary-light"
                  />
                  <label :for="`item-${item.id}-nota-${nota}`" class="ml-2 block text-sm text-gray-900">
                    {{ nota }}
                  </label>
                </div>
              </fieldset>
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

// --- Estado do Wizard ---
const currentStep = ref(0); // Começa no Passo 0 (Seleção de Empresa)
const totalSteps = computed(() => grupos.value.length); // Total de passos (será 6)

// --- Estado dos Dados ---
const grupos = ref([]);
const empresas = ref([]);
const selectedEmpresa = ref(null);
const respostas = ref({});

// --- Estado da UI ---
const isLoading = ref(true);
const isSubmitting = ref(false);
const errorMessage = ref('');

const router = useRouter();
const userStore = useUserStore();

// --- Lógica de Carregamento ---
onMounted(() => {
  fetchInitialData();
});

const fetchInitialData = async () => {
  try {
    isLoading.value = true;
    errorMessage.value = '';

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
    errorMessage.value = 'Não foi possível carregar os dados. Tente novamente.';
    if (error.response && error.response.status === 401) {
      router.push('/login');
    }
  } finally {
    isLoading.value = false;
  }
};

// --- Lógica de Navegação do Wizard ---

const nextStep = () => {
  errorMessage.value = ''; // Limpa erros antigos

  // Validação do Passo 0 (Empresa)
  if (currentStep.value === 0) {
    if (!selectedEmpresa.value) {
      errorMessage.value = 'Erro: Por favor, selecione uma empresa para diagnosticar.';
      return;
    }
  }

  // Validação dos Passos 1-6 (Perguntas)
  if (currentStep.value > 0 && currentStep.value <= totalSteps.value) {
    // Pega o grupo de perguntas do passo atual
    const grupoAtual = grupos.value[currentStep.value - 1];

    // Verifica se todos os itens deste grupo foram respondidos
    const todasRespondidas = grupoAtual.item_parametros.every(item => {
      return respostas.value[item.id] !== null;
    });

    if (!todasRespondidas) {
      errorMessage.value = `Erro: Por favor, responda todas as perguntas do grupo "${grupoAtual.codigo}" antes de avançar.`;
      return;
    }
  }

  // Avança para o próximo passo
  if (currentStep.value < totalSteps.value) {
    currentStep.value++;
  }
};

const prevStep = () => {
  errorMessage.value = ''; // Limpa erros
  if (currentStep.value > 0) {
    currentStep.value--;
  }
};

const goToStep = (step) => {
  // Permite saltar para passos anteriores, mas não futuros
  if (step < currentStep.value) {
    currentStep.value = step;
    errorMessage.value = '';
  }
  // Se tentar saltar para um passo futuro, força o 'nextStep' (que fará a validação)
  if (step > currentStep.value && step === (currentStep.value + 1)) {
    nextStep();
  }
};


// --- Lógica de Envio (Quase idêntica à anterior) ---
const handleSubmit = async () => {
  isSubmitting.value = true;
  errorMessage.value = '';

  // Validação Final (embora o nextStep já valide, é uma segurança extra)
  const todasRespondidas = Object.values(respostas.value).every(nota => nota !== null);
  if (!todasRespondidas || !selectedEmpresa.value) {
    errorMessage.value = 'Erro: Por favor, certifique-se de que todas as 39 perguntas estão respondidas e uma empresa foi selecionada.';
    isSubmitting.value = false;
    return;
  }

  // Transforma as respostas
  const payloadRespostas = Object.keys(respostas.value).map(itemId => {
    return {
      item_parametro_id: parseInt(itemId),
      nota: respostas.value[itemId]
    };
  });

  try {
    const payloadFinal = {
      empresa_id: selectedEmpresa.value,
      respostas: payloadRespostas
    };

    const response = await api.post('/diagnosticos', payloadFinal);
    const diagnosticoSalvo = response.data;

    router.push(`/relatorio/${diagnosticoSalvo.id}`);

  } catch (error) {
    console.error('Erro ao salvar diagnóstico:', error);
    errorMessage.value = 'Ocorreu um erro ao salvar seu diagnóstico. Tente novamente.';
    isSubmitting.value = false;
  }
};
</script>
