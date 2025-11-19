<template>
  <div class="min-h-screen flex items-center justify-center bg-gradient-to-br from-gray-100 via-gray-200 to-gray-300 p-4">

    <div class="bg-white w-full max-w-md rounded-2xl shadow-2xl overflow-hidden animate-fade-in-up">
      <div class="p-8">

        <div class="text-center mb-8">
          <div class="inline-flex items-center justify-center w-12 h-12 rounded-full bg-indigo-50 mb-3">
            <img class="h-8 w-auto" src="/src/assets/logo.svg" alt="Logo" />
          </div>
          <h2 class="text-2xl font-bold text-gray-900">Crie sua conta</h2>
          <p class="text-sm text-gray-500 mt-1">Junte-se ao SisDISE para começar.</p>
        </div>

        <form class="space-y-5" @submit.prevent="handleRegister">

          <div>
            <label class="text-xs font-bold text-gray-500 uppercase tracking-wide mb-1 block">Nome Completo</label>
            <input v-model="form.name" type="text" required class="input-style-premium" placeholder="Seu nome" />
          </div>

          <div>
            <label class="text-xs font-bold text-gray-500 uppercase tracking-wide mb-1 block">E-mail</label>
            <input v-model="form.email" type="email" required class="input-style-premium" placeholder="seu@email.com" />
          </div>

          <div>
            <label class="text-xs font-bold text-gray-500 uppercase tracking-wide mb-1 block">Tipo de Conta</label>
            <select v-model="form.tipo" class="input-style-premium cursor-pointer">
              <option value="Avaliador">Avaliador (Consultor)</option>
              <option value="Gestor Empresarial">Gestor Empresarial</option>
            </select>
          </div>

          <div class="grid grid-cols-2 gap-4">
            <div>
              <label class="text-xs font-bold text-gray-500 uppercase tracking-wide mb-1 block">Senha</label>
              <input v-model="form.password" type="password" required class="input-style-premium" placeholder="******" />
            </div>
            <div>
              <label class="text-xs font-bold text-gray-500 uppercase tracking-wide mb-1 block">Confirmar</label>
              <input v-model="form.password_confirmation" type="password" required class="input-style-premium" placeholder="******" />
            </div>
          </div>

          <button type="submit" class="w-full py-3 px-4 bg-primary hover:bg-primary-dark text-white font-bold rounded-xl shadow-lg hover:shadow-xl transition-all transform hover:-translate-y-0.5 disabled:opacity-70" :disabled="isSubmitting">
            {{ isSubmitting ? 'Criando...' : 'Registrar Conta' }}
          </button>
        </form>

        <div class="mt-6 text-center text-sm text-gray-600">
          Já tem uma conta?
          <router-link to="/login" class="font-bold text-primary hover:underline">Entrar agora</router-link>
        </div>
      </div>
      <div class="h-1.5 bg-gradient-to-r from-primary-light via-primary to-primary-dark"></div>
    </div>
  </div>
</template>

<style scoped>
.input-style-premium {
  width: 100%; padding: 12px 16px; border: 1px solid #e5e7eb; border-radius: 12px; transition: all 0.2s; background: #fff;
}
/* Estilo Customizado para Select */
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
.animate-fade-in-up { animation: fadeInUp 0.6s ease-out forwards; }
@keyframes fadeInUp { from { opacity: 0; transform: translateY(20px); } to { opacity: 1; transform: translateY(0); } }
</style>

<script setup>
import { reactive, ref } from 'vue';
import { useRouter } from 'vue-router';
import api from '@/api.js';
import { useUserStore } from '@/stores/userStore';
import { useToast } from "vue-toastification";

const router = useRouter();
const userStore = useUserStore();
const toast = useToast();
const isSubmitting = ref(false);

const form = reactive({
  name: '', email: '', password: '', password_confirmation: '', tipo: 'Avaliador',
});

const handleRegister = async () => {
  isSubmitting.value = true;
  try {
    const response = await api.post('/register', form);
    const token = response.data.token;
    localStorage.setItem('authToken', token);
    api.defaults.headers.common['Authorization'] = `Bearer ${token}`;
    userStore.setUser(response.data.user);
    router.push('/dashboard');
  } catch (error) {
    if (error.response && error.response.data.errors) {
      toast.error(Object.values(error.response.data.errors)[0][0]);
    } else {
      toast.error('Erro ao registrar. Verifique os dados.');
    }
  } finally {
    isSubmitting.value = false;
  }
};
</script>
