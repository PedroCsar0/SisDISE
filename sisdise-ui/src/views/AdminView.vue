<template>
  <div class="max-w-5xl mx-auto animate-fade-in">
    <div class="flex items-center gap-3 mb-8">
      <div class="p-2 bg-red-100 text-red-600 rounded-lg">
        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
      </div>
      <h1 class="text-3xl font-bold text-gray-900">Configuração de Parâmetros</h1>
    </div>

    <div v-if="isLoading" class="text-center text-gray-500 py-10">Carregando...</div>

    <div v-else class="space-y-8">
      <div v-for="grupo in grupos" :key="grupo.id" class="bg-white rounded-2xl shadow-lg overflow-hidden border border-gray-100">
        <div class="bg-gray-50 p-6 border-b border-gray-100 flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
          <h2 class="text-xl font-bold text-indigo-900 flex items-center gap-2">
            <span class="bg-indigo-200 text-indigo-800 px-2 py-1 rounded text-sm">{{ grupo.codigo }}</span>
            {{ grupo.nome }}
          </h2>
        </div>

        <div class="p-6">
          <form @submit.prevent="updateGrupo(grupo)" class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-8 bg-indigo-50/50 p-4 rounded-xl border border-indigo-100">
            <div class="md:col-span-2">
               <label class="text-xs font-bold text-gray-500 uppercase mb-1 block">Nome do Grupo</label>
               <input v-model="grupo.nome" class="input-style-premium" />
            </div>
            <div>
               <label class="text-xs font-bold text-gray-500 uppercase mb-1 block">Peso (pi)</label>
               <input v-model.number="grupo.peso" type="number" step="0.1" class="input-style-premium" />
            </div>
            <div class="flex items-end">
               <button type="submit" class="w-full py-3 bg-indigo-600 text-white font-bold rounded-xl hover:bg-indigo-700 transition-all">Salvar Grupo</button>
            </div>
          </form>

          <div class="space-y-3">
            <h3 class="text-sm font-bold text-gray-400 uppercase tracking-wider mb-2">Parâmetros de Avaliação</h3>
            <div v-for="item in grupo.item_parametros" :key="item.id" class="group bg-white border border-gray-200 rounded-xl p-4 hover:shadow-md transition-all">
              <form @submit.prevent="updateParametro(item)" class="flex flex-col md:flex-row gap-4 items-center">
                 <div class="w-full md:w-20">
                    <input v-model="item.codigo_item" class="input-style-premium text-center font-bold text-gray-700 bg-gray-50" />
                 </div>
                 <div class="flex-grow w-full">
                    <input v-model="item.descricao" class="input-style-premium" />
                 </div>
                 <button type="submit" class="p-3 bg-green-100 text-green-600 rounded-lg hover:bg-green-200 transition-colors opacity-50 group-hover:opacity-100">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                 </button>
              </form>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<style scoped>
.input-style-premium {
  width: 100%; padding: 10px 14px; border: 1px solid #e5e7eb; border-radius: 10px; transition: all 0.2s;
}
.input-style-premium:focus {
  outline: none; border-color: #4f46e5; box-shadow: 0 0 0 3px rgba(79, 70, 229, 0.1);
}
.animate-fade-in { animation: fadeIn 0.5s ease-out; }
@keyframes fadeIn { from { opacity: 0; transform: translateY(10px); } to { opacity: 1; transform: translateY(0); } }
</style>

<script setup>
import { ref, onMounted } from 'vue';
import api from '@/api.js';
import { useUserStore } from '@/stores/userStore';
import { useToast } from "vue-toastification";

// (Router removido pois não era usado aqui)
const userStore = useUserStore();
const toast = useToast();
const grupos = ref([]);
const isLoading = ref(true);

const fetchParametros = async () => {
  try {
    isLoading.value = true;
    if (!userStore.user) await userStore.fetchUser();
    const response = await api.get('/admin/parametros');
    grupos.value = response.data;
  } catch (error) {
    // Adicionado uso do 'error'
    console.error(error);
    toast.error('Erro ao carregar.');
  } finally { isLoading.value = false; }
};

const updateGrupo = async (grupo) => {
  try {
    await api.put(`/admin/grupos/${grupo.id}`, grupo);
    toast.success('Grupo salvo!');
  } catch (error) {
    // Adicionado uso do 'error'
    console.error(error);
    toast.error('Erro ao salvar.');
  }
};

const updateParametro = async (item) => {
  try {
    await api.put(`/admin/parametros/${item.id}`, item);
    toast.success('Parâmetro salvo!');
  } catch (error) {
    // Adicionado uso do 'error'
    console.error(error);
    toast.error('Erro ao salvar.');
  }
};

onMounted(() => { fetchParametros(); });
</script>
