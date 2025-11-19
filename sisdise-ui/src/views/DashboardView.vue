<template>
  <div class="animate-fade-in">
    <div v-if="isLoading" class="flex justify-center items-center h-64">
      <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-primary"></div>
    </div>

    <div v-else>
      <div class="mb-8">
        <h1 class="text-4xl font-extrabold text-gray-900 tracking-tight">
          Olá, <span class="text-transparent bg-clip-text bg-gradient-to-r from-primary to-primary-dark">{{ userStore.user ? userStore.user.name.split(' ')[0] : 'Visitante' }}</span>
        </h1>
        <p class="text-gray-600 mt-2 text-lg">Aqui está o resumo das atividades de sustentabilidade.</p>
      </div>

      <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">

        <div class="lg:col-span-2 space-y-8">

          <div
            class="rounded-2xl shadow-xl p-8 relative overflow-hidden border transition-colors duration-300"
            :class="getStatusCardClass(ultimoDiagnostico ? ultimoDiagnostico.escoreFinal : null)"
          >
            <div class="absolute top-0 right-0 -mt-4 -mr-4 w-32 h-32 rounded-full opacity-20 blur-2xl bg-white"></div>

            <h2 class="text-sm font-bold uppercase tracking-wider mb-4 opacity-80" :class="ultimoDiagnostico ? 'text-gray-800' : 'text-gray-400'">
              Status Atual
            </h2>

            <div v-if="ultimoDiagnostico" class="relative z-10">
              <div class="flex items-end gap-3">
                <span class="text-6xl font-black text-gray-900">{{ ultimoDiagnostico.escoreFinal.toFixed(0) }}</span>
                <span class="text-xl text-gray-600 mb-2">/ 1000</span>
              </div>
              <div class="mt-4 flex items-center gap-3">
                <span
                  class="px-4 py-1.5 rounded-full text-sm font-bold shadow-sm border border-black/5"
                  :class="getClassificacaoBgColor(ultimoDiagnostico.escoreFinal)"
                >
                  {{ ultimoDiagnostico.classificacao }}
                </span>
                <span class="text-sm text-gray-600 font-medium">
                  Em {{ formatarData(ultimoDiagnostico.dataAnalise) }}
                </span>
              </div>
            </div>

            <div v-else class="relative z-10 py-6">
               <p class="text-xl text-gray-600">Nenhum diagnóstico recente.</p>
               <p class="text-gray-500 text-sm mt-1">Inicie uma nova avaliação para ver o seu score.</p>
            </div>
          </div>

          <div
             v-if="userStore.user && userStore.user.tipo !== 'Gestor Empresarial'"
             class="grid grid-cols-1 sm:grid-cols-3 gap-4"
          >
            <router-link
              to="/diagnostico/novo"
              class="group relative flex flex-col items-center justify-center p-6 rounded-2xl shadow-lg hover:shadow-2xl transition-all duration-300 border overflow-hidden bg-white border-gray-200 hover:border-yellow-400"
            >
              <div class="absolute inset-0 bg-yellow-50 opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
              <div class="relative z-10 flex flex-col items-center">
                <div class="p-3 rounded-full bg-yellow-100 text-yellow-600 mb-3 group-hover:scale-110 transition-transform shadow-sm">
                  <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path v-if="hasDraft" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z" />
                    <path v-if="hasDraft" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    <path v-else stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                  </svg>
                </div>
                <h3 class="text-base font-bold text-gray-900 mb-1 group-hover:text-yellow-800 transition-colors text-center leading-tight">
                  {{ hasDraft ? 'Continuar' : 'Novo Diagnóstico' }}
                </h3>
                <p class="text-sm text-yellow-700 opacity-80 font-medium">
                  {{ hasDraft ? 'Retomar rascunho' : 'Iniciar questionário' }}
                </p>
              </div>
            </router-link>

            <router-link
              to="/empresas"
              class="group relative flex flex-col items-center justify-center p-6 rounded-2xl shadow-lg hover:shadow-2xl transition-all duration-300 border overflow-hidden bg-white border-gray-200 hover:border-blue-400"
            >
              <div class="absolute inset-0 bg-blue-50 opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
              <div class="relative z-10 flex flex-col items-center">
                <div class="p-3 rounded-full bg-blue-100 text-blue-600 mb-3 group-hover:scale-110 transition-transform shadow-sm">
                  <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                  </svg>
                </div>
                <h3 class="text-base font-bold text-gray-900 mb-1 group-hover:text-blue-800 transition-colors text-center">Empresas</h3>
              </div>
            </router-link>

            <router-link
              v-if="userStore.user.tipo === 'Administrador'"
              to="/admin/users"
              class="group relative flex flex-col items-center justify-center p-6 rounded-2xl shadow-lg hover:shadow-2xl transition-all duration-300 border overflow-hidden bg-white border-gray-200 hover:border-red-400"
            >
              <div class="absolute inset-0 bg-red-50 opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
              <div class="relative z-10 flex flex-col items-center">
                <div class="p-3 rounded-full bg-red-100 text-red-600 mb-3 group-hover:scale-110 transition-transform shadow-sm">
                  <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                  </svg>
                </div>
                <h3 class="text-base font-bold text-gray-900 mb-1 group-hover:text-red-800 transition-colors text-center">Usuários</h3>
              </div>
            </router-link>
          </div>

        </div> <div class="bg-white p-6 rounded-2xl shadow-xl flex flex-col border border-gray-100">

          <div class="mb-6">
            <h2 class="text-sm font-bold text-gray-400 uppercase tracking-wider mb-3">Evolução Histórica</h2>

            <div v-if="availableCompanies.length > 0" class="relative w-full">
              <select
                v-model="selectedCompanyId"
                class="w-full appearance-none bg-gray-50 border border-gray-200 text-gray-700 text-sm font-bold py-2.5 pl-4 pr-10 rounded-xl focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary cursor-pointer hover:bg-gray-100 transition-colors shadow-sm"
              >
                <option v-for="company in availableCompanies" :key="company.id" :value="company.id">
                  {{ company.name }}
                </option>
              </select>
              <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-3 text-gray-500">
                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" /></svg>
              </div>
            </div>
            <div v-else class="text-xs text-gray-400 italic bg-gray-50 p-2 rounded text-center">
              Sem dados de empresas disponíveis
            </div>
          </div>
          <div v-if="filteredDiagnostics.length > 0" class="grid grid-cols-2 gap-4 mb-6">
            <div class="bg-gray-50 p-3 rounded-xl border border-gray-100">
              <p class="text-xs text-gray-500 uppercase font-semibold">Melhor Score</p>
              <p class="text-lg font-bold text-gray-900">{{ metrics.best }}</p>
            </div>
            <div class="bg-gray-50 p-3 rounded-xl border border-gray-100">
              <p class="text-xs text-gray-500 uppercase font-semibold">Variação</p>
              <div class="flex items-center">
                <span class="text-lg font-bold" :class="metrics.trend >= 0 ? 'text-green-600' : 'text-red-600'">
                  {{ metrics.trend > 0 ? '+' : '' }}{{ metrics.trend }}
                </span>
                <span class="text-xs text-gray-500 ml-1">pts</span>
              </div>
            </div>
          </div>

          <div class="flex-grow min-h-[250px]">
             <div v-if="filteredDiagnostics.length > 0" class="h-full">
                <LineChart :chartData="chartData" :chartOptions="chartOptions" />
             </div>
             <div v-else class="h-full flex flex-col items-center justify-center text-center">
                <div class="bg-gray-100 p-4 rounded-full mb-3">
                   <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 12l3-3 3 3 4-4M8 21l4-4 4 4M3 4h18M4 4h16v12a1 1 0 01-1 1H5a1 1 0 01-1-1V4z"></path></svg>
                </div>
                <p class="text-sm text-gray-500">Selecione uma empresa com histórico.</p>
             </div>
          </div>
        </div>

        <div class="lg:col-span-3 bg-white rounded-2xl shadow-xl overflow-hidden border border-gray-100">
          <div class="p-6 border-b border-gray-100 bg-gray-50/50">
            <h2 class="text-lg font-bold text-gray-900">Histórico de Relatórios</h2>
          </div>

          <ul v-if="diagnosticos.length > 0" class="divide-y divide-gray-100">
            <li
              v-for="diag in diagnosticos"
              :key="diag.id"
              @click="goToRelatorio(diag.id)"
              class="p-6 hover:bg-indigo-50/50 transition-colors cursor-pointer flex flex-col sm:flex-row justify-between sm:items-center group"
            >
              <div class="flex items-center gap-4">
                <div class="h-10 w-10 rounded-full flex items-center justify-center text-white font-bold shadow-md" :class="getClassificacaoBgColor(diag.escoreFinal)">
                  {{ diag.escoreFinal.toFixed(0)[0] }}
                </div>
                <div>
                  <p class="font-bold text-gray-900 text-lg group-hover:text-primary transition-colors">{{ diag.titulo }}</p>
                  <p class="text-sm text-gray-500">
                    {{ formatarData(diag.dataAnalise) }}
                    <span v-if="userStore.user && userStore.user.tipo !== 'Gestor Empresarial'" class="mx-1">•</span>
                    <span v-if="userStore.user && userStore.user.tipo !== 'Gestor Empresarial'">{{ diag.empresa ? diag.empresa.razaoSocial : 'N/A' }}</span>
                  </p>
                </div>
              </div>

              <div class="mt-4 sm:mt-0 flex items-center gap-6">
                <div class="text-right">
                  <p class="text-2xl font-bold text-gray-900">{{ diag.escoreFinal.toFixed(0) }}</p>
                  <p class="text-xs text-gray-500 uppercase font-semibold">Pontos</p>
                </div>

                <button
                   v-if="userStore.user && (userStore.user.tipo === 'Administrador' || userStore.user.id === diag.user_id)"
                   @click.stop="handleDeleteDiagnostico(diag.id)"
                   class="p-3 text-gray-300 hover:text-red-500 hover:bg-red-50 rounded-xl transition-all shadow-sm hover:shadow-md border border-transparent hover:border-red-100"
                   title="Excluir Diagnóstico"
                >
                  <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                  </svg>
                </button>
              </div>
            </li>
          </ul>
          <div v-else class="p-10 text-center text-gray-500">
             O seu histórico aparecerá aqui.
          </div>
        </div>

      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted, computed, watch } from 'vue';
import { useRouter } from 'vue-router';
import api from '@/api.js';
import { useUserStore } from '@/stores/userStore';
import { useToast } from "vue-toastification";
import LineChart from '@/components/LineChart.vue';

const diagnosticos = ref([]);
const isLoading = ref(true);
const router = useRouter();
const userStore = useUserStore();
const toast = useToast();
const hasDraft = ref(false);

const selectedCompanyId = ref(null);

// --- FUNÇÃO PARA VERIFICAR RASCUNHO (Crucial estar aqui) ---
const checkDraft = () => {
  if (userStore.user && userStore.user.id) {
    // Chave precisa ser consistente
    const draftKey = `sisdise_draft_user_${userStore.user.id}`;
    const savedDraft = localStorage.getItem(draftKey);
    hasDraft.value = !!savedDraft;
  } else {
    hasDraft.value = false;
  }
};

onMounted(async () => {
  isLoading.value = true;

  try {
    if (!userStore.user) {
      await userStore.fetchUser();
    }

    // Verifica rascunho assim que temos o utilizador
    checkDraft();

    const diagnosticosResponse = await api.get('/diagnosticos');
    diagnosticos.value = diagnosticosResponse.data;
  } catch (error) {
    console.error('Erro ao buscar dados:', error);
    if (error.response && error.response.status === 401) {
      userStore.clearUser();
      router.push('/login');
    }
  } finally {
    isLoading.value = false;
  }
});

// Watcher para garantir que se o user mudar (logout/login rápido), o botão atualiza
watch(() => userStore.user, () => {
  checkDraft();
});

const availableCompanies = computed(() => {
  const companiesMap = new Map();
  diagnosticos.value.forEach(d => {
    if (d.empresa) {
      companiesMap.set(d.empresa.id, d.empresa.razaoSocial);
    }
  });
  return Array.from(companiesMap, ([id, name]) => ({ id, name }));
});

watch(availableCompanies, (newCompanies) => {
  if (newCompanies.length > 0 && !selectedCompanyId.value) {
    selectedCompanyId.value = newCompanies[0].id;
  }
});

const filteredDiagnostics = computed(() => {
  if (!selectedCompanyId.value) return [];
  return diagnosticos.value.filter(d => d.empresa && d.empresa.id === selectedCompanyId.value);
});

const ultimoDiagnostico = computed(() => {
  if (diagnosticos.value && diagnosticos.value.length > 0) {
    return diagnosticos.value[0];
  }
  return null;
});

const getStatusCardClass = (score) => {
  if (score === null || score === undefined) return 'bg-white border-gray-200';
  if (score <= 500) return 'bg-red-50 border-red-200';
  if (score <= 750) return 'bg-yellow-50 border-yellow-200';
  return 'bg-green-50 border-green-200';
};

const chartOptions = ref({
  responsive: true,
  maintainAspectRatio: false,
  plugins: {
    legend: { display: false },
    tooltip: {
      callbacks: {
        label: function(context) {
          return 'Score: ' + context.parsed.y.toFixed(0);
        }
      }
    }
  },
  scales: {
    y: { beginAtZero: false, suggestedMin: 300, suggestedMax: 1000, grid: { display: false } },
    x: { grid: { display: false } }
  },
  elements: { line: { tension: 0.4 } }
});

const chartData = computed(() => {
  const data = filteredDiagnostics.value;
  if (data.length === 0) {
    return { labels: [], datasets: [] };
  }
  const cronologicos = [...data].reverse();
  return {
    labels: cronologicos.map(d => formatarData(d.dataAnalise)),
    datasets: [{
      label: 'Score',
      data: cronologicos.map(d => d.escoreFinal),
      borderColor: '#4f46e5',
      backgroundColor: 'rgba(79, 70, 229, 0.1)',
      fill: true
    }]
  };
});

const handleDeleteDiagnostico = async (id) => {
  if (!window.confirm('Tem certeza que deseja realizar a exclusão?')) return;
  try {
    await api.delete(`/diagnosticos/${id}`);
    diagnosticos.value = diagnosticos.value.filter(d => d.id !== id);
    toast.success('Excluído com sucesso.');
  } catch (error) {
    console.error('Erro ao excluir:', error);
    toast.error('Erro ao excluir.');
  }
};

const goToRelatorio = (id) => router.push(`/relatorio/${id}`);

const formatarData = (dataISO) => {
  if (!dataISO) return '';
  const dataApenas = dataISO.split('T')[0];
  const partes = dataApenas.split('-');
  if (partes.length !== 3) return dataISO;
  return `${partes[2]}/${partes[1]}/${partes[0]}`;
};

const getClassificacaoBgColor = (score) => {
  if (score <= 500) return 'bg-red-500 text-white';
  if (score <= 750) return 'bg-yellow-500 text-white';
  return 'bg-green-500 text-white';
};

const metrics = computed(() => {
  const data = filteredDiagnostics.value;
  if (data.length === 0) return { best: 0, trend: 0 };
  const scores = data.map(d => d.escoreFinal);
  const best = Math.max(...scores).toFixed(0);
  let trend = 0;
  if (data.length >= 2) {
    const current = data[0].escoreFinal;
    const previous = data[1].escoreFinal;
    trend = (current - previous).toFixed(0);
  }
  return { best, trend };
});
</script>
