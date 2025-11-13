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
            class="w-full px-4 py-2 font-medium text-white bg-indigo-600 rounded-md hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
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

const email = ref('teste@avaliador.com');
const password = ref('senha123');
const errorMessage = ref('');
const router = useRouter();

const handleLogin = async () => {
  errorMessage.value = '';

  try {
    // NÃO precisamos mais do 'csrf-cookie'

    // PASSO 1: Chama a nova rota /api/login
    // (O Axios automaticamente usa a baseURL: '.../api/login')
    const response = await api.post('/login', {
      email: email.value,
      password: password.value,
    });

    // PASSO 2: Pega o token da resposta
    const token = response.data.token;

    // PASSO 3: Salva o token no localStorage
    localStorage.setItem('authToken', token);

    // PASSO 4: Informa ao Axios para USAR este token a partir de agora
    // (Isso adiciona o header 'Authorization' em todas as futuras chamadas)
    api.defaults.headers.common['Authorization'] = `Bearer ${token}`;

    // PASSO 5: Redireciona para o Dashboard
    router.push('/dashboard');

  } catch (error) {
    console.error('Erro no login:', error);
    errorMessage.value = 'E-mail ou senha inválidos. Tente novamente.';
  }
};
</script>
