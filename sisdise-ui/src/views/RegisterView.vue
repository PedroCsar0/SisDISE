<template>
  <div class="flex items-center justify-center min-h-screen bg-gray-50">
    <div class="w-full max-w-md p-8 space-y-6 bg-white rounded-lg shadow-md">
      <div class="flex justify-center">
        <img class="h-12 w-auto" src="/src/assets/logo.svg" alt="SisDISE Logo" />
      </div>
      <h2 class="text-2xl font-bold text-center text-gray-900">Criar Nova Conta</h2>

      <form class="space-y-4" @submit.prevent="handleRegister">

        <div>
          <label class="block text-sm font-medium text-gray-700">Nome Completo</label>
          <input v-model="form.name" type="text" required class="input-style" />
        </div>

        <div>
          <label class="block text-sm font-medium text-gray-700">E-mail</label>
          <input v-model="form.email" type="email" required class="input-style" />
        </div>

        <div>
          <label class="block text-sm font-medium text-gray-700">Tipo de Conta</label>
          <select v-model="form.tipo" class="input-style bg-white">
            <option value="Avaliador">Avaliador (Consultor)</option>
            <option value="Gestor Empresarial">Gestor Empresarial</option>
          </select>
        </div>

        <div>
          <label class="block text-sm font-medium text-gray-700">Senha</label>
          <input v-model="form.password" type="password" required class="input-style" />
        </div>

        <div>
          <label class="block text-sm font-medium text-gray-700">Confirmar Senha</label>
          <input v-model="form.password_confirmation" type="password" required class="input-style" />
        </div>

        <div v-if="errorMessage" class="text-sm text-red-600 text-center">
          {{ errorMessage }}
        </div>

        <button type="submit" class="btn-primary w-full" :disabled="isSubmitting">
          {{ isSubmitting ? 'Criando...' : 'Registrar' }}
        </button>
      </form>

      <div class="text-center text-sm text-gray-600">
        Já tem uma conta?
        <router-link to="/login" class="text-primary hover:underline">Faça login</router-link>
      </div>
    </div>
  </div>
</template>

<style scoped>
.input-style {
  width: 100%; padding: 0.5rem; border: 1px solid #D1D5DB; border-radius: 0.375rem; margin-top: 0.25rem;
}
.btn-primary {
  padding: 0.5rem 1rem; background-color: #4f46e5; color: white; border-radius: 0.375rem; font-weight: 500;
}
.btn-primary:hover { background-color: #4338ca; }
</style>

<script setup>
import { reactive, ref } from 'vue';
import { useRouter } from 'vue-router';
import api from '@/api.js';
import { useUserStore } from '@/stores/userStore';

const router = useRouter();
const userStore = useUserStore();
const isSubmitting = ref(false);
const errorMessage = ref('');

const form = reactive({
  name: '',
  email: '',
  password: '',
  password_confirmation: '', // Campo exigido pelo Laravel 'confirmed'
  tipo: 'Avaliador', // Padrão
});

const handleRegister = async () => {
  isSubmitting.value = true;
  errorMessage.value = '';

  try {
    const response = await api.post('/register', form);

    // Login automático após registro
    const token = response.data.token;
    localStorage.setItem('authToken', token);
    api.defaults.headers.common['Authorization'] = `Bearer ${token}`;
    userStore.setUser(response.data.user);

    router.push('/dashboard');
  } catch (error) {
    console.error(error);
    if (error.response && error.response.data.errors) {
      // Pega o primeiro erro de validação (ex: email já existe)
      errorMessage.value = Object.values(error.response.data.errors)[0][0];
    } else {
      errorMessage.value = 'Erro ao registrar. Tente novamente.';
    }
  } finally {
    isSubmitting.value = false;
  }
};
</script>
