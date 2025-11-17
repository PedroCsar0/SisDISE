<template>
  <div v-if="isLoading" class="text-center">Carregando...</div>

  <div v-if="user">
    <div class="bg-white shadow rounded-lg p-6 mb-6">
      <div class="flex flex-col sm:flex-row justify-between sm:items-start">
        <div>
          <h1 class="text-3xl font-bold text-gray-900">{{ user.name }}</h1>
          <p class="text-gray-500">{{ user.email }}</p>
          <span class="mt-2 px-3 py-1 inline-flex text-sm font-semibold rounded-full"
            :class="{
              'bg-blue-100 text-blue-800': user.tipo === 'Avaliador',
              'bg-green-100 text-green-800': user.tipo === 'Gestor Empresarial',
              'bg-red-100 text-red-800': user.tipo === 'Administrador'
            }">
            {{ user.tipo }}
          </span>
        </div>
        <div class="mt-4 sm:mt-0">
          <router-link
            :to="`/admin/users/${user.id}/edit`"
            class="px-6 py-2 font-medium text-white bg-green-600 rounded-md hover:bg-green-700"
          >
            Editar Usuário
          </router-link>
        </div>
      </div>
    </div>

    <div v-if="user.tipo === 'Avaliador'" class="bg-white shadow rounded-lg p-6">
      <h2 class="text-xl font-bold text-gray-800 mb-4">Histórico de Atividades</h2>
      <p v-if="!user.diagnosticos || user.diagnosticos.length === 0" class="text-gray-500">Nenhum diagnóstico realizado.</p>
      <ul v-else class="divide-y divide-gray-200">
        <li v-for="diag in user.diagnosticos" :key="diag.id" class="py-4">
          <div class="flex justify-between">
            <div>
              <p class="font-medium text-gray-900">Diagnóstico #{{ diag.id }}</p>
              <p class="text-sm text-gray-600">Empresa: {{ diag.empresa ? diag.empresa.razaoSocial : 'N/A' }}</p>
            </div>
            <div class="text-right">
              <p class="text-sm font-bold" :class="getScoreColor(diag.escoreFinal)">
                Score: {{ diag.escoreFinal.toFixed(0) }}
              </p>
              <p class="text-xs text-gray-500">{{ new Date(diag.dataAnalise).toLocaleDateString() }}</p>
            </div>
          </div>
        </li>
      </ul>
    </div>

    <div v-if="user.tipo === 'Gestor Empresarial'" class="bg-white shadow rounded-lg p-6">
      <h2 class="text-xl font-bold text-gray-800 mb-4">Empresa Vinculada</h2>
      <div v-if="user.empresa" class="border p-4 rounded-md">
        <p class="text-lg font-semibold">{{ user.empresa.razaoSocial }}</p>
        <p class="text-gray-600">CNPJ: {{ user.empresa.cnpj }}</p>
      </div>
      <div v-else class="bg-yellow-50 border border-yellow-200 p-4 rounded-md text-yellow-700">
        Este gestor ainda não está vinculado a nenhuma empresa.
      </div>
    </div>

  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import api from '@/api.js';
import { useUserStore } from '@/stores/userStore'; // Importar o store

const props = defineProps(['id']);
const user = ref(null);
const isLoading = ref(true);
const userStore = useUserStore(); // Usar o store

onMounted(async () => {
  try {
    isLoading.value = true;
    // Garantir que o admin está carregado (para o 'Gate' funcionar no recarregamento)
    if (!userStore.user) {
      await userStore.fetchUser();
    }

    const response = await api.get(`/admin/users/${props.id}`);
    user.value = response.data;
  } catch (error) {
    console.error("Erro ao buscar detalhes do usuário:", error);
  } finally {
    isLoading.value = false;
  }
});

// Função auxiliar para colorir os scores (esta função É usada pelo template)
const getScoreColor = (score) => {
  if (score <= 250) return 'text-red-600';
  if (score <= 500) return 'text-orange-600';
  if (score <= 750) return 'text-yellow-600';
  return 'text-green-600';
};
</script>
