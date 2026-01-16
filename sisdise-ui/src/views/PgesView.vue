<template>
  <div class="max-w-5xl mx-auto px-4 pb-12 animate-fade-in">

    <div v-if="isLoading" class="max-w-4xl mx-auto space-y-6 py-12">
       <div class="flex justify-between">
          <div class="h-10 bg-gray-200 rounded-lg w-1/3 animate-pulse"></div>
          <div class="h-10 bg-gray-200 rounded-lg w-1/4 animate-pulse"></div>
       </div>
       <div class="h-32 bg-gray-100 rounded-2xl animate-pulse"></div>
       <div class="h-32 bg-gray-100 rounded-2xl animate-pulse" style="animation-delay: 0.1s"></div>
       <div class="h-32 bg-gray-100 rounded-2xl animate-pulse" style="animation-delay: 0.2s"></div>
    </div>

    <div v-else-if="itensParaMelhorar">

      <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-6 mb-10 pt-6">
        <div>
          <div class="flex items-center gap-3 mb-1">
            <div class="p-2.5 bg-red-100 text-red-600 rounded-xl shadow-sm">
               <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"/></svg>
            </div>
            <h1 class="text-4xl font-black text-gray-900 tracking-tight">Plano de Ação</h1>
          </div>
          <p class="text-gray-500 ml-1 text-lg font-medium">Diagnóstico #{{ id }} • Pontos de Atenção</p>
        </div>

        <div class="flex gap-3 w-full md:w-auto">
          <button @click="router.push(`/relatorio/${id}`)" class="flex-1 md:flex-none px-5 py-3 text-sm font-bold text-gray-700 bg-white border border-gray-200 rounded-xl hover:bg-gray-50 hover:border-gray-300 transition-all shadow-sm flex items-center justify-center gap-2">
            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
            Voltar
          </button>
          <button @click="handleDownloadPGES" :disabled="isDownloading" class="flex-1 md:flex-none px-6 py-3 text-sm font-bold text-white bg-gray-900 rounded-xl hover:bg-black disabled:opacity-70 transition-all shadow-lg hover:shadow-xl hover:-translate-y-0.5 flex items-center justify-center gap-2">
            <svg v-if="!isDownloading" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
            <svg v-else class="animate-spin h-5 w-5 text-white" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
            {{ isDownloading ? 'Gerando PDF...' : 'Baixar PDF' }}
          </button>
        </div>
      </div>

      <div class="bg-blue-50 border border-blue-100 p-6 rounded-2xl mb-10 flex gap-4 items-start shadow-sm animate-fade-up">
        <div class="bg-white p-2 rounded-full shadow-sm text-blue-500">
           <svg class="h-6 w-6" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/></svg>
        </div>
        <div>
           <h3 class="text-blue-900 font-bold mb-1">Análise de Prioridades</h3>
           <p class="text-sm text-blue-800 leading-relaxed">
             Com base na metodologia SisDISE, foram identificados os seguintes parâmetros com <span class="font-bold border-b-2 border-blue-300">nota ≤ 2</span>.
             A correção destes itens é fundamental para elevar o nível de sustentabilidade da empresa.
           </p>
        </div>
      </div>

      <div class="space-y-6">
        <TransitionGroup name="list" tag="ul" class="space-y-6">
          <li v-for="(item, index) in itensParaMelhorar" :key="item.id" class="group relative bg-white rounded-2xl shadow-lg border border-gray-100 p-0 overflow-hidden hover:shadow-2xl transition-all duration-300 hover:-translate-y-1" :style="{ transitionDelay: `${index * 100}ms` }">

            <div class="p-6 border-b border-gray-50 flex flex-col sm:flex-row justify-between gap-4 bg-gradient-to-r from-white to-gray-50/50">
               <div class="flex items-start gap-4">
                  <div class="mt-1 h-10 w-10 rounded-lg bg-red-100 text-red-600 flex items-center justify-center font-bold text-sm border border-red-200 shadow-sm">
                     {{ item.nota }}
                  </div>
                  <div>
                     <div class="flex items-center gap-2 mb-1">
                        <span class="text-xs font-bold text-gray-400 uppercase tracking-wider">Parâmetro {{ item.item_parametro.codigo_item }}</span>
                     </div>
                     <h3 class="text-lg font-bold text-gray-900 leading-tight">
                        {{ item.item_parametro.descricao }}
                     </h3>
                  </div>
               </div>
               <div class="flex-shrink-0">
                  <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold bg-red-50 text-red-700 border border-red-100 uppercase tracking-wide">
                     Crítico
                  </span>
               </div>
            </div>

            <div class="p-6 bg-white">
               <div class="flex gap-4">
                  <div class="flex-shrink-0 mt-1">
                     <div class="w-8 h-8 rounded-full bg-green-50 text-green-600 flex items-center justify-center">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                     </div>
                  </div>
                  <div>
                     <p class="text-xs font-bold text-gray-400 uppercase mb-1">Recomendação Técnica</p>
                     <p class="text-gray-700 leading-relaxed text-sm md:text-base">
                       {{ item.item_parametro.recomendacao || 'Recomenda-se a elaboração de um plano de ação corretiva para adequação a este requisito, definindo responsáveis e prazos.' }}
                     </p>
                  </div>
               </div>
            </div>

            <div class="absolute left-0 top-0 bottom-0 w-1.5 bg-red-500"></div>
          </li>
        </TransitionGroup>

        <div v-if="itensParaMelhorar.length === 0" class="text-center py-20 bg-white rounded-3xl shadow-lg border border-gray-100 animate-fade-up">
          <div class="inline-flex items-center justify-center w-24 h-24 rounded-full bg-green-50 mb-6 animate-bounce-slow">
            <svg class="w-12 h-12 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
          </div>
          <h3 class="text-2xl font-bold text-gray-900">Excelente Resultado!</h3>
          <p class="mt-3 text-gray-500 max-w-md mx-auto text-lg">Nenhum item crítico (nota ≤ 2) foi identificado neste diagnóstico. A sua gestão está de parabéns.</p>
        </div>
      </div>

    </div>
  </div>
</template>

<style scoped>
@keyframes fadeUp { from { opacity: 0; transform: translateY(20px); } to { opacity: 1; transform: translateY(0); } }
@keyframes bounceSlow { 0%, 100% { transform: translateY(0); } 50% { transform: translateY(-10px); } }

.animate-fade-in { animation: fadeUp 0.6s ease-out forwards; }
.animate-fade-up { animation: fadeUp 0.8s cubic-bezier(0.16, 1, 0.3, 1) forwards; opacity: 0; }
.animate-bounce-slow { animation: bounceSlow 3s infinite ease-in-out; }

/* Transições de Lista */
.list-enter-active, .list-leave-active { transition: all 0.5s cubic-bezier(0.4, 0, 0.2, 1); }
.list-enter-from, .list-leave-to { opacity: 0; transform: translateY(20px) scale(0.98); }
</style>

<script setup>
import { ref, onMounted } from 'vue';
import { useRouter } from 'vue-router';
import api from '@/api.js';
import { useUserStore } from '@/stores/userStore';
import { useToast } from "vue-toastification";

const props = defineProps({ id: { type: String, required: true } });
const router = useRouter();
const userStore = useUserStore();
const toast = useToast();
const isLoading = ref(true);
const itensParaMelhorar = ref(null);
const isDownloading = ref(false);

const fetchPGES = async () => {
  try {
    isLoading.value = true;
    // O userStore.fetchUser() já é chamado no main.js, mas mal não faz checar
    if (!userStore.user) await userStore.fetchUser();
    
    const response = await api.get(`/diagnosticos/${props.id}/pges`);
    itensParaMelhorar.value = response.data;
  } catch (error) {
    console.error('Erro ao carregar plano:', error);
    // Não precisamos checar 401 aqui, o interceptor já resolveu.
    // Só avisamos se for outro erro (ex: 500 ou 404)
    if (!error.response || error.response.status !== 401) {
       toast.error('Não foi possível carregar o Plano de Ação.');
    }
  } finally { 
    // Pequeno delay para a animação do skeleton não piscar muito rápido
    setTimeout(() => { isLoading.value = false; }, 300); 
  }
};

const handleDownloadPGES = async () => {
  isDownloading.value = true;
  try {
    const response = await api.get(`/diagnosticos/${props.id}/pges/pdf`, { responseType: 'blob' });
    const url = window.URL.createObjectURL(new Blob([response.data], { type: 'application/pdf' }));
    const link = document.createElement('a');
    link.href = url;
    const fileName = `SisDISE_PlanoDeAcao_${props.id}.pdf`;
    link.setAttribute('download', fileName);
    document.body.appendChild(link);
    link.click();
    document.body.removeChild(link);
    window.URL.revokeObjectURL(url);
    toast.success("Download iniciado!");
  } catch (error) {
    console.error(error);
    toast.error("Erro ao gerar PDF.");
  } finally { isDownloading.value = false; }
};

onMounted(() => { fetchPGES(); });
</script>
