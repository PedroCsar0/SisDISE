<template>
  <div v-if="isLoading" class="text-center">
    <p class="text-lg text-gray-600">Carregando parâmetros...</p>
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
</template>

<style scoped>
/* Os estilos continuam os mesmos */
.input-style {
  width: 100%;
  padding: 0.5rem 0.75rem;
  border: 1px solid #D1D5DB;
  border-radius: 0.375rem;
  box-shadow: 0 1px 2px 0 rgba(0, 0, 0, 0.05);
}
.input-style:focus {
  outline: none;
  border-color: #6366f1;
  box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.3);
}
</style>

<script setup>
import { ref, onMounted } from 'vue';
import { useRouter } from 'vue-router';
import api from '@/api.js';
import { useUserStore } from '@/stores/userStore';
import { useToast } from "vue-toastification"; // <-- 1. IMPORTE

const router = useRouter();
const userStore = useUserStore();
const toast = useToast(); // <-- 2. INICIE

const grupos = ref([]);
const isLoading = ref(true);
// errorMessage e successMessage foram removidos

const fetchParametros = async () => {
  try {
    isLoading.value = true;
    if (!userStore.user) {
      await userStore.fetchUser();
    }
    const response = await api.get('/admin/parametros');
    grupos.value = response.data;
  } catch (error) {
    console.error('Erro ao buscar parâmetros:', error);
    if (error.response && error.response.status === 403) {
      toast.error('Acesso Negado. Você não tem permissão de Administrador.'); // <-- MUDANÇA
    } else if (error.response && error.response.status === 401) {
      router.push('/login');
    } else {
      toast.error('Falha ao carregar parâmetros.'); // <-- MUDANÇA
    }
  } finally {
    isLoading.value = false;
  }
};

const updateGrupo = async (grupo) => {
  try {
    await api.put(`/admin/grupos/${grupo.id}`, grupo);
    toast.success(`Grupo "${grupo.nome}" atualizado com sucesso!`); // <-- MUDANÇA
  } catch (error) {
    console.error('Erro ao atualizar grupo:', error);
    toast.error('Falha ao atualizar o grupo.'); // <-- MUDANÇA
  }
};

const updateParametro = async (item) => {
  try {
    await api.put(`/admin/parametros/${item.id}`, item);
    toast.success(`Parâmetro "${item.codigo_item}" atualizado com sucesso!`); // <-- MUDANÇA
  } catch (error) {
    console.error('Erro ao atualizar parâmetro:', error);
    toast.error('Falha ao atualizar o parâmetro.'); // <-- MUDANÇA
  }
};

onMounted(() => {
  fetchParametros();
});
</script>
