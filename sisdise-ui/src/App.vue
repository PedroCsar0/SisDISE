<template>
  <div class="min-h-screen bg-gray-50">

    <header v-if="showHeader" class="bg-white shadow-md sticky top-0 z-50">
      <nav class="container mx-auto max-w-5xl px-4 py-3 flex justify-between items-center">

        <router-link to="/dashboard" class="flex items-center space-x-2">
          <img class="h-10 w-auto" src="/src/assets/logo.svg" alt="SisDISE Logo" />
          <span class="text-xl font-bold text-gray-800">SisDISE Relatórios ESG</span>
        </router-link>

        <div v-if="userStore.user" class="flex items-center space-x-4">

          <router-link
            v-if="userStore.user.tipo === 'Administrador'"
            to="/admin"
            class="px-3 py-1 text-sm font-medium text-white bg-red-600 rounded-full hover:bg-red-700"
          >
            Painel Admin
          </router-link>

          <span class="text-gray-600">Olá, {{ userStore.user.name }}</span>

          <button @click="handleLogout" class="px-3 py-1 text-sm font-medium text-primary-dark bg-primary-light/20 rounded-full hover:bg-primary-light/40">
          Sair
        </button>
        </div>
      </nav>
    </header>

    <main class="container mx-auto max-w-5xl p-4 mt-6">
      <RouterView />
    </main>

  </div>
</template>

<script setup>
import { computed } from 'vue';
import { RouterView, useRoute, useRouter } from 'vue-router';
import { useUserStore } from '@/stores/userStore'; // <-- Vamos criar este
import api from '@/api.js';

const route = useRoute();
const router = useRouter();
const userStore = useUserStore();

// Propriedade computada que verifica se o header deve ser mostrado
const showHeader = computed(() => {
  // Oculta o cabeçalho na página de login
  return route.name !== 'login';
});

// Função de Logout (agora fica centralizada aqui)
const handleLogout = async () => {
  try {
    localStorage.removeItem('authToken');
    delete api.defaults.headers.common['Authorization'];
    userStore.clearUser(); // Limpa o utilizador do "store"
    router.push('/login');
  } catch (error) {
    console.error('Erro no logout:', error);
  }
};
</script>
