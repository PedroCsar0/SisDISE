<template>
  <div v-if="isLoading">
    <p>Carregando dados do dashboard...</p>
  </div>

  <div v-else>
    <h1 class="text-3xl font-bold text-gray-900 mb-6">
      BEM VINDO, {{ userStore.user.value ? userStore.user.name.toUpperCase() : '' }}
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

        <div
        v-if="userStore.user && userStore.user.tipo !== 'Gestor Empresarial'"
        class="grid grid-cols-1 sm:grid-cols-2 gap-6"
      >
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

      </div> <div class="bg-white p-6 rounded-lg shadow-md">
        <h2 class="text-lg font-semibold text-gray-900 mb-4">Evolução</h2>
        <div v-if="diagnosticos.length > 0" class="h-64">
          <LineChart :chartData="chartData" :chartOptions="chartOptions" />
        </div>
        <div v-else class="flex items-center justify-center h-64 bg-gray-50 rounded-md">
          <p class="text-gray-500">Faça seu primeiro diagnóstico para ver a evolução.</p>
        </div>
      </div>

      <div class="md:col-span-3 bg-white p-6 rounded-lg shadow-md">
        <h2 class="text-lg font-semibold text-gray-900 mb-4">
          Registro de Relatórios
        </h2>
        <ul v-if="diagnosticos.length > 0" class="space-y-3 max-h-60 overflow-y-auto">
          <li
            v-for="diag in diagnosticos"
            :key="diag.id"
            @click="goToRelatorio(diag.id)"
            class="p-3 border rounded-md hover:bg-gray-100 hover:shadow-sm cursor-pointer flex justify-between items-center"
          >
            <div>
              <p class="font-semibold text-indigo-700">Relatório #{{ diag.id }} (Empresa: {{ diag.empresa.razaoSocial }})</p>
              <p class="text-sm text-gray-600">Data: {{ formatarData(diag.dataAnalise) }}</p>
            </div>
            <p class="text-lg font-bold text-gray-800">Score: {{ diag.escoreFinal }}</p>
          </li>
        </ul>
        <p v-else class="text-sm text-gray-500">
          Seu histórico de diagnósticos aparecerá aqui.
        </p>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted, computed } from 'vue';
import { useRouter } from 'vue-router';
import api from '@/api.js';
import { useUserStore } from '@/stores/userStore'; // <-- IMPORTA O STORE
import LineChart from '@/components/LineChart.vue';

// --- 1. DADOS ---
const diagnosticos = ref([]);
const isLoading = ref(true);
const router = useRouter();
const userStore = useUserStore(); // <-- USA O STORE

// --- 2. BUSCAR DADOS (onMounted limpo) ---
onMounted(async () => {
  isLoading.value = true;
  try {
    // Garante que o usuário esteja carregado (caso o usuário dê F5 nesta página)
    if (!userStore.user) {
      await userStore.fetchUser();
    }

    // Busca apenas os diagnósticos
    const diagnosticosResponse = await api.get('/diagnosticos');
    diagnosticos.value = diagnosticosResponse.data;

  } catch (error) {
    console.error('Erro ao buscar dados do dashboard:', error);
    if (error.response && error.response.status === 401) {
      // Se o token for inválido, limpa o store e volta ao login
      userStore.clearUser();
      router.push('/login');
    }
  } finally {
    isLoading.value = false;
  }
});

// --- 3. COMPUTED: ultimoDiagnostico ---
const ultimoDiagnostico = computed(() => {
  if (diagnosticos.value && diagnosticos.value.length > 0) {
    return diagnosticos.value[0];
  }
  return null;
});

// --- 4. COMPUTED: chartOptions ---
const chartOptions = ref({
  responsive: true,
  maintainAspectRatio: false,
  plugins: {
    legend: {
      display: false,
    },
  },
  scales: {
    y: {
      beginAtZero: false,
      suggestedMin: 400,
      suggestedMax: 1000,
    },
  },
});

// --- 5. COMPUTED: chartData ---
const chartData = computed(() => {
  if (!diagnosticos.value || diagnosticos.value.length === 0) {
    return { labels: [], datasets: [] };
  }
  const diagnosticosCronologicos = [...diagnosticos.value].reverse();
  const labels = diagnosticosCronologicos.map(diag => formatarData(diag.dataAnalise));
  const data = diagnosticosCronologicos.map(diag => diag.escoreFinal);
  return {
    labels: labels,
    datasets: [
      {
        label: 'Escore Final',
        data: data,
        backgroundColor: 'rgba(79, 70, 229, 0.2)',
        borderColor: 'rgba(79, 70, 229, 1)',
        fill: true,
        tension: 0.1,
      },
    ],
  };
});

// --- 6. FUNÇÕES DE NAVEGAÇÃO ---
const goToRelatorio = (id) => {
  router.push(`/relatorio/${id}`);
};

// --- 7. FUNÇÕES AUXILIARES ---
const formatarData = (dataISO) => {
  const data = new Date(dataISO);
  return data.toLocaleDateString('pt-BR', {
    day: '2-digit',
    month: '2-digit',
    year: 'numeric',
  });
};

// A FUNÇÃO handleLogout() FOI REMOVIDA
</script>
