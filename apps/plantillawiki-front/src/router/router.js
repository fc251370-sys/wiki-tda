import { createRouter, createWebHistory } from 'vue-router'
import GuestLayout from '../view/layout/guest-layout.vue'
import AdminLayout from '../view/layout/admin-layout.vue'

import Home from '../view/Home.vue'
import AdminDashboard from '../view/AdminDashboard.vue'
import { routerAuth } from '../view/auth/router.js'
import { configurationRouter } from '../view/configuracion/router.js'

const routes = [
  {
    path: '/',
    component: GuestLayout,
    children: [
      {
        path: '',
        name: 'Home',
        component: Home
      },
      ...routerAuth,
    ],
     meta: {
    guestOnly: true
  }
  },
  {
    path: '/admin',
    component: AdminLayout,
    children: [
      {
        path: '',
        name: 'Dashboard',
        component: AdminDashboard,
        meta: {
          requiresAuth: true
        }
      },
      ...configurationRouter
    ],
    meta: {
      requiresAuth: true,
      
    }
  }
]

const router = createRouter({
  history: createWebHistory(),
  routes
})

router.beforeEach((to, from, next) => {
    const isAuthenticated = !!localStorage.getItem('token');

    // Rutas protegidas
    if (to.matched.some(record => record.meta.requiresAuth)) {
        if (!isAuthenticated) {
            return next({ name: 'Login' });
        }

        return next();
    }

    // Rutas solo para invitados
    if (to.matched.some(record => record.meta.guestOnly)) {
        if (isAuthenticated) {
            return next({ name: 'AdminDashboard' });
        }

        return next();
    }

    next();
});

export default router
