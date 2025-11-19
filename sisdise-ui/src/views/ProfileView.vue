<template>
  <div class="max-w-2xl mx-auto animate-fade-in">
    <div class="flex items-center gap-3 mb-8">
      <div class="p-2 bg-indigo-100 text-primary rounded-lg">
        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
      </div>
      <h1 class="text-3xl font-bold text-gray-900">Minha Conta</h1>
    </div>

    <div class="bg-white shadow-xl rounded-2xl p-8 border border-gray-100">
      <form @submit.prevent="handleUpdateProfile" class="space-y-6">

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
          <div>
            <label class="text-xs font-bold text-gray-500 uppercase mb-1 block">Nome</label>
            <input v-model="form.name" type="text" class="input-style-premium" />
          </div>
          <div>
            <label class="text-xs font-bold text-gray-500 uppercase mb-1 block">E-mail</label>
            <input v-model="form.email" type="email" class="input-style-premium" />
          </div>
        </div>

        <div class="border-t border-gray-100 pt-6 mt-2">
          <h2 class="text-lg font-bold text-gray-800 mb-4 flex items-center gap-2">
            <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
            Alterar Senha
          </h2>
          <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
              <label class="text-xs font-bold text-gray-500 uppercase mb-1 block">Nova Senha</label>
              <input v-model="form.password" type="password" class="input-style-premium" placeholder="Digite sua nova senha" />
            </div>
            <div>
              <label class="text-xs font-bold text-gray-500 uppercase mb-1 block">Confirmar</label>
              <input v-model="form.password_confirmation" type="password" class="input-style-premium" placeholder="Digite novamente" />
            </div>
          </div>
        </div>

        <div class="flex justify-end pt-4">
          <button
            type="submit"
            class="px-8 py-3 bg-primary text-white font-bold rounded-xl hover:bg-primary-dark shadow-lg hover:shadow-xl hover:-translate-y-0.5 transition-all disabled:opacity-70"
            :disabled="isSubmitting"
          >
            {{ isSubmitting ? 'Salvando...' : 'Salvar Alterações' }}
          </button>
        </div>
      </form>
    </div>
  </div>
</template>

<style scoped>
.input-style-premium {
  width: 100%; padding: 12px 16px; border: 1px solid #e5e7eb; border-radius: 12px; transition: all 0.2s;
}
.input-style-premium:focus {
  outline: none; border-color: #4f46e5; box-shadow: 0 0 0 3px rgba(79, 70, 229, 0.1);
}
.animate-fade-in { animation: fadeIn 0.5s ease-out; }
@keyframes fadeIn { from { opacity: 0; transform: translateY(10px); } to { opacity: 1; transform: translateY(0); } }
</style>

<script setup>
import { reactive, ref, onMounted } from 'vue';
import api from '@/api.js';
import { useUserStore } from '@/stores/userStore';
import { useToast } from "vue-toastification";

const userStore = useUserStore();
const toast = useToast();
const isSubmitting = ref(false);
const form = reactive({ name: '', email: '', password: '', password_confirmation: '' });

onMounted(async () => {
  if (!userStore.user) await userStore.fetchUser();
  if (userStore.user) { form.name = userStore.user.name; form.email = userStore.user.email; }
});

const handleUpdateProfile = async () => {
  isSubmitting.value = true;
  try {
    const response = await api.put('/profile', form);
    userStore.setUser(response.data.user);
    toast.success('Perfil atualizado!');
    form.password = ''; form.password_confirmation = '';
  } catch (error) {
    console.error(error); // Adicionado uso do 'error'
    toast.error('Erro ao atualizar.');
  } finally { isSubmitting.value = false; }
};
</script>
