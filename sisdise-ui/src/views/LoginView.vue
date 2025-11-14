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
            v-model="email"
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
            v-model="password"
            id="password"
            name="password"
            type="password"
            required
            class="w-full px-3 py-2 mt-1 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500"
          />
        </div>

        <div v-if="errorMessage" class="text-sm text-red-600">
          {{ errorMessage }}
        </div>

        <div>
          <button
          type="submit"
          class="w-full px-4 py-2 font-medium text-white bg-primary rounded-md hover:bg-primary-dark focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-light"
        >
          Login
        </button>
        </div>
      </form>
    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue';
import { useRouter } from 'vue-router';
import api from '@/api.js'; // Nosso Axios (agora com a baseURL /api)
import { useUserStore } from '@/stores/userStore';

const email = ref('teste@avaliador.com');
const password = ref('senha123');
const errorMessage = ref('');
const router = useRouter();
const userStore = useUserStore();

const handleLogin = async () => {
  errorMessage.value = '';

  try {
    // PASSO 1: Chama a rota /api/login
    const response = await api.post('/login', {
      email: email.value,
      password: password.value,
    });

    const token = response.data.token;

    // PASSO 2: Salva o token no localStorage
    localStorage.setItem('authToken', token);

    // PASSO 3: Informa ao Axios para USAR este token
    api.defaults.headers.common['Authorization'] = `Bearer ${token}`;

    // PASSO 4: (A LINHA NOVA E IMPORTANTE) Salva os dados do usuário no "armazém"
    userStore.setUser(response.data.user);

    // PASSO 5: Redireciona para o Dashboard
    router.push('/dashboard');

  } catch (error) {
    console.error('Erro no login:', error);
    errorMessage.value = 'E-mail ou senha inválidos. Tente novamente.';
  }
};
</script>
