<template>
  <div class="animate-fade-in max-w-4xl mx-auto">

    <div class="flex items-center justify-between mb-8">
      <div class="flex items-center gap-3">
         <button @click="router.push('/admin/users')" class="p-2 bg-white border border-gray-200 rounded-lg text-gray-500 hover:text-primary hover:border-primary transition-colors">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
         </button>
         <div>
            <h1 class="text-2xl font-bold text-gray-900">Editar Usuário</h1>
            <p class="text-sm text-gray-500">Atualize as informações e permissões.</p>
         </div>
      </div>
    </div>

    <div v-if="isLoading" class="text-center py-10">
      <div class="inline-block animate-spin rounded-full h-8 w-8 border-b-2 border-primary"></div>
    </div>

    <form v-else-if="form" @submit.prevent="handleUpdateUser" class="space-y-6">

      <div class="bg-white shadow-xl rounded-2xl p-8 border border-gray-100">
        <h2 class="text-lg font-bold text-gray-800 mb-6 flex items-center gap-2">
           <svg class="w-5 h-5 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
           Informações Pessoais
        </h2>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
          <div>
            <label class="text-xs font-bold text-gray-500 uppercase mb-1 block">Nome Completo</label>
            <input v-model="form.name" type="text" class="input-style-premium" />
          </div>
          <div>
            <label class="text-xs font-bold text-gray-500 uppercase mb-1 block">E-mail</label>
            <input v-model="form.email" type="email" class="input-style-premium" />
          </div>
        </div>
      </div>

      <div class="bg-white shadow-xl rounded-2xl p-8 border border-gray-100">
        <h2 class="text-lg font-bold text-gray-800 mb-6 flex items-center gap-2">
           <svg class="w-5 h-5 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
           Controle de Acesso
        </h2>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
           <div>
            <label class="text-xs font-bold text-gray-500 uppercase mb-1 block">Função / Tipo</label>
            <div class="relative">
              <select v-model="form.tipo" class="input-style-premium appearance-none cursor-pointer">
                <option value="Avaliador">Avaliador</option>
                <option value="Gestor Empresarial">Gestor Empresarial</option>
                <option value="Administrador">Administrador</option>
              </select>
              </div>
          </div>

          <div v-if="form.tipo === 'Gestor Empresarial'" class="animate-fade-in">
            <label class="text-xs font-bold text-gray-500 uppercase mb-1 block">Empresa Vinculada</label>
            <select v-model="form.empresa_id" class="input-style-premium appearance-none cursor-pointer">
              <option :value="null">-- Nenhuma --</option>
              <option v-for="empresa in empresas" :key="empresa.id" :value="empresa.id">
                {{ empresa.razaoSocial }}
              </option>
            </select>
          </div>
        </div>
      </div>

      <div class="bg-white shadow-xl rounded-2xl p-8 border border-gray-100">
        <h2 class="text-lg font-bold text-gray-800 mb-6 flex items-center gap-2">
           <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
           Redefinir Senha (Opcional)
        </h2>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
          <div>
            <label class="text-xs font-bold text-gray-500 uppercase mb-1 block">Nova Senha</label>
            <input v-model="form.password" type="password" class="input-style-premium" placeholder="••••••" />
          </div>
          <div>
            <label class="text-xs font-bold text-gray-500 uppercase mb-1 block">Confirmar Nova Senha</label>
            <input v-model="form.password_confirmation" type="password" class="input-style-premium" placeholder="••••••" />
          </div>
        </div>
      </div>

      <div class="flex justify-between items-center pt-4">
        <button
          type="button"
          @click="handleDeleteUser"
          class="px-6 py-3 text-red-600 font-bold bg-red-50 rounded-xl hover:bg-red-100 transition-all flex items-center gap-2"
          :disabled="isSubmitting"
        >
          <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
          Excluir Usuário
        </button>

        <button
          type="submit"
          class="px-8 py-3 text-white font-bold bg-green-600 rounded-xl hover:bg-green-700 shadow-lg hover:shadow-xl transition-all transform hover:-translate-y-0.5 flex items-center gap-2"
          :disabled="isSubmitting"
        >
          <svg v-if="!isSubmitting" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
          {{ isSubmitting ? 'Salvando...' : 'Salvar Alterações' }}
        </button>
      </div>
    </form>
  </div>
</template>

<style scoped>
.input-style-premium {
  width: 100%;
  padding: 12px 16px;
  border: 1px solid #e5e7eb;
  border-radius: 12px; /* Borda redonda */
  background-color: #fff;
  font-size: 0.95rem;
  transition: all 0.2s;
}

/* Estilo específico para SELECT para adicionar a seta customizada */
select.input-style-premium {
  appearance: none; /* Remove a seta padrão feia */
  background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 20 20'%3e%3cpath stroke='%236b7280' stroke-linecap='round' stroke-linejoin='round' stroke-width='1.5' d='M6 8l4 4 4-4'/%3e%3c/svg%3e");
  background-position: right 0.5rem center;
  background-repeat: no-repeat;
  background-size: 1.5em 1.5em;
  padding-right: 2.5rem;
}

.input-style-premium:focus {
  outline: none;
  border-color: #4f46e5;
  box-shadow: 0 0 0 4px rgba(79, 70, 229, 0.1);
}

.animate-fade-in { animation: fadeIn 0.5s ease-out; }
@keyframes fadeIn { from { opacity: 0; transform: translateY(10px); } to { opacity: 1; transform: translateY(0); } }
</style>

<script setup>
import { ref, reactive, onMounted } from 'vue';
import { useRouter } from 'vue-router';
import api from '@/api.js';
import { useToast } from "vue-toastification";

const props = defineProps(['id']);
const router = useRouter();
const toast = useToast();

const isLoading = ref(true);
const isSubmitting = ref(false);

const user = ref(null);
const form = reactive({ name: '', email: '', tipo: '', empresa_id: null, password: '', password_confirmation: '' });
const empresas = ref([]);

onMounted(async () => {
  try {
    const [userResponse, empresasResponse] = await Promise.all([
      api.get(`/admin/users/${props.id}`),
      api.get('/empresas')
    ]);

    user.value = userResponse.data;
    form.name = user.value.name;
    form.email = user.value.email;
    form.tipo = user.value.tipo;
    form.empresa_id = user.value.empresa_id;
    empresas.value = empresasResponse.data;

  } catch (error) {
    toast.error("Erro ao carregar dados.");
    console.error(error);
  } finally {
    isLoading.value = false;
  }
});

const handleUpdateUser = async () => {
  isSubmitting.value = true;
  try {
    const response = await api.put(`/admin/users/${props.id}`, form);
    user.value = response.data;
    toast.success("Usuário atualizado com sucesso!");
    form.password = ''; form.password_confirmation = '';
  } catch (error) {
    toast.error("Erro ao atualizar.");
    console.error(error);
  } finally {
    isSubmitting.value = false;
  }
};

const handleDeleteUser = async () => {
  if (!window.confirm('TEM CERTEZA? Esta ação excluirá o usuário permanentemente.')) return;

  isSubmitting.value = true;
  try {
    await api.delete(`/admin/users/${props.id}`);
    toast.success("Usuário excluído.");
    router.push('/admin/users');
  } catch (error) {
    if (error.response && error.response.status === 403) {
      toast.error(error.response.data.message);
    } else {
      toast.error("Erro ao excluir.");
    }
    console.error(error);
    isSubmitting.value = false;
  }
};
</script>
