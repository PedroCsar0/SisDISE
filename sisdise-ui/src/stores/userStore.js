import { defineStore } from 'pinia'
import api from '@/api.js'

export const useUserStore = defineStore('user', {
  // 1. O 'state' (estado) guarda os dados
  state: () => ({
    user: null, // O utilizador começa como 'null'
  }),

  // 2. As 'actions' (ações) modificam o estado
  actions: {
    async fetchUser() {
      if (this.user) return; // Já temos, não buscar de novo

      try {
        const response = await api.get('/user');
        this.user = response.data; // Atribuição direta
      } catch (error) {
        console.error('Falha ao buscar utilizador no store:', error);
        this.user = null;
      }
    },

    // Ação para definir o utilizador após o login
    setUser(userData) {
      this.user = userData;
    },

    // Ação para limpar o utilizador (no logout)
    clearUser() {
      this.user = null;
    }
  }
})
