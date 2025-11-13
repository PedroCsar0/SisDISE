<template>
  <div>
    <header class="bg-white shadow-md">
      <nav class="container mx-auto max-w-5xl px-4 py-3 flex justify-between items-center">
        <div>
          <img class="h-10 w-auto" src="/src/assets/logo.svg" alt="SisDISE Logo" />
        </div>
        <button @click="router.push('/dashboard')" class="text-sm font-medium text-gray-600 hover:text-indigo-600">
          Voltar ao Dashboard
        </button>
      </nav>
    </header>

    <main class="container mx-auto max-w-5xl p-4 mt-6">
      <h1 class="text-3xl font-bold text-gray-900 mb-6">Gerenciar Empresas</h1>

      <div class="grid grid-cols-1 md:grid-cols-3 gap-8">

        <div class="md:col-span-1">
          <form @submit.prevent="handleSave" class="p-6 bg-white rounded-lg shadow-md space-y-4">
            <h2 class="text-xl font-semibold text-gray-800 border-b pb-2">Cadastrar Nova Empresa</h2>

            <div v-if="formMessage" :class="isError ? 'text-red-600' : 'text-green-600'">
              {{ formMessage }}
            </div>

            <div>
              <label for="razaoSocial" class="mt-1 w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500">Razão Social</label>
              <input v-model="newEmpresa.razaoSocial" type="text" id="razaoSocial" required class="mt-1 w-full input-style" />
            </div>
            <div>
              <label for="cnpj" class="block text-sm font-medium text-gray-700">CNPJ</label>
              <input v-model="newEmpresa.cnpj" type="text" id="cnpj" required class="mt-1 w-full input-style" />
            </div>
            <div>
              <label for="setor" class="mt-1 w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500">Setor</label>
              <input v-model="newEmpresa.setor" type="text" id="setor" class="mt-1 w-full input-style" />
            </div>
            <div>
              <label for="cidade" class="mt-1 w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500">Cidade</label>
              <input v-model="newEmpresa.cidade" type="text" id="cidade" class="mt-1 w-full input-style" />
            </div>
            <div>
              <label for="estado" class="mt-1 w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500">Estado (UF)</label>
              <input v-model="newEmpresa.estado" type="text" id="estado" maxlength="2" class="mt-1 w-full input-style" />
            </div>

            <button
              type="submit"
              class="w-full px-4 py-2 font-medium text-white bg-indigo-600 rounded-md hover:bg-indigo-700"
              :disabled="isSubmitting"
            >
              {{ isSubmitting ? 'Salvando...' : 'Salvar Empresa' }}
            </button>
          </form>
        </div>

        <div class="md:col-span-2">
          <div class="p-6 bg-white rounded-lg shadow-md">
            <h2 class="text-xl font-semibold text-gray-800 border-b pb-2">Empresas Cadastradas</h2>

            <div v-if="isLoading" class="mt-4">Carregando empresas...</div>

            <ul v-else class="mt-4 space-y-3 max-h-96 overflow-y-auto">
              <li v-for="empresa in empresas" :key="empresa.id" class="p-3 border rounded-md">
                <p class="font-semibold text-gray-900">{{ empresa.razaoSocial }}</p>
                <p class="text-sm text-gray-600">CNPJ: {{ empresa.cnpj }}</p>
                <p class="text-sm text-gray-600">{{ empresa.cidade }} - {{ empresa.estado }}</p>
              </li>
              <li v-if="empresas.length === 0" class="text-gray-500">
                Nenhuma empresa cadastrada.
              </li>
            </ul>
          </div>
        </div>

      </div>
    </main>
  </div>
</template>

<script setup>
import { ref, reactive, onMounted } from 'vue';
import { useRouter } from 'vue-router';
import api from '@/api.js';

const router = useRouter();

// --- Estado da Lista ---
const empresas = ref([]);
const isLoading = ref(true);

// --- Estado do Formulário ---
const isSubmitting = ref(false);
const formMessage = ref('');
const isError = ref(false);

// Usamos 'reactive' para o formulário (é bom para objetos)
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
};

// --- Lógica da API ---

// 1. Busca a lista de empresas quando a página é carregada
const fetchEmpresas = async () => {
  try {
    isLoading.value = true;
    const response = await api.get('/empresas');
    empresas.value = response.data;
  } catch (error) {
    console.error('Erro ao buscar empresas:', error);
    formMessage.value = 'Falha ao carregar lista de empresas.';
    isError.value = true;
  } finally {
    isLoading.value = false;
  }
};

// 2. Salva a nova empresa
const handleSave = async () => {
  isSubmitting.value = true;
  formMessage.value = '';
  isError.value = false;

  try {
    // Envia os dados do formulário 'newEmpresa' para a API
    const response = await api.post('/empresas', newEmpresa);

    // Sucesso!
    // Adiciona a nova empresa à lista (para atualização instantânea)
    empresas.value.push(response.data);

    formMessage.value = 'Empresa salva com sucesso!';
    resetForm(); // Limpa o formulário

  } catch (error) {
    // Trata erros (ex: CNPJ duplicado)
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

// 3. Hook de Carregamento
onMounted(() => {
  fetchEmpresas();
});
</script>
