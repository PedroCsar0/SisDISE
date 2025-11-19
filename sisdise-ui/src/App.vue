<template>
  <div class="min-h-screen bg-gradient-to-br from-gray-100 via-gray-200 to-gray-300 font-sans text-gray-900">

    <header v-if="showHeader" class="sticky top-0 z-50">
      <nav class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 mt-4">
        <div class="bg-white/90 backdrop-blur-md shadow-lg rounded-2xl px-6 py-3 flex justify-between items-center border border-white/20">

          <router-link to="/dashboard" class="flex items-center gap-3 group">
            <div class="bg-indigo-50 p-2 rounded-full group-hover:bg-indigo-100 transition-colors">
              <img class="h-8 w-auto" src="/src/assets/logo.svg" alt="SisDISE Logo" />
            </div>
            <span class="text-xl font-bold bg-clip-text text-transparent bg-gradient-to-r from-primary-dark to-primary">
              SisDISE Relatórios ESG
            </span>
          </router-link>

          <div v-if="userStore.user" class="flex items-center gap-4">

            <router-link
              v-if="userStore.user.tipo === 'Administrador'"
              to="/admin"
              class="hidden md:flex items-center gap-2 px-4 py-2 text-sm font-medium text-white bg-red-500 rounded-xl hover:bg-red-600 shadow-md hover:shadow-lg transition-all"
            >
              <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M11.49 3.17c-.38-1.56-2.6-1.56-2.98 0a1.532 1.532 0 01-2.286.948c-1.372-.836-2.942.734-2.106 2.106.54.886.061 2.042-.947 2.287-1.561.379-1.561 2.6 0 2.978a1.532 1.532 0 01.947 2.287c-.836 1.372.734 2.942 2.106 2.106a1.532 1.532 0 012.287.947c.379 1.561 2.6 1.561 2.978 0a1.533 1.533 0 012.287-.947c1.372.836 2.942-.734 2.106-2.106a1.533 1.533 0 01.947-2.287c1.561-.379 1.561-2.6 0-2.978a1.532 1.532 0 01-.947-2.287c.836-1.372-.734-2.942-2.106-2.106a1.532 1.532 0 01-2.287-.947zM10 13a3 3 0 100-6 3 3 0 000 6z" clip-rule="evenodd" /></svg>
              Configurar parâmetros de cálculo
            </router-link>

            <div class="hidden md:flex flex-col items-end">
              <span class="text-sm font-bold text-gray-800">{{ userStore.user.name }}</span>
              <span class="text-xs text-gray-500">{{ userStore.user.tipo }}</span>
            </div>

            <router-link
              v-if="userStore.user.tipo !== 'Administrador'"
              to="/perfil"
              class="p-2 text-gray-500 hover:text-primary hover:bg-indigo-50 rounded-full transition-all"
              title="Minha Conta"
            >
              <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
              </svg>
            </router-link>

            <button
              @click="handleLogout"
              class="p-2 text-gray-500 hover:text-red-600 hover:bg-red-50 rounded-full transition-all"
              title="Sair"
            >
              <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
              </svg>
            </button>
          </div>
        </div>
      </nav>
    </header>

    <main :class="showHeader ? 'max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8' : 'w-full'">
      <RouterView />
    </main>

  </div>
</template>

<script setup>
import { computed } from 'vue';
import { RouterView, useRoute, useRouter } from 'vue-router';
import { useUserStore } from '@/stores/userStore';
import api from '@/api.js';

const route = useRoute();
const router = useRouter();
const userStore = useUserStore();

const showHeader = computed(() => {
  return route.name !== 'login' && route.name !== 'register';
});

const handleLogout = async () => {
  try {
    // Remove de ambos para garantir
    localStorage.removeItem('authToken');
    sessionStorage.removeItem('authToken');

    delete api.defaults.headers.common['Authorization'];
    userStore.clearUser();
    router.push('/login');
  } catch (error) {
    console.error('Erro no logout:', error);
  }
};
</script>
