<template>
  <div>
    <header class="bg-white shadow-md">
      <nav class="container mx-auto max-w-5xl px-4 py-3 flex justify-between items-center">
        <div>
          <img class="h-10 w-auto" src="/src/assets/logo.svg" alt="SisDISE Logo" />
          <span class="text-xl font-bold text-gray-800 ml-2">SisDISE Relatórios ESG</span>
        </div>
        <button @click="router.push('/dashboard')" class="text-sm font-medium text-gray-600 hover:text-indigo-600">
          Voltar ao Dashboard
        </button>
      </nav>
    </header>

    <main class="container mx-auto max-w-5xl p-4 mt-6">

      <div v-if="isLoading" class="text-center">
        <p class="text-lg text-gray-600">Carregando questionário...</p>
      </div>

      <div v-else>
        <h1 class="text-3xl font-bold text-gray-900 mb-6">Novo Diagnóstico</h1>

        <div v-if="errorMessage" class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-md mb-6">
          {{ errorMessage }}
        </div>

        <form class="space-y-8" @submit.prevent="handleSubmit">

          <div class="p-6 bg-white rounded-lg shadow-md">
            <h2 class="text-2xl font-semibold text-gray-800 border-b pb-2 mb-4">
              Passo 1: Selecione a Empresa
            </h2>
            <label for="empresa-select" class="block text-sm font-medium text-gray-700">Empresa a ser diagnosticada:</label>
            <select
              id="empresa-select"
              v-model="selectedEmpresa"
              class="mt-1 block w-full py-2 px-3 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500"
            >
              <option :value="null" disabled>-- Por favor, escolha uma empresa --</option>
              <option v-for="empresa in empresas" :key="empresa.id" :value="empresa.id">
                {{ empresa.razaoSocial }} (CNPJ: {{ empresa.cnpj }})
              </option>
            </select>
            <p v-if="empresas.length === 0" class="text-sm text-gray-500 mt-2">
              Nenhuma empresa cadastrada.
              <router-link to="/empresas" class="text-indigo-600 hover:underline">Cadastre uma empresa primeiro.</router-link>
            </p>
          </div>
          <div v-for="grupo in grupos" :key="grupo.id" class="p-6 bg-white rounded-lg shadow-md">

            <h2 class="text-2xl font-semibold text-gray-800 border-b pb-2 mb-4">
              {{ grupo.nome }}
            </h2>

            <div class="space-y-6">
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
                      class="h-4 w-4 text-indigo-600 border-gray-300 focus:ring-indigo-500"
                    />
                    <label :for="`item-${item.id}-nota-${nota}`" class="ml-2 block text-sm text-gray-900">
                      {{ nota }}
                    </label>
                  </div>
                </fieldset>
              </div>
            </div>
          </div>

          <div class="flex justify-end pt-4">
            <button
              type="submit"
              class="w-full sm:w-auto px-8 py-3 font-semibold text-white bg-green-600 rounded-md hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500"
              :disabled="isSubmitting"
            >
              {{ isSubmitting ? 'Calculando...' : 'Finalizar e Calcular Escore' }}
            </button>
          </div>

        </form>
      </div>
    </main>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import { useRouter } from 'vue-router';
import api from '@/api.js'; // Nosso Axios configurado

// --- 1. VARIÁVEIS DE ESTADO (Refs) ---

// Armazena os 6 grupos e 39 itens (perguntas) vindos da API
const grupos = ref([]);

// NOVO: Armazena a lista de empresas para o dropdown
const empresas = ref([]);

// NOVO: Armazena o ID da empresa selecionada no dropdown
const selectedEmpresa = ref(null);

// O objeto que armazena as respostas. Ex: { 1: 5, 2: 3, ... 39: 0 }
const respostas = ref({});

// Controladores de UI
const isLoading = ref(true); // Agora controla o carregamento de TUDO
const isSubmitting = ref(false);
const errorMessage = ref('');

const router = useRouter();

// --- 2. LÓGICA DE CARREGAMENTO (onMounted) ---

// Esta função é executada automaticamente quando a página é carregada
onMounted(() => {
  // Agora buscamos as perguntas E as empresas
  fetchInitialData();
});

// NOVO: Função para carregar tudo (perguntas e empresas)
const fetchInitialData = async () => {
  try {
    isLoading.value = true;
    errorMessage.value = '';

    // Busca as duas listas da API em paralelo
    const [questionarioResponse, empresasResponse] = await Promise.all([
      api.get('/questionario'),
      api.get('/empresas') // <-- BUSCA A LISTA DE EMPRESAS
    ]);

    // Processa o questionário
    grupos.value = questionarioResponse.data;
    for (const grupo of questionarioResponse.data) {
      for (const item of grupo.item_parametros) {
        respostas.value[item.id] = null; // Inicia sem resposta
      }
    }

    // Salva a lista de empresas
    empresas.value = empresasResponse.data;

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

// --- 3. LÓGICA DE ENVIO (handleSubmit) ---

// Esta função é chamada quando o usuário clica em "Finalizar"
const handleSubmit = async () => {
  isSubmitting.value = true;
  errorMessage.value = '';

  // A. Validação 1: Verifica se uma empresa foi selecionada
  if (!selectedEmpresa.value) {
    errorMessage.value = 'Erro: Por favor, selecione uma empresa para diagnosticar.';
    isSubmitting.value = false;
    window.scrollTo(0, 0);
    return;
  }

  // B. Transforma nosso objeto de respostas
  const payloadRespostas = Object.keys(respostas.value).map(itemId => {
    return {
      item_parametro_id: parseInt(itemId),
      nota: respostas.value[itemId]
    };
  });

  // C. Validação 2: Verifica se alguma resposta está 'null'
  const todasRespondidas = payloadRespostas.every(r => r.nota !== null);
  if (!todasRespondidas) {
    errorMessage.value = 'Erro: Por favor, responda todas as 39 perguntas antes de finalizar.';
    isSubmitting.value = false;
    window.scrollTo(0, 0);
    return;
  }

  try {
    // D. Cria o payload final para a API (AGORA DINÂMICO)
    const payloadFinal = {
      empresa_id: selectedEmpresa.value, // <-- USA A EMPRESA SELECIONADA
      respostas: payloadRespostas
    };

    // E. Envia os dados para 'POST /api/diagnosticos'
    const response = await api.post('/diagnosticos', payloadFinal);
    const diagnosticoSalvo = response.data;

    // F. SUCESSO! Redireciona para a página de Relatório
    router.push(`/relatorio/${diagnosticoSalvo.id}`);

  } catch (error) {
    console.error('Erro ao salvar diagnóstico:', error);
    errorMessage.value = 'Ocorreu um erro ao salvar seu diagnóstico. Tente novamente.';
    isSubmitting.value = false;
  }
};
</script>
