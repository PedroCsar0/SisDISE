<template>
  <div>
    <h1 class="text-3xl font-bold text-gray-900 mb-6">Gerenciar Empresas</h1>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-8">

      <div class="md:col-span-1">
        <form @submit.prevent="handleSave" class="p-6 bg-white rounded-lg shadow-md space-y-4">

          <h2 class="text-xl font-semibold text-gray-800 border-b pb-2">
            {{ isEditing ? 'Editar Empresa' : 'Cadastrar Nova Empresa' }}
          </h2>

          <div v-if="formMessage" :class="isError ? 'text-red-600' : 'text-green-600'">
            {{ formMessage }}
          </div>

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

          <ul v-else class="mt-4 space-y-3 max-h-96 overflow-y-auto">
            <li v-for="empresa in empresas" :key="empresa.id" class="p-3 border rounded-md flex justify-between items-center">
              <div>
                <p class="font-semibold text-gray-900">{{ empresa.razaoSocial }}</p>
                <p class="text-sm text-gray-600">CNPJ: {{ empresa.cnpj }}</p>
                <p class="text-sm text-gray-600">{{ empresa.cidade }} - {{ empresa.estado }}</p>
              </div>

              <div class="flex space-x-2">
                <button
                  @click="startEdit(empresa)"
                  class="px-3 py-1 text-sm font-medium text-white bg-blue-600 rounded-md hover:bg-blue-700"
                >
                  Editar
                </button>
                <button
                  @click="handleDelete(empresa.id)"
                  class="px-3 py-1 text-sm font-medium text-white bg-red-600 rounded-md hover:bg-red-700"
                >
                  Excluir
                </button>
              </div>
            </li>
            <li v-if="empresas.length === 0" class="text-gray-500">
              Nenhuma empresa cadastrada.
            </li>
          </ul>
        </div>
      </div>

    </div>
  </div>
</template>

<style scoped>
/* Estilos dos inputs corrigidos (sem erros de linter) */
.input-style {
  margin-top: 0.25rem;
  width: 100%;
  padding-left: 0.75rem;
  padding-right: 0.75rem;
  padding-top: 0.5rem;
  padding-bottom: 0.5rem;
  border-width: 1px;
  border-color: #D1D5DB; /* border-gray-300 */
  border-radius: 0.375rem; /* rounded-md */
  box-shadow: 0 1px 2px 0 rgba(0, 0, 0, 0.05); /* shadow-sm */
}
.input-style:focus {
  outline: none;
  border-color: #6366f1; /* focus:border-indigo-500 */
  --tw-ring-color: #6366f1; /* focus:ring-indigo-500 */
  box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.3);
}
</style>

<script setup>
import { ref, reactive, onMounted } from 'vue';
import { useRouter } from 'vue-router';
import api from '@/api.js';
import { useUserStore } from '@/stores/userStore'; // Importar o store

const router = useRouter();
const userStore = useUserStore(); // Usar o store

// --- Estado da Lista ---
const empresas = ref([]);
const isLoading = ref(true);

// --- Estado do Formulário ---
const isSubmitting = ref(false);
const formMessage = ref('');
const isError = ref(false);
const isEditing = ref(false);
const editingEmpresaId = ref(null);

const newEmpresa = reactive({
  razaoSocial: '',
  cnpj: '',
  setor: '',
  cidade: '',
  estado: '',
});

// Função para limpar o formulário
const resetForm = () => {
  newEmpresa.razaoSocial = '';
  newEmpresa.cnpj = '';
  newEmpresa.setor = '';
  newEmpresa.cidade = '';
  newEmpresa.estado = '';
  isEditing.value = false;
  editingEmpresaId.value = null;
};

// --- Lógica da API ---

// 1. Busca a lista de empresas quando a página é carregada
const fetchEmpresas = async () => {
  try {
    isLoading.value = true;

    // Garantir que o utilizador está carregado (para o 'Gate' funcionar no recarregamento)
    if (!userStore.user) {
      await userStore.fetchUser();
    }

    const response = await api.get('/empresas');
    empresas.value = response.data;
  } catch (error) {
    console.error('Erro ao buscar empresas:', error);
    formMessage.value = 'Falha ao carregar lista de empresas.';
    isError.value = true;
    if (error.response && error.response.status === 401) {
      router.push('/login');
    }
  } finally {
    isLoading.value = false;
  }
};

// 2. Salva (Cria OU Atualiza) a empresa
const handleSave = async () => {
  isSubmitting.value = true;
  formMessage.value = '';
  isError.value = false;

  try {
    if (isEditing.value) {
      // --- LÓGICA DE ATUALIZAÇÃO (UPDATE) ---
      const id = editingEmpresaId.value;
      const response = await api.put(`/empresas/${id}`, newEmpresa);

      const index = empresas.value.findIndex(e => e.id === id);
      if (index !== -1) {
        empresas.value[index] = response.data;
      }
      formMessage.value = 'Empresa atualizada com sucesso!';

    } else {
      // --- LÓGICA DE CRIAÇÃO (CREATE) ---
      const response = await api.post('/empresas', newEmpresa);
      empresas.value.push(response.data);
      formMessage.value = 'Empresa salva com sucesso!';
    }

    resetForm();

  } catch (error) {
    isError.value = true;
    if (error.response && error.response.status === 422) {
      formMessage.value = 'Erro de validação: ' + error.response.data.message;
    } else {
      formMessage.value = 'Ocorreu um erro ao salvar.';
    }
    console.error('Erro ao salvar empresa:', error);
  } finally {
    isSubmitting.value = false;
  }
};

// 3. Prepara o formulário para edição
const startEdit = (empresa) => {
  isEditing.value = true;
  editingEmpresaId.value = empresa.id;

  newEmpresa.razaoSocial = empresa.razaoSocial;
  newEmpresa.cnpj = empresa.cnpj;
  newEmpresa.setor = empresa.setor;
  newEmpresa.cidade = empresa.cidade;
  newEmpresa.estado = empresa.estado;

  formMessage.value = '';
  isError.value = false;
  window.scrollTo(0, 0);
};

// 4. Limpa o formulário e sai do modo de edição
const cancelEdit = () => {
  resetForm();
};

// 5. Exclui uma empresa
const handleDelete = async (id) => {
  if (!window.confirm('Tem certeza de que deseja excluir esta empresa? Esta ação não pode ser desfeita.')) {
    return;
  }

  try {
    formMessage.value = '';
    isError.value = false;

    await api.delete(`/empresas/${id}`);

    empresas.value = empresas.value.filter(e => e.id !== id);
    formMessage.value = 'Empresa excluída com sucesso!';

  } catch (error) {
    isError.value = true;
    if (error.response && error.response.status === 409) {
      formMessage.value = error.response.data.message;
    } else {
      formMessage.value = 'Ocorreu um erro ao excluir.';
    }
    console.error('Erro ao excluir empresa:', error);
  }
};

// --- Hook de Carregamento ---
onMounted(() => {
  fetchEmpresas();
});
</script>
