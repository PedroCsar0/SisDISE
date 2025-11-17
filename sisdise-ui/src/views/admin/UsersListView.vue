<template>
  <div>
    <h1 class="text-3xl font-bold text-gray-900 mb-6">Gestão de Usuários</h1>

    <div v-if="errorMessage" class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-md mb-6">
      {{ errorMessage }}
    </div>

    <div v-if="isLoading" class="text-center text-gray-500">
      Carregando lista de usuários...
    </div>

    <div v-else class="bg-white shadow-md rounded-lg overflow-hidden">
      <table class="min-w-full divide-y divide-gray-200">

        <thead class="bg-gray-50">
          <tr>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nome</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tipo</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Data Cadastro</th>
            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Ações</th>
          </tr>
        </thead>

        <tbody class="bg-white divide-y divide-gray-200">

          <tr v-for="user in users" :key="user.id" class="hover:bg-gray-50">

            <td class="px-6 py-4 whitespace-nowrap">
              <div class="text-sm font-medium text-gray-900">{{ user.name }}</div>
              <div class="text-sm text-gray-500">{{ user.email }}</div>
            </td>

            <td class="px-6 py-4 whitespace-nowrap">
              <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full"
                :class="{
                  'bg-blue-100 text-blue-800': user.tipo === 'Avaliador',
                  'bg-green-100 text-green-800': user.tipo === 'Gestor Empresarial',
                  'bg-red-100 text-red-800': user.tipo === 'Administrador'
                }">
                {{ user.tipo }}
              </span>
            </td>

            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
              {{ new Date(user.created_at).toLocaleDateString() }}
            </td>

            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium space-x-2">
              <router-link
                :to="`/admin/users/${user.id}`"
                class="px-3 py-1 text-sm font-medium text-white bg-blue-600 rounded-md hover:bg-blue-700"
              >
                Ver Detalhes
              </router-link>
              <router-link
                :to="`/admin/users/${user.id}/edit`"
                class="px-3 py-1 text-sm font-medium text-white bg-green-600 rounded-md hover:bg-green-700"
              >
                Editar
              </router-link>
            </td>
          </tr>
          <tr v-if="users.length === 0">
            <td colspan="4" class="px-6 py-4 text-center text-gray-500">
              Nenhum usuário encontrado.
            </td>
          </tr>
        </tbody>
      </table>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import api from '@/api.js';

const users = ref([]);
const isLoading = ref(true);
const errorMessage = ref('');

onMounted(async () => {
  try {
    isLoading.value = true;
    errorMessage.value = '';
    const response = await api.get('/admin/users');
    users.value = response.data;
  } catch (error) {
    console.error("Erro ao buscar usuários:", error);
    errorMessage.value = "Não foi possível carregar a lista de usuários.";
  } finally {
    isLoading.value = false;
  }
});
</script>
