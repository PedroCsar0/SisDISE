<template>
  <div>
    <header class="bg-white shadow-md">
      <nav class="container mx-auto max-w-5xl px-4 py-3 flex justify-between items-center">
        <div>
          <span class="text-xl font-bold text-red-600 ml-2">SisDISE - Painel Administrador</span>
        </div>
        <button @click="router.push('/dashboard')" class="text-sm font-medium text-gray-600 hover:text-indigo-600">
          Voltar ao Dashboard
        </button>
      </nav>
    </header>

    <main class="container mx-auto max-w-5xl p-4 mt-6">

      <div v-if="isLoading" class="text-center">
        <p class="text-lg text-gray-600">Carregando parâmetros...</p>
      </div>

      <div v-if="errorMessage" class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-md mb-6">
        {{ errorMessage }}
      </div>
      <div v-if="successMessage" class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-md mb-6">
        {{ successMessage }}
      </div>

      <div v-else class="space-y-6">
        <h1 class="text-3xl font-bold text-gray-900 mb-6">Configurar Parâmetros e Pesos</h1>

        <div v-for="grupo in grupos" :key="grupo.id" class="p-6 bg-white rounded-lg shadow-md border-l-4 border-indigo-500">

          <h2 class="text-xl font-semibold text-gray-800 mb-4">Grupo: {{ grupo.codigo }}</h2>
          <form @submit.prevent="updateGrupo(grupo)" class="grid grid-cols-1 md:grid-cols-4 gap-4 items-end p-4 bg-indigo-50 rounded-md">
            <div>
              <label :for="`grupo-nome-${grupo.id}`" class="block text-sm font-medium text-gray-700">Nome do Grupo</label>
              <input v-model="grupo.nome" type="text" :id="`grupo-nome-${grupo.id}`" class="input-style" />
            </div>
            <div>
              <label :for="`grupo-peso-${grupo.id}`" class="block text-sm font-medium text-gray-700">Peso (pi)</label>
              <input v-model.number="grupo.peso" type="number" step="0.1" :id="`grupo-peso-${grupo.id}`" class="input-style" />
            </div>
            <div>
              <label :for="`grupo-nmax-${grupo.id}`" class="block text-sm font-medium text-gray-700">Nota Máxima (Nmax)</label>
              <input v-model.number="grupo.nota_maxima_grupo" type="number" :id="`grupo-nmax-${grupo.id}`" class="input-style" />
            </div>
            <button type="submit" class="h-10 px-4 py-2 text-sm font-medium text-white bg-indigo-600 rounded-md hover:bg-indigo-700">
              Salvar Grupo
            </button>
          </form>

          <h3 class="text-lg font-medium text-gray-700 mt-6 mb-2">Parâmetros de Avaliação:</h3>
          <ul class="space-y-2">
            <li v-for="item in grupo.item_parametros" :key="item.id">
              <form @submit.prevent="updateParametro(item)" class="grid grid-cols-1 md:grid-cols-12 gap-2 items-center p-2 rounded-md hover:bg-gray-50">

                <div class="md:col-span-2">
                  <label :for="`item-codigo-${item.id}`" class="sr-only">Código</label>
                  <input v-model="item.codigo_item" type="text" :id="`item-codigo-${item.id}`" class="input-style text-sm" />
                </div>

                <div class="md:col-span-9">
                  <label :for="`item-desc-${item.id}`" class="sr-only">Descrição</label>
                  <input v-model="item.descricao" type="text" :id="`item-desc-${item.id}`" class="input-style text-sm" />
                </div>

                <div class="md:col-span-1">
                  <button type="submit" class="w-full px-3 py-2 text-sm font-medium text-white bg-green-600 rounded-md hover:bg-green-700">
                    Salvar
                  </button>
                </div>
              </form>
            </li>
          </ul>
        </div>

      </div>
    </main>
  </div>
</template>

<style scoped>
.input-style {
  width: 100%;
  padding: 0.5rem 0.75rem; /* px-3 py-2 */
  border: 1px solid #D1D5DB; /* border-gray-300 */
  border-radius: 0.375rem; /* rounded-md */
  box-shadow: 0 1px 2px 0 rgba(0, 0, 0, 0.05); /* shadow-sm */
}
.input-style:focus {
  outline: none;
  border-color: #6366f1; /* focus:border-indigo-500 */
  box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.3); /* focus:ring-indigo-500 */
}
</style>

<script setup>
import { ref, onMounted } from 'vue';
import { useRouter } from 'vue-router';
import api from '@/api.js';

const router = useRouter();
const grupos = ref([]);
const isLoading = ref(true);
const errorMessage = ref('');
const successMessage = ref('');

// 1. Busca os dados quando a página carrega
const fetchParametros = async () => {
  try {
    isLoading.value = true;
    errorMessage.value = '';
    // Chama a rota de admin
    const response = await api.get('/admin/parametros');
    grupos.value = response.data;

  } catch (error) {
    console.error('Erro ao buscar parâmetros:', error);
    // Se o erro for 403, significa que o usuário não é Admin
    if (error.response && error.response.status === 403) {
      errorMessage.value = 'Acesso Negado. Você não tem permissão de Administrador.';
    } else {
      errorMessage.value = 'Falha ao carregar parâmetros.';
    }
  } finally {
    isLoading.value = false;
  }
};

// 2. Função para ATUALIZAR UM GRUPO (ex: Sdt1)
const updateGrupo = async (grupo) => {
  try {
    successMessage.value = '';
    errorMessage.value = '';

    // Envia os dados do grupo (v-model) para a API
    await api.put(`/admin/grupos/${grupo.id}`, grupo);

    successMessage.value = `Grupo "${grupo.nome}" atualizado com sucesso!`;
  } catch (error) {
    console.error('Erro ao atualizar grupo:', error);
    errorMessage.value = 'Falha ao atualizar o grupo.';
  }
};

// 3. Função para ATUALIZAR UM PARÂMETRO (ex: 1.1.1)
const updateParametro = async (item) => {
  try {
    successMessage.value = '';
    errorMessage.value = '';

    // Envia os dados do item (v-model) para a API
    await api.put(`/admin/parametros/${item.id}`, item);

    successMessage.value = `Parâmetro "${item.codigo_item}" atualizado com sucesso!`;
  } catch (error) {
    console.error('Erro ao atualizar parâmetro:', error);
    errorMessage.value = 'Falha ao atualizar o parâmetro.';
  }
};

// 4. Hook de Carregamento
onMounted(() => {
  fetchParametros();
});
</script>
