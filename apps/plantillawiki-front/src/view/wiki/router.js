// Exporta el array de rutas del módulo wiki
export const routerWiki = [
  // Rutas reales (viven bajo /admin por estar en children)
  {
    path: 'wiki',
    name: 'wiki',
    component: () => import('./index.vue')
  },
  {
    path: 'wiki/categoria',
    name: 'wiki-categoria',
    component: () => import('./categoria/index.vue')
  },
  {
    path: 'wiki/tag',
    name: 'wiki-tag',
    component: () => import('./tag/index.vue')
  },
  {
    path: 'wiki/articulo',
    name: 'wiki-articulo',
    component: () => import('./articulo/index.vue')
  },

  // Alias absolutos: permiten que /wiki/... funcione igual
  // (redirigen a las rutas reales bajo /admin/wiki/...)
  {
    path: '/wiki',
    redirect: '/admin/wiki'
  },
  {
    path: '/wiki/categoria',
    redirect: '/admin/wiki/categoria'
  },
  {
    path: '/wiki/tag',
    redirect: '/admin/wiki/tag'
  },
  {
    path: '/wiki/articulo',
    redirect: '/admin/wiki/articulo'
  }
]