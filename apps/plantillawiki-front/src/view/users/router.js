export const routerUsers = [
     {
        path: '/usuarios',
        name: 'Usuarios',
        component: () => import('./index.vue'),
        meta: {
            roles: ['admin'],
        },
    }
]
