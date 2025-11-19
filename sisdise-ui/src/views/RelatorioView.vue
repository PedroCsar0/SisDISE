<template>
  <div class="max-w-6xl mx-auto pb-12 px-4 sm:px-6">

    <div v-if="isLoading" class="flex flex-col justify-center items-center h-[60vh] animate-pulse">
      <div class="h-16 w-16 bg-indigo-100 rounded-full flex items-center justify-center mb-4">
        <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-primary"></div>
      </div>
      <p class="text-gray-400 font-medium">Gerando análise...</p>
    </div>

    <div v-else-if="diagnostico" class="space-y-8">

      <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-6 animate-slide-down">
        <div>
          <div class="flex items-center gap-3 mb-2">
            <span class="px-3 py-1 rounded-full bg-indigo-50 text-primary text-xs font-bold uppercase tracking-wider border border-indigo-100">
              Relatório #{{ diagnostico.id }}
            </span>
            <span class="text-gray-400 text-sm flex items-center gap-1">
              <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
              {{ formatarData(diagnostico.dataAnalise) }}
            </span>
          </div>
          <h1 class="text-3xl md:text-4xl font-black text-gray-900 tracking-tight leading-tight">
            {{ diagnostico.titulo }}
          </h1>
          <p class="text-lg text-gray-500 mt-1 font-light flex items-center gap-2">
            Empresa: <span class="font-semibold text-gray-700">{{ diagnostico.empresa ? diagnostico.empresa.razaoSocial : 'N/A' }}</span>
          </p>
        </div>

        <button
          @click="handleDownloadPDF"
          :disabled="isDownloading"
          class="flex items-center gap-2 px-6 py-2.5 text-sm font-bold text-white bg-green-600 rounded-xl hover:bg-green-700 shadow-md hover:shadow-lg transition-all transform hover:-translate-y-0.5 disabled:opacity-70 disabled:transform-none"
        >
          <svg v-if="!isDownloading" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
          </svg>
          <svg v-else class="animate-spin w-5 h-5" fill="none" viewBox="0 0 24 24">
            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
          </svg>
          {{ isDownloading ? 'Gerando...' : 'Baixar PDF' }}
        </button>
        </div>

      <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">

        <div class="lg:col-span-5 bg-white rounded-3xl shadow-2xl border border-gray-100 overflow-hidden relative animate-fade-up" style="animation-delay: 0.1s;">
          <div class="absolute top-0 right-0 w-64 h-64 bg-gradient-to-bl from-indigo-50 to-transparent rounded-bl-full -mr-10 -mt-10 opacity-70"></div>

          <div class="p-8 relative z-10 h-full flex flex-col justify-between">
            <div>
              <h2 class="text-sm font-bold text-gray-400 uppercase tracking-widest mb-8">Score de Sustentabilidade</h2>

              <div class="flex items-baseline gap-2 mb-6">
                <span class="text-8xl font-black text-gray-900 tracking-tighter transition-all duration-700 hover:text-primary cursor-default">
                  {{ diagnostico.escoreFinal.toFixed(0) }}
                </span>
                <span class="text-2xl text-gray-300 font-medium">/ 1000</span>
              </div>

              <div class="relative h-4 bg-gray-100 rounded-full overflow-hidden mb-6 shadow-inner">
                <div
                   class="absolute top-0 left-0 h-full transition-all duration-1000 ease-out rounded-full shadow-lg"
                   :class="scoreColorClasses.bg"
                   :style="{ width: scorePercent + '%' }"
                >
                   <div class="absolute inset-0 bg-white/30 w-full h-full animate-shimmer"></div>
                </div>
              </div>

              <div class="inline-block px-4 py-2 rounded-xl border transition-colors duration-300" :class="scoreColorClasses.badge">
                <span class="font-bold text-lg">{{ diagnostico.classificacao }}</span>
              </div>
            </div>

            <div class="mt-10 grid grid-cols-2 gap-4">
              <button
                @click="showModal = true"
                class="flex items-center justify-center gap-2 px-4 py-3 bg-indigo-50 text-primary font-bold rounded-xl hover:bg-indigo-100 transition-colors"
              >
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
                Detalhes
              </button>
              <button
                @click="goToPGES"
                class="flex items-center justify-center gap-2 px-4 py-3 bg-white border-2 border-gray-100 text-gray-700 font-bold rounded-xl hover:border-blue-200 hover:text-blue-600 transition-all"
              >
                Plano de ação
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
              </button>
            </div>
          </div>
        </div>

        <div class="lg:col-span-7 bg-white rounded-3xl shadow-xl border border-gray-100 p-8 animate-fade-up" style="animation-delay: 0.2s;">
          <h2 class="text-lg font-bold text-gray-800 mb-8 flex items-center gap-3">
            <div class="w-10 h-10 rounded-lg bg-gray-50 flex items-center justify-center text-gray-400">
               <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 3.055A9.001 9.001 0 1020.945 13H11V3.055z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.488 9H15V3.512A9.025 9.025 0 0120.488 9z"/></svg>
            </div>
            Performance por Pilar
          </h2>

          <div class="space-y-8">
            <div v-for="(principio, index) in principiosComPercentual" :key="principio.id" class="group">
              <div class="flex justify-between items-end mb-2">
                <span class="font-bold text-gray-700 text-lg flex items-center gap-3">
                   <span class="relative flex h-3 w-3">
                      <span :class="`animate-ping absolute inline-flex h-full w-full rounded-full opacity-75 ${principio.cor.replace('bg-', 'bg-opacity-30 ')}`"></span>
                      <span :class="`relative inline-flex rounded-full h-3 w-3 ${principio.cor}`"></span>
                    </span>
                  {{ principio.nomePrincipio }}
                </span>
                <div class="text-right">
                  <span class="text-2xl font-bold" :class="`text-${principio.cor.replace('bg-', '')}-600`">
                    {{ principio.escoreObtido.toFixed(0) }}
                  </span>
                  <span class="text-sm text-gray-400 font-medium"> / {{ principio.maxScore }}</span>
                </div>
              </div>

              <div class="w-full bg-gray-50 rounded-full h-4 overflow-hidden shadow-inner">
                <div
                   :class="`h-full rounded-full ${principio.cor} transition-all duration-1000 ease-out group-hover:brightness-110`"
                   :style="{ width: principio.percentual + '%', transitionDelay: `${index * 100}ms` }"
                ></div>
              </div>
            </div>
          </div>
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

<style scoped>
@keyframes slideDown { from { opacity: 0; transform: translateY(-20px); } to { opacity: 1; transform: translateY(0); } }
@keyframes fadeUp { from { opacity: 0; transform: translateY(20px); } to { opacity: 1; transform: translateY(0); } }
@keyframes shimmer { 0% { transform: translateX(-100%); } 100% { transform: translateX(100%); } }

.animate-slide-down { animation: slideDown 0.6s ease-out forwards; }
.animate-fade-up { animation: fadeUp 0.8s cubic-bezier(0.16, 1, 0.3, 1) forwards; opacity: 0; }
.animate-shimmer { animation: shimmer 2s infinite; }
</style>

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

const scorePercent = computed(() => {
  if (!diagnostico.value) return 0;
  return (diagnostico.value.escoreFinal / 1000) * 100;
});

const scoreColorClasses = computed(() => {
  if (!diagnostico.value) return { text: 'text-gray-600', bg: 'bg-gray-600', badge: 'bg-gray-100 text-gray-600 border-gray-200' };
  const score = diagnostico.value.escoreFinal;

  if (score <= 500) {
    return { text: 'text-red-600', bg: 'bg-red-500', badge: 'bg-red-50 text-red-700 border-red-100' };
  }
  if (score <= 750) {
    return { text: 'text-yellow-600', bg: 'bg-yellow-500', badge: 'bg-yellow-50 text-yellow-700 border-yellow-100' };
  }
  return { text: 'text-green-600', bg: 'bg-green-500', badge: 'bg-green-50 text-green-700 border-green-100' };
});

const fetchRelatorio = async () => {
  try {
    isLoading.value = true;
    if (!userStore.user) await userStore.fetchUser();
    const response = await api.get(`/diagnosticos/${props.id}`);
    diagnostico.value = response.data;
  } catch (error) {
    console.error('Erro:', error);
    toast.error('Não foi possível carregar o relatório.');
    if (error.response && error.response.status === 401) router.push('/login');
  } finally { isLoading.value = false; }
};

const principiosComPercentual = computed(() => {
  if (!diagnostico.value || !diagnostico.value.principios) return [];
  return diagnostico.value.principios.map(p => {
    const meta = principioMetadados[p.nomePrincipio] || { cor: 'bg-gray-500', maxScore: 1 };
    return { ...p, cor: meta.cor, maxScore: meta.maxScore, percentual: (p.escoreObtido / meta.maxScore) * 100 };
  });
});

const formatarData = (dataISO) => {
  if (!dataISO) return '';
  const dataApenas = dataISO.split('T')[0];
  const partes = dataApenas.split('-');
  if (partes.length !== 3) return dataISO;
  return `${partes[2]}/${partes[1]}/${partes[0]}`;
};

const handleDownloadPDF = async () => {
  isDownloading.value = true;
  try {
    const response = await api.get(`/relatorio/${props.id}/pdf`, { responseType: 'blob' });
    const url = window.URL.createObjectURL(new Blob([response.data], { type: 'application/pdf' }));
    const link = document.createElement('a');
    link.href = url;
    const fileName = `SisDISE_Relatorio_${diagnostico.value.titulo || props.id}.pdf`;
    link.setAttribute('download', fileName);
    document.body.appendChild(link);
    link.click();
    document.body.removeChild(link);
    window.URL.revokeObjectURL(url);
    toast.success("Download iniciado!");
  } catch (error) {
    console.error('Erro PDF:', error);
    toast.error('Erro ao gerar o PDF. Verifique o servidor.');
  } finally { isDownloading.value = false; }
};

const goToPGES = () => router.push(`/relatorio/${props.id}/pges`);

onMounted(() => fetchRelatorio());
</script>
