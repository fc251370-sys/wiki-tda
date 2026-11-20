export const routerWiki = [
    {
        path: '/wiki',
        name: '/wiki',

        component: () => import('./index.vue')
    },
    {
        path: '/wiki/articulo',
        name: '/wiki/articulo',

        component: () => import('./articulo/index.vue')
    }
]