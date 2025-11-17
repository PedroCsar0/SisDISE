<template>
  <div>
    <div v-if="isLoading" class="text-center">Carregando...</div>

    <div v-if="successMessage" class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-md mb-6">
      {{ successMessage }}
    </div>
    <div v-if="errorMessage" class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-md mb-6">
      {{ errorMessage }}
    </div>

    <form v-if="form" @submit.prevent="handleUpdateUser">

      <div class="bg-white shadow rounded-lg p-6 mb-6">
        <h1 class="text-3xl font-bold text-gray-900 mb-6">Editar Usuário</h1>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
          <div>
            <label class="block text-sm font-medium text-gray-700">Nome</label>
            <input v-model="form.name" type="text" class="input-style" />
          </div>
          <div>
            <label class="block text-sm font-medium text-gray-700">E-mail</label>
            <input v-model="form.email" type="email" class="input-style" />
          </div>
          <div>
            <label class="block text-sm font-medium text-gray-700">Tipo de Usuário</label>
            <select v-model="form.tipo" class="input-style bg-white">
              <option value="Avaliador">Avaliador</option>
              <option value="Gestor Empresarial">Gestor Empresarial</option>
              <option value="Administrador">Administrador</option>
            </select>
          </div>

          <div v-if="form.tipo === 'Gestor Empresarial'">
            <label class="block text-sm font-medium text-gray-700">Empresa Vinculada</label>
            <select v-model="form.empresa_id" class="input-style bg-white">
              <option :value="null">-- Nenhuma --</option>
              <option v-for="empresa in empresas" :key="empresa.id" :value="empresa.id">
                {{ empresa.razaoSocial }}
              </option>
            </select>
          </div>
        </div>
      </div>

      <div class="bg-white shadow rounded-lg p-6 mb-6">
        <h2 class="text-xl font-bold text-gray-800 mb-4">Alterar Senha (Opcional)</h2>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
          <div>
            <label class="block text-sm font-medium text-gray-700">Nova Senha</label>
            <input v-model="form.password" type="password" class="input-style" placeholder="Deixe em branco para não alterar" />
          </div>
          <div>
            <label class="block text-sm font-medium text-gray-700">Confirmar Nova Senha</label>
            <input v-model="form.password_confirmation" type="password" class="input-style" />
          </div>
        </div>
      </div>

      <div v-if="user && user.tipo === 'Avaliador'" class="bg-white shadow rounded-lg p-6 mb-6">
        <h2 class="text-xl font-bold text-gray-800 mb-4">Histórico de Atividades (Diagnósticos)</h2>
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

      <div v-if="user && user.tipo === 'Gestor Empresarial' && !user.empresa" class="bg-yellow-50 border border-yellow-200 p-4 rounded-md text-yellow-700 mb-6">
        Este gestor ainda não está vinculado a nenhuma empresa. Use o menu "Empresa Vinculada" acima para atribuí-lo.
      </div>

      <div class="bg-white shadow rounded-lg p-6">
        <div class="flex justify-between items-center">
          <div>
            <button
              type="submit"
              class="px-6 py-2 font-medium text-white bg-green-600 rounded-md hover:bg-green-700"
              :disabled="isSubmitting"
            >
              {{ isSubmitting ? 'Salvando...' : 'Salvar Alterações' }}
            </button>
          </div>
          <div>
            <button
              type="button"
              @click="handleDeleteUser"
              class="px-6 py-2 font-medium text-white bg-red-600 rounded-md hover:bg-red-700"
              :disabled="isSubmitting"
            >
              Excluir Usuário
            </button>
          </div>
        </div>
      </div>
    </form>
  </div>
</template>

<style scoped>
/* Estilos dos inputs */
.input-style {
  margin-top: 0.25rem; width: 100%; padding: 0.5rem 0.75rem; border: 1px solid #D1D5DB;
  border-radius: 0.375rem; box-shadow: 0 1px 2px 0 rgba(0, 0, 0, 0.05);
}
</style>

<script setup>
import { ref, reactive, onMounted } from 'vue';
import { useRouter } from 'vue-router';
import api from '@/api.js';

const props = defineProps(['id']);
const router = useRouter();

const isLoading = ref(true);
const isSubmitting = ref(false);
const successMessage = ref('');
const errorMessage = ref('');

// 'user' guarda os dados brutos (para a lista de histórico)
const user = ref(null);

// 'form' guarda os dados editáveis
const form = reactive({
  name: '',
  email: '',
  tipo: '',
  empresa_id: null,
  password: '',
  password_confirmation: '',
});

// 'empresas' guarda a lista para o dropdown
const empresas = ref([]);

// 1. Carrega os dados iniciais (Usuário e lista de Empresas)
onMounted(async () => {
  try {
    const [userResponse, empresasResponse] = await Promise.all([
      api.get(`/admin/users/${props.id}`), // Rota que busca o user E seus diagnósticos
      api.get('/empresas') // Pega a lista de empresas para o dropdown
    ]);

    // Guarda o usuário bruto (com diagnósticos)
    user.value = userResponse.data;

    // Preenche o formulário com os dados do usuário
    form.name = user.value.name;
    form.email = user.value.email;
    form.tipo = user.value.tipo;
    form.empresa_id = user.value.empresa_id;

    // Preenche a lista de empresas
    empresas.value = empresasResponse.data;

  } catch (error) {
    errorMessage.value = "Erro ao carregar dados.";
    console.error(error);
  } finally {
    isLoading.value = false;
  }
});

// 2. Função de Atualização
const handleUpdateUser = async () => {
  isSubmitting.value = true;
  successMessage.value = '';
  errorMessage.value = '';

  try {
    // Envia o objeto 'form' inteiro para a rota PUT
    const response = await api.put(`/admin/users/${props.id}`, form);

    // Atualiza os dados do 'user' local (para o histórico)
    user.value = response.data;

    successMessage.value = "Usuário atualizado com sucesso!";

    // Limpa os campos de senha após o sucesso
    form.password = '';
    form.password_confirmation = '';

  } catch (error) {
    errorMessage.value = "Erro ao atualizar. Verifique os campos.";
    console.error(error);
  } finally {
    isSubmitting.value = false;
  }
};

// 3. Função de Exclusão
const handleDeleteUser = async () => {
  if (!window.confirm('TEM CERTEZA? Esta ação excluirá o usuário permanentemente.')) {
    return;
  }

  isSubmitting.value = true;
  errorMessage.value = '';

  try {
    await api.delete(`/admin/users/${props.id}`);
    router.push('/admin/users'); // Sucesso! Volta para a lista

  } catch (error) {
    if (error.response && error.response.status === 403) {
      errorMessage.value = error.response.data.message;
    } else {
      errorMessage.value = "Erro ao excluir o usuário.";
    }
    console.error(error);
    isSubmitting.value = false;
  }
};

// 4. Função auxiliar para colorir os scores
const getScoreColor = (score) => {
  if (score <= 250) return 'text-red-600';
  if (score <= 500) return 'text-orange-600';
  if (score <= 750) return 'text-yellow-600';
  return 'text-green-600';
};
</script>
