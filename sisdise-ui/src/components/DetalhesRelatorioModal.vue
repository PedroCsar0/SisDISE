<template>
  <div
    @click.self="$emit('close')"
    class="fixed inset-0 z-50 flex items-center justify-center bg-gray-900/70 backdrop-blur-md p-4 transition-all duration-300"
  >
    <div class="bg-white rounded-xl shadow-2xl w-full max-w-6xl max-h-[90vh] overflow-hidden flex flex-col animate-zoom-in ring-1 ring-gray-900/5">

      <header class="flex justify-between items-center px-8 py-6 border-b border-gray-200 bg-gray-50">
        <div class="flex items-center gap-5">
          <div class="h-14 w-14 bg-white rounded-xl flex items-center justify-center text-primary shadow-sm border border-gray-200">
            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
          </div>
          <div>
            <h2 class="text-2xl font-black text-gray-900 tracking-tight">Análise Técnica Detalhada</h2>
            <div class="flex items-center gap-2 mt-1">
              <span class="px-2.5 py-0.5 rounded-md bg-gray-200 text-gray-700 text-xs font-bold uppercase tracking-wide">
                ID #{{ diagnostico.id }}
              </span>
              <span class="text-sm text-gray-500 font-medium">{{ formatarData(diagnostico.dataAnalise) }}</span>
            </div>
          </div>
        </div>
        <button @click="$emit('close')" class="group p-2 rounded-lg hover:bg-gray-200 transition-colors">
          <svg class="w-6 h-6 text-gray-400 group-hover:text-gray-700" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
        </button>
      </header>

      <main class="flex-1 overflow-y-auto custom-scrollbar bg-gray-100 p-8">

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
          <div class="bg-white p-6 rounded-xl border border-gray-200 shadow-sm">
            <p class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-1">Empresa</p>
            <p class="text-lg font-bold text-gray-900 truncate">{{ diagnostico.empresa ? diagnostico.empresa.razaoSocial : 'N/A' }}</p>
          </div>
          <div class="bg-white p-6 rounded-xl border border-gray-200 shadow-sm">
             <p class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-1">Status Geral</p>
             <div class="flex items-center gap-2">
                <div class="h-3 w-3 rounded-full" :class="getStatusDotColor(diagnostico.escoreFinal)"></div>
                <p class="text-lg font-bold text-gray-900">{{ diagnostico.classificacao }}</p>
             </div>
          </div>
          <div class="bg-white p-6 rounded-xl border border-gray-200 shadow-sm flex justify-between items-center">
            <div>
               <p class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-1">Score Total (Sf)</p>
               <div class="flex items-baseline gap-1">
                 <p class="text-4xl font-black text-gray-900">{{ diagnostico.escoreFinal.toFixed(0) }}</p>
                 <span class="text-sm text-gray-400 font-medium">/ 1000</span>
               </div>
            </div>
            
            <div class="h-20 w-20 rounded-full border-[6px] border-gray-100 bg-gray-50 flex items-center justify-center font-bold text-xl text-gray-700 shadow-inner">
               {{ (diagnostico.escoreFinal / 10).toFixed(0) }}%
            </div>
          </div>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
          <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
              <tr>
                <th class="px-6 py-4 text-left text-xs font-bold text-gray-500 uppercase tracking-wider w-[35%]">Parâmetro Técnico</th>
                <th class="px-6 py-4 text-left text-xs font-bold text-gray-500 uppercase tracking-wider w-[30%]">Performance e Progresso</th>
                <th class="px-6 py-4 text-center text-xs font-bold text-gray-500 uppercase tracking-wider w-[10%]">Status</th>
                <th class="px-6 py-4 text-center text-xs font-bold text-gray-500 uppercase tracking-wider w-[10%]">Peso</th>
                <th class="px-6 py-4 text-right text-xs font-bold text-gray-500 uppercase tracking-wider w-[15%]">Escore (Si)</th>
              </tr>
            </thead>

            <tbody class="divide-y divide-gray-100 bg-white">
              <tr v-for="param in parametrosDetalhados" :key="param.id" class="hover:bg-gray-50 transition-colors group">

                <td class="px-6 py-5 align-top">
                  <div class="flex items-start gap-3">
                    <span class="mt-1 flex-shrink-0 px-2 py-1 rounded text-xs font-bold bg-gray-100 text-gray-600 border border-gray-200 font-mono">
                      {{ param.codigo }}
                    </span>
                    <span class="text-sm font-semibold text-gray-900 leading-snug pt-0.5">{{ param.descricao }}</span>
                  </div>
                </td>

                <td class="px-6 py-5 align-middle">
                  <div class="w-full">
                    <div class="flex justify-between text-xs mb-2 font-bold">
                      <span class="text-gray-700">{{ param.nota }} pts</span>
                      <span class="text-gray-400">Meta: {{ param.nmax }}</span>
                    </div>
                    <div class="w-full bg-gray-200 rounded-full h-4 overflow-hidden shadow-inner border border-gray-200/50">
                      <div
                        class="h-full rounded-full transition-all duration-1000 ease-out relative flex items-center justify-end pr-2"
                        :class="getProgressBarClass(param.nota, param.nmax)"
                        :style="{ width: (param.nota / param.nmax * 100) + '%' }"
                      >
                         <span v-if="(param.nota / param.nmax) > 0.2" class="text-[10px] font-bold text-white drop-shadow-md">
                           {{ ((param.nota / param.nmax) * 100).toFixed(0) }}%
                         </span>
                      </div>
                    </div>
                  </div>
                </td>

                <td class="px-6 py-5 text-center align-middle">
                   <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-bold border" :class="getStatusBadgeClass(param.nota, param.nmax)">
                      {{ getStatusLabel(param.nota, param.nmax) }}
                   </span>
                </td>

                <td class="px-6 py-5 text-center align-middle">
                  <div class="flex flex-col items-center justify-center">
                     <span class="text-sm font-bold text-gray-700">x{{ Number(param.peso).toFixed(1) }}</span>
                  </div>
                </td>

                <td class="px-6 py-5 text-right align-middle">
                  <span class="text-2xl font-black tracking-tight" :class="getScoreTextColor(param.escore_obtido, param.nmax, param.peso)">
                    {{ param.escore_obtido.toFixed(0) }}
                  </span>
                </td>
              </tr>
            </tbody>

            <tfoot class="bg-gray-50/80 border-t border-gray-200">
               <tr>
                  <td colspan="4" class="px-8 py-5 text-right text-sm font-bold text-gray-500 uppercase tracking-wider">Escore Final Total (Sf)</td>
                  <td class="px-8 py-5 text-right text-3xl font-black text-gray-900">{{ diagnostico.escoreFinal.toFixed(0) }}</td>
               </tr>
            </tfoot>
          </table>
        </div>
      </main>

      <footer class="p-6 bg-white border-t border-gray-200 flex justify-between items-center">
        <p class="text-xs text-gray-400 italic">Valores calculados conforme metodologia SisDISE.</p>
        <button @click="$emit('close')" class="px-8 py-3 font-bold text-white bg-gray-900 rounded-xl hover:bg-black shadow-lg hover:shadow-xl hover:-translate-y-0.5 transition-all">
          Fechar
        </button>
      </footer>
    </div>
  </div>
</template>

<style scoped>
.animate-zoom-in { animation: zoomIn 0.3s cubic-bezier(0.16, 1, 0.3, 1) forwards; }
@keyframes zoomIn {
  from { opacity: 0; transform: scale(0.95) translateY(10px); }
  to { opacity: 1; transform: scale(1) translateY(0); }
}

/* Scrollbar Profissional */
.custom-scrollbar::-webkit-scrollbar { width: 10px; }
.custom-scrollbar::-webkit-scrollbar-track { background: #f3f4f6; border-left: 1px solid #e5e7eb; }
.custom-scrollbar::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 5px; border: 2px solid #f3f4f6; }
.custom-scrollbar::-webkit-scrollbar-thumb:hover { background: #94a3b8; }
</style>

<script setup>
import { computed } from 'vue';
const props = defineProps({ diagnostico: { type: Object, required: true } });

const metadados = {
  'Proteção dos direitos humanos e trabalho': { codigo: 'Proteção', nmax: 70 },
  'Abusos aos direitos humanos': { codigo: 'Abusos', nmax: 30 },
  'Ações preventivas aos desafios ambientais': { codigo: 'Ações Prev.', nmax: 20 },
  'Iniciativas de responsabilidade ambiental': { codigo: 'Iniciativas', nmax: 25 },
  'Estímulo ao desenvolvimento e a difusão de tecnologias ecologicamente corretas': { codigo: 'Tecnologias', nmax: 30 },
  'Ações de combate a corrupção': { codigo: 'Anticorrupção', nmax: 20 },
};

const parametrosDetalhados = computed(() => {
  if (!props.diagnostico || !props.diagnostico.principios) return [];
  return props.diagnostico.principios.flatMap(principio =>
    principio.parametro_avaliacaos.map(param => {
      const meta = metadados[param.descricao] || { codigo: 'N/A', nmax: 1 };
      return { ...param, codigo: meta.codigo, nmax: meta.nmax };
    })
  );
});

// --- Helpers Visuais Avançados ---

const formatarData = (dataISO) => {
  if (!dataISO) return '';
  const dataApenas = dataISO.split('T')[0];
  const partes = dataApenas.split('-');
  if (partes.length !== 3) return dataISO;
  return `${partes[2]}/${partes[1]}/${partes[0]}`;
};

const getStatusDotColor = (score) => {
  if (score <= 500) return 'bg-red-500';
  if (score <= 750) return 'bg-yellow-500';
  return 'bg-green-500';
};

// Cores das Barras (Gradientes)
const getProgressBarClass = (nota, nmax) => {
  const percent = nota / nmax;
  if (percent < 0.5) return 'bg-gradient-to-r from-red-500 to-red-600';
  if (percent < 0.8) return 'bg-gradient-to-r from-yellow-400 to-yellow-500';
  return 'bg-gradient-to-r from-green-500 to-emerald-600';
};

// Badges de Status
const getStatusBadgeClass = (nota, nmax) => {
  const percent = nota / nmax;
  if (percent < 0.5) return 'bg-red-50 text-red-700 border-red-200';
  if (percent < 0.8) return 'bg-yellow-50 text-yellow-700 border-yellow-200';
  return 'bg-green-50 text-green-700 border-green-200';
};

const getStatusLabel = (nota, nmax) => {
  const percent = nota / nmax;
  if (percent < 0.5) return 'Crítico';
  if (percent < 0.8) return 'Moderado';
  return 'Excelente';
};

const getScoreTextColor = (score, nmax, peso) => {
  const maxPossible = 100 * peso;
  const percent = maxPossible > 0 ? score / maxPossible : 0;
  if (percent < 0.5) return 'text-red-600';
  if (percent < 0.8) return 'text-yellow-600';
  return 'text-green-600';
};
</script>
