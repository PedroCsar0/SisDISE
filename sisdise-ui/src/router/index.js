import { createRouter, createWebHistory } from 'vue-router'

const router = createRouter({
  history: createWebHistory(import.meta.env.BASE_URL),
  routes: [
    {
      path: '/dashboard',
      name: 'dashboard',
      component: () => import('../views/DashboardView.vue') // Corrigido
    },
    {
      path: '/admin',
      name: 'admin',
      component: () => import('../views/AdminView.vue') // Aponta para o novo arquivo
    },
    {
      path: '/empresas',
      name: 'gerenciar-empresas',
      component: () => import('../views/EmpresasView.vue') // Aponta para o novo arquivo
    },
    {
      path: '/diagnostico/novo',
      name: 'novo-diagnostico',
      component: () => import('../views/QuestionarioView.vue') // Aponta para o novo arquivo
    },
    {
      path: '/relatorio/:id', // O :id é dinâmico
      name: 'relatorio',
      props: true, // Permite que o ID seja passado como "prop"
      component: () => import('../views/RelatorioView.vue')
    },
    {
      path: '/relatorio/:id/pges',
      name: 'pges',
      props: true,
      component: () => import('../views/PgesView.vue')
    },
    {
      path: '/login', // URL agora é /login
      name: 'login', // Nome da rota é 'login'
      component: () => import('../views/LoginView.vue'), // Aponta para o novo arquivo
    },
  ],
})

export default router
