<template>
  <div class="animate-fade-in">
    <div class="flex items-center gap-3 mb-8">
      <div class="p-3 bg-indigo-100 text-primary rounded-xl">
        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
      </div>
      <div>
        <h1 class="text-3xl font-bold text-gray-900">Gestão de Usuários</h1>
        <p class="text-gray-500 text-sm">Administre os acessos e permissões do sistema.</p>
      </div>
    </div>

    <div v-if="errorMessage" class="bg-red-50 border border-red-200 text-red-600 px-4 py-3 rounded-xl mb-6 flex items-center gap-2">
      <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
      {{ errorMessage }}
    </div>

    <div v-if="isLoading" class="flex justify-center py-12">
      <div class="animate-spin rounded-full h-10 w-10 border-b-2 border-primary"></div>
    </div>

    <div v-else class="bg-white shadow-xl rounded-2xl overflow-hidden border border-gray-100">
      <table class="min-w-full divide-y divide-gray-100">

        <thead class="bg-gray-50/50">
          <tr>
            <th class="px-6 py-4 text-left text-xs font-bold text-gray-400 uppercase tracking-wider">Usuário</th>
            <th class="px-6 py-4 text-left text-xs font-bold text-gray-400 uppercase tracking-wider">Função</th>
            <th class="px-6 py-4 text-left text-xs font-bold text-gray-400 uppercase tracking-wider">Data de criação</th>
            <th class="px-6 py-4 text-right text-xs font-bold text-gray-400 uppercase tracking-wider">Ações</th>
          </tr>
        </thead>

        <tbody class="bg-white divide-y divide-gray-100">
          <tr v-for="user in users" :key="user.id" class="hover:bg-indigo-50/30 transition-colors duration-150 group">

            <td class="px-6 py-4 whitespace-nowrap">
              <div class="flex items-center">
                <div class="h-10 w-10 rounded-full bg-gray-100 flex items-center justify-center text-gray-500 font-bold text-lg mr-3">
                  {{ user.name.charAt(0).toUpperCase() }}
                </div>
                <div>
                  <div class="text-sm font-bold text-gray-900">{{ user.name }}</div>
                  <div class="text-sm text-gray-500">{{ user.email }}</div>
                </div>
              </div>
            </td>

            <td class="px-6 py-4 whitespace-nowrap">
              <span class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full border"
                :class="{
                  'bg-blue-50 text-blue-700 border-blue-100': user.tipo === 'Avaliador',
                  'bg-emerald-50 text-emerald-700 border-emerald-100': user.tipo === 'Gestor Empresarial',
                  'bg-purple-50 text-purple-700 border-purple-100': user.tipo === 'Administrador'
                }">
                {{ user.tipo }}
              </span>
            </td>

            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
              {{ new Date(user.created_at).toLocaleDateString('pt-BR') }}
            </td>

            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
              <div class="flex justify-end gap-2 opacity-70 group-hover:opacity-100 transition-opacity">

                <router-link :to="`/admin/users/${user.id}`" class="p-2 text-blue-600 bg-blue-50 rounded-lg hover:bg-blue-100 transition-colors" title="Ver Detalhes">
                  <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                </router-link>

                <router-link :to="`/admin/users/${user.id}/edit`" class="p-2 text-green-600 bg-green-50 rounded-lg hover:bg-green-100 transition-colors" title="Editar">
                  <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                </router-link>

                <button @click="deleteUser(user.id)" class="p-2 text-red-600 bg-red-50 rounded-lg hover:bg-red-100 transition-colors" title="Excluir">
                  <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                </button>
              </div>
            </td>
          </tr>

          <tr v-if="users.length === 0">
            <td colspan="4" class="px-6 py-10 text-center text-gray-500">
              Nenhum usuário encontrado.
            </td>
          </tr>
        </tbody>
      </table>
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
import { useToast } from "vue-toastification";

const users = ref([]);
const isLoading = ref(true);
const errorMessage = ref('');
const toast = useToast();

const fetchUsers = async () => {
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
};

// Função de exclusão na lista
const deleteUser = async (id) => {
  if (!window.confirm('Tem certeza? Esta ação removerá o usuário permanentemente.')) return;

  try {
    await api.delete(`/admin/users/${id}`);
    users.value = users.value.filter(u => u.id !== id);
    toast.success('Usuário excluído.');
  } catch (error) {
    if (error.response && error.response.status === 403) {
       toast.error(error.response.data.message);
    } else {
       toast.error('Erro ao excluir.');
    }
  }
};

onMounted(() => {
  fetchUsers();
});
</script>
