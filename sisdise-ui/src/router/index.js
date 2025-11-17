import { createRouter, createWebHistory } from 'vue-router'
import { useUserStore } from '@/stores/userStore' // <-- 1. IMPORTAR O STORE

// --- DEFINIÇÃO DAS ROTAS ---
const routes = [
  {
    path: '/',
    redirect: '/login'
  },
  {
    path: '/register',
    name: 'register',
    component: () => import('../views/RegisterView.vue'),
    meta: { requiresAuth: false }
  },
  {
    path: '/login',
    name: 'login',
    component: () => import('../views/LoginView.vue'),
    meta: { requiresAuth: false } // Esta rota não requer login
  },
  {
    path: '/dashboard',
    name: 'dashboard',
    component: () => import('../views/DashboardView.vue'),
    meta: { requiresAuth: true } // Requer login (qualquer tipo)
  },
  {
    path: '/admin',
    name: 'admin',
    component: () => import('../views/AdminView.vue'),
    // Requer login E uma permissão específica
    meta: { requiresAuth: true, roles: ['Administrador'] }
  },
  {
    path: '/admin/users',
    name: 'admin-users',
    component: () => import('../views/admin/UsersListView.vue'),
    meta: { requiresAuth: true, roles: ['Administrador'] }
  },
  {
    path: '/admin/users/:id',
    name: 'admin-user-details',
    props: true,
    component: () => import('../views/admin/UserDetailsView.vue'),
    meta: { requiresAuth: true, roles: ['Administrador'] }
  },
  {
    path: '/admin/users/:id/edit',
    name: 'admin-user-edit',
    props: true,
    component: () => import('../views/admin/UserEditView.vue'),
    meta: { requiresAuth: true, roles: ['Administrador'] }
  },
  {
    path: '/empresas',
    name: 'gerenciar-empresas',
    component: () => import('../views/EmpresasView.vue'),
    // Requer login E uma permissão específica
    meta: { requiresAuth: true, roles: ['Administrador', 'Avaliador'] }
  },
  {
    path: '/diagnostico/novo',
    name: 'novo-diagnostico',
    component: () => import('../views/QuestionarioView.vue'),
    // Requer login E uma permissão específica
    meta: { requiresAuth: true, roles: ['Administrador', 'Avaliador'] }
  },
  {
    path: '/relatorio/:id',
    name: 'relatorio',
    props: true,
    component: () => import('../views/RelatorioView.vue'),
    meta: { requiresAuth: true } // Requer login (qualquer tipo)
  },
  {
    path: '/relatorio/:id/pges',
    name: 'pges',
    props: true,
    component: () => import('../views/PgesView.vue'),
    meta: { requiresAuth: true } // Requer login (qualquer tipo)
  }
]

// --- CRIAÇÃO DO ROUTER ---
const router = createRouter({
  history: createWebHistory(import.meta.env.BASE_URL),
  routes
})

// --- O "PORTEIRO" (NAVIGATION GUARD) ---
router.beforeEach(async (to, from, next) => {
  const userStore = useUserStore();
  const token = localStorage.getItem('authToken');

  // 1. Tentar buscar o utilizador se tivermos um token mas não tivermos dados no store
  // (Isto é crucial para quem dá F5 na página)
  if (token && !userStore.user) {
    await userStore.fetchUser();
  }

  const isAuthenticated = !!userStore.user;
  const userRole = userStore.user ? userStore.user.tipo : null;

  // 2. Se a rota requer autenticação e o utilizador NÃO está logado
  if (to.meta.requiresAuth && !isAuthenticated) {
    next({ name: 'login' }); // Redireciona para o login
  }
  // 3. Se a rota requer permissões (roles)
  else if (to.meta.roles) {
    // Se está autenticado E o seu 'tipo' está na lista de permissões
    if (isAuthenticated && to.meta.roles.includes(userRole)) {
      next(); // Permite o acesso
    } else {
      // Não tem permissão. Envia-o de volta ao dashboard.
      next({ name: 'dashboard' });
    }
  }
  // 4. Se a rota é /login e o utilizador JÁ está logado
  else if (to.name === 'login' && isAuthenticated) {
    next({ name: 'dashboard' }); // Vai para o dashboard
  }
  // 5. Para todos os outros casos (rotas públicas)
  else {
    next(); // Permite o acesso
  }
})

export default router
