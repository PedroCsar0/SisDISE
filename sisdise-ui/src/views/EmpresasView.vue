<template>
  <div class="animate-fade-in max-w-6xl mx-auto">
    <div class="flex items-center gap-3 mb-8">
      <div class="p-2 bg-blue-100 text-blue-600 rounded-lg">
        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
      </div>
      <h1 class="text-3xl font-bold text-gray-900">Gerenciar Empresas</h1>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">

      <div class="lg:col-span-1">
        <div class="bg-white rounded-2xl shadow-xl p-6 border border-gray-100 sticky top-24">
          <h2 class="text-lg font-bold text-gray-800 mb-4 pb-2 border-b flex items-center gap-2">
            <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v3m0 0v3m0-3h3m-3 0H9m12 0a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            Nova Empresa
          </h2>
          <p class="text-xs text-gray-400 mb-4">Preencha para cadastrar uma nova organização.</p>

          <form @submit.prevent="handleCreate" class="space-y-4">
            <div v-for="(label, key) in {razaoSocial:'Razão Social', cnpj:'CNPJ', setor:'Setor', cidade:'Cidade', estado:'Estado (UF)'}" :key="key">
              <label class="text-xs font-bold text-gray-500 uppercase mb-1 block">{{ label }}</label>
              <input v-model="newEmpresa[key]" type="text" :id="key" class="input-style-premium" :maxlength="key === 'estado' ? 2 : 255" />
            </div>

            <div class="pt-2">
              <button type="submit" class="w-full py-3 px-4 bg-primary text-white font-bold rounded-xl hover:bg-primary-dark shadow-md transition-all flex justify-center items-center gap-2" :disabled="isCreating">
                <svg v-if="!isCreating" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/></svg>
                <span v-if="isCreating">Salvando...</span>
                <span v-else>Cadastrar Empresa</span>
              </button>
            </div>
          </form>
        </div>
      </div>

      <div class="lg:col-span-2 space-y-4">
        
        <div v-if="isLoading" class="space-y-4">
          <div v-for="i in 3" :key="i" class="bg-white rounded-xl shadow-md border border-gray-100 p-5 animate-pulse">
            <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
              <div class="w-full">
                <div class="h-6 bg-gray-200 rounded w-1/3 mb-3"></div>
                <div class="flex gap-2"><div class="h-5 bg-gray-100 rounded w-24"></div><div class="h-5 bg-gray-100 rounded w-32"></div></div>
              </div>
            </div>
          </div>
        </div>

        <div v-else-if="empresas.length === 0" class="bg-white rounded-2xl shadow-md p-10 text-center">
          <p class="text-gray-400">Nenhuma empresa cadastrada ainda.</p>
        </div>

        <div 
          v-else 
          v-for="empresa in empresas" 
          :key="empresa.id" 
          class="bg-white rounded-xl shadow-md transition-all border border-gray-100 overflow-hidden"
          :class="editingEmpresaId === empresa.id ? 'ring-2 ring-primary shadow-lg scale-[1.01]' : 'hover:shadow-lg'"
        >
          
          <div v-if="editingEmpresaId === empresa.id" class="p-5 bg-indigo-50/30">
            <form @submit.prevent="handleUpdate" class="space-y-4">
              <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                 <div>
                    <label class="text-xs font-bold text-gray-500 uppercase mb-1 block">Razão Social</label>
                    <input v-model="editingForm.razaoSocial" class="input-style-premium bg-white" />
                 </div>
                 <div>
                    <label class="text-xs font-bold text-gray-500 uppercase mb-1 block">CNPJ</label>
                    <input v-model="editingForm.cnpj" class="input-style-premium bg-white" />
                 </div>
                 <div>
                    <label class="text-xs font-bold text-gray-500 uppercase mb-1 block">Setor</label>
                    <input v-model="editingForm.setor" class="input-style-premium bg-white" />
                 </div>
                 <div class="grid grid-cols-3 gap-2">
                    <div class="col-span-2">
                      <label class="text-xs font-bold text-gray-500 uppercase mb-1 block">Cidade</label>
                      <input v-model="editingForm.cidade" class="input-style-premium bg-white" />
                    </div>
                    <div>
                      <label class="text-xs font-bold text-gray-500 uppercase mb-1 block">UF</label>
                      <input v-model="editingForm.estado" class="input-style-premium bg-white" maxlength="2" />
                    </div>
                 </div>
              </div>
              
              <div class="flex gap-3 justify-end border-t border-gray-200 pt-4 mt-2">
                 <button type="button" @click="cancelEdit" class="px-4 py-2 text-gray-600 bg-white border border-gray-200 font-bold rounded-xl hover:bg-gray-50 transition-colors">
                    Cancelar
                 </button>
                 <button type="submit" class="px-6 py-2 bg-primary text-white font-bold rounded-xl hover:bg-primary-dark shadow-md transition-all" :disabled="isUpdating">
                    {{ isUpdating ? 'Salvando...' : 'Salvar Alterações' }}
                 </button>
              </div>
            </form>
          </div>

          <div v-else>
            <div class="p-5 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
              <div>
                <h3 class="text-lg font-bold text-gray-900">{{ empresa.razaoSocial }}</h3>
                <div class="flex flex-wrap items-center gap-3 mt-2 text-sm text-gray-500">
                  <span class="flex items-center gap-1" title="CNPJ">
                    <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" /></svg>
                    {{ empresa.cnpj }}
                  </span>
                  <span v-if="empresa.setor" class="flex items-center gap-1 px-2 py-0.5 bg-indigo-50 text-indigo-700 rounded text-xs font-bold uppercase tracking-wide border border-indigo-100">
                    {{ empresa.setor }}
                  </span>
                  <span class="flex items-center gap-1 ml-1">
                    <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                    {{ empresa.cidade }}/{{ empresa.estado }}
                  </span>
                </div>
              </div>

              <div v-if="userStore.user && (userStore.user.tipo === 'Administrador' || userStore.user.id === empresa.user_id)" class="flex gap-2">
                <button @click="startEdit(empresa)" class="p-2 bg-blue-50 text-blue-600 rounded-lg hover:bg-blue-100 transition-colors" title="Editar">
                  <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/></svg>
                </button>
                <button @click="handleDelete(empresa.id)" class="p-2 bg-red-50 text-red-600 rounded-lg hover:bg-red-100 transition-colors" title="Excluir">
                  <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                </button>
              </div>
            </div>

            <div class="bg-gray-50 px-5 py-3 border-t border-gray-100 flex justify-between items-center">
              <div class="text-xs text-gray-500">
                <span class="font-bold">{{ empresa.gestores ? empresa.gestores.length : 0 }}</span> gestores vinculados
              </div>
              <button
                v-if="userStore.user && (userStore.user.tipo === 'Administrador' || userStore.user.id === empresa.user_id)"
                @click="openLinkModal(empresa)"
                class="text-xs font-bold text-indigo-600 hover:text-indigo-800 flex items-center gap-1"
              >
                Gerenciar Acesso <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
              </button>
            </div>
          </div>
        </div>
      </div>
    </div>

    <div v-if="showLinkModal" class="fixed inset-0 z-50 flex items-center justify-center bg-black/60 backdrop-blur-sm p-4">
      <div class="bg-white p-6 rounded-2xl shadow-2xl w-full max-w-lg transform transition-all scale-100 animate-fade-in">
        <h2 class="text-xl font-bold text-gray-900 mb-4">Vincular Gestor</h2>
        <p class="mb-6 text-gray-600">Selecione um gestor para: <strong class="text-primary">{{ linkingEmpresa?.razaoSocial }}</strong></p>

        <div v-if="isLinkingLoading" class="py-4 text-center">
           <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-primary mx-auto"></div>
        </div>

        <div v-else class="mb-6">
          <div v-if="gestoresDisponiveis.length > 0">
             <select v-model="selectedGestorId" class="input-style-premium w-full cursor-pointer">
                <option :value="null" disabled>Selecione da lista...</option>
                <option v-for="gestor in gestoresDisponiveis" :key="gestor.id" :value="gestor.id">{{ gestor.name }} ({{ gestor.email }})</option>
             </select>
          </div>
          <div v-else class="bg-yellow-50 border border-yellow-200 rounded-xl p-4 text-yellow-800 text-sm flex items-start gap-3">
             <svg class="w-5 h-5 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
             <p>Não há gestores disponíveis (sem vínculo) no momento.</p>
          </div>
        </div>

        <div class="flex justify-end gap-3">
          <button @click="closeLinkModal" class="px-4 py-2 text-gray-600 font-bold hover:bg-gray-100 rounded-xl">Cancelar</button>
          <button
             @click="handleLinkGestor"
             class="px-6 py-2 bg-primary text-white font-bold rounded-xl hover:bg-primary-dark disabled:opacity-50"
             :disabled="!selectedGestorId || isSubmitting"
          >
             Confirmar
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<style scoped>
.input-style-premium {
  width: 100%; padding: 10px 14px; border: 1px solid #e5e7eb; border-radius: 10px; transition: all 0.2s; background: #fff;
}
select.input-style-premium {
  appearance: none;
  background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 20 20'%3e%3cpath stroke='%236b7280' stroke-linecap='round' stroke-linejoin='round' stroke-width='1.5' d='M6 8l4 4 4-4'/%3e%3c/svg%3e");
  background-position: right 0.5rem center;
  background-repeat: no-repeat;
  background-size: 1.5em 1.5em;
  padding-right: 2.5rem;
}
.input-style-premium:focus {
  outline: none; border-color: #4f46e5; box-shadow: 0 0 0 3px rgba(79, 70, 229, 0.1);
}
.animate-fade-in { animation: fadeIn 0.5s ease-out; }
@keyframes fadeIn { from { opacity: 0; transform: translateY(10px); } to { opacity: 1; transform: translateY(0); } }
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

// Estado de CRIAÇÃO (Formulário do topo)
const isCreating = ref(false);
const newEmpresa = reactive({ razaoSocial: '', cnpj: '', setor: '', cidade: '', estado: '' });

// Estado de EDIÇÃO (Inline nos cards)
const isUpdating = ref(false);
const editingEmpresaId = ref(null);
const editingForm = reactive({ razaoSocial: '', cnpj: '', setor: '', cidade: '', estado: '' });

// Estado do Modal de Vínculo
const isSubmitting = ref(false); // Para o modal
const showLinkModal = ref(false);
const isLinkingLoading = ref(false);
const linkingEmpresa = ref(null);
const gestoresDisponiveis = ref([]);
const selectedGestorId = ref(null);

const fetchEmpresas = async () => {
  try {
    isLoading.value = true;
    if (!userStore.user) await userStore.fetchUser();
    const response = await api.get('/empresas');
    empresas.value = response.data;
  } catch (error) {
    console.error(error);
    toast.error('Falha ao carregar empresas.');
    if (error.response && error.response.status === 401) {
      router.push('/login');
    }
  } finally { isLoading.value = false; }
};

// --- FUNÇÃO PARA CRIAR NOVA EMPRESA (Topo) ---
const handleCreate = async () => {
  if (!newEmpresa.razaoSocial || !newEmpresa.cnpj) {
      toast.warning("Preencha os campos obrigatórios.");
      return;
  }
  isCreating.value = true;
  try {
      const response = await api.post('/empresas', newEmpresa);
      empresas.value.push(response.data);
      toast.success('Empresa cadastrada!');
      // Limpa o formulário de criação
      Object.assign(newEmpresa, { razaoSocial: '', cnpj: '', setor: '', cidade: '', estado: '' });
  } catch (error) {
    console.error(error);
    toast.error('Erro ao cadastrar.');
  } finally { isCreating.value = false; }
};

// --- FUNÇÕES DE EDIÇÃO (Inline) ---
const startEdit = (empresa) => {
  // Fecha qualquer outra edição aberta
  editingEmpresaId.value = empresa.id;
  // Copia os dados da empresa clicada para o formulário de edição temporário
  Object.assign(editingForm, empresa);
};

const cancelEdit = () => {
  editingEmpresaId.value = null;
  Object.assign(editingForm, { razaoSocial: '', cnpj: '', setor: '', cidade: '', estado: '' });
};

const handleUpdate = async () => {
  if (!editingEmpresaId.value) return;
  isUpdating.value = true;
  try {
    const response = await api.put(`/empresas/${editingEmpresaId.value}`, editingForm);
    
    // Atualiza a lista localmente sem precisar recarregar
    const index = empresas.value.findIndex(e => e.id === editingEmpresaId.value);
    if (index !== -1) {
      // Preserva os gestores e atualiza os dados
      empresas.value[index] = { ...empresas.value[index], ...response.data };
    }
    
    toast.success('Alterações salvas!');
    cancelEdit(); // Sai do modo de edição
  } catch (error) {
    console.error(error);
    toast.error('Erro ao atualizar empresa.');
  } finally { isUpdating.value = false; }
};

// --- FUNÇÕES DE EXCLUSÃO E VÍNCULO (Mantidas) ---
const handleDelete = async (id) => {
  if (!window.confirm('Tem certeza que deseja excluir esta empresa?')) return;
  try {
    await api.delete(`/empresas/${id}`);
    empresas.value = empresas.value.filter(e => e.id !== id);
    toast.success('Excluído!');
  } catch (error) {
    console.error(error);
    toast.error('Erro ao excluir.');
  }
};

const openLinkModal = async (empresa) => {
  linkingEmpresa.value = empresa; showLinkModal.value = true; isLinkingLoading.value = true; selectedGestorId.value = null;
  try {
    const response = await api.get('/gestores-disponiveis');
    gestoresDisponiveis.value = response.data;
  } catch (error) {
    console.error(error);
    toast.error('Erro ao buscar gestores.');
  }
  finally { isLinkingLoading.value = false; }
};
const closeLinkModal = () => { showLinkModal.value = false; linkingEmpresa.value = null; };
const handleLinkGestor = async () => {
  if (!selectedGestorId.value) return;
  isSubmitting.value = true;
  try {
    const response = await api.post(`/empresas/${linkingEmpresa.value.id}/vincular-gestor`, { user_id: selectedGestorId.value });
    const index = empresas.value.findIndex(e => e.id === linkingEmpresa.value.id);
    if (index !== -1) {
       if (!empresas.value[index].gestores) empresas.value[index].gestores = [];
       empresas.value[index].gestores.push(response.data);
    }
    toast.success('Vinculado!');
    closeLinkModal();
  } catch (error) {
    console.error(error);
    toast.error('Erro ao vincular.');
  }
  finally { isSubmitting.value = false; }
};

onMounted(() => { fetchEmpresas(); });
</script>