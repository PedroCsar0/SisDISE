<template>
  <div class="animate-fade-in max-w-4xl mx-auto">

    <div class="mb-6">
       <button @click="router.push('/admin/users')" class="flex items-center text-gray-500 hover:text-primary transition-colors font-medium">
          <svg class="w-5 h-5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
          Voltar à lista
       </button>
    </div>

    <div v-if="isLoading" class="text-center py-10">Carregando...</div>

    <div v-if="user" class="space-y-6">

      <div class="bg-white shadow-xl rounded-2xl p-8 border border-gray-100 relative overflow-hidden">
        <div class="absolute top-0 right-0 w-64 h-64 bg-indigo-50 rounded-full mix-blend-multiply filter blur-3xl opacity-70 -mr-16 -mt-16"></div>

        <div class="relative z-10 flex flex-col md:flex-row justify-between items-start md:items-center gap-6">
          <div class="flex items-center gap-4">
             <div class="h-20 w-20 rounded-2xl bg-indigo-100 text-primary flex items-center justify-center text-3xl font-bold shadow-inner">
                {{ user.name.charAt(0).toUpperCase() }}
             </div>
             <div>
                <h1 class="text-3xl font-bold text-gray-900">{{ user.name }}</h1>
                <p class="text-gray-500 text-lg">{{ user.email }}</p>
                <div class="mt-3">
                   <span class="px-3 py-1 inline-flex text-sm font-semibold rounded-full border"
                    :class="{
                      'bg-blue-50 text-blue-700 border-blue-100': user.tipo === 'Avaliador',
                      'bg-green-50 text-green-700 border-green-100': user.tipo === 'Gestor Empresarial',
                      'bg-red-50 text-red-700 border-red-100': user.tipo === 'Administrador'
                    }">
                    {{ user.tipo }}
                  </span>
                </div>
             </div>
          </div>

          <router-link
            :to="`/admin/users/${user.id}/edit`"
            class="px-6 py-3 bg-primary text-white font-bold rounded-xl hover:bg-primary-dark shadow-lg hover:shadow-xl transition-all flex items-center gap-2"
          >
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
            Editar Perfil
          </router-link>
        </div>
      </div>

      <div v-if="user.tipo === 'Avaliador'" class="bg-white shadow-xl rounded-2xl p-8 border border-gray-100">
        <h2 class="text-xl font-bold text-gray-800 mb-6 flex items-center gap-2">
           <svg class="w-6 h-6 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"/></svg>
           Histórico de Atividades
        </h2>

        <div v-if="!user.diagnosticos || user.diagnosticos.length === 0" class="p-8 text-center bg-gray-50 rounded-xl border border-dashed border-gray-200">
           <p class="text-gray-400">Nenhum diagnóstico realizado por este avaliador.</p>
        </div>

        <ul v-else class="divide-y divide-gray-100">
          <li v-for="diag in user.diagnosticos" :key="diag.id" class="py-4 flex justify-between items-center">
            <div>
              <p class="font-bold text-gray-900 text-lg">{{ diag.titulo || `Diagnóstico #${diag.id}` }}</p>
              <p class="text-sm text-gray-500 flex gap-2">
                 <span class="font-medium">{{ diag.empresa ? diag.empresa.razaoSocial : 'N/A' }}</span> • {{ new Date(diag.dataAnalise).toLocaleDateString() }}
              </p>
            </div>
            <div class="text-right">
              <span class="text-xl font-bold" :class="getScoreColor(diag.escoreFinal)">
                {{ diag.escoreFinal.toFixed(0) }}
              </span>
              <p class="text-xs text-gray-400 uppercase font-bold">Pontos</p>
            </div>
          </li>
        </ul>
      </div>

      <div v-if="user.tipo === 'Gestor Empresarial'" class="bg-white shadow-xl rounded-2xl p-8 border border-gray-100">
        <h2 class="text-xl font-bold text-gray-800 mb-6 flex items-center gap-2">
           <svg class="w-6 h-6 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
           Empresa Vinculada
        </h2>

        <div v-if="user.empresa" class="p-6 bg-indigo-50 rounded-xl border border-indigo-100 flex items-start gap-4">
           <div class="p-3 bg-white rounded-lg text-primary shadow-sm">
              <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 14v3m4-3v3m4-3v3M3 21h18M3 10h18M3 7l9-4 9 4M4 10h16v11H4V10z"/></svg>
           </div>
           <div>
              <p class="text-lg font-bold text-gray-900">{{ user.empresa.razaoSocial }}</p>
              <p class="text-gray-600">CNPJ: {{ user.empresa.cnpj }}</p>
              <div class="mt-2 flex gap-2">
                 <span class="px-2 py-1 bg-white rounded text-xs font-medium text-gray-500 border border-gray-200">{{ user.empresa.setor || 'Setor N/A' }}</span>
                 <span class="px-2 py-1 bg-white rounded text-xs font-medium text-gray-500 border border-gray-200">{{ user.empresa.cidade }}</span>
              </div>
           </div>
        </div>

        <div v-else class="p-6 bg-yellow-50 border border-yellow-100 rounded-xl text-yellow-800 flex items-center gap-3">
           <svg class="w-6 h-6 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
           <p>Este gestor ainda não está vinculado a nenhuma empresa.</p>
        </div>
      </div>

    </div>
  </div>
</template>

<style scoped>
.animate-fade-in { animation: fadeIn 0.5s ease-out; }
@keyframes fadeIn { from { opacity: 0; transform: translateY(10px); } to { opacity: 1; transform: translateY(0); } }
</style>

<script setup>
import { ref, onMounted } from 'vue';
import api from '@/api.js';
import { useUserStore } from '@/stores/userStore';
import { useRouter } from 'vue-router';

const props = defineProps(['id']);
const router = useRouter();
const user = ref(null);
const isLoading = ref(true);
const userStore = useUserStore();

onMounted(async () => {
  try {
    isLoading.value = true;
    if (!userStore.user) await userStore.fetchUser();
    const response = await api.get(`/admin/users/${props.id}`);
    user.value = response.data;
  } catch (error) {
    console.error("Erro ao buscar detalhes:", error);
  } finally {
    isLoading.value = false;
  }
});

const getScoreColor = (score) => {
  if (score <= 500) return 'text-red-600';
  if (score <= 750) return 'text-yellow-600';
  return 'text-green-600';
};
</script>
