<template>
  <div class="max-w-5xl mx-auto animate-fade-in">

    <div v-if="isLoading" class="text-center py-20">
      <div class="animate-spin rounded-full h-16 w-16 border-t-4 border-b-4 border-primary mx-auto mb-6"></div>
      <p class="text-xl text-gray-500 font-medium">Preparando o questionário...</p>
    </div>

    <div v-else class="bg-white rounded-3xl shadow-2xl overflow-hidden border border-gray-100">

      <div class="relative h-3 bg-gray-100 w-full">
        <div
          class="h-full bg-gradient-to-r from-primary to-indigo-400 transition-all duration-700 ease-in-out shadow-[0_0_10px_rgba(79,70,229,0.5)]"
          :style="{ width: progressoPercentual + '%' }"
        ></div>
      </div>

      <div class="p-8 md:p-12">
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-10 gap-4">
          <div>
            <h1 class="text-3xl md:text-4xl font-extrabold text-gray-900 tracking-tight">Novo Diagnóstico</h1>
            <p class="text-gray-500 mt-2 text-lg">Avalie os parâmetros de sustentabilidade abaixo.</p>
          </div>

          <button
            v-if="hasDraft"
            @click="clearDraft"
            class="group flex items-center gap-2 px-4 py-2 text-sm font-semibold text-red-600 bg-red-50 hover:bg-red-100 rounded-xl transition-all"
          >
            <svg class="w-4 h-4 group-hover:rotate-90 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
            Descartar Rascunho
          </button>
        </div>

        <form @submit.prevent="handleSubmit" class="space-y-10">

          <div v-if="currentStep === 0" class="space-y-8 animate-fade-in">

            <div class="bg-indigo-50/50 p-8 rounded-2xl border border-indigo-100">
              <h2 class="text-2xl font-bold text-indigo-900 mb-6 flex items-center gap-3">
                <span class="bg-indigo-600 text-white w-8 h-8 rounded-full flex items-center justify-center text-sm font-bold shadow-lg">1</span>
                Informações Iniciais
              </h2>

              <div class="grid gap-6">
                <div>
                  <label class="block text-sm font-bold text-gray-700 mb-2 uppercase tracking-wide">Empresa</label>
                  <div class="relative">
                    <select v-model="selectedEmpresa" class="input-style-premium appearance-none cursor-pointer">
                      <option :value="null" disabled>Selecione a empresa...</option>
                      <option v-for="empresa in empresas" :key="empresa.id" :value="empresa.id">
                        {{ empresa.razaoSocial }}
                      </option>
                    </select>
                    <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-4 text-gray-500">
                      <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                    </div>
                  </div>
                  <p v-if="empresas.length === 0" class="text-sm text-red-500 mt-2 font-medium">
                    Nenhuma empresa cadastrada. <router-link to="/empresas" class="underline hover:text-red-700">Cadastre uma primeiro.</router-link>
                  </p>
                </div>

                <div>
                  <label class="block text-sm font-bold text-gray-700 mb-2 uppercase tracking-wide">Título do Relatório</label>
                  <input v-model="tituloDiagnostico" type="text" placeholder="Ex: Avaliação Anual 2024 - Matriz" class="input-style-premium" />
                </div>
              </div>
            </div>
          </div>

          <div v-for="(grupo, index) in grupos" :key="grupo.id">
            <div v-if="currentStep === (index + 1)" class="animate-fade-in">

              <div class="mb-8 pb-4 border-b border-gray-200 flex items-center gap-4">
                <div class="bg-indigo-100 text-primary font-bold text-xl w-12 h-12 rounded-2xl flex items-center justify-center shadow-sm">
                  {{ grupo.codigo }}
                </div>
                <h2 class="text-2xl font-bold text-gray-900">
                  {{ grupo.nome }}
                </h2>
              </div>

              <div class="space-y-6">
                <div
                  v-for="item in grupo.item_parametros"
                  :key="item.id"
                  class="bg-white p-6 rounded-2xl border-2 transition-all duration-300 relative overflow-hidden group"
                  :class="respostas[item.id] !== null ? 'border-indigo-500 shadow-md' : 'border-gray-100 hover:border-indigo-200 hover:shadow-lg'"
                >
                  <div class="absolute left-0 top-0 bottom-0 w-1.5 transition-colors duration-300" :class="respostas[item.id] !== null ? 'bg-indigo-500' : 'bg-gray-100 group-hover:bg-indigo-200'"></div>

                  <div class="pl-4">
                    <p class="text-lg font-medium text-gray-900 mb-4 leading-relaxed">
                      <span class="text-indigo-600 font-bold mr-2">{{ item.codigo_item }}</span>
                      {{ item.descricao }}
                    </p>

                    <div class="relative">
                      <select v-model="respostas[item.id]" class="input-style-premium appearance-none cursor-pointer bg-gray-50 focus:bg-white">
                        <option :value="null" disabled>Selecione uma classificação...</option>
                        <option v-for="option in notaOptions" :key="option.value" :value="option.value">
                          {{ option.text }}
                        </option>
                      </select>
                       <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-4 text-gray-500">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                      </div>
                    </div>

                    <div v-if="respostas[item.id] !== null" class="mt-4 flex gap-3 items-start animate-fade-in">
                       <div class="mt-0.5 text-indigo-500">
                          <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                       </div>
                       <div class="text-sm text-indigo-800 font-medium bg-indigo-50 px-3 py-2 rounded-lg flex-grow">
                          {{ getHint(respostas[item.id]) }}
                       </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <div class="flex flex-col-reverse sm:flex-row justify-between items-center pt-8 border-t border-gray-100 gap-4">

            <button
              type="button"
              @click="prevStep"
              :disabled="currentStep === 0"
              class="w-full sm:w-auto px-6 py-3 text-gray-500 font-bold hover:text-gray-800 hover:bg-gray-100 rounded-xl disabled:opacity-0 transition-all flex items-center justify-center gap-2"
            >
              <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
              Voltar
            </button>

            <div class="flex gap-3 w-full sm:w-auto">
               <button
                type="button"
                @click="handleSaveAndExit"
                class="flex-1 sm:flex-none px-6 py-3 bg-white border-2 border-gray-200 text-gray-600 font-bold rounded-xl hover:border-gray-400 hover:text-gray-800 transition-all flex items-center justify-center gap-2"
              >
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4"/></svg>
                Salvar e Sair
              </button>

              <button
                v-if="currentStep < totalSteps"
                type="button"
                @click="nextStep"
                class="flex-1 sm:flex-none px-8 py-3 bg-primary text-white font-bold rounded-xl hover:bg-primary-dark shadow-lg hover:shadow-indigo-500/30 hover:-translate-y-0.5 transition-all flex items-center justify-center gap-2"
              >
                Próximo
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
              </button>

              <button
                v-if="currentStep === totalSteps"
                type="submit"
                class="flex-1 sm:flex-none px-8 py-3 bg-green-600 text-white font-bold rounded-xl hover:bg-green-700 shadow-lg hover:shadow-green-500/30 hover:-translate-y-0.5 transition-all flex items-center justify-center gap-2"
                :disabled="isSubmitting"
              >
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                {{ isSubmitting ? 'Calculando...' : 'Finalizar Diagnóstico' }}
              </button>
            </div>
          </div>

        </form>
      </div>
    </div>
  </div>
</template>

<style scoped>
.input-style-premium {
  width: 100%;
  padding: 14px 20px;
  border: 2px solid #f3f4f6; /* Border mais suave */
  border-radius: 12px;
  background-color: #fff;
  font-size: 1rem;
  color: #1f2937;
  transition: all 0.2s ease;
}
.input-style-premium:focus {
  outline: none;
  border-color: #4f46e5;
  background-color: #fff;
  box-shadow: 0 0 0 4px rgba(79, 70, 229, 0.1);
}
/* Animações de entrada */
.animate-fade-in {
  animation: fadeIn 0.5s cubic-bezier(0.4, 0, 0.2, 1) forwards;
}
@keyframes fadeIn {
  from { opacity: 0; transform: translateY(15px); }
  to { opacity: 1; transform: translateY(0); }
}
</style>

<script setup>
import { ref, onMounted, computed, watch } from 'vue';
import { useRouter } from 'vue-router';
import api from '@/api.js';
import { useUserStore } from '@/stores/userStore';
import { useToast } from "vue-toastification";

const notaOptions = [
  { value: null, text: 'Selecione uma classificação...' },
  { value: 0, text: '0 - Não cumpre (Ruim)' },
  { value: 1, text: '1 - Cumpre parcialmente (Razoável)' },
  { value: 2, text: '2 - Cumpre a lei (Mediano)' },
  { value: 3, text: '3 - Ações informais (Bom)' },
  { value: 4, text: '4 - Políticas oficializadas (Ótimo)' },
  { value: 5, text: '5 - Monitora e divulga (Excelente)' }
];

const getHint = (nota) => {
  if (nota === null) return '';
  const option = notaOptions.find(o => o.value === nota);
  const hintMatch = option ? option.text.match(/\((.*?)\)/) : null;
  return hintMatch ? hintMatch[1] : '';
};

const currentStep = ref(0);
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

const totalSteps = computed(() => grupos.value.length);

const progressoPercentual = computed(() => {
  if (totalSteps.value === 0) return 0;
  return (currentStep.value / totalSteps.value) * 100;
});

const getDraftKey = () => {
  if (userStore.user && userStore.user.id) {
    return `sisdise_draft_user_${userStore.user.id}`;
  }
  return null;
};

// Watcher para Auto-Save
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

// --- (NOVO) Função Salvar e Sair ---
const handleSaveAndExit = () => {
  // Apenas redireciona, pois o 'watch' já salvou automaticamente no localStorage
  toast.success("Rascunho salvo com sucesso! Você pode continuar depois.");
  router.push('/dashboard');
};
// --- Fim ---

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
    toast.error('Não foi possível carregar os dados.');
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
      toast.error('Erro: Por favor, selecione uma empresa.');
      return;
    }
    if (!tituloDiagnostico.value.trim()) {
      toast.error('Erro: Por favor, insira um título.');
      return;
    }
  }
  if (currentStep.value > 0 && currentStep.value <= totalSteps.value) {
    const grupoAtual = grupos.value[currentStep.value - 1];
    const todasRespondidas = grupoAtual.item_parametros.every(item => {
      return respostas.value[item.id] !== null;
    });
    if (!todasRespondidas) {
      toast.error(`Erro: Responda todas as perguntas do grupo "${grupoAtual.codigo}".`);
      return;
    }
  }
  if (currentStep.value < totalSteps.value) {
    currentStep.value++;
    window.scrollTo({ top: 0, behavior: 'smooth' });
  }
};

const prevStep = () => {
  if (currentStep.value > 0) {
    currentStep.value--;
    window.scrollTo({ top: 0, behavior: 'smooth' });
  }
};

const handleSubmit = async () => {
  isSubmitting.value = true;

  const todasRespondidas = Object.values(respostas.value).every(nota => nota !== null);
  if (!todasRespondidas || !selectedEmpresa.value || !tituloDiagnostico.value.trim()) {
    toast.error('Erro: Preencha todos os campos obrigatórios.');
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

    toast.success("Diagnóstico salvo com sucesso!");

    const key = getDraftKey();
    if (key) localStorage.removeItem(key);
    hasDraft.value = false;

    router.push(`/relatorio/${diagnosticoSalvo.id}`);

  } catch (error) {
    console.error('Erro ao salvar diagnóstico:', error);
    toast.error('Ocorreu um erro ao salvar.');
    isSubmitting.value = false;
  }
};
</script>
