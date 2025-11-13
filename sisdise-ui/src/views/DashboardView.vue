<template>
  <header class="bg-white shadow-md">
    <nav class="container mx-auto max-w-5xl px-4 py-3 flex justify-between items-center">
      <div>
        <img class="h-10 w-auto" src="/src/assets/logo.svg" alt="SisDISE Logo" />
        <span class="text-xl font-bold text-gray-800 ml-2">SisDISE Relatórios ESG</span>
      </div>

      <div v-if="user" class="flex items-center space-x-4"> <router-link
        v-if="user.tipo === 'Administrador'"
        to="/admin"
        class="px-3 py-1 text-sm font-medium text-white bg-red-600 rounded-full hover:bg-red-700"
      >
        Painel Admin
      </router-link>

      <span class="text-gray-600">Olá, {{ user.name }}</span>
      <button @click="handleLogout" class="px-3 py-1 text-sm font-medium text-indigo-600 bg-indigo-100 rounded-full hover:bg-indigo-200">
        Sair
      </button>
    </div>
    </nav>
  </header>

  <main class="container mx-auto max-w-5xl p-4 mt-6">

    <div v-if="isLoading">
      <p>Carregando dados do dashboard...</p>
    </div>

    <div v-else>
      <h1 class="text-3xl font-bold text-gray-900 mb-6">
        BEM VINDO, {{ user ? user.name.toUpperCase() : '' }}
      </h1>

      <div class="grid grid-cols-1 md:grid-cols-3 gap-6">

        <div class="md:col-span-2 space-y-6">

          <div
            class="p-6 rounded-lg border"
            :class="ultimoDiagnostico ? 'bg-green-100 border-green-300' : 'bg-gray-100 border-gray-300'"
          >
            <h2 class="text-lg font-semibold" :class="ultimoDiagnostico ? 'text-green-800' : 'text-gray-800'">Status</h2>

            <template v-if="ultimoDiagnostico">
              <p class="text-2xl font-bold text-green-900 mt-2">
                Score: {{ ultimoDiagnostico.escoreFinal }} pontos
              </p>
              <p class="text-sm text-gray-600 mt-1">
                Último relatório: {{ formatarData(ultimoDiagnostico.dataAnalise) }}
              </p>
              <p class="text-sm text-gray-600">
                Classificação: {{ ultimoDiagnostico.classificacao }}
              </p>
            </template>

            <template v-else>
              <p class="text-lg text-gray-700 mt-2">
                Nenhum diagnóstico encontrado. Comece o seu primeiro!
              </p>
            </template>
          </div>

          <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
            <router-link
              to="/diagnostico/novo"
              class="bg-yellow-400 p-8 rounded-lg text-center text-gray-900 font-bold text-xl hover:bg-yellow-500 transition-colors"
            >
              + Novo Diagnóstico
            </router-link>

            <router-link
              to="/empresas"
              class="bg-blue-400 p-8 rounded-lg text-center text-white font-bold text-xl hover:bg-blue-500 transition-colors"
            >
              Gerenciar Empresas
            </router-link>
        </div>

        </div> <div class="bg-white p-6 rounded-lg shadow-md md:row-span-2">
          <h2 class="text-lg font-semibold text-gray-900 mb-4">
            Registro de Relatórios
          </h2>

          <ul v-if="diagnosticos.length > 0" class="space-y-3">
            <li
              v-for="diag in diagnosticos"
              :key="diag.id"
              @click="goToRelatorio(diag.id)"
              class="p-3 border rounded-md hover:bg-gray-100 hover:shadow-sm cursor-pointer"
            >
              <p class="font-semibold text-indigo-700">Relatório #{{ diag.id }}</p>
              <p class="text-sm text-gray-600">Data: {{ formatarData(diag.dataAnalise) }}</p>
              <p class="text-sm font-bold text-gray-800">Score: {{ diag.escoreFinal }}</p>
            </li>
          </ul>

          <p v-else class="text-sm text-gray-500">
            Seu histórico de diagnósticos aparecerá aqui.
          </p>
        </div>

      </div>
    </div>
</main>
</template>

<script setup>
import { ref, onMounted, computed } from 'vue'; // Importe o 'computed'
import { useRouter } from 'vue-router';
import api from '@/api.js';

// 1. NOVAS VARIÁVEIS REATIVAS
const user = ref(null);
const diagnosticos = ref([]); // Armazena a lista de diagnósticos
const isLoading = ref(true);  // Controla o carregamento de TUDO

const router = useRouter();

// 2. BUSCAR DADOS (ATUALIZADO)
// Quando o componente é carregado
onMounted(async () => {
  isLoading.value = true;
  try {
    // Busca o usuário e a lista de diagnósticos em paralelo
    const [userResponse, diagnosticosResponse] = await Promise.all([
      api.get('/user'),
      api.get('/diagnosticos') // <-- CHAMA A NOVA ROTA
    ]);

    user.value = userResponse.data;
    diagnosticos.value = diagnosticosResponse.data;

  } catch (error) {
    console.error('Erro ao buscar dados do dashboard:', error);
    if (error.response && error.response.status === 401) {
      router.push('/login');
    }
  } finally {
    isLoading.value = false;
  }
});

// 3. PROPRIEDADE COMPUTADA (NOVA)
// Encontra o diagnóstico mais recente na lista
const ultimoDiagnostico = computed(() => {
  // Como a API já retorna os mais recentes primeiro,
  // o último diagnóstico é simplesmente o primeiro item (índice 0)
  if (diagnosticos.value && diagnosticos.value.length > 0) {
    return diagnosticos.value[0];
  }
  return null; // Retorna nulo se não houver diagnósticos
});

// 4. FUNÇÕES DE NAVEGAÇÃO E LOGOUT (sem alterações)
const handleLogout = async () => {
  try {
    localStorage.removeItem('authToken');
    delete api.defaults.headers.common['Authorization'];
    router.push('/login');
  } catch (error) {
    console.error('Erro no logout:', error);
  }
};

// Navega para a página de relatório que já criamos
const goToRelatorio = (id) => {
  router.push(`/relatorio/${id}`);
};

// Formata a data (função auxiliar)
const formatarData = (dataISO) => {
  const data = new Date(dataISO);
  return data.toLocaleDateString('pt-BR', {
    day: '2-digit',
    month: '2-digit',
    year: 'numeric',
  });
};
</script>
