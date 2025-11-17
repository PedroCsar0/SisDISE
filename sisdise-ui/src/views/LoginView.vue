<template>
  <div class="flex items-center justify-center min-h-screen bg-gray-100">
    <div class="w-full max-w-md p-8 space-y-6 bg-white rounded-lg shadow-md">
      <div class="flex justify-center">
        <img class="h-12 w-auto" src="/src/assets/logo.svg" alt="SisDISE Logo" />
      </div>
      <h2 class="text-2xl font-bold text-center text-gray-900">SisDISE Relatórios ESG</h2>

      <form class="space-y-6" @submit.prevent="handleLogin">
        <div>
          <label for="email" class="block text-sm font-medium text-gray-700">
            Endereço de e-mail
          </label>
          <input
            v-model="form.email"
            id="email"
            name="email"
            type="email"
            required
            class="w-full px-3 py-2 mt-1 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500"
          />
        </div>

        <div>
          <label for="password" class="block text-sm font-medium text-gray-700"> Senha </label>
          <input
            v-model="form.password"
            id="password"
            name="password"
            type="password"
            required
            class="w-full px-3 py-2 mt-1 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500"
          />
        </div>

        <div>
          <button
          type="submit"
          class="w-full px-4 py-2 font-medium text-white bg-primary rounded-md hover:bg-primary-dark focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-light"
        >
          Login
        </button>
        <div class="text-center text-sm text-gray-600 mt-4">
          Não possui uma conta?
          <router-link to="/register" class="text-indigo-600 hover:underline">Registre-se aqui</router-link>
        </div>
        </div>
      </form>
    </div>
  </div>
</template>

<script setup>
import { reactive } from 'vue'; // ref foi atualizado para reactive
import { useRouter } from 'vue-router';
import api from '@/api.js';
import { useUserStore } from '@/stores/userStore';
import { useToast } from "vue-toastification"; // <-- 1. IMPORTE

const form = reactive({ // Usar 'reactive' é mais limpo para formulários
  email: 'teste@avaliador.com',
  password: 'senha123',
});

const router = useRouter();
const userStore = useUserStore();
const toast = useToast(); // <-- 2. INICIE

// errorMessage foi removido

const handleLogin = async () => {
  try {
    const response = await api.post('/login', {
      email: form.email,
      password: form.password,
    });

    const token = response.data.token;
    localStorage.setItem('authToken', token);
    api.defaults.headers.common['Authorization'] = `Bearer ${token}`;
    userStore.setUser(response.data.user);

    router.push('/dashboard');

  } catch (error) {
    console.error('Erro no login:', error);
    // <-- 3. USE O TOAST
    toast.error('E-mail ou senha inválidos. Tente novamente.');
  }
};
</script>
