<template>
  <div>
    <h1 class="text-3xl font-bold text-gray-900 mb-6">Gerenciar Empresas</h1>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-8">

      <div class="md:col-span-1">
        <form @submit.prevent="handleSave" class="p-6 bg-white rounded-lg shadow-md space-y-4">

          <h2 class="text-xl font-semibold text-gray-800 border-b pb-2">
            {{ isEditing ? 'Editar Empresa' : 'Cadastrar Nova Empresa' }}
          </h2>

          <div>
            <label for="razaoSocial" class="block text-sm font-medium text-gray-700">Razão Social</label>
            <input v-model="newEmpresa.razaoSocial" type="text" id="razaoSocial" required class="input-style" />
          </div>
          <div>
            <label for="cnpj" class="block text-sm font-medium text-gray-700">CNPJ</label>
            <input v-model="newEmpresa.cnpj" type="text" id="cnpj" required class="input-style" />
          </div>
          <div>
            <label for="setor" class="block text-sm font-medium text-gray-700">Setor</label>
            <input v-model="newEmpresa.setor" type="text" id="setor" class="input-style" />
          </div>
          <div>
            <label for="cidade" class="block text-sm font-medium text-gray-700">Cidade</label>
            <input v-model="newEmpresa.cidade" type="text" id="cidade" class="input-style" />
          </div>
          <div>
            <label for="estado" class="block text-sm font-medium text-gray-700">Estado (UF)</label>
            <input v-model="newEmpresa.estado" type="text" id="estado" maxlength="2" class="input-style" />
          </div>

          <div class="flex space-x-2">
            <button
              type="submit"
              class="w-full px-4 py-2 font-medium text-white rounded-md"
              :class="isEditing ? 'bg-green-600 hover:bg-green-700' : 'bg-indigo-600 hover:bg-indigo-700'"
              :disabled="isSubmitting"
            >
              {{ isSubmitting ? 'Salvando...' : (isEditing ? 'Atualizar Empresa' : 'Salvar Empresa') }}
            </button>

            <button
              v-if="isEditing"
              type="button"
              @click="cancelEdit"
              class="w-full px-4 py-2 font-medium text-gray-700 bg-gray-200 rounded-md hover:bg-gray-300"
            >
              Cancelar
            </button>
          </div>
        </form>
      </div>

      <div class="md:col-span-2">
        <div class="p-6 bg-white rounded-lg shadow-md">
          <h2 class="text-xl font-semibold text-gray-800 border-b pb-2">Empresas Cadastradas</h2>

          <div v-if="isLoading" class="mt-4">Carregando empresas...</div>

          <ul v-else class="mt-4 space-y-4 max-h-96 overflow-y-auto">
            <li v-for="empresa in empresas" :key="empresa.id" class="p-4 border rounded-md">

              <div class="flex justify-between items-center">
                <div>
                  <p class="font-semibold text-gray-900">{{ empresa.razaoSocial }}</p>
                  <p class="text-sm text-gray-600">CNPJ: {{ empresa.cnpj }}</p>
                </div>
                <div
                  v-if="userStore.user && (userStore.user.tipo === 'Administrador' || userStore.user.id === empresa.user_id)"
                  class="flex space-x-2"
                >
                  <button @click="startEdit(empresa)" class="px-3 py-1 text-sm font-medium text-white bg-blue-600 rounded-md hover:bg-blue-700">Editar</button>
                  <button @click="handleDelete(empresa.id)" class="px-3 py-1 text-sm font-medium text-white bg-red-600 rounded-md hover:bg-red-700">Excluir</button>
                </div>
              </div>

              <div class="mt-4 border-t pt-3">
                <h4 class="text-sm font-semibold text-gray-700">Gestores Vinculados:</h4>
                <ul v-if="empresa.gestores && empresa.gestores.length > 0" class="list-disc list-inside mt-2 text-sm text-gray-600">
                  <li v-for="gestor in empresa.gestores" :key="gestor.id">{{ gestor.name }} ({{ gestor.email }})</li>
                </ul>
                <p v-else class="text-sm text-gray-500 mt-2">Nenhum gestor vinculado.</p>

                <button
                  v-if="userStore.user && (userStore.user.tipo === 'Administrador' || userStore.user.id === empresa.user_id)"
                  @click="openLinkModal(empresa)"
                  class="mt-3 px-3 py-1 text-xs font-medium text-white bg-gray-700 rounded-md hover:bg-gray-900"
                >
                  + Vincular Novo Gestor
                </button>
              </div>
            </li>

            <li v-if="empresas.length === 0" class="text-gray-500">Nenhuma empresa cadastrada.</li>
          </ul>
        </div>
      </div>

    </div>

    <div v-if="showLinkModal" class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50">
      <div class="bg-white p-6 rounded-lg shadow-lg w-full max-w-lg">
        <h2 class="text-xl font-bold text-gray-900 mb-4">Vincular Gestor</h2>
        <p class="mb-4">Selecione um gestor disponível para vincular à empresa: <strong class="text-primary-dark">{{ linkingEmpresa.razaoSocial }}</strong></p>

        <div v-if="isLinkingLoading">Carregando gestores...</div>

        <div v-else>
          <select v-model="selectedGestorId" class="input-style bg-white w-full">
            <option :value="null" disabled>Selecione um gestor</option>
            <option v-for="gestor in gestoresDisponiveis" :key="gestor.id" :value="gestor.id">
              {{ gestor.name }} ({{ gestor.email }})
            </option>
          </select>
          <p v-if="gestoresDisponiveis.length === 0" class="text-sm text-red-500 mt-2">
            Nenhum gestor disponível encontrado. Peça para o gestor se cadastrar no sistema.
          </p>
        </div>

        <div class="flex justify-end space-x-4 mt-6">
          <button @click="closeLinkModal" class="px-4 py-2 text-gray-700 bg-gray-200 rounded-md hover:bg-gray-300">
            Cancelar
          </button>
          <button
            @click="handleLinkGestor"
            :disabled="!selectedGestorId || isSubmitting"
            class="px-4 py-2 text-white bg-primary rounded-md hover:bg-primary-dark disabled:bg-gray-400"
          >
            {{ isSubmitting ? 'Salvando...' : 'Confirmar Vínculo' }}
          </button>
        </div>
      </div>
    </div>

  </div>
</template>

<style scoped>
.input-style {
  margin-top: 0.25rem; width: 100%; padding-left: 0.75rem; padding-right: 0.75rem;
  padding-top: 0.5rem; padding-bottom: 0.5rem; border-width: 1px;
  border-color: #D1D5DB; border-radius: 0.375rem; box-shadow: 0 1px 2px 0 rgba(0, 0, 0, 0.05);
}
.input-style:focus {
  outline: none; border-color: #6366f1; --tw-ring-color: #6366f1;
  box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.3);
}
</style>

<script setup>
import { ref, reactive, onMounted } from 'vue';
import { useRouter } from 'vue-router';
import api from '@/api.js';
import { useUserStore } from '@/stores/userStore';
import { useToast } from "vue-toastification";

const router = useRouter();
const userStore = useUserStore();
const toast = useToast();

const empresas = ref([]);
const isLoading = ref(true);

const isSubmitting = ref(false);
const isEditing = ref(false);
const editingEmpresaId = ref(null);
const newEmpresa = reactive({ razaoSocial: '', cnpj: '', setor: '', cidade: '', estado: '' });

const showLinkModal = ref(false);
const isLinkingLoading = ref(false);
const linkingEmpresa = ref(null);
const gestoresDisponiveis = ref([]);
const selectedGestorId = ref(null);

const resetForm = () => {
  newEmpresa.razaoSocial = ''; newEmpresa.cnpj = ''; newEmpresa.setor = '';
  newEmpresa.cidade = ''; newEmpresa.estado = '';
  isEditing.value = false; editingEmpresaId.value = null;
};

const fetchEmpresas = async () => {
  try {
    isLoading.value = true;
    if (!userStore.user) {
      await userStore.fetchUser();
    }
    const response = await api.get('/empresas');
    empresas.value = response.data;
  } catch (error) { // 'error' é usado aqui
    console.error('Erro ao buscar empresas:', error);
    toast.error('Falha ao carregar lista de empresas.');
    if (error.response && error.response.status === 401) {
      router.push('/login');
    }
  } finally {
    isLoading.value = false;
  }
};

const handleSave = async () => {
  isSubmitting.value = true;
  try {
    if (isEditing.value) {
      const id = editingEmpresaId.value;
      const response = await api.put(`/empresas/${id}`, newEmpresa);
      const index = empresas.value.findIndex(e => e.id === id);
      if (index !== -1) {
        empresas.value[index] = { ...empresas.value[index], ...response.data };
      }
      toast.success('Empresa atualizada com sucesso!');
    } else {
      const response = await api.post('/empresas', newEmpresa);
      empresas.value.push(response.data);
      toast.success('Empresa salva com sucesso!');
    }
    resetForm();
  } catch (error) { // 'error' é usado aqui
    if (error.response && error.response.status === 422) {
      toast.error('Erro de validação: ' + error.response.data.message);
    } else {
      toast.error('Ocorreu um erro ao salvar.');
    }
    console.error('Erro ao salvar empresa:', error);
  } finally {
    isSubmitting.value = false;
  }
};

const startEdit = (empresa) => {
  isEditing.value = true; editingEmpresaId.value = empresa.id;
  newEmpresa.razaoSocial = empresa.razaoSocial; newEmpresa.cnpj = empresa.cnpj;
  newEmpresa.setor = empresa.setor; newEmpresa.cidade = empresa.cidade;
  newEmpresa.estado = empresa.estado; window.scrollTo(0, 0);
};
const cancelEdit = () => { resetForm(); };
const handleDelete = async (id) => {
  if (!window.confirm('Tem certeza?')) return;
  try {
    await api.delete(`/empresas/${id}`);
    empresas.value = empresas.value.filter(e => e.id !== id);
    toast.success('Empresa excluída com sucesso!');
  } catch (error) { // 'error' é usado aqui
    if (error.response && error.response.status === 409) {
      toast.error(error.response.data.message);
    } else {
      toast.error('Ocorreu um erro ao excluir.');
    }
    console.error('Erro ao excluir empresa:', error);
  }
};

const openLinkModal = async (empresa) => {
  linkingEmpresa.value = empresa;
  showLinkModal.value = true;
  isLinkingLoading.value = true;
  selectedGestorId.value = null;

  try {
    const response = await api.get('/gestores-disponiveis');
    gestoresDisponiveis.value = response.data;
  } catch (error) { // 'error' é usado aqui
    console.error('Erro ao buscar gestores:', error);
    toast.error('Não foi possível buscar os gestores disponíveis.');
    closeLinkModal();
  } finally {
    isLinkingLoading.value = false;
  }
};

const closeLinkModal = () => {
  showLinkModal.value = false;
  linkingEmpresa.value = null;
  gestoresDisponiveis.value = [];
};

const handleLinkGestor = async () => {
  if (!selectedGestorId.value) return;
  isSubmitting.value = true;

  try {
    const empresaId = linkingEmpresa.value.id;
    const response = await api.post(`/empresas/${empresaId}/vincular-gestor`, {
      user_id: selectedGestorId.value
    });

    const index = empresas.value.findIndex(e => e.id === empresaId);
    if (index !== -1) {
      if (!empresas.value[index].gestores) {
        empresas.value[index].gestores = [];
      }
      empresas.value[index].gestores.push(response.data);
    }

    toast.success('Gestor vinculado com sucesso!');
    closeLinkModal();

  } catch (error) { // --- ESTA É A CORREÇÃO ---
    if (error.response && error.response.status === 409) {
      toast.error(error.response.data.message);
    } else {
      toast.error('Erro ao vincular o gestor.');
    }
    console.error('Erro ao vincular gestor:', error); // <-- A LINHA QUE FALTAVA
  } finally {
    isSubmitting.value = false;
  }
};

onMounted(() => {
  fetchEmpresas();
});
</script>
